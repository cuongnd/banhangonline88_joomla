<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<?php if (!empty($eventId)) { ?>
<div class="item-heading">
	<?php echo $this->loadTemplate('site/events/mini.header' , array('event' => FD::event($eventId) , 'showApps' => false)); ?>
</div>
<?php } ?>

<?php if (!empty($groupId)) { ?>
<div class="item-heading">
	<?php echo $this->loadTemplate( 'site/groups/mini.header' , array('group' => FD::group($groupId) , 'showApps' => false ) ); ?>
</div>
<?php } ?>

<?php if( $hashtag ){ ?>
<div class="es-streams">
	<div class="row">
		<div class="col-md-12">
			<a href="javascript:void('0');"
			   class="fd-small mt-10 pull-right"
			   data-hashtag-filter-save
			   data-tag="<?php echo $hashtag; ?>"
			><i class="icon-es-create"></i> <?php echo JText::_( 'COM_EASYSOCIAL_STREAM_SAVE_FILTER' );?></a>

			<h3 class="pull-left">
				<a href="<?php echo FRoute::dashboard( array( 'layout' => 'hashtag' , 'tag' => $hashtagAlias ) );?>">#<?php echo $hashtag; ?></a>
			</h3>
		</div>
	</div>
	<p class="fd-small">
		<?php echo JText::sprintf( 'COM_EASYSOCIAL_STREAM_HASHTAG_CURRENTLY_FILTERING' , '<a href="' . FRoute::dashboard( array( 'layout' => 'hashtag' , 'tag' => $hashtagAlias ) ) . '">#' . $hashtag . '</a>' ); ?>
	</p>
</div>
<hr />
<?php } ?>

<?php echo $stream->html();?>
