<?php

namespace alekciy\ofd\providers\yandex\Request;

use alekciy\ofd\providers\yandex\Model\Document;
use alekciy\ofd\providers\yandex\RequestPage;

/**
 * Список документов.
 */
class DocumentList extends RequestPage
{
	public $method = 'POST';
	protected $path = '/v1/docs/aggregations/receipts';

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

	/** @var array Тип операции */
	public $operationList = [];

	/** @var array Тип операции */
	public $taxList = [];

	/** @var string Фискальный признак документа (ФПД) */
	public $fpd = '';

	/** @var string Порядковый номер фискального документа */
	public $fdNumber = '';

	/** @var integer Список смен */
	public $shiftNumber;

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
			'operationList'    => ['body' => 'operation_types'],
			'taxList'          => ['body' => 'taxation_types'],
			'shiftNumber'      => ['body' => 'shifts'],
			'start'            => ['body' => 'date_from'],
			'end'              => ['body' => 'date_to'],
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

			'cashDeskIdList' => ['array'],
			'companyIdList'  => ['array'],
			'outletIdList'   => ['array'],
			'shiftNumber'    => ['integer', ['min', 1]],
			'taxList'        => ['array', ['subset', [
				Document::TAX_AGRICULTURAL,
				Document::TAX_OSN,
				Document::TAX_PATENT,
				Document::TAX_USN_INCOME,
				Document::TAX_USN_INCOME_EXPENDITURE,
			]]],
			'operationList' => ['array', ['subset', [
				Document::OPERATION_INCOME,
				Document::OPERATION_INCOME_RETURN,
				Document::OPERATION_OUTCOME,
				Document::OPERATION_OUTCOME_RETURN,
			]]],
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