<?php // no direct access

/*------------------------------------------------------------------------
# com_invoices - Invoices for Joomla
# ------------------------------------------------------------------------
# author				Germinal Camps
# copyright 			Copyright (C) 2012 JoomlaFinances.com. All Rights Reserved.
# @license				http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: 			http://www.JoomlaFinances.com
# Technical Support:	Forum - http://www.JoomlaFinances.com/forum
-------------------------------------------------------------------------*/

//no direct access
defined('_JEXEC') or die('Restricted access.');

$itemid = $this->params->get('itemid');
if($itemid != "") $itemid = "&Itemid=" . $itemid;

$affiliate_link = AffiliateHelper::get_account_link();
$atid = AffiliateHelper::getCurrentUserAtid();
?>

<div class="page-header">
    <h1><?php echo JText::_('AFFILIATE_TITLE'); ?></h1>
</div>

<?php echo AffiliateHelper::nav_tabs(); ?>

<?php
$intro = new stdClass();

$intro->text = $this->params->get('textmarketings');

$dispatcher = JDispatcher::getInstance();
$plug_params = new JRegistry('');

JPluginHelper::importPlugin('content');
$results = $dispatcher->trigger('onContentPrepare', array ('com_affiliatetracker.marketings', &$intro, &$plug_params, 0));

echo $intro->text;
?>

<div class="row-fluid at_module_wrapper" >
    <?php
    $modules = JModuleHelper::getModules("at_marketing");
    $document	=JFactory::getDocument();
    $renderer	= $document->loadRenderer('module');
    $attribs 	= array();
    $attribs['style'] = 'xhtml';
    foreach ( @$modules as $mod )
    {
        echo $renderer->render($mod, $attribs);
    }
    ?>
</div>

<div class="card-at-columns">

<?php
$k = 0;
$total_comission = 0 ;
$total = 0 ;

for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
    $row =$this->items[$i];
    $html = str_replace(array('{affiliate_link}', '{atid}'), array($affiliate_link, $atid), $row->html_code);
    ?>
        <div class="card-at">
            <div class="card-at-img-top">
                <?php echo $html; ?>
            </div>
            <div class="card-at-block">
                <h4 class="card-at-title"><?php echo $row->title; ?></h4>
                <p class="card-at-text"><?php echo $row->description; ?></p>
                <div class="code-to-copy">
                    <textarea class="boxsizingBorder hidden" cols="10" rows="5" id="html<?php echo $i; ?>"><?php echo htmlspecialchars($html); ?></textarea>
                </div>
                <div class="card-at-button">
                    <button class="btn btn-info" onclick="showHTML('<?php echo $i; ?>')"><?php echo JText::_('COPY_HTML'); ?></button>
                </div>
            </div>
        </div>

    <?php
    $k = 1 - $k;
}
?>

</div>

<script type="text/javascript">
    function showHTML(index) {
        jQuery("#html" + index).removeClass("hidden").select();
    }
</script>

<?php
$modules = JModuleHelper::getModules("at_bottom");
$document	=JFactory::getDocument();
$renderer	= $document->loadRenderer('module');
$attribs 	= array();
$attribs['style'] = 'xhtml';
foreach ( @$modules as $mod )
{
    echo $renderer->render($mod, $attribs);
}
?>
<input type="hidden" name="option" value="com_affiliatetracker" />
<input type="hidden" name="view" value="marketings" />
<input type="hidden" name="Itemid" value="<?php echo $this->params->get('itemid'); ?>" />

<input type="hidden" name="filter_order" value="<?php echo JRequest::getVar('filter_order'); ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo JRequest::getVar('filter_order_Dir'); ?>" />
</form>
<div align="center"><?php echo AffiliateHelper::showATFooter(); ?></div>
