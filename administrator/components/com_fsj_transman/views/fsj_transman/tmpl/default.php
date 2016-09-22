<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
jimport( 'fsj_core.lib.utils.admin');
JHTML::addIncludePath(array(JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'html'));
?>
<div class="fsj">
	<div class="row-fluid">
		<div class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div class="span10">
			<div class="pull-right" style="margin-top: 9px;">
				<?php echo JText::_("com_fsj_transman_menu"); ?> v<b><?php echo FSJ_Admin_Helper::getVersion("com_fsj_transman"); ?></b>
			</div>
			<?php $overview = FSJ_AdminHelper::GetOverview('fsj_transman'); ?>
			<?php echo JHtml::_('fsjtabs.start', 'fsj_transman_overview_tabs', array('useCookie' => 0, 'default' => 'comp')); ?>
			<?php echo JHtml::_('fsjtabs.panel', JText::_("com_fsj_transman_menu"), 'comp'); ?>
			<?php if ($overview && method_exists($overview, "position_1")) echo $overview->position_1(); ?>
			<?php
			$com = "fsj_transman";
			$xmlname = str_replace("fsj_", "", $com) . ".xml";
			$xml_file = JPATH_ROOT.DS."administrator".DS."components".DS."com_".$com.DS.$xmlname;
			$xml = @simplexml_load_file($xml_file);
			if ($xml):
			?>
			<?php if ($xml && $xml->admin && $xml->admin->section) foreach ($xml->admin->section as $section): ?>
				<div class="well well-small pull-left margin-small">
					<h4><?php echo JText::_((string)$section->attributes()->name);?></h4>
					<ul class="nav nav-list">
						<?php if ($section->dropdown) foreach ($section->dropdown as $dropdown): ?>
							<li>
								<div class="btn-group" style="margin-bottom: 8px;">
									<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
										<?php echo JText::_((string)$dropdown->attributes()->name); ?>
										<span class="caret"></span>
									</a>
									<ul class="dropdown-menu">
										<?php if (isset($dropdown->code)): ?>
											<?php 
												$file = JPATH_ROOT . DS . (string)$dropdown->code->attributes()->file;
												$class = (string)$dropdown->code->attributes()->class;
												$function = (string)$dropdown->code->attributes()->function;
												if (file_exists($file))
												{
													require_once($file);
													$obj = new $class();
													echo $obj->$function();
												}	
											?>
										<?php endif; ?>
										<?php foreach ($dropdown->item as $item): ?>
											<li>
												<a href="<?php echo JRoute::_((string)$item->attributes()->url); ?>">
													<?php echo JText::_((string)$item->title); ?>
												</a>
											</li>
										<?php endforeach; ?>											
									</ul>
								</div>
							</li>
						<?php endforeach; ?>
						<?php foreach ($section->item as $item): ?>
							<?php
								$component = (string)$item->attributes()->component;
								$url = (string)$item->attributes()->url;
								$id = (string)$item->attributes()->id;
								$icon = (string)$item->attributes()->icon;
							?>
							<?php if ($id == "spacer"): ?>
								<li class="divider" />	
							<?php elseif ($url): ?>
								<?php echo FSJ_AdminHelper::Item((string)$item->title,$url,'com_fsj_'.$component,$icon,(string)$item->description); ?>
							<?php elseif ($component): ?>
								<?php echo FSJ_AdminHelper::Item((string)$item->title,"index.php?option=com_fsj_{$component}&admin_com=".str_replace("fsj_", "", $com)."&view={$id}s",'com_fsj_'.$component,$icon,(string)$item->description); ?>
							<?php else: ?>
								<?php echo FSJ_AdminHelper::Item((string)$item->title,"index.php?option=com_{$com}&view={$id}s","com_".$com,$icon,(string)$item->description); ?>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>
			<?php endif; ?>
			<?php if ($overview && method_exists($overview, "position_2")) echo $overview->position_2(); ?>
			<?php 
			$showsettings = true;
			if ($overview && method_exists($overview, "has_settings")) $showsettings = $overview->has_settings();
			if ($showsettings): ?>
			<?php if (JFactory::getUser()->authorise('core.admin', 'com_fsj_main') || JFactory::getUser()->authorise('core.admin', 'com_fsj_transman')): ?>
				<div class="well well-small pull-left margin-small">
					<h4><?php echo JText::_('FSJ_ADMIN_SETTINGS');?></h4>
					<ul class="nav nav-list">
			<?php if (JFactory::getUser()->authorise('core.admin', 'com_fsj_main')): ?>
						<?php echo FSJ_AdminHelper::Item('FSJ_ADMIN_GLOBAL_SETTINGS',"index.php?option=com_fsj_main&view=settings&admin_com=transman&settings=global",'com_fsj_main','globalsettings',JText::_('FSJ_ADMIN_GLOBAL_SETTINGS_DESC')); ?>
			<?php endif; ?>
			<?php if (JFactory::getUser()->authorise('core.admin', 'com_fsj_transman')): ?>
						<?php echo FSJ_AdminHelper::Item('FSJ_ADMIN_COMPONENT_SETTINGS',"index.php?option=com_fsj_main&view=settings&admin_com=transman",'com_fsj_main','componentsettings',JText::_('FSJ_ADMIN_COMPONENT_SETTINGS_DESC')); ?>
			<?php endif; ?>
					</ul>
				</div>
			<?php endif; ?>
			<?php endif; ?>
				<?php if ($overview && method_exists($overview, "position_3")) echo $overview->position_3(); ?>
			<?php if (JFactory::getUser()->authorise('core.admin', 'com_fsj_transman') && isset($this->xml->admin->templates)): ?>
				<div class="well well-small pull-left margin-small">
					<h4><?php echo JText::_('FSJ_ADMIN_TEMPLATES');?></h4>
					<ul class="nav nav-list">
						<?php foreach ($this->xml->admin->templates->template as $template): ?>
							<?php if (array_key_exists((string)$template, $this->templates)): ?>
								<li>
									<a href="<?php echo JRoute::_('index.php'); ?>">
										<img src="<?php echo JURI::root(); ?>libraries/fsj_core/assets/images/general/edit-16.png" width="16" height="16">
										<span><?php echo $this->templates[(string)$template]->title; ?></span>
									</a>
								</li>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>	
			<?php if ($overview && method_exists($overview, "position_4")) echo $overview->position_4(); ?>
			<?php echo JHtml::_('fsjtabs.panel', JText::_('FSJ_M_HEADER'), 'components'); ?>
			<?php include (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_main'.DS.'views'.DS.'fsj_main'.DS.'tmpl'.DS.'default_components.php'); ?>
			<?php include (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_main'.DS.'views'.DS.'fsj_main'.DS.'tmpl'.DS.'default_global.php'); ?>
			<?php echo JHtml::_('fsjtabs.end'); ?>
		</div>
	</div>
</div>
