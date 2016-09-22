<?php
/**
 * @package		Foundry
 * @copyright	Copyright (C) 2012 StackIdeas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Foundry is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

// Load FoundryConfiguration
require_once(EASYBLOG_FOUNDRY_CONFIGURATION);
require_once( dirname( __FILE__ ) . '/compiler.php' );

class EasyBlogConfiguration extends FD31_FoundryComponentConfiguration
{
	static $attached = false;
	static $instance = null;

	public function __construct()
	{
		$config = EasyBlogHelper::getConfig();

		// @legacy: If environment is set to production, change to static.
		$environment = $config->get('easyblog_environment');
		if ($environment=='production') {
			$environment='static';
		}

		$this->fullName    = 'EasyBlog';
		$this->shortName   = 'eb';
		$this->environment = $environment;
		$this->mode        = $config->get('easyblog_mode');		
		$this->version     = (string) EasyBlogHelper::getLocalVersion();
		$this->baseUrl     = EasyBlogHelper::getBaseUrl();
		$this->token       = EasyBlogHelper::getToken();
		$this->options     = array(
			"scriptVersioning" => (bool) $config->get('main_script_versioning'),
			"responsive"       => (bool) $config->get('layout_responsive')
		);

		parent::__construct();
	}

	public static function getInstance()
	{
		if (is_null(self::$instance)) {
			self::$instance	= new self();
		}

		return self::$instance;
	}

	public function update()
	{
		// We need to call parent's update method first
		// because they will automatically check for
		// url overrides, e.g. es_env, es_mode.
		parent::update();

		switch ($this->environment) {

			case 'static':
			default:
				$this->scripts = array(
					'easyblog-' . $this->version . '.static'
				);
				break;

			case 'optimized':
				$this->scripts = array(
					'easyblog-' . $this->version . '.optimized'
				);
				break;

			case 'development':
				$this->scripts = array(
					'easyblog'
				);
				break;
		}
	}

	public function attach()
	{
		if (self::$attached) return;

		parent::attach();

		if ($this->environment!=="development") {

			// Get resources
			$compiler = new EasyBlogCompiler();
			$resource = $compiler->getResources();
			
			// Attach resources
			if (!empty($resource)) {
			
				$scriptTag = $this->createScriptTag($resource["uri"]);

				$document = JFactory::getDocument();
				$document->addCustomTag($scriptTag);
			}
		}
		
		self::$attached = true;		
	}
}