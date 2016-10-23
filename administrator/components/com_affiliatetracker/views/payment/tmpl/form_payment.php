<?php 

/*------------------------------------------------------------------------
# com_affiliatetracker - Affiliate Tracker for Joomla
# ------------------------------------------------------------------------
# author        Germinal Camps
# copyright       Copyright (C) 2014 JoomlaThat.com. All Rights Reserved.
# @license        http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites:       http://www.JoomlaThat.com
# Technical Support:  Forum - http://www.JoomlaThat.com/support
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access'); 

$params =JComponentHelper::getParams( 'com_affiliatetracker' );

    // load the plugin
      $import = JPluginHelper::importPlugin( strtolower( 'Affiliates' ) );
    // fire plugin
      $dispatcher = JDispatcher::getInstance();
      $the_payment_options = $dispatcher->trigger( 'onRenderPaymentOptions', array( $this->payment, $this->payment->user_id ) );
      
      if(count($the_payment_options)){

        echo '<ul class="nav nav-tabs" id="payments_tab">' ;
        $first = true;
        $class_tab = "active";

        foreach($the_payment_options as $method){

          echo '<li class="'.$class_tab.'"><a data-toggle="tab" href="#'.str_replace(" ", "", $method[1]).'">'.JText::_( $method[1] ).'</a></li>';

          if($first){$class_tab = ""; $first = false;} 

        }

        echo "</ul>" ;
        
        $pane = '1';
        
        //echo JHtml::_('tabs.start', "pane_$pane");
        echo JHtml::_('bootstrap.startPane', 'payments_tab', array('active' => str_replace(" ", "", $the_payment_options[0][1])));

        foreach($the_payment_options as $method){
          
          //echo JHtml::_('tabs.panel', JText::_( $method[1] ), $method[1]);
          echo JHtml::_('bootstrap.addPanel', 'payments_tab', str_replace(" ", "", $method[1]));
          echo $method[0]; 
          echo JHtml::_('bootstrap.endPanel');
          
        }
        
        //echo JHtml::_('tabs.end');
        echo JHtml::_('bootstrap.endPane', 'payments_tab');
        
      }
      else echo JText::_( 'NO_PAYMENT_OPTIONS_AVAILABLE' );
