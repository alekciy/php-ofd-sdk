<?php

namespace alekciy\ofd\interfaces;

/**
 * Общий интерфейс смены.
 */
interface ShiftInterface
{
	/**
	 * Номер фискального накопителя (ФН).
	 *
	 * @return string
	 */
	public function getFnFactoryNumber(): string;

	/**
	 * Номер смены.
	 *
	 * @return integer
	 */
	public function getShiftNumber(): int;

	/**
	 * Дата открытия.
	 *
	 * @return string
	 */
	public function getOpenDateTime(): string;

	/**
	 * Дата закрытия.
	 *
	 * @return string|null
	 */
	public function getCloseDateTime(): string;
}
