<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Document
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_PLATFORM') or die;
use Joomla\Registry\Registry;
jimport('joomla.utilities.utility');
/**
 * JDocumentJson class, provides an easy interface to parse and display a HTML document
 *
 * @since  11.1
 */
class JDocumentJson extends JDocument
{
    /**
     * Array of Header `<link>` tags
     *
     * @var    array
     * @since  11.1
     */
    public $_links = array();
    /**
     * Array of custom tags
     *
     * @var    array
     * @since  11.1
     */
    public $_custom = array();
    /**
     * Name of the template
     *
     * @var    string
     * @since  11.1
     */
    public $template = null;
    /**
     * Base url
     *
     * @var    string
     * @since  11.1
     */
    public $baseurl = null;
    /**
     * Array of template parameters
     *
     * @var    array
     * @since  11.1
     */
    public $params = null;
    /**
     * File name
     *
     * @var    array
     * @since  11.1
     */
    public $_file = null;
    /**
     * String holding parsed template
     *
     * @var    string
     * @since  11.1
     */
    protected $_template = '';
    /**
     * Array of parsed template JDoc tags
     *
     * @var    array
     * @since  11.1
     */
    protected $_template_tags = array();
    /**
     * Integer with caching setting
     *
     * @var    integer
     * @since  11.1
     */
    protected $_caching = null;
    /**
     * Set to true when the document should be output as HTML5
     *
     * @var    boolean
     * @since  12.1
     */
    private $_html5 = null;
    /**
     * Class constructor
     *
     * @param   array $options Associative array of options
     *
     * @since   11.1
     */
    public function __construct($options = array())
    {
        parent::__construct($options);
        // Set document type
        $this->_type = 'json';
        // Set default mime type and document metadata (meta data syncs with mime type by default)
        $this->setMimeEncoding('text/html');
    }
    /**
     * Get the HTML document head data
     *
     * @return  array  The document head data in array form
     *
     * @since   11.1
     */
    public function getHeadData()
    {

        return $data;
    }
    /**
     * Set the HTML document head data
     *
     * @param   array $data The document head data in array form
     *
     * @return  JDocumentJson|null instance of $this to allow chaining or null for empty input data
     *
     * @since   11.1
     */
    public function setHeadData($data)
    {

        return $this;
    }
    /**
     * Merge the HTML document head data
     *
     * @param   array $data The document head data in array form
     *
     * @return  JDocumentJson|null instance of $this to allow chaining or null for empty input data
     *
     * @since   11.1
     */
    public function mergeHeadData($data)
    {

        return $this;
    }
    /**
     * Adds `<link>` tags to the head of the document
     *
     * $relType defaults to 'rel' as it is the most common relation type used.
     * ('rev' refers to reverse relation, 'rel' indicates normal, forward relation.)
     * Typical tag: `<link href="index.php" rel="Start">`
     *
     * @param   string $href The link that is being related.
     * @param   string $relation Relation of link.
     * @param   string $relType Relation type attribute.  Either rel or rev (default: 'rel').
     * @param   array $attribs Associative array of remaining attributes.
     *
     * @return  JDocumentJson instance of $this to allow chaining
     *
     * @since   11.1
     */
    public function addHeadLink($href, $relation, $relType = 'rel', $attribs = array())
    {
       
        return $this;
    }
    /**
     * Adds a shortcut icon (favicon)
     *
     * This adds a link to the icon shown in the favorites list or on
     * the left of the url in the address bar. Some browsers display
     * it on the tab, as well.
     *
     * @param   string $href The link that is being related.
     * @param   string $type File type
     * @param   string $relation Relation of link
     *
     * @return  JDocumentJson instance of $this to allow chaining
     *
     * @since   11.1
     */
    public function addFavicon($href, $type = 'image/vnd.microsoft.icon', $relation = 'shortcut icon')
    {
        $href = str_replace('\\', '/', $href);
        $this->addHeadLink($href, $relation, 'rel', array('type' => $type));
        return $this;
    }
    /**
     * Adds a custom HTML string to the head block
     *
     * @param   string $html The HTML to add to the head
     *
     * @return  JDocumentJson instance of $this to allow chaining
     *
     * @since   11.1
     */
    public function addCustomTag($html)
    {
        $this->_custom[] = trim($html);
        return $this;
    }
    /**
     * Returns whether the document is set up to be output as HTML5
     *
     * @return  boolean true when HTML5 is used
     *
     * @since   12.1
     */
    public function isHtml5()
    {
        return $this->_html5;
    }
    /**
     * Sets whether the document should be output as HTML5
     *
     * @param   bool $state True when HTML5 should be output
     *
     * @return  void
     *
     * @since   12.1
     */
    public function setHtml5($state)
    {
        if (is_bool($state)) {
            $this->_html5 = $state;
        }
    }
    /**
     * Get the contents of a document include
     *
     * @param   string $type The type of renderer
     * @param   string $name The name of the element to render
     * @param   array $attribs Associative array of remaining attributes.
     *
     * @return  mixed|string The output of the renderer
     *
     * @since   11.1
     */
    public function getBuffer($type = null, $name = null, $attribs = array())
    {
        $app = JFactory::getApplication();
        $client_id = $app->getClientId();
        // If no type is specified, return the whole buffer
        if ($type === null) {
            return parent::$_buffer;
        }
        $title = (isset($attribs['title'])) ? $attribs['title'] : null;
        if (isset(parent::$_buffer[$type][$name][$title])) {
            return parent::$_buffer[$type][$name][$title];
        }
        $renderer = $this->loadRenderer($type);

        if ($this->_caching == true && $type == 'modules') {
            $cache = JFactory::getCache('com_modules', '');
            $hash = md5(serialize(array($name, $attribs, null, $renderer)));
            $cbuffer = $cache->get('cbuffer_' . $type);
            if (isset($cbuffer[$hash])) {
                $a_cbuffer= JCache::getWorkarounds($cbuffer[$hash], array('mergehead' => 1));
                $module=$a_cbuffer['module'];
                $func_check_module_in_array=function($module_id,$modules){
                    foreach($modules as $module){
                        if($module->id==$module_id){
                            return true;
                        }
                        return false;
                    }
                };
                if(!$func_check_module_in_array($module->id,$app->modules))
                {
                    array_push($app->modules,$module);
                }

                return $a_cbuffer;
            } else {
                $options = array();
                $options['nopathway'] = 1;
                $options['nomodules'] = 1;
                $options['modulemode'] = 1;

                $this->setBuffer($renderer->render($name, $attribs, null), $type, $name);
                $data = parent::$_buffer[$type][$name][$title];
                $tmpdata = JCache::setWorkarounds($data, $options);

                $tmpdata['module']=$data;
                $cbuffer[$hash] = $tmpdata;
                $cache->store($cbuffer, 'cbuffer_' . $type);
            }
        } else {
            $this->setBuffer($renderer->render($name, $attribs, null), $type, $name, $title);
        }
        return parent::$_buffer[$type][$name][$title];
    }
    /**
     * Set the contents a document includes
     *
     * @param   string $content The content to be set in the buffer.
     * @param   array $options Array of optional elements.
     *
     * @return  JDocumentJson instance of $this to allow chaining
     *
     * @since   11.1
     */
    public function setBuffer($content, $options = array())
    {
        // The following code is just for backward compatibility.
        if (func_num_args() > 1 && !is_array($options)) {
            $args = func_get_args();
            $options = array();
            $options['type'] = $args[1];
            $options['name'] = (isset($args[2])) ? $args[2] : null;
            $options['title'] = (isset($args[3])) ? $args[3] : null;
        }
        parent::$_buffer[$options['type']][$options['name']][$options['title']] = $content;
        return $this;
    }
    /**
     * Parses the template and populates the buffer
     *
     * @param   array $params Parameters for fetching the template
     *
     * @return  JDocumentJson instance of $this to allow chaining
     *
     * @since   11.1
     */
    public function parse($params = array())
    {

        return $this->_fetchTemplate($params)->_parseTemplate();
    }
    /**
     * Outputs the template to the browser.
     *
     * @param   boolean $caching If true, cache the output
     * @param   array $params Associative array of attributes
     *
     * @return  string The rendered data
     *
     * @since   11.1
     */
    public function render($caching = false, $params = array())
    {
        $this->_caching = $caching;

        if (empty($this->_template)) {
            $this->parse($params);
        }
        $data = $this->_renderTemplate();

        parent::render();
        return $data;
    }
    /**
     * Count the modules based on the given condition
     *
     * @param   string $condition The condition to use
     *
     * @return  integer  Number of modules found
     *
     * @since   11.1
     */
    public function countModules($condition)
    {
        $operators = '(\+|\-|\*|\/|==|\!=|\<\>|\<|\>|\<=|\>=|and|or|xor)';
        $words = preg_split('# ' . $operators . ' #', $condition, null, PREG_SPLIT_DELIM_CAPTURE);
        if (count($words) === 1) {
            $name = strtolower($words[0]);
            $result = ((isset(parent::$_buffer['modules'][$name])) && (parent::$_buffer['modules'][$name] === false))
                ? 0 : count(JModuleHelper::getModules($name));
            return $result;
        }
        JLog::add('Using an expression in JDocumentJson::countModules() is deprecated.', JLog::WARNING, 'deprecated');
        for ($i = 0, $n = count($words); $i < $n; $i += 2) {
            // Odd parts (modules)
            $name = strtolower($words[$i]);
            $words[$i] = ((isset(parent::$_buffer['modules'][$name])) && (parent::$_buffer['modules'][$name] === false))
                ? 0
                : count(JModuleHelper::getModules($name));
        }
        $str = 'return ' . implode(' ', $words) . ';';
        return eval($str);
    }
    /**
     * Count the number of child menu items
     *
     * @return  integer  Number of child menu items
     *
     * @since   11.1
     */
    public function countMenuChildren()
    {
        static $children;
        if (!isset($children)) {
            $db = JFactory::getDbo();
            $app = JFactory::getApplication();
            $menu = $app->getMenu();
            $active = $menu->getActive();
            $children = 0;
            if ($active) {
                $query = $db->getQuery(true)
                    ->select('COUNT(*)')
                    ->from('#__menu')
                    ->where('parent_id = ' . $active->id)
                    ->where('published = 1');
                $db->setQuery($query);
                $children = $db->loadResult();
            }
        }
        return $children;
    }
    /**
     * Load a template file
     *
     * @param   string $directory The name of the template
     * @param   string $filename The actual filename
     *
     * @return  string  The contents of the template
     *
     * @since   11.1
     */
    protected function _loadTemplate($directory, $filename)
    {
        $contents = '';
        // Check to see if we have a valid template file
        if (file_exists($directory . '/' . $filename)) {
            // Store the file path
            $this->_file = $directory . '/' . $filename;
            // Get the file content
            ob_start();
            require $directory . '/' . $filename;
            $contents = ob_get_contents();
            ob_end_clean();
        }
        // Try to find a favicon by checking the template and root folder
        $icon = '/favicon.ico';
        foreach (array($directory, JPATH_BASE) as $dir) {
            if (file_exists($dir . $icon)) {
                $path = str_replace(JPATH_BASE, '', $dir);
                $path = str_replace('\\', '/', $path);
                $this->addFavicon(JUri::base(true) . $path . $icon);
                break;
            }
        }
        return $contents;
    }
    /**
     * Fetch the template, and initialise the params
     *
     * @param   array $params Parameters to determine the template
     *
     * @return  JDocumentJson instance of $this to allow chaining
     *
     * @since   11.1
     */
    protected function _fetchTemplate($params = array())
    {
        // Check

        $directory = isset($params['directory']) ? $params['directory'] : 'templates';
        $filter = JFilterInput::getInstance();
        $template = $filter->clean($params['template'], 'cmd');
        $file = $filter->clean($params['file'], 'cmd');
        if (!file_exists($directory . '/' . $template . '/' . $file)) {

            $template = 'system';
        }
        if (!file_exists($directory . '/' . $template . '/' . $file)) {
            $file = 'index.php';
        }
        // Load the language file for the template
        $lang = JFactory::getLanguage();
        // 1.5 or core then 1.6
        $lang->load('tpl_' . $template, JPATH_BASE, null, false, true)
        || $lang->load('tpl_' . $template, $directory . '/' . $template, null, false, true);
        // Assign the variables
        $this->template = $template;
        $this->baseurl = JUri::base(true);
        $this->params = isset($params['params']) ? $params['params'] : new Registry;
        // Load
        $this->_template = $this->_loadTemplate($directory . '/' . $template, $file);
        return $this;
    }
    /**
     * Parse a document template
     *
     * @return  JDocumentJson  instance of $this to allow chaining
     *
     * @since   11.1
     */
    protected function _parseTemplate()
    {
        $app=JFactory::getApplication();
        $matches = array();
        if (preg_match_all('#<jdoc:include\ type="([^"]+)"(.*)\/>#iU', $this->_template, $matches)) {
            $template_tags_first = array();
            $template_tags_last = array();
            // Step through the jdocs in reverse order.
            for ($i = count($matches[0]) - 1; $i >= 0; $i--) {
                $type = $matches[1][$i];
                $attribs = empty($matches[2][$i]) ? array() : JUtility::parseAttributes($matches[2][$i]);
                $name = isset($attribs['name']) ? $attribs['name'] : null;
                // Separate buffers to be executed first and last
                if ($type == 'module' || $type == 'modules') {
                    $template_tags_first[$matches[0][$i]] = array('type' => $type, 'name' => $name, 'attribs' => $attribs);
                } else {
                    $template_tags_last[$matches[0][$i]] = array('type' => $type, 'name' => $name, 'attribs' => $attribs);
                }
            }
            // Reverse the last array so the jdocs are in forward order.
            $template_tags_last = array_reverse($template_tags_last);
            $this->_template_tags = $template_tags_first + $template_tags_last;
        }
        return $this;
    }
    /**
     * Render pre-parsed template
     *
     * @return string rendered template
     *
     * @since   11.1
     */
    protected function _renderTemplate()
    {
        $app=JFactory::getApplication();
        $replace = array();
        $with = array();
        foreach ($this->_template_tags as $jdoc => $args) {
            $replace[] = $jdoc;
            $with[] = $this->getBuffer($args['type'], $args['name'], $args['attribs']);
        }
        $cache = JFactory::getCache('com_modules', '');
        $hash = md5(serialize($this->_template_tags));
        $list_module_by_template_tags = $cache->get('list_module_by_template_tags');
        if (!isset($list_module_by_template_tags[$hash])) {
            $list_module_by_template_tags[$hash]=$app->modules;
            $cache->store($list_module_by_template_tags, 'list_module_by_template_tags');
        }else{
            $app->modules=$list_module_by_template_tags[$hash];
        }

        return str_replace($replace, $with, $this->_template);
    }
}

