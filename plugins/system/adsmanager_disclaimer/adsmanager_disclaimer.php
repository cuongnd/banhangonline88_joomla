<?php
/**
 * Content plugin for show a disclaimer, adult warning, age check or something
 * similar before users can view an article.
 *
 * @version		disclaimer.php, v1.7, rev. 237, May 2013.
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) Adonay R. M. All rights reserved.
 * @author		Adonay R. M. -> http://adonay.name/
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
 * PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM,
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
/**
* Guess what? TModel,Controller are interfaces in Joomla! 3.0. Holly smoke, Batman!
*/
if(!class_exists('TController')) {
   if(interface_exists('JController')) {
       abstract class TController extends JControllerLegacy {}
   } else {
       jimport('joomla.application.component.controller');
       class TController extends JController {}
   }
}

if(!class_exists('TModel')) {
   if(interface_exists('JModel')) {
       abstract class TModel extends JModelLegacy {}
   } else {
       jimport('joomla.application.component.model');
       class TModel extends JModel {}
   }
}
if(!class_exists('TView')) {
   if(interface_exists('JView')) {
       abstract class TView extends JViewLegacy {}
   } else {
       jimport('joomla.application.component.view');
       class TView extends JView {}
   }
}

class plgSystemAdsmanager_disclaimer extends JPlugin
{
	public function onAfterRender()
	{
        $app = JFactory::getApplication();
		if ($app->isAdmin() == true){
            return true;
        }
        
        if(JRequest::getCmd('option') != "com_adsmanager"){
            return true;
        }
        
        include_once(JPATH_ROOT."/components/com_adsmanager/lib/core.php");
        
        $adsManagerConf = TConf::getConfig();
        if(@$adsManagerConf->disclaimer_categories == null){
			return true;
		}
		$categoriesId = @$adsManagerConf->disclaimer_categories;
		if(!is_array($categoriesId))
			$categoriesId = array();
        $text = $adsManagerConf->disclaimer_message;
        
        $catid = JRequest::getInt("catid", 0);
        
        if(!in_array($catid, $categoriesId)){
            return true;
        }

		// include style
        $header = "";
        
        // duration of the cookie
        $parameters = $this->params;
		$duration = $parameters->get ('duration');

		if (empty ($duration) || $duration == 0) $duration = 1;
		$script = '<script>var disclaimer_duration='.$duration.';</script>';
        
        $header .= $script;
        $header .= '<script src="'.JUri::root().'plugins/system/adsmanager_disclaimer/js/scripts.js" type="text/javascript"></script>';
		$header .= '<link rel="stylesheet" href="plugins/system/adsmanager_disclaimer/css/style.css" type="text/css" />';

		$lang = JFactory::getLanguage();
		$lang->load('plg_content_adsmanager_disclaimer', JPATH_ADMINISTRATOR);

		$warning = JText::_('WARNING');
		$defaultText = JText::_('DEFAULT_DISCLAIMER');
		$open = JText::_('ENTER');
		$exit = JText::_('LEAVE');

		$warningText = $parameters->get ('warningtext');
		$blinktext = $parameters->get ('blinktext');
        
		if (empty ($text) || $text == "\r\n") $text = $defaultText;
		if (!empty ($blinktext)) $warning = $blinktext;
		if (!$warningText) unset ($warning);

		$textopen = $parameters->get ('textopen');
		$textexit = $parameters->get ('textexit');

		if (empty ($textopen)) $textopen = $open;
		if (empty ($textexit)) $textexit = $exit;
    
		$redir = $parameters->get ('redir');

		if (empty ($redir)) $redir = 'http://www.google.com/';

		$backgroundcolor = $parameters->get ('backgroundcolor');

		if (empty ($backgroundcolor) || $backgroundcolor === 0) $backgroundcolor = '#3D3D3D';

		$mybackgroundcolor = $parameters->get ('mybackgroundcolor');

		if (!empty ($mybackgroundcolor)) $backgroundcolor = $mybackgroundcolor;

		$colortext = $parameters->get ('colortext');

		if (empty ($colortext)) $colortext = '#FFFFFF';

		$align = $parameters->get ('align');

		if (empty ($align) || $align == 0) $align = 'center';
		if ($align == 1) $align = 'justify';

		$image = $parameters->get ('image');

		if (empty ($image)) $image = 'url(\'plugins/system/adsmanager_disclaimer/images/disclaimer.png\') no-repeat scroll center center transparent';
		else $image = 'url(\''.$image.'\') no-repeat scroll center center transparent';
        $output = JResponse::getBody();
		$content = '	
				<div id="popup">
				 <div id="dialog" class="window" style="background-color: '.$backgroundcolor.';">
				  <div id="logopopup" style="background: '.$image.';"></div>
				  <h6 class="warning" style="text-align: '.$align.'; color: '.$colortext.';"><span>'.$warning.' </span>'.$text.'</h6>
				  <div id="buttons">
				   <div><a href="#" id="disclaimer_open" class="enter readmore btn btn-primary button-primary readon">'.$textopen.'</a></div>
				   <div><a href="'.$redir.'" class="exit readmore btn button-default readon">'.$textexit.'</a></div>
				  </div>
				 </div>
				 <div style="opacity: 0.9;" id="overlay"></div>
				</div> ';
		
        $output .= $content;
        $output = str_replace('</head>', $header."</head>", $output);
        
        JResponse::setBody($output);
        
        return true;
	}
}