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
<?php if ($broadcast->title) { ?>
    <?php echo JText::sprintf('APP_USER_BROADCASTS_STREAM_TITLE_POSTED_NEW_ANNOUNCEMENT_TITLE', $this->html('html.user', $actor->id), '<a href="' . $broadcast->link . '">' . $broadcast->title . '</a>'); ?>
<?php } else { ?>
    <?php echo JText::sprintf('APP_USER_BROADCASTS_STREAM_TITLE_POSTED_NEW_ANNOUNCEMENT', $this->html('html.user', $actor->id), '<a href="' . $broadcast->link . '">' . $broadcast->title . '</a>'); ?>
<?php }?>