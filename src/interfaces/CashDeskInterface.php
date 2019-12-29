<?php

namespace alekciy\ofd\interfaces;

/**
 * Общий интерфейс кассы.
 */
interface CashDeskInterface
{
	/**
	 * Номер фискального регистратора (ФН).
	 *
	 * @return string
	 */
	public function getFnFactoryNumber(): string;

	/**
	 * Серийный (заводской) номер кассы.
	 *
	 * @return string
	 */
	public function getKktFactoryNumber(): string;

	/**
	 * Регистрационный номер кассы (полученный в ФНС).
	 *
	 * @return string
	 */
	public function getKktRegNumber(): string;

	/**
	 * Название.
	 *
	 * @return string
	 */
	public function getName(): string;
}
