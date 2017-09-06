<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php foreach ($polls as $poll) { ?>
<div class="app-poll-item row-table">
    <div class="col-cell cell-tight pr-15">
        <i class="ies-pie"></i>
    </div>

    <div class="col-cell">
        <div>
            <?php echo $poll->content; ?>
        </div>
        
        <div class="es-action-wrap">
            <ul class="g-list-unstyled es-action-feedback t-fs--sm">
                <li>
                    <a href="<?php echo $poll->author->getPermalink(); ?>"><?php echo $poll->author->getName(); ?></a>
                </li>
                <li>
                    <span><?php echo FD::date( $poll->created )->format( JText::_( 'DATE_FORMAT_LC1' ) );?></span>
                </li>
            </ul>
        </div>
    </div>
</div>
<?php } ?>
