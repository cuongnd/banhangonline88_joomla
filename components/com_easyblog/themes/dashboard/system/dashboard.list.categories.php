<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
$document = JFactory::getDocument();
$document->addStylesheet( rtrim(JURI::root(), '/') . '/components/com_easyblog/assets/css/common.css' );
$document->addStylesheet( rtrim(JURI::root(), '/') . '/components/com_easyblog/themes/dashboard/system/css/styles.css' );
?>
<style type="text/css">
body{width:100%!important;}
body,
body .dialog-reset{margin:0!important;padding:0!important;border:0!important;width:auto!important;}
</style>
<script type="text/javascript">
EasyBlog.ready(function($){
	$('#eblog-wrapper').parents('div').addClass('dialog-reset');
});

function resetForm()
{
	document.getElementById('search').value = '';
	submitForm();
}

function submitForm()
{
	document.forms.categories.submit();
}

function addCategory()
{
	var catName = document.getElementById('category_name').value;
	catName = catName.replace(/^\s+/,"").replace(/\s+$/,"");

	if( catName.length > 0 )
	{
	    parent.addCategory( catName );
	}
}

</script>
<div id="eblog-wrapper">
<div id="ezblog-dashboard">
	<form name="categories" id="categories" method="post" action="<?php echo htmlspecialchars(JRequest::getURI()); ?>" >
    <div class="form-head pbl mbl clearfix" style="border-bottom:1px solid #ddd">
    	<div class="float-l">
    		<input type="text" name="search" id="search" value="<?php echo $search; ?>" class="input text float-l" style="width:200px" />
    		<input type="submit" value="<?php echo JText::_( 'COM_EASYBLOG_SEARCH_BUTTON' );?>" class="buttons" />
    		<input type="button" name="Reset" value="<?php echo JText::_( 'COM_EASYBLOG_RESET_BUTTON' );?>" onclick="resetForm();" class="buttons" />
    	</div>

    	<?php if( $this->acl->rules->create_category ){ ?>
    	<div class="float-r">
    	    <span class="float-l mrm" style="line-height:30px"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_NAME'); ?></span>
    		<span class="float-l mrs"><input type="text" name="category_name" id="category_name" value="" class="input text" /></span>
    		<button class="buttons float-l" onclick="addCategory();return false;" id="save_category_button" type="button"><?php echo JText::_( 'COM_EASYBLOG_CREATE' );?></button>
    		<span class="ui-inmsg mlm"></span>
    	</div>
    	<?php } ?>
	</div>

    <div class="form-body mbl" style="margin-right:1px">
    	<table id="blogger-container" width="100%" border="0" cellpadding="0" cellspacing="0" class="reset-table adminlist">
    		<thead>
    		<tr>
    			<th width="3%"><?php echo JText::_('ID'); ?></th>
                <th width="4%">&nbsp;</th>
    			<th><?php echo JText::_('NAME'); ?></th>
    		</tr>
    		</thead>
    	<?php
    	if(!empty($categories))
    	{
    	?>
    		<tbody>
    		<?php
    		$count = 0;
    		foreach($categories as $category)
    		{
    		?>
    		<tr class="row<?php echo $count % 2; ?>">
    			<td align="center"><?php echo $category->id; ?></td>
                <td align="center"><img src="<?php echo $category->avatar; ?>" width="32" height="32" /></td>
    			<td>
    			    <?php echo str_repeat( '|&mdash;' , $category->depth ); ?>
    				<a href="javascript:void(0);" onclick="parent.changeCategory('<?php echo $category->id; ?>', '<?php echo $category->title; ?>');">
    					<?php echo $category->title; ?>
    				</a>
    			</td>
    		</tr>
    		<?php
    		$count++;
    		}
    		?>
    		</tbody>
    	<?php
    	}
    	else
    	{
    	?>
    		<tbody>
    		<tr>
    			<td colspan="5"><?php echo JText::_('COM_EASYBLOG_NO_BLOGGERS_AVAILABLE'); ?></td>
    		</tr>
    		</tbody>
    	<?php
    	}
    	?>
    	</table>
    </div>

	<input type="hidden" name="layout" value="listCategories" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="browse" value="1" />
	<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $order; ?>" />
	<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="asc" />
	<input type="hidden" name="filter_state" id="filter_state" value="<?php echo $filter_state; ?>" />
	</form>

	<?php if ( !empty($pagination) ) : ?>
        <div class="pagination clearfix"><?php echo $pagination->getPagesLinks(); ?></div>
    <?php endif; ?>
</div>
</div>
