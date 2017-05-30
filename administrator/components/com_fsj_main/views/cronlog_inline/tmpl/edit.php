<?php include JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_main'.DS.'views'.DS.'cronlog_inline'.DS.'snippet'.DS.'_setup.php'; ?>
<div class="fsj fsj_inline">

	<form action="<?php echo JRoute::_('index.php?option=com_fsj_main&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="item-form" id="item-form" class="form-validate">
	
		<div class="modal-header fsj fsj-modal fsj-modal-head">
			<button class="close" data-dismiss="modal">&times;</button>
			<h3><?php echo JHtml::_('string.truncate', JFactory::getApplication()->JComponentTitle, 0, false, false);?></h3>
		</div>

		<div class="modal-body fsj fsj-modal fsj-modal-body">
			<?php include JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_main'.DS.'views'.DS.'cronlog_inline'.DS.'snippet'.DS.'_form.php'; ?>
		</div>
	
		<div class="modal-footer fsj fsj-modal fsj-modal-foot btn-toolbar">
			<?php 
			$bar = JToolBar::getInstance('toolbar');
			echo $bar->render(); 
			?>
		</div>
		
		<?php include JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_main'.DS.'views'.DS.'cronlog_inline'.DS.'snippet'.DS.'_footer.php'; ?>
	</form>

	<?php include JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_main'.DS.'views'.DS.'cronlog_inline'.DS.'snippet'.DS.'_scripts.php'; ?>

	
</div>