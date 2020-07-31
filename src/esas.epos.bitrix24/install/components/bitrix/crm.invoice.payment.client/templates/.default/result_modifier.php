<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 23.07.2020
 * Time: 12:45
 */

use esas\cmsgate\CmsConnectorBitrix24;
use esas\cmsgate\epos\RegistryEposBitrix24;
use esas\cmsgate\epos\view\client\ClientViewFieldsEpos;

/**
 * result_modifier используется для того, чтобы на странице публичной ссылки подменить логоти
 * и подпись для оплаты (вместо лого и текста epos используются лого и текст webpay )
 */
$eposPaySystemId = CmsConnectorBitrix24::getInstance()->getPaysystemId();
if ($eposPaySystemId > 0) {
    foreach ($arResult['PAYSYSTEMS_LIST'] as $key => &$paySystem) {
        if ($paySystem['ID'] == $eposPaySystemId) {
            $paySystem['NAME'] = RegistryEposBitrix24::getRegistry()->getTranslator()->translate(ClientViewFieldsEpos::WEBPAY_TAB_LABEL);
            $paySystem["LOGOTIP"] = '/bitrix/images/sale/sale_payments/epos_webpay.png';
        }
    }
    unset($paySystem);
}