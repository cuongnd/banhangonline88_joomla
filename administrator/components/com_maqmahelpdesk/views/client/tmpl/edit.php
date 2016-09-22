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
	static function display(&$row, $users, $clientdocs, $lists, $contracts, $tickets, $inforecords, $downloads, $cfields)
	{
		$database = JFactory::getDBO();
		$session = JFactory::getSession();
		$supportConfig = HelpdeskUtility::GetConfig();
		$GLOBALS['titulo_client_edit'] = ($row->id ? JText::_('edit') : JText::_('add')) . ' ' . JText::_('client');
		$imgpath = JURI::root() . '/components/com_maqmahelpdesk/images';
		$formtoken = (HelpdeskUtility::JoomlaCheck() ? $session->getToken() : JSession::getFormToken());

		$sel3 = '';
		for ($i = 0; $i <= 8; $i++)
		{
			$sel3 .= '<option value="' . ($i) . '.00"' . ($row->travel_time == $i . ".00" ? ' selected' : '') . '>' . ($i) . ':00</option>';
			$sel3 .= '<option value="' . ($i) . '.15"' . ($row->travel_time == $i . ".15" ? ' selected' : '') . '>' . ($i) . ':15</option>';
			$sel3 .= '<option value="' . ($i) . '.30"' . ($row->travel_time == $i . ".30" ? ' selected' : '') . '>' . ($i) . ':30</option>';
			$sel3 .= '<option value="' . ($i) . '.45"' . ($row->travel_time == $i . ".45" ? ' selected' : '') . '>' . ($i) . ':45</option>';
		}

		$editCustomParam = "";
		if (count($cfields) > 0)
		{
			for ($x = 0; $x < count($cfields); $x++)
			{
				$cfield = $cfields[$x];
				$editCustomParam .= ", custom" . $cfield->id_field;
			}
		} ?>

	    <div id="Layer1"
	         style="position: absolute; margin-left: auto; margin-right: auto; width: 200px; height: 125px; z-index: 1; display: none; background-color: #efefef; layer-background-color: #FF0000; border: 1px solid #99989D;">
	        <table width="100%">
	            <tr>
	                <td bgcolor="#330099" height="25">
	                    &nbsp;<span style="color:#FFFFFF"><?php echo JText::_('save');?></span>
	                </td>
	            </tr>
	            <tr>
	                <td align="center" valign="middle">
	                    <p><?php echo JText::_('save_progress');?></p>

	                    <p><img src="../components/com_maqmahelpdesk/images/loading.gif"></p>

	                    <p><?php echo JText::_('be_pacient');?></p>
	                </td>
	            </tr>
	        </table>
	    </div>

	    <div class="breadcrumbs">
	        <a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
	        <a href="index.php?option=com_maqmahelpdesk&task=client"><?php echo JText::_('clients_manager'); ?></a>
	        <span><?php echo JText::_('edit'); ?></span>
	    </div>

	    <div class="tabbable tabs-left contentarea pad5">
	    <ul class="nav nav-tabs equalheight">
	        <li class="active"><a href="#tab1" data-toggle="tab"><img
	                src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/clients.png"
	                border="0" align="absmiddle"/>&nbsp; <?php echo JText::_('general');?></a></li>
	        <li><a href="#tab8" data-toggle="tab"><img
	                src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/access.png"
	                border="0"
	                align="absmiddle"/>&nbsp; <?php echo JText::_('ACCESS');?>
	        </a></li>
	        <li><a href="#tab2" data-toggle="tab"><img
	                src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/users.png"
	                border="0"
	                align="absmiddle"/>&nbsp; <?php echo JText::_('users') . ' <span class="lbl">' . count($users) . '</span>';?>
	        </a></li>
	        <li><a href="#tab3" data-toggle="tab"><img
	                src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/contracts.png"
	                border="0"
	                align="absmiddle"/>&nbsp; <?php echo JText::_('contracts') . ' <span class="lbl">' . count($contracts) . '</span>';?>
	        </a></li>
	        <li><a href="#tab4" data-toggle="tab"><img
	                src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/files.png"
	                border="0"
	                align="absmiddle"/>&nbsp; <?php echo JText::_('downloads') . ' <span class="lbl">' . count($downloads) . '</span>';?>
	        </a></li>
	        <li><a href="#tab5" data-toggle="tab"><img
	                src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/table.png"
	                border="0"
	                align="absmiddle"/>&nbsp; <?php echo JText::_('attachments') . ' <span class="lbl">' . count($clientdocs) . '</span>';?>
	        </a></li>
	        <li><a href="#tab6" data-toggle="tab"><img
	                src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/logs.png"
	                border="0"
	                align="absmiddle"/>&nbsp; <?php echo JText::_('info_title') . ' <span class="lbl">' . count($inforecords) . '</span>';?>
	        </a></li>
	        <li><a href="#tab7" data-toggle="tab"><img
	                src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/tickets.png"
	                border="0"
	                align="absmiddle"/>&nbsp; <?php echo JText::_('tickets') . ' <span class="lbl">' . count($tickets) . '</span>';?>
	        </a></li>
	    </ul>
        <form action="index.php" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data" class="label-inline">
	    <div class="tab-content contentbar withleft">
		<?php echo JHtml::_('form.token'); ?>
	    <div id="tab1" class="tab-pane active equalheight">
		    <div class="row-fluid">
		        <div class="span12">
		            <div class="row-fluid">
		                <div class="span2 showPopover"
		                     data-original-title="<?php echo htmlspecialchars(JText::_('block_client')); ?>"
		                     data-content="<?php echo htmlspecialchars(JText::_('block_client_tooltip')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('block_client'); ?>
				                    </span>
		                </div>
		                <div class="span10">
							<?php echo $lists['block']; ?>
		                </div>
		            </div>
		        </div>
		    </div>
		    <div class="row-fluid">
		        <div class="span12">
		            <div class="row-fluid">
		                <div class="span2 showPopover"
		                     data-original-title="<?php echo htmlspecialchars(JText::_('name')); ?>"
		                     data-content="<?php echo htmlspecialchars(JText::_('block_client_tooltip')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('name'); ?>
				                    </span>
		                </div>
		                <div class="span10">
		                    <input type="text"
		                           id="clientname"
		                           name="clientname"
		                           class="span10"
		                           value="<?php echo $row->clientname; ?>"
		                           maxlength="100"
		                           onblur="CreateSlug('clientname');" />
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
		        <div class="span6">
		            <div class="row-fluid">
		                <div class="span4 showPopover"
		                     data-original-title="<?php echo htmlspecialchars(JText::_('clientid')); ?>"
		                     data-content="<?php echo htmlspecialchars(JText::_('clientid')); ?>">
							                    <span class="label">
								                    <?php echo JText::_('clientid'); ?>
							                    </span>
		                </div>
		                <div class="span8">
		                    <input type="text"
		                           id="clientid"
		                           name="clientid"
		                           value="<?php echo $row->clientid; ?>"
		                           maxlength="100" />
		                </div>
		            </div>
		        </div>
		        <div class="span6">
		            <div class="row-fluid">
		                <div class="span4 showPopover"
		                     data-original-title="<?php echo htmlspecialchars(JText::_('taxnumber')); ?>"
		                     data-content="<?php echo htmlspecialchars(JText::_('taxnumber')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('taxnumber'); ?>
				                    </span>
		                </div>
		                <div class="span8">
		                    <input type="text"
		                           id="taxnumber"
		                           name="taxnumber"
		                           value="<?php echo $row->taxnumber; ?>"
		                           maxlength="100" />
		                </div>
		            </div>
		        </div>
		    </div>
		    <div class="row-fluid">
		        <div class="span6">
		            <div class="row-fluid">
		                <div class="span4 showPopover"
		                     data-original-title="<?php echo htmlspecialchars(JText::_('logo')); ?>"
		                     data-content="<?php echo htmlspecialchars(JText::_('logo')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('logo'); ?>
				                    </span>
		                </div>
		                <div class="span8">
		                    <input type="file"
		                           id="logo"
		                           name="logo"
		                           value="" />
							<?php if ($row->logo != ''):?>
		                    <br />
		                    <span class="label"><?php echo JText::_('CURRENT_LOGO'); ?></span>
		                    <img src="<?php echo JURI::root();?>media/com_maqmahelpdesk/images/logos/<?php echo $row->logo; ?>" alt="" />
							<?php endif;?>
		                </div>
		            </div>
		        </div>
		    </div>
		    <div class="row-fluid">
		        <div class="span12">
		            <div class="row-fluid">
		                <div class="span2 showPopover"
		                     data-original-title="<?php echo htmlspecialchars(JText::_('description')); ?>"
		                     data-content="<?php echo htmlspecialchars(JText::_('description')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('description'); ?>
					                    </span>
		                </div>
		                <div class="span10">
		                    <textarea id="description"
		                              name="description"
	                                  class="span10"
		                              style="height:100px;"><?php echo str_replace("\'","'",$row->description); ?></textarea>
		                </div>
		            </div>
		        </div>
		    </div>
		    <div class="row-fluid">
		        <div class="span12">
		            <div class="row-fluid">
		                <div class="span2 showPopover"
		                     data-original-title="<?php echo htmlspecialchars(JText::_('address')); ?>"
		                     data-content="<?php echo htmlspecialchars(JText::_('address')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('address'); ?>
					                    </span>
		                </div>
		                <div class="span10">
		                    <input type="text"
		                           id="address"
		                           name="address"
		                           class="span10"
		                           value="<?php echo $row->address; ?>"
		                           maxlength="100" />
		                </div>
		            </div>
		        </div>
		    </div>
		    <div class="row-fluid">
		        <div class="span6">
		            <div class="row-fluid">
		                <div class="span4 showPopover"
		                     data-original-title="<?php echo htmlspecialchars(JText::_('zipcode')); ?>"
		                     data-content="<?php echo htmlspecialchars(JText::_('zipcode')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('zipcode'); ?>
					                    </span>
		                </div>
		                <div class="span8">
		                    <input type="text"
		                           id="zipcode"
		                           name="zipcode"
		                           value="<?php echo $row->zipcode; ?>"
		                           maxlength="100" />
		                </div>
		            </div>
		        </div>
		        <div class="span6">
		            <div class="row-fluid">
		                <div class="span4 showPopover"
		                     data-original-title="<?php echo htmlspecialchars(JText::_('city')); ?>"
		                     data-content="<?php echo htmlspecialchars(JText::_('city')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('city'); ?>
					                    </span>
		                </div>
		                <div class="span8">
		                    <input type="text"
		                           id="city"
		                           name="city"
		                           value="<?php echo $row->city; ?>"
		                           maxlength="100" />
		                </div>
		            </div>
		        </div>
		    </div>
		    <div class="row-fluid">
		        <div class="span6">
		            <div class="row-fluid">
		                <div class="span4 showPopover"
		                     data-original-title="<?php echo htmlspecialchars(JText::_('state')); ?>"
		                     data-content="<?php echo htmlspecialchars(JText::_('state')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('state'); ?>
					                    </span>
		                </div>
		                <div class="span8">
		                    <input type="text"
		                           id="state"
		                           name="state"
		                           value="<?php echo $row->state; ?>"
		                           maxlength="100" />
		                </div>
		            </div>
		        </div>
		        <div class="span6">
		            <div class="row-fluid">
		                <div class="span4 showPopover"
		                     data-original-title="<?php echo htmlspecialchars(JText::_('country')); ?>"
		                     data-content="<?php echo htmlspecialchars(JText::_('country')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('country'); ?>
					                    </span>
		                </div>
		                <div class="span8">
		                    <input type="text"
		                           id="country"
		                           name="country"
		                           value="<?php echo $row->country; ?>"
		                           maxlength="100" />
		                </div>
		            </div>
		        </div>
		    </div>
		    <div class="row-fluid">
		        <div class="span6">
		            <div class="row-fluid">
		                <div class="span4 showPopover"
		                     data-original-title="<?php echo htmlspecialchars(JText::_('phone')); ?>"
		                     data-content="<?php echo htmlspecialchars(JText::_('phone')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('phone'); ?>
					                    </span>
		                </div>
		                <div class="span8">
		                    <input type="text"
		                           id="phone"
		                           name="phone"
		                           value="<?php echo $row->phone; ?>"
		                           maxlength="100" />
		                </div>
		            </div>
		        </div>
		        <div class="span6">
		            <div class="row-fluid">
		                <div class="span4 showPopover"
		                     data-original-title="<?php echo htmlspecialchars(JText::_('fax')); ?>"
		                     data-content="<?php echo htmlspecialchars(JText::_('fax')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('fax'); ?>
					                    </span>
		                </div>
		                <div class="span8">
		                    <input type="text"
		                           id="fax"
		                           name="fax"
		                           value="<?php echo $row->fax; ?>"
		                           maxlength="100" />
		                </div>
		            </div>
		        </div>
		    </div>
		    <div class="row-fluid">
		        <div class="span6">
		            <div class="row-fluid">
		                <div class="span4 showPopover"
		                     data-original-title="<?php echo htmlspecialchars(JText::_('mobile')); ?>"
		                     data-content="<?php echo htmlspecialchars(JText::_('mobile')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('mobile'); ?>
					                    </span>
		                </div>
		                <div class="span8">
		                    <input type="text"
		                           id="mobile"
		                           name="mobile"
		                           value="<?php echo $row->mobile; ?>"
		                           maxlength="100" />
		                </div>
		            </div>
		        </div>
		        <div class="span6">
		            <div class="row-fluid">
		                <div class="span4 showPopover"
		                     data-original-title="<?php echo htmlspecialchars(JText::_('email')); ?>"
		                     data-content="<?php echo htmlspecialchars(JText::_('email')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('email'); ?>
					                    </span>
		                </div>
		                <div class="span8">
		                    <input type="text"
		                           id="email"
		                           name="email"
		                           value="<?php echo $row->email; ?>"
		                           maxlength="100" />
		                </div>
		            </div>
		        </div>
		    </div>
		    <div class="row-fluid">
		        <div class="span6">
		            <div class="row-fluid">
		                <div class="span4 showPopover"
		                     data-original-title="<?php echo htmlspecialchars(JText::_('contact')); ?>"
		                     data-content="<?php echo htmlspecialchars(JText::_('contact')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('contact'); ?>
					                    </span>
		                </div>
		                <div class="span8">
		                    <input type="text"
		                           id="contactname"
		                           name="contactname"
		                           value="<?php echo $row->contactname; ?>"
		                           maxlength="100" />
		                </div>
		            </div>
		        </div>
		        <div class="span6">
		            <div class="row-fluid">
		                <div class="span4 showPopover"
		                     data-original-title="<?php echo htmlspecialchars(JText::_('website')); ?>"
		                     data-content="<?php echo htmlspecialchars(JText::_('website')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('website'); ?>
					                    </span>
		                </div>
		                <div class="span8">
		                    <input type="text"
		                           id="website"
		                           name="website"
		                           value="<?php echo $row->website; ?>"
		                           maxlength="100" />
		                </div>
		            </div>
		        </div>
		    </div>
		    <div class="row-fluid">
		        <div class="span6">
		            <div class="row-fluid">
		                <div class="span4 showPopover"
		                     data-original-title="<?php echo htmlspecialchars(JText::_('notify_manager')); ?>"
		                     data-content="<?php echo htmlspecialchars(JText::_('contact')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('notify_manager'); ?>
					                    </span>
		                </div>
		                <div class="span8">
							<?php echo $lists['manager2']; ?>
		                </div>
		            </div>
		        </div>
		        <div class="span6">
		            <div class="row-fluid">
		                <div class="span4 showPopover"
		                     data-original-title="<?php echo htmlspecialchars(JText::_('client_approval')); ?>"
		                     data-content="<?php echo htmlspecialchars(JText::_('client_approval_tooltip')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('client_approval'); ?>
					                    </span>
		                </div>
		                <div class="span8">
							<?php echo $lists['approval']; ?>
		                </div>
		            </div>
		        </div>
		    </div>
		    <div class="row-fluid">
		        <div class="span6">
		            <div class="row-fluid">
		                <div class="span4 showPopover"
		                     data-original-title="<?php echo htmlspecialchars(JText::_('client_mail_notify')); ?>"
		                     data-content="<?php echo htmlspecialchars(JText::_('client_mail_notify_tooltip')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('client_mail_notify'); ?>
					                    </span>
		                </div>
		                <div class="span8">
		                    <textarea id="client_mail_notify"
		                              name="client_mail_notify"
		                              style="height:50px;"><?php echo $row->client_mail_notify; ?></textarea>
		                </div>
		            </div>
		        </div>
		        <div class="span6">
		            <div class="row-fluid">
		                <div class="span4 showPopover"
		                     data-original-title="<?php echo htmlspecialchars(JText::_('travel_time')); ?>"
		                     data-content="<?php echo htmlspecialchars(JText::_('travel_time_tooltip')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('travel_time'); ?>
					                    </span>
		                </div>
		                <div class="span8">
		                    <select id="travel_time" name="travel_time"><?php echo $sel3; ?></select>
		                </div>
		            </div>
		        </div>
		    </div>
		    <div class="row-fluid">
		        <div class="span6">
		            <div class="row-fluid">
		                <div class="span4 showPopover"
		                     data-original-title="<?php echo htmlspecialchars(JText::_('billing_rate')); ?>"
		                     data-content="<?php echo htmlspecialchars(JText::_('billing_tooltip')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('billing_rate'); ?>
					                    </span>
		                </div>
		                <div class="span8">
		                    <input type="text"
		                           id="rate"
		                           name="rate"
		                           value="<?php echo $row->rate; ?>"
		                           maxlength="10" />
		                </div>
		            </div>
		        </div>
	            <div class="span6">
	                <div class="row-fluid">
	                    <div class="span4 showPopover"
	                         data-original-title="<?php echo htmlspecialchars(JText::_('CLIENT_AUTO_ASSIGN')); ?>"
	                         data-content="<?php echo htmlspecialchars(JText::_('CLIENT_AUTO_ASSIGN_TOOLTIP')); ?>">
		                    <span class="label">
			                    <?php echo JText::_('CLIENT_AUTO_ASSIGN'); ?>
		                    </span>
	                    </div>
	                    <div class="span8">
						    <?php echo $lists['auto_assign']; ?>
	                    </div>
	                </div>
	            </div><?php

			    for ($x = 0; $x < count($lists['cfields']); $x++):
				    $cfield = $lists['cfields'][$x]; ?>
				    <div class="row-fluid">
				    <div class="span12">
					    <div class="row-fluid">
						    <div class="span2 showPopover"
						         data-original-title="<?php echo htmlspecialchars($cfield->caption); ?>"
						         data-content="<?php echo htmlspecialchars($cfield->caption); ?>">
						                    <span class="label">
							                    <?php echo $cfield->caption; ?>
						                    </span>
						    </div>
						    <div class="span10">
							    <?php echo HelpdeskForm::WriteField(0, $cfield->id_field, $cfield->ftype, $cfield->value, $cfield->size, $cfield->maxlength, 0, 0, 0, 0, 0, 0, 0, $row->id); ?>
						    </div>
					    </div>
				    </div>
				    </div><?php
			    endfor;?>
		    </div>

		    <input type="hidden" name="option" value="com_maqmahelpdesk"/>
		    <input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
		    <input type="hidden" name="date_created"
		           value="<?php echo ($row->id > 0 ? $row->date_created : date("Y-m-d")); ?>"/>
		    <input type="hidden" id="task" name="task" value=""/>
	    </div>
	    <div id="tab8" class="tab-pane equalheight"><?php
		    $captions = array(JText::_('MQ_NO'), JText::_('MQ_YES'));
		    $values = array('0', '1');

		    $lists['app_announcements'] = HelpdeskForm::SwitchCheckbox('radio', 'app_announcements_0', $captions, $values, HelpdeskClient::AccessApplication(0, $row->id, 'announcements'), 'switch');
		    $lists['app_bugtracker'] = HelpdeskForm::SwitchCheckbox('radio', 'app_bugtracker_0', $captions, $values, HelpdeskClient::AccessApplication(0, $row->id, 'bugtracker'), 'switch');
		    $lists['app_discussions'] = HelpdeskForm::SwitchCheckbox('radio', 'app_discussions_0', $captions, $values, HelpdeskClient::AccessApplication(0, $row->id, 'discussions'), 'switch');
		    $lists['app_glossary'] = HelpdeskForm::SwitchCheckbox('radio', 'app_glossary_0', $captions, $values, HelpdeskClient::AccessApplication(0, $row->id, 'glossary'), 'switch');
		    $lists['app_trouble'] = HelpdeskForm::SwitchCheckbox('radio', 'app_trouble_0', $captions, $values, HelpdeskClient::AccessApplication(0, $row->id, 'trouble'), 'switch');
		    $lists['app_downloads'] = HelpdeskForm::SwitchCheckbox('radio', 'app_downloads_0', $captions, $values, HelpdeskClient::AccessApplication(0, $row->id, 'downloads'), 'switch');
		    $lists['app_kb'] = HelpdeskForm::SwitchCheckbox('radio', 'app_kb_0', $captions, $values, HelpdeskClient::AccessApplication(0, $row->id, 'kb'), 'switch');
		    $lists['app_faq'] = HelpdeskForm::SwitchCheckbox('radio', 'app_faq_0', $captions, $values, HelpdeskClient::AccessApplication(0, $row->id, 'faq'), 'switch');
		    $lists['app_ticket'] = HelpdeskForm::SwitchCheckbox('radio', 'app_ticket_0', $captions, $values, HelpdeskClient::AccessApplication(0, $row->id, 'ticket'), 'switch'); ?>

	        <div class="row-fluid">
	            <h4><?php echo JText::_("ALL");?> &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;" onclick="ToggleDepartmentAccess(0);" style="font-size:10px;"><?php echo JText::_("DEPARTMENT_SET_ACCESS");?></a></h4>
                <div class="alert alert-block">
                    <h4><?php echo JText::_("WARNING");?></h4>
	                <?php echo JText::_("ALL_DEPARTMENTS_ACCESS_WARNING");?>
		        </div>
	        </div>
	        <input type="hidden" name="depaccess[]" value="0" />
	        <div id="depaccess0_area" style="<?php echo (is_numeric(HelpdeskClient::AccessApplication(0, $row->id, 'announcements')) ? '' : 'display:none;');?>">
	            <div class="row-fluid">
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('ANNOUNCEMENTS')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('CLIENT_CAN_ACCESS_TIP')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('ANNOUNCEMENTS'); ?>
					                    </span>
	                        </div>
	                        <div class="span8">
							    <?php echo $lists['app_announcements']; ?>
	                        </div>
	                    </div>
	                </div>
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('BUGTRACKER')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('CLIENT_CAN_ACCESS_TIP')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('BUGTRACKER'); ?>
					                    </span>
	                        </div>
	                        <div class="span8">
							    <?php echo $lists['app_bugtracker']; ?>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="row-fluid">
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('ANNOUNCEMENTS')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('CLIENT_CAN_ACCESS_TIP')); ?>">
						                    <span class="label">
							                    <?php echo JText::_('DISCUSSIONS'); ?>
						                    </span>
	                        </div>
	                        <div class="span8">
							    <?php echo $lists['app_discussions']; ?>
	                        </div>
	                    </div>
	                </div>
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('GLOSSARY')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('CLIENT_CAN_ACCESS_TIP')); ?>">
						                    <span class="label">
							                    <?php echo JText::_('GLOSSARY'); ?>
						                    </span>
	                        </div>
	                        <div class="span8">
							    <?php echo $lists['app_glossary']; ?>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="row-fluid">
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('TROUBLESHOOTER')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('CLIENT_CAN_ACCESS_TIP')); ?>">
						                    <span class="label">
							                    <?php echo JText::_('TROUBLESHOOTER'); ?>
						                    </span>
	                        </div>
	                        <div class="span8">
							    <?php echo $lists['app_trouble']; ?>
	                        </div>
	                    </div>
	                </div>
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('DOWNLOADS')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('CLIENT_CAN_ACCESS_TIP')); ?>">
						                    <span class="label">
							                    <?php echo JText::_('DOWNLOADS'); ?>
						                    </span>
	                        </div>
	                        <div class="span8">
							    <?php echo $lists['app_downloads']; ?>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="row-fluid">
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('KNOWLEDGE_BASE')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('CLIENT_CAN_ACCESS_TIP')); ?>">
						                    <span class="label">
							                    <?php echo JText::_('KNOWLEDGE_BASE'); ?>
						                    </span>
	                        </div>
	                        <div class="span8">
							    <?php echo $lists['app_kb']; ?>
	                        </div>
	                    </div>
	                </div>
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('WK_FAQ')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('CLIENT_CAN_ACCESS_TIP')); ?>">
						                    <span class="label">
							                    <?php echo JText::_('WK_FAQ'); ?>
						                    </span>
	                        </div>
	                        <div class="span8">
							    <?php echo $lists['app_faq']; ?>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="row-fluid">
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('TICKETS')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('CLIENT_CAN_ACCESS_TIP')); ?>">
						                    <span class="label">
							                    <?php echo JText::_('TICKETS'); ?>
						                    </span>
	                        </div>
	                        <div class="span8">
							    <?php echo $lists['app_ticket']; ?>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div><?php

	        for($i=0; $i<count($lists['wks']); $i++)
	        {
		        $depaccess = $lists['wks'][$i];
		        $lists['app_announcements'] = HelpdeskForm::SwitchCheckbox('radio', 'app_announcements_' . $depaccess->id, $captions, $values, HelpdeskClient::AccessApplication($depaccess->id, $row->id, 'announcements'), 'switch');
		        $lists['app_bugtracker'] = HelpdeskForm::SwitchCheckbox('radio', 'app_bugtracker_' . $depaccess->id, $captions, $values, HelpdeskClient::AccessApplication($depaccess->id, $row->id, 'bugtracker'), 'switch');
		        $lists['app_discussions'] = HelpdeskForm::SwitchCheckbox('radio', 'app_discussions_' . $depaccess->id, $captions, $values, HelpdeskClient::AccessApplication($depaccess->id, $row->id, 'discussions'), 'switch');
		        $lists['app_glossary'] = HelpdeskForm::SwitchCheckbox('radio', 'app_glossary_' . $depaccess->id, $captions, $values, HelpdeskClient::AccessApplication($depaccess->id, $row->id, 'glossary'), 'switch');
		        $lists['app_trouble'] = HelpdeskForm::SwitchCheckbox('radio', 'app_trouble_' . $depaccess->id, $captions, $values, HelpdeskClient::AccessApplication($depaccess->id, $row->id, 'trouble'), 'switch');
		        $lists['app_downloads'] = HelpdeskForm::SwitchCheckbox('radio', 'app_downloads_' . $depaccess->id, $captions, $values, HelpdeskClient::AccessApplication($depaccess->id, $row->id, 'downloads'), 'switch');
		        $lists['app_kb'] = HelpdeskForm::SwitchCheckbox('radio', 'app_kb_' . $depaccess->id, $captions, $values, HelpdeskClient::AccessApplication($depaccess->id, $row->id, 'kb'), 'switch');
		        $lists['app_faq'] = HelpdeskForm::SwitchCheckbox('radio', 'app_faq_' . $depaccess->id, $captions, $values, HelpdeskClient::AccessApplication($depaccess->id, $row->id, 'faq'), 'switch');
		        $lists['app_ticket'] = HelpdeskForm::SwitchCheckbox('radio', 'app_ticket_' . $depaccess->id, $captions, $values, HelpdeskClient::AccessApplication($depaccess->id, $row->id, 'ticket'), 'switch'); ?>

                <div class="row-fluid">
                    <h4><?php echo $depaccess->wkdesc;?> &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;" onclick="ToggleDepartmentAccess(<?php echo $depaccess->id;?>);" style="font-size:10px;"><?php echo JText::_("DEPARTMENT_SET_ACCESS");?></a></h4>
                </div>
	            <input type="hidden" name="depaccess[]" value="<?php echo $depaccess->id;?>" />
                <div id="depaccess<?php echo $depaccess->id;?>_area" style="<?php echo (is_numeric(HelpdeskClient::AccessApplication($depaccess->id, $row->id, 'announcements')) ? '' : 'display:none;');?>">
			        <div class="row-fluid">
	                    <div class="span6">
	                        <div class="row-fluid">
	                            <div class="span4 showPopover"
	                                 data-original-title="<?php echo htmlspecialchars(JText::_('ANNOUNCEMENTS')); ?>"
	                                 data-content="<?php echo htmlspecialchars(JText::_('CLIENT_CAN_ACCESS_TIP')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('ANNOUNCEMENTS'); ?>
				                    </span>
	                            </div>
	                            <div class="span8">
							        <?php echo $lists['app_announcements']; ?>
	                            </div>
	                        </div>
	                    </div>
	                    <div class="span6">
	                        <div class="row-fluid">
	                            <div class="span4 showPopover"
	                                 data-original-title="<?php echo htmlspecialchars(JText::_('BUGTRACKER')); ?>"
	                                 data-content="<?php echo htmlspecialchars(JText::_('CLIENT_CAN_ACCESS_TIP')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('BUGTRACKER'); ?>
				                    </span>
	                            </div>
	                            <div class="span8">
							        <?php echo $lists['app_bugtracker']; ?>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	                <div class="row-fluid">
	                    <div class="span6">
	                        <div class="row-fluid">
	                            <div class="span4 showPopover"
	                                 data-original-title="<?php echo htmlspecialchars(JText::_('ANNOUNCEMENTS')); ?>"
	                                 data-content="<?php echo htmlspecialchars(JText::_('CLIENT_CAN_ACCESS_TIP')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('DISCUSSIONS'); ?>
					                    </span>
	                            </div>
	                            <div class="span8">
							        <?php echo $lists['app_discussions']; ?>
	                            </div>
	                        </div>
	                    </div>
	                    <div class="span6">
	                        <div class="row-fluid">
	                            <div class="span4 showPopover"
	                                 data-original-title="<?php echo htmlspecialchars(JText::_('GLOSSARY')); ?>"
	                                 data-content="<?php echo htmlspecialchars(JText::_('CLIENT_CAN_ACCESS_TIP')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('GLOSSARY'); ?>
					                    </span>
	                            </div>
	                            <div class="span8">
							        <?php echo $lists['app_glossary']; ?>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	                <div class="row-fluid">
	                    <div class="span6">
	                        <div class="row-fluid">
	                            <div class="span4 showPopover"
	                                 data-original-title="<?php echo htmlspecialchars(JText::_('TROUBLESHOOTER')); ?>"
	                                 data-content="<?php echo htmlspecialchars(JText::_('CLIENT_CAN_ACCESS_TIP')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('TROUBLESHOOTER'); ?>
					                    </span>
	                            </div>
	                            <div class="span8">
							        <?php echo $lists['app_trouble']; ?>
	                            </div>
	                        </div>
	                    </div>
	                    <div class="span6">
	                        <div class="row-fluid">
	                            <div class="span4 showPopover"
	                                 data-original-title="<?php echo htmlspecialchars(JText::_('DOWNLOADS')); ?>"
	                                 data-content="<?php echo htmlspecialchars(JText::_('CLIENT_CAN_ACCESS_TIP')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('DOWNLOADS'); ?>
					                    </span>
	                            </div>
	                            <div class="span8">
							        <?php echo $lists['app_downloads']; ?>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	                <div class="row-fluid">
	                    <div class="span6">
	                        <div class="row-fluid">
	                            <div class="span4 showPopover"
	                                 data-original-title="<?php echo htmlspecialchars(JText::_('KNOWLEDGE_BASE')); ?>"
	                                 data-content="<?php echo htmlspecialchars(JText::_('CLIENT_CAN_ACCESS_TIP')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('KNOWLEDGE_BASE'); ?>
					                    </span>
	                            </div>
	                            <div class="span8">
							        <?php echo $lists['app_kb']; ?>
	                            </div>
	                        </div>
	                    </div>
	                    <div class="span6">
	                        <div class="row-fluid">
	                            <div class="span4 showPopover"
	                                 data-original-title="<?php echo htmlspecialchars(JText::_('WK_FAQ')); ?>"
	                                 data-content="<?php echo htmlspecialchars(JText::_('CLIENT_CAN_ACCESS_TIP')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('WK_FAQ'); ?>
					                    </span>
	                            </div>
	                            <div class="span8">
							        <?php echo $lists['app_faq']; ?>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	                <div class="row-fluid">
	                    <div class="span6">
	                        <div class="row-fluid">
	                            <div class="span4 showPopover"
	                                 data-original-title="<?php echo htmlspecialchars(JText::_('TICKETS')); ?>"
	                                 data-content="<?php echo htmlspecialchars(JText::_('CLIENT_CAN_ACCESS_TIP')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('TICKETS'); ?>
					                    </span>
	                            </div>
	                            <div class="span8">
							        <?php echo $lists['app_ticket']; ?>
	                            </div>
	                        </div>
	                    </div>
	                </div>
		        </div><?php
	        } ?>

            <div class="clr"></div>
        </div>

	    <div id="tab2" class="tab-pane equalheight">
			<?php if ($row->id): ?>
	        <table class="table table-striped table-bordered ontop">
	            <thead>
	            <tr>
	                <th class="valgmdl"><?php echo JText::_('name'); ?></th>
	                <th class="valgmdl"><?php echo JText::_('email'); ?></th>
	                <th class="valgmdl"><?php echo JText::_('phone'); ?></th>
	                <th class="valgmdl"><?php echo JText::_('fax'); ?></th>
	                <th class="valgmdl"><?php echo JText::_('mobile'); ?></th>
	                <th class="algcnt valgmdl" width="50"><?php echo JText::_('manager'); ?></th>
	                <th class="algcnt valgmdl">&nbsp;</th>
	            </tr>
	            </thead>
	            <tbody><?php
					for ($i = 0, $n = count($users); $i < $n; $i++) {
						$row_user = &$users[$i]; ?>
	                <tr>
	                    <td class="valgmdl"><?php echo $row_user->name . ' (' . $row_user->username . ')'; ?></td>
	                    <td class="valgmdl"><?php echo ($row_user->email != '' ? '<a href="mailto:' . $row_user->email . '">' . $row_user->email . '</a>' : ''); ?></td>
	                    <td class="valgmdl"><?php echo $row_user->phone; ?></td>
	                    <td class="valgmdl"><?php echo $row_user->fax; ?></td>
	                    <td class="valgmdl"><?php echo $row_user->mobile; ?></td>
	                    <td class="algcnt valgmdl">
	                        <a class="btn btn-<?php echo ($row_user->manager ? 'success' : 'danger');?>" href="index.php?option=com_maqmahelpdesk&task=client_manager&id_client=<?php echo $row->id; ?>&id_user=<?php echo $row_user->id; ?>&action=<?php echo ($row_user->manager ? 0 : 1); ?>"><i class="ico-<?php echo ($row_user->manager ? 'ok' : 'remove');?>-sign ico-white"></i></a>
	                    </td>
	                    <td class="algcnt valgmdl">
	                        <div class="btn-group">
	                            <a class="btn" href="#" onclick="$jMaQma('#task').val('users_edit');$jMaQma('#adminForm').append($jMaQma('<input/>', { type: 'hidden', name: 'cid[0]', value: <?php echo $row_user->id; ?>}));$jMaQma('#adminForm').submit();" title="<?php echo JText::_('edit'); ?>"><i class="ico-edit"></i> <?php echo JText::_('edit'); ?></a>
	                            <a class="btn btn-danger" href="index.php?option=com_maqmahelpdesk&task=client_deluser&id=<?php echo $row_user->id; ?>&id_client=<?php echo $row_user->id_client; ?>&<?php echo $formtoken;?>=1" title="<?php echo JText::_('delete'); ?>"><i class="ico-trash ico-white"></i> <?php echo JText::_('delete'); ?></a>
	                        </div>
	                    </td>
	                </tr><?php
					} ?>
					<?php if (!count($users)): ?>
	                <tr class="first">
	                    <td align="center" colspan="8" height="20"><img src="../components/com_maqmahelpdesk/images/info.png" align="absmiddle"/>&nbsp;<b><?php echo JText::_('no_users'); ?></b></td>
	                </tr>
						<?php endif;?>
	            <tfoot>
	            <tr>
	                <td colspan="8"><a class="btn btn-success" data-toggle="modal" href="#addUserModal"><?php echo JText::_('add');?></a></td>
	            </tr>
	            </tfoot>
	            </tbody>
	        </table>
			<?php else: ?>
	        <div align="center">
	            <img src="../components/com_maqmahelpdesk/images/info.png"
	                 align="absmiddle"/>&nbsp;<b><?php echo JText::_('client_create_users');?></b>
	        </div>
	        <br/>
			<?php endif;?>
	        <div class="clr"></div>
	    </div>
	    <div id="tab3" class="tab-pane equalheight">
			<?php if ($row->id): ?>
	        <table class="table table-striped table-bordered ontop">
	            <thead>
	            <tr>
	                <th class="valgmdl"><?php echo JText::_('number'); ?></th>
	                <th class="valgmdl"><?php echo JText::_('type'); ?></th>
	                <th class="valgmdl"><?php echo JText::_('maintainer'); ?></th>
	                <th class="algcnt valgmdl"><?php echo JText::_('dates'); ?></th>
	                <th class="algcnt valgmdl"><?php echo JText::_('unit'); ?></th>
	                <th class="algcnt valgmdl"><?php echo JText::_('value'); ?></th>
	                <th class="algcnt valgmdl"><?php echo JText::_('current'); ?></th>
	                <th class="algcnt valgmdl"><?php echo JText::_('status'); ?></th><?php
					if (count($cfields) > 0) {
						for ($x = 0; $x < count($cfields); $x++) {
							$cfield = $cfields[$x]; ?>
	                        <th class="valgmdl"><?php echo htmlspecialchars($cfield->caption); ?></th><?php
						}
					} ?>
	                <th class="algcnt valgmdl" width="50"><?php echo JText::_('components'); ?></th>
	                <th class="algcnt valgmdl">&nbsp;</th>
	            </tr>
	            </thead>
	            <tbody><?php
					for ($i = 0, $n = count($contracts); $i < $n; $i++)
					{
						$row_user = $contracts[$i];

						if ($row_user->unit != 'H' && $row_user->unit != 'T')
						{
							$start = strtotime($row_user->date_start);
							$current = strtotime(date("Y-m-d H:i:s")) - $start;
							$end = (strtotime($row_user->date_end) - $start);
							$percentage = 100 - (($current * 100) / $end);
						}
						else
						{
							$percentage = (($row_user->actual_value * 100) / $row_user->value);
						}

						if ($percentage < 0)
						{
							$percentage = 0;
						}

						$contract_components = '';
						$sql = "SELECT co.id, co.name, co.description
								FROM #__support_contract_comp c, #__support_components co
								WHERE c.id_component=co.id AND c.id_contract=" . $row_user->id;
						$database->setQuery($sql);
						$components = $database->loadObjectList();

						$components_desc = '';
						for ($ii = 0; $ii < count($components); $ii++)
						{
							$row_comp = $components[$ii];
							$components_desc .= ($components_desc != '' ? '<br />' : '');
							$components_desc .= '- ' . $row_comp->name;
							$contract_components = $contract_components . $row_comp->id . '|';
						}

						$contract_components = JString::substr($contract_components, 0, strlen($contract_components) - 1);
						$link = 'index.php?option=com_maqmahelpdesk&task=client_publishcontract&id=' . $row_user->id . '&id_client=' . $row_user->id_client . '&action=' . $row_user->status; ?>

		                <tr>
		                    <td><?php echo $row_user->contract_number; ?></td>
		                    <td><?php echo $row_user->contracttmpl; ?></td>
		                    <td><?php echo $row_user->name; ?><br/><?php echo $row_user->username; ?></td>
		                    <td class="valgmdl" nowrap="nowrap"><?php
								echo JText::_('creation') . ': ' . HelpdeskDate::DateOffset($supportConfig->dateonly_format,strtotime($row_user->creation_date)); ?><br /><?php
								echo JText::_('start') . ': ' . HelpdeskDate::DateOffset($supportConfig->dateonly_format,strtotime($row_user->date_start)); ?><br /><?php
								echo JText::_('end') . ': ' . HelpdeskDate::DateOffset($supportConfig->dateonly_format,strtotime($row_user->date_end)); ?>
		                    </td>
		                    <td class="algcnt valgmdl"><?php echo $row_user->unit; ?></td>
		                    <td class="algcnt valgmdl"><?php echo $row_user->value; ?></td>
		                    <td class="algcnt valgmdl">
								<?php echo ($row_user->unit != 'H' && $row_user->unit != 'T' ? number_format($percentage,0).'%' : HelpdeskDate::ConvertDecimalsToHoursMinutes($row_user->actual_value)); ?>
		                        <div class="progress">
		                            <div class="bar" style="width:<?php echo number_format($percentage,0);?>%;"></div>
		                        </div>
		                    </td>
		                    <td class="algcnt valgmdl">
		                        <a href="<?php echo $link; ?>"><?php echo ($row_user->status == 'I' ? JText::_('inactive') : JText::_('active')); ?></a>
		                    </td><?php
							$editcustom = "";
							if (count($cfields) > 0)
							{
								for ($x = 0; $x < count($cfields); $x++)
								{
									$cfield = $cfields[$x];
									$sql = "SELECT cf.value
												FROM #__support_contract_fields_values cf
												WHERE cf.id_contract='" . $row_user->id . "' AND cf.id_field='" . $cfield->id_field . "'
												LIMIT 1";
									$database->setQuery($sql);
									$contract_custom_field = $database->loadResult();
									$custom_value = ($contract_custom_field == '' ? '-' : $contract_custom_field);
									if (($cfield->ftype == 'radio') || ($cfield->ftype == 'checkbox')) {
										$editcustom .= ",'" . preg_replace('/[^A-Za-z0-9_]/', '_', $custom_value) . "'";
									} else {
										$editcustom .= ",'" . $custom_value . "'";
									} ?>
		                            <td class="valgmdl"><?php echo $custom_value; ?></td><?php
								}
							} ?>
		                    <td class="algcnt valgmdl" width="50">
								<?php echo ($components_desc != '' ? '<span class="editlinktip hasTip" title="' . htmlspecialchars(JText::_('components') . '::' . $components_desc) . '"><img src="../components/com_maqmahelpdesk/images/info.png" align="absmiddle" border="0" hspace="5" style="cursor:help; cursor:hand;" /></span>' : '-'); ?>
		                    </td>
		                    <td class="algcnt valgmdl" nowrap="nowrap">
		                        <div class="btn-group">
		                            <a class="btn" title="<?php echo JText::_('edit'); ?>" data-toggle="modal" href="#addContractModal" onclick="EditContract('<?php echo $row_user->id; ?>', '<?php echo $row_user->contract_number; ?>', '<?php echo $row_user->creation_date; ?>', '<?php echo $row_user->date_start; ?>', '<?php echo $row_user->date_end; ?>', '<?php echo $row_user->unit; ?>', '<?php echo $row_user->id_contract; ?>', '<?php echo $row_user->value; ?>', '<?php echo $row_user->actual_value; ?>', '<?php echo $row_user->status; ?>', '#hidden_remarks_<?php echo $row_user->id; ?>', '<?php echo $row_user->id_user; ?>', '<?php echo $contract_components; ?>'<?php echo $editcustom; ?>);"><i class="ico-edit"></i> <?php echo JText::_('edit'); ?></a>
		                            <a class="btn btn-danger" href="index.php?option=com_maqmahelpdesk&task=client_delcontract&id=<?php echo $row_user->id; ?>&id_client=<?php echo $row_user->id_client; ?>&<?php echo $formtoken;?>=1" title="<?php echo JText::_('delete'); ?>"><i class="ico-trash ico-white"></i> <?php echo JText::_('delete'); ?></a>
		                        </div>
			                    <textarea id="hidden_remarks_<?php echo $row_user->id; ?>" style="display:none;"><?php echo $row_user->remarks; ?></textarea>
		                    </td>
		                </tr><?php
					} ?>
					<?php if (!count($contracts)): ?>
	                <tr>
	                    <td align="center" colspan="<?php echo (count($cfields) + 11);?>" height="20"><img src="../components/com_maqmahelpdesk/images/info.png" align="absmiddle"/>&nbsp;<b><?php echo JText::_('no_contracts'); ?></b></td>
	                </tr>
						<?php endif;?>
	            <tfoot>
	            <tr>
	                <td colspan="<?php echo (count($cfields) + 11);?>"><a class="btn btn-success" data-toggle="modal" href="#addContractModal"><?php echo JText::_('add');?></a></td>
	            </tr>
	            </tfoot>
	            </tbody>
	        </table>
			<?php else: ?>
	        <div align="center">
	            <img src="../components/com_maqmahelpdesk/images/info.png" align="absmiddle"/>&nbsp;<b><?php echo JText::_('create_client_contract');?></b>
	        </div>
	        <br/>
			<?php endif;?>
	        <div class="clr"></div>
	    </div>
	    <div id="tab4" class="tab-pane equalheight">
	        <table class="table table-striped table-bordered ontop">
	            <thead>
	            <tr>
	                <th class="valgmdl"><?php echo JText::_('download'); ?></th>
	                <th class="valgmdl"><?php echo JText::_('serial_number'); ?></th>
	                <th class="algcnt valgmdl"><?php echo JText::_('start'); ?></th>
	                <th class="algcnt valgmdl"><?php echo JText::_('end'); ?></th>
	                <th class="algcnt valgmdl" width="50"><?php echo JText::_('status'); ?></th>
	                <th class="algcnt valgmdl">&nbsp;</th>
	            </tr>
	            </thead>
	            <tbody><?php
					for ($i = 0, $n = count($downloads); $i < $n; $i++) {
						$row_download = &$downloads[$i];
						$link = 'index.php?option=com_maqmahelpdesk&task=customer&cid=' . $row_download->id; ?>
	                <tr>
	                    <td><?php echo $row_download->cname . ' - ' . $row_download->pname; ?></td>
	                    <td><?php echo $row_download->serialno; ?></td>
	                    <td><?php echo HelpdeskDate::DateOffset($supportConfig->dateonly_format,strtotime($row_download->servicefrom)); ?></td>
	                    <td><?php echo HelpdeskDate::DateOffset($supportConfig->dateonly_format,strtotime($row_download->serviceuntil)); ?></td>
	                    <td>
							<?php echo ($row_download->isactive ? JText::_('active') : JText::_('inactive'));?>
	                    </td>
	                    <td>
	                        <a href="<?php echo $link; ?>" class="btn" title="<?php echo JText::_('edit'); ?>"><i class="ico-edit"></i> <?php echo JText::_('edit'); ?></a>
	                    </td>
	                </tr><?php
					} ?>
					<?php if (!count($downloads)): ?>
	                <tr>
	                    <td colspan="6"><img src="../components/com_maqmahelpdesk/images/info.png" align="absmiddle"/>&nbsp;<b><?php echo JText::_('no_downloads'); ?></b></td>
	                </tr>
						<?php endif;?>
	            </tbody>
	        </table>
	        <div class="clr"></div>
	    </div>
	    <div id="tab5" class="tab-pane equalheight">
	        <table class="table table-striped table-bordered ontop">
	            <thead>
	            <tr>
	                <th class="algcnt valgmdl"><?php echo JText::_('date_created'); ?></th>
	                <th class="valgmdl"><?php echo JText::_('description'); ?></th>
	                <th class="valgmdl"><?php echo JText::_('file'); ?></th>
	                <th class="algcnt valgmdl" width="50"><?php echo JText::_('PUBLIC'); ?></th>
	                <th class="algcnt valgmdl" width="50">&nbsp;</th>
	            </tr>
	            </thead>
	            <tbody><?php
					for ($i = 0, $n = count($clientdocs); $i < $n; $i++)
					{
						$row_user = &$clientdocs[$i];
						$link = 'index.php?option=com_maqmahelpdesk&task=client_download&id=' . $row_user->id; ?>
	                <tr>
	                    <td class="algcnt valgmdl"><?php echo HelpdeskDate::DateOffset($supportConfig->dateonly_format,strtotime($row_user->date_created)); ?></td>
	                    <td class="valgmdl"><?php echo $row_user->description; ?></td>
	                    <td class="valgmdl"><a href="<?php echo $link;?>" target="_blank"><?php echo $row_user->filename; ?></a></td>
	                    <td class="algcnt valgmdl">
	                        <span class="btn btn-<?php echo (!$row_user->available ? 'danger' : 'success');?>"><i class="ico-<?php echo ($row_user->available ? 'ok' : 'remove');?>-sign ico-white"></i></span>
	                    </td>
	                    <td class="algcnt valgmdl">
	                        <a class="btn btn-danger" href="index.php?option=com_maqmahelpdesk&task=client_delfile&id=<?php echo $row_user->id;?>&id_client=<?php echo $row_user->id_client;?>&filename=<?php echo $row_user->filename;?>&<?php echo $formtoken;?>=1" title="<?php echo JText::_('delete'); ?>"><i class="ico-trash ico-white"></i> <?php echo JText::_('delete'); ?></a>
	                    </td>
	                </tr><?php
					} ?>
					<?php if (!count($clientdocs)): ?>
	                <tr>
	                    <td colspan="5" height="20"><img src="../components/com_maqmahelpdesk/images/info.png" align="absmiddle"/>&nbsp;<b><?php echo JText::_('no_files'); ?></b></td>
	                </tr>
						<?php endif;?>
	            <tfoot>
	            <tr>
	                <td colspan="5"><a class="btn btn-success" data-toggle="modal" href="#addFileModal"><?php echo JText::_('add');?></a></td>
	            </tr>
	            </tfoot>
	            </tbody>
	        </table>
	        <div class="clr"></div>
	    </div>
	    <div id="tab6" class="tab-pane equalheight">
	        <table class="table table-striped table-bordered ontop">
	            <thead>
	            <tr>
	                <th class="algcnt valgmdl"><?php echo JText::_('date'); ?></th>
	                <th class="valgmdl"><?php echo JText::_('subject'); ?></th>
	                <th class="valgmdl"><?php echo JText::_('message'); ?></th>
	                <th class="algcnt valgmdl" width="50"><?php echo JText::_('published'); ?></th>
	                <th class="algcnt valgmdl"></th>
	            </tr>
	            </thead>
	            <tbody><?php
					for ($i = 0, $n = count($inforecords); $i < $n; $i++)
					{
						$row_info = &$inforecords[$i]; ?>
	                <tr>
	                    <td class="algcnt valgmdl"><?php echo HelpdeskDate::DateOffset($supportConfig->dateonly_format,strtotime($row_info->date)); ?></td>
	                    <td class="valgmdl"><?php echo $row_info->subject; ?></td>
	                    <td class="valgmdl"><?php echo nl2br($row_info->message); ?></td>
	                    <td class="algcnt valgmdl">
	                        <a class="btn btn-<?php echo ($row_info->published ? 'success' : 'danger');?>" href="index.php?option=com_maqmahelpdesk&task=client_publishinfo&id=<?php echo $row_info->id;?>&id_client=<?php echo $row_info->id_client;?>&action=<?php echo ($row_info->published ? 0 : 1);?>&<?php echo $formtoken;?>=1"><i class="ico-<?php echo ($row_info->published ? 'ok' : 'remove');?>-sign ico-white"></i></a>
	                    </td>
	                    <td class="algcnt valgmdl">
	                        <a class="btn btn-danger" href="index.php?option=com_maqmahelpdesk&task=client_delinfo&id=<?php echo $row_info->id;?>&id_client=<?php echo $row_info->id_client;?>&<?php echo $formtoken;?>=1" title="<?php echo JText::_('delete'); ?>"><i class="ico-trash ico-white"></i> <?php echo JText::_('delete'); ?></a>
	                    </td>
	                </tr><?php
					} ?>
					<?php if (!$row->id): ?>
	                <tr>
	                    <td colspan="5"><img src="../components/com_maqmahelpdesk/images/info.png" align="absmiddle"/>&nbsp;<b><?php echo JText::_('create_client_info'); ?></b></td>
	                </tr>
						<?php endif;?>
	            <tfoot>
	            <tr>
	                <td colspan="5"><a class="btn btn-success" data-toggle="modal" href="#addInfoModal"><?php echo JText::_('add');?></a></td>
	            </tr>
	            </tfoot>
	            </tbody>
	        </table>
	        <div class="clr"></div>
	    </div>
	    <div id="tab7" class="tab-pane equalheight">
	        <table class="table table-striped table-bordered ontop">
	            <thead>
	            <tr>
	                <th class="algcnt valgmdl" width="20">&nbsp;</th>
	                <th class="algcnt valgmdl"><?php echo JText::_('id'); ?></th>
	                <th class="valgmdl"><?php echo JText::_('subject'); ?></th>
	                <th class="valgmdl"><?php echo JText::_('workgroup'); ?></th>
	                <th class="valgmdl"><?php echo JText::_('user'); ?></th>
	                <th class="algcnt valgmdl"><?php echo JText::_('date'); ?></th>
	                <th class="algcnt valgmdl"><?php echo JText::_('status'); ?></th>
	                <th class="algcnt valgmdl" width="50"><?php echo JText::_('more'); ?></th>
	            </tr>
	            </thead>
	            <tbody><?php
					for ($i = 0, $n = count($tickets); $i < $n; $i++)
					{
						$row_user = &$tickets[$i];
						$link = '../index.php?option=com_maqmahelpdesk&id_workgroup=' . $row_user->id_workgroup . '&task=ticket_view&id=' . $row_user->id;

						// Get the source
						$source_desc = '';
						if ($row_user->source == "M") {
							$source_desc = JText::_('email');
						} elseif ($row_user->source == "F") {
							$source_desc = JText::_('fax');
						} elseif ($row_user->source == "O") {
							$source_desc = JText::_('other');
						} elseif ($row_user->source == "W") {
							$source_desc = JText::_('website');
						} elseif ($row_user->source == "P") {
							$source_desc = JText::_('phone');
						}

						//Get the number of messages
						$database->setQuery("SELECT COUNT(*) FROM #__support_ticket_resp WHERE id_ticket=" . $row_user->id);
						$num_msgs = 0;
						$num_msgs = $database->loadResult();

						// Get number of attachments
						$database->setQuery("SELECT COUNT(*) FROM #__support_file WHERE source='T' AND id='" . $row_user->id . "'");
						$attachs = $database->loadResult(); ?>
	                <tr>
	                    <td class="algcnt valgmdl"><?php echo ($attachs ? '<img src="../media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/attach.png" alt="' . JText::_('attachments') . ': ' . $attachs . '">' : ''); ?></td>
	                    <td class="algcnt valgmdl"><a href="<?php echo $link;?>" target="_blank"><?php echo $row_user->ticketmask; ?></a></td>
	                    <td class="valgmdl"><?php echo $row_user->subject; ?></td>
	                    <td class="valgmdl"><?php echo $row_user->wkdesc; ?></td>
	                    <td class="valgmdl"><?php echo $row_user->an_name; ?></td>
	                    <td class="algcnt valgmdl"><?php echo HelpdeskDate::DateOffset($supportConfig->date_short,strtotime($row_user->date)); ?></td>
	                    <td class="algcnt valgmdl"><?php echo $row_user->status; ?></td>
	                    <td class="algcnt valgmdl" width="50">
										<span class="editlinktip hasTip"
	                                          title="<?php echo htmlspecialchars(JText::_('more') . '::' . JText::_('id') . ': <b>' . $row_user->ticketmask . '</b><br />' . JText::_('date_updated') . ': <b>' . $row_user->last_update . '</b><br />' . JText::_('source') . ': <b>' . $source_desc . '</b><br />' . JText::_('messages') . ': <b>' . ($num_msgs + 1) . '</b>');?>"><img
	                                            src="../components/com_maqmahelpdesk/images/info.png" align="absmiddle" border="0"
	                                            hspace="5" style="cursor:help; cursor:hand;"/></span>
	                    </td>
	                </tr><?php
					} ?>
					<?php if (!count($tickets)): ?>
	                <tr class="first">
	                    <td align="center" colspan="8" height="20"><img
	                            src="../components/com_maqmahelpdesk/images/info.png"
	                            align="absmiddle"/>&nbsp;<b><?php echo JText::_('no_tickets'); ?></b></td>
	                </tr>
						<?php endif;?>
	            </tbody>
	        </table>
	        <div class="clr"></div>
	    </div>
	    </div>
	    </div>
    </form>
	    <div id="addUserModal" class="modal hide" style="width:500px;">
	        <form id="addUser" name="addUser" action="index.php" method="post" class="label-inline">
	            <div class="modal-header">
	                <a href="#" class="close" data-dismiss="modal">&times;</a>
	                <h3><?php echo JText::_('add_user');?></h3>
	            </div>
	            <div class="modal-body">
	                <div class="field w100">
						<span class="label" rel="tooltip"
	                          title="<?php echo htmlspecialchars(JText::_('user') . '::' . JText::_('add_user_tooltip')); ?>">
							<?php echo JText::_('user'); ?>
						</span>
	                    <input class="medium" type="text" id="ac_me" name="ac_me" value="" maxlength="100"/>
	                    <input type="hidden" id="id_user" name="id_user" value=""/>
	                </div>
	                <div class="field w100">
						<span class="label" rel="tooltip"
	                          title="<?php echo htmlspecialchars(JText::_('manager') . '::' . JText::_('manager_tooltip')); ?>">
							<?php echo JText::_('manager'); ?>
						</span>
	                    <div class="controlset-pad">
							<?php echo $lists['manager']; ?>
	                    </div>
	                </div>
	                <br class="clr" />
	            </div>
	            <div class="modal-footer">
	                <a href="#" class="btn" data-dismiss="modal"><?php echo JText::_('cancel');?></a>
	                <button type="submit" class="btn btn-success"><?php echo JText::_('save');?></button>
	            </div>
				<?php echo JHtml::_('form.token'); ?>
	            <input type="hidden" name="id_client" value="<?php echo $row->id; ?>"/>
	            <input type="hidden" name="task" value="client_saveuser"/>
	            <input type="hidden" name="id" value="0"/>
	            <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	        </form>
	    </div>

	    <div id="addContractModal" class="modal hide" style="width:500px;">
	        <form name="addContract" action="index.php" method="post" onsubmit="return ValidateForm();" class="label-inline">
	            <div class="modal-header">
	                <a href="#" class="close" data-dismiss="modal">&times;</a>
	                <h3><?php echo JText::_('add_contract');?></h3>
	            </div>
	            <div class="modal-body">
	                <div class="field w100">
						<span class="label" rel="tooltip"
	                          title="<?php echo htmlspecialchars(JText::_('contract')); ?>">
							<?php echo JText::_('contract'); ?>
						</span>
	                    <div class="controlset-pad">
							<?php echo $lists['contract']; ?>
	                    </div>
	                </div>
	                <div class="field w100">
						<span class="label" rel="tooltip"
	                          title="<?php echo htmlspecialchars(JText::_('maintainer')); ?>">
							<?php echo JText::_('maintainer'); ?>
						</span>
	                    <div class="controlset-pad">
							<?php echo $lists['staff']; ?>
	                    </div>
	                </div>
	                <div class="field w100">
						<span class="label" rel="tooltip"
	                          title="<?php echo htmlspecialchars(JText::_('start')); ?>">
							<?php echo JText::_('start'); ?>
						</span>
						<?php echo JHTML::Calendar('', 'date_start', 'date_start', '%Y-%m-%d', array('class' => '', 'size' => '12', 'maxlength' => '12')); ?>
	                </div>
	                <div class="field w100">
						<span class="label" rel="tooltip"
	                          title="<?php echo htmlspecialchars(JText::_('end')); ?>">
							<?php echo JText::_('end'); ?>
						</span>
						<?php echo JHTML::Calendar('', 'date_end', 'date_end', '%Y-%m-%d', array('class' => '', 'size' => '12', 'maxlength' => '12')); ?>
	                </div>
	                <div class="field w100">
						<span class="label" rel="tooltip"
	                          title="<?php echo htmlspecialchars(JText::_('number')); ?>">
							<?php echo JText::_('number'); ?>
						</span>
	                    <input class="medium" type="text" id="contract_number" name="contract_number" maxlength="200"/>
	                </div><?php
					if (count($cfields) > 0)
					{
						for ($x = 0; $x < count($cfields); $x++)
						{
							$cfield = $cfields[$x]; ?>
	                        <div class="field w100">
								<span class="label" rel="tooltip"
	                                  title="<?php echo htmlspecialchars($cfield->caption); ?>">
									<?php echo $cfield->caption; ?>
								</span><?php
								echo HelpdeskForm::WriteField(0, $cfield->id_field, $cfield->ftype, $cfield->value, $cfield->size, $cfield->maxlength, 0, 0, 0, 1);
								echo $cfield->required ? ' *' : ''; ?>
	                        </div><?php
						}
					} ?>
	                <div class="field w100" style="height:150px;">
	                    <label for="components">
							<span class="editlinktip hasTip"
	                              title="<?php echo htmlspecialchars(JText::_('components')); ?>">
								<?php echo JText::_('components'); ?>
							</span>
	                    </label>
	                    <div class="controlset-pad">
							<?php echo $lists['components']; ?>
	                    </div>
	                </div>
	                <div class="field w100" style="height:110px;">
	                    <label for="remarks">
							<span class="editlinktip hasTip"
	                              title="<?php echo htmlspecialchars(JText::_('remarks')); ?>">
								<?php echo JText::_('remarks'); ?>
							</span>
	                    </label>
	                    <textarea id="remarks" name="remarks" style="height:100px;" class="medium"></textarea>
	                </div>
	                <br class="clr" />
	            </div>
	            <div class="modal-footer">
	                <a href="#" class="btn" data-dismiss="modal"><?php echo JText::_('cancel');?></a>
	                <button type="submit" class="btn btn-success"><?php echo JText::_('save');?></button>
	            </div>
				<?php echo JHtml::_('form.token'); ?>
	            <input type="hidden" name="id_client" value="<?php echo $row->id; ?>"/>
	            <input type="hidden" name="task" value="client_savecontract"/>
	            <input type="hidden" name="components" value=""/>
	            <input type="hidden" name="actual_value" value=""/>
	            <input type="hidden" name="unit" value=""/>
	            <input type="hidden" name="value" value=""/>
	            <input type="hidden" name="creation_date" value=""/>
	            <input type="hidden" name="status" value="I"/>
	            <input type="hidden" name="contract_components" value=""/>
	            <input type="hidden" name="id" value="0"/>
	            <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	        </form>
	    </div>

	    <div id="addInfoModal" class="modal hide" style="width:500px;">
	        <form name="addInformation" action="index.php" method="post" class="label-inline">
	            <div class="modal-header">
	                <a href="#" class="close" data-dismiss="modal">&times;</a>
	                <h3><?php echo JText::_('add_info');?></h3>
	            </div>
	            <div class="modal-body">
	                <div class="field w100">
						<span class="label" rel="tooltip"
	                          title="<?php echo htmlspecialchars(JText::_('subject')); ?>">
							<?php echo JText::_('subject'); ?>
						</span>
	                    <input class="medium" type="text" id="subject" name="subject" value=""/>
	                </div>
	                <div class="field w100">
						<span class="label" rel="tooltip"
	                          title="<?php echo htmlspecialchars(JText::_('description')); ?>">
							<?php echo JText::_('description'); ?>
						</span>
	                    <textarea id="message" name="message" style="height:100px;width:230px;"></textarea>
	                </div>
	                <br class="clr" />
	            </div>
	            <div class="modal-footer">
	                <a href="#" class="btn" data-dismiss="modal"><?php echo JText::_('cancel');?></a>
	                <button type="submit" class="btn btn-success"><?php echo JText::_('save');?></button>
	            </div>
				<?php echo JHtml::_('form.token'); ?>
	            <input type="hidden" name="id_client" value="<?php echo $row->id;?>"/>
	            <input type="hidden" name="date" value="<?php echo HelpdeskDate::DateOffset("%Y-%m-%d");?>"/>
	            <input type="hidden" name="task" value="client_saveinfo"/>
	            <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	        </form>
	    </div>

	    <div id="addFileModal" class="modal hide" style="width:500px;">
	        <form id="addFiles" name="addFiles" action="index.php" method="post" enctype="multipart/form-data" onsubmit="ShowProgress();" class="label-inline">
	            <div class="modal-header">
	                <a href="#" class="close" data-dismiss="modal">&times;</a>
	                <h3><?php echo JText::_('add_file');?></h3>
	            </div>
	            <div class="modal-body">
	                <div class="field w100">
						<span class="label" rel="tooltip" title="<?php echo htmlspecialchars(JText::_('file')); ?>">
							<?php echo JText::_('file'); ?>
						</span>
	                    <input class="medium" type="file" id="file" name="file" value=""/>
	                </div>
	                <div class="field w100">
						<span class="label" rel="tooltip" title="<?php echo htmlspecialchars(JText::_('available')); ?>">
							<?php echo JText::_('available'); ?>
						</span>
	                    <div class="controlset-pad">
							<?php echo $lists['available']; ?>
	                    </div>
	                </div>
	                <div class="field w100" style="height:110px;">
	                    <label for="description">
							<span class="label" rel="tooltip" title="<?php echo htmlspecialchars(JText::_('description')); ?>">
								<?php echo JText::_('description'); ?>
							</span>
	                    </label>
	                    <textarea id="description" name="description" style="height:100px;width:230px;"></textarea>
	                </div>
	                <br class="clr" />
	            </div>
	            <div class="modal-footer">
	                <a href="#" class="btn" data-dismiss="modal"><?php echo JText::_('cancel');?></a>
	                <button type="submit" class="btn btn-success"><?php echo JText::_('save');?></button>
	            </div>
				<?php echo JHtml::_('form.token'); ?>
	            <input type="hidden" id="id_client" name="id_client" value="<?php echo $row->id;?>"/>
	            <input type="hidden" name="task" value="client_savefile"/>
	            <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	        </form>
	    </div>

	    <script type='text/javascript'>
	        function EditContract(id, contract_number, creation_date, date_start, date_end, unit, id_contract, value, actual_value, status, remarks, id_user, contract_components <?php echo $editCustomParam; ?>) {
	            document.addContract.contract_number.value = contract_number;
	            document.addContract.creation_date.value = creation_date;
	            document.addContract.date_start.value = date_start;
	            document.addContract.date_end.value = date_end;
	            document.addContract.unit.value = unit;
	            document.addContract.id_contract.value = id_contract;
	            document.addContract.value.value = value;
	            document.addContract.actual_value.value = actual_value;
	            document.addContract.status.value = status;
	            document.addContract.remarks.value = $jMaQma(remarks).val();
	            document.addContract.id_user.value = id_user;
	            document.addContract.id.value = id;
	            document.addContract.contract_components.value = contract_components;

	            FillComponents();<?php

				if (count($cfields) > 0)
				{
					for ($x = 0; $x < count($cfields); $x++)
					{
						$cfield = $cfields[$x];
						if (($cfield->ftype == 'radio') || ($cfield->ftype == 'checkbox'))
						{
							echo '$'."jMaQma('#custom" . $cfield->id_field . "').attr('checked', 'checked').parent().addClass('checked');";
						}
						else
						{
							echo '$'."jMaQma('#custom" . $cfield->id_field . "').val(custom" . $cfield->id_field . ");";
						}
					}
				} ?>
	        }

	        function ValidateForm()
	        {
	            var form = document.addContract;
	            if (form.id_contract.value == "0") {
	                alert("Error: Select Contract Type");
	                form.id_contract.focus();
	                return false;
	            } else if (form.id_user.value == "0") {
	                alert("Error: Select Contract Maintainner");
	                form.id_user.focus();
	                return false;
	            } else if (form.date_start.value == "") {
	                alert("Error: Select Contract date start");
	                form.date_start.focus();
	                return false;
	            } else if (form.date_end.value == "") {
	                alert("Error: Select Contract date end");
	                form.date_end.focus();
	                return false;
					<?php
					for ($x = 0; $x < count($cfields); $x++) {
						$cfield = $cfields[$x];
						if ($cfield->required) {
							echo '} else if (form.custom' . $cfield->id_field . '.value == "" ) {' . "\n";
							echo 'alert( "' . JText::_('cfield_required') . ' ' . $cfield->caption . '." );' . "\n";
							echo 'return false;';
						}
					} ?>
	            }

	            return true;
	        }

	        $jMaQma(document).ready(function () {
	            $jMaQma('.showPopover').popover({'html':true, 'trigger':'hover'});
	        });
	    </script><?php
	}
}
