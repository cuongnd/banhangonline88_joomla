<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Category
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/

/** @var KunenaForumCategory $section */
/** @var KunenaForumCategory $category */
/** @var KunenaForumCategory $subcategory */

defined('_JEXEC') or die;

if ($this->config->enableforumjump)
{
	echo $this->subLayout('Widget/Forumjump')->set('categorylist', $this->categorylist);
}

$mmm = 0;
$config = KunenaFactory::getTemplate()->params;

if ($config->get('displayModule'))
{
	echo $this->subLayout('Widget/Module')->set('position', 'kunena_index_top');
}

foreach ($this->sections as $section) :
	$markReadUrl = $section->getMarkReadUrl();

	if ($config->get('displayModule'))
	{
	 echo $this->subLayout('Widget/Module')->set('position', 'kunena_section_top_' . ++$mmm);
	}
	?>
	<div class="kfrontend">
		<h2 class="btn-toolbar pull-right">
			<?php if (count($this->sections) > 0) : ?>
				<div class="btn btn-small" data-toggle="collapse" data-target="#section<?php echo $section->id; ?>"></div>
			<?php endif; ?>
		</h2>

		<h1>
			<?php echo $this->getCategoryLink($section, $this->escape($section->name), null, 'hasTooltip', true, false); ?>
			<small class="hidden-phone nowrap">
				<?php if ($section->getTopics() > 0) : ?>
					(<?php echo JText::plural('COM_KUNENA_X_TOPICS_MORE', $this->formatLargeNumber($section->getTopics())); ?>)
				<?php else : ?>
					(<?php echo JText::_('COM_KUNENA_X_TOPICS_0'); ?>)
				<?php endif; ?>
			</small>
		</h1>

		<div class="row-fluid collapse in section section<?php echo $this->escape($section->class_sfx); ?>" id="section<?php echo $section->id; ?>">
			<table class="table<?php echo KunenaTemplate::getInstance()->borderless();?>">
				<?php if (!empty($section->description)) : ?>
					<thead class="hidden-phone">
						<tr>
							<td colspan="3">
								<div class="header-desc"><?php echo $section->displayField('description'); ?></div>
							</td>
						</tr>
					</thead>
				<?php endif; ?>

				<?php if ($section->isSection() && empty($this->categories[$section->id]) && empty($this->more[$section->id])) : ?>
					<tr>
						<td>
							<h4>
								<?php echo JText::_('COM_KUNENA_GEN_NOFORUMS'); ?>
							</h4>
						</td>
					</tr>
				<?php else : ?>
					<?php if (!empty($this->categories[$section->id])) : ?>
						<tr>
							<td colspan="2" class="hidden-phone">
								<div class="header-desc"><?php echo JText::_('COM_KUNENA_GEN_CATEGORY'); ?></div>
							</td>
							<td colspan="1" class="hidden-phone post-info">
								<?php echo JText::_('COM_KUNENA_GEN_LAST_POST'); ?>
							</td>
						</tr>
					<?php endif; ?>
					<?php
					foreach ($this->categories[$section->id] as $category) : ?>
						<tr class="category<?php echo $this->escape($category->class_sfx); ?>" id="category<?php echo $category->id; ?>">
							<td class="span1 center hidden-phone">
								<?php echo $this->getCategoryLink($category, $this->getCategoryIcon($category), '', null, true, false); ?>
							</td>
							<td class="span8">
								<div>
									<h3>
										<?php echo $this->getCategoryLink($category, null, null, null, true, false); ?>
										<small class="hidden-phone nowrap">
											<?php if ($category->getTopics() > 0) : ?>
												(<?php echo JText::plural('COM_KUNENA_X_TOPICS_MORE', $this->formatLargeNumber($category->getTopics())); ?>)
											<?php else : ?>
												(<?php echo JText::_('COM_KUNENA_X_TOPICS_0'); ?>)
											<?php endif; ?>
											<span>
												<?php if (($new = $category->getNewCount()) > 0) : ?>
													<sup class="knewchar"> (<?php echo $new . ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR'); ?>)</sup>
												<?php endif; ?>
												<?php if ($category->locked) : ?>
													<span class="icon-lock hasTooltip" data-original-title="<?php echo JText::_('COM_KUNENA_LOCKED_CATEGORY'); ?>"></span>
												<?php endif; ?>
												<?php if ($category->review) : ?>
													<span class="icon-shield hasTooltip" data-original-title="<?php echo JText::_('COM_KUNENA_GEN_MODERATED'); ?>"></span>
												<?php endif; ?>
												<?php if (KunenaFactory::getConfig()->enablerss) : ?>
													<a href="<?php echo $this->getCategoryRSSURL($category->id); ?>" rel="alternate" type="application/rss+xml" data-original-title="<?php echo JText::_('COM_KUNENA_LISTCAT_RSS');?>">
														 <?php echo KunenaIcons::rss(); ?>
													</a>
												<?php endif; ?>
											</span>
										</small>
									</h3>
								</div>

								<?php if (!empty($category->description)) : ?>
									<div class="hidden-phone header-desc"><?php echo $category->displayField('description'); ?></div>
								<?php endif; ?>

								<?php
								// Display subcategories
								if (!empty($this->categories[$category->id])) : ?>
									<div class="subcategories">
										<ul class="inline">

											<?php foreach ($this->categories[$category->id] as $subcategory) : ?>
												<li>
													<?php $totaltopics = $subcategory->getTopics() > 0 ?  JText::plural('COM_KUNENA_X_TOPICS_MORE', $this->formatLargeNumber($subcategory->getTopics())) : JText::_('COM_KUNENA_X_TOPICS_0'); ?>

													<?php echo $this->getCategoryLink($subcategory, $this->getSmallCategoryIcon($subcategory), '', null, true, false) . $this->getCategoryLink($subcategory, '', null, null, true, false) . '<small class="hidden-phone muted"> ('
														. $totaltopics . ')</small>';

													if (($new = $subcategory->getNewCount()) > 0)
													{
														echo '<sup class="knewchar">(' . $new . ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>';
													}
													?>
												</li>
											<?php endforeach; ?>

											<?php if (!empty($this->more[$category->id])) : ?>
												<li>
													<?php echo $this->getCategoryLink($category, JText::_('COM_KUNENA_SEE_MORE'), null, null, true, false); ?>
													<small class="hidden-phone muted">
														(<?php echo JText::sprintf('COM_KUNENA_X_HIDDEN', (int) $this->more[$category->id]); ?>)
													</small>
												</li>
											<?php endif; ?>

										</ul>
									</div>
									<div class="clearfix"></div>
								<?php endif; ?>

								<?php if ($category->getmoderators() && KunenaConfig::getInstance()->listcat_show_moderators) : ?>
									<br />
									<div class="moderators">
										<?php
										// get the Moderator list for display
										$modslist = array();
										foreach ($category->getmoderators() as $moderator)
										{
											$modslist[] = KunenaFactory::getUser($moderator)->getLink(null, null, '');
										}

										echo JText::_('COM_KUNENA_MODERATORS') . ': ' . implode(', ', $modslist);
										?>
									</div>
								<?php endif; ?>

								<?php if (!empty($this->pending[$category->id])) : ?>
									<div class="alert" style="margin-top:20px;">
										<a class="alert-link" href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topics&layout=posts&mode=unapproved&userid=0&catid=' . intval($category->id)); ?>" title="<?php echo JText::_('COM_KUNENA_SHOWCAT_PENDING')?>" rel="nofollow"><?php echo intval($this->pending[$category->id]) . ' ' . JText::_('COM_KUNENA_SHOWCAT_PENDING')?></a>
									</div>
								<?php endif; ?>
							</td>

							<?php $last = $category->getLastTopic(); ?>

							<?php if ($last->exists()) :
								$author = $last->getLastPostAuthor();
								$time   = $last->getLastPostTime();
								$avatar = $this->config->avataroncat ? $author->getAvatarImage(KunenaFactory::getTemplate()->params->get('avatarType'), 'post') : null;
							?>

								<td class="span3 hidden-phone">
									<div class="container-fluid">
										<div class="row-fluid">
											<?php if ($avatar) : ?>
												<div class="span3">
													<?php echo $author->getLink($avatar); ?>
												</div>
												<div class="span9">
											<?php else : ?>
												<div class="span12">
											<?php endif; ?>
												<span><?php echo $this->getLastPostLink($category,null, null, null, null, false, true) ?></span>
												<br>
												<span><?php echo JText::sprintf('COM_KUNENA_BY_X', $author->getLink(null, '', '', '', null, $category->id)); ?></span>
												<br>
												<span><?php echo $time->toKunena('config_post_dateformat'); ?></span>
											</div>
										</div>
									</div>
								</td>
							<?php else : ?>
								<td class="span3 hidden-phone">
									<div class="last-post-message">
										<?php echo JText::_('COM_KUNENA_X_TOPICS_0'); ?>
									</div>
								</td>
							<?php endif; ?>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>

				<?php if (!empty($this->more[$section->id])) : ?>
					<tr>
						<td colspan="3">
							<h4>
								<?php echo $this->getCategoryLink($section, JText::sprintf('COM_KUNENA_SEE_ALL_SUBJECTS')); ?>
								<small>(<?php echo JText::sprintf('COM_KUNENA_X_HIDDEN', (int) $this->more[$section->id]); ?>)</small>
							</h4>
						</td>
					</tr>
				<?php endif; ?>

			</table>
		</div>
	</div>
	<!-- Begin: Category Module Position -->
	<?php
	if ($config->get('displayModule'))
	{
		echo $this->subLayout('Widget/Module')->set('position', 'kunena_section_' . ++$mmm);
	} ?>
	<!-- Finish: Category Module Position -->
<?php endforeach;

if ($config->get('displayModule'))
{
	echo $this->subLayout('Widget/Module')->set('position', 'kunena_index_bottom');
}
