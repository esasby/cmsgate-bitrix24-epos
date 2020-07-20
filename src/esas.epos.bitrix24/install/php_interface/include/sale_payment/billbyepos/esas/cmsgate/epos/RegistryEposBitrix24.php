<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 01.10.2018
 * Time: 12:05
 */

namespace esas\cmsgate\epos;

use esas\cmsgate\CmsConnectorBitrix24;
use esas\cmsgate\descriptors\ModuleDescriptor;
use esas\cmsgate\descriptors\VendorDescriptor;
use esas\cmsgate\descriptors\VersionDescriptor;
use esas\cmsgate\epos\view\client\CompletionPanelEpos;
use esas\cmsgate\epos\view\client\CompletionPanelEposBitrix24;
use esas\cmsgate\view\admin\AdminViewFields;
use CMain;
use COption;
use esas\cmsgate\view\admin\ConfigFormBitrix;

class RegistryEposBitrix24 extends RegistryEpos
{
    public function __construct()
    {
        $this->cmsConnector = new CmsConnectorBitrix24();
        $this->paysystemConnector = new PaysystemConnectorEpos();
    }


    /**
     * Переопделение для упрощения типизации
     * @return RegistryEposBitrix24
     */
    public static function getRegistry()
    {
        return parent::getRegistry();
    }

    /**
     * @throws \Exception
     */
    public function createConfigForm()
    {
        $managedFields = $this->getManagedFieldsFactory()->getManagedFieldsExcept(AdminViewFields::CONFIG_FORM_COMMON,
            [
                ConfigFieldsEpos::shopName(),
                ConfigFieldsEpos::paymentMethodName(),
                ConfigFieldsEpos::paymentMethodDetails(),
                ConfigFieldsEpos::completionCssFile(),
                ConfigFieldsEpos::webpaySection()
            ]);
        $configForm = new ConfigFormBitrix(
            AdminViewFields::CONFIG_FORM_COMMON,
            $managedFields);
        return $configForm;
    }

    function getUrlWebpay($orderId)
    {
        global $APPLICATION;
        return (CMain::IsHTTPS() ? "https" : "http")
            . "://"
            . ((defined("SITE_SERVER_NAME") && strlen(SITE_SERVER_NAME) > 0) ? SITE_SERVER_NAME : COption::GetOptionString("main", "server_name", "")) . $APPLICATION->GetCurUri();
    }

    public function getCompletionPanel($orderWrapper)
    {
        $completionPanel = new CompletionPanelEposBitrix24($orderWrapper);
        return $completionPanel;
    }


    public function createModuleDescriptor()
    {
        return new ModuleDescriptor(
            "esas.epos.bitrix24",
            new VersionDescriptor("1.10.0", "2020-06-17"),
            "Прием платежей через ЕРИП (сервис EPOS)",
            "https://bitbucket.esas.by/projects/CG/repos/cmsgate-bitrix24-epos/browse",
            VendorDescriptor::esas(),
            "Выставление пользовательских счетов в ЕРИП"
        );
    }
}