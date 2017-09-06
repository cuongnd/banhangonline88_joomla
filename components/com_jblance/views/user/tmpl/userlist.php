<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	11 June 2012
 * @file name	:	views/user/tmpl/userlist.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	User list page (jblance)
 */
 defined('_JEXEC') or die('Restricted access');
 
 JHtml::_('behavior.framework', true);
 JHtml::_('formbehavior.chosen', '.advancedSelect');
 
 $doc = JFactory::getDocument();
 $doc->addScript("components/com_jblance/js/utility.js");
 
 $app  = JFactory::getApplication();
 $user = JFactory::getUser();
 $letter = $app->input->get('letter', '', 'string');
 $actionLetter = (!empty($letter)) ? '&letter='.$letter : '';
 
 $action	= JRoute::_('index.php?option=com_jblance&view=user&layout=userlist'.$actionLetter);
 $actionAll	= JRoute::_('index.php?option=com_jblance&view=user&layout=userlist');
 
 $jbuser = JblanceHelper::get('helper.user');		// create an instance of the class UserHelper
 $select = JblanceHelper::get('helper.select');		// create an instance of the class SelectHelper
 
 $keyword	  = $app->input->get('keyword', '', 'string');
 $id_categ	  = $app->input->get('id_categ', array(), 'array');
 
 // Load the parameters.
 $params = $app->getParams();
 $show_search = $params->get('show_search', false);
 
 JblanceHelper::setJoomBriToken();
 ?>
<form action="<?php echo $action; ?>" method="post" name="userFormJob" class="form-inline" enctype="multipart/form-data">
<!-- show search fields if enabled -->
	<?php if($show_search) : ?>
	<div class="sp10">&nbsp;</div>
	<div class="row-fluid">
		<div class="span12">
			<div id="filter-bar" class="btn-toolbar">
				<div class="filter-search btn-group pull-left">
					<input type="text" name="keyword" id="keyword" value="<?php echo $keyword; ?>" class="input-large" placeholder="<?php echo JText::_('COM_JBLANCE_KEYWORDS'); ?>" style="margin-right: 10px;" />
					<?php 
					$attribs = 'class="input-xlarge advancedSelect" size="1"';
					$categtree = $select->getSelectCategoryTree('id_categ[]', $id_categ, 'COM_JBLANCE_ALL_CATEGORIES', $attribs, '', true);
					echo $categtree; ?>
				</div>
				<div class="btn-group pull-left">
					<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('COM_JBLANCE_SEARCH'); ?>"><i class="icon-search"></i></button>
					<a href="<?php echo $action; ?>" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></a>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<div class="jbl_h3title"><?php echo $this->escape($this->params->get('page_heading', JText::_('COM_JBLANCE_USERLIST'))); ?></div>
	<!-- hide alpha index if search form is enabled -->
	<?php if(!$show_search) : ?>
	<div class="btn-group">
	<?php
	echo JHtml::_('link', $actionAll, '#', array('title'=>JText::_('COM_JBLANCE_ALL'), 'class'=>'btn btn-mini')); 
	foreach (range('A', 'Z') as $i) :
		$link_comp_index = JRoute::_('index.php?option=com_jblance&view=user&layout=userlist&letter='.strtolower($i), false);
		if(strcasecmp($letter, $i) == 0)
			echo JHtml::_('link', $link_comp_index, $i, array('title'=>$i, 'class'=>'btn btn-mini active'));
		else
			echo JHtml::_('link', $link_comp_index, $i, array('title'=>$i, 'class'=>'btn btn-mini'));
	endforeach; ?>	
    </div>
	<div class="sp10">&nbsp;</div>
	<?php endif; ?>

	<?php
	for ($i=0, $x=count($this->rows); $i < $x; $i++){
		$row 		 = $this->rows[$i];
		$status 	 = $jbuser->isOnline($row->user_id);		//get user online status
		$viewerInfo  = $jbuser->getUserGroupInfo($user->id, null); 	// this holds the info of profile viewer
		$isFavourite = JblanceHelper::checkFavorite($row->user_id, 'profile');
		$isMine		 = ($user->id == $row->user_id);
		$link_sendpm = JRoute::_('index.php?option=com_jblance&view=message&layout=compose&username='.$row->username);
		?>
	<div class="media">
		<?php
		$attrib = 'width=48 height=48 class="img-polaroid"';
		$avatar = JblanceHelper::getThumbnail($row->user_id, $attrib);
		echo !empty($avatar) ? LinkHelper::GetProfileLink($row->user_id, $avatar, '', '', ' pull-left') : '&nbsp;' ?>
		<div class="media-body">
			<h5 class="media-heading">
				<?php $stats = ($status) ? 'online' : 'offline'; ?>
				<span class="online-status <?php echo $stats; ?>" title="<?php echo JText::_('COM_JBLANCE_'.strtoupper($stats)); ?>"></span>
				<?php echo LinkHelper::GetProfileLink($row->user_id, $row->name); ?> <small><?php echo $row->username; ?></small>
				<!-- show Add to Favorite button to others and registered users and who can post project -->
				<?php if(!$user->guest && !$isMine && $viewerInfo->allowPostProjects){ ?>
				<span  class="pull-right">
				<span id="fav-msg-<?php echo $row->user_id; ?>">
					<?php if($isFavourite > 0) : ?>
					<a onclick="favourite('<?php echo $row->user_id; ?>', -1,'profile');" href="javascript:void(0);" class="btn btn-mini btn-danger"><span class="icon-minus-sign icon-white"></span> <?php echo JText::_('COM_JBLANCE_REMOVE_FAVOURITE')?></a>
					<?php else : ?>
					<a onclick="favourite('<?php echo $row->user_id; ?>', 1,'profile');" href="javascript:void(0);" class="btn btn-mini"><span class="icon-plus-sign"></span> <?php echo JText::_('COM_JBLANCE_ADD_FAVOURITE')?></a>
					<?php endif; ?>
				</span>				
				<a class="btn btn-mini" href="<?php echo $link_sendpm; ?>"><i class="icon-comment"></i> <?php echo JText::_('COM_JBLANCE_SEND_MESSAGE'); ?></a>
				<?php } ?>
				</span>
			</h5>
			<div>
				<?php $rate = JblanceHelper::getAvarageRate($row->user_id, true); ?>
				<?php if($row->rate > 0){ ?>
				<span class="font14" style="margin-left: 10px;"><?php echo JblanceHelper::formatCurrency($row->rate, true, true, 0).'/'.JText::_('COM_JBLANCE_HR'); ?></span>
				<?php } ?>
			</div>
			<?php if(!empty($row->id_category)){ ?>
			<div class="boldfont font12">
				<?php echo JText::_('COM_JBLANCE_SKILLS'); ?>: <?php echo JblanceHelper::getCategoryNames($row->id_category); ?>
			</div>
			<?php } ?>
			<?php if(!empty($row->biz_name)) : ?>
			<strong><?php echo JText::_('COM_JBLANCE_BUSINESS_NAME'); ?>: </strong><?php echo $row->biz_name; ?>
			<?php endif; ?>
			<!-- <strong><?php echo JText::_('COM_JBLANCE_USERGROUP'); ?> : </strong><?php echo $row->grpname; ?> | 
			<?php if($status) : ?>
				<span class="label label-success"><?php echo JText::_('COM_JBLANCE_ONLINE'); ?></span>
			<?php else : ?>
				<span class="label"><?php echo JText::_('COM_JBLANCE_OFFLINE'); ?></span>
			<?php endif; ?> -->
		</div>
	</div>
	<div class="lineseparator"></div>
	<?php } ?>
	<div class="pagination pagination-centered clearfix">
		<div class="display-limit pull-right">
			<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
			<?php echo $this->pageNav->getLimitBox(); ?>
		</div>
		<?php echo $this->pageNav->getPagesLinks(); ?>
	</div>
</form> 