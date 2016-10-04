<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.formfield');
class JFormFieldTabs extends JFormField {

    protected $type = 'tabs';

    protected function getLabel() {
        return '';
    }
	protected function getInput() {
	if (JRequest::get('post') && JRequest::getString('action')) {
           $obLevel = ob_get_level();
			while ($obLevel > 0 ) {
				ob_end_clean();
				$obLevel --;
            }
            echo self::doPost();
            exit;
        }
       		
	?>		
	 <script type="text/javascript">		
		jQuery(document).ready(function(){
			jQuery('#btnEnable').click(function(){			
				loadEnable();				
				return false;
			});
		});
		function loadEnable(){					
			jQuery.ajax({
				url: location.href,
				data: { "get" : "btnEnable", "action" : "enable" },
                type: "post",
				beforeSend: function(){
					jQuery('#btnEnable span').animate({opacity: 0},100, function(){
						jQuery(this).text('<?php echo JText::_('MOD_BT_SMARTSEARCH_ENABLE_PLUGIN_ENABLING')?>').animate({opacity: 1},100)
					});
					
				},
				
				success: function (response) {		
					jQuery('#btnEnable span').animate({opacity: 0},100, function(){
						jQuery(this).text('<?php echo JText::_('MOD_BT_SMARTSEARCH_ENABLE_PLUGIN_ENABLE_SUCCESS')?>').animate({opacity: 1},100)
						.delay(1000)
						.animate({opacity: 0},100, function(){
							jQuery(this).text('<?php echo JText::_('MOD_BT_SMARTSEARCH_ENABLE_PLUGIN')?>').animate({opacity: 1},100)
						})
					});			
				}
			});				
		
		}		
		
	</script>

<div id="modal-archive" class="modal hide fade">
	<div class="modal-header">
		<button class="close" data-dismiss="modal" type="button">x</button>
		<h3>Smart Search Indexer</h3>
	</div>
	<div id="modal-archive-container"> </div>
</div>
<script>
	jQuery('#modal-archive').on('show', function () {
		document.getElementById('modal-archive-container').innerHTML = '<div class="modal-body"><iframe class="iframe" src="<?php echo JURI::root();?>administrator/index.php?option=com_finder&view=indexer&tmpl=component" height="210" width="500"></iframe></div>';
	});
	jQuery('#modal-archive').on('hide', function () {
	window.parent.location.reload();
	});
	jQuery(document).ready(function(){
		jQuery('.hasTip').tooltip();
	});
</script>		
		<?php
		$isJ30 = version_compare(JVERSION,'3.0.0','ge');
		$link = JURI::root() ;       			             
        $html = '<ul id="btt-form">';               
        $html .= '<li>';        
        $html .= '<button class="hasTip btn-success-enable " id="btnEnable" title="' . JTEXT::_('MOD_BT_SMARTSEARCH_BTN_ENABLE_TOOLTIP') . '" ><span>' . JText::_('MOD_BT_SMARTSEARCH_ENABLE_PLUGIN') . '<span></button>';
		
		if($isJ30) {		
			$html .= '<a  href="#" class="modal"> <button  class="hasTip btn-primary" id="btnIndexing"  data-target="#modal-archive" data-toggle="modal" title="' . JTEXT::_('MOD_BT_SMARTSEARCH_BTN_INDEXING_TOOLTIP') . '" >' . JText::_('MOD_BT_SMARTSEARCH_INDEXING_DATA') . '</button></a>';
		}else{
			$html .= '<a rel="{handler: \'iframe\', size: {x: 500, y: 210}, onClose: function() {}}" href="'.$link.'administrator/index.php?option=com_finder&view=indexer&tmpl=component" class="modal"> <button class="hasTip btn-primary " id="btnIndexing" title="' . JTEXT::_('MOD_BT_SMARTSEARCH_BTN_INDEXING_TOOLTIP') . '">' . JText::_('MOD_BT_SMARTSEARCH_INDEXING_DATA') . '</button></a>';
			$html .= '<div class="btfilter-head J25"> </div>';  
		}                     
		$html .= '</li>';
        $html .= '</ul>';        
              
        return $html;		
		
    }
	private function doPost() {	
		$db= JFactory::getDBO();
		$query = "Update   #__extensions set enabled = '1' where folder ='finder' AND type ='plugin'";
		$db->setQuery($query);		
		if($db->query()){	
			echo 'Successful '; 
		}
		else{
			echo 'UnSuccessful';
		}       
	
	}
}
?>