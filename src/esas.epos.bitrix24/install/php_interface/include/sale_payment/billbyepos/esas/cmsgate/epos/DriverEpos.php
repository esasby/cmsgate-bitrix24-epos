<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 25.06.2020
 * Time: 11:03
 */

namespace esas\cmsgate\epos;

class DriverEpos
{
    public static function onDriverCollectClasses ()
    {
        return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, [
            'templateClassName' => TemplateEpos::class,
        ]);
    }

}