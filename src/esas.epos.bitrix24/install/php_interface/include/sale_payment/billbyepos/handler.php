<?php

namespace Sale\Handlers\PaySystem;

use Bitrix\Main\Error;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Request;
use Bitrix\Sale;
use Bitrix\Sale\Payment;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\PaySystem\Service;
use Bitrix\Sale\PaySystem\ServiceResult;
use esas\cmsgate\epos\controllers\ControllerEposAddInvoice;
use esas\cmsgate\epos\controllers\ControllerEposCompletionPage;
use esas\cmsgate\epos\controllers\ControllerEposWebpayForm;
use esas\cmsgate\epos\RegistryEposBitrix24;
use esas\cmsgate\epos\utils\QRUtils;
use esas\cmsgate\Registry;
use esas\cmsgate\utils\Logger;
use esas\cmsgate\wrappers\OrderWrapperImpl;
use Throwable;

PaySystem\Manager::includeHandler('Billby');
Loc::loadMessages(__FILE__);
require_once("init.php");

/**
 * Class BillByHandler
 * @package Sale\Handlers\PaySystem
 */
class BillByEposHandler extends BillByHandler
{
    protected $logger;

    public function __construct($type, Service $service)
    {
        parent::__construct($type, $service);
        $this->logger = Logger::getLogger(get_class($this));
    }

    public function initiatePay(Payment $payment, Request $request = null)
    {
        if (Loader::includeModule(Registry::getRegistry()->getModuleDescriptor()->getModuleMachineName())) {
            try {
                $orderWrapper = Registry::getRegistry()->getOrderWrapper($payment->getOrderId());
                // проверяем, привязан ли к заказу extId, если да,
                // то счет не выставляем, а просто прорисовываем старницу
                if (empty($orderWrapper->getExtId())) {
                    $controller = new ControllerEposAddInvoice();
                    $controller->process($orderWrapper);
                }
                if (array_key_exists("paySystemId", $_REQUEST)) {
                    $controller = new ControllerEposWebpayForm();
                    $eposWebPayRs = $controller->process($orderWrapper);
                    $extraParams['webpayForm'] = $eposWebPayRs->getHtmlForm();
                    $template = 'template_webpay';
                } else {
                    $controller = new ControllerEposCompletionPage();
                    $completionPanel = $controller->process($orderWrapper->getOrderId());
                    $extraParams = $this->getPreparedParams($payment, $request);
                    $extraParams['completionPanel'] = $completionPanel;
                    if (array_key_exists('pdf', $_REQUEST)) {
                        ob_clean(); // иначе возникает ошибка Some data has already been output, can't send PDF file
                        $template = 'template_pdf';
                    } else
                        $template = 'template';
                }
                $this->setExtraParams($extraParams);
                return $this->showTemplate($payment, $template);
            } catch (Throwable $e) {
                $this->logger->error("Exception:", $e);
                $result = new ServiceResult();
                $result->addError(new Error($e->getMessage()));
                return $result;
            }
        } else {
            $result = new ServiceResult();
            $result->addError(new Error(Loc::getMessage('SALE_HPS_PAYMENTGATE_MODULE_NOT_FOUND')));
            return $result;
        }
    }

    public function getDemoParams()
    {
        $data = parent::getDemoParams();
        $orderWrapper = new OrderWrapperImpl(
            (isset($data['ACCOUNT_NUMBER']) ? $data['ACCOUNT_NUMBER'] : "108"),
            "23",
            "Иванов Иван",
            "+375172345678",
            "ivanov@mail.ru",
            "Беларусь, г.Минск, ул.Советская 23",
            "54",
            "BYN",
            null,
            "42",
            "N"
        );
        $completionPanel = RegistryEposBitrix24::getRegistry()->getCompletionPanel($orderWrapper);
        $completionPanel->setQrCode(QRUtils::createQRCode("https://www.e-pos.by/", ""));
        $data['completionPanel'] = $completionPanel;
        return $data;
    }


}