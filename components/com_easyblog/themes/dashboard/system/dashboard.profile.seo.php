<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>
<?php if( $this->acl->rules->add_entry && $this->acl->rules->allow_seo ){ ?>
<div class="ui-modbox" id="widget-profile-seo">
	<div class="ui-modhead">
		<div class="ui-modtitle"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_SEO_SETTINGS_TITLE'); ?></div>
		<a href="javascript:void(0);" onclick="eblog.dashboard.toggle( this );" class="ui-tog pabs atr ir"><?php echo JText::_( 'COM_EASYBLOG_HIDE' );?></a>
	</div>
	<div class="ui-modbody clearfix">
		<div class="eblog-message info">
			<span><?php echo JText::_('COM_EASYBLOG_DASHBOARD_SEO_SETTINGS_DESC'); ?></span>
		</div>
		<ul class="list-form reset-ul">
			<li>
				<label for="metadescription"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_SEO_META_DESCRIPTION'); ?> :</label>
				<div>
					<textarea class="input textarea width-full" cols="30" rows="5" name="metadescription" id="metadescription"><?php echo $meta->description; ?></textarea>
					<div class="mts">
						<input type="text" disabled="disabled"  id="text-counter" width="" size="5" style="text-align:center;padding:0px" class="ui-disable mrs" value="0" >
						<label><?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_SEO_META_DESCRIPTION_INSTRUCTIONS'); ?></label>
					</div>
				</div>
			</li>
			<li>
				<label for="metakeywords"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_SEO_META_KEYWORDS'); ?> :</label>
				<div>
					<textarea class="input textarea width-full" cols="30" rows="3" name="metakeywords" id="metakeywords"><?php echo $meta->keywords; ?></textarea>
					<div class="small"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_SEO_META_KEYWORDS_SEPARATE_WITH_COMMA'); ?></div>
				</div>
			</li>
		</ul>
	</div>
</div>
<?php } ?>
