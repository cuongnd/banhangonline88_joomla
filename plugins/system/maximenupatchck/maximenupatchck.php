<?php

/**
 * @copyright	Copyright (C) 2011 Cédric KEIFLIN alias ced1870
 * http://www.ck-web-creation-alsace.com
 * http://www.joomlack.fr
 * @license		GNU/GPL
 * Version 1.0
 * */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemmaximenupatchck extends JPlugin {

    function plgSystemmaximenupatchck(&$subject, $config) {
        parent :: __construct($subject, $config);
    }

    function onAfterRender() {

        $mainframe = JFactory::getApplication();
        $document = JFactory::getDocument();
        $doctype = $document->getType();

        // si pas en frontend, on sort
        if ($mainframe->isAdmin()) {
            return false;
        }

        // si pas HTML, on sort
        if ($doctype !== 'html') {
            return;
        }

        // renvoie les données dans la page
        $body = JResponse::getBody();
        $regex = "#{maximenu}(.*?){/maximenu}#s"; // masque de recherche
		$body = preg_replace($regex, '', $body);
        JResponse::setBody($body);
    }

}