<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/esas.epos.bitrix24/install/php_interface/include/sale_payment/billbyepos/init.php");

use esas\cmsgate\bitrix\CmsgateCModuleBitrix24;
use esas\cmsgate\bitrix\CmsgateEventHandler;
use esas\cmsgate\epos\EventHandlerEpos;
use esas\cmsgate\Registry;

if (class_exists('esas_epos_bitrix24')) return;

class esas_epos_bitrix24 extends CmsgateCModuleBitrix24
{
    const PRINT_FORM_MODULE_ID = 'billbyepos'; // обязательно должен начинаться с bill (см. bitrix/modules/crm/classes/general/crm_pay_system.php:1763)

    function InstallEvents()
    {
        $eventManager = \Bitrix\Main\EventManager::getInstance();
//        $eventManager->registerEventHandler('crm', 'onCrmInvoiceAdd', Registry::getRegistry()->getModuleDescriptor()->getModuleMachineName(), EposHandlerBitrix24::class, 'onCrmInvoiceAdd');
        $eventManager->registerEventHandler('sale', 'OnGetBusinessValueGroups', Registry::getRegistry()->getModuleDescriptor()->getModuleMachineName(), CmsgateEventHandler::class, 'onGetBusValueGroups');
        $eventManager->registerEventHandler('documentgenerator', 'onGetDataProviderList', Registry::getRegistry()->getModuleDescriptor()->getModuleMachineName(), EventHandlerEpos::class, 'getDataProvider');
        $eventManager->registerEventHandler('documentgenerator', 'onBeforeProcessDocument', Registry::getRegistry()->getModuleDescriptor()->getModuleMachineName(), EventHandlerEpos::class, 'onBeforeProcessDocument');
        $eventManager->registerEventHandler('documentgenerator', 'onDriverCollectClasses', Registry::getRegistry()->getModuleDescriptor()->getModuleMachineName(), EventHandlerEpos::class, 'onDriverCollectClasses');
        $eventManager->registerEventHandler('documentgenerator', 'onDataProviderManagerFillSubstitutionProviders', Registry::getRegistry()->getModuleDescriptor()->getModuleMachineName(), EventHandlerEpos::class, 'onDataProviderManagerFillSubstitutionProviders');
    }

    function UnInstallEvents()
    {
        $eventManager = \Bitrix\Main\EventManager::getInstance();
//        $eventManager->unRegisterEventHandler('crm', 'onCrmInvoiceAdd', Registry::getRegistry()->getModuleDescriptor()->getModuleMachineName(), EposHandlerBitrix24::class, 'onCrmInvoiceAdd');
        $eventManager->unRegisterEventHandler('sale', 'OnGetBusinessValueGroups', Registry::getRegistry()->getModuleDescriptor()->getModuleMachineName(), CmsgateEventHandler::class, 'onGetBusValueGroups');
        $eventManager->unRegisterEventHandler('documentgenerator', 'onGetDataProviderList', Registry::getRegistry()->getModuleDescriptor()->getModuleMachineName(), EventHandlerEpos::class, 'getDataProvider');
        $eventManager->unRegisterEventHandler('documentgenerator', 'onBeforeProcessDocument', Registry::getRegistry()->getModuleDescriptor()->getModuleMachineName(), EventHandlerEpos::class, 'onBeforeProcessDocument');
        $eventManager->unRegisterEventHandler('documentgenerator', 'onDriverCollectClasses', Registry::getRegistry()->getModuleDescriptor()->getModuleMachineName(), EventHandlerEpos::class, 'onDriverCollectClasses');
        $eventManager->unRegisterEventHandler('documentgenerator', 'onDataProviderManagerFillSubstitutionProviders', Registry::getRegistry()->getModuleDescriptor()->getModuleMachineName(), EventHandlerEpos::class, 'onDataProviderManagerFillSubstitutionProviders');
    }

    public function getModuleActionName() {
        return "billbyepos";
    }

    protected function addFilesToInstallList()
    {
        parent::addFilesToInstallList();
        $this->installFilesList[] = "/components/bitrix/crm.invoice.payment.client/templates/.default/result_modifier.php";
        $this->installFilesList[] = "/php_interface/include/sale_payment/esas_epos_bitrix24";
        $this->installFilesList[] = "/tools/sale_ps_epos_result.php";
        $this->installFilesList[] = "/images/sale/sale_payments/epos_webpay.png";
    }
}
