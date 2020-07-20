<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 25.06.2020
 * Time: 11:08
 */

namespace esas\cmsgate\epos;


use Bitrix\DocumentGenerator\Template;

class TemplateEpos extends Template
{
    public function getBodyClassName(): string
    {
        return DocxEpos::class;
    }

}