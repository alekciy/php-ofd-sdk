<?php

use alekciy\ofd\interfaces\CashDeskInterface;
use alekciy\ofd\interfaces\DocumentInterface;
use alekciy\ofd\interfaces\ShiftInterface;
use alekciy\ofd\providers\taxcom\Client;
use alekciy\ofd\providers\taxcom\Credentials;
use alekciy\ofd\providers\taxcom\Model\CashDesk;
use alekciy\ofd\providers\taxcom\Model\CashDeskShort;
use alekciy\ofd\providers\taxcom\Model\Document;
use alekciy\ofd\providers\taxcom\Model\OutletShort;
use alekciy\ofd\providers\taxcom\Model\ShiftShort;
use alekciy\ofd\providers\taxcom\Status;
use alekciy\ofd\providers\taxcom\Taxcom;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class TaxcomTest extends TestCase
{
	/** @var MockHandler */
	protected $mock;

	/** @var Taxcom */
	protected $provider;

	/** @var array  */
	protected $fixtureList = [];

	const ID_OUTLET   = '56529ad6-a488-4fef-b7da-555555555555';
	const ID_CASHDESK = '3333333333333333'; // kktFactoryNumber

	/**
	 * @inheritDoc
	 *
	 * @throws Exception
	 */
	public function setUp()
	{
		$this->mock = new MockHandler();
		$handlerStack = HandlerStack::create($this->mock);

		$credentialsProd = new Credentials(
			'api-tlk-ofd.taxcom.ru',
			'TEST',
			'TEST',
			'TEST'
		);
		$agreementNumber = 'TEST';
		$client = new Client($credentialsProd, $agreementNumber, '', ['handler' => $handlerStack]);
		$this->provider = new Taxcom($client);

		foreach (glob(__DIR__ . '/../fixtures/providers/taxcom/*.json') as $fileName) {
			$this->fixtureList[basename($fileName, '.json')] = file_get_contents($fileName);
		}
	}

	/**
	 * Проверить получение списка торговых точек.
	 *
	 * @test
	 * @return OutletShort[]
	 * @throws ReflectionException
	 * @throws GuzzleException
	 */
	public function testGetOutletList(): array
	{
		$this->mock->append(new Response(200, [], $this->fixtureList['OutletList']));
		$outletList = $this->provider->getOutletList();

		$this->assertCount(5, $outletList);
		$this->assertContainsOnlyInstancesOf(OutletShort::class, $outletList);
		$this->assertArrayHasKey(self::ID_OUTLET, $outletList);

		return $outletList;
	}

	/**
	 * Проверить получение списка касс для заданной торговой точки $outletList.
	 *
	 * @test
	 * @depends testGetOutletList
	 * @param array $outletList
	 * @return CashDeskInterface
	 * @throws GuzzleException
	 */
	public function testGetCashDeskList(array $outletList): CashDeskInterface
	{
		$outlet = $outletList[self::ID_OUTLET];
		$this->mock->append(new Response(200, [], $this->fixtureList['CashDeskList']));

		/** @var CashDeskShort[] $cashDeskList */
		$cashDeskList = $this->provider->getCashDeskList($outlet);
		$this->assertCount(1, $cashDeskList);
		$this->assertContainsOnlyInstancesOf(CashDeskShort::class, $cashDeskList);

		$cashDesk = reset($cashDeskList);
		$this->assertEquals(self::ID_CASHDESK, $cashDesk->getKktFactoryNumber());
		$this->assertEquals(self::ID_CASHDESK, $cashDesk->kktFactoryNumber);
		$this->assertEquals('1111111111111111', $cashDesk->getName());
		$this->assertEquals('1111111111111111', $cashDesk->name);
		$this->assertEquals('2222222222222222', $cashDesk->getKktRegNumber());
		$this->assertEquals('2222222222222222', $cashDesk->kktRegNumber);
		$this->assertEquals('4444444444444444', $cashDesk->getFnFactoryNumber());
		$this->assertEquals('4444444444444444', $cashDesk->fnFactoryNumber);
		$this->assertEquals(CashDesk::STATUS_ACTIVE, $cashDesk->cashdeskState);
		$this->assertEquals(Status::OK, $cashDesk->problemIndicator);

		return $cashDesk;
	}

	/**
	 * Проверяет получение списка смен для заданной ККТ $cashDesk.
	 *
	 * @test
	 * @depends testGetCashDeskList
	 * @param CashDeskInterface $cashDesk
	 * @return ShiftInterface
	 * @throws GuzzleException
	 * @throws ReflectionException
	 */
	public function testGetShiftList(CashDeskInterface $cashDesk): ShiftInterface
	{
		$this->mock->append(new Response(200, [], $this->fixtureList['ShiftList']));

		/** @var ShiftInterface[] $shiftList */
		$shiftList = $this->provider->getShiftList($cashDesk);
		$this->assertCount(4, $shiftList);
		$this->assertContainsOnlyInstancesOf(ShiftInterface::class, $shiftList);

		$shift = reset($shiftList);
		$this->assertEquals('4444444444444444', $shift->getFnFactoryNumber());
		$this->assertEquals('4444444444444444', $shift->fnFactoryNumber);
		$this->assertEquals(84, $shift->getShiftNumber());
		$this->assertEquals(84, $shift->shiftNumber);
		$this->assertEquals(4, $shift->receiptCount);

		return $shift;
	}

	/**
	 * Проверяем получение списка фискальных документов за смену $shift.
	 *
	 * @test
	 * @depends testGetShiftList
	 * @param ShiftInterface $shift
	 * @return DocumentInterface
	 * @throws GuzzleException
	 * @throws ReflectionException
	 */
	public function testGetDocumentList(ShiftInterface $shift): DocumentInterface
	{
		$this->mock->append(new Response(200, [], $this->fixtureList['DocumentList']));

		/** @var DocumentInterface[] $documentList */
		$documentList = $this->provider->getDocumentList($shift);
		$this->assertCount(4, $documentList);
		$this->assertContainsOnlyInstancesOf(DocumentInterface::class, $documentList);

		/** @var Document $document */
		$document = reset($documentList);
		$this->assertEquals('4444444444444444', $document->getFnFactoryNumber());
		$this->assertEquals('4444444444444444', $document->fnFactoryNumber);
		$this->assertEquals(84, $document->shiftNumber);
		$this->assertEquals(16214, $document->fdNumber);
		$this->assertEquals('824629212', $document->fpd);
		$this->assertEquals(1400, $document->sum);
		$this->assertEquals(1400, $document->cash);

		return $document;
	}

	/**
	 * Проверяем получение ФФД тегов документа $document.
	 *
	 * @test
	 * @depends testGetDocumentList
	 * @param DocumentInterface $document
	 * @throws GuzzleException
	 * @throws ReflectionException
	 */
	public function testGetDocumentTag(DocumentInterface $document)
	{
		$this->mock->append(new Response(200, [], $this->fixtureList['DocumentInfo']));

		$documentTag = $this->provider->getDocumentTag($document);
		$this->assertTrue(is_array($documentTag->extend->value), 'Расширенный тег extend должен быть массивом');
		$this->assertArrayHasKey('check#', $documentTag->extend->value);
		$this->assertEquals('124466', $documentTag->extend->value['check#']);
		$this->assertEquals(\alekciy\ffd\DocumentInterface::TYPE_CHECK, $documentTag->type);
	}
}