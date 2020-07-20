<?php
use esas\cmsgate\epos\RegistryEposBitrix24;
use esas\cmsgate\CmsPlugin;

if (!class_exists("esas\cmsgate\CmsPlugin")) {
    require_once(dirname(__FILE__) . '/vendor/esas/cmsgate-core/src/esas/cmsgate/CmsPlugin.php');

    (new CmsPlugin(dirname(__FILE__) . '/vendor', dirname(__FILE__)))
        ->setRegistry(new RegistryEposBitrix24())
        ->init();

}

