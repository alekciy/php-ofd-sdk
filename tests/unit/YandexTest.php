<?php

namespace alekciy\ofd\test\unit;

use alekciy\ofd\interfaces\CashDeskInterface;
use alekciy\ofd\interfaces\DocumentInterface;
use alekciy\ofd\interfaces\OutletInterface;
use alekciy\ofd\interfaces\ShiftInterface;
use alekciy\ofd\providers\yandex\Client;
use alekciy\ofd\providers\yandex\Credentials;
use alekciy\ofd\providers\yandex\Model\CashDesk;
use alekciy\ofd\providers\yandex\Model\Document;
use alekciy\ofd\providers\yandex\Model\OperationShiftReport;
use alekciy\ofd\providers\yandex\Model\Outlet;
use alekciy\ofd\providers\yandex\Model\Shift;
use alekciy\ofd\providers\yandex\Yandex;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class YandexTest extends TestCase
{
	/** @var MockHandler */
	protected $mock;

	/** @var Yandex */
	protected $provider;

	/** @var array  */
	protected $fixtureList = [];

	/**
	 * @inheritDoc
	 *
	 * @throws Exception
	 */
	public function setUp(): void
	{
		$this->mock = new MockHandler();
		$handlerStack = HandlerStack::create($this->mock);

		$credentials = new Credentials('api.ofd.yandex.net', 'TEST', 'TEST');
		$client = new Client($credentials, ['handler' => $handlerStack]);
		$this->provider = new Yandex($client);

		foreach (glob(__DIR__ . '/../fixtures/providers/yandex/*.json') as $fileName) {
			$this->fixtureList[basename($fileName, '.json')] = file_get_contents($fileName);
		}
	}

	/**
	 * Проверить получение списка торговых точек.
	 *
	 * @test
	 * @return OutletInterface
	 * @throws GuzzleException
	 */
	public function testGetOutletList(): OutletInterface
	{
		$this->mock->append(new Response(200, [], $this->fixtureList['OutletList']));
		$this->mock->append(new Response(200, [], '[]'));
		$outletList = $this->provider->getOutletList();

		$this->assertCount(2, $outletList);
		$this->assertContainsOnlyInstancesOf(OutletInterface::class, $outletList);

		/** @var Outlet $outlet */
		$outlet = reset($outletList);
		$this->assertEquals('105094, Москва, ул Новая, д 5', $outlet->getAddress());
		$this->assertEquals('105094, Москва, ул Новая, д 5', $outlet->address);
		$this->assertEquals(88, $outlet->clientId);
		$this->assertEquals(728, $outlet->getId());
		$this->assertEquals(728, $outlet->id);
		$this->assertEquals('ТЦ 1', $outlet->getName());
		$this->assertEquals('ТЦ 1', $outlet->name);
		$this->assertEmpty($outlet->companyId);

		return $outlet;
	}

	/**
	 * Проверить получение списка касс для заданной торговой точки $outletList.
	 *
	 * @test
	 * @depends testGetOutletList
	 * @param OutletInterface $outlet
	 * @return CashDeskInterface
	 * @throws GuzzleException
	 */
	public function testGetCashDeskList(OutletInterface $outlet): CashDeskInterface
	{
		$this->mock->append(new Response(200, [], $this->fixtureList['CashDeskList']));
		$this->mock->append(new Response(200, [], '[]'));

		/** @var CashDesk[] $cashDeskList */
		$cashDeskList = $this->provider->getCashDeskList($outlet);
		$this->assertCount(1, $cashDeskList);
		$this->assertContainsOnlyInstancesOf(CashDesk::class, $cashDeskList);

		/** @var CashDesk $cashDesk */
		$cashDesk = reset($cashDeskList);
		$this->assertEquals('00000000381007926499', $cashDesk->getKktFactoryNumber());
		$this->assertEquals('00000000381007926499', $cashDesk->kktFactoryNumber);
		$this->assertEquals('00000000381007926499', $cashDesk->getName());
		$this->assertEmpty($cashDesk->getKktRegNumber());
		$this->assertEmpty($cashDesk->kktRegNumber);
		$this->assertEquals('8710000100875131', $cashDesk->getFnFactoryNumber());
		$this->assertEquals('8710000100875131', $cashDesk->fnFactoryNumber);
		$this->assertEquals(728, $cashDesk->outletId);
		$this->assertEquals(902, $cashDesk->id);
		$this->assertEquals(587, $cashDesk->companyId);

		return $cashDesk;
	}

	/**
	 * Проверить получение информации по кассе.
	 *
	 * @test
	 * @throws GuzzleException
	 */
	public function testGetCashDesk()
	{
		$this->mock->append(new Response(403, [], $this->fixtureList['CashDeskNotFound']));
		$this->mock->append(new Response(200, [], $this->fixtureList['CashDesk']));

		// Проверка негативного сценария: такой кассы нет
		try {
			$cashDesk = $this->provider->getCashDesk(-1);
		} catch (Exception $e) {
			$this->assertEquals(4, $e->getCode(), 'Код исключения должен совпадать с кодом ошибки API');
		}
		$this->assertFalse(isset($cashDesk), 'Запрос несуществующей кассы должен генерировать исключение');

		// Проверка позитивного сценария
		$cashDeskId = 902;
		/** @var CashDesk $cashDesk */
		$cashDesk = $this->provider->getCashDesk($cashDeskId);
		$this->assertEquals('00000000381007926499', $cashDesk->getKktFactoryNumber());
		$this->assertEquals('00000000381007926499', $cashDesk->kktFactoryNumber);
		$this->assertEquals('00000000381007926499', $cashDesk->getName());
		$this->assertEmpty($cashDesk->getKktRegNumber());
		$this->assertEmpty($cashDesk->kktRegNumber);
		$this->assertEquals('8710000100875131', $cashDesk->getFnFactoryNumber());
		$this->assertEquals('8710000100875131', $cashDesk->fnFactoryNumber);
		$this->assertEquals(728, $cashDesk->outletId);
		$this->assertEquals(902, $cashDesk->id);
		$this->assertEquals(587, $cashDesk->companyId);

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
	 */
	public function testGetShiftList(CashDeskInterface $cashDesk): ShiftInterface
	{
		$this->mock->append(new Response(200, [], $this->fixtureList['ShiftList']));
		$this->mock->append(new Response(200, [], '[]'));

		/** @var ShiftInterface[] $shiftList */
		$shiftList = $this->provider->getShiftList($cashDesk);
		$this->assertCount(1, $shiftList);

		/** @var Shift $shift */
		$shift = reset($shiftList);
		$this->assertEquals('4346576876976321', $shift->fnFactoryNumber);
		$this->assertEquals('4346576876976321', $shift->getFnFactoryNumber());
		$this->assertEquals(2, $shift->getShiftNumber());
		$this->assertEquals(2, $shift->shiftNumber);
		$this->assertEquals('2018-06-15 14:02:00', $shift->openDateTime);
		$this->assertEquals('2018-06-18 11:22:00', $shift->closeDateTime);

		$this->assertTrue($shift->income instanceof OperationShiftReport);
		$this->assertTrue($shift->incomeReturn instanceof OperationShiftReport);
		$this->assertTrue($shift->outcome instanceof OperationShiftReport);
		$this->assertTrue($shift->outcomeReturn instanceof OperationShiftReport);

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
	 */
	public function testGetDocumentList(ShiftInterface $shift): DocumentInterface
	{
		$this->mock->append(new Response(200, [], $this->fixtureList['DocumentList']));
		$this->mock->append(new Response(200, [], '[]'));

		/** @var DocumentInterface[] $documentList */
		$documentList = $this->provider->getDocumentList($shift);
		$this->assertCount(4, $documentList);
		$this->assertContainsOnlyInstancesOf(DocumentInterface::class, $documentList);

		/** @var Document $document */
		$document = reset($documentList);
		$this->assertEquals('2018-07-06 15:33:00', $document->dateTime);
		$this->assertEquals('0000097587029813', $document->kktRegNumber);
		$this->assertEquals('2325436457568687', $document->getFnFactoryNumber());
		$this->assertEquals('2325436457568687', $document->fnFactoryNumber);
		$this->assertEquals(9, $document->shiftNumber);
		$this->assertEquals(64, $document->fdNumber);
		$this->assertEquals('120756789', $document->fpd);
		$this->assertEquals(7000, $document->totalSum);
		$this->assertEquals(7000, $document->cashSum);
		$this->assertEquals(0, $document->prepaidSum);
		$this->assertEquals(0, $document->creditSum);
		$this->assertEquals(0, $document->provisionSum);
		$this->assertEquals(0, $document->electronicSum);

		return $document;
	}
}
