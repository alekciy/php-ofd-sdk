<?php

namespace alekciy\ofd\interfaces;

/**
 * Общий интерфейс документа.
 */
interface DocumentInterface
{
	/**
	 * Номер фискального накопителя (ФН).
	 *
	 * @return string
	 */
	public function getFnFactoryNumber(): string;

	/**
	 * Номер фискального документа (ФД).
	 *
	 * @return integer
	 */
	public function getNumber(): int;

	/**
	 * Дата создания.
	 *
	 * @return string
	 */
	public function getCreatAt(): string;

	/**
	 * Фискальный признак документа.
	 *
	 * @return string
	 */
	public function getFpd(): string;
}
