<?php
/*------------------------------------------------------------------------
# Copyright (C) 2012-2015 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
--------------------------------------------------------------------------*/

defined('_JEXEC') or die;

$data = $displayData;

?>
<<?php echo $data->tag;?>
	data-id="<?php echo $data->id;?>"
	data-context="<?php echo $data->context;?>"
	data-type="<?php echo $data->type;?>"
	data-itemtype="<?php echo $data->itemtype;?>"
	class="<?php echo 'editable' . ($data->class ? ' '.$data->class : '');?>"
	<?php if($data->style): ?>
	style="<?php echo $data->style;?>;"
	<?php endif;?>
	contenteditable="true"
>
<?php echo $data->text;?>
</<?php echo $data->tag;?>>
