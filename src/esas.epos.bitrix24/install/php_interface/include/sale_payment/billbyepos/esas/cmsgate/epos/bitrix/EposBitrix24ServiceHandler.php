<?

namespace esas\cmsgate\epos\bitrix;

use Bitrix\Main\Request;
use Bitrix\Main\Type\DateTime;
use Bitrix\Sale\Payment;
use Bitrix\Sale\PaySystem;
use esas\cmsgate\bitrix\CmsgateServiceHandler;
use esas\cmsgate\epos\protocol\EposInvoiceGetRs;
use esas\cmsgate\utils\CMSGateException;
use esas\epos\controllers\ControllerEposCallbackBitrix;
use Exception;

/**
 * Имя класса обязательно должно совпадать с именем родительского каталога и значением в ACTION_FILE в БД (\esas\cmsgate\bitrix\CmsgateCModule::addPaysys)
 * @package Sale\Handlers\PaySystem
 */
class EposBitrix24ServiceHandler extends CmsgateServiceHandler
{
    /**
     * @param Payment $payment
     * @param Request|null $request
     * @return PaySystem\ServiceResult
     * @throws \Bitrix\Main\LoaderException
     */
    public function initiatePay(Payment $payment, Request $request = null)
    {
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
}