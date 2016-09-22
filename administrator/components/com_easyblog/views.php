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
defined('_JEXEC') or die();

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'views.php' );

class EasyBlogAdminView extends EasyBlogViewParent
{
	public function __construct()
	{
		if( EasyBlogHelper::getJoomlaVersion() >= '3.0' )
		{
			JHtml::_('bootstrap.tooltip');
			JHtml::_('behavior.multiselect');
			JHtml::_('formbehavior.chosen', 'select');
			JHtml::_('dropdown.init');	
		}

		parent::__construct();
	}

	/**
	 * Determines if needed to load the bootstrap or joomla version
	 * of the theme file.
	 *
	 * @since	3.7
	 * @access	public
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getTheme()
	{
		$version 	= EasyBlogHelper::getJoomlaVersion();

		if( $version >= '3.0' )
		{
		JHtmlSidebar::addEntry(
			JText::_('COM_TEMPLATES_SUBMENU_STYLES'),
			'index.php?option=com_templates&view=styles',
			true
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_TEMPLATES_SUBMENU_TEMPLATES'),
			'index.php?option=com_templates&view=templates',
			false
		);
			if( method_exists( $this , 'addSidebar' ) )
			{
				$this->addSidebar();
			}

			return 'bootstrap';
		}

		return 'joomla';
	}

	function renderCheckbox( $configName , $state , $id = '' )
	{
		ob_start();

		if( EasyBlogHelper::getJoomlaVersion() >= '3.0' )
		{
			$id 	= !empty( $id ) ? $id : $configName;
		?>
			<fieldset class="radio btn-group" id="<?php echo $id;?>">
				<input type="radio" value="0" name="<?php echo $configName;?>" id="<?php echo $id;?>0" <?php echo $state == 0 ? ' checked="checked"' : '';?>/>
				<label for="<?php echo $id;?>0" class="option-disable<?php echo $state == 1 ? ' selected' : '';?>"><?php echo JText::_( 'COM_EASYBLOG_NO_OPTION' ); ?></label>

				<input type="radio" value="1" name="<?php echo $configName;?>" id="<?php echo $id;?>1" <?php echo $state == 1 ? ' checked="checked"' : '';?>/>
				<label for="<?php echo $id;?>1" class="option-enable<?php echo $state == 0 ? ' selected' : '';?>"><?php echo JText::_( 'COM_EASYBLOG_YES_OPTION' );?></label>
				
			</fieldset>
		<?php
		}
		else
		{
		?>
			<label class="option-enable<?php echo $state == 1 ? ' selected' : '';?>"><span><?php echo JText::_( 'COM_EASYBLOG_YES_OPTION' );?></span></label>
			<label class="option-disable<?php echo $state == 0 ? ' selected' : '';?>"><span><?php echo JText::_( 'COM_EASYBLOG_NO_OPTION' ); ?></span></label>
			<input name="<?php echo $configName; ?>" value="<?php echo $state;?>" type="radio" id="<?php echo $configName; ?>" class="radiobox" checked="checked" />
			<div style="clear:both;"></div>
		<?php
		}
		
		$html	= ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function renderFilters( $options = array() , $value , $element )
	{
		ob_start();

		if( EasyBlogHelper::getJoomlaVersion() < '3.0' )
		{
		?>

		<script type="text/javascript">
		EasyBlog.ready(function($){
			$(".eblog-filter").click(function(){

				$('#' + $(this).data('element'))
					.val($(this).data('key'));
				submitform();
			});
		});
		</script>

		<?php foreach( $options as $key => $val ) { ?>
		<a class="eblog-filter<?php echo $value == $key ? ' eblog-filter-active' : '';?>" href="javascript:void(0);" data-element="<?php echo $element;?>" data-key="<?php echo $key;?>"><?php echo JText::_( $val ); ?></a>
		<?php } ?>
		<input type="hidden" name="filter_type" id="filter_type" value="<?php echo $value;?>" />
		<?php
		}
		else
		{
		?>
		<select name="<?php echo $element;?>" onchange="this.document.submit();">
			<?php foreach( $options as $key => $val ){ ?>
			<option value="<?php echo $key;?>"><?php echo JText::_( $val ); ?></option>
			<?php } ?>
		</select>
		<?php
		}

		$html	= ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
