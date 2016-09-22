<div class="maqmahelpdesk container-fluid">

    <?php if ($user->id && (($is_support && $supportConfig->show_dashboard_support) || (!$is_support && $supportConfig->show_dashboard_customer))) : ?>
	<div class="row-fluid">
		<div class="span4 container">
			<span class="badge"><?php echo $tickets_pending; ?></span> <?php echo JText::_('pending_tickets');?>
		</div>
		<div class="span4 container">
			<span class="badge badge-important"><?php echo $tickets_overdue; ?></span> <?php echo JText::_('overdued_ticket');?>
		</div>
		<div class="span4 container">
			<span class="badge badge-success"><?php echo $tickets_today; ?></span> <?php echo JText::_('tickets_today');?>
		</div>
	</div>
    <?php endif;?>

    <h2><?php echo JText::_('select_workgroup');?></h2><?php
	$previous_group = '';
	$item = 1;
	foreach ($workgroups as $row)
	{
		if ($previous_group != $row["group_title"])
		{
			$previous_group = $row["group_title"]; ?>
			<div style="width:100%;clear:both;padding-top:10px;">
				<h3><?php echo $row["group_title"];?></h3><?php
				echo $row["group_description"]; ?>
			</div><?php
		}
		if ($item == 1):?>
			<div class="row-fluid"><?php
		endif;?>
		<div class="span6" style="margin-bottom:10px;">
			<a href="<?php echo $row['link'];?>" class="btn equalheight" style="display:block;padding:15px;text-align:left;min-height:60px;">
				<img src="<?php echo $row['image'];?>" alt="<?php echo $row['title'];?>" class="img-rounded " style="float:left;padding-right:10px;padding-bottom:10px;" />
				<h4 style="margin:5px 0;"><?php echo $row['title'];?></h4>
				<small><?php echo $row['shortdesc'] != '' ? strip_tags($row['shortdesc']) : strip_tags($row['description']);?></small>
			</a>
		</div><?php
		if ($item == 2):
			$item = 0;?>
			</div><?php
		endif;
		$item++;
	}
	// Verify close
	if ($item == 2):?>
		</div><?php
	endif;?>
    <div style="clear:both;"></div>

	<?php if ($supportConfig->show_kb_frontpage && (count($featured_kb) || count($viewed_kb))): ?>
	<div class="row-fluid">
		<div class="span6">
			<table class="table table-striped table-bordered">
				<thead>
				<tr>
					<th><?php echo JText::_('kb_most_rated');?></th>
				</tr>
				</thead>
				<tbody><?php
				foreach ($featured_kb as $row)
				{ ?>
					<tr>
						<td>
							<p>
								<a href="<?php echo $row['link'];?>" title="<?php echo $row['title'];?>">
									<?php echo $row['title'];?>
								</a><br />
								<small>
									<img src="media/com_maqmahelpdesk/images/rating/<?php echo $row['rating'];?>star.png"
									     alt="<?php echo JText::_('rating');?>" align="absmiddle"/>
								</small>
							</p>
						</td>
					</tr><?php
				} ?>
				</tbody>
			</table>
		</div>
		<div class="span6">
			<table class="table table-striped table-bordered">
				<thead>
				<tr>
					<th><?php echo JText::_('kb_most_viewed');?></th>
				</tr>
				</thead>
				<tbody><?php
					foreach ($viewed_kb as $row)
					{ ?>
						<tr>
							<td>
								<p>
									<a href="<?php echo $row['link'];?>" title="<?php echo $row['title'];?>">
										<?php echo $row['title'];?>
									</a><br />
									<small>
										<?php echo JText::_('views');?>: <b><?php echo $row['views'];?></b>
									</small>
								</p>
							</td>
						</tr><?php
					} ?>
				</tbody>
			</table>
		</div>
	</div>
    <?php endif;?>

</div>