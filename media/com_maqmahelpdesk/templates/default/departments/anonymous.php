<div class="maqmahelpdesk container-fluid">

	<h2><?php echo $workgroupSettings->wkdesc;?></h2>

    <?php echo $workgroupSettings->wkabout;?>

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

    <div class="alert">
        <p><img src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/alert.png"
                align="left" style="padding-right:5px;"/> <?php echo JText::_('wk_anonymous_warning');?></p>
    </div>

    <?php if ($supportConfig->show_login_form): ?>
    <form action="<?php echo JRoute::_("index.php");?>" method="post" name="login" id="form-login"
          class="well form-inline">
        <?php echo JHtml::_('form.token'); ?>
        <?php echo JText::_('Username') ?>: <input id="modlgn_username" type="text" name="username" class="input-small"
                                                   alt="" placeholder="<?php echo JText::_('username');?>"/>
        <?php echo JText::_('Password') ?>: <input id="modlgn_passwd" type="password"
                                                   name="password"
                                                   class="input-small" alt=""
                                                   placeholder="<?php echo JText::_('password');?>"/>
        <input type="submit" name="Submit" class="btn" value="<?php echo JText::_('LOGIN') ?>"/>
        <input type="hidden" name="option" value="com_users"/>
        <input type="hidden" name="task" value="user.login"/>
        <input type="hidden" name="return"
               value="<?php echo base64_encode($url->toString()); ?>"/>
        <?php echo JHTML::_('form.token'); ?>
    </form>
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