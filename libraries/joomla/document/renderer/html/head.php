<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Document
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_PLATFORM') or die;
use MatthiasMullie\Minify;
use Joomla\Utilities\ArrayHelper;

/**
 * JDocument head renderer
 *
 * @since  3.5
 */
class JDocumentRendererHtmlHead extends JDocumentRenderer
{

    public $rebuild_js=false;
    public $rebuild_css=false;
    /**
     * Renders the document head and returns the results as a string
     *
     * @param   string $head (unused)
     * @param   array $params Associative array of values
     * @param   string $content The script
     *
     * @return  string  The output of the script
     *
     * @since   3.5
     */
    public function render($head, $params = array(), $content = null)
    {
        return $this->fetchHead($this->_doc);
    }

    /**
     * Generates the head HTML and return the results as a string
     *
     * @param   JDocumentHtml $document The document for which the head will be created
     *
     * @return  string  The head hTML
     *
     * @since   3.5
     * @deprecated  4.0  Method code will be moved into the render method
     */
    public function fetchHead($document)
    {
        // Convert the tagids to titles
        if (isset($document->_metaTags['name']['tags'])) {
            $tagsHelper = new JHelperTags;
            $document->_metaTags['name']['tags'] = implode(', ', $tagsHelper->getTagNames($document->_metaTags['name']['tags']));
        }
        // Trigger the onBeforeCompileHead event
        $app = JFactory::getApplication();
        $client=$app->getClientId();
        if($client==0) {
            $parser = Less_Parser::getInstance();
            $file_css = 'templates/vina_bonnie/bootstrap-3.3.7/css/bootstrap.css';
            $renew_file_bootstrap = false;
            if ($renew_file_bootstrap) {
                $css = $parser->getCss();
                JFile::write(JPATH_ROOT . DS . $file_css, $css);
            }
            $document->addStyleSheet("/$file_css");
        }

        // Get line endings
        $lnEnd = $document->_getLineEnd();
        $tab = $document->_getTab();
        $tagEnd = ' />';
        $buffer = '';
        // Generate charset when using HTML5 (should happen first)
        if ($document->isHtml5()) {
            $buffer .= $tab . '<meta charset="' . $document->getCharset() . '" />' . $lnEnd;
        }
        // Generate base tag (need to happen early)
        $base = $document->getBase();
        if (!empty($base)) {
            $buffer .= $tab . '<base href="' . $base . '" />' . $lnEnd;
        }
        // Generate META tags (needs to happen as early as possible in the head)
        foreach ($document->_metaTags as $type => $tag) {
            foreach ($tag as $name => $content) {
                if ($type == 'http-equiv' && !($document->isHtml5() && $name == 'content-type')) {
                    $buffer .= $tab . '<meta http-equiv="' . $name . '" content="' . htmlspecialchars($content, ENT_COMPAT, 'UTF-8') . '" />' . $lnEnd;
                } elseif ($type != 'http-equiv' && !empty($content)) {
                    $buffer .= $tab . '<meta ' . $type . '="' . $name . '" content="' . htmlspecialchars($content, ENT_COMPAT, 'UTF-8') . '" />' . $lnEnd;
                }
            }
        }
        // Don't add empty descriptions
        $documentDescription = $document->getDescription();
        if ($documentDescription) {
            $buffer .= $tab . '<meta name="description" content="' . htmlspecialchars($documentDescription, ENT_COMPAT, 'UTF-8') . '" />' . $lnEnd;
        }
        // Don't add empty generators
        $generator = $document->getGenerator();
        if ($generator) {
            $buffer .= $tab . '<meta name="generator" content="' . htmlspecialchars($generator, ENT_COMPAT, 'UTF-8') . '" />' . $lnEnd;
        }
        $buffer .= $tab . '<title>' . htmlspecialchars($document->getTitle(), ENT_COMPAT, 'UTF-8') . '</title>' . $lnEnd;
        // Generate link declarations
        foreach ($document->_links as $link => $linkAtrr) {
            $buffer .= $tab . '<link href="' . $link . '" ' . $linkAtrr['relType'] . '="' . $linkAtrr['relation'] . '"';
            if (is_array($linkAtrr['attribs'])) {
                if ($temp = ArrayHelper::toString($linkAtrr['attribs'])) {
                    $buffer .= ' ' . $temp;
                }
            }
            $buffer .= ' />' . $lnEnd;
        }
        $client = $app->getClientId();

        if ($this->merge_css && $client == 0) {
            $table_compress = JTable::getInstance('compress');
            $json_file = json_encode($document->_styleSheets);
            $table_compress->load(array(json_file => $json_file));
            if (!$table_compress->id) {
                $table_compress->json_file = $json_file;
                $table_compress->store();
            }
            $path_file_all_css = 'media/system/css/all_' . $table_compress->id . '.css';

            if($this->rebuild_css  || !JFile::exists(JPATH_ROOT.DS.$path_file_all_css)) {
                require_once JPATH_ROOT . DS . 'libraries/less.php_1.7.0.10/less.php/Less.php';
                foreach ($document->_styleSheets as $strSrc => $strAttr) {
                    $file_type = strtolower(substr($strSrc, -4));
                    if ($file_type == "less") {
                        $parser = new Less_Parser();
                        $parser->parseFile(JPATH_ROOT . $strSrc, JUri::root());
                        $css = $parser->getCss();
                        $css_path = substr($strSrc, 0, -4) . 'css';
                        JFile::write(JPATH_ROOT . $css_path, $css);
                        $document->_styleSheets["$css_path"] = $strAttr;
                        unset($document->_styleSheets[$strSrc]);
                    }
                }
                $list_file_css = array();
                foreach ($document->_styleSheets as $strSrc => $strAttr) {
                    $list_file_css[] = JPATH_ROOT . $strSrc;
                }
                require_once JPATH_ROOT . DS . 'libraries/minifyjscss/minify-master/src/Minify.php';
                require_once JPATH_ROOT . DS . 'libraries/minifyjscss/minify-master/src/Exceptions/BasicException.php';
                require_once JPATH_ROOT . DS . 'libraries/minifyjscss/minify-master/src/Exceptions/FileImportException.php';
                require_once JPATH_ROOT . DS . 'libraries/minifyjscss/minify-master/src/JS.php';
                require_once JPATH_ROOT . DS . 'libraries/minifyjscss/minify-master/src/CSS.php';
                require_once JPATH_ROOT . DS . 'libraries/minifyjscss/minify-master/src/Exception.php';
                require_once JPATH_ROOT . DS . 'libraries/minifyjscss/path-converter-master/src/ConverterInterface.php';
                require_once JPATH_ROOT . DS . 'libraries/minifyjscss/path-converter-master/src/Converter.php';
                $minifier = new Minify\CSS();
                $minifier->add($list_file_css);
                $js_content = $minifier->minify();
                JFile::write(JPATH_ROOT . DS . $path_file_all_css, $js_content);
            }
            $document->_styleSheets = array();
            $document->addLessStyleSheet("/$path_file_all_css");
        }

        $defaultCssMimes = array('text/css');
        // Generate stylesheet links
        foreach ($document->_styleSheets as $strSrc => $strAttr) {
            if (array_key_exists('rel', $strAttr['attribs'])) {
                $buffer .= $tab . '<link  href="' . $strSrc . '"';
            } else {
                $buffer .= $tab . '<link rel="stylesheet" href="' . $strSrc . '"';
            }
            if (!is_null($strAttr['mime']) && (!$document->isHtml5() || !in_array($strAttr['mime'], $defaultCssMimes))) {
                $buffer .= ' type="' . $strAttr['mime'] . '"';
            }
            if (!is_null($strAttr['media'])) {
                $buffer .= ' media="' . $strAttr['media'] . '"';
            }
            if (is_array($strAttr['attribs'])) {
                if ($temp = ArrayHelper::toString($strAttr['attribs'])) {
                    $buffer .= ' ' . $temp;
                }
            }
            $buffer .= $tagEnd . $lnEnd;
        }
        // Generate stylesheet declarations
        foreach ($document->_style as $type => $content) {
            $buffer .= $tab . '<style';
            if (!is_null($type) && (!$document->isHtml5() || !in_array($type, $defaultCssMimes))) {
                $buffer .= ' type="' . $type . '"';
            }
            $buffer .= '>' . $lnEnd;
            // This is for full XHTML support.
            if ($document->_mime != 'text/html') {
                $buffer .= $tab . $tab . '/*<![CDATA[*/' . $lnEnd;
            }
            $buffer .= $content . $lnEnd;
            // See above note
            if ($document->_mime != 'text/html') {
                $buffer .= $tab . $tab . '/*]]>*/' . $lnEnd;
            }
            $buffer .= $tab . '</style>' . $lnEnd;
        }
        if ($this->merge_js && $client == 0) {
            $table_compress = JTable::getInstance('compress');
            $json_file = json_encode($document->_scripts);
            $table_compress->load(array(json_file => $json_file));
            if (!$table_compress->id) {
                $table_compress->json_file = $json_file;
                $table_compress->store();
            }
            $path_file_all_js = 'media/system/js/all_' . $table_compress->id . '.js';

            if($this->rebuild_js || !JFile::exists(JPATH_ROOT.DS.$path_file_all_js)) {
                $list_file_js = array();
                foreach ($document->_scripts as $strSrc => $strAttr) {
                    $list_file_js[] = JPATH_ROOT . $strSrc;
                }
                $js_content = "";
                require_once JPATH_ROOT . DS . 'libraries/minifyjscss/minify-master/src/Minify.php';
                require_once JPATH_ROOT . DS . 'libraries/minifyjscss/minify-master/src/JS.php';
                require_once JPATH_ROOT . DS . 'libraries/minifyjscss/minify-master/src/Exception.php';
                require_once JPATH_ROOT . DS . 'libraries/minifyjscss/path-converter-master/src/ConverterInterface.php';
                require_once JPATH_ROOT . DS . 'libraries/minifyjscss/path-converter-master/src/Converter.php';
                $minifier = new Minify\JS();
                $minifier->add($list_file_js);
                $js_content = $minifier->minify();
                JFile::write(JPATH_ROOT . DS . $path_file_all_js, $js_content);
            }
            $document->_scripts = array();
            $document->addScript("/$path_file_all_js");

        }
        $defaultJsMimes = array('text/javascript', 'application/javascript', 'text/x-javascript', 'application/x-javascript');
        // Generate script file links
        foreach ($document->_scripts as $strSrc => $strAttr) {
            $buffer .= $tab . '<script src="' . $strSrc . '"';
            if (!is_null($strAttr['mime']) && (!$document->isHtml5() || !in_array($strAttr['mime'], $defaultJsMimes))) {
                $buffer .= ' type="' . $strAttr['mime'] . '"';
            }
            if ($strAttr['defer']) {
                $buffer .= ' defer="defer"';
            }
            if ($strAttr['async']) {
                $buffer .= ' async="async"';
            }
            $buffer .= '></script>' . $lnEnd;
        }
        // Generate scripts options
        $scriptOptions = $document->getScriptOptions();
        if (!empty($scriptOptions)) {
            $buffer .= $tab . '<script type="text/javascript">' . $lnEnd;
            // This is for full XHTML support.
            if ($document->_mime != 'text/html') {
                $buffer .= $tab . $tab . '//<![CDATA[' . $lnEnd;
            }
            $pretyPrint = (JDEBUG && defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : false);
            $jsonOptions = json_encode($scriptOptions, $pretyPrint);
            $jsonOptions = $jsonOptions ? $jsonOptions : '{}';
            // TODO: use .extend(Joomla.optionsStorage, options) when it will be safe
            $buffer .= $tab . 'var Joomla = Joomla || {};' . $lnEnd;
            $buffer .= $tab . 'Joomla.optionsStorage = ' . $jsonOptions . ';' . $lnEnd;
            // See above note
            if ($document->_mime != 'text/html') {
                $buffer .= $tab . $tab . '//]]>' . $lnEnd;
            }
            $buffer .= $tab . '</script>' . $lnEnd;
        }
        // Generate script declarations
        foreach ($document->_script as $type => $content) {
            $buffer .= $tab . '<script';
            if (!is_null($type) && (!$document->isHtml5() || !in_array($type, $defaultJsMimes))) {
                $buffer .= ' type="' . $type . '"';
            }
            $buffer .= '>' . $lnEnd;
            // This is for full XHTML support.
            if ($document->_mime != 'text/html') {
                $buffer .= $tab . $tab . '//<![CDATA[' . $lnEnd;
            }
            $buffer .= $content . $lnEnd;
            // See above note
            if ($document->_mime != 'text/html') {
                $buffer .= $tab . $tab . '//]]>' . $lnEnd;
            }
            $buffer .= $tab . '</script>' . $lnEnd;
        }
        // Generate script language declarations.
        if (count(JText::script())) {
            $buffer .= $tab . '<script';
            if (!$document->isHtml5()) {
                $buffer .= ' type="text/javascript"';
            }
            $buffer .= '>' . $lnEnd;
            if ($document->_mime != 'text/html') {
                $buffer .= $tab . $tab . '//<![CDATA[' . $lnEnd;
            }
            $buffer .= $tab . $tab . '(function() {' . $lnEnd;
            $buffer .= $tab . $tab . $tab . 'Joomla.JText.load(' . json_encode(JText::script()) . ');' . $lnEnd;
            $buffer .= $tab . $tab . '})();' . $lnEnd;
            if ($document->_mime != 'text/html') {
                $buffer .= $tab . $tab . '//]]>' . $lnEnd;
            }
            $buffer .= $tab . '</script>' . $lnEnd;
        }
        // Output the custom tags - array_unique makes sure that we don't output the same tags twice
        foreach (array_unique($document->_custom) as $custom) {
            $buffer .= $tab . $custom . $lnEnd;
        }
        return $buffer;
    }

    private function make_js($scripts, $path_file_all_js, $write_file = true)
    {
        if ($write_file) {

        }
        $document = JFactory::getDocument();
        $document->_scripts = array();
        $compress_file = substr($path_file_all_js, 0, -2) . "min.js";;
        $document->addScript('/' . $compress_file);
    }

    private function make_style_sheets($styleSheets, $path_file_all_css, $write_file = true)
    {
        $css_content = '';
        $list_less_file = array();
        foreach ($styleSheets as $strSrc => $strAttr) {
            if (strpos($strSrc, '.less') !== false) {
                $list_less_file[$strSrc] = $strAttr;
            } else {
                if (filter_var($strSrc, FILTER_VALIDATE_URL)) {
                    if (strpos($strSrc, JUri::root()) !== false) {
                        $strSrc = str_replace(JUri::root(), JPATH_ROOT . DS, $strSrc);
                    }
                } else {
                    $strSrc = JPATH_ROOT . DS . $strSrc;
                }
                if (!filter_var($strSrc, FILTER_VALIDATE_URL)) {
                    $has_character = strpos($strSrc, "?");
                    //check has charactor "?"
                    if ($has_character !== false) {
                        $strSrc = substr($strSrc, 0, $has_character);
                    }
                }
                if ($write_file) {
                    $file_content = JFile::read($strSrc);
                    if (strpos($file_content, "../") !== false) {
                        $strSrc1 = str_replace(JPATH_ROOT . DS, "", $strSrc);
                        $strSrc1 = explode('/', $strSrc1);
                        $strSrc1 = array_slice($strSrc1, 0, count($strSrc1) - 1);
                        $strSrc1 = implode('/', $strSrc1);
                        $file_content = str_replace("../", "/" . $strSrc1 . '/../', $file_content);
                    }
                    $css_content .= $file_content . "\n\r";
                }
            }
        }
        if ($write_file) {
            JFile::write(JPATH_ROOT . DS . $path_file_all_css, $css_content);
        }
        $document = JFactory::getDocument();
        $document->_styleSheets = $list_less_file;
        $document->addStyleSheet(JUri::root() . $path_file_all_css);
    }
}
