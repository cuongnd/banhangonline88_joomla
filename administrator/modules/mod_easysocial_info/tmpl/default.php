<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="es">
	<div class="es-backend-module-wrap">
		<?php if ($showCounterHeader) { ?>
		<div class="mod-state">
			<?php if (isset($totalUsers)) { ?>
			<div class="mod-state__item">
				<a href="index.php?option=com_easysocial&view=users">
					<?php echo $totalUsers;?>
					<div class="mod-state__item-name"><?php echo JText::_('MOD_ES_INFO_USERS');?></div>
				</a>
			</div>
			<?php } ?>
			<?php if (isset($totalPendingUsers)) { ?>
			<div class="mod-state__item">
				<a href="index.php?option=com_easysocial&view=users&layout=pending">
					<?php echo $totalPendingUsers;?>
					<div class="mod-state__item-name"><?php echo JText::_('MOD_ES_INFO_PENDING_APPROVALS');?></div>
				</a>
			</div>
			<?php } ?>
			<?php if (isset($totalPages)) { ?>
			<div class="mod-state__item">
				<a href="index.php?option=com_easysocial&view=pages">
					<?php echo $totalPages;?> 
					<div class="mod-state__item-name"><?php echo JText::_('MOD_ES_INFO_PAGES');?></div>
				</a>
			</div>
			<?php } ?>
			<?php if (isset($totalGroups)) { ?>
			<div class="mod-state__item">
				<a href="index.php?option=com_easysocial&view=groups">
					<?php echo $totalGroups;?>
					<div class="mod-state__item-name"><?php echo JText::_('MOD_ES_INFO_GROUPS');?></div>
				</a>
			</div>
			<?php } ?>
			<?php if (isset($totalEvents)) { ?>
			<div class="mod-state__item">
				<a href="index.php?option=com_easysocial&view=events">
					<?php echo $totalEvents;?>
					<div class="mod-state__item-name"><?php echo JText::_('MOD_ES_INFO_EVENTS');?></div>
				</a>
			</div>
			<?php } ?>
			<?php if (isset($totalAlbums)) { ?>
			<div class="mod-state__item">
				<a href="index.php?option=com_easysocial&view=albums">
					<?php echo $totalAlbums; ?> 
					<div class="mod-state__item-name"><?php echo JText::_('MOD_ES_INFO_ALBUMS');?></div>
				</a>
			</div>
			<?php } ?>
			<?php if (isset($totalVideos)) { ?>
			<div class="mod-state__item">
				<a href="index.php?option=com_easysocial&view=videos">
					<?php echo $totalVideos;?> 
					<div class="mod-state__item-name"><?php echo JText::_('MOD_ES_INFO_VIDEOS');?></div>
				</a>
			</div>
			<?php } ?>
			<?php if (isset($totalReports)) { ?>
			<div class="mod-state__item">
				<a href="index.php?option=com_easysocial&view=reports">
					<?php echo $totalReports;?>
					<div class="mod-state__item-name"><?php echo JText::_('MOD_ES_INFO_REPORTS');?></div>
				</a>
			</div>
			<?php } ?>
		</div>
		<?php } ?>

		<div class="o-row">
			<div class="o-col--7 o-col--top t-lg-pr--lg t-xs-pr--no">

				<div class="es-mod es-mod--info mod-knowledge" style="position: relative;">
					<div class="es-mod__title">
						<?php echo JText::_('MOD_ES_INFO_FROM_THE_DECK'); ?>
						<a href="https://stackideas.com/blog" class="t-lg-pull-right" target="_blank"><?php echo JText::_('MOD_ES_INFO_VIEW_ALL_POSTS');?> &rarr;</a>
					</div>

					<div class="es-mod__news-item is-loading" data-news-loading>
						<div class="es-mod__news-item-desc t-text--center">
							<b class="o-loader"></b>
						</div>
					</div>

					<?php echo $lib->html('html.emptyBlock', 'MOD_ES_INFO_EMPTY_NEWS', 'fa-newspaper-o'); ?>

					<div data-news-result>
					</div>

					<div data-news-template>
						<div class="es-mod__news-item t-hidden">
							<a href="javascript:void(0);" data-permalink target="_blank">
								<div class="es-mod__news-item-title" data-title></div>
								<div class="t-text--muted t-fs--sm" data-date></div>
							</a>

							<div class="es-mod__news-item-desc">
								<a href="javascript:void(0);" data-permalink target="_blank"><img src="" align="right" data-image /></a>
								<div data-content></div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="o-col--5 o-col--top">

				<div class="es-mod-version panel">
					<div class="panel-body dash-summary">
						<section class="dash-version is-loading" data-version-status>
							<div class="row-table">
								<div class="col-cell cell-icon cell-tight">
									<i class="fa fa-thumbs-down"></i>
									<i class="fa fa-thumbs-up"></i>
								</div>

								<div class="col-cell">
									<h4 class="heading-outdated text-danger"><?php echo JText::_('COM_EASYSOCIAL_VERSION_OUTDATED_VERSION_INFO');?></h4>
									<h4 class="heading-updated"><?php echo JText::_('COM_EASYSOCIAL_VERSION_HEADER_UP_TO_DATE');?></h4>
									<h4 class="heading-loading"><?php echo JText::_('COM_EASYSOCIAL_CHECKING_VERSIONS');?></h4>
									<div class="version-installed hide" data-version-installed>
										<?php echo JText::_('COM_EASYSOCIAL_VERSION_INSTALLED_VERSION');?>: <span data-current-version></span>
										<span class="version-latest text-success">&nbsp; <?php echo JText::_('COM_EASYSOCIAL_VERSION_LATEST_VERSION');?>: <span data-latest-version></span></span>
									</div>

									<div class="cell-btn t-lg-mt--lg">
										<a href="<?php echo JRoute::_('index.php?option=com_easysocial&launchInstaller=1');?>" class="btn btn-default"><?php echo JText::_('COM_EASYSOCIAL_GET_UPDATES_BUTTON');?></a>
									</div>
								</div>

								
							</div>
						</section>
					</div>
				</div>

				<?php if (isset($pendingUsers) && $pendingUsers) { ?>
				<div class="es-mod mod-pending-users <?php echo !$pendingUsers ? 'is-empty' : ''; ?>">
					<div class="es-mod__title">
						<?php echo JText::_('MOD_ES_INFO_PENDING_USERS');?> <span class="es-mod__bubble"><?php echo $totalPending;?></span>
					</div>

					
					<?php foreach ($pendingUsers as $user) { ?>
					<div class="es-mod__list-item">
						<div class="o-flag">
							<div class="o-flag__image o-flag--top">
								<a href="index.php?option=com_easysocial&view=users&layout=form&id=<?php echo $user->id;?>" class="o-avatar o-avatar--sm">
									<img src="<?php echo $user->getAvatar();?>" />
								</a>
							</div>
							<div class="o-flag__body">
								<div class="pull-left">
									<a href="index.php?option=com_easysocial&view=users&layout=form&id=<?php echo $user->id;?>"><?php echo $user->getName();?> (<?php echo $user->getProfile()->getTitle();?>)</a>
									<div class="t-text--muted">
										<?php echo JText::sprintf('MOD_ES_INFO_REGISTERED_ON', $user->getRegistrationDate()->format(JText::_('DATE_FORMAT_LC3')));?>
									</div>
								</div>

								<div class="pull-right">
									<a href="javascript:void(0);" class="btn-mod-primary" data-approve data-id="<?php echo $user->id;?>"><?php echo JText::_('MOD_ES_INFO_APPROVE');?></a>
									<a href="javascript:void(0);" class="btn-mod-danger t-lg-ml--md" data-reject data-id="<?php echo $user->id;?>"><?php echo JText::_('MOD_ES_INFO_REJECT');?></a>    
								</div>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
				<?php } ?>

				<?php if (isset($recentUsers) && $recentUsers) { ?>
				<div class="es-mod mod-recent-users">
					<div class="es-mod__title">
						<?php echo JText::_('MOD_ES_INFO_RECENT_USERS');?>
						<a href="index.php?option=com_easysocial&view=users" class="t-lg-pull-right"><?php echo JText::_('MOD_ES_INFO_VIEW_ALL');?> &rarr;</a>
					</div>

					<?php foreach ($recentUsers as $user) { ?>
					<div class="es-mod__list-item">
						<div class="o-flag">
							<div class="o-flag__image o-flag--top">
								<a href="index.php?option=com_easysocial&view=users&layout=form&id=<?php echo $user->id;?>" class="o-avatar o-avatar--sm">
									<img src="<?php echo $user->getAvatar();?>" />
								</a>
							</div>
							<div class="o-flag__body">
								<div class="pull-left">
									<a href="index.php?option=com_easysocial&view=users&layout=form&id=<?php echo $user->id;?>"><?php echo $user->getName();?> (<?php echo $user->getProfile()->getTitle();?>)</a>
									<div class="t-text--muted">
										<?php echo JText::sprintf('MOD_ES_INFO_REGISTERED_ON', $user->getRegistrationDate()->format(JText::_('DATE_FORMAT_LC3')));?>
									</div>
								</div>

								<?php if ($user->location) { ?>
								<div class="pull-right t-text--muted">
									<i class="fa fa-map-marker t-lg-mr--sm"></i> <?php echo $user->location;?>
								</div>
								<?php } ?>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
EasySocial.ready(function($) {

	$(document).on('click.approve.user', '[data-approve]', function() {
		var id = $(this).data('id');

		EasySocial.dialog({
			"content": EasySocial.ajax('admin/views/users/confirmApprove', {"id": id}),
			"bindings": {

				"{approveButton} click" : function() {
					this.approveUserForm().submit();
				}
			}
		});
	});


	$(document).on('click.reject.user', '[data-reject]', function() {
		var id = $(this).data('id');

		EasySocial.dialog({
			"content": EasySocial.ajax('admin/views/users/confirmReject', {"id" : id})
		});
	});

	// Get the current version of EasySocial
	$.ajax({
		url: "<?php echo SOCIAL_SERVICE_VERSION;?>",
		jsonp: "callback",
		dataType: "jsonp",
		data: {
			"apikey": "<?php echo $config->get('general.key');?>",
			"version": "<?php echo $version;?>"
		},
		success: function(data) {

			// Update the latest version
			$('[data-latest-version]').html(data.version);

			var versionSection = $('[data-version-status]');
			var currentVersion = $('[data-current-version]');
			var installedSection = $('[data-version-installed]');

			var version = {
				"latest": data.version,
				"installed": "<?php echo $version;?>"
			};

			var outdated = EasySocial.compareVersion(version.installed, version.latest) === -1;

			if (versionSection.length > 0) {
				currentVersion.html(version.installed);
				installedSection.removeClass('hide');
				versionSection.removeClass('is-loading');

				// Update version checking
				if (outdated) {
					versionSection.addClass('is-outdated');
				} else {
					versionSection.addClass('is-updated');
				}
			}

			var news = data.news;

			if (news) {
				$.each(news, function(i, article) {
					var wrapper = $('[data-news-template]').clone();
					var tmpl = $(wrapper.html());
					
					// Hide loading
					$('[data-news-loading]').addClass('t-hidden');

					tmpl.removeClass('t-hidden');
					tmpl.find('[data-date]').html(article.date);
					tmpl.find('[data-image]').attr('src', article.image);
					tmpl.find('[data-permalink]').attr('href', article.permalink);
					tmpl.find('[data-title]').html(article.title);
					tmpl.find('[data-content]').html(article.content);

					$('[data-news-result]').append(tmpl);
				});
			}
		}
	});
});
</script>