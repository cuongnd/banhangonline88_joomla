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

		<div class="span12">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ADVANCE_SETTINGS_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key" style="vertical-align: top ! important;">
					<span class="editlinktip">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ROUTING_BEHAVIOR' ); ?>
					</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ROUTING_BEHAVIOR_DESC' ); ?></div>
							<div class="radio-wrap">
								<div>
									<input type="radio" class="inputbox" value="default" id="main_routing0" name="main_routing"<?php echo $this->config->get('main_routing') == 'default' ? ' checked="checked"' : '';?>>
									<label for="main_routing0">
										<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ROUTING_BEHAVIOR_DEAULT');?>
									</label>

									<div >
										<table>
											<tr>
												<td width="25%">
													<label for="normal"><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ROUTING_BEHAVIOR_BLOGGER_STANDALONE');?></label>
												</td>
												<td width="15%">
													<div>
														<input type="text" class="input-mini" name="main_routing_order_bloggerstandalone" id="main_routing_order_bloggerstandalone" value="<?php echo $this->config->get('main_routing_order_bloggerstandalone' );?>" size="2" maxlength="1"/>
													</div>
												</td>
												<td width="25%">
													<div>
														<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ROUTING_BEHAVIOR_IGNORE');?>
													</div>
												</td>
												<td width="30%">
													<div>
														<?php echo $this->renderCheckbox( 'main_routing_order_bloggerstandalone_ignore' , $this->config->get( 'main_routing_order_bloggerstandalone_ignore' ) );?>
													</div>
												</td>
											</tr>

											<tr>
												<td>
													<label for="normal"><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ROUTING_BEHAVIOR_ENTRY');?></label>
												</td>
												<td>
													<div>
														<input type="text" class="input-mini" name="main_routing_order_entry" id="main_routing_order_entry" value="<?php echo $this->config->get('main_routing_order_entry' );?>" size="2" maxlength="1" />
													</div>
												</td>
												<td>
													<div>
														<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ROUTING_BEHAVIOR_IGNORE');?>
													</div>
												</td>
												<td>
													<div>
														<?php echo $this->renderCheckbox( 'main_routing_order_entry_ignore' , $this->config->get( 'main_routing_order_entry_ignore' ) );?>
													</div>
												</td>
											</tr>

											<tr>
												<td>
													<label for="normal"><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ROUTING_BEHAVIOR_CATEGORY');?></label>
												</td>
												<td>
													<div>
														<input type="text" class="input-mini" name="main_routing_order_category" id="main_routing_order_category" value="<?php echo $this->config->get('main_routing_order_category' );?>" size="2" maxlength="1" />
													</div>
												</td>
												<td>
													<div>
														<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ROUTING_BEHAVIOR_IGNORE');?>
													</div>
												</td>
												<td>
													<div>
														<?php echo $this->renderCheckbox( 'main_routing_order_category_ignore' , $this->config->get( 'main_routing_order_category_ignore' ) );?>
													</div>
												</td>
											</tr>

											<tr>
												<td>
													<label for="normal"><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ROUTING_BEHAVIOR_BLOGGER');?></label>
												</td>
												<td>
													<div>
														<input type="text" class="input-mini" name="main_routing_order_blogger" id="main_routing_order_blogger" value="<?php echo $this->config->get('main_routing_order_blogger' );?>" size="2" maxlength="1" />
													</div>
												</td>
												<td>
													<div>
														<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ROUTING_BEHAVIOR_IGNORE');?>
													</div>
												</td>
												<td>
													<div>
														<?php echo $this->renderCheckbox( 'main_routing_order_blogger_ignore' , $this->config->get( 'main_routing_order_blogger_ignore' ) );?>
													</div>
												</td>
											</tr>

											<tr>
												<td>
													<label for="normal"><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ROUTING_BEHAVIOR_TEAMBLOG');?></label>
												</td>
												<td>
													<div>
														<input type="text" class="input-mini" name="main_routing_order_teamblog" id="main_routing_order_teamblog" value="<?php echo $this->config->get('main_routing_order_teamblog' );?>" size="2" maxlength="1" />
													</div>
												</td>
												<td>
													<div>
														<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ROUTING_BEHAVIOR_IGNORE');?>
													</div>
												</td>
												<td>
													<div>
														<?php echo $this->renderCheckbox( 'main_routing_order_teamblog_ignore' , $this->config->get( 'main_routing_order_teamblog_ignore' ) );?>
													</div>
												</td>
											</tr>

										</table>
									</div>
									<div class="notice full-width" style="margin-top: 5px;">
										<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ROUTING_BEHAVIOR_ORDERING_NOTE' ); ?>
									</div>
								</div>
								<div>
									<input type="radio" class="inputbox" value="currentactive" id="main_routing1" name="main_routing"<?php echo $this->config->get('main_routing') == 'currentactive' ? ' checked="checked"' : '';?>>
									<label for="main_routing1">
										<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ROUTING_BEHAVIOR_USE_CURRENT_ACTIVEMENU');?>
									</label>
								</div>
								<div>
									<input type="radio" class="inputbox" value="menuitemid" id="main_routing2" name="main_routing"<?php echo $this->config->get('main_routing') == 'menuitemid' ? ' checked="checked"' : '';?>>
									<label for="main_routing2">
										<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ROUTING_BEHAVIOR_USE_MENUITEM');?>
									</label>
									<label for="main_routing_itemid">
										<input type="text" name="main_routing_itemid" class="inputbox" style="width: 50px;" value="<?php echo $this->config->get('main_routing_itemid' );?>" />
									</label>
								</div>
							</div>
							<div style="clear:both"></div>
							<div class="notice full-width" style="margin-top: 5px">
							    <?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ROUTING_BEHAVIOR_NOTE' ); ?>
							</div>
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
