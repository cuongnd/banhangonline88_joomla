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

	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#acl-rules" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_ACL_RULE_SET' ); ?></a>
			</li>
			<li>
				<a href="#acl-textfilter" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_ACL_TEXT_FILTERS' );?></a>
			</li>
		</ul>

	</div>

	<div class="tab-content">

		<div class="tab-pane active" id="acl-rules">
			<table class="table table-striped">
				<tr>
					<td width="25%" class="key">
						<label for="cid"><?php echo JText::_( 'COM_EASYBLOG_ID' ); ?></label>
					</td>
					<td>
						<div id="aclid"><?php echo !empty($this->rulesets->id)? $this->rulesets->id : ''; ?></div>
					</td>
				</tr>
				<tr>
					<td width="150" class="key">
						<label for="name"><?php echo JText::_( 'COM_EASYBLOG_ACL_NAME' ); ?></label>
					</td>
					<td>
						<input type="text" readonly="readonly" class="inputbox" id="aclname" value="<?php echo !empty($this->rulesets->name)?  $this->escape( $this->rulesets->name ) : ''; ?>">
						<?php if ( $this->type == 'assigned' ) : ?>
						[ <a class="modal" rel="{handler: 'iframe', size: {x: 650, y: 375}}" href="index.php?option=com_easyblog&view=users&tmpl=component&browse=1"><?php echo JText::_('COM_EASYBLOG_BROWSE_USERS');?></a> ]
						<?php endif; ?>
					</td>

				</tr>
			<?php
			foreach($this->rulesets->rules as $key=>$data)
			{
			?>
				<tr>
					<td width="150" class="key">
						<label for="name">
							<?php echo JText::_( 'COM_EASYBLOG_ACL_OPTION_' . $key ); ?>
						</label>
					</td>
					<td>
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( $this->getDescription( $key ) ); ?></div>
							<?php echo $this->renderCheckbox( $key , $data ); ?>
						</div>
					</td>
				</tr>
			<?php
			}
			?>
			</table>
		</div>

		<div class="tab-pane" id="acl-textfilter">
			<p class="small"><?php echo JText::_( 'COM_EASYBLOG_ACL_TEXT_FILTERS_INFO' );?></p>
			<table class="table table-striped">
				<tr>
					<td width="25%" class="key">
						<label for="disallow-tags"><?php echo JText::_( 'COM_EASYBLOG_DISALLOWED_HTML_TAGS' ); ?></label>
					</td>
					<td>
						<textarea id="disallow-tags" name="disallow_tags" class="input-xlarge"><?php echo $this->filter->disallow_tags;?></textarea>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="disallow-attributes"><?php echo JText::_( 'COM_EASYBLOG_DISALLOWED_HTML_ATTRIBUTES' ); ?></label>
					</td>
					<td>
						<textarea id="disallow-attributes" name="disallow_attributes" class="input-xlarge"><?php echo $this->filter->disallow_attributes;?></textarea>
					</td>

				</tr>
			</table>

		</div>
	</div>

</div>
