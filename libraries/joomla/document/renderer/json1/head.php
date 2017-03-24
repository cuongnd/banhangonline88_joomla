<?php/** * @package     Joomla.Platform * @subpackage  Document * * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved. * @license     GNU General Public License version 2 or later; see LICENSE */defined('JPATH_PLATFORM') or die;use MatthiasMullie\Minify;use Joomla\Utilities\ArrayHelper;/** * JDocument head renderer * * @since  3.5 */class JDocumentRendererJsonHead extends JDocumentRenderer{    public $rebuild_js = false;    public $rebuild_css = false;    /**     * Renders the document head and returns the results as a string     *     * @param   string $head (unused)     * @param   array $params Associative array of values     * @param   string $content The script     *     * @return  string  The output of the script     *     * @since   3.5     */    public function render($head, $params = array(), $content = null)    {        return $this->fetchHead($this->_doc);    }    /**     * Generates the head HTML and return the results as a string     *     * @param   JDocumentHtml $document The document for which the head will be created     *     * @return  string  The head hTML     *     * @since   3.5     * @deprecated  4.0  Method code will be moved into the render method     */    public function fetchHead($document)    {    }}