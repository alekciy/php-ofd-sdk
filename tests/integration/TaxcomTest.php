<?php

namespace alekciy\ofd\test\integration;

use alekciy\ofd\interfaces\CashDeskInterface;
use alekciy\ofd\interfaces\OutletInterface;
use alekciy\ofd\providers\taxcom\Client;
use alekciy\ofd\providers\taxcom\Credentials;
use alekciy\ofd\providers\taxcom\Model\CashDeskShort;
use alekciy\ofd\providers\taxcom\Model\Document;
use alekciy\ofd\providers\taxcom\Model\OutletShort;
use alekciy\ofd\providers\taxcom\Model\ShiftShort;
use alekciy\ofd\providers\taxcom\Taxcom;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;

/**
 * @group taxcom
 */
class TaxcomTest extends TestCase
{

	/** @var Taxcom */
	protected $provider;

	/**
	 * @inheritDoc
	 * @throws Exception
	 */
	public function setUp()
	{
		$credentialFile = realpath( __DIR__ . '/../credentials/taxcom.php');
		if (!is_readable($credentialFile)) {
			throw new Exception("Файл {$credentialFile} не существует или отсутствует");
		}
		$prod = include $credentialFile;
		if (!isset($prod['credentials'])
			|| !$prod['credentials'] instanceof Credentials
		) {
			throw new Exception("Файл $credentialFile должен возвращать объект ['credentials' => " . Credentials::class . ', ...]');
		}
		if (!isset($prod['agreementNumber'])
			|| !is_string($prod['agreementNumber'])
		) {
			throw new Exception("Файл $credentialFile должен возвращать строку ['agreementNumber' => '...', ...]");
		}
		$client = new Client($prod['credentials'], $prod['agreementNumber']);
		$this->provider = new Taxcom($client);
	}

	/**
	 * @test
	 * @return OutletInterface[]
	 * @throws GuzzleException
	 */
	public function testGetOutletList(): array
	{
		$outletList = $this->provider->getOutletList();
		$this->assertContainsOnlyInstancesOf(OutletShort::class, $outletList);
		$this->assertTrue(is_array($outletList));
		return $outletList;
	}

	/**
	 * @test
	 * @depends testGetOutletList
	 * @return CashDeskInterface[]
	 * @throws GuzzleException
	 */
	public function testGetCashDeskList(array $outletList): array
	{
		$result = [];
		/** @var OutletShort $outlet */
		foreach ($outletList as $outlet) {
			$cashDeskList = $this->provider->getCashDeskList($outlet);
			$this->assertTrue(is_array($cashDeskList));
			foreach ($cashDeskList as $cashDesk) {
				$this->assertInstanceOf(CashDeskShort::class, $cashDesk);
				$result[] = $cashDesk;
			}

		}
		return $result;
	}

	/**
	 * @test
	 * @depends testGetCashDeskList
	 * @param array $cashDeskList
	 * @return ShiftShort[]
	 * @throws GuzzleException
	 */
	public function testGetShiftList(array $cashDeskList): array
	{
		$result = [];
		/** @var CashDeskShort $cashDesk */
		foreach ($cashDeskList as $cashDesk) {
			$shiftList = $this->provider->getShiftList($cashDesk);
			$this->assertTrue(is_array($shiftList));
			foreach ($shiftList as $shift) {
				$this->assertInstanceOf(ShiftShort::class, $shift);
				$result[] = $shift;
			}
		}
		return $result;
	}

	/**
	 * @test
	 * @depends testGetShiftList
	 * @param ShiftShort[] $shiftList
	 * @return Document[]
	 * @throws GuzzleException
	 */
	public function testGetDocumentList(array $shiftList): array
	{
		$result = [];
		foreach ($shiftList as $shift) {
			$documentList = $this->provider->getDocumentList($shift);
			$this->assertTrue(is_array($documentList));
			foreach ($documentList as $document) {
				$this->assertInstanceOf(Document::class, $document);
				$result[] = $document;
			}
		}
		return $result;
	}
}
