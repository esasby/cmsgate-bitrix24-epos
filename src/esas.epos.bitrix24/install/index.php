<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/esas.epos.bitrix24/install/php_interface/include/sale_payment/billbyepos/init.php");

use Bitrix\Sale\PaySystem\Manager;
use esas\cmsgate\bitrix\CmsgateEventHandler;
use esas\cmsgate\ConfigFields;
use esas\cmsgate\epos\InvoiceEpos;
use esas\cmsgate\epos\EventHandlerEpos;
use esas\cmsgate\Registry;
use Sale\Handlers\PaySystem\EposHandlerBitrix24;

if (class_exists('esas_epos_bitrix24')) return;

class esas_epos_bitrix24 extends \esas\cmsgate\bitrix\CmsgateCModuleBitrix24
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

//    protected function addFilesToInstallList()
//    {
//        parent::addFilesToInstallList();
//        $this->installFilesList[] = self::MODULE_SUB_PATH . self::PRINT_FORM_MODULE_ID;
//    }
//
//    protected function addPaysys()
//    {
//        return $this->addPaySystemWebpay();
//    }
//
//    function InstallDB($arParams = array())
//    {
//        $retA = parent::InstallDB($arParams);
//        $retB = $this->addPaySystem4PrintForm() ;
//        return $retA && $retB;
//    }
//
//
//    /**
//     * @return \Bitrix\Main\Entity\AddResult
//     */
//    protected function addPaySystemWebpay()
//    {
//        return Manager::Add(
//            array(
//                "NAME" => Registry::getRegistry()->getTranslator()->getConfigFieldDefault(ConfigFields::paymentMethodName()),
//                "DESCRIPTION" => "Оплата картой",  //todo
//                "ACTION_FILE" => Registry::getRegistry()->getModuleDescriptor()->getModuleMachineName(),
//                "LOGOTIP" => CFile::MakeFileArray('/bitrix/images/sale/sale_payments/' . Registry::getRegistry()->getModuleDescriptor()->getModuleMachineName() . '.png'),
//                "ACTIVE" => "N",
//                "ENTITY_REGISTRY_TYPE" => $this->getPaysystemType(), // без этого созданная платежная система не отображается в списке
//                "NEW_WINDOW" => "N",
//                "HAVE_PREPAY" => "N",
//                "HAVE_RESULT" => "N",
//                "HAVE_ACTION" => "N",
//                "HAVE_PAYMENT" => "Y",
//                "HAVE_RESULT_RECEIVE" => "Y",
//                "ENCODING" => "utf-8",
//                "SORT" => 100,
//            )
//        );
//    }
//
//    /**
//     * Необходимо создавать отдельную платежную систему для платежной формы (форма которая будет доступна клиенту по публичной ссылке).
//     * ACTION_FILE должен начинаться со слова bill
//     * @return \Bitrix\Main\Entity\AddResult
//     */
//    protected function addPaySystem4PrintForm()
//    {
//        return Manager::Add(
//            array(
//                "NAME" => Registry::getRegistry()->getTranslator()->getConfigFieldDefault(ConfigFields::paymentMethodName()),
//                "DESCRIPTION" => "Оплата картой",  //todo
//                "ACTION_FILE" => self::PRINT_FORM_MODULE_ID,
//                "LOGOTIP" => CFile::MakeFileArray('/bitrix/images/sale/sale_payments/' . Registry::getRegistry()->getModuleDescriptor()->getModuleMachineName() . '.png'),
//                "ACTIVE" => "N",
//                "ENTITY_REGISTRY_TYPE" => $this->getPaysystemType(), // без этого созданная платежная система не отображается в списке
//                "NEW_WINDOW" => "N",
//                "HAVE_PREPAY" => "N",
//                "HAVE_RESULT" => "N",
//                "HAVE_ACTION" => "N",
//                "HAVE_PAYMENT" => "Y",
//                "HAVE_RESULT_RECEIVE" => "Y",
//                "ENCODING" => "utf-8",
//                "SORT" => 100,
//            )
//        );
//    }


}
