<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>
<div class="mb-4" >
    <?
    $webpayForm = $params['webpayForm'];
    if ($webpayForm) {
        echo $webpayForm;
    }
    ?>
</div>