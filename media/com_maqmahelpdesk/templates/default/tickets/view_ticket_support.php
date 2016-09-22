<div class="maqmahelpdesk container-fluid">

	<h2><?php echo $row->subject; ?></h2>

	<?php if (isset($client_details) && $client_details->approval && !$row->approved): ?>
	<div class="alert alert-error">
	    <p><?php echo JText::_('approve_pending');?></p>
	</div>
    <?php endif;?>

	<?php if ($row->internal): ?>
	<div class="alert alert-warning">
	    <p><i class="ico-lock"></i> <?php echo JText::_('INTERNAL_TICKET');?></p>
	</div>
    <?php endif;?>

    <div class="btn-group flrg">
        <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
            <?php echo JText::_("ACTIONS");?>
            <span class="caret"></span>
        </a>
        <ul class="dropdown-menu pull-right">
	        <li><a href="<?php echo JRoute::_(JURI::root() . 'index.php?option=com_maqmahelpdesk&task=pdf_ticket&id=' . $row->id . '&format=raw');?>"
	           target="_blank" title="<?php echo JText::_('pdf_version');?>"><i class="ico-print"></i> <?php echo JText::_('pdf_version');?></a></li>
	        <?php if ($workgroupSettings->use_bookmarks): ?>
            <li><a href="<?php echo JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_bookmark&id=' . $row->id);?>"><i class="ico-star"></i> <?php echo JText::_('add_to_bookmarks');?></a></li>
	        <?php endif;?>
            <li><a href="javascript:;" onclick="javascript:$jMaQma('#workgroup_change').toggle();"><i class="ico-refresh"></i> <?php echo JText::_('change_workgroup');?></a></li>
	        <?php if ($supportConfig->use_merge): ?>
            <li><a href="#ticket_merge" data-toggle="modal" title="<?php echo JText::_('merge_title');?>"><i class="ico-resize-small"></i> <?php echo JText::_('merge');?></a></li>
	        <?php endif;?>
	        <?php if ($supportConfig->use_as_reply): ?>
            <li><a href="#ticket_as_reply" data-toggle="modal" title="<?php echo JText::_('as_reply_title');?>"><i class="ico-comment"></i> <?php echo JText::_('as_reply');?></a></li>
	        <?php endif;?>
	        <?php if ($supportConfig->use_parent): ?>
            <li><a href="#ticket_parent" data-toggle="modal" title="<?php echo JText::_('set_parent');?>"><i class="ico-arrow-up"></i> <?php echo JText::_('set_parent');?></a></li>
	        <?php endif;?>
	        <?php if ($workgroupSettings->wkkb): ?>
            <li><a href="<?php echo JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=kb_convert&id=0&ticket=' . $row->id);?>"
	           title="<?php echo JText::_('convert_to_kb');?>"><i class="ico-random"></i> <?php echo JText::_('convert');?></a></li>
	        <?php endif;?>
            <li><a href="<?php echo JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_duplicate&id=' . $row->id);?>" title="<?php echo JText::_('duplicate');?>"><i class="ico-plus"></i> <?php echo JText::_('duplicate');?></a>
            <li class="divider"></li>
	        <li><a href="javascript:;" onclick="javascript:deleteTicket();"><i class="ico-trash"></i> <?php echo JText::_('delete_ticket');?></a></li>
		</ul>
    </div>

	<p>&nbsp;</p>

	<div id="ticket_merge" style="display:none;width:350px;" class="modal fade">
	    <div class="modal-header">
	        <a class="close" data-dismiss="modal">&times;</a>
	        <h3><?php echo JText::_('merge_title'); ?></h3>
	    </div>
	    <div class="modal-body">
	        <div>
	            <p><?php echo JText::_('merge_desc');?> <input type="text" id="merge_number" name="merge_number"
	                                                           style="font-size:16px;color:#333;font-weight:bold;text-align:center;width:120px;height:22px;"/>
	        </div>
	    </div>
	    <div class="modal-footer">
	        <a href="javascript:;" onclick="mergeTicket();" class="btn btn-success"><?php echo JText::_('merge');?></a>
	        <a href="javascript:;" onclick="$jMaQma('#ticket_merge').modal('hide');" data-dismiss="modal"
	           class="btn"><?php echo JText::_('close');?></a>
	    </div>
	</div>

	<div id="ticket_as_reply" style="display:none;width:350px;" class="modal fade">
	    <div class="modal-header">
	        <a class="close" data-dismiss="modal">&times;</a>

	        <h3><?php echo JText::_('as_reply_title'); ?></h3>
	    </div>
	    <div class="modal-body">
	        <div>
	            <p><?php echo JText::_('ticket_as_reply_desc');?> <input type="text" id="ticket_as_reply_number"
	                                                                     name="ticket_as_reply_number"
	                                                                     style="font-size:16px;color:#333;font-weight:bold;text-align:center;width:120px;height:22px;"/>
	        </div>
	    </div>
	    <div class="modal-footer">
	        <a href="javascript:;" onclick="asReplyTicket();" class="btn btn-success"><?php echo JText::_('add');?></a>
	        <a href="javascript:;" onclick="$jMaQma('#ticket_as_reply').modal('hide');" data-dismiss="modal"
	           class="btn"><?php echo JText::_('close');?></a>
	    </div>
	</div>

	<div id="ticket_parent" style="display:none;width:350px;" class="modal fade">
	    <div class="modal-header">
	        <a class="close" data-dismiss="modal">&times;</a>

	        <h3><?php echo JText::_('setparent_title'); ?></h3>
	    </div>
	    <div class="modal-body">
	        <div>
	            <p><?php echo JText::_('parent_desc');?> <input type="text" id="parent_ticket" name="parent_ticket"
	                                                            style="font-size:16px;color:#333;font-weight:bold;text-align:center;width:120px;height:22px;"/>
	        </div>
	    </div>
	    <div class="modal-footer">
	        <a href="javascript:;" onclick="setParentTicket();"
	           class="btn btn-success"><?php echo JText::_('set_parent');?></a>
	        <a href="javascript:;" onclick="$jMaQma('#ticket_parent').modal('hide');" data-dismiss="modal"
	           class="btn"><?php echo JText::_('close');?></a>
	    </div>
	</div>

	<div>
	    <div style="float:left;">
	        <span class="lbl lbl-<?php echo $status_color;?>"
	              style="font-size:20px;font-weight:bold;">#<?php echo $row->ticketmask;?></span>
	    </div>
	    <div id="rating"></div>
	    <div style="float:right"><em><?php echo JText::_('date');?>:
	        <b><?php echo HelpdeskDate::DateOffset($supportConfig->date_long, strtotime($row->date));?></b></em></div>
	</div>
	<div class="clear"></div>

	<p>&nbsp;</p>

	<form id="adminForm" name="adminForm" action="<?php echo JRoute::_("index.php");?>" method="post"
	      enctype="multipart/form-data" class="form-horizontal">
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="clientrate" value="<?php echo $clientvalue;?>"/>
	<input type="hidden" name="option" value="com_maqmahelpdesk"/>
	<input type="hidden" id="Itemid" name="Itemid" value="<?php echo $Itemid;?>"/>
	<input type="hidden" name="id_workgroup" id="id_workgroup" value="<?php echo $id_workgroup;?>"/>
	<input type="hidden" name="id_directory" id="id_directory" value="<?php echo $row->id_directory;?>"/>
	<input type="hidden" name="id" id="id" value="<?php echo $row->id;?>"/>
	<input type="hidden" name="id_user" id="id_user" value="<?php echo $row->id_user;?>"/>
	<input type="hidden" name="id_client" id="id_client" value="<?php echo $row->id_client;?>"/>
	<input type="hidden" name="old_client" id="id_client" value="<?php echo $row->id_client;?>"/>
	<input type="hidden" name="last_status" value="<?php echo $old_status;?>"/>
	<input type="hidden" name="old_assign" value="<?php echo $row->assign_to;?>"/>
	<input type="hidden" name="old_status" value="<?php echo $row->id_status;?>"/>
	<input type="hidden" name="old_priority" value="<?php echo $row->id_priority;?>"/>
	<input type="hidden" name="old_duedate_date" value="<?php echo $old_duedate_date;?>"/>
	<input type="hidden" name="old_duedate_hour" value="<?php echo $old_duedate_hour;?>"/>
	<input type="hidden" name="old_id_category" value="<?php echo $row->id_category;?>"/>
	<input type="hidden" id="ticketmask" name="ticketmask" value="<?php echo $row->ticketmask;?>"/>
	<input type="hidden" id="an_name" name="an_name" value="<?php echo $row->an_name;?>"/>
	<input type="hidden" id="an_mail" name="an_mail" value="<?php echo $row->an_mail;?>"/>
	<input type="hidden" name="is_editreplied" value="0"/>
	<input type="hidden" name="originalmsg" value="0"/>
	<input type="hidden" name="queue" value="0"/>
    <input type="hidden" name="now_date" value=""/>
	<input type="hidden" id="order" name="order" value="<?php echo $order;?>"/>
	<input type="hidden" id="orderby" name="orderby" value="<?php echo $orderby;?>"/>
	<input type="hidden" id="page" name="page" value="<?php echo $page;?>"/>
	<input type="hidden" id="user_is_valid" name="user_is_valid" value="0"/>

    <div class="control-group row-fluid">
        <label class="control-label"><?php echo JText::_('workgroup');?></label>
        <div class="controls" style="padding-top:5px;">
	        <?php echo HelpdeskDepartment::GetName($row->id_workgroup);?>
        </div>
    </div>
    <div id="workgroup_change" name="workgroup_change"
         style="display:none;background:#ffffcc;padding:1px;margin-left:25px;">
        <table cellpadding="5" cellspacing="0">
            <tr>
                <td valign="top"><p><b><?php echo JText::_('wk_select');?></b></p></td>
                <td valign="top"><?php echo $wkchange_html;?></td>
            </tr>
        </table>
    </div>

	<div class="control-group row-fluid">
		<label class="control-label" for="internal"><?php echo JText::_('INTERNAL_TICKET');?> <span class="required">*</span></label>
		<div class="controls">
			<?php echo $lists['internal'];?>
		</div>
	</div>
    <div class="control-group row-fluid">
        <label class="control-label" for="searchclient"><?php echo JText::_('client_name');?></label>
        <div class="controls">
	        <span id="newclientname"><?php echo HelpdeskClient::GetName($row->id_user);?></span>
	        <input type="text" id="searchclient" name="searchclient" />
            <a id="cancelSearchClientBtn" href="javascript:;" class="btn"><i class="ico-remove-sign"></i></a>
            <div class="btn-group"><?php
		        if (isset($client_details) && $client_details->id_client)
		        { ?>
	                <a id="openclient"
	                   href="<?php echo JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=client_view&id=' . $client_details->id_client);?>"
	                   target="_blank" class="btn btn-mini"><i class="ico-eye-open"></i></a><?php
		        } ?>
	            <a id="searchClientBtn" href="javascript:;" class="btn btn-mini"><i class="ico-search"></i></a>
	            <a id="addClientBtn" href="javascript:;" class="btn btn-success btn-mini"><i class="ico-plus-sign ico-white"></i></a>
            </div>
        </div>
    </div>
	<div id="addclient" class="popin">
        <div class="control-group row-fluid">
            <label class="control-label" for="clientname"><?php echo JText::_('name');?></label>
            <div class="controls">
                <input type="text" id="clientname" name="clientname" value="">
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="clientaddress"><?php echo JText::_('address');?></label>
            <div class="controls">
                <input type="text" id="clientaddress" name="clientaddress" value="">
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="clientcity"><?php echo JText::_('city');?></label>
            <div class="controls">
                <input type="text" id="clientcity" name="clientcity" value="">
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="clientzip"><?php echo JText::_('zipcode');?></label>
            <div class="controls">
                <input type="text" id="clientzip" name="clientzip" value="">
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="clientphone"><?php echo JText::_('phone');?></label>
            <div class="controls">
                <input type="text" id="clientphone" name="clientphone" value="">
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="clientwebsite"><?php echo JText::_('website');?></label>
            <div class="controls">
                <input type="text" id="clientwebsite" name="clientwebsite" value="">
            </div>
        </div>
        <div class="form-actions" style="margin-left:-10px;margin-right:-10px;margin-bottom:-21px;">
            <button id="cancelAddClientBtn" type="button" class="btn"><?php echo JText::_('cancel');?></button>
        </div>
	</div>

    <div class="control-group row-fluid">
        <label class="control-label"><?php echo JText::_('user');?></label>
        <div class="controls" style="padding-top:5px;"><?php
	        echo $row->an_name;
	        if ($row->id_user)
	        { ?>
                <div class="btn-group">
			        <a href="<?php echo JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=users_getuserdetails&id=' . $row->id_user);?>"
	                   target="_blank"class="btn btn-mini"><i class="ico-user"></i></a><?php

			        // JomSocial integration with link to the user and online status
			        $jspath = JPATH_ROOT . DS . 'components' . DS . 'com_community';
			        if (file_exists($jspath . DS . 'libraries' . DS . 'core.php'))
			        {
				        include_once($jspath . DS . 'libraries' . DS . 'core.php');
				        $jsuser = CFactory::getUser($row->id_user);
				        $link = CRoute::_('index.php?option=com_community&view=profile&userid=' . $row->id_user);
				        echo '<a href="' . $link . '" target="_blank" class="btn btn-mini js_status" title="' . JText::_('PROFILE') . '"><img src="' . JURI::root() . '/media/com_maqmahelpdesk/images/integrations/jomsocial.png" width="14" height="14" alt="" border="0" /></a> ';
			        }

			        // JBolo integration
			        if (HelpdeskUser::IsOnline($row->id_user) && $row->id && $supportConfig->integrate_jbolo)
			        { ?>
	                    <a style="text-decoration:none;" href="javascript:;"
	                       onclick="javascript:chatFromTicket(<?php echo $row->id_user;?>,'<?php echo $row->ticketmask;?>')"
	                       title="<?php echo JText::_('REQUEST_CHAT');?>" class="btn btn-success btn-mini"><i class="ico-comment ico-white"></i></a><?php
			        } ?>
		        </div><?php
	        } else { ?>
                &mdash;
                <span class="lbl lbl-important"><?php echo JText::_('anonymous');?></span>
                <a id="addUserBtn" href="javascript:;" onclick="$jMaQma('#adduser').show();" class="btn btn-success btn-mini"><i class="ico-plus-sign ico-white"></i></a><?php
	        } ?>
        </div>
    </div>
	<div id="adduser" class="popin">
	    <div class="control-group row-fluid">
            <label class="control-label" for="username"><?php echo JText::_('name');?></label>
            <div class="controls">
                <input type="text" id="username" name="username" value="">
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="usermail"><?php echo JText::_('email');?></label>
            <div class="controls">
                <input type="text" id="usermail" name="usermail" value="">
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="userpassword"><?php echo JText::_('password');?></label>
            <div class="controls">
                <input type="text" id="userpassword" name="userpassword" value="">
            </div>
        </div>
        <div class="form-actions" style="margin-left:-10px;margin-right:-10px;margin-bottom:-21px;">
            <button id="cancelAddUserBtn" type="button" class="btn"><?php echo JText::_('cancel');?></button>
        </div>
	</div>
    <div class="control-group row-fluid">
        <label class="control-label" for="id_status"><?php echo JText::_('status');?></label>
        <div class="controls">
			<?php echo $status;?>
        </div>
    </div>
    <div class="control-group row-fluid">
        <label class="control-label" for="assign_to"><?php echo JText::_('assignto');?></label>
        <div class="controls">
			<?php echo $assign;?>
        </div>
    </div>
    <div class="control-group row-fluid">
        <label class="control-label" for="duedate_date"><?php echo JText::_('duedate');?></label>
        <div class="controls">
			<?php echo JHTML::Calendar($duedate_date, 'duedate_date', 'duedate_date', '%Y-%m-%d', array('style' => 'width:100px;', 'maxlength' => '10')); ?>
            <input type="text" name="duedate_hours" id="duedate_hours" class="timepicker" style="width:50px;" maxlength="5" value="<?php echo $duedate_hour; ?>"/>
        </div>
    </div>

	<?php if (is_object($directory) && ($supportConfig->integrate_mtree || $supportConfig->integrate_sobi)): ?>
    <div class="control-group row-fluid">
        <label class="control-label"><?php echo JText::_('listing');?></label>
        <div class="controls" style="padding-top:5px;">
	        <?php echo $directory->directory_name;?>
        </div>
    </div>
    <?php endif;?>

	<div id="ExtraDetailsShow" name="ExtraDetailsShow" style="text-align:right;"><a
	    href="javascript:ShowDetails();"><em><?php echo JText::_('showmore');?></em> <img
	    src="<?php echo JURI::root();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/9px/plus.png"
	    border="0" align="absmiddle"></a>
	</div>
	<div id="ExtraDetails" name="ExtraDetails" style="display:none;">
        <div class="control-group row-fluid">
            <label class="control-label" for="id_priority"><?php echo JText::_('priority');?></label>
            <div class="controls">
				<?php echo $priority;?>
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="id_category"><?php echo JText::_('category');?></label>
            <div class="controls">
				<?php echo $category;?>
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label"><?php echo JText::_('last_status');?></label>
            <div class="controls" style="padding-top:5px;">
				<?php echo $old_status;?>
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label"><?php echo JText::_('source');?></label>
            <div class="controls" style="padding-top:5px;">
				<?php echo $source_desc;?>
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label"><?php echo JText::_('ipaddress');?></label>
            <div class="controls" style="padding-top:5px;">
				<?php echo $row->ipaddress;?>
            </div>
        </div><?php
		if (count($customfields))
		{
			$section = '';
			foreach ($cfields_rows as $rowloop)
			{
				if ($section != $rowloop['section'])
				{
					$section = $rowloop['section']; ?>
                    <div class="span12 issection cfieldsection-<?php echo JFilterOutput::stringURLSafe($rowloop['section']);?>">
                        <label class="control-label" style="font-size:120%;padding:5px 10px;"><?php echo $rowloop['section'];?></label>
                    </div><?php
				}
				$cfclass = '';
				if ($rowloop['id_category'] == '') {
					$cfclass = ' cat0';
				} else {
					$cfclasses = explode(",", $rowloop['id_category']);
					foreach ($cfclasses as $cfclassid) {
						$cfclass .= ' cat' . $cfclassid;
					}
				} ?>
                <div id="cf<?php echo $rowloop['id'];?>" class="control-group row-fluid <?php echo ($rowloop['ftype'] == 'note' ? 'note' : 'field');?> cfield<?php echo $cfclass;?> cfieldsection-<?php echo JFilterOutput::stringURLSafe($rowloop['section']);?>">
	                <?php if ($rowloop['ftype'] != 'note') : ?>
	                <label class="control-label" for="custom<?php echo $rowloop['id'];?>">
						<?php echo $rowloop['caption'];?>
						<?php if ($rowloop['required']): ?> <span class="required">*</span><?php endif;?>
                    </label>
					<?php endif; ?>
                    <div class="controls">
						<?php echo $rowloop['field'];?>
						<?php if ($rowloop['tooltip']!=''):?>
                        <span class="help-block"><?php echo $rowloop['tooltip'];?></span>
						<?php endif;?>
                    </div>
                </div><?php
			}
		} ?>

	    <div id="ExtraDetailsHide" name="ExtraDetailsHide" style="text-align:right;"><a
	        href="javascript:HideDetails();"><em><?php echo JText::_('hidedetails');?></em> <img
	        src="<?php echo JURI::root();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/9px/minus.png"
	        border="0" align="absmiddle"></a></div>
	</div>

	<ul id="tab" class="nav nav-tabs">
	    <li class="active"><a href="#messages"
	                          data-toggle="tab"><?php echo '<img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/comments.png" align="absmiddle" /> ' . JText::_('activity_history'); ?></a>
	    </li>
	    <li><a href="#attachments" onclick="FileNotify(<?php echo $Itemid;?>);"
	           data-toggle="tab"><?php echo '<img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/attach.png" align="absmiddle" /> ' . JText::_('attachments') . ' <span class="badge">' . $count_ticketAttachs . '</span>';?></a>
	    </li>
	    <li><a href="#logs"
	           data-toggle="tab"><?php echo '<img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/table.png" align="absmiddle" /> ' . JText::_('activity_logs');?></a>
	    </li>
	    <?php if (count($related_tickets) || count($related_kb) || count($related_discussions)): ?>
	    <li class="dropdown">
	        <a href="javascript:;" class="dropdown-toggle"
	           data-toggle="dropdown"><?php echo JText::_('possible_relations');?> <b class="caret"></b></a>
	        <ul class="dropdown-menu">
	            <li><a href="#related_tickets"
	                   data-toggle="tab"><?php echo '<img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/tickets.png" align="absmiddle" /> ' . JText::_('tickets') . ' <span class="badge">' . count($related_tickets) . '</span>';?></a>
	            </li>
	            <li><a href="#related_kb"
	                   data-toggle="tab"><?php echo '<img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/kb.png" align="absmiddle" /> ' . JText::_('kb') . ' <span class="badge">' . count($related_kb) . '</span>';?></a>
	            </li>
	            <li><a href="#related_discussions"
	                   data-toggle="tab"><?php echo '<img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/replies.png" align="absmiddle" /> ' . JText::_('discussions') . ' <span class="badge">' . count($related_discussions) . '</span>';?></a>
	            </li>
	        </ul>
	    </li>
	    <?php endif;?>
	    <li class="dropdown">
	        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"><?php echo JText::_('more');?> <b
	            class="caret"></b></a>
	        <ul class="dropdown-menu">
	            <li><a href="#tasks"
	                   data-toggle="tab"><?php echo '<img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/calendar.png" align="absmiddle" /> ' . JText::_('tasks') . ' <span class="badge">' . count($ticketTasks) . '</span>';?></a>
	            </li>
	            <li><a href="#times"
	                   data-toggle="tab"><?php echo '<img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/time.png" align="absmiddle" /> ' . JText::_('timesheet');?></a>
	            </li>
	            <?php if ($supportConfig->bbb_url != '' && $supportConfig->bbb_apikey): ?>
	            <li><a href="#meetings"
	                   data-toggle="tab"><?php echo '<img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/meetings.png" align="absmiddle" /> ' . JText::_('meetings');?></a>
	            </li>
	            <?php endif;?>
	            <?php if ($supportConfig->integrate_mtree): ?>
	            <li><a href="#mtree"
	                   data-toggle="tab"><?php echo '<img src="' . JURI::root() . 'components/com_mtree/img/icon-16-mosetstree.png" align="absmiddle" /> MosetsTree <span class="badge">' . count($mtree_links) . '</span>';?></a>
	            </li>
	            <?php endif;?>
	            <?php if ($supportConfig->integrate_sobi): ?>
	            <li><a href="#sobipro"
	                   data-toggle="tab"><?php echo '<img src="' . JURI::root() . 'media/sobipro/SobiPro_16.png" align="absmiddle" /> SobiPro <span class="badge">' . count($sobipro_links) . '</span>';?></a>
	            </li>
	            <?php endif;?>
	            <?php if ($supportConfig->integrate_artofuser): ?>
	            <li><a href="#artofuser"
	                   data-toggle="tab"><?php echo '<img src="' . JURI::root() . 'administrator/components/com_artofuser/media/images/artofuser_16x16.png" align="absmiddle" /> ArtOfUser <span class="badge">' . count($artofuser_notes) . '</span>';?></a>
	            </li>
	            <?php endif;?>
	        </ul>
	    </li>
	</ul>

	<div id="my-tab-content" class="tab-content">
		<div class="tab-pane fade in active" id="messages">
	        <div class="post-box-wrapper">
		        <div class="post-box">
			        <ol class="messagelist"><?php
					foreach ($activities_rows as $rowloop):?>
				        <li>
				            <div class="message-body">
				                <img alt="" src="<?php echo $rowloop['avatar'];?>" class="message-avatar hidden-mobile" height="60" width="60">
				                <div class="message-arrow hidden-mobile"></div>
				                <div class="message-box <?php echo ($rowloop['msgtype'] != 'message' ? 'note' : '');?>">
				                    <div class="message-author">
				                        <strong><?php echo $rowloop['user'];?></strong>
				                        <small><?php echo HelpdeskDate::LongDate($rowloop['date']);?></small>
				                    </div>
				                    <div class="message-text"><?php
										echo ($rowloop['reply_summary'] != '' ? '<p><span style="color:#666;font-size:11px;">' . JText::_('summary') . ':</span> ' . $rowloop['reply_summary'] . '</p>' : '');
										echo $rowloop['message'];
					                    if ($rowloop['msgtype']=='message')
					                    {
						                    $reply_attachs = HelpdeskTicket::GetMessageAttachments($row->id, $rowloop["id"]);
						                    for ($c=0; $c<count($reply_attachs); $c++)
						                    {
							                    $reply_attach = $reply_attachs[$c];
							                    $link = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_download&id=' . $reply_attach->id_file . '&extid=' . $reply_attach->id);?>
	                                            <p><img src="<?php echo JURI::root();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/attach.png" align="left" alt="" /> <a href="<?php echo $link;?>"><?php echo $reply_attach->filename;?></a></p><?php
						                    }
					                    } ?>
				                    </div>
				                </div>
				            </div>
				        </li><?php
					endforeach; ?>
                    </ol>
                </div>
            </div>

		    <p align="right">
		        <a href="javascript:;" onclick="AddNote();" title="<?php echo JText::_('add_note');?>" class="btn"><i class="ico-comment"></i> <?php echo JText::_('add_note');?></a>
		    </p>

		    <h3><img
		        src="<?php echo JURI::root();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/replies.png"
		        align="absmiddle" border="0" alt=""/> <?php echo JText::_('add_reply');?> <small class="pull-right" style="margin-top:15px;"><span class="required">*</span> <?php echo JText::_('field_required_desc');?></small></h3>

		    <div id="AddReply" name="AddReply">
	            <div class="control-group row-fluid">
	                <label class="control-label" for="reply_date"><?php echo JText::_('tmpl_msg27');?></label>
	                <div class="controls">
					    <?php echo JHTML::Calendar('', 'reply_date', 'reply_date', '%Y-%m-%d', array('maxlength' => '10', 'style' => 'width:100px;')); ;?>
	                    <input type="text" class="timepicker" id="reply_hours" name="reply_hours" maxlength="5" value=""/>
	                    <span class="help-block"><?php echo JText::_("SET_ACTIVITY_DATE");?></span>
	                </div>
	            </div>
	            <div class="control-group row-fluid">
	                <label class="control-label" for="reply_summary"><?php echo JText::_('summary');?></label>
	                <div class="controls">
	                    <input type="text" id="reply_summary" name="reply_summary" value="" maxlength="150">
	                </div>
	            </div>
	            <div class="control-group row-fluid">
	                <label class="control-label" for="reply"><?php echo JText::_('activity');?></label>
	                <div class="controls">
		                <?php if($supportConfig->editor == 'builtin'):?>
                        <textarea id="reply" name="reply" class="redactor_agent"></textarea>
		                <?php else:?>
		                <?php echo $editor->display('reply', '', '100%', '400', '75', '20');?>
		                <?php endif;?>
	                </div>
	            </div>
			    <?php if ($workgroupSettings->use_activity):?>
	            <div class="control-group row-fluid">
	                <label class="control-label"></label>
	                <div class="control span4">
					    <?php echo JText::_('tmpl_msg12');?>:<br/><?php echo $lists['activity_type'];?>
	                </div>
	                <div class="control span4">
					    <?php echo JText::_('tmpl_msg13');?>:<br/><?php echo $lists['activity_rate'];?>
	                </div>
	            </div>
	            <div class="control-group row-fluid">
	                <label class="control-label"></label>
	                <div class="control span2">
					    <?php echo JText::_('tmpl_msg14');?>:<br/>
	                    <input type="text" class="timepicker" id="start_time" name="start_time" maxlength="5" value="08:00" onchange="GetLabourTime();" onblur="GetLabourTime();"/>
	                </div>
	                <div class="control span2">
					    <?php echo JText::_('tmpl_msg15');?>:<br/>
	                    <input type="text" class="timepicker" id="end_time" name="end_time" maxlength="5" value="08:00" onchange="GetLabourTime();" onblur="GetLabourTime();" />
	                </div>
	                <div class="control span2">
					    <?php echo JText::_('tmpl_msg16');?>:<br/>
	                    <input type="text" class="timepicker" id="break_time" name="break_time" maxlength="5" value="00:00" onchange="GetLabourTime();" onblur="GetLabourTime();" />
	                </div>
	                <div class="control span3">
					    <?php echo JText::_('tmpl_msg17');?>:<br/>
	                    <input style="width:100px;" style="text-align:center;" type="text" value="" name="replytime" readonly />
	                </div>
	            </div>
			    <?php if ($supportConfig->use_travel): ?>
	                <div class="control-group row-fluid">
	                    <label class="control-label"></label>
	                    <div class="control span3">
						    <?php echo JText::_('tmpl_msg20');?>:<br/>
	                        <input type="text" class="timepicker" id="tickettravel" name="tickettravel" maxlength="5" value="<?php echo $clienttravel;?>" />
	                    </div>
	                    <div class="control span3">
						    <?php echo JText::_('tmpl_msg30');?>:<br/><?php echo $lists['travel'];?>
	                    </div>
	                </div>
			    <?php else: ?>
	                <input type="hidden" id="tickettravel" name="tickettravel" value="0"/>
			    <?php endif; ?>
		    <?php else: ?>
	            <input type="hidden" name="id_activity_type" value="0"/>
	            <input type="hidden" name="id_activity_rate" value="0"/>
	            <input type="hidden" name="start_time" id="start_time" value="0"/>
	            <input type="hidden" name="end_time" value="0"/>
	            <input type="hidden" name="break_time" value="0"/>
	            <input type="hidden" name="replytime" value="0"/>
	            <input type="hidden" name="tickettravel" value="0"/>
		    <?php endif;?>

		    <?php if ($supportConfig->extra_email_notification): ?>
		    <div class="control-group row-fluid">
			    <label class="control-label">CC</label>
			    <div class="controls">
				    <input id="cc_report" name="cc_report" type="text" />
				    <div id="cc_emails"></div>
			    </div>
		    </div>
		    <div class="control-group row-fluid">
			    <label class="control-label">BCC</label>
			    <div class="controls">
				    <input id="bcc_report" name="bcc_report" type="text" />
				    <div id="bcc_emails"></div>
			    </div>
		    </div>
		    <?php endif;?>

	        <div class="control-group row-fluid">
	            <div class="control-label hidden-phone"></div>
	            <div class="controls">
	                <a href="javascript:;" onclick="AddAttachment();" title="<?php echo JText::_('add_attachment');?>" class="btn">
	                    <i class="ico-upload"></i> <?php echo JText::_('add_attachment');?>
	                </a>
	            </div>
	        </div>
	        <div id="AddAttachment" name="AddAttachment" style="display:none;">
                <div class="control-group row-fluid">
                    <div class="control-label hidden-phone"></div>
                    <div class="controls">
                        <div class="alert alert-info">
                            <p><?php echo JText::_("ALLOWED_TYPES");?>: <b><?php echo $supportConfig->extensions;?></b><br />
			                    <?php echo JText::_("MAXALLOWED");?>: <b><?php echo HelpdeskFile::FormatFileSize($supportConfig->maxAllowed);?></b></p>
                        </div>
                    </div>
                </div>
			    <?php foreach ($attachs as $rowloop): ?>
	            <div class="control-group row-fluid">
	                <label class="control-label" for="file<?php echo $rowloop['number'];?>"><?php echo JText::_('file');?> (<?php echo $rowloop['number'];?>)</label>
	                <div class="controls">
	                    <input type="file" id="file<?php echo $rowloop['number'];?>" name="file<?php echo $rowloop['number'];?>" />
                        &nbsp;
                        <a href="javascript:;" onclick="$jMaQma('#file<?php echo $rowloop['number'];?>').val('');" class="btn"><i class="ico-remove"></i></a>
	                </div>
	            </div>
	            <div class="control-group row-fluid">
	                <label class="control-label" for="desc<?php echo $rowloop['number'];?>"><?php echo JText::_('description');?></label>
	                <div class="controls">
	                    <textarea id="desc<?php echo $rowloop['number'];?>" name="desc<?php echo $rowloop['number'];?>" cols="48" rows="5"></textarea>
	                </div>
	            </div>
	            <div class="control-group row-fluid">
	                <label class="control-label" for="available<?php echo $rowloop['number'];?>"><?php echo JText::_('AVAILABLE_CUSTOMER');?></label>
	                <div class="controls">
					    <?php echo $rowloop['available'];?>
	                </div>
	            </div>
			    <?php endforeach;?>
	        </div>
		</div>
	</div>

	<div id="attachments" class="tab-pane fade"><?php
	    if (count($ticketAttachs)):?>
	        <table class="table table-striped table-bordered" cellspacing="0">
	            <thead>
	            <tr>
	                <th class="title"><?php echo JText::_('date');?></th>
	                <th class="title"><?php echo JText::_('filename');?></th>
	                <th class="title"><?php echo JText::_('description');?></th>
	                <th class="title"><?php echo JText::_('attachs_tools');?></th>
	                <th class="title"><?php echo JText::_('availability');?></th>
	            </tr>
	            </thead>
	            <tbody>
	                <?php $i = 0;
	                foreach ($ticket_attachs as $rowloop):?>
	                <tr>
	                    <td><?php echo $rowloop['date'];?></td>
	                    <td><a href="<?php echo $rowloop['link'];?>"><?php echo $rowloop['filename'];?></a></td>
	                    <td><?php echo $rowloop['description'];?></td>
	                    <td width="50" align="center"><?php echo $rowloop['tools'];?></td>
	                    <td width="50" align="center"><?php echo $rowloop['available'];?></td>
	                </tr><?php
	                    $i++;
	                endforeach;?>
	            </tbody>
	        </table>
	        <?php else: ?>
	        <p><span style="color:#ff0000"><?php echo JText::_('no_attachments');?></span></p>
	        <?php endif; ?>
	</div>

	<div id="logs" class="tab-pane fade">
	    <table class="table table-striped table-bordered" cellspacing="0">
	        <tbody><?php
	        $i = 0;
	        $pdate = '';
	        foreach ($ticketLogs as $rowloop):
	            $time = date("H:i", strtotime($rowloop->date));
	            $date = HelpdeskDate::DateOffset($supportConfig->dateonly_format, strtotime($rowloop->date));
	            if ($pdate != $date) {
	                $pdate = $date; ?>
		            <tr class="row">
		                <td class="log date" valign="top"><?php echo $date;?></td>
		                <td class="log daterow" width="20" valign="top"><?php echo $time;?></td>
		                <td class="log daterow"
		                    valign="top"><?php echo ($rowloop->image != '' ? '<img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/logs/' . $rowloop->image . '" align="absmiddle" alt="" />' : '');?> <?php echo $rowloop->message;?></td>
		            </tr><?php
	            } else {
	                ?>
		            <tr class="row">
		                <td valign="top"></td>
		                <td class="log empty" width="20" valign="top"><?php echo $time;?></td>
		                <td class="log empty"
		                    valign="top"><?php echo ($rowloop->image != '' ? '<img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/logs/' . $rowloop->image . '" align="absmiddle" alt="" />' : '');?> <?php echo $rowloop->message;?></td>
		            </tr><?php
	            }

	            $i++;
	        endforeach;?>

	        </tbody>
	    </table>
	</div>

	<?php if ($supportConfig->bbb_url != '' && $supportConfig->bbb_apikey): ?>
	<div id="meetings" class="tab-pane fade">
	    <p align="right">
	        <a href="javascript:;" onclick="AddMeeting();" class="mqmbutton success icon add" title="New meeting">New
	            meeting</a>
	    </p>

	    <div id="addlink" style="background:#fefefe;padding:10px;display:none;">
	        <input type="hidden" id="meeting_id" value=""/>

	        <p>Link: <input type="text" id="meeting_link" name="meeting_link" value="" size="75"/></p>

	        <p><a href="javascript:;" onclick="SaveLink();" class="mqmbutton success icon add" title="Save meeting">Save
	            link</a></p>

	        <p>&nbsp;</p>
	    </div>
	    <div id="addmeeting" style="background:#fefefe;padding:10px;display:none;">
	        <p>Date and
	            hours: <?php echo JHTML::Calendar('', 'meeting_date', 'meeting_date', '%Y-%m-%d', array('class' => 'inputbox', 'size' => '12', 'maxlength' => '10')); ?>
	            &nbsp; <input type="text" id="meeting_hours" name="meeting_hours" class="inputbox" size="3" maxlength="5"
	                          value=""/> <input type="hidden" name="now_date" value=""/></p>

	        <p>Invites (place one e-mail address per line):<br/><textarea id="meeting_invites" name="meeting_invites"
	                                                                      style="width:400px;height:200px;"></textarea></p>

	        <p><a href="javascript:;" onclick="SaveMeeting();" class="mqmbutton success icon add" title="Save meeting">Save
	            meeting</a></p>

	        <p>&nbsp;</p>
	    </div>
	    <div id="responsemeeting"
	         style="display:none;border:1px solid #ddd;background:#fefefe;padding:5px;margin-bottom:10px;"></div><?php
	    if (count($ticketMeetings)): ?>
	        <table class="table table-striped table-bordered" cellspacing="0">
	            <thead>
	            <tr>
	                <td class="title"><?php echo JText::_('date');?></td>
	                <td class="title"><?php echo JText::_('hour');?></td>
	                <td class="title"><?php echo JText::_('invites');?></td>
	                <td class="title">Links</td>
	                <td class="title">&nbsp;</td>
	            </tr>
	            </thead>
	            <tbody>
	                <?php $i = 0;
	                foreach ($ticketMeetings as $rowloop):
	                    $sql = "SELECT `invite`
								FROM `#__support_bbb_invites`
								WHERE `id_meeting`=" . $rowloop->id;
	                    $database->setQuery($sql);
	                    $invites = $database->loadObjectList();

	                    $sql = "SELECT `link`
								FROM `#__support_bbb_links`
								WHERE `id_meeting`=" . $rowloop->id;
	                    $database->setQuery($sql);
	                    $links = $database->loadObjectList();?>

	                <tr>
	                    <td valign="top"><?php echo $rowloop->meeting_date;?></td>
	                    <td valign="top"><?php echo $rowloop->meeting_hours;?></td>
	                    <td valign="top"><?php
	                        for ($x = 0; $x < count($invites); $x++) {
	                            echo $invites[$x]->invite . "<br />";
	                        } ?>
	                    </td>
	                    <td valign="top"><?php
	                        for ($x = 0; $x < count($links); $x++) {
	                            echo $links[$x]->link . "<br />";
	                        } ?>
	                        <a href="javascript:;" onclick="AddLink(<?php echo $rowloop->id;?>);">
	                            <small>ADD LINK</small>
	                        </a>
	                    </td>
	                    <td valign="top" align="center"><a href="javascript:;"
	                                                       onclick="StartMeeting(this,<?php echo $rowloop->id;?>);">
	                        <small>START</small>
	                    </a></td>
	                </tr><?php
	                    $i++;
	                endforeach;?>
	            </tbody>
	        </table>
	        <?php else: ?>
	        <p><span style="color:#ff0000">No meetings created for this ticket!</span></p>
	        <?php endif; ?>
	</div>
	    <?php endif;?>

	<div id="tasks" class="tab-pane fade">
	    <p align="right">
	        <a href="#AddTask" data-toggle="modal" class="btn btn-success"
	           title="<?php echo JText::_('newtask');?>"><?php echo JText::_('newtask');?></a>
	    </p><?php
	    if (count($ticketTasks)): ?>
	        <table class="table table-striped table-bordered" cellspacing="0">
	            <thead>
	            <tr>
	                <td class="title"><?php echo JText::_('date');?></td>
	                <td class="title"><?php echo JText::_('user');?></td>
	                <td class="title"><?php echo JText::_('task');?></td>
	            </tr>
	            </thead>
	            <tbody>
	                <?php $i = 0;
	                foreach ($ticket_tasks as $rowloop):?>
	                <tr>
	                    <td><?php echo $rowloop['date'];?></td>
	                    <td><?php echo $rowloop['user'];?></td>
	                    <td><?php echo $rowloop['task'];?></td>
	                </tr><?php
	                    $i++;
	                endforeach;?>
	            </tbody>
	        </table>
	        <?php else: ?>
	        <p><span style="color:#ff0000"><?php echo JText::_('no_tasks');?></span></p>
	        <?php endif; ?>
	</div>

	<div id="times" class="tab-pane fade">
	    <table width="100%" cellspacing="5">
	        <tr>
	            <td valign="top">
	                <p><b><?php echo JText::_('values');?></b></p>
	                <table class="table table-striped table-bordered" cellspacing="0">
	                    <thead>
	                    <tr>
	                        <td class="title"><?php echo JText::_('description');?></td>
	                        <td class="title"><?php echo JText::_('value');?></td>
	                    </tr>
	                    </thead>
	                    <tbody>
	                    <?php $i = 0;
	                    foreach ($ticket_values as $rowloop):?>
		                    <tr>
		                        <td><?php echo $rowloop['description'];?></td>
		                        <td align="right"><?php echo $supportConfig->currency . ' ' . $rowloop['value'];?></td>
		                    </tr><?php
	                        $i++;
	                    endforeach;?>
	                    <tr class="total">
	                        <td><b><?php echo JText::_('total');?></b></td>
	                        <td align="right">
	                            <b><?php echo $supportConfig->currency . ' ' . number_format($total_values, 2);?></b></td>
	                    </tr>
	                    </tbody>
	                </table>
	            </td>
	            <td valign="top">
	                <p><b><?php echo JText::_('times');?></b></p>
	                <table class="table table-striped table-bordered" cellspacing="0">
	                    <thead>
	                    <tr>
	                        <td class="title"><?php echo JText::_('description');?></td>
	                        <td class="title"><?php echo JText::_('time');?></td>
	                    </tr>
	                    </thead>
	                    <tbody>
	                    <?php $i = 0;
	                    foreach ($ticket_times as $rowloop):?>
		                    <tr>
		                        <td><?php echo $rowloop['description'];?></td>
		                        <td align="right"><?php echo $rowloop['value'];?></td>
		                    </tr><?php
	                        $i++;
	                    endforeach;?>
	                    <tr class="total">
	                        <td><b><?php echo JText::_('total');?></b></td>
	                        <td align="right"><b><?php echo $ticketTimeTotal; ?></b></td>
	                    </tr>
	                    </tbody>
	                </table>
	            </td>
	        </tr>
	    </table>
	</div>

	<div id="related_tickets" class="tab-pane fade"><?php
	    if (count($related_tickets)):?>
	        <table class="table table-striped table-bordered" cellspacing="0">
	            <thead>
	            <tr>
	                <td class="title"><?php echo JText::_('tickets');?></td>
	            </tr>
	            </thead>
	            <tbody>
	                <?php $i = 0;
	                foreach ($related_tickets as $rowloop):?>
	                <tr>
	                    <td><a
	                        href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $rowloop->id_workgroup;?>&task=ticket_view&id=<?php echo $rowloop->id;?>"
	                        target="_blank"><?php echo $rowloop->subject;?></a></td>
	                </tr><?php
	                    $i++;
	                endforeach;?>
	            </tbody>
	        </table>
	        <?php else: ?>
	        <p><span style="color:#ff0000"><?php echo JText::_('table_zero');?></span></p>
	        <?php endif; ?>
	</div>

	<div id="related_kb" class="tab-pane fade"><?php
	    if (count($related_kb)):?>
	        <table class="table table-striped table-bordered" cellspacing="0">
	            <thead>
	            <tr>
	                <td class="title"><?php echo JText::_('kb');?></td>
	            </tr>
	            </thead>
	            <tbody>
	                <?php $i = 0;
	                foreach ($related_kb as $rowloop):?>
	                <tr>
	                    <td><a
	                        href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=kb_view&id=<?php echo $rowloop->id;?>"
	                        target="_blank"><?php echo $rowloop->kbtitle;?></a></td>
	                </tr><?php
	                    $i++;
	                endforeach;?>
	            </tbody>
	        </table>
	        <?php else: ?>
	        <p><span style="color:#ff0000"><?php echo JText::_('table_zero');?></span></p>
	        <?php endif; ?>
	</div>

	<div id="related_discussions" class="tab-pane fade"><?php
	    if (count($related_discussions)):?>
	        <table class="table table-striped table-bordered" cellspacing="0">
	            <thead>
	            <tr>
	                <td class="title"><?php echo JText::_('discussions');?></td>
	            </tr>
	            </thead>
	            <tbody>
	                <?php $i = 0;
	                foreach ($related_discussions as $rowloop):?>
	                <tr>
	                    <td><a
	                        href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $rowloop->id_workgroup;?>&task=discussion_view&id_category=<?php echo $rowloop->id_category;?>&id=<?php echo $rowloop->id;?>"
	                        target="_blank"><?php echo $rowloop->title;?></a></td>
	                </tr><?php
	                    $i++;
	                endforeach;?>
	            </tbody>
	        </table>
	        <?php else: ?>
	        <p><span style="color:#ff0000"><?php echo JText::_('table_zero');?></span></p>
	        <?php endif; ?>
	</div>

	<div id="mtree" class="tab-pane fade"><?php
	    if ($supportConfig->integrate_mtree) {
	        ?>
	        <table class="table table-striped table-bordered" cellspacing="0">
	            <thead>
	            <tr>
	                <td class="title" width="30">ID</td>
	                <td class="title"><?php echo JText::_('name');?></td>
	                <td class="title"><?php echo JText::_('notes');?></td>
	                <td class="title"><?php echo JText::_('website');?></td>
	                <td class="title" width="75" align="center"><?php echo JText::_('approved');?></td>
	                <td class="title" width="75" align="center"><?php echo JText::_('published');?></td>
	            </tr>
	            </thead>
	            <tbody>
	                <?php $i = 0;
	                foreach ($mtree_links as $mtree):?>
	                <tr>
	                    <td valign="top" width="30" align="center"><?php echo $mtree->link_id;?></td>
	                    <td valign="top">
	                        <?php echo ($mtree->link_featured ? '<img src="' . JURI::root() . 'components/com_mtree/img/star_10.png" alt="' . JText::_('featured') . '" align="left" style="padding-right:5px;" /> ' : '') . $mtree->link_name;?>
	                        <br/>
	                        <small>
	                            <?php if ($mtree->link_published): ?>
	                            <a href="index.php?option=com_mtree&task=viewlink&link_id=<?php echo $mtree->link_id;?>"
	                               target="_blank"><?php echo JText::_('frontend');?></a> /
	                            <?php endif;?>
	                            <a target="_blank"
	                               href="<?php echo JURI::root();?>administrator/index.php?option=com_mtree&task=editlink&link_id=<?php echo $mtree->link_id;?>"><?php echo JText::_('backend');?></a>
	                        </small>
	                    </td>
	                    <td valign="top"><?php echo $mtree->internal_notes;?></td>
	                    <td valign="top"><?php echo $mtree->website;?></td>
	                    <td valign="top" width="75" align="center"><img
	                        src="<?php echo JURI::root();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/<?php echo ($mtree->link_approved ? 'ok' : 'no');?>.png"
	                        alt=""/></td>
	                    <td valign="top" width="75" align="center"><img
	                        src="<?php echo JURI::root();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/<?php echo ($mtree->link_published ? 'ok' : 'no');?>.png"
	                        alt=""/></td>
	                </tr><?php
	                    $i++;
	                endforeach;?>
	            </tbody>
	        </table><?php
	    } ?>
	</div>

	<div id="sobipro" class="tab-pane fade"><?php
	    if ($supportConfig->integrate_sobi) {
	        ?>
	        <table class="table table-striped table-bordered" cellspacing="0">
	            <thead>
	            <tr>
	                <td class="title" width="30">ID</td>
	                <td class="title"><?php echo JText::_('name');?></td>
	            </tr>
	            </thead>
	            <tbody>
	                <?php $i = 0;
	                foreach ($sobipro_links as $sobilisting):?>
	                <tr>
	                    <td width="30" align="center"><?php echo $sobilisting->sid;?></td>
	                    <td>
	                        <a href="index.php?option=com_sobipro&pid=<?php echo $sobilisting->section;?>&sid=<?php echo $sobilisting->sid;?>&tmpl=component&format=raw"
	                           class="modal"
	                           rel="{handler:'iframe', size: {x:600, y:450}}"><?php echo $sobilisting->baseData;?></a>
	                    </td>
	                </tr><?php
	                    $i++;
	                endforeach;?>
	            </tbody>
	        </table><?php
	    } ?>
	</div>

	<div id="artofuser" class="tab-pane fade"><?php
	    if ($supportConfig->integrate_artofuser) {
	        ?>
	        <table class="table table-striped table-bordered" cellspacing="0">
	            <thead>
	            <tr>
	                <td class="title" width="30">ID</td>
	                <td class="title"><?php echo JText::_('subject');?></td>
	                <td class="title"><?php echo JText::_('content');?></td>
	            </tr>
	            </thead>
	            <tbody>
	                <?php $i = 0;
	                foreach ($artofuser_notes as $artnote):?>
	                <tr>
	                    <td valign="top" width="30" align="center"><?php echo $artnote->id;?></td>
	                    <td valign="top"><a target="_blank"
	                                        href="<?php echo JURI::root();?>administrator/index.php?option=com_artofuser&task=note.edit&id=<?php echo $artnote->id;?>"><?php echo $artnote->subject;?></a>
	                    </td>
	                    <td valign="top"><?php echo strip_tags($artnote->body);?></td>
	                </tr><?php
	                    $i++;
	                endforeach;?>
	            </tbody>
	        </table><?php
	    } ?>
	</div>
	</div>

	<div class="form-actions" style="margin-left:-10px;margin-right:-10px;margin-bottom:-21px;">
        <button type="button" class="btn btn-success" id="ticket_reply" name="ticket_reply" onclick="submitbutton('ticket_reply');">
			<?php echo JText::_('save');?>
        </button>
        <button type="button" class="btn btn-link" name="ticket_cancel" onclick="Cancel();">
			<?php echo JText::_('cancel');?>
        </button>
    </div>

	</form>

	<div id="AddTask" style="display:none;width:650px;" class="modal fade">
	    <div class="modal-header">
	        <a class="close" data-dismiss="modal">&times;</a>
	        <h3><?php echo JText::_('add_task'); ?></h3>
	    </div>
	    <div class="modal-body">
	        <form id="addTaskForm" name="addTaskForm" action="<?php echo JRoute::_("index.php");?>" method="post"
	              class="form label-inline">
	            <?php echo JHtml::_('form.token'); ?>
	            <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	            <input type="hidden" name="Itemid" value="<?php echo $Itemid;?>"/>
	            <input type="hidden" name="id_workgroup" id="id_workgroup" value="<?php echo $id_workgroup;?>"/>
	            <input type="hidden" name="id" value="<?php echo $row->id;?>"/>
	            <input type="hidden" name="task" value="ticket_savetask"/>
	            <input type="hidden" name="clientrate" value="<?php echo $clientvalue;?>"/>

	            <div style="float:left;width:350px;">
	                <p><label for="usertask"><?php echo JText::_('user');?>: </label> <?php echo $lists['usertask'];?> </p>

	                <p><label for="taskdate"><?php echo JText::_('tmpl_msg25');?>: </label> <input type="text"
	                                                                                               class="inputbox"
	                                                                                               id="taskdate"
	                                                                                               name="taskdate"
	                                                                                               maxlength="10" size="12"
	                                                                                               value="<?php echo HelpdeskDate::DateOffset("%Y-%m-%d");?>"/>
	                </p>

	                <p><label for="taskfield"><?php echo JText::_('tmpl_msg23');?>: </label> <textarea name="taskfield"
	                                                                                                   id="taskfield"
	                                                                                                   style="width:325px; height:150px;"></textarea>
	                </p>

	                <p><label for="activity_type"><?php echo JText::_('tmpl_msg12');?>
	                    : </label> <?php echo $lists['task_type'];?> </p>

	                <p><label for="activity_rate"><?php echo JText::_('tmpl_msg13');?>
	                    : </label> <?php echo $lists['task_rate'];?> </p>

	                <p><label for="status"><?php echo JText::_('tmpl_msg26');?>: </label> <?php echo $lists['taskstatus'];?>
	                </p>
	            </div>
	            <div style="float:left;width:200px;">
	                <h3><?php echo JText::_('tasks_extra'); ?></h3>

	                <p><img src="<?php echo $imgpath;?>16px/time.png" align="absmiddle" border="0" hspace="5"/>
	                    <b><?php echo JText::_('start_times_hover_subj');?></b></p>

	                <p><?php echo JText::_('tmpl_msg14');?>: <input type="text" class="timepicker" id="taskstart"
	                                                                name="taskstart" maxlength="5" value="08:00"
	                                                                onchange="GetLabourTimeTasks();" size="4"/></p>

	                <p><?php echo JText::_('tmpl_msg15');?>: <input type="text" class="timepicker" id="taskend"
	                                                                name="taskend" maxlength="5" value="08:00"
	                                                                onchange="GetLabourTimeTasks();" size="4"/></p>

	                <p><?php echo JText::_('tmpl_msg16');?>: <input type="text" class="timepicker" id="taskbreak"
	                                                                name="taskbreak" maxlength="5" value="08:00"
	                                                                onchange="GetLabourTimeTasks();" size="4"/></p>

	                <p><?php echo JText::_('total');?>: <input style="text-align:center;width:50px;" type="text" value=""
	                                                           id="tasktime" name="tasktime" class="input" size="4"
	                                                           readonly/></p>

	                <p><img src="<?php echo $imgpath;?>16px/car.png" align="absmiddle" border="0" hspace="5"/>
	                    <b><?php echo JText::_('tmpl_msg22');?></b></p>

	                <p><?php echo JText::_('tmpl_msg20');?>: <input type="text" class="timepicker" id="traveltime"
	                                                                name="traveltime" maxlength="5"
	                                                                value="<?php echo $clienttravel;?>"/></p>

	                <p><?php echo JText::_('tmpl_msg30');?>: <?php echo $lists['task_travel'];?> </p>
	            </div>
	        </form>
	    </div>
	    <div class="modal-footer">
	        <button type="button" class="btn btn-success"
	                onclick="$jMaQma('#addTaskForm').submit();"><?php echo JText::_('save');?></button>
	        <a href="javascript:;" onclick="$jMaQma('#AddTask').modal('hide');" data-dismiss="modal"
	           class="btn"><?php echo JText::_('close');?></a>
	    </div>
	</div>

	<div id="AddNote" style="display:none;width:600px;" class="modal fade">
	    <div class="modal-header">
	        <a class="close" data-dismiss="modal">&times;</a>

	        <h3><?php echo JText::_('add_note'); ?></h3>
	    </div>
	    <div class="modal-body">
	        <form id="addNoteForm" name="addNoteForm" action="<?php echo JRoute::_("index.php");?>" method="post"
	              class="form label-inline">
	            <?php echo JHtml::_('form.token'); ?>
	            <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	            <input type="hidden" name="Itemid" value="<?php echo $Itemid;?>"/>
	            <input type="hidden" name="id_workgroup" id="id_workgroup" value="<?php echo $id_workgroup;?>"/>
	            <input type="hidden" name="id" value="<?php echo $row->id;?>"/>
	            <input type="hidden" name="task" value="ticket_savenote"/>

	            <div class="field">
	                <textarea name="note" id="note"
	                          style="height:250px;"></textarea>
	            </div>
	            <div class="field">
	                <label for="show0"><?php echo JText::_('available');?> </label>
	                <input type="radio" id="show0" name="show" value="0" class="inputbox" checked="checked" /> <?php echo JText::_('MQ_NO');?>
	                <input type="radio" id="show1" name="show" value="1" class="inputbox" /> <?php echo JText::_('MQ_YES');?>
	            </div>
	        </form>
	    </div>
	    <div class="modal-footer">
	        <button type="button" class="btn btn-success"
	                onclick="$jMaQma('#addNoteForm').submit();"><?php echo JText::_('save');?></button>
	        <a href="javascript:;" onclick="$jMaQma('#AddNote').modal('hide');" data-dismiss="modal"
	           class="btn"><?php echo JText::_('cancel');?></a>
	    </div>
	</div>

</div>