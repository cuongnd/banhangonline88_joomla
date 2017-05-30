<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<div class="juloawrapper">
    <?php 
    $this->general->showGeneralLink();
    ?>
    <div class="row-fluid">
        <fieldset>
            <legend><?php echo JText::_('ADSMANAGER_RULES'); ?></legend>
        </fieldset>
    </div>
    <div class="container-fluid">
        <div class="span12">
            <?php echo $this->conf->rules_text; ?>
        </div>
    </div>
</div>