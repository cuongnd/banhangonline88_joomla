<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
EasyDiscuss
.require()
.script( 'votes' )
.done(function($){

	$( '.discussVote-<?php echo $post->id;?>' ).implement(
		EasyDiscuss.Controller.Votes ,
		{
			viewVotes : <?php echo !$system->config->get( 'main_allowguestview_whovoted' ) && !$system->my->id ? 'false' : 'true'; ?>
		}
	);
});
</script>
<div class="discuss-vote discussVote-<?php echo $post->id;?>" data-postid="<?php echo $post->id;?>">
	<div class="discuss-resolvedbar"><?php echo JText::_( 'COM_EASYDISCUSS_RESOLVED' , true );?></div>
	<div class="vote-points pos-r votePoints">

		<b><?php echo $post->sum_totalvote;?></b>
		<span>votes</span>
	</div>

	<?php if( $access->canVote() && !$post->isVoted ){ ?>
		<a href="javascript:void(0);" class="vote-up voteUp"> <i class="icon-plus-sign"></i></a>
	<?php }else{ ?>
		<a href="javascript:void(0);" class="vote-up" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ALREADY_VOTED_POLL' ); ?>" data-placement="top" rel="ed-tooltip" > <i class="icon-plus-sign"></i></a>
	<?php } ?>

	<?php if( $access->canVote() && !$post->isVoted ){ ?>
	<a href="javascript:void(0);" class="vote-down voteDown"> <i class="icon-minus-sign"></i></a>
	<?php }else{ ?>
		<a href="javascript:void(0);" class="vote-down" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ALREADY_VOTED_POLL' ); ?>" data-placement="top" rel="ed-tooltip" > <i class="icon-minus-sign"></i></a>
	<?php } ?>
</div>
