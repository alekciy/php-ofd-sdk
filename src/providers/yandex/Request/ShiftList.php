<?php

namespace alekciy\ofd\providers\yandex\Request;

use alekciy\ofd\providers\yandex\Model\Document;
use alekciy\ofd\providers\yandex\RequestPage;

/**
 * Список смен заданной ККТ.
 */
class ShiftList extends RequestPage
{
	public $method = 'POST';
	protected $path = '/v1/docs/aggregations/shifts';

	/** @var string Начало периода */
	public $start = '';

	/** @var string Окончание периода */
	public $end = '';

	/** @var array Список идентификаторов ККТ, по которым будет построен отчет */
	public $cashDeskIdList = [];

	/** @var array Список идентификаторов компаний, по ККТ (кассам) которых будет построен отчет */
	public $companyIdList = [];

	/** @var array Список идентификаторов точек продаж, по ККТ которых будет построен отчет */
	public $outletIdList = [];

	/** @var array Тип фискального документа (ФД) */
	public $documentTypeList = [];

	/**
	 * @inheritDoc
	 */
	public function getPropertyMap(): array
	{
		return array_merge(parent::getPropertyMap(), [
			'cashDeskIdList'   => ['body' => 'cashbox_ids'],
			'companyIdList'    => ['body' => 'company_ids'],
			'outletIdList'     => ['body' => 'retail_point_ids'],
			'documentTypeList' => ['body' => 'document_types'],
			'start'            => ['body' => 'begin'],
			'end'              => ['body' => 'end'],
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function getRuleList(): array
	{
		return array_merge(parent::getRuleList(), [
			'start' => ['required', ['dateFormat', 'Y-m-d H:i:s']],
			'end'   => ['required', ['dateFormat', 'Y-m-d H:i:s']],

			'cashDeskIdList'   => ['array'],
			'companyIdList'    => ['array'],
			'outletIdList'     => ['array'],
			'documentTypeList' => ['array', ['subset', [
				Document::TYPE_OPEN,
				Document::TYPE_CLOSE,
				Document::TYPE_STATE,
				Document::TYPE_CHECK,
				Document::TYPE_CHECK_CORRECT,
				Document::TYPE_STRICT,
				Document::TYPE_STRICT_CORRECT,
				Document::TYPE_REGISTRATION,
				Document::TYPE_REGISTRATION_CHANGE,
				Document::TYPE_FN_CLOSE,
			]]],
		]);
	}
}