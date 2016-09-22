<?php
/**
* @package 		EasyBlog
* @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<script type="text/javascript">
EasyBlog.ready(function($){

	window.checkReportMessage	= function(){

		var value = $( 'textarea[name=reason]' ).val();

		if( value == '' )
		{
			// $( '.report-error' ).html( '<?php echo JText::_( 'COM_EASYBLOG_REPORT_PLEASE_SPECIFY_REASON' , true ); ?>' ).show();

			// return false;
		}

		// Hide report errors.
		$( '.report-error' ).hide();

		$( '#reportForm' ).submit();
	}
});
</script>
<form name="reportForm" id="reportForm" action="<?php echo JRoute::_( 'index.php' );?>" method="post">
<p>
	<?php echo JText::_( 'COM_EASYBLOG_REPORTS_INFO' );?>
</p>
<p><?php echo JText::_('COM_EASYBLOG_REPORTS_SPECIFY_REASON'); ?>:</p>
<div class="mtm mbm" style="padding-right:12px">
	
	<textarea class="inputbox textarea" name="reason" id="reason" style="width:100%"></textarea>

	<div class="report-error" style="color:#ff0000;display:none;"></div>

</div>

<div class="dialog-actions">
	<input type="button" value="<?php echo JText::_('COM_EASYBLOG_CANCEL');?>" class="button" id="edialog-cancel" name="edialog-cancel" onclick="ejax.closedlg();" />
	<input type="button" value="<?php echo JText::_('COM_EASYBLOG_REPORT');?>" class="button" onclick="checkReportMessage();" />
	<input type="hidden" name="obj_id" value="<?php echo $id; ?>" />
	<input type="hidden" name="obj_type" value="<?php echo $type; ?>" />
	<input type="hidden" name="option" value="com_easyblog" />
	<input type="hidden" name="controller" value="reports" />
	<input type="hidden" name="task" value="submitreport" />
	<?php echo JHTML::_( 'form.token' ); ?>
	<span id="eblog_loader"></span>
</div>
</form>
