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
<div class="stackTip tooltip-team" data-options='<?php echo $options; ?>'>
<div id="ezttip">
<div id="ezttip-in">
<div id="ezttip-in-in">
    <div class="ezttip-wrap">
        <div class="ezttip-avatar"><img src="<?php echo $team->getAvatar(); ?>" width="45" height="45" /></div>
        <div class="ezttip-content">
            <div class="ezttip-title"><span><?php echo $team->title;?></span></div>
            <div class="ezttip-desc"><?php echo JString::substr( strip_tags( $team->description ) , 0 , 80 );?></div>
        </div>
    </div>
</div>
</div>
</div>
</div>