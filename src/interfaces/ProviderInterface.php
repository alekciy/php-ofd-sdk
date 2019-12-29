<?php

namespace alekciy\ofd\interfaces;

use alekciy\ofd\providers\taxcom\Model\CashDesk;
use alekciy\ofd\providers\taxcom\Model\Document;
use alekciy\ofd\providers\taxcom\Model\Shift;
use DateTime;

interface ProviderInterface
{

	/**
	 * Получить список торговых точек.
	 *
	 * @return OutletInterface[]
	 */
	public function getOutletList(): array;

	/**
	 * Вернет список касс относящихся к торговой точке $outlet. Если торговая точка не заданна, то все кассы.
	 *
	 * @param OutletInterface|null $outlet
	 * @return CashDesk[]
	 */
	public function getCashDeskList(OutletInterface $outlet = null): array;

	/**
	 * Получить список смен кассы $cashDesk за заданный период. По умолчанию за сегодня. Если касса не задана, то смены
	 * со всех касса в периоде.
	 *
	 * @param CashDeskInterface|null $cashDesk
	 * @param DateTime|null $start
	 * @param DateTime|null $end
	 * @return Shift[]
	 */
	public function getShiftList(CashDeskInterface $cashDesk = null, DateTime $start = null, DateTime $end = null): array;

	/**
	 * Получить список документов смены $shift за заданный период. По умолчанию за сегодня. Если смена не задана, то документы
	 * со всех касса в периоде.
	 *
	 * @param ShiftInterface|null $shift
	 * @param DateTime|null $start
	 * @param DateTime|null $end
	 * @return Document[]
	 */
	public function getDocumentList(ShiftInterface $shift = null, DateTime $start = null, DateTime $end = null): array;
}
