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
<div id="es" class="es mod-es-profile-stat module-profile-stat<?php echo $lib->getSuffix();?>">
    <div class="es-side-widget">

        <div class="es-side-widget__bd">
            <p class="t-text--muted">
                <?php echo JText::sprintf('MOD_EASYSOCIAL_PROFILE_STATS_PROFILE_INFO', $my->getProfile()->getTitle()); ?>
            </p>

            <ul class="o-nav o-nav--stacked ">
                <?php foreach ($stat as $key => $item) { ?>
                <li class="o-nav__item">
                    <div>
                        <i class="fa <?php echo $item->icon; ?> t-text--muted t-lg-mr--sm"></i>
                        <b><?php echo JText::_('MOD_EASYSOCIAL_PROFILE_STATS_TITLE_' . strtoupper($key)); ?></b>
                        <span class="t-text--muted">
                            (<?php echo $item->usage; ?> / <?php echo ($item->limit) ? $item->limit : JText::_('MOD_EASYSOCIAL_PROFILE_STATS_UNLIMITED'); ?><?php echo (isset($item->interval) && $item->interval && $item->limit) ? ' ' . $item->interval : ''; ?>)
                        </span>
                    </div>

                    <?php if ($item->limit) { ?>
                    <div class="progress es-side-progress-bar t-lg-mt--md">
                        <div class="progress-bar" style="width: <?php echo ($item->usage / $item->limit) * 100; ?>%;" role="progressbar"></div>
                    </div>
                    <?php } else { ?>
                    <div class="t-lg-mt--md"></div>
                    <?php } ?>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>

</div>
