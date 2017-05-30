<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Document
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_PLATFORM') or die;

/**
 * JDocument Modules renderer
 *
 * @since  3.5
 */
class JDocumentRendererHtmlModules extends JDocumentRenderer
{
    /**
     * Renders multiple modules script and returns the results as a string
     *
     * @param   string $position The position of the modules to render
     * @param   array $params Associative array of values
     * @param   string $content Module content
     *
     * @return  string  The output of the script
     *
     * @since   3.5
     */
    public function render($position, $params = array(), $content = null)
    {
        $renderer = $this->_doc->loadRenderer('module');
        $buffer = '';
        $app = JFactory::getApplication();
        $user = JFactory::getUser();
        foreach (JModuleHelper::getModules($position) as $mod) {
            $moduleHtml ='';
            $moduleHtml = $renderer->render($mod, $params, $content);
            $buffer .= $moduleHtml;
        }
        return $buffer;
    }
}
