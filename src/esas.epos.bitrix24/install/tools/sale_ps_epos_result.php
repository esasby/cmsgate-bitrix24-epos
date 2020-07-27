<?php
use \Bitrix\Main\Application;
use \Bitrix\Sale\PaySystem;
use esas\cmsgate\CmsConnectorBitrix24;

define("STOP_STATISTICS", true);
define('NO_AGENT_CHECK', true);
define('NOT_CHECK_PERMISSIONS', true);
define("DisableEventsCheck", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/sale_payment/billbyepos/init.php");


global $APPLICATION;

if (CModule::IncludeModule("sale"))
{
	$context = Application::getInstance()->getContext();
	$request = $context->getRequest();

	$item = PaySystem\Manager::getById(CmsConnectorBitrix24::getInstance()->getPaysystemId());
	if ($item !== false)
	{
	    $item["ACTION_FILE"] = "esas_epos_bitrix24";
		$service = new PaySystem\Service($item);
		if ($service instanceof PaySystem\Service)
		{
			$result = $service->processRequest($request);
		}
	}
	else
	{
		$debugInfo = http_build_query($request->toArray(), "", "\n");
		if (empty($debugInfo))
		{
			$debugInfo = file_get_contents('php://input');
		}
		PaySystem\Logger::addDebugInfo('Pay system not found. Request: '.($debugInfo ? $debugInfo : "empty"));
	}
}

$APPLICATION->FinalActions();
die();