<?

namespace Sale\Handlers\PaySystem;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/php_interface/include/sale_payment/esas.epos.bitrix24/init.php");

use Bitrix\Main\Error;
use Bitrix\Main\Event;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Request;
use Bitrix\Main\Type\DateTime;
use Bitrix\Sale\Payment;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\PaySystem\ServiceResult;
use esas\cmsgate\bitrix\CmsgateServiceHandler;
use esas\cmsgate\epos\controllers\ControllerEposAddInvoice;
use esas\cmsgate\epos\controllers\ControllerEposCompletionPage;
use esas\cmsgate\epos\protocol\EposInvoiceGetRs;
use esas\cmsgate\Registry;
use esas\cmsgate\utils\CMSGateException;
use esas\cmsgate\utils\Logger;
use esas\epos\controllers\ControllerEposCallbackBitrix;
use Exception;
use Throwable;

class EposHandlerBitrix24 extends CmsgateServiceHandler
{
    /**
     * @param Payment $payment
     * @param Request|null $request
     * @return PaySystem\ServiceResult
     * @throws \Bitrix\Main\LoaderException
     */
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
                $controller = new ControllerEposCompletionPage();
                $completionPanel = $controller->process($orderWrapper->getOrderId());
                $extraParams['completionPanel'] = $completionPanel;
                $this->setExtraParams($extraParams);
                return $this->showTemplate($payment, 'template');
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

    /**
     * @param Request $request
     * @return mixed
     * @throws CMSGateException
     */
    public function getPaymentIdFromRequestSafe(Request $request)
    {
        $controller = new ControllerEposCallbackBitrix();
        $eposInvoiceGetRs = $controller->process();
        CMSGateException::throwIfNull($eposInvoiceGetRs, "Epos get invoice rs is null");
        $_SESSION["epos_invoice_get_rs"] = $eposInvoiceGetRs; // для корректной работы processRequest

        $dbPayment = \Bitrix\Sale\PaymentCollection::getList([
            'select' => ['ID'],
            'filter' => [
                '=ORDER_ID' => $eposInvoiceGetRs->getOrderNumber(),
            ]
        ]);
        while ($item = $dbPayment->fetch()) {
            return $item["ID"];
        }
        throw new CMSGateException("Can not find payments for order[" . $eposInvoiceGetRs->getOrderNumber() . "]");
    }

    /**
     * @param Payment $payment
     * @param Request $request
     * @return PaySystem\ServiceResult
     * @throws Exception
     */
    public function processRequestSafe(Payment $payment, Request $request)
    {
        $result = new PaySystem\ServiceResult();
        /** @var EposInvoiceGetRs $eposInvoiceGetRs */
        $eposInvoiceGetRs = $_SESSION["epos_invoice_get_rs"];
        CMSGateException::throwIfNull($eposInvoiceGetRs, "Epos invoice is not loaded");
        $fields = array(
            "PS_STATUS" => $eposInvoiceGetRs->isStatusPayed() ? "Y" : "N",
            "PS_STATUS_CODE" => $eposInvoiceGetRs->getStatus(),
            "PS_STATUS_DESCRIPTION" => $eposInvoiceGetRs->getResponseMessage(),
            "PS_STATUS_MESSAGE" => "",
            "PS_SUM" => $eposInvoiceGetRs->getAmount()->getValue(),
            "PS_CURRENCY" => $eposInvoiceGetRs->getAmount()->getCurrency(),
            "PS_RESPONSE_DATE" => new DateTime(),
        );
        $result->setPsData($fields);
        $result->setOperationType(PaySystem\ServiceResult::MONEY_COMING);
        return $result;
    }

    public function sendResponse(PaySystem\ServiceResult $result, Request $request)
    {
    }

    public static function onCrmInvoiceAdd(Event $event)
    {
        if (Loader::includeModule(Registry::getRegistry()->getModuleDescriptor()->getModuleMachineName())) {
            try {
                $orderWrapper = Registry::getRegistry()->getOrderWrapper($event->getParameter('ID'));
                // проверяем, привязан ли к заказу extId, если да,
                // то счет не выставляем, а просто прорисовываем старницу
                if (empty($orderWrapper->getExtId())) {
                    $controller = new ControllerEposAddInvoice();
                    $controller->process($orderWrapper);
                }
                return true;
            } catch (Throwable $e) {
                Logger::getLogger("onCrmInvoiceAdd")->logger->error("Exception:", $e);
                return false;
            }
        } else {
            return false;
        }
    }
}