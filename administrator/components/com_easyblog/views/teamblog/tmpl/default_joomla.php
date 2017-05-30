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
<form name="adminForm" id="adminForm" action="index.php?option=com_easyblog" method="post" enctype="multipart/form-data">
<table width="100%" id="teamblogForm">
	<tr>
		<td width="50%" valign="top">
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_TEAMBLOGS_DETAILS' ); ?></legend>
				<table width="100%" class="admintable" cellspacing="1">
					<tr>
						<td class="key">
							<span><?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_TEAM_NAME'); ?></span>
						</td>
						<td class="paramlist_value">
							<input class="inputbox full-width" type="text" id="title" name="title" value="<?php echo $this->escape( $this->team->title );?>" />
						</td>
					</tr>
					<tr>
						<td class="key">
							<span><?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_TEAM_ALIAS'); ?></span>
						</td>
						<td class="paramlist_value">
							<input class="inputbox full-width" type="text" id="alias" name="alias" value="<?php echo $this->escape( $this->team->alias );?>" />
						</td>
					</tr>
					<tr>
						<td class="key" style="vertical-align: top;">
							<span><?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_TEAM_DESCRIPTION'); ?></span>
						</td>
						<td class="paramlist_value">
							<?php echo $this->editor->display( 'write_description', $this->team->description, '100%', '280', '10', '10' , array('article', 'image', 'readmore', 'pagebreak') ); ?>
						</td>
					</tr>

					<?php if($this->config->get('layout_teamavatar', true)) : ?>
					<tr>
			        	<td class="key">
			        	    <span><?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_AVATAR'); ?></span>
						</td>
						<td>
						    <?php if(! empty($this->team->avatar)) { ?>
							<img style="border-style:solid; float:none;" src="<?php echo $this->team->getAvatar(); ?>" width="60" height="60"/><br />
						    <?php } ?>
							<input id="file-upload" type="file" name="Filedata" class="inputbox" size="33"/>
						</td>
					</tr>
					<?php endif; ?>
					<tr>
						<td class="key">
							<span><?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_CREATED'); ?></span>
						</td>
						<td class="paramlist_value">
							<?php echo JHTML::_('calendar', $this->team->created , "created", "created" , '%Y-%m-%d %H:%M:%S' , 'size="35"'); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<span><?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_PUBLISHED'); ?></span>
						</td>
						<td class="paramlist_value">
							<?php echo $this->renderCheckbox( 'published' , $this->team->published ); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<span><?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_ACCESS'); ?></span>
						</td>
						<td class="paramlist_value"><?php echo $this->blogAccessList; ?></td>
					</tr>
				</table>
				<div style="clear:both;"></div>
			</fieldset>
		</td>
		<td valign="top">
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_TEAMBLOGS_MEMBERS' ); ?></legend>
				<p><?php echo JText::_( 'COM_EASYBLOG_TEAMBLOGS_MEMBERS_DESC' ); ?></p>
				<div id="members-container">
				<?php
				if( $members = $this->getMembers( $this->team->id ) )
				{
					foreach($members as $member)
					{
						$user	= JFactory::getUser( $member->user_id );

						$markAdmin		= '- <a href="javascript:void(0);" onclick="admin.teamblog.markAdmin('.$this->team->id.','.$member->user_id.');">' . JText::_( 'COM_EASYBLOG_TEAMBLOGS_SET_ADMIN' ) . '</a>';
						$removeAdmin	= '- <a href="javascript:void(0);" onclick="admin.teamblog.removeAdmin('.$this->team->id.','.$member->user_id.');">' . JText::_( 'COM_EASYBLOG_TEAMBLOGS_REMOVE_ADMIN' ) . '</a>';
					?>
						<span id="member-<?php echo $user->id;?>" class="members-item">
							<input type="hidden" name="members[]" value="<?php echo $user->id;?>" />
							<a class="remove_item" href="javascript:void(0);" onclick="removeMember('member-<?php echo $user->id;?>', '<?php echo $user->id;?>');">X</a>
							<span class="<?php echo $member->isadmin ? 'admin-member' : 'normal-member'; ?>">
								<?php echo $user->name; ?>
								<?php echo ($member->isadmin) ? $removeAdmin : $markAdmin; ?>
							</span>

						</span>
					<?php
					}
				}
				?>
				</div>
				<div style="clear:both;"></div>
				<div style="margin-top: 10px;">
					<a class="modal button" rel="{handler: 'iframe', size: {x: 650, y: 375}}" href="index.php?option=com_easyblog&view=users&tmpl=component&browse=1"><?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_ADD_MEMBER');?></a>
				</div>
			</fieldset>

			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYBLOG_TEAMBLOGS_GROUPS' ); ?></legend>
				<p><?php echo JText::_( 'COM_EASYBLOG_TEAMBLOGS_GROUPS_DESC' ); ?></p>
				<div id="groups-container">
				<?php
				if( $groups = $this->getGroups( $this->team->id ) )
				{
					foreach( $groups as $group )
					{
					?>
						<span id="group-<?php echo $group->id;?>" class="members-item">
							<input type="hidden" name="groups[]" value="<?php echo $group->id;?>" />
							<a class="remove_item" href="javascript:void(0);" onclick="removeGroup('group-<?php echo $group->id;?>', '<?php echo $group->id;?>');">X</a>
							<span class="normal-member">
								<?php echo $group->name; ?>
							</span>

						</span>
					<?php
					}
				}
				?>
				</div>
				<div style="clear:both;"></div>
				<div style="margin-top: 10px;">
					<a class="modal button" rel="{handler: 'iframe', size: {x: 650, y: 375}}" href="index.php?option=com_easyblog&view=usergroups&tmpl=component&browse=1"><?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_ADD_GROUP');?></a>
				</div>
			</fieldset>

			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_META_TAGS'); ?></legend>

	  			<table class="admintable">
	  				<tr>
	  					<td>
			  				<label for="keywords" class="label label-title"><?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_META_KEYWORDS'); ?></label><br />
			    		</td>
			    	</tr>
			    	<tr>
			    		<td>
			    			<textarea name="keywords" id="keywords" class="inputbox" style="width: 98%;"><?php echo $this->meta->keywords; ?></textarea><br />
							<div><small>( <?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_META_KEYWORDS_INSTRUCTIONS'); ?> )</small></div>
						</td>
					</tr>

					<tr>
	  					<td>
			  				<label for="description" class="label label-title"><?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_META_DESCRIPTION'); ?></label><br />
			    		</td>
			    	</tr>
			    	<tr>
			    		<td>
			    			<textarea name="description" id="description" class="inputbox" style="width: 98%;"><?php echo $this->meta->description; ?></textarea>
						</td>
					</tr>
    				<input type="hidden" name="metaid" value="<?php echo $this->meta->id; ?>" />
				</table>
			</fieldset>
		</td>
	</tr>
</table>