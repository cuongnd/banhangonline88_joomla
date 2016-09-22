<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>
<div class="ui-modbox" id="widget-profile-blog">
	<div class="ui-modhead">
		<div class="ui-modtitle"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_BLOGGER_SETTINGS_TITLE'); ?></div>
		<a href="javascript:void(0);" onclick="eblog.dashboard.toggle( this );" class="ui-tog pabs atr ir"><?php echo JText::_( 'COM_EASYBLOG_HIDE' );?></a>
	</div>
	<div class="ui-modbody clearfix">
		<div class="eblog-message info">
			<span><?php echo JText::_('COM_EASYBLOG_DASHBOARD_BLOGGER_SETTINGS_DESC'); ?></span>
		</div>
		<ul class="list-form reset-ul">
			<?php if($this->acl->rules->add_entry) : ?>
			<li>
				<label for="title"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_BLOGGER_BLOG_TITLE'); ?> :</label>
				<div><input type="text" class="input text width-half" id="title" name="title" value="<?php echo $this->escape( $profile->title );?>" /></div>
			</li>
			<li>
				<label><?php echo JText::_('COM_EASYBLOG_DASHBOARD_BLOGGER_BLOG_DESC'); ?> :</label>
				<div>
					<textarea name="description" class="input textarea width-full"><?php echo $profile->getDescription();?></textarea>
				</div>
			</li>
			<?php endif; ?>
			<li>
				<label><?php echo JText::_('COM_EASYBLOG_DASHBOARD_BLOGGER_BIOGRAPHICAL_INFO'); ?> :</label>
				<div>
        	    	<?php if( $system->config->get( 'layout_dashboard_biography_editor' ) ){ ?>
        	    		<?php echo $editor->display('biography', $profile->getBiography() , '300', '300', '10', '10', array( 'image' ,'readmore' , 'pagebreak' , 'jcommentsoff' , 'jcommentson') ); ?>
        	    	<?php } else { ?>
                    	<textarea name="biography" class="input textarea width-full"><?php echo $profile->getBiography(true);?></textarea>
                    <?php } ?>
				</div>
			</li>
			<li>
				<label for="url"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_BLOGGER_WEBSITE'); ?> :</label>
				<div><input class="input text width-250" id="url" type="text" name="url" size="50" value="<?php echo $this->escape( $profile->url ); ?>" /></div>
			</li>
			<?php if( $this->acl->rules->add_entry ){?>
			<li>
				<label for="user_permalink"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_BLOGGER_PERMALINK'); ?> :</label>
				<div>
					<?php if( JPluginHelper::isEnabled( 'system' , 'blogurl') ){ ?>
						<span style="line-height: 28px;"><?php echo JURI::root(); ?></span>
					<?php } ?>
					<input type="text" id="user_permalink" name="user_permalink" class="input text width-half" value="<?php echo $profile->permalink; ?>" />
					<div class="small"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_NOTICE_PERMALINK_USAGE')?></div>
				</div>
			</li>
			<?php } ?>
			<?php if($multithemes->enable){ ?>
			<li>
				<label for="theme"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_BLOGGER_SELECT_THEME'); ?> :</label>
				<div>
				<?php

					$options[] = JHTML::_('select.option', 'global', JText::_('COM_EASYBLOG_DASHBOARD_THEMES_THEME_GLOBAL'));

					if(!empty($multithemes->availableThemes))
					{
						foreach($multithemes->availableThemes as $theme)
						{
							$themeName = JText::_('COM_EASYBLOG_DASHBOARD_THEMES_THEME_'.$theme);

							// No language found, revert to default
							if ($themeName=='COM_EASYBLOG_DASHBOARD_THEMES_THEME_'.$theme) {
								$themeName = $theme;
							}

							$options[] = JHTML::_('select.option', $theme, $themeName);
						}
					}
					echo JHTML::_('select.genericlist', $options, 'theme', 'class="input select width-half"', 'value', 'text', $multithemes->selectedTheme );
					?>
				</div>
			</li>
			<?php } ?>
		</ul>
	</div>
</div>
