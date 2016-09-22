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
<h3><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_TAB_INTEGRATIONS' );?></h3>
<hr />
<div class="row-fluid">

	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#team-details" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_TEAMBLOGS_DETAILS' ); ?></a>
			</li>
			<li>
				<a href="#team-members" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_TEAMBLOGS_MEMBERS' );?></a>
			</li>
			<li>
				<a href="#team-groups" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_TEAMBLOGS_GROUPS' );?></a>
			</li>
			<li>
				<a href="#team-tags" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_TEAMBLOGS_META_TAGS' );?></a>
			</li>
		</ul>

	</div>

	<div class="tab-content">

		<div class="tab-pane active" id="team-details">
			<table width="100%" class="table table-striped">
				<tr>
					<td class="key" width="20%">
						<span><?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_TEAM_NAME'); ?></span>
					</td>
					<td class="paramlist_value">
						<input class="input-xlarge" type="text" id="title" name="title" value="<?php echo $this->escape( $this->team->title );?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<span><?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_TEAM_ALIAS'); ?></span>
					</td>
					<td class="paramlist_value">
						<input class="input-xlarge" type="text" id="alias" name="alias" value="<?php echo $this->escape( $this->team->alias );?>" />
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
		</div>

		<div class="tab-pane" id="team-members">
			<p><?php echo JText::_( 'COM_EASYBLOG_TEAMBLOGS_MEMBERS_DESC' ); ?></p>
			<div id="members-container">
			<?php
			if( $members = $this->getMembers( $this->team->id ) )
			{
				foreach($members as $member)
				{
					$user	= JFactory::getUser( $member->user_id );

					$markAdmin		= '- <a href="javascript:void(0);" onclick="admin.teamblog.markAdmin('.$this->team->id.','.$member->user_id.',\'' . EasyBlogHelper::getToken() . '\');">' . JText::_( 'COM_EASYBLOG_TEAMBLOGS_SET_ADMIN' ) . '</a>';
					$removeAdmin	= '- <a href="javascript:void(0);" onclick="admin.teamblog.removeAdmin('.$this->team->id.','.$member->user_id.',\'' . EasyBlogHelper::getToken() . '\');">' . JText::_( 'COM_EASYBLOG_TEAMBLOGS_REMOVE_ADMIN' ) . '</a>';
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
				<a class="modal btn btn-primary" rel="{handler: 'iframe', size: {x: 650, y: 375}}" href="index.php?option=com_easyblog&view=users&tmpl=component&browse=1">
					<i class="icon-plus icon-white"></i> <?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_ADD_MEMBER');?>
				</a>
			</div>
		</div>

		<div class="tab-pane" id="team-groups">
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
				<a class="modal btn btn-primary" rel="{handler: 'iframe', size: {x: 650, y: 375}}" href="index.php?option=com_easyblog&view=usergroups&tmpl=component&browse=1">
					<i class="icon-plus icon-white"></i> <?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_ADD_GROUP');?>
				</a>
			</div>
		</div>

		<div class="tab-pane" id="team-tags">

			<h3><?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_META_KEYWORDS'); ?></h3>
			<hr />
			<textarea name="keywords" id="keywords" class="input-xxlarge"><?php echo $this->meta->keywords; ?></textarea><br />
			<div><small>( <?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_META_KEYWORDS_INSTRUCTIONS'); ?> )</small></div>

			<h3><?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_META_DESCRIPTION'); ?></h3>
			<hr />
			<textarea name="description" id="description" class="input-xxlarge"><?php echo $this->meta->description; ?></textarea>
			<input type="hidden" name="metaid" value="<?php echo $this->meta->id; ?>" />
			
		</div>
<br/>
<br/>
</div>
