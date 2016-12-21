<?php
    /**
    * @package Helix Framework
    * @author JoomShaper http://www.joomshaper.com
    * @copyright Copyright (c) 2010 - 2015 JoomShaper
    * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
    */	

    //no direct accees
    defined ('_JEXEC') or die ('resticted aceess');

    jimport( 'joomla.event.plugin' );

    class  plgSystemHelix extends JPlugin
    {
        function onAfterInitialise()
        {
            $helix_path = JPATH_PLUGINS.'/system/helix/core/helix.php';
            if (file_exists($helix_path)) {
                require_once($helix_path);
                Helix::getInstance()
                ;
            }

        }







        // Updated 1.9.5
}