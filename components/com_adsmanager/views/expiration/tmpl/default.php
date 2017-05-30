<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
?>
<div class="juloawrapper">
    <?php echo sprintf(JText::_('ADSMANAGER_RENEW_AD_QUESTION'),$this->content->ad_headline,$this->content->expiration_date); 
    $target = TRoute::_("index.php?option=com_adsmanager&task=renew&id=".$this->content->id);
    ?>
    <div class="container-fluid">
        <form action="<?php echo $target;?>" method="post" class="form-horizontal" name="adminForm" enctype="multipart/form-data">
       <?php
       if (function_exists("showPaidDuration")) {
            showPaidDuration($this->content);
       } else { ?>
            <div class="row-fluid">
                <div class="span12">
                    <input type='submit' value='<?php echo JText::_('ADSMANAGER_RENEW_AD'); ?>' />
                </div>
            </div>
      <?php } ?>
        </form>
    </div>
</div>