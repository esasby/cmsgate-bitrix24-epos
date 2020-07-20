<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use esas\cmsgate\Registry;
use esas\cmsgate\view\admin\ConfigFormBitrix;

require_once('init.php');

// $arPSCorrespondence - старый формат описания настроек, $data - новый
$data = Registry::getRegistry()->getConfigForm()->generate();
$description = ConfigFormBitrix::generateModuleDescription();

