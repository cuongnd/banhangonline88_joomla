<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php if( !$this->singleSelection ) { ?>
<fieldset>
	<div class="toolbar" id="toolbar" style="float: right;">
		<button class="btn" type="button" onclick="if(document.hikamarket_form.boxchecked.value==0){alert('<?php echo JText::_('PLEASE_SELECT_SOMETHING', true); ?>');}else{hikamarket.submitform('useselection',this.form);}"><img src="<?php echo HIKASHOP_IMAGES; ?>add.png"/><?php echo JText::_('OK'); ?></button>
	</div>
</fieldset>
<?php } else { ?>
<script type="text/javascript">
function hikamarket_setId(id) {
	var form = document.getElementById("hikamarket_form");
	form.cid.value = id;
	hikamarket.submitform("useselection",form);
}
</script>
<?php } ?>
<form action="index.php?option=<?php echo HIKAMARKET_COMPONENT ?>&amp;ctrl=<?php echo JRequest::getCmd('ctrl'); ?>" method="post" name="hikamarket_form" id="hikamarket_form">
	<div>
		<input type="text" onchange="window.oTrees['categoryselection'].search(this.value);" onkeyup="window.oTrees['categoryselection'].search(this.value);"/>
<?php
$doc = JFactory::getDocument();
$doc->addScript(HIKAMARKET_JS.'otree.js');
$doc->addStyleSheet(HIKAMARKET_CSS.'otree.css');
?>
<div id="categoryselection_otree" class="oTree"></div>
<script type="text/javascript">
var options = {rootImg:"<?php echo HIKAMARKET_IMAGES; ?>otree/", showLoading:false};
var data = [<?php
$cpt = count($this->elements)-1;
$sep = '';
$rootDepth = 0;
foreach($this->elements as $k => $element) {
	$next = null;
	if($k < $cpt)
		$next = $this->elements[$k+1];

	$status = 4;
	if(!empty($next) && $next->category_parent_id == $element->category_id)
		$status = 2;
	if($element->category_type == 'root') {
		$status = 5;
		$rootDepth = (int)$element->category_depth + 1;
	}

	echo $sep.'{"status":'.$status.',"name":"'.str_replace('"','&quot;',$element->category_name).'"';

	if($element->category_type == 'root') {
		echo ',"icon":"world","noselection":1';
	}

	$sep = '';
	if(!empty($next)) {
		if($next->category_depth > $element->category_depth && $element->category_type != 'root') {
			echo ',"data":[';
		} else if($next->category_depth < $element->category_depth) {
			echo '}'.str_pad('', ($element->category_depth - $next->category_depth) * 2, ']}');
			$sep = ',';
		} else {
			echo '}';
			$sep = ',';
		}
	} else {
		echo '}'.str_pad('', ($element->category_depth - $rootDepth) * 2, ']}');
	}
}
?>];
var callbackSelection = function(tree,id) {
	var node = tree.get(id);
	if( node.value ) {
	}
}
categoryselection = new oTree("categoryselection",options,null,data,false);
categoryselection.addIcon('world','world.png');
categoryselection.callbackSelection = callbackSelection;
categoryselection.render(true);
</script>
	</div>
<?php if( $this->singleSelection ) { ?>
	<input type="hidden" name="cid" value="0" />
<?php } ?>
	<input type="hidden" name="option" value="<?php echo HIKAMARKET_COMPONENT; ?>" />
	<input type="hidden" name="task" value="select" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="confirm" value="<?php echo $this->confirm ? '1' : '0'; ?>" />
	<input type="hidden" name="single" value="<?php echo $this->singleSelection ? '1' : '0'; ?>" />
	<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_('form.token'); ?>
</form>
<script type="text/javascript">
document.adminForm = document.getElementById("hikamarket_form");
</script>
