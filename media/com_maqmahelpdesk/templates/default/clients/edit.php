<div class="maqmahelpdesk container-fluid">

	<h2><?php echo $client->clientname; ?></h2>

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