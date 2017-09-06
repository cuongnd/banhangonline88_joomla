<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	14 March 2012
 * @file name	:	views/admconfig/tmpl/config.php
 * @copyright   :	Copyright (C) 2012. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Shows list of Users (jblance)
 */
 defined('_JEXEC') or die('Restricted access');
 
 ?>
<form action="index.php" method="post" name="adminForm">
<div id="j-sidebar-container" class="span2">
	<?php include_once(JPATH_COMPONENT.'/views/configmenu.php'); ?>
</div>
<div id="j-main-container" class="span10">
	<div class="alert alert-error">
		<h4><?php echo JText::_('COM_JBLANCE_OPTIMISE_JOOMBRI_DATABASE');?></h4>
		<p><?php echo JText::_('COM_JBLANCE_OPTIMISE_JOOMBRI_DATABASE_DESC');?></p>
	</div>
	<div class="row-fluid">
		<div class="span6">
			<div class="well well-small">
				<h2 class="module-title nav-header"><?php echo JText::_('COM_JBLANCE_PENDING_ACTIONS'); ?></h2>
				<div class="row-striped">
				<?php
				if(count($this->results) > 0){
					foreach($this->results as $result){ ?>
					<div class="row-fluid">
						<div class="span12">
						<?php echo $result; ?>
						</div>
					</div>
					<?php
					}
				}
				else { ?>
					<div class="row-fluid">
						<div class="span12">
							<div class="alert"><?php echo JText::_('COM_JBLANCE_NOTHING_TO_OPTIMISE'); ?></div>
						</div>
					</div>
			<?php 
			}?>
				</div>
			</div>
		</div>
		<?php if(count($this->results) > 0){ ?>
		<div class="span6">
			<div class="well well-small">
				<h4><?php echo JText::_('COM_JBLANCE_OPTIMISE'); ?></h4>
				<p><?php echo JText::_('COM_JBLANCE_OPTIMISE_CONFIRM'); ?></p>
				<p><input class="btn btn-danger" type="button" name="submit_app" value="<?php echo JText::_('COM_JBLANCE_OPTIMISE'); ?>" onclick="this.form.submit()"/></p>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
	<input type="hidden" name="option" value="com_jblance" />
	<input type="hidden" name="task" value="admconfig.optimise" />
	<input type="hidden" name="userIds" value="<?php echo $this->userIds; ?>" />
	<input type="hidden" name="projectIds" value="<?php echo $this->projectIds; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>