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
<?php if( $system->config->get( 'main_postsubscription' )  ){ ?>
	<?php if( $isSubscribed && $system->my->id != 0 ) { ?>
		<a id="unsubscribe-<?php echo $sid; ?>" 
		   class="butt butt-default<?php echo ($class) ? ' '.$class : ''; ?>" 
		   href="javascript:void(0);" onclick="disjax.loadingDialog();disjax.load('index', 'ajaxUnSubscribe', '<?php echo $type; ?>', '<?php echo $isSubscribed; ?>', '<?php echo $cid; ?>');">
			<?php echo JText::_( 'COM_EASYDISCUSS_UNSUBSCRIBE_VIAEMAIL_'.strtoupper($type) ); ?>
			<?php if( $type == 'site' ) { ?>
			<?php echo JText::_('COM_EASYDISCUSS_UNSUBSCRIBE'); ?>
			<?php } ?>
		</a>
	<?php } else { ?>
		<a data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SUBSCRIBE_VIAEMAIL_'.strtoupper($type) ); ?>" 
		   id="subscribe-<?php echo $type.'-'.$cid; ?>" 
		   class="butt butt-default via-email<?php echo ($class) ? ' '.$class : ''; ?>"
		   href="javascript:void(0);" onclick="disjax.loadingDialog();disjax.load('index', 'ajaxSubscribe', '<?php echo $type; ?>', '<?php echo $cid; ?>');">
			<i class="i i-envelope muted"></i>
			&nbsp;
			<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_VIA_EMAIL'); ?>
		</a>
	<?php } ?>
<?php } ?>

