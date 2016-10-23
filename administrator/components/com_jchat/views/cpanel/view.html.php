<?php
// namespace administrator\components\com_jchat\views\cpanel;
/**
 *
 * @package JCHAT::CPANEL::administrator::components::com_jchat
 * @subpackage views
 * @subpackage cpanel
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html 
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport ( 'joomla.application.component.view' );

/**
 * CPanel view
 *
 * @package JCHAT::CPANEL::administrator::components::com_jchat
 * @subpackage views
 * @subpackage cpanel
 * @since 1.0
 */
class JChatViewCpanel extends JChatView {
	/**
	 * Renderizza l'iconset del cpanel
	 *
	 * @param $link string
	 * @param $image string
	 * @access private
	 * @return string
	 */
	private function getIcon($link, $image, $text, $target = '', $title = null, $class = 'icons') {
		$mainframe = JFactory::getApplication ();
		$lang = JFactory::getLanguage ();
		$option = $this->option;
		?>
		<div class="<?php echo $class;?>" style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a <?php echo $title;?> <?php echo $target;?> href="<?php echo $link; ?>"> 
					<div class="task <?php echo $image;?>"></div> 
					<span class="task"><?php echo $text; ?></span>
				</a>
			</div>
		</div>
		<?php
		}
		
	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addDisplayToolbar() {
		$doc = JFactory::getDocument();
		JToolBarHelper::title( JText::_('COM_JCHAT_CPANEL_TOOLBAR' ), 'jchat' );
		JToolBarHelper::custom('cpanel.display', 'home', 'home', 'COM_JCHAT_CPANEL', false);
	}
	
	/**
	 * Effettua il rendering del pannello di controllo
	 * @access public
	 * @return void
	 */
	public function display($tpl = null) {
		jimport ( 'joomla.html.pane' );
		$doc = JFactory::getDocument ();
		$this->loadJQuery($doc);
		$this->loadBootstrap($doc);
		$doc->addStylesheet ( JURI::root ( true ) . '/administrator/components/com_jchat/css/cpanel.css' );
		$doc->addScript ( JURI::root ( true ) . '/administrator/components/com_jchat/js/chart.js' );
		$doc->addScript ( JURI::root ( true ) . '/administrator/components/com_jchat/js/cpanel.js' );
		
		// Inject js translations
		$translations = array (	'COM_JCHAT_TOTALUSERS_CHART',
								'COM_JCHAT_LOGGEDUSERS_CHART',
								'COM_JCHAT_TOTALMESSAGES_CHART',
								'COM_JCHAT_TOTALFILEMESSAGES_CHART',
								'COM_JCHAT_TOTALBANNEDUSERS_CHART',
								'COM_JCHAT_TOTALVIDEOCHATSESSIONS_CHART',
								'COM_JCHAT_EXPIREON',
								'COM_JCHAT_CLICKTOUPDATE',
								'COM_JCHAT_UPDATEPROGRESSTITLE',
								'COM_JCHAT_DOWNLOADING_UPDATE_SUBTITLE',
								'COM_JCHAT_INSTALLING_UPDATE_SUBTITLE',
								'COM_JCHAT_COMPLETED_UPDATE_SUBTITLE');
		$this->injectJsTranslations($translations, $doc);
		
		// Buffer delle icons
		ob_start ();
		$this->getIcon ( 'index.php?option=com_jchat&task=rooms.display', 'icon-grid-view', JText::_ ( 'COM_JCHAT_ROOMS' ) );
		$this->getIcon ( 'index.php?option=com_jchat&task=messages.display', 'icon-mail', JText::_ ( 'COM_JCHAT_MESSAGES' ) );
		$this->getIcon ( 'index.php?option=com_jchat&task=config.display#_webrtc', 'icon-camera-2', JText::_ ( 'COM_JCHAT_WEBRTC' ) );
		$this->getIcon ( 'index.php?option=com_jchat&task=config.display#_chatpermissions', 'icon-locked', JText::_ ( 'COM_JCHAT_PERMISSIONS' ) );
		$this->getIcon ( 'index.php?option=com_jchat&task=config.display#_rendering', 'icon-color-palette', JText::_ ( 'COM_JCHAT_RENDERING' ) );
		$this->getIcon ( 'index.php?option=com_jchat&task=lamessages.display', 'icon-mail-2', JText::_ ( 'COM_JCHAT_TICKETS' ) );
		$this->getIcon ( 'index.php?option=com_jchat&task=users.display', 'icon-users', JText::_ ( 'COM_JCHAT_USERS' ) );
		$this->getIcon ( 'index.php?option=com_jchat&task=config.display#_social_login', 'icon-power-cord', JText::_ ( 'COM_JCHAT_SOCIAL_LOGIN' ) );
		$this->getIcon ( 'index.php?option=com_jchat&task=emoticons.display', 'icon-pictures', JText::_ ( 'COM_JCHAT_EMOTICONS' ) );
		$this->getIcon ( 'index.php?option=com_jchat&task=recorder.display', 'icon-play-2', JText::_ ( 'COM_JCHAT_RECORDED_MEDIAS' ) );
		$this->getIcon ( 'index.php?option=com_jchat&task=config.display', 'icon-cog', JText::_ ( 'COM_JCHAT_CONFIG' ) );
		$this->getIcon ( 'index.php?option=com_jchat&task=cpanel.purgeFileCache', 'icon-cancel', JText::_ ( 'COM_JCHAT_PURGECACHE' ) );
		$this->getIcon ( 'index.php?option=com_jchat&task=cpanel.purgeDbCache', 'icon-cancel', JText::_ ( 'COM_JCHAT_PURGECACHE_DB' ) );
		$this->getIcon ( 'http://storejextensions.org/jchatsocial_enterprise_documentation.html', 'icon-help', JText::_ ( 'COM_JCHAT_HELP' ) );
		
		$contents = ob_get_clean ();
		
		$infoData = $this->getModel()->getData();
		$doc->addScriptDeclaration('var jchatChartData = ' . json_encode($infoData));
		
		// Assign reference variables
		$this->icons = $contents;
		$this->componentParams = JComponentHelper::getParams('com_jchat');
		$this->updatesData = $this->getModel()->getUpdates($this->get('httpclient'));
		$this->infodata = $infoData;
		$this->currentVersion = strval(simplexml_load_file(JPATH_COMPONENT_ADMINISTRATOR . '/jchat.xml')->version);
		
		// Add toolbar
		$this->addDisplayToolbar();
		
		// Output del template
		parent::display ();
	}
}
?>