<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 25.06.2020
 * Time: 11:03
 */

namespace esas\cmsgate\epos;


use Bitrix\Crm\Integration\DocumentGenerator\DataProvider\Invoice;
use esas\cmsgate\bitrix\CmsgateEventHandler;
use esas\cmsgate\Registry;

class EventHandlerEpos extends CmsgateEventHandler
{
    public static function getDataProvider()
    {
        $result[InvoiceEpos::class] = [
            'NAME' => "InvoiceEPOS",
            'CLASS' => InvoiceEpos::class,
            'MODULE' => "crm", //иначе отфильтровывает
        ];
        return $result;
    }

    public static function onBeforeProcessDocument($event)
    {
        $document = $event->getParameter('document');
        /** @var \Bitrix\DocumentGenerator\Document $document */
        // добавить дополнительные описания полей
        // $document->setFields($newFields);
        // добавить значения полей
//        $fields = $document->getFields();
        $orderWrapper = Registry::getRegistry()->getOrderWrapper($_GET["value"]);

//        $document->setValues(['EposQrCode' => self::getQRCode()]);
    }

    public static function onDriverCollectClasses ()
    {
        return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, [
            'templateClassName' => TemplateEpos::class,
        ]);
    }


    public static function onDataProviderManagerFillSubstitutionProviders ()
    {
        return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, [
            Invoice::class => InvoiceEpos::class,
        ]);
    }

}