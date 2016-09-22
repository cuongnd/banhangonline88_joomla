<div class="maqmahelpdesk container-fluid">

	<h2><?php echo $client->clientname; ?></h2>

	<ul id="tab" class="nav nav-tabs">
		<li class="active"><a href="#general" data-toggle="tab"><?php echo '<img src="'.JURI::root().'media/com_maqmahelpdesk/images/themes/'.$supportConfig->theme_icon.'/16px/comments.png" align="absmiddle" /> '.JText::_('client_name'); ?></a></li>
		<li><a href="#workgroups" data-toggle="tab"><?php echo '<img src="'.JURI::root().'media/com_maqmahelpdesk/images/themes/'.$supportConfig->theme_icon.'/16px/workgroups.png" align="absmiddle" /> '.JText::_('workgroups');?></a></li>
		<li><a href="#users" data-toggle="tab"><?php echo '<img src="'.JURI::root().'media/com_maqmahelpdesk/images/themes/'.$supportConfig->theme_icon.'/16px/users.png" align="absmiddle" /> '.JText::_('users');?></a></li>
		<li><a href="#contracts" data-toggle="tab"><?php echo '<img src="'.JURI::root().'media/com_maqmahelpdesk/images/themes/'.$supportConfig->theme_icon.'/16px/contracts.png" align="absmiddle" /> '.JText::_('contracts');?></a></li>
		<li><a href="#files" data-toggle="tab"><?php echo '<img src="'.JURI::root().'media/com_maqmahelpdesk/images/themes/'.$supportConfig->theme_icon.'/16px/attach.png" align="absmiddle" /> '.JText::_('files_title');?></a></li>
		<li><a href="#logs" data-toggle="tab"><?php echo '<img src="'.JURI::root().'media/com_maqmahelpdesk/images/themes/'.$supportConfig->theme_icon.'/16px/logs.png" align="absmiddle" /> '.JText::_('info_title');?></a></li>
		<li><a href="#downloads" data-toggle="tab"><?php echo '<img src="'.JURI::root().'media/com_maqmahelpdesk/images/themes/'.$supportConfig->theme_icon.'/16px/files.png" align="absmiddle" /> '.JText::_('downloads');?></a></li>
		<li><a href="#tickets" data-toggle="tab"><?php echo '<img src="'.JURI::root().'media/com_maqmahelpdesk/images/themes/'.$supportConfig->theme_icon.'/16px/tickets.png" align="absmiddle" /> '.JText::_('tickets');?></a></li>
	</ul>

	<div id="my-tab-content" class="tab-content">
		<div id="general" class="tab-pane fade in active">
			<table cellspacing="0" cellpadding="5">
			<tr>
				<td height="25" align="left"> <b><?php echo JText::_('name');?>:</b> </td>
				<td align="left"><?php echo $client->clientname;?></td>
			</tr>
			<tr>
				<td height="25" align="left" valign="top"> <b><?php echo JText::_('description');?>:</b> </td>
				<td align="left"><?php echo $client->description;?> </td>
			</tr>
			<tr>
				<td height="25" align="left"> <b><?php echo JText::_('address');?>:</b> </td>
				<td align="left"><?php echo $client->address;?> </td>
			</tr>
			<tr>
				<td height="25" align="left"> <b><?php echo JText::_('zipcode');?>:</b> </td>
				<td align="left"><?php echo $client->zipcode;?> </td>
			</tr>
			<tr>
				<td height="25" align="left"> <b><?php echo JText::_('city');?>:</b> </td>
				<td align="left"><?php echo $client->city;?> </td>
			</tr>
			<tr>
				<td height="25" align="left"> <b><?php echo JText::_('state');?>:</b> </td>
				<td align="left"><?php echo $client->state;?> </td>
			</tr>
			<tr>
				<td height="25" align="left"> <b><?php echo JText::_('country');?>:</b> </td>
				<td align="left"><?php echo $client->country;?> </td>
			</tr>
			<tr>
				<td height="25" align="left"> <b><?php echo JText::_('phone');?>:</b> </td>
				<td align="left"><?php echo $client->phone;?> </td>
			</tr>
			<tr>
				<td height="25" align="left"> <b><?php echo JText::_('fax');?>:</b> </td>
				<td align="left"><?php echo $client->fax;?> </td>
			</tr>
			<tr>
				<td height="25" align="left"> <b><?php echo JText::_('mobile');?>:</b> </td>
				<td align="left"><?php echo $client->mobile;?> </td>
			</tr>
			<tr>
				<td height="25" align="left"> <b><?php echo JText::_('email_address');?>:</b> </td>
				<td align="left"><?php echo $client->email;?> </td>
			</tr>
			<tr>
				<td height="25" align="left"> <b><?php echo JText::_('contact');?>:</b> </td>
				<td align="left"><?php echo $client->contactname;?> </td>
			</tr>
			<tr>
				<td height="25" align="left"> <b><?php echo JText::_('website_address');?>:</b> </td>
				<td align="left"><?php echo $client->website;?> </td>
			</tr>
			<tr>
				<td height="25" align="left"> <b><?php echo JText::_('default_travel_time');?>:</b> </td>
				<td align="left"><?php echo $client->travel_time;?> </td>
			</tr>
			<tr>
				<td height="25" align="left"> <b><?php echo JText::_('billing_rate');?>:</b> </td>
				<td align="left"><?php echo $client->rate;?> </td>
			</tr>
			<tr>
				<td height="25" align="left"> <b><?php echo JText::_('notify_client_manager');?>:</b> </td>
				<td align="left"><?php echo ($client->manager ? JText::_('MQ_YES') : JText::_('MQ_NO'));?> </td>
			</tr>
			</table>
		</div>
		<div id="workgroups" class="tab-pane fade"><?php 
			if(count($client_wks)) { ?>
				<table class="table table-striped table-bordered" cellspacing="0">
				<thead>
				<tr>
					<th nowrap width="200px"><?php echo JText::_('name');?></th>
					<th nowrap><?php echo JText::_('dl_description');?></th>
				</tr>
				</thead><?php 
				$i = 0;
				foreach($client_wks as $row) 
				{ ?>
					<tr class="<?php echo (!$i ? 'first' : ($i%2 ? 'even' : ''));?>">
						<td><b><?php echo $row->name;?></b></td>
						<td><?php echo $row->description;?></td>
					</tr><?php 
					$i++;
				} ?>
				</table><?php 
			}else{ ?>
				<img src="<?php echo JURI::base();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/info.png" align="absmiddle" /> <b><?php echo JText::_('no_workgroups');?></b><?php
			} ?>

		</div>
		<div id="users" class="tab-pane fade"><?php
			if(count($client_users)){ ?>
				<table class="table table-striped table-bordered" cellspacing="0">
				<thead>
				<tr>
					<th nowrap> <?php echo JText::_('name');?> </th>
					<th nowrap> <?php echo JText::_('phone');?> </th>
					<th nowrap> <?php echo JText::_('fax');?> </th>
					<th nowrap> <?php echo JText::_('mobile');?> </th>
					<th nowrap width="50"><?php echo JText::_('manager');?></th>
				</tr>
				</thead><?php 
				$i = 0;
				foreach($client_users as $row)
				{ ?>
					<tr class="<?php echo (!$i ? 'first' : ($i%2 ? 'even' : ''));?>">
						<td> <?php echo $row->name;?></td>
						<td> <?php echo $row->phone;?></td>
						<td> <?php echo $row->fax;?></td>
						<td> <?php echo $row->mobile;?></td>
						<td width="50"><div align="center"><img src='<?php echo JURI::base();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/<?php echo ($row->manager ? 'ok' : 'no');?>.png' border='0' /></div></td>
					</tr><?php 
					$i++;
				} ?>
				</table><?php 
			}else{ ?>
				<img src="<?php echo JURI::base();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/info.png" align="absmiddle" /> <b><?php echo JText::_('no_users');?></b><?php
			} ?>
		</div>
		<div id="contracts" class="tab-pane fade">
			<table class="table table-striped table-bordered" cellspacing="0">
			<thead>
			<tr>
				<th><?php echo JText::_('number');?></th>
				<th><?php echo JText::_('contract_tmpl');?></th>
				<th><?php echo JText::_('start');?></th>
				<th><?php echo JText::_('end');?></th>
				<th><?php echo JText::_('unit');?></th>
				<th><?php echo JText::_('value');?></th>
				<th><?php echo JText::_('current');?></th>
				<th><?php echo JText::_('status');?></th>
				<th>&nbsp;</th>
			</tr>
			</thead><?php 
			if(count($client_contracts)) { 
				$i = 0; 
				foreach($contracts as $row)
				{ ?>
					<tr>
						<td><?php echo $row['number'];?></td>
						<td><?php echo $row['contract_tmpl'];?></td>
						<td><?php echo HelpdeskDate::DateOffset($supportConfig->dateonly_format,strtotime($row['date_start']));?></td>
						<td><?php echo HelpdeskDate::DateOffset($supportConfig->dateonly_format,strtotime($row['date_end']));?></td>
						<td><?php echo $row['unit'];?></td>
						<td><?php echo $row['value'];?></td>
						<td><?php echo $row['current'];?></td>
						<td><?php echo $row['status'];?></td><?php
						if (count($cfields))
						{
							$cfields_info = '';
							for ($x = 0; $x < count($cfields); $x++)
							{
								$cfield = $cfields[$x];
								$sql = "SELECT cf.value
										FROM #__support_contract_fields_values cf
										WHERE cf.id_contract='" . $row["id"] . "' AND cf.id_field='" . $cfield->id_field . "'
										LIMIT 1";
								$database->setQuery($sql);
								$contract_custom_field = $database->loadResult();
								$custom_value = ($contract_custom_field == '' ? '-' : $contract_custom_field);
								$cfields_info.= '<b>' . $cfield->caption . '</b>: ' . $custom_value . '<br />';
							}
						}?>
						<td><span class="showPopover" data-original-title="<?php echo JText::_('MORE_INFORMATION');?>" data-content="<?php echo $cfields_info . '<hr />' . JText::_('components') . '<br />' . $row['components'];?>"> <img src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/info.png" border="0"></span></td>
					</tr><?php 
					$i++;
				} 
			}else{?>
				<img src="<?php echo JURI::base();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/info.png" align="absmiddle" /> <b><?php echo JText::_('no_contracts');?></b><?php
			} ?>
			</table>
		</div>
		<div id="files" class="tab-pane fade"><?php 
			if(count($client_docs)) { ?>
				<table class="table table-striped table-bordered" cellspacing="0">
				<thead>
				<tr>
					<th><?php echo JText::_('created');?></th>
					<th><?php echo JText::_('description');?></th>
					<th><?php echo JText::_('file');?></th>
				</tr>
				</thead><?php 
				$i = 0;
				foreach($client_docs as $row) 
				{ ?>
					<tr>
						<td><?php echo HelpdeskDate::DateOffset($supportConfig->dateonly_format,strtotime($row->date_created." 00:00:00"));?></td>
						<td><?php echo $row->description;?></td>
						<td><a href="<?php echo JRoute::_('index.php?option=com_maqmahelpdesk&Itemid='.$Itemid.'&id_workgroup='.$id_workgroup.'&task=client_download&id='.$row->id);?>"><?php echo $row->filename;?></a></td>
					</tr><?php
					$i++; 
				} ?>
				</table><?php 
			}else{ ?>
				<img src="<?php echo JURI::base();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/info.png" align="absmiddle" /> <b><?php echo JText::_('no_files');?></b><?php
			}  ?>
		</div>
		<div id="logs" class="tab-pane fade"><?php
			if(count($client_info)) { ?>
				<table class="table table-striped table-bordered" cellspacing="0">
				<thead>
				<tr>
					<th><?php echo JText::_('date');?></th>
					<th><?php echo JText::_('subject');?></th>
					<th><?php echo JText::_('message');?></th>
				</tr>
				</thead><?php 
				$i = 0;
				foreach($client_info as $row) 
				{ ?>
					<tr>
						<td><?php echo HelpdeskDate::DateOffset($supportConfig->dateonly_format,strtotime($row->date));?></td>
						<td><?php echo $row->subject;?></td>
						<td><?php echo nl2br($row->message);?></td>
					</tr><?php 
					$i++;
				} ?>
				</table><?php 
			}else{ ?>
				<img src="<?php echo JURI::base();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/info.png" align="absmiddle" /> <b><?php echo JText::_('no_info');?></b><?php
			} ?>
		</div>
		<div id="downloads" class="tab-pane fade"><?php
			if(count($client_downloads))
			{ ?>
				<table class="table table-striped table-bordered" cellspacing="0">
				<thead>
				<tr>
					<th nowrap> <?php echo JText::_('download');?> </th>
					<th nowrap> <?php echo JText::_('serial_number');?> </th>
					<th nowrap> <?php echo JText::_('start');?> </th>
					<th nowrap> <?php echo JText::_('end');?> </th><?php

					// Get downloads custom fields
					$sql = "SELECT cf.id, cf.caption, fv.value
							FROM #__support_custom_fields AS cf
								 LEFT JOIN #__support_download_field_value AS fv ON fv.id_field=cf.id
							WHERE cf.cftype='D'
							ORDER BY cf.caption";
					$database->setQuery($sql);
					$cfields_downloads = $database->loadObjectList();

					for ($i=0; $i<count($cfields_downloads); $i++)
					{
						echo '<th nowrap>' . $cfields_downloads[$i]->caption . '</th>';
					} ?>

					<th nowrap width="50"><?php echo JText::_('status');?></th>
				</tr>
				</thead><?php 
				$i = 0;
				foreach($client_downloads as $row)
				{ ?>
					<tr>
						<td> <?php echo $row->pname;?></td>
						<td> <?php echo $row->serialno;?></td>
						<td> <?php echo $row->servicefrom;?></td>
						<td> <?php echo $row->serviceuntil;?></td><?php

						// Get downloads custom fields
						$sql = "SELECT cf.id, cf.caption, fv.value
								FROM #__support_custom_fields AS cf
									 LEFT JOIN #__support_download_field_value AS fv ON fv.id_field=cf.id AND fv.id_download=" . (int) $row->id . "
								WHERE cf.cftype='D'
								ORDER BY cf.caption";
						$database->setQuery($sql);
						$cfields_downloads = $database->loadObjectList();

						for ($i=0; $i<count($cfields_downloads); $i++)
						{
							echo '<td>' . $cfields_downloads[$i]->value . '</td>';
						} ?>

						<td width="50"><div align="center"><?php echo ( $row->isactive ? JText::_('active') : JText::_('inactive') );?></div></td>
					</tr><?php 
					$i++;
				} ?>
				</table><?php 
			}else{ ?>
				<img src="<?php echo JURI::base();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/info.png" align="absmiddle" /> <b><?php echo JText::_('NO_DOWNLOADS');?></b><?php
			} ?>
		</div>
		<div id="tickets" class="tab-pane fade">
			<div align="right">
				<p><a href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=ticket_new&id_client=<?php echo $id;?>" target="_parent" class="btn btn-success"><?php echo JText::_('qk_create_ticket');?></a></p>
			</div><?php 
			if(count($client_tickets)) { ?>
				<table class="table table-striped table-bordered" cellspacing="0">
				<thead>
				<tr>
					<th width="20">&nbsp;</th>
					<th><?php echo JText::_('id');?></th>
					<th><?php echo JText::_('subject');?></th>
					<th><?php echo JText::_('user');?></th>
					<th><?php echo JText::_('date');?></th>
					<th><?php echo JText::_('status');?></th>
					<th width="50"><?php echo JText::_('more');?></th>
				</tr>
				</thead><?php
				$i = 0;
				foreach($tickets as $row)
				{ ?>
					<tr class="<?php echo (!$i ? 'first' : ($i%2 ? 'even' : ''));?>">
						<td width="20"> <?php echo $row['attachs_image'];?></td>
						<td><a href="<?php echo $row['link'];?>"><?php echo $row['ticketid'];?></a></td>
						<td><a href="<?php echo $row['link'];?>"><?php echo $row['subject'];?></a></td>
						<td> <?php echo $row['user'];?></td>
						<td> <?php echo $row['date_created'];?></td>
						<td> <?php echo $row['status'];?></td>
						<td width="50" class="showPopover tac" data-original-title="<?php echo JText::_('more_information');?>" data-content="<?php echo JText::_('ticketid');?>: <b><?php echo $row['ticketid'];?></b><br /><?php echo JText::_('update');?>: <b><?php echo $row['date_updated'];?></b><br /><?php echo JText::_('source');?>: <b><?php echo $row['source'];?></b><br /><?php echo JText::_('messages');?>: <b><?php echo $row['messages'];?></b>"><?php echo $row['icon_duedate'];?></td>
					</tr><?php 
					$i++;
				} ?>
				</table><?php 
			}else{?>
				<img src="<?php echo JURI::base();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/info.png" align="absmiddle" /> <b><?php echo JText::_('no_tickets');?></b><?php
			} ?>
			</div>
	</div>

</div>

<script type="text/javascript">
$jMaQma(document).ready(function ($) {
	$jMaQma('.showPopover').popover({'html':true, 'placement':'bottom', 'trigger':'hover'});
});
</script>