<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 25.06.2020
 * Time: 12:38
 */

namespace esas\cmsgate\epos;


use Bitrix\DocumentGenerator\Body\DocxXml;

class DocxXmlEpos extends DocxXml
{
    protected function printValue($value, $placeholder, $modifier = '', array $params = []): string
    {
        if ($this->isDocx($value))
            return $value;
        else
            return parent::printValue($value, $placeholder, $modifier, $params);
    }


    protected function isDocx($string): bool
    {
        return (preg_match('<w:p\s?[^\>]*\/?\s?>', $string) != false);
    }
}