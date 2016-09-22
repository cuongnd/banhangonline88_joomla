<div class="maqmahelpdesk container-fluid">

	<h2><?php echo $workgroupSettings->wkdesc;?></h2>

    <?php echo $workgroupSettings->wkabout;?>

	<?php if ($supportConfig->show_dashboard_customer) : ?>
	<div class="row-fluid" style="margin-top:10px;margin-bottom:10px;">
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

	<?php if (count($announcements) && $workgroupSettings->wkannounces) : ?>
	<div class="well well-small">
		<?php foreach ($announcements as $row) : ?>
		<div class="row-fluid">
			<div class="span3 container">
				<p style="padding:0px;margin:0;">
					<span class="lbl"><?php echo HelpdeskDate::DateOffset($supportConfig->dateonly_format,mktime(0,0,0,$row->date_month,$row->date_day,$row->date_year));?></span>
					<?php echo ($row->urgent ? '<span class="lbl lbl-important">' . JText::_("URGENT") . '</span>' : '');?>
				</p>
			</div>
			<div class="span9 container">
				<h4 style="margin:0;"><a href="<?php echo $row->link;?>" title="<?php echo $row->intro;?>"><?php echo $row->intro;?></a></h4>
			</div>
		</div>
		<?php endforeach;?>
	</div>
	<?php endif;?>

	<?php
	$item = 1;
	foreach ($wk_options as $row):
		if ($item == 1):?>
				<div class="row-fluid"><?php
		endif;?>
		<div class="span6" style="margin-bottom:10px;">
			<a href="<?php echo $row['link'];?>" class="btn equalheight" style="display:block;padding:15px;text-align:left;min-height:60px;">
				<img src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/48px/<?php echo $row['icon'];?>" alt="<?php echo $row['title'];?>" style="float:left;padding-right:10px;padding-bottom:10px;" />
				<h4 style="margin:5px 0;"><?php echo $row['title'];?></h4>
				<small><?php echo $row['description'];?></small>
			</a>
		</div><?php
		if ($item == 2):
			$item = 0;?>
				</div><?php
		endif;
		$item++;
	endforeach;

	for ($i=0; $i<count($links); $i++):
		$link = $links[$i];
		if ($item == 1):?>
				<div class="row-fluid"><?php
		endif;?>
		<div class="span6" style="margin-bottom:10px;">
			<a href="<?php echo $link->link;?>" class="btn equalheight" style="display:block;padding:15px;text-align:left;min-height:60px;">
				<img src="<?php echo $link->icon;?>" alt="<?php echo $link->title;?>" style="float:left;padding-right:10px;padding-bottom:10px;" />
				<h4 style="margin:5px 0;"><?php echo $link->title;?></h4>
				<small><?php echo $link->description;?></small>
			</a>
		</div><?php
		if ($item == 2):
			$item = 0;?>
				</div><?php
		endif;
		$item++;
	endfor;
	// Verify close
	if ($item == 2):?>
			</div><?php
	endif;?>
	<div style="clear:both;"></div>

</div>