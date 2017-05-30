<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package   MaQma_Helpdesk
 * @copyright (C) 2006-2012 Components Lab, Lda.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class HTML_config
{
	static function configForm($lists)
	{
		$supportConfig = HelpdeskUtility::GetConfig();
		$task = JRequest::getCmd('task'); ?>

		<form action="index.php" method="post" id="adminForm" name="adminForm" class="label-inline">
		<?php echo JHtml::_('form.token'); ?>
		<input type="hidden" name="task" value="<?php echo $task;?>"/>
		<input type="hidden" name="option" value="com_maqmahelpdesk"/>
		<input type="hidden" name="id" value="1"/>
		<input type="hidden" name="function" value="config"/>
		<input type="hidden" name="users_registration" value="0"/>
		<input type="hidden" name="users_login" value="0"/>

		<div class="breadcrumbs">
			<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
			<span><?php echo JText::_('configuration'); ?></span>
		</div>
		<div class="tabbable tabs-left contentarea">
		<ul class="nav nav-tabs equalheight">
			<li class="active">
				<a href="#generaltab"
				   data-toggle="tab">
					<img src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/config.png"
					     border="0"
					     align="absmiddle"/>&nbsp; <?php echo JText::_('general');?>
				</a>
			</li>
			<li><a href="#integrationstab" data-toggle="tab"><img
						src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/addons.png"
						border="0" align="absmiddle"/>&nbsp; <?php echo JText::_('integrations');?></a></li>
			<li><a href="#userstab" data-toggle="tab"><img
						src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/users.png"
						border="0" align="absmiddle"/>&nbsp; <?php echo JText::_('users');?></a></li>
			<li><a href="#departmentstab" data-toggle="tab"><img
						src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/workgroups.png"
						border="0" align="absmiddle"/>&nbsp; <?php echo JText::_('workgroups');?></a></li>
			<li><a href="#ticketstab" data-toggle="tab"><img
						src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/tickets.png"
						border="0" align="absmiddle"/>&nbsp; <?php echo JText::_('tickets');?></a></li>
			<li><a href="#attachmentstab" data-toggle="tab"><img
						src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/attach.png"
						border="0" align="absmiddle"/>&nbsp; <?php echo JText::_('attachments');?></a></li>
			<li><a href="#kbtab" data-toggle="tab"><img
						src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/kb.png" border="0"
						align="absmiddle"/>&nbsp; <?php echo JText::_('sefext_kb_list') . '/' . JText::_('faq_tab');?></a></li>
			<li><a href="#bugtrackertab" data-toggle="tab"><img
						src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/bug.png" border="0"
						align="absmiddle"/>&nbsp; <?php echo JText::_('bugtracker');?></a></li>
			<li><a href="#discussionstab" data-toggle="tab"><img
						src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/discussions.png" border="0"
						align="absmiddle"/>&nbsp; <?php echo JText::_('discussions');?></a></li>
			<li><a href="#downloadstab" data-toggle="tab"><img
						src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/files.png" border="0"
						align="absmiddle"/>&nbsp; <?php echo JText::_('downloads');?></a></li>
			<li><a href="#smstab" data-toggle="tab"><img
						src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/email.png" border="0"
						align="absmiddle"/>&nbsp; <?php echo JText::_('SMS');?></a></li>
			<li><a href="#linkscpanel" data-toggle="tab"><img
						src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/table.png"
						border="0" align="absmiddle"/>&nbsp; <?php echo JText::_('links');?></a></li>
		</ul>
		<div class="tab-content contentbar withleft">
		<div id="generaltab" class="tab-pane equalheight active">
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('an_access')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('an_access_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('an_access'); ?>
		                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['unregister']; ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('start_day')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('start_day_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('start_day'); ?>
		                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['week_start']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('html_editor')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('html_editor_tooltip')); ?>">
                            <span class="label">
	                            <?php echo JText::_('html_editor'); ?>
                            </span>
					</div>
					<div class="span7">
						<?php echo $lists['editor']; ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('short_date')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('short_date_tooltip')); ?>">
	                        <span class="label">
		                        <?php echo JText::_('short_date'); ?>
		                        <br/>
								<a href="http://us2.php.net/manual/en/function.strftime.php"
								   target="_blank">
									<?php echo JText::_('syntax');?>
								</a>
	                        </span>
					</div>
					<div class="span7">
						<input type="text"
						       class="medium"
						       id="date_short"
						       name="date_short"
						       value="<?php echo $supportConfig->date_short;?>"
						       size="20"
						       maxlength="150" />
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('long_date')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('long_date_tooltip')); ?>">
	                        <span class="label">
		                        <?php echo JText::_('long_date'); ?>
		                        <br/>
								<a href="http://us2.php.net/manual/en/function.strftime.php"
								   target="_blank">
									<?php echo JText::_('syntax');?>
								</a>
	                        </span>
					</div>
					<div class="span7">
						<input type="text"
						       class="medium"
						       id="date_long"
						       name="date_long"
						       value="<?php echo $supportConfig->date_long;?>"
						       size="20"
						       maxlength="150" />
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('dateonly_format')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('dateonly_format_tooltip')); ?>">
	                        <span class="label">
		                        <?php echo JText::_('dateonly_format'); ?>
		                        <br/>
								<a href="http://us2.php.net/manual/en/function.strftime.php"
								   target="_blank">
									<?php echo JText::_('syntax');?>
								</a>
	                        </span>
					</div>
					<div class="span7">
						<input type="text"
						       class="medium"
						       id="dateonly_format"
						       name="dateonly_format"
						       value="<?php echo $supportConfig->dateonly_format;?>"
						       size="20"
						       maxlength="150" />
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('DATE_COUNTRY_CODE')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('DATE_COUNTRY_CODE_TIP')); ?>">
	                        <span class="label">
		                        <?php echo JText::_('DATE_COUNTRY_CODE'); ?>
	                        </span>
					</div>
					<div class="span7">
						<input type="text"
						       class="medium"
						       id="date_country_code"
						       name="date_country_code"
						       value="<?php echo $supportConfig->date_country_code;?>"
						       size="20"
						       maxlength="25" />
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('timeoffset')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('timeoffset_desc')); ?>">
	                        <span class="label">
		                        <?php echo JText::_('timeoffset'); ?>
		                        <br />
		                        <i class="ico-time"></i>
		                        <?php echo HelpdeskDate::DateOffset($supportConfig->date_long);?>
	                        </span>
					</div>
					<div class="span7">
						<?php echo $lists['offset']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('hide_powered')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('hide_powered_tooltip')); ?>">
	                        <span class="label">
		                        <?php echo JText::_('hide_powered'); ?>
	                        </span>
					</div>
					<div class="span7">
						<?php echo $lists['hide_powered']; ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('currency')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('currency_tooltip')); ?>">
	                        <span class="label">
		                        <?php echo JText::_('currency'); ?>
	                        </span>
					</div>
					<div class="span7">
						<input type="text"
						       class="medium"
						       id="currency"
						       name="currency"
						       value="<?php echo $supportConfig->currency;?>"
						       size="20"
						       maxlength="50" />
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('theme_icon')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('theme_icon_tooltip')); ?>">
	                        <span class="label">
		                        <?php echo JText::_('theme_icon'); ?>
	                        </span>
					</div>
					<div class="span7">
						<?php echo $lists['theme_icon']; ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('system_log')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('system_log_tooltip')); ?>">
	                        <span class="label">
		                        <?php echo JText::_('system_log'); ?>
	                        </span>
					</div>
					<div class="span7">
						<?php echo $lists['system_log']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('INCLUDE_JQUERY')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('INCLUDE_JQUERY_TOOLTIP')); ?>">
	                        <span class="label">
		                        <?php echo JText::_('INCLUDE_JQUERY'); ?>
	                        </span>
					</div>
					<div class="span7">
						<?php echo $lists['jquery_source']; ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('INCLUDE_BOOTSTRAP')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('INCLUDE_BOOTSTRAP_TOOLTIP')); ?>">
	                        <span class="label">
		                        <?php echo JText::_('INCLUDE_BOOTSTRAP'); ?>
	                        </span>
					</div>
					<div class="span7">
						<?php echo $lists['include_bootstrap']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('google_adwords')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('google_adwords_desc')); ?>">
	                        <span class="label">
		                        <?php echo JText::_('google_adwords'); ?>
	                        </span>
					</div>
					<div class="span7">
						<input type="text"
						       class="medium"
						       id="google_adwords"
						       name="google_adwords"
						       value="<?php echo $supportConfig->google_adwords;?>"
						       size="20"
						       maxlength="50" />
					</div>
				</div>
			</div>
		</div>
		</div>
		<div id="integrationstab" class="tab-pane equalheight">
		<div class="row-fluid">
			<div class="span12">
				<div class="row-fluid">
					<div class="span12">
						<h4 style="border-bottom:1px solid #ccc;padding:5px;margin-bottom:0;margin-top:0;">
							<img src="../media/com_maqmahelpdesk/images/integrations/jomsocial.png"
							     align="left"
							     alt=""
							     style="padding-right:5px;" />
							JomSocial
						</h4>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('post_comments_in_wall')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('post_comments_in_wall_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('post_comments_in_wall'); ?>
		                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['post_comments_in_wall']; ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('use_jomsocial_avatars')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('use_jomsocial_avatars_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('use_jomsocial_avatars'); ?>
		                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['use_jomsocial_avatars']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('post_kb_creation_in_wall')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('post_kb_creation_in_wall_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('post_kb_creation_in_wall'); ?>
		                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['post_kb_creation_in_wall']; ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('js_post_question_wall')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('js_post_question_wall_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('js_post_question_wall'); ?>
		                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['js_post_question_wall']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('js_post_answer_wall')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('js_post_answer_wall_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('js_post_answer_wall'); ?>
		                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['js_post_answer_wall']; ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('js_post_votes_wall')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('js_post_votes_wall_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('js_post_votes_wall'); ?>
		                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['js_post_votes_wall']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('js_answer_selected_wall')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('js_answer_selected_wall_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('js_answer_selected_wall'); ?>
		                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['js_answer_selected_wall']; ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('js_post_bugtracker_wall')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('js_post_bugtracker_wall_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('js_post_bugtracker_wall'); ?>
		                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['js_post_bugtracker_wall']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<div class="row-fluid">
					<div class="span12">
						<h4 style="border-bottom:1px solid #ccc;padding:5px;margin-bottom:0;margin-top:0;">
							<img src="../media/com_maqmahelpdesk/images/integrations/digistore.png"
							     align="left"
							     alt=""
							     style="padding-right:5px;" />
							iJoomla DigiStore
						</h4>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('integrate_digistore')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('integrate_digistore_desc')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('integrate_digistore'); ?>
		                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['integrate_digistore']; ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('DG_REQUIRES_DOMAINS')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('DG_REQUIRES_DOMAINS_TOOLTIP')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('DG_REQUIRES_DOMAINS'); ?>
		                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['digistore_domains']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<div class="row-fluid">
					<div class="span12">
						<h4 style="border-bottom:1px solid #ccc;padding:5px;margin-bottom:0;margin-top:0;">
							<img src="../media/com_maqmahelpdesk/images/integrations/sobipro.png"
							     align="left"
							     alt=""
							     style="padding-right:5px;" />
							Sobi Pro
						</h4>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('integrate_sobi')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('integrate_sobi_desc')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('integrate_sobi'); ?>
		                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['integrate_sobi']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<div class="row-fluid">
					<div class="span12">
						<h4 style="border-bottom:1px solid #ccc;padding:5px;margin-bottom:0;margin-top:0;">
							<img src="../media/com_maqmahelpdesk/images/integrations/mosetstree.png"
							     align="left"
							     alt=""
							     style="padding-right:5px;" />
							Mosets Tree
						</h4>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('integrate_mtree')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('integrate_mtree_desc')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('integrate_mtree'); ?>
		                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['integrate_mtree']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<div class="row-fluid">
					<div class="span12">
						<h4 style="border-bottom:1px solid #ccc;padding:5px;margin-bottom:0;margin-top:0;">
							<img src="../media/com_maqmahelpdesk/images/integrations/artofuser.png"
							     align="left"
							     alt=""
							     style="padding-right:5px;" />
							ArtOfUser
						</h4>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('integrate_artofuser')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('integrate_artofuser_desc')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('integrate_artofuser'); ?>
		                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['integrate_artofuser']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<div class="row-fluid">
					<div class="span12">
						<h4 style="border-bottom:1px solid #ccc;padding:5px;margin-bottom:0;margin-top:0;">
							<img src="../media/com_maqmahelpdesk/images/integrations/jbolo.png"
							     align="left"
							     alt=""
							     style="padding-right:5px;" />
							JBolo
						</h4>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('integrate_jbolo')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('integrate_jbolo_desc')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('integrate_jbolo'); ?>
		                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['integrate_jbolo']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<div class="row-fluid">
					<div class="span12">
						<h4 style="border-bottom:1px solid #ccc;padding:5px;margin-bottom:0;margin-top:0;">
							<img src="../media/com_maqmahelpdesk/images/integrations/cb.png"
							     align="left"
							     alt=""
							     style="padding-right:5px;" />
							Community Builder
						</h4>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('use_cb_avatars')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('use_cb_avatars_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('use_cb_avatars'); ?>
		                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['use_cb_avatars']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<div class="row-fluid">
					<div class="span12">
						<h4 style="border-bottom:1px solid #ccc;padding:5px;margin-bottom:0;margin-top:0;">
							<img src="../media/com_maqmahelpdesk/images/integrations/bbb.png"
							     align="left"
							     alt=""
							     style="padding-right:5px;" />
							Big Blue Button
						</h4>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('url')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('url')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('url'); ?>
		                    </span>
					</div>
					<div class="span7">
						<input type="text"
						       id="bbb_url"
						       name="bbb_url"
						       valign="top"
						       value="<?php echo $supportConfig->bbb_url; ?>" />
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('api_key')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('api_key')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('api_key'); ?>
		                    </span>
					</div>
					<div class="span7">
						<input type="text"
						       id="bbb_apikey"
						       name="bbb_apikey"
						       valign="top"
						       value="<?php echo $supportConfig->bbb_apikey; ?>" />
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<div class="row-fluid">
					<div class="span12">
						<h4 style="border-bottom:1px solid #ccc;padding:5px;margin-bottom:0;margin-top:0;">
							<img src="../media/com_maqmahelpdesk/images/integrations/awdwall.png"
							     align="left"
							     alt=""
							     style="padding-right:5px;" />
							jomWall
						</h4>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('use_jomwall_avatars')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('use_jomwall_avatars_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('use_jomwall_avatars'); ?>
		                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['use_jomwall_avatars']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<div class="row-fluid">
					<div class="span12">
						<h4 style="border-bottom:1px solid #ccc;padding:5px;margin-bottom:0;margin-top:0;">
							<img src="../media/com_maqmahelpdesk/images/integrations/eshop.png"
							     align="left"
							     alt=""
							     style="padding-right:5px;" />
							eShop Suite
						</h4>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('USE_ESHOP_AVATARS')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('USE_ESHOP_AVATARS_TOOLTIP')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('USE_ESHOP_AVATARS'); ?>
		                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['use_eshop_suite_avatars']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<div class="row-fluid">
					<div class="span12">
						<h4 style="border-bottom:1px solid #ccc;padding:5px;margin-bottom:0;margin-top:0;">
							<img src="../media/com_maqmahelpdesk/images/integrations/screenr.png"
							     align="left"
							     alt=""
							     style="padding-right:5px;" />
							Screenr Business
	                            <span style="font-size:12px;font-weight:normal;">
		                            (<a href="http://business.screenr.com" target="_blank">
			                            http://business.screenr.com
		                            </a>)
		                        </span>
						</h4>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('screenr_account')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('screenr_account_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('screenr_account'); ?>
		                    </span>
					</div>
					<div class="span7">
						<input type="text"
						       id="screenr_account"
						       name="screenr_account"
						       valign="top"
						       value="<?php echo $supportConfig->screenr_account; ?>" />
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('screenr_api')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('screenr_api_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('screenr_api'); ?>
		                    </span>
					</div>
					<div class="span7">
						<input type="text"
						       id="screenr_api_id"
						       name="screenr_api_id"
						       valign="top"
						       value="<?php echo $supportConfig->screenr_api_id; ?>" />
					</div>
				</div>
			</div>
		</div>
		</div>
		<div id="userstab" class="tab-pane equalheight">
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('profile_required')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('profile_required_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('profile_required'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['profile_required']; ?>
						</div>
					</div>
				</div>
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('phone_req')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('phone_req_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('phone_req'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['rf_phone']; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('fax_req')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('fax_req_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('fax_req'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['rf_fax']; ?>
						</div>
					</div>
				</div>
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('mobile_req')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('mobile_req_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('mobile_req'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['rf_mobile']; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('address1_req')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('address1_req_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('address1_req'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['rf_address1']; ?>
						</div>
					</div>
				</div>
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('address2_req')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('address2_req_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('address2_req'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['rf_address2']; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('zip_req')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('zip_req_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('zip_req'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['rf_zipcode']; ?>
						</div>
					</div>
				</div>
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('state_req')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('state_req_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('state_req'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['rf_location']; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('city_req')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('city_req_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('city_req'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['rf_city']; ?>
						</div>
					</div>
				</div>
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('country_req')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('country_req_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('country_req'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['rf_country']; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('show_login_form')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('show_login_form_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('show_login_form'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['show_login_form']; ?>
						</div>
					</div>
				</div>
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('show_login_details')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('show_login_details_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('show_login_details'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['show_login_details']; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="departmentstab" class="tab-pane equalheight">
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('support_workgroup_only')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('support_workgroup_only_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('support_workgroup_only'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['support_workgroup_only']; ?>
						</div>
					</div>
				</div>
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('show_dashboard_support_title')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('show_dashboard_support_desc')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('show_dashboard_support_title'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['show_dashboard_support']; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('show_dashboard_customer_title')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('show_dashboard_customer_desc')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('show_dashboard_customer_title'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['show_dashboard_customer']; ?>
						</div>
					</div>
				</div>
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('use_department_groups')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('use_department_groups_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('use_department_groups'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['use_department_groups']; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('DEPARTMENTS_LIST_TEMPLATE')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('DEPARTMENTS_LIST_TEMPLATE_TOOLTIP')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('DEPARTMENTS_LIST_TEMPLATE'); ?>
		                    </span>
						</div>
						<div class="span7">
							<input type="text"
							       id="departments_template"
							       name="departments_template"
							       value="<?php echo $supportConfig->departments_template;?>"
							       size="20"
							       maxlength="50" />
						</div>
					</div>
				</div>
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('TICKETS_PER_DEPARTMENT')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('TICKETS_PER_DEPARTMENT_TIP')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('TICKETS_PER_DEPARTMENT'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['tickets_per_department']; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="ticketstab" class="tab-pane equalheight">
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('use_uncategorized')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('use_uncategorized_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('use_uncategorized'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['use_uncategorized']; ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('anonymous_tickets')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('anonymous_tickets_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('anonymous_tickets'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['anonymous_tickets']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('dfsource')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('dfsource_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('dfsource'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['source']; ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('extra_email_notification')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('extra_email_notification_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('extra_email_notification'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['extra_email_notification']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('autoclose_on')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('autoclose_on_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('autoclose_on'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['ac_active']; ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('notify_admin')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('notify_admin_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('notify_admin'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['receive_mail']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('ticket_age')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('ticket_age_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('ticket_age'); ?>
			                    </span>
					</div>
					<div class="span7">
						<input type="text"
						       id="ac_days"
						       name="ac_days"
						       size="5"
						       maxlength="11"
						       valign="top"
						       value="<?php echo $supportConfig->ac_days; ?>" />
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('support_change_status')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('support_change_status_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('support_change_status'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['sup_usertype']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('cli_change_status')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('cli_change_status_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('cli_change_status'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['client_change_status']; ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('reguser_change_status')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('reguser_change_status_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('reguser_change_status'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['register_user_change_status']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('mins_interval')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('mins_interval_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('mins_interval'); ?>
			                    </span>
					</div>
					<div class="span7">
						<input type="text"
						       id="minutes"
						       name="minutes"
						       size="5"
						       maxlength="11"
						       valign="top"
						       value="<?php echo $supportConfig->minutes; ?>" />
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('low_rate')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('low_rate_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('low_rate'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['notify_rate']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('ticket_rating')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('ticket_rating_desc')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('ticket_rating'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['less_rate']; ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('support_only_show_assign')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('support_only_show_assign_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('support_only_show_assign'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['support_only_show_assign']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('mail_queue')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('mail_queue_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('mail_queue'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['mail_queue']; ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('readmail_user')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('readmail_user_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('readmail_user'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['readmail_create_user']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('stopspam')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('stopspam_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('stopspam'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['stopspam']; ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('common_ticket_views')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('common_ticket_views_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('common_ticket_views'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['common_ticket_views']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('use_merge')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('use_merge_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('use_merge'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['use_merge']; ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('use_as_reply')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('use_as_reply_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('use_as_reply'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['use_as_reply']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('use_parent')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('use_parent_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('use_parent'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['use_parent']; ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('use_travel')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('use_travel_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('use_travel'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['use_travel']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('use_type')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('use_type_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('use_type'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['use_type']; ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('customfields_search')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('customfields_search_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('customfields_search'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['customfields_search']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('tickets_numbers')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('tickets_numbers_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('tickets_numbers'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['tickets_numbers']; ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('manual_times')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('manual_times_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('manual_times'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['manual_times']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('ignore_letters')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('ignore_letters_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('ignore_letters'); ?>
			                    </span>
					</div>
					<div class="span7">
						<textarea id="ticket_ignore_letter"
						          name="ticket_ignore_letter"
						          style="height:50px;"><?php echo $supportConfig->ticket_ignore_letter; ?>
						</textarea>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('AUTOCLOSE_STATUS')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('AUTOCLOSE_STATUS_TOOLTIP')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('AUTOCLOSE_STATUS'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['autoclose_status']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('duedate_algoritm')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('duedate_algoritm_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('duedate_algoritm'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['duedate_algoritm']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('duedate_firstday')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('duedate_firstday_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('duedate_firstday'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['duedate_firstday']; ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('duedate_firstday_minimum')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('duedate_firstday_minimum_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('duedate_firstday_minimum'); ?>
			                    </span>
					</div>
					<div class="span7">
						<input type="text"
						       id="duedate_firstday_minimum"
						       name="duedate_firstday_minimum"
						       size="5"
						       maxlength="11"
						       valign="top"
						       value="<?php echo $supportConfig->duedate_firstday_minimum; ?>" />
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('duedate_holidays')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('duedate_holidays_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('duedate_holidays'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['duedate_holidays']; ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('duedate_vacations')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('duedate_vacations_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('duedate_vacations'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['duedate_vacations']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('duedate_schedule')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('duedate_schedule_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('duedate_schedule'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['duedate_schedule']; ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('duedate_default_schedule')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('duedate_default_schedule_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('duedate_default_schedule'); ?>
			                    </span>
					</div>
					<div class="span7">
						<?php echo $lists['schedules']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('duedate_hoursday')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('duedate_hoursday_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('duedate_hoursday'); ?>
			                    </span>
					</div>
					<div class="span7">
						<input type="text"
						       id="duedate_hoursday"
						       name="duedate_hoursday"
						       size="5"
						       maxlength="11"
						       valign="top"
						       value="<?php echo $supportConfig->duedate_hoursday; ?>" />
					</div>
				</div>
			</div>
		</div>
		</div>
		<div id="attachmentstab" class="tab-pane equalheight">
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('allowattachs')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('allowattachs_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('allowattachs'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['public_attach']; ?>
						</div>
					</div>
				</div>
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('num_attachs')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('num_attachs_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('num_attachs'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['attachs_num']; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('maxallowed')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('maxallowed_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('maxallowed'); ?>
		                    </span>
						</div>
						<div class="span7">
							<input type="text"
							       id="maxAllowed"
							       name="maxAllowed"
							       size="50"
							       maxlength="11"
							       valign="top"
							       value="<?php echo $supportConfig->maxAllowed; ?>" />
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<div class="row-fluid">
						<div class="span2 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('attachs_path')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('attachs_path_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('attachs_path'); ?>
		                    </span>
						</div>
						<div class="span10">
							<input type="text"
							       class="span10"
							       id="docspath"
							       name="docspath"
							       maxlength="150"
							       valign="top"
							       value="<?php echo $supportConfig->docspath; ?>" />
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<div class="row-fluid">
						<div class="span2 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('allowed_types')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('allowed_types_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('allowed_types'); ?>
		                    </span>
						</div>
						<div class="span10">
							<textarea id="extensions"
							          name="extensions"
							          class="span10"
							          style="height:50px;"><?php echo trim($supportConfig->extensions); ?>
							</textarea>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="kbtab" class="tab-pane equalheight">
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('faq_manual')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('faq_manual_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('faq_manual'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['faq_kb_manual']; ?>
						</div>
					</div>
				</div>
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('articles_hits')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('articles_hits_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('articles_hits'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['faq_kb_hits']; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('set_hits')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('set_hits_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('set_hits'); ?>
		                    </span>
						</div>
						<div class="span7">
							<input type="text"
							       id="faq_kb_nhits"
							       name="faq_kb_nhits"
							       size="5"
							       maxlength="11"
							       valign="top"
							       value="<?php echo $supportConfig->faq_kb_nhits; ?>" />
						</div>
					</div>
				</div>
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('kb_approvement')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('kb_approvement_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('kb_approvement'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['kb_approvement']; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('kbsocial')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('kbsocial_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('kbsocial'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['kbsocial']; ?>
						</div>
					</div>
				</div>
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('FAQ_SINGLE_PAGE')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('FAQ_SINGLE_PAGE_TOOLTIP')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('FAQ_SINGLE_PAGE'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['faq_single_page']; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('moderate')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('moderate_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('moderate'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['kb_moderate']; ?>
						</div>
					</div>
				</div>
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('kb_enable_rating')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('kb_enable_rating_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('kb_enable_rating'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['kb_enable_rating']; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('kb_enable_comments')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('kb_enable_comments_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('kb_enable_comments'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['kb_enable_comments']; ?>
						</div>
					</div>
				</div>
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('show_kb_frontpage')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('show_kb_frontpage_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('show_kb_frontpage'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['show_kb_frontpage']; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('KB_NUMBER_CHARS')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('KB_NUMBER_CHARS_TOOLTIP')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('KB_NUMBER_CHARS'); ?>
		                    </span>
						</div>
						<div class="span7">
							<input type="text"
							       id="kb_number_chars"
							       name="kb_number_chars"
							       size="5"
							       maxlength="3"
							       valign="top"
							       value="<?php echo $supportConfig->kb_number_chars; ?>" />
						</div>
					</div>
				</div>
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('KB_NUMBER_COLUMNS')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('KB_NUMBER_COLUMNS_TOOLTIP')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('KB_NUMBER_COLUMNS'); ?>
		                    </span>
						</div>
						<div class="span7">
							<input type="text"
							       id="kb_number_columns"
							       name="kb_number_columns"
							       size="5"
							       maxlength="2"
							       valign="top"
							       value="<?php echo $supportConfig->kb_number_columns; ?>" />
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="bugtrackertab" class="tab-pane equalheight">
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('status')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('bug_default_status_tooltip')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('status'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['bug_status']; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="discussionstab" class="tab-pane equalheight">
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('DISCUSSIONS_ANONYMOUS')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('DISCUSSIONS_ANONYMOUS_TOOLTIP')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('DISCUSSIONS_ANONYMOUS'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['discussions_anonymous']; ?>
						</div>
					</div>
				</div>
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('discussions_moderated')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('discussions_moderated_desc')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('discussions_moderated'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['discussions_moderated']; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="downloadstab" class="tab-pane equalheight">
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('ENABLE_DOWNLOAD_SUBSCRIPTION')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('ENABLE_DOWNLOAD_SUBSCRIPTION_TOOLTIP')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('ENABLE_DOWNLOAD_SUBSCRIPTION'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['download_notification']; ?>
						</div>
					</div>
				</div>
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('DOWNLOADS_BADGES')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('DOWNLOADS_BADGES_TOOLTIP')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('DOWNLOADS_BADGES'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['downloads_badges']; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="smstab" class="tab-pane equalheight">
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('ENABLE_SMS_AGENTS')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('ENABLE_SMS_AGENTS_TOOLTIP')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('ENABLE_SMS_AGENTS'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['sms_assign']; ?>
						</div>
					</div>
				</div>
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('SMS_SYSTEM')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('SMS_SYSTEM_TOOLTIP')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('SMS_SYSTEM'); ?>
		                    </span>
						</div>
						<div class="span7">
							<?php echo $lists['sms_gateway']; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('username')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('username')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('username'); ?>
		                    </span>
						</div>
						<div class="span7">
							<input type="text"
							       id="sms_username"
							       name="sms_username"
							       value="<?php echo $supportConfig->sms_username; ?>" />
						</div>
					</div>
				</div>
				<div class="span6">
					<div class="row-fluid">
						<div class="span4 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('password')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('password')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('password'); ?>
		                    </span>
						</div>
						<div class="span7">
							<input type="text"
							       id="sms_password"
							       name="sms_password"
							       value="<?php echo $supportConfig->sms_password; ?>" />
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="linkscpanel" class="tab-pane equalheight">
		</div>
		<div class="clr"></div>
		</div>
		</div>
		</form><?php
	}
}
