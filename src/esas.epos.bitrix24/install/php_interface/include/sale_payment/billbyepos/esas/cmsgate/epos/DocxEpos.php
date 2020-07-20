<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 25.06.2020
 * Time: 12:02
 */

namespace esas\cmsgate\epos;


use Bitrix\DocumentGenerator\Body\Docx;

class DocxEpos extends Docx
{
    protected function getXmlClassName(): string
    {
        return DocxXmlEpos::class;
    }


}