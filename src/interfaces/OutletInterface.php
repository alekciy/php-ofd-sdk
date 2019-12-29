<?php

namespace alekciy\ofd\interfaces;

/**
 * Общий интерфейс торговой точки.
 */
interface OutletInterface
{
	/**
	 * Идентификатор торговой точки.
	 *
	 * @return string
	 */
	public function getId(): string;

	/**
	 * Имя торговой точки.
	 *
	 * @return string
	 */
	public function getName(): string;

	/**
	 * Адрес торговой точки.
	 *
	 * @return string
	 */
	public function getAddress(): string;
}
