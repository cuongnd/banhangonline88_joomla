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
?>
<div class="row-fluid">
	<div class="span12">

		<div class="span6">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_RATINGS_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_RATINGS' ); ?>
						</span>
					</td>
					<td class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_RATINGS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_ratings' , $this->config->get( 'main_ratings' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_RATINGS_FRONTPAGE' ); ?>
						</span>
					</td>
					<td class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_RATINGS_FRONTPAGE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_ratings_frontpage' , $this->config->get( 'main_ratings_frontpage' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCKED_ON_FRONTPAGE' ); ?>
						</span>
					</td>
					<td class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCKED_ON_FRONTPAGE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_ratings_frontpage_locked' , $this->config->get( 'main_ratings_frontpage_locked' ) );?>
						</div>
						
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_GUEST_RATING' ); ?>
						</span>
					</td>
					<td class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_GUEST_RATING_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_ratings_guests' , $this->config->get( 'main_ratings_guests' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_DISPLAY_PEOPLE_RATED' ); ?>
						</span>
					</td>
					<td class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_DISPLAY_PEOPLE_RATED_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_ratings_display_raters' , $this->config->get( 'main_ratings_display_raters' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_AUP_FOR_RATINGS' ); ?>
						</span>
					</td>
					<td class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_AUP_FOR_RATINGS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_ratings_aup_rate' , $this->config->get( 'main_ratings_aup_rate' ) );?>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</div>

		<div class="span6">
		</div>

	</div>
</div>
