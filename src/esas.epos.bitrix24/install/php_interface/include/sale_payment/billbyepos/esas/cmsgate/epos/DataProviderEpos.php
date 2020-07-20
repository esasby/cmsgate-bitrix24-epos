<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 19.06.2020
 * Time: 10:53
 */

namespace esas\cmsgate\epos;


use Bitrix\Crm\Integration\DocumentGenerator\DataProvider\Invoice;
use esas\cmsgate\Registry;

class DataProviderEpos extends Invoice
{
    public static function getDataProvider()
    {
        $result[DataProviderEpos::class] = [
            'NAME' => "InvoiceEPOS",
            'CLASS' => DataProviderEpos::class,
            'MODULE' => "crm", //иначе отфильтровывает
        ];
        return $result;
    }

    const FIELD_EPOS_QR_CODE = "EposQrCode";
    const FIELD_EPOS_INSTRUCTIONS = "EposInstructions";

    public function getFields()
    {
        if($this->fields === null) {
            parent::getFields();
            $this->fields[self::FIELD_EPOS_QR_CODE] = [
                'VALUE' => [$this, 'getQRCode'],
//                'TITLE' => GetMessage('CRM_DOCGEN_DATAPROVIDER_INVOICE_DEAL_TITLE'),
            ];
            $this->fields[self::FIELD_EPOS_INSTRUCTIONS] = [
                'VALUE' => [$this, 'getInstructions'],
//                'TITLE' => GetMessage('CRM_DOCGEN_DATAPROVIDER_INVOICE_DEAL_TITLE'),
            ];
        }
        return $this->fields;
    }

    function onBeforeProcessDocument($event)
    {
        $document = $event->getParameter('document');
        /** @var \Bitrix\DocumentGenerator\Document $document */
        // добавить дополнительные описания полей
        // $document->setFields($newFields);
        // добавить значения полей
//        $fields = $document->getFields();
        $orderWrapper = Registry::getRegistry()->getOrderWrapper($_GET["value"]);

        $document->setValues(['EposQrCode' => self::getQRCode()]);
    }

    public static function getQRCode() {
        return "<p>Счет №<b>W54</b> успешно
выставлен в EPOS</p>

<p>Вы можете оплатить
его наличными деньгами, пластиковой
карточкой и электронными деньгами, в
любом из отделений банков, кассах,
банкоматах, платежных терминалах, в
системе электронных денег, через
Интернет-банкинг, М-банкинг,
интернет-эквайринг</p>

<h3><b>Инструкция по оплате
счета в EPOS</b></h3>
<p>Для оплаты
счета в EPOS необходимо:</p>
<ul>
	<li><p>Выбрать дерево
	платежей ЕРИП</p>
	</li><li><p>Выбрать услугу:
	Сервис EPOS</p>
	</li><li><p>Ввести номер счета:
	&lt;b&gt;108-1-W54&lt;/b&gt;</p>
	</li><li><p>Проверить корректность
	информации</p>
	</li><li><p>Совершить платеж.</p>
</li></ul>";
    }

    public function getInstructions() {
        return "Hello EPOS instructions!";
    }

    /**
     * @return string
     */
    public static function getLangName()
    {
        return GetMessage('CRM_DOCGEN_DATAPROVIDER_EPOS_INVOICE_TITLE');
    }
}