<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 19.06.2020
 * Time: 10:53
 */

namespace esas\cmsgate\epos;

use Bitrix\Crm\Integration\DocumentGenerator\DataProvider\Invoice;
use Bitrix\DocumentGenerator\DataProvider;
use esas\cmsgate\Registry;

class InvoiceEpos extends Invoice
{
    const FIELD_EPOS_SERVICE_DESCRIPTION = "EPOS_SERVICE_DESCRIPTION";
    const FIELD_EPOS_QR_CODE_SECTION = "EPOS_QR_CODE_SECTION";
    const FIELD_EPOS_QR_CODE = "EPOS_QR_CODE";
    const FIELD_EPOS_INVOICE_ID = "EPOS_INVOICE_ID";
    const FIELD_EPOS_INSTRUCTIONS_SECTION = "EPOS_INSTRUCTIONS_SECTION";

    public static function getDocxContent($filePath) {
        $zip = new \ZipArchive();
        $openResult = $zip->open($filePath);
        $content = $zip->getFromName('word/document.xml');
        $zip->close();

        // Extract the content of the first document
        $p = strpos($content, '<w:body');
        if ($p===false) exit("Tag <w:body> not found in document 1.");
        $p = strpos($content, '>', $p);
        $content = substr($content, $p+1);
        $p = strrpos($content, '<w:sectPr');
//        $p = strpos($content1, '</w:body>');
        if ($p===false)
            $p = strpos($content, '</w:body>');
        return substr($content, 0, $p);
    }


    public function getFields()
    {
        if($this->fields === null) {
            parent::getFields();
            $this->fields[self::FIELD_EPOS_SERVICE_DESCRIPTION] = [
                'VALUE' => [$this, 'getServiceDescription'],
//                'TITLE' => GetMessage('CRM_DOCGEN_DATAPROVIDER_INVOICE_DEAL_TITLE'),
            ];
            $this->fields[self::FIELD_EPOS_INVOICE_ID] = [
                'VALUE' => [$this, 'getInvoiceId'],
//                'TITLE' => GetMessage('CRM_DOCGEN_DATAPROVIDER_INVOICE_DEAL_TITLE'),
            ];
            $this->fields[self::FIELD_EPOS_QR_CODE_SECTION] = [
                'VALUE' => [$this, 'getQRCodeSection'],
//                'TITLE' => GetMessage('CRM_DOCGEN_DATAPROVIDER_INVOICE_DEAL_TITLE'),
            ];
            $this->fields[self::FIELD_EPOS_QR_CODE] = [
                'TYPE' => DataProvider::FIELD_TYPE_IMAGE,
                'VALUE' => [$this, 'getQRCode'],
//                'TITLE' => GetMessage('CRM_DOCGEN_DATAPROVIDER_INVOICE_DEAL_TITLE'),
            ];
            $this->fields[self::FIELD_EPOS_INSTRUCTIONS_SECTION] = [
                'VALUE' => [$this, 'getInstructionsSection'],
//                'TITLE' => GetMessage('CRM_DOCGEN_DATAPROVIDER_INVOICE_DEAL_TITLE'),
            ];
        }
        return $this->fields;
    }

    public function getServiceDescription() {
        return RegistryEpos::getRegistry()->getConfigWrapper()->getPaymentMethodDetails();
    }

    public function getInvoiceId() {
        $orderWrapper = Registry::getRegistry()->getOrderWrapper($this->getSource());
        return PaysystemConnectorEpos::getInvoiceId($orderWrapper);
    }

    public function getQRCode() {
        return "https://dh.img.tyt.by/n/it/kartinki_logo/07/6/title_logo_1x_rus_dom.png";
//        return dirname(dirname(dirname(dirname(__FILE__)))) . "/template/epos.png";
//        return "<w:p w14:paraId=\"007A4EF5\" w14:textId=\"77777777\" w:rsidR=\"003C5BB6\" w:rsidRDefault=\"003C5BB6\" w:rsidP=\"003C5BB6\"><w:r><w:t>Счет №</w:t></w:r><w:r w:rsidRPr=\"003C5BB6\"><w:rPr><w:b/><w:bCs/></w:rPr><w:t>W54</w:t></w:r><w:r><w:t xml:space=\"preserve\"> успешно выставлен в EPOS</w:t></w:r></w:p>";
    }

    public function getQRCodeSection() {
        return "QRCodeSection will be hear";
    }

    public function getInstructionsSection() {
        $templatesHome = dirname(dirname(dirname(dirname(__FILE__)))) . "/template/";
        $instructionsDocx = self::getDocxContent($templatesHome . "instructions.docx");

        $orderWrapper = Registry::getRegistry()->getOrderWrapper($this->getSource());
        return Registry::getRegistry()->getConfigWrapper()->cookText($instructionsDocx, $orderWrapper);
    }

    /**
     * @return string
     */
    public static function getLangName()
    {
        return GetMessage('CRM_DOCGEN_DATAPROVIDER_EPOS_INVOICE_TITLE');
    }
}