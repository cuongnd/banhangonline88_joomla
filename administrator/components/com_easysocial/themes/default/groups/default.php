<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<form action="index.php" method="post" name="adminForm" class="esForm" id="adminForm" data-table-grid>

	<div class="filter-bar form-inline<?php echo $callback ? ' mt-20' : '';?>">
		<div class="form-group">
			<?php echo $this->html( 'filter.search' , $search ); ?>
		</div>

		<?php if( $this->tmpl != 'component' ){ ?>
		<div class="form-group">
			<strong><?php echo JText::_( 'COM_EASYSOCIAL_FILTER_BY' ); ?> :</strong>
			<div>
				<?php echo $this->html( 'filter.published' , 'state' , $state ); ?>
				<select class="form-control input-sm" name="type" id="filterType" data-table-grid-filter>
					<option value="all"<?php echo $type == 'all' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYSOCIAL_FILTER_GROUP_TYPE' ); ?></option>
					<option value="1"<?php echo $type === 1 ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_OPEN_GROUP' ); ?></option>
					<option value="2"<?php echo $type === 2 ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_CLOSED_GROUP' ); ?></option>
					<option value="3"<?php echo $type === 3 ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_INVITE_GROUP' ); ?></option>
				</select>
			</div>
		</div>

		<div class="form-group pull-right">
			<div><?php echo $this->html( 'filter.limit' , $limit ); ?></div>
		</div>
		<?php } ?>
	</div>

	<div id="profilesTable" data-profiles>

		<table class="table table-striped table-es table-hover">
			<thead>
				<tr>
					<?php if( $this->tmpl != 'component' ){ ?>
					<th width="1%" class="center">
						<input type="checkbox" name="toggle" data-table-grid-checkall />
					</th>
					<?php } ?>

					<th style="text-align: left;">
						<?php echo $this->html( 'grid.sort' , 'a.title' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_TITLE' ) , $ordering , $direction ); ?>
					</th>

					<?php if( $this->tmpl != 'component' ){ ?>
					<th class="center" width="15%">
						<?php echo $this->html( 'grid.sort' , 'b.title' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_CATEGORY' ) , $ordering , $direction ); ?>
					</th>
					<th class="center" width="5%">
						<?php echo $this->html( 'grid.sort' , 'a.featured' , JText::_('COM_EASYSOCIAL_TABLE_COLUMN_FEATURED') , $ordering , $direction ); ?>
					</th>
					<th class="center" width="5%">
						<?php echo $this->html( 'grid.sort' , 'a.state' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_STATUS' ) , $ordering , $direction ); ?>
					</th>
					<th class="center" width="5%">
						<?php echo JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_CREATED_BY' ); ?>
					</th>
					<?php } ?>

					<th width="5%" class="center">
						<?php echo JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_USERS' ); ?>
					</th>

					<?php if( $this->tmpl != 'component' ){ ?>
					<th width="10%" class="center">
						<?php echo $this->html( 'grid.sort' , 'a.created' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_CREATED' ) , $ordering , $direction ); ?>
					</th>
					<?php } ?>

					<th width="<?php echo $callback ? '10%' : '5%';?>" class="center">
						<?php echo $this->html( 'grid.sort' , 'a.id' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_ID' ) , $ordering , $direction ); ?>
					</th>
				</tr>
			</thead>
			<tbody>

				<?php if( $groups ){ ?>
					<?php $i = 0; ?>
					<?php foreach( $groups as $group ){ ?>
					<tr class="row<?php echo $i; ?>"
						data-profiles-item
						data-grid-row
						data-title="<?php echo $this->html( 'string.escape' , $group->getName() );?>"
						data-id="<?php echo $group->id;?>"
					>
						<?php if( $this->tmpl != 'component' ){ ?>
						<td align="center">
							<?php echo $this->html( 'grid.id' , $i , $group->id ); ?>
						</td>
						<?php } ?>

						<td>
							<div class="media">
								<div class="media-object pull-left">
									<img src="<?php echo $group->getAvatar();?>" class="es-avatar" />
								</div>

								<div class="media-body">
									<a href="<?php echo FRoute::_( 'index.php?option=com_easysocial&view=groups&layout=form&id=' . $group->id );?>"
										data-group-insert
										data-id="<?php echo $group->id;?>"
										data-avatar="<?php echo $group->getAvatar();?>"
										data-title="<?php echo $this->html( 'string.escape' , $group->getName() );?>"
										style="font-size: 16px;font-weight:700;">
										<?php echo $group->getName(); ?>
									</a>

									<?php if( $this->tmpl != 'component' ){ ?>
										<p class="mt-5 mb-10 fd-small">
											<?php if( $group->description ){ ?>
												<?php echo $this->html('string.truncater', $group->description, 180);?>
											<?php } else { ?>
												<?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_NO_DESCRIPTION' ); ?>
											<?php } ?>
										</p>


										<?php if( $group->isOpen() ){ ?>
										<span class="label label-success" data-original-title="<?php echo JText::_('COM_EASYSOCIAL_GROUPS_OPEN_GROUP_TOOLTIP' , true );?>" data-es-provide="tooltip" data-placement="top">
											<i class="ies-earth"></i> <?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_OPEN_GROUP' ); ?>
										</span>
										<?php } ?>

										<?php if( $group->isClosed() ){ ?>
										<span class="label label-danger" data-original-title="<?php echo JText::_('COM_EASYSOCIAL_GROUPS_CLOSED_GROUP_TOOLTIP' , true );?>" data-es-provide="tooltip" data-placement="top">
											<i class="ies-locked"></i> <?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_CLOSED_GROUP' ); ?>
										</span>
										<?php } ?>

										<?php if( $group->isInviteOnly() ){ ?>
										<span class="label label-warning" data-original-title="<?php echo JText::_('COM_EASYSOCIAL_GROUPS_INVITE_GROUP_TOOLTIP' , true );?>" data-es-provide="tooltip" data-placement="top">
											<i class="ies-locked"></i> <?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_INVITE_GROUP' ); ?>
										</span>
										<?php } ?>
									<?php } ?>

								</div>
							</div>
						</td>

						<td class="center">
							<a href="index.php?option=com_easysocial&view=groups&layout=category&id=<?php echo $group->getCategory()->id;?>"><?php echo $group->getCategory()->get( 'title' ); ?></a>
						</td>

						<?php if( $this->tmpl != 'component' ){ ?>
						<td class="center">
							<?php echo $this->html('grid.featured', $group, 'groups', 'featured'); ?>
						</td>
						<td class="center">
							<?php echo $this->html( 'grid.published' , $group , 'groups' ); ?>
						</td>
						<td class="center">
							<a href="<?php echo $group->getCreator()->getPermalink( true , true );?>" target="_blank"><?php echo $group->getCreator()->getName();?></a>
						</td>

						<td class="center">
							<?php echo $group->getTotalMembers(); ?>
						</td>

						<td class="center">
							<?php echo $group->created; ?>
						</td>
						<?php } ?>

						<td class="center">
							<?php echo $group->id;?>
						</td>
					</tr>
						<?php $i++; ?>
					<?php } ?>
				<?php } else { ?>
					<tr class="is-empty">
						<td colspan="9" class="center empty">
							<?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_NO_GROUPS_AVAILABLE' );?>
						</td>
					</tr>
				<?php } ?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="9" class="center">
						<div class="footer-pagination"><?php echo $pagination->getListFooter(); ?></div>
					</td>
				</tr>
			</tfoot>

		</table>

	</div>

	<?php echo JHTML::_( 'form.token' ); ?>
	<?php if ($this->tmpl == 'component') { ?>
	<input type="hidden" name="tmpl" value="component" />
	<?php } ?>
	<input type="hidden" name="jscallback" value="<?php echo $this->html('string.escape', JRequest::getWord('jscallback'));?>" />
	<input type="hidden" name="ordering" value="<?php echo $ordering;?>" data-table-grid-ordering />
	<input type="hidden" name="direction" value="<?php echo $direction;?>" data-table-grid-direction />
	<input type="hidden" name="boxchecked" value="0" data-table-grid-box-checked />
	<input type="hidden" name="task" value="" data-table-grid-task />
	<input type="hidden" name="option" value="com_easysocial" />
	<input type="hidden" name="view" value="groups" />
	<input type="hidden" name="controller" value="groups" />
</form>
