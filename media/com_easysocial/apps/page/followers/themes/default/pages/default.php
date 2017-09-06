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
<div class="app-followers app-pages" data-es-page-followers data-id="<?php echo $page->id;?>" data-return="<?php echo $returnUrl;?>">

	<div class="app-contents-wrap">

		<?php if ($page->isAdmin()) { ?>
			<div class="t-text--right">
				<div class="btn-group">

				    	<a href="javascript:void(0);" data-filter data-type="all" class="btn btn-es-default-o btn-sm <?php echo $active == '' ? ' active' : '';?>">
				    		<?php echo JText::_('APP_PAGE_FOLLOWERS_FILTER_ALL');?>
				    	</a>
				    
				    
				    	<a href="javascript:void(0);" data-filter data-type="followers" class="btn btn-es-default-o btn-sm <?php echo $active == 'followers' ? ' active' : '';?>">
				    		<?php echo JText::_('APP_PAGE_FOLLOWERS_FILTER_FOLLOWERS');?>
				    	</a>
				    
				    
				    	<a href="javascript:void(0);" data-filter data-type="admin" class="btn btn-es-default-o btn-sm <?php echo $active == 'admin' ? ' active' : '';?>">
				    		<?php echo JText::_('APP_PAGE_FOLLOWERS_FILTER_ADMINS');?>
				    	</a>
				    

				    <?php if ($page->isClosed()) { ?>
				    
				    	<a href="javascript:void(0);" data-filter data-type="pending" class="btn btn-es-default-o btn-sm <?php echo $active == 'pending' ? ' active' : '';?>">
				    		<?php echo JText::_('APP_PAGE_FOLLOWERS_FILTER_PENDING');?>
				    	</a>
				    
				    <?php } ?>
				</div>	
			</div>
		<?php } ?>

		<div class="t-lg-mt--xl" data-wrapper>

			<?php echo $this->html('html.loading'); ?>

			<div data-result>
				<?php echo $this->includeTemplate('apps/page/followers/pages/list'); ?>
			</div>
		</div>

	</div>
</div>
