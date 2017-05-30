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

class MaQmaHtmlEdit
{
	static function display(&$row, $lists)
	{
		global $wkoptions;

		$supportConfig = HelpdeskUtility::GetConfig();
		$editor = JFactory::getEditor();
		$user = JFactory::getUser();

		if ($row->id) {
			$GLOBALS['workgroup_title'] = JText::_('edit_workgroup') . " (" . $row->wkdesc . ")";
		} else {
			$GLOBALS['workgroup_title'] = JText::_('add_workgroup');
		} ?>

    <script type="text/javascript">
        $jMaQma(document).ready(function(){
            $jMaQma('.showPopover').popover({'html':true, 'trigger':'hover'});
        });
    </script>

    <form action="index.php" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data" class="label-inline">
		<?php echo JHtml::_('form.token'); ?>
    <div class="breadcrumbs">
        <a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
        <a href="index.php?option=com_maqmahelpdesk&task=workgroup"><?php echo JText::_('workgroups'); ?></a>
        <span><?php echo JText::_('edit'); ?></span>
    </div>
    <div class="tabbable tabs-left contentarea">
    <ul class="nav nav-tabs equalheight">
        <li class="active"><a href="#tab1" data-toggle="tab"><img
                src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/workgroups.png"
                border="0" align="absmiddle"/>&nbsp; <?php echo JText::_('general');?></a></li>
        <li><a href="#tab5" data-toggle="tab"><img
                src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/addons.png"
                border="0" align="absmiddle"/>&nbsp; <?php echo JText::_('applications');?></a></li>
	    <li><a href="#tab6" data-toggle="tab"><img
				    src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/access.png"
				    border="0"
				    align="absmiddle"/>&nbsp; <?php echo JText::_('ACCESS');?>
		    </a></li>
        <li><a href="#tab2" data-toggle="tab"><img
                src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/send.png"
                border="0" align="absmiddle"/>&nbsp; <?php echo JText::_('wk_notifications');?></a></li>
        <li><a href="#tab3" data-toggle="tab"><img
                src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/support_staff.png"
                border="0" align="absmiddle"/>&nbsp; <?php echo JText::_('assignments');?></a></li>
        <li><a href="#tab4" data-toggle="tab"><img
                src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/table.png"
                border="0" align="absmiddle"/>&nbsp; <?php echo JText::_('links');?></a></li>
    </ul>
    <div class="tab-content contentbar withleft pad5">
    <div id="tab1" class="tab-pane active equalheight">
    <div class="row-fluid">
        <div class="span12">
            <div class="row-fluid">
                <div class="span2 showPopover"
                     data-original-title="<?php echo htmlspecialchars(JText::_('wk_name')); ?>"
                     data-content="<?php echo htmlspecialchars(JText::_('wk_name_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('wk_name'); ?>
			                    </span>
                </div>
                <div class="span10">
                    <input type="text"
                           id="wkdesc"
                           name="wkdesc"
                           class="span10"
                           value="<?php echo str_replace("\'", "'", $row->wkdesc); ?>"
                           maxlength="100"
                           onblur="CreateSlug('wkdesc');" />
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <div class="row-fluid">
                <div class="span2 showPopover"
                     data-original-title="<?php echo htmlspecialchars(JText::_('slug')); ?>"
                     data-content="<?php echo htmlspecialchars(JText::_('slug_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('slug'); ?>
			                    </span>
                </div>
                <div class="span10">
                    <input type="text"
                           id="slug"
                           name="slug"
                           class="span10"
                           value="<?php echo $row->slug; ?>"
                           maxlength="100" />
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <div class="row-fluid">
                <div class="span2 showPopover"
                     data-original-title="<?php echo htmlspecialchars(JText::_('wk_desc')); ?>"
                     data-content="<?php echo htmlspecialchars(JText::_('wk_desc_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('wk_desc'); ?>
			                    </span>
                </div>
                <div class="span10">
					<?php echo $editor->display('wkabout', $row->wkabout, '100%', '300', '75', '20');?>
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <div class="row-fluid">
                <div class="span2 showPopover"
                     data-original-title="<?php echo htmlspecialchars(JText::_('short_desc')); ?>"
                     data-content="<?php echo htmlspecialchars(JText::_('short_desc_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('short_desc'); ?>
			                    </span>
                </div>
                <div class="span10">
                    <textarea id="shortdesc"
                              name="shortdesc"
                              class="span10"
                              style="height:50px;"><?php echo $row->shortdesc; ?></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <div class="row-fluid">
                <div class="span2 showPopover"
                     data-original-title="<?php echo htmlspecialchars(JText::_('wk_logo')); ?>"
                     data-content="<?php echo htmlspecialchars(JText::_('wk_logo_tooltip')); ?>">
	                    <span class="label">
		                    <?php echo JText::_('wk_logo'); ?>
	                    </span>
                </div>
                <div class="span10">
					<?php if (!$row->logo): ?>
                    <input type="file"
                           id="logo"
                           name="logo"
                           maxlength="100" />
					<?php else: ?>
					<?php echo $lists['logo_remove']; ?><br />
                    <img hspace="10"
                         vspace="10"
                         width="48"
                         height="48"
                         src="<?php echo JURI::root();?>media/com_maqmahelpdesk/images/logos/<?php echo $row->logo; ?>"/>
					<?php endif;?>
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span6">
            <div class="row-fluid">
                <div class="span4 showPopover"
                     data-original-title="<?php echo htmlspecialchars(JText::_('wk_publish')); ?>"
                     data-content="<?php echo htmlspecialchars(JText::_('wk_publish_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('wk_publish'); ?>
			                    </span>
                </div>
                <div class="span8">
					<?php echo $lists['show']; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span6">
            <div class="row-fluid">
                <div class="span4 showPopover"
                     data-original-title="<?php echo htmlspecialchars(JText::_('wk_theme')); ?>"
                     data-content="<?php echo htmlspecialchars(JText::_('wk_theme_tooltip')); ?>">
	                    <span class="label">
		                    <?php echo JText::_('wk_theme'); ?>
	                    </span>
                </div>
                <div class="span8">
					<?php echo $lists['themes']; ?>
                </div>
            </div>
        </div>
        <div class="span6">
            <div class="row-fluid">
                <div class="span4 showPopover"
                     data-original-title="<?php echo htmlspecialchars(JText::_('priority')); ?>"
                     data-content="<?php echo htmlspecialchars(JText::_('wk_default_priority')); ?>">
	                    <span class="label">
		                    <?php echo JText::_('priority'); ?>
	                    </span>
                </div>
                <div class="span8">
					<?php echo $lists['priority']; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span6">
            <div class="row-fluid">
                <div class="span4 showPopover"
                     data-original-title="<?php echo htmlspecialchars(JText::_('wk_assign')); ?>"
                     data-content="<?php echo htmlspecialchars(JText::_('wk_assign_tooltip')); ?>">
	                    <span class="label">
		                    <?php echo JText::_('wk_assign'); ?>
	                    </span>
                </div>
                <div class="span8">
					<?php echo $lists['auto_assign']; ?>
                </div>
            </div>
        </div>
        <div class="span6">
            <div class="row-fluid">
                <div class="span4 showPopover"
                     data-original-title="<?php echo htmlspecialchars(JText::_('wk_hyperlinks')); ?>"
                     data-content="<?php echo htmlspecialchars(JText::_('wk_hyperlinks_tooltip')); ?>">
	                    <span class="label">
		                    <?php echo JText::_('wk_hyperlinks'); ?>
	                    </span>
                </div>
                <div class="span8">
					<?php echo $lists['hyper_links']; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span6">
            <div class="row-fluid">
                <div class="span4 showPopover"
                     data-original-title="<?php echo htmlspecialchars(JText::_('wk_hide_activities')); ?>"
                     data-content="<?php echo htmlspecialchars(JText::_('wk_hide_activities')); ?>">
	                    <span class="label">
		                    <?php echo JText::_('wk_hide_activities'); ?>
	                    </span>
                </div>
                <div class="span8">
					<?php echo $lists['lim_actmsgs']; ?>
                </div>
            </div>
        </div>
        <div class="span6">
            <div class="row-fluid">
                <div class="span4 showPopover"
                     data-original-title="<?php echo htmlspecialchars(JText::_('group')); ?>"
                     data-content="<?php echo htmlspecialchars(JText::_('group_department_tooltip')); ?>">
	                    <span class="label">
		                    <?php echo JText::_('group'); ?>
	                    </span>
                </div>
                <div class="span8">
					<?php echo $lists['id_group']; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <div class="row-fluid">
                <div class="span2 showPopover"
                     data-original-title="<?php echo htmlspecialchars(JText::_('wk_chars')); ?>"
                     data-content="<?php echo htmlspecialchars(JText::_('wk_chars')); ?>">
	                    <span class="label">
		                    <?php echo JText::_('wk_chars'); ?>
	                    </span>
                </div>
                <div class="span10">
					<?php echo $lists['lim_actmsgs_chars']; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <div class="row-fluid">
                <div class="span2 showPopover"
                     data-original-title="<?php echo htmlspecialchars(JText::_('wk_lines')); ?>"
                     data-content="<?php echo htmlspecialchars(JText::_('wk_lines')); ?>">
	                    <span class="label">
		                    <?php echo JText::_('wk_lines'); ?>
	                    </span>
                </div>
                <div class="span10">
					<?php echo $lists['lim_actmsgs_lines']; ?>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div id="tab5" class="tab-pane equalheight pad5">
        <div class="row-fluid">
            <div class="span6">
                <div class="row-fluid">
                    <div class="span4 showPopover"
                         data-original-title="<?php echo htmlspecialchars(JText::_('wk_activities')); ?>"
                         data-content="<?php echo htmlspecialchars(JText::_('wk_activities_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('wk_activities'); ?>
			                    </span>
                    </div>
                    <div class="span8">
						<?php echo $lists['use_activity']; ?>
                    </div>
                </div>
            </div>
            <div class="span6">
                <div class="row-fluid">
                    <div class="span4 showPopover"
                         data-original-title="<?php echo htmlspecialchars(JText::_('wk_tickets2')); ?>"
                         data-content="<?php echo htmlspecialchars(JText::_('wk_tickets_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('wk_tickets2'); ?>
			                    </span>
                    </div>
                    <div class="span8">
						<?php echo $lists['wkticket']; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span6">
                <div class="row-fluid">
                    <div class="span4 showPopover"
                         data-original-title="<?php echo htmlspecialchars(JText::_('wk_kb2')); ?>"
                         data-content="<?php echo htmlspecialchars(JText::_('wk_kb_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('wk_kb2'); ?>
			                    </span>
                    </div>
                    <div class="span8">
						<?php echo $lists['wkkb']; ?>
                    </div>
                </div>
            </div>
            <div class="span6">
                <div class="row-fluid">
                    <div class="span4 showPopover"
                         data-original-title="<?php echo htmlspecialchars(JText::_('wk_downloads_use')); ?>"
                         data-content="<?php echo htmlspecialchars(JText::_('wk_downloads_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('wk_downloads_use'); ?>
			                    </span>
                    </div>
                    <div class="span8">
						<?php echo $lists['wkdownloads']; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span6">
                <div class="row-fluid">
                    <div class="span4 showPopover"
                         data-original-title="<?php echo htmlspecialchars(JText::_('wk_faq2')); ?>"
                         data-content="<?php echo htmlspecialchars(JText::_('wk_faq_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('wk_faq2'); ?>
			                    </span>
                    </div>
                    <div class="span8">
						<?php echo $lists['wkfaq']; ?>
                    </div>
                </div>
            </div>
            <div class="span6">
                <div class="row-fluid">
                    <div class="span4 showPopover"
                         data-original-title="<?php echo htmlspecialchars(JText::_('wk_trouble')); ?>"
                         data-content="<?php echo htmlspecialchars(JText::_('wk_trouble_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('wk_trouble'); ?>
			                    </span>
                    </div>
                    <div class="span8">
						<?php echo $lists['trouble']; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span6">
                <div class="row-fluid">
                    <div class="span4 showPopover"
                         data-original-title="<?php echo htmlspecialchars(JText::_('wk_use_glossary')); ?>"
                         data-content="<?php echo htmlspecialchars(JText::_('wk_glossary_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('wk_use_glossary'); ?>
			                    </span>
                    </div>
                    <div class="span8">
						<?php echo $lists['wkglossary']; ?>
                    </div>
                </div>
            </div>
            <div class="span6">
                <div class="row-fluid">
                    <div class="span4 showPopover"
                         data-original-title="<?php echo htmlspecialchars(JText::_('wk_use_announces')); ?>"
                         data-content="<?php echo htmlspecialchars(JText::_('wk_announces_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('wk_use_announces'); ?>
			                    </span>
                    </div>
                    <div class="span8">
						<?php echo $lists['wkannounces']; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span6">
                <div class="row-fluid">
                    <div class="span4 showPopover"
                         data-original-title="<?php echo htmlspecialchars(JText::_('enable_discussions')); ?>"
                         data-content="<?php echo htmlspecialchars(JText::_('enable_discussions_desc')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('enable_discussions'); ?>
			                    </span>
                    </div>
                    <div class="span8">
						<?php echo $lists['enable_discussions']; ?>
                    </div>
                </div>
            </div>
            <div class="span6">
                <div class="row-fluid">
                    <div class="span4 showPopover"
                         data-original-title="<?php echo htmlspecialchars(JText::_('use_bookmarks')); ?>"
                         data-content="<?php echo htmlspecialchars(JText::_('use_bookmarks_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('use_bookmarks'); ?>
			                    </span>
                    </div>
                    <div class="span8">
						<?php echo $lists['use_bookmarks']; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span6">
                <div class="row-fluid">
                    <div class="span4 showPopover"
                         data-original-title="<?php echo htmlspecialchars(JText::_('use_account')); ?>"
                         data-content="<?php echo htmlspecialchars(JText::_('use_account_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('use_account'); ?>
			                    </span>
                    </div>
                    <div class="span8">
						<?php echo $lists['use_account']; ?>
                    </div>
                </div>
            </div>
            <div class="span6">
                <div class="row-fluid">
                    <div class="span4 showPopover"
                         data-original-title="<?php echo htmlspecialchars(JText::_('digistore_wk')); ?>"
                         data-content="<?php echo htmlspecialchars(JText::_('digistore_wk_desc')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('digistore_wk'); ?>
			                    </span>
                    </div>
                    <div class="span8">
						<?php echo $lists['digistore']; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span6">
                <div class="row-fluid">
                    <div class="span4 showPopover"
                         data-original-title="<?php echo htmlspecialchars(JText::_('use_bugtracker')); ?>"
                         data-content="<?php echo htmlspecialchars(JText::_('use_bugtracker_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('use_bugtracker'); ?>
			                    </span>
                    </div>
                    <div class="span8">
						<?php echo $lists['bugtracker']; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="tab6" class="tab-pane equalheight">
	    <div class="row-fluid">
		    <div class="span6">
			    <div class="row-fluid">
				    <div class="span4 showPopover"
				         data-original-title="<?php echo htmlspecialchars(JText::_('wk_contract')); ?>"
				         data-content="<?php echo htmlspecialchars(JText::_('wk_contract_tooltip')); ?>">
	                    <span class="label">
		                    <?php echo JText::_('wk_contract'); ?>
	                    </span>
				    </div>
				    <div class="span8">
					    <?php echo $lists['contract']; ?>
				    </div>
			    </div>
		    </div>
		    <div class="span6">
			    <div class="row-fluid">
				    <div class="span4 showPopover"
				         data-original-title="<?php echo htmlspecialchars(JText::_('contract_total_disable')); ?>"
				         data-content="<?php echo htmlspecialchars(JText::_('contract_total_disable_tooltip')); ?>">
	                    <span class="label">
		                    <?php echo JText::_('contract_total_disable'); ?>
	                    </span>
				    </div>
				    <div class="span8">
					    <?php echo $lists['contract_total_disable']; ?>
				    </div>
			    </div>
		    </div>
	    </div>
	    <div class="row-fluid">
		    <div class="span6">
			    <div class="row-fluid">
				    <div class="span4 showPopover"
				         data-original-title="<?php echo htmlspecialchars(JText::_('department_support_only')); ?>"
				         data-content="<?php echo htmlspecialchars(JText::_('department_support_only_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('department_support_only'); ?>
			                    </span>
				    </div>
				    <div class="span8">
					    <?php echo $lists['support_only']; ?>
				    </div>
			    </div>
		    </div>
	    </div>
	    <div class="row-fluid">
		    <div class="span6">
			    <div class="row-fluid">
				    <div class="span4 showPopover"
				         data-original-title="<?php echo htmlspecialchars(JText::_('usergroups')); ?>"
				         data-content="<?php echo htmlspecialchars(JText::_('usergroups_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('usergroups'); ?>
			                    </span>
				    </div>
				    <div class="span8">
					    <?php echo JHtml::_('access.usergroups', 'groups', $lists['groups'], true); ?>
				    </div>
			    </div>
		    </div>
	    </div>
	    <div class="clr"></div>
    </div>
    <div id="tab2" class="tab-pane equalheight pad5">
        <div class="row-fluid">
            <div class="span6">
                <div class="row-fluid">
                    <div class="span4 showPopover"
                         data-original-title="<?php echo htmlspecialchars(JText::_('wk_from_name')); ?>"
                         data-content="<?php echo htmlspecialchars(JText::_('wk_from_name_tooltip')); ?>">
	                    <span class="label">
		                    <?php echo JText::_('wk_from_name'); ?>
	                    </span>
                    </div>
                    <div class="span8">
                        <input type="text"
                               id="wkmail_address_name"
                               name="wkmail_address_name"
                               value="<?php echo $row->wkmail_address_name; ?>"
                               maxlength="100" />
                    </div>
                </div>
            </div>
            <div class="span6">
                <div class="row-fluid">
                    <div class="span4 showPopover"
                         data-original-title="<?php echo htmlspecialchars(JText::_('wk_from_mail')); ?>"
                         data-content="<?php echo htmlspecialchars(JText::_('wk_from_mail_tooltip')); ?>">
	                    <span class="label">
		                    <?php echo JText::_('wk_from_mail'); ?>
	                    </span>
                    </div>
                    <div class="span8">
                        <input type="text"
                               id="wkmail_address"
                               name="wkmail_address"
                               value="<?php echo $row->wkmail_address; ?>"
                               maxlength="100" />
                    </div>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span6">
                <div class="row-fluid">
                    <div class="span4 showPopover"
                         data-original-title="<?php echo htmlspecialchars(JText::_('wk_admin_mail')); ?>"
                         data-content="<?php echo htmlspecialchars(JText::_('wk_admin_mail_tooltip')); ?>">
	                    <span class="label">
		                    <?php echo JText::_('wk_admin_mail'); ?>
	                    </span>
                    </div>
                    <div class="span8">
                        <input type="text" id="wkadmin_email" name="wkadmin_email"
                               value="<?php echo $row->wkadmin_email; ?>" maxlength="100" />
                    </div>
                </div>
            </div>
            <div class="span6">
                <div class="row-fluid">
                    <div class="span4 showPopover"
                         data-original-title="<?php echo htmlspecialchars(JText::_('add_mail_tag')); ?>"
                         data-content="<?php echo htmlspecialchars(JText::_('add_mail_tag_tooltip')); ?>">
	                    <span class="label">
		                    <?php echo JText::_('add_mail_tag'); ?>
	                    </span>
                    </div>
                    <div class="span8">
						<?php echo $lists['add_mail_tag']; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span12">
                <h4 class="clear"><?php echo JText::_('wk_assign_notifications'); ?></h4>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span6">
                <div class="row-fluid">
                    <div class="span4 showPopover"
                         data-original-title="<?php echo htmlspecialchars(JText::_('tkt_asgn_new_asgn')); ?>"
                         data-content="<?php echo htmlspecialchars(JText::_('tkt_asgn_new_asgn_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('tkt_asgn_new_asgn'); ?>
			                    </span>
                    </div>
                    <div class="span8">
						<?php echo $lists['tkt_asgn_new_asgn']; ?>
                    </div>
                </div>
            </div>
            <div class="span6">
                <div class="row-fluid">
                    <div class="span4 showPopover"
                         data-original-title="<?php echo htmlspecialchars(JText::_('tkt_asgn_old_asgn')); ?>"
                         data-content="<?php echo htmlspecialchars(JText::_('tkt_asgn_old_asgn_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('tkt_asgn_old_asgn'); ?>
			                    </span>
                    </div>
                    <div class="span8">
						<?php echo $lists['tkt_asgn_old_asgn']; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span6">
                <div class="row-fluid">
                    <div class="span4 showPopover"
                         data-original-title="<?php echo htmlspecialchars(JText::_('tkt_asgn_nfy_usr_one')); ?>"
                         data-content="<?php echo htmlspecialchars(JText::_('tkt_asgn_nfy_usr_one_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('tkt_asgn_nfy_usr_one'); ?>
			                    </span>
                    </div>
                    <div class="span8">
						<?php echo $lists['tkt_asgn_nfy_usr_one']; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span12">
                <h4 class="clear"><?php echo JText::_('wk_create_notifications'); ?></h4>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span6">
                <div class="row-fluid">
                    <div class="span4 showPopover"
                         data-original-title="<?php echo htmlspecialchars(JText::_('tkt_crt_nfy_mgr')); ?>"
                         data-content="<?php echo htmlspecialchars(JText::_('tkt_crt_nfy_mgr_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('tkt_crt_nfy_mgr'); ?>
			                    </span>
                    </div>
                    <div class="span8">
						<?php echo $lists['tkt_crt_nfy_mgr']; ?>
                    </div>
                </div>
            </div>
            <div class="span6">
                <div class="row-fluid">
                    <div class="span4 showPopover"
                         data-original-title="<?php echo htmlspecialchars(JText::_('wkemail')); ?>"
                         data-content="<?php echo htmlspecialchars(JText::_('wkemail_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('wkemail'); ?>
			                    </span>
                    </div>
                    <div class="span8">
						<?php echo $lists['wkemail']; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="tab3" class="tab-pane equalheight">
        <div id="assigns" name="assigns" align="left"></div>
        <div class="clr"></div>
    </div>
    <div id="tab4" class="tab-pane equalheight">
        <div id="links" name="links" align="left"></div>
        <div class="clr"></div>
    </div>
    </div>
    </div>

    <input type="hidden" name="option" value="com_maqmahelpdesk"/>
    <input type="hidden" id="id" name="id" value="<?php echo $row->id; ?>"/>
    <input type="hidden" name="old_logo" value="<?php echo $row->logo; ?>"/>
    <input type="hidden" name="task" value=""/>
    </form><?php
	}
}
