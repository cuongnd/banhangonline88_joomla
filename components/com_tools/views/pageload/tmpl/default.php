<?php
$doc=JFactory::getDocument();
$doc->addScript(JUri::root().'components/com_tools/assets/js/jquery.view_pageload.js');
?>
<div class="view-pageload" >
    sdsdfsdfsdfdsfdsfd
</div>
<?php
$js_content = '';
ob_start();
?>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            $(".view-pageload").view_pageload({

            });
        });
    </script>
<?php
$js_content = ob_get_clean();
$js_content = JUtility::remove_string_javascript($js_content);
$doc->addScriptDeclaration($js_content);
?>