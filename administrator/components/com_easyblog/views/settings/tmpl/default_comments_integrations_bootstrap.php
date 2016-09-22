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
	<div class="span12">
		<div class="span6">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_OTHER_COMMENT_TITLE' ); ?></legend>
			<p><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_OTHER_COMMENT_DESC');?></p>
			<table class="admintable" cellspacing="1">
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_MULTIPLE_SYSTEM' ); ?>
						</span>
					</td>
					<td valign="top" class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_MULTIPLE_SYSTEM_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_comment_multiple' , $this->config->get( 'main_comment_multiple' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_BUILTIN_COMMENTS' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_BUILTIN_COMMENTS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'comment_easyblog' , $this->config->get( 'comment_easyblog' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_EASYSOCIAL_COMMENTS' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_EASYSOCIAL_COMMENTS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'comment_easysocial' , $this->config->get( 'comment_easysocial' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_FACEBOOK_COMMENTS' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_FACEBOOK_COMMENTS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'comment_facebook' , $this->config->get( 'comment_facebook' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_INTENSE_DEBATE' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_INTENSE_DEBATE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'comment_intensedebate' , $this->config->get( 'comment_intensedebate' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_INTENSE_DEBATE_CODE' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_INTENSE_DEBATE_CODE_DESC' ); ?></div>
							<input type="text" class="input inputbox" style="width: 75%;" value="<?php echo $this->config->get( 'comment_intensedebate_code' );?>" name="comment_intensedebate_code" />
							<a href="http://stackideas.com/docs/easyblog/how-tos/148-setting-up-intensedebate-integrations"><?php echo JText::_('COM_EASYBLOG_WHAT_IS_THIS'); ?></a>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_DISQUS' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_DISQUS_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'comment_disqus' , $this->config->get( 'comment_disqus' ) );?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_DISQUS_CODE' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_DISQUS_CODE_DESC' ); ?></div>
							<input type="text" name="comment_disqus_code" class="inputbox" value="<?php echo $this->config->get('comment_disqus_code');?>" style="width: 75%;" />
							<a href="http://stackideas.com/docs/easyblog/how-tos/147-setting-up-disqus-integrations"><?php echo JText::_('COM_EASYBLOG_WHAT_IS_THIS'); ?></a>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_JOMCOMMENT' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_JOMCOMMENT_DESC' ); ?></div>
							<?php
								if($this->jcInstalled)
								{
									echo $this->renderCheckbox( 'comment_jomcomment' , $this->config->get( 'comment_jomcomment' ) );
								}
								else
								{
								?>
								<div class="pt-5"><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_JOMCOMMENT_NOT_FOUND'); ?></div>
								<?php
								}
							?>
						</div>

					</td>
				</tr>

				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_COMPOJOOM' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_COMPOJOOM_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'comment_compojoom' , $this->config->get( 'comment_compojoom' ) ); ?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_JCOMMENT' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_JCOMMENT_DESC' ); ?></div>
							<?php
								if($this->jcommentInstalled)
								{
									echo $this->renderCheckbox( 'comment_jcomments' , $this->config->get( 'comment_jcomments' ) );
								}
								else
								{
								?>
								<div class="pt-5"><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_JCOMMENT_NOT_FOUND'); ?></div>
								<?php
								}
							?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_RSCOMMENTS' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_RSCOMMENTS_DESC' ); ?></div>
							<?php
								if($this->rscommentInstalled)
								{
									echo $this->renderCheckbox( 'comment_rscomments' , $this->config->get( 'comment_rscomments' ) );
								}
								else
								{
								?>
								<div class="pt-5"><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RSCOMMENTS_NOT_FOUND'); ?></div>
								<?php
								}
							?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_EASYDISCUSS' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_EASYDISCUSS_DESC' ); ?></div>
							<?php if( $this->easydiscuss ){ ?>
								<?php echo $this->renderCheckbox( 'comment_easydiscuss' , $this->config->get( 'comment_easydiscuss' ) ); ?>
							<?php } else { ?>
								<div class="pt-5"><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_EASYDISCUSS_NOT_INSTALLED'); ?></div>
							<?php } ?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_KOMENTO' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_KOMENTO_DESC' ); ?></div>
							<?php if( $this->komento ){ ?>
								<?php echo $this->renderCheckbox( 'comment_komento' , $this->config->get( 'comment_komento' ) ); ?>
							<?php } else { ?>
								<div class="pt-5"><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_KOMENTO_NOT_INSTALLED'); ?></div>
							<?php } ?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_LIVEFYRE' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_LIVEFYRE_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'comment_livefyre' , $this->config->get( 'comment_livefyre' ) ); ?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_LIVEFYRE_SITEID' ); ?>
						</span>
					</td>
					<td valign="top">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_KOMENTO_DESC' ); ?></div>
							<input type="text" name="comment_livefyre_siteid" class="inputbox" value="<?php echo $this->config->get('comment_livefyre_siteid');?>" style="width: 150px;" />
							<a href="http://stackideas.com/docs/easyblog/comments/285-integrating-with-livefyre" target="_blank"><?php echo JText::_('COM_EASYBLOG_WHAT_IS_THIS'); ?></a>
						</div>
					</td>
				</tr>
			</table>
			</fieldset>
		</div>

		<div class="span6">
		</div>

	</div>
</div>
