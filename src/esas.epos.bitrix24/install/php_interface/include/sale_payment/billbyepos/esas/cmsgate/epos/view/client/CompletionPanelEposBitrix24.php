<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 24.06.2019
 * Time: 14:11
 */

namespace esas\cmsgate\epos\view\client;

use esas\cmsgate\utils\htmlbuilder\Attributes as attribute;
use esas\cmsgate\utils\htmlbuilder\Elements as element;


class CompletionPanelEposBitrix24 extends CompletionPanelEpos
{
    public function getCssClass4MsgSuccess()
    {
        return "alert alert-info";
    }

    public function getCssClass4MsgUnsuccess()
    {
        return "alert alert-danger";
    }

    public function getCssClass4TabHeaderLabel()
    {
        return "tab-header-label";
    }

    public function getCssClass4Tab()
    {
        return "bx-soa-section";
    }

    public function getCssClass4TabHeader()
    {
        return "bx-soa-section-title-container d-flex justify-content-between align-items-center flex-nowrap";
    }


    public function getCssClass4TabBodyContent()
    {
        return "bx-soa-section-content";
    }

    public function getCssClass4AlfaclickForm()
    {
        return "input-group";
    }

    public function getCssClass4FormInput()
    {
        return "form-control";
    }


    public function getCssClass4Button()
    {
        return "pull-right btn btn-primary pl-3 pr-3";
    }

    public function getModuleCSSFilePath()
    {
        return dirname(__FILE__) . "/bitrix.css";
    }

    /**
     * Переопределяем, чтобы не подключать accordion
     * @return array
     */
    public function addCss()
    {
        return array(
            element::styleFile($this->getModuleCSSFilePath()), // CSS, специфичный для модуля
            element::styleFile($this->getAdditionalCSSFilePath())
        );
    }


}