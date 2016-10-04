<?php
/**
 * @package 	BT Smart Search
 * @version	1.0
 * @created	Dec 2012
 * @author	BowThemes
 * @email	support@bowthemes.com
 * @website	http://bowthemes.com
 * @support     Forum - http://bowthemes.com/forum/
 * @copyright   Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license     http://bowthemes.com/terms-and-conditions.html
 *
 */
defined('_JEXEC') or die;
	$document	= JFactory::getDocument();
	$value = JRequest::getVar('q');
	$config=JURI::root().'modules/mod_bt_smartsearch/tmpl/images/config.jpg';
	$output = '<div class="borderinput"><input name="q" id="mod-bt-smartsearchword"  class="inputboxsearch" type="text" value="'. ($value ? $value : $opensearch_title).'"  onblur="if (this.value==\'\') this.value=\''.$opensearch_title.'\';" onfocus="if (this.value==\''.$opensearch_title.'\') this.value=\'\';" />';
	if($show_advanced  == 1){
	$output .='<a id ="btss-advanced"><img src="'.$config.'"  alt="config"/></a>';	}
	$output .='</div>';	
	$image=JURI::root().'modules/mod_bt_smartsearch/tmpl/images/button.jpg';
	$button = '<button type="submit" class="btnsearch" >
					<img src="'.$image.'" width="57" height="27" alt="submit" />
			   </button>';	
	if($show_button ==1){
		switch($button_pos){
		case'top':			
			$document->addStyleDeclaration('.borderinput{margin-top:35px;} .btnsearch{position:absolute;top: -30px; left: -4px;}');
			break;
		case'left':
			$document->addStyleDeclaration('.borderinput{margin-left:63px;} .btnsearch{position:absolute;top: 0px; left: -4px;}');				
			break;
		case'right':
			$document->addStyleDeclaration('.borderinput{margin-right:63px;} .btnsearch{position:absolute;top: 0px; right: -4px;}');		
			break;
		case'bottom':
				$document->addStyleDeclaration(' .btnsearch{position:absolute;top: 30px; left: -4px;} .btsmartsearch{margin-left:60px;} .smartsearch{margin-bottom:20px;}');
			break;
		}
	}
	JHtml::stylesheet('com_finder/finder.css', false, true, false);	
	?>

<script type="text/javascript">
jQuery(document).ready(function(){	
	jQuery('#btss-advanced').click(function(){
	if (jQuery(".smartsearch-advanced:first").is(":hidden")) {
		jQuery(".smartsearch-advanced").css('width','100%');			
		jQuery("#btss-advanced ").css({'height':'28px','background':'#FFFFFF','border-left':'1px solid #CCCCCC','border-top':'1px solid #CCCCCC','border-right':'1px solid #CCCCCC'});		
		jQuery(".smartsearch-advanced").slideDown("slow");
		} else {
		jQuery(".smartsearch-advanced").slideUp('slow');
		jQuery("#btss-advanced").css({'border':'1px solid #FFFFFF','height':'18px'});	
		}
	});
	jQuery(".smartsearch-advanced").hide();
	  jQuery("#btss-advanced").hover(function () {
		jQuery(this).append('<div class="tooltipsmartseach"><p> Advance search</p></div>');
		jQuery('.tooltipsmartseach').css('background','#545454');
		jQuery('.tooltipsmartseach >p').css('margin-left','5px');
		jQuery('.tooltipsmartseach >p').css('margin-top','3px');
	  }, function () {
		jQuery("div.tooltipsmartseach").remove();
	  });
	jQuery('.inputboxsmart').data("placeholder","Select Frameworks...").chosen();	
	
	jQuery('#mod-bt-smartsearchword').keydown(function (event) {
		if(jQuery('#mod-bt-smartsearchword').val()!=""){
		var keypressed = event.keyCode || event.which;
			if (keypressed == 13) {
				$(this).closest('form').submit();
			}
		}
	});
	jQuery('.btnsearch').click(function(){
		if(jQuery('#mod-bt-smartsearchword').val()!=""){
			if(jQuery('#mod-bt-smartsearchword').val()!="<?php echo $opensearch_title; ?>"){			
			var keypressed = event.keyCode || event.which;
				if (keypressed == 13) {
					$(this).closest('form').submit();
				}
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	});
	

});
</script>	
<script type="text/javascript">
	window.addEvent('domready', function() {	
	<?php if ($params->get('show_autosuggest', 1)): ?>
			<?php JHtml::script('com_finder/autocompleter.js', false, true); ?>
			var url = '<?php echo JRoute::_('index.php?option=com_finder&task=suggestions.display&format=json&tmpl=component', false); ?>';
			var ModCompleter = new Autocompleter.Request.JSON(document.id('mod-bt-smartsearchword'), url, {'postVar': 'q'});
	<?php endif; ?>
	});
</script>
<form id="mod-bt-smartsearchform-<?php echo $module->id?>" action="<?php echo JRoute::_($route); ?>" method="get">
	<div class="smartsearch <?php echo $moduleclass_sfx; ?>">
		<div class="keyword">
			<?php		
				echo $output;
				if($show_button ==1){
					echo $button ;
				}		
		?>
		</div>
		<?php if ($show_advanced) : ?>
			<?php if ($show_advanced  == 2): ?>
				<a href="<?php echo JRoute::_($route); ?>"><?php echo JText::_('MOD_BT_SMART_ADVANCED_SEARCH'); ?></a>
			<?php elseif ($show_advanced  == 1): ?>											
					<div id="mod-smartsearch-advanced" class="smartsearch-advanced <?php echo $moduleclass_sfx ;?> ">
						<?php 						
							echo ModBtSmartsearchHelper::Selectbox($query->filters);						
						?>														
					</div>
			<?php endif; ?>
		<?php endif; ?>
		<?php echo ModBtSmartsearchHelper::getGetFields($route); ?>		
	</div>
</form>
