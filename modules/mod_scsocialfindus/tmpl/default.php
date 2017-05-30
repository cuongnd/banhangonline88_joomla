<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2014-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');
?>
<?php if (!empty($userIntro)): ?>
    <div class="scsocialfindus_desc"><?php echo $userIntro; ?></div>
<?php endif; ?>

    <div style="<?php echo $groupStyles; ?> z-index: 99; overflow: visible;" class="scsocialfindus <?php echo $orientation; ?>">

        <?php if (!empty($facebookLink)): ?>
            <a class="scsocialfindus-btn scsocialfindus-facebook-btn" href="<?php echo $facebookLink; ?>" target="_blank"><span title="Facebook">Facebook</span></a>
        <?php endif; ?>

        <?php if (!empty($googleLink)): ?>
            <a class="scsocialfindus-btn scsocialfindus-google-btn" href="<?php echo $googleLink; ?>" target="_blank"><span title="Google+">Google+</span></a>
        <?php endif; ?>

        <?php if (!empty($twitterLink)): ?>
            <a class="scsocialfindus-btn scsocialfindus-twitter-btn" href="<?php echo $twitterLink; ?>" target="_blank"><span title="Twitter">Twitter</span></a>
        <?php endif; ?>

        <?php if (!empty($linkedinLink)): ?>
            <a class="scsocialfindus-btn scsocialfindus-linkedin-btn" href="<?php echo $linkedinLink; ?>" target="_blank"><span title="LinkedIn">LinkedIn</span></a>
        <?php endif; ?>

        <?php if (!empty($pinterestLink)): ?>
            <a class="scsocialfindus-btn scsocialfindus-pinterest-btn" href="<?php echo $pinterestLink; ?>" target="_blank"><span title="Pinterest">Pinterest</span></a>
        <?php endif; ?>

        <?php if (!empty($rssLink)): ?>
            <a class="scsocialfindus-btn scsocialfindus-rss-btn" href="<?php echo $rssLink; ?>" target="_blank"><span title="RSS">RSS</span></a>
        <?php endif; ?>

    </div>
    <div style="clear:left"></div>
<?php require(JPATH_ROOT . '/components/com_jfbconnect/assets/poweredBy.php'); ?>