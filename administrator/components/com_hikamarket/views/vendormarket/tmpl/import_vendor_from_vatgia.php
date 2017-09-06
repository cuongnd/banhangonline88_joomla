<?php
$doc = JFactory::getDocument();
JHtml::_('jquerybackend.base64');
JHtml::_('jquerybackend.auto_numeric');
$doc->addScript(JUri::root() . 'administrator/components/com_hikamarket/assests/js/view_vendormarket_import_vendor_from_vatgia.js');

?>
<div class="view-vendormarket-import_vendor_from_vatgia">
    <div>
        <button class="btn btn-primary btn-import-vendor" type="button">import vendor</button>
        <button class="btn btn-primary btn-cancel-import-vendor" type="button">cancel import vendor</button>
    </div>
    <h3 class="link"></h3>
    <div >total page <span class="total_page"></span></div>
    <div class="list-vendor">

    </div>
</div>
<?php
$js_content = '';
$doc = JFactory::getDocument();
ob_start();
?>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $(".view-vendormarket-import_vendor_from_vatgia").view_vendormarket_import_vendor_from_vatgia({});
    });
</script>
<?php
$js_content = ob_get_clean();
$js_content = JUtility::remove_string_javascript($js_content);
$doc->addScriptDeclaration($js_content);
?>

