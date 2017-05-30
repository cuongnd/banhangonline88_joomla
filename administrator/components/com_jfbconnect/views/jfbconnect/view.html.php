<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('sourcecoast.adminHelper');

class JFBConnectViewJfbconnect extends JViewLegacy
{
    function display($tpl = null)
    {
        $configModel = JFBCFactory::config();
        $usermapModel = JFBCFactory::usermap();
        $autotuneModel = JModelLegacy::getInstance('AutoTune', 'JFBConnectModel');

        if (JFBCFactory::provider('facebook')->appId)
        {
            $appConfig = JFBCFactory::config()->get('autotune_app_config', null);
            if (!$appConfig || count($appConfig) == 0)
            {
                $app = JFactory::getApplication();
                $app->enqueueMessage(JText::sprintf('COM_JFBCONNECT_MSG_RUN_AUTOTUNE', '<a href="index.php?option=com_jfbconnect&view=autotune">AutoTune</a>'), 'error');
            }
        }

        $userCounts = array();
        foreach (JFBCFactory::getAllProviders() as $p)
        {
            if ($p->appId)
                $userCounts[$p->systemName] = $usermapModel->getTotalMappings($p->systemName);
        }

        $this->configModel = $configModel;
        $this->autotuneModel = $autotuneModel;
        $this->usermapModel = $usermapModel;
        $this->userCounts = $userCounts;

        $this->addToolbar();

        parent::display($tpl);
    }

    function addToolbar()
    {
        JToolBarHelper::title('JFBConnect', 'jfbconnect.png');
        SCAdminHelper::addAutotuneToolbarItem();
    }

    function getFeed()
    {
        $feedHtml = JFBCFactory::cache()->get('sourcecoast.rss');
        if ($feedHtml === false)
        {
            $curl = curl_init();

            curl_setopt_array($curl, Array(
                CURLOPT_URL            => 'http://feeds.sourcecoast.com/sourcecoast-blog',
                CURLOPT_USERAGENT      => 'spider',
                CURLOPT_TIMEOUT        => 120,
                CURLOPT_CONNECTTIMEOUT => 30,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_ENCODING       => 'UTF-8'
            ));

            $data = curl_exec($curl);
            $errorCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if($data && !empty($data) && $errorCode == 200)
            {
                $xml = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
                if($xml)
                {
                    $feedHtml = '<h4>Recent News from SourceCoast</h4>';
                    for($i=0; $i<4; $i++) // Only show four items
                    {
                        if(isset($xml->channel->item[$i]))
                        {
                            $item = $xml->channel->item[$i];
                            $date = JFactory::getDate($item->pubDate);
                            $dateStr = $date->format(JText::_('DATE_FORMAT_LC4'));
                            $feedHtml .= '<p><a href="'.$item->link.'">' . $item->title . '</a> <span><em>'.$dateStr.'</em></span></p>';
                        }
                    }
                }
            }
            JFBCFactory::cache()->store($feedHtml, 'sourcecoast.rss');
        }

        return $feedHtml;
    }
}
