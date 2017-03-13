<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class JFBConnectToolbarButtonMeta extends JFBConnectToolbarButton
{
    var $order = '10';
    var $displayName = "Social Meta";
    var $systemName = "meta";

    public function html()
    {
        $openGraphLibrary = OpenGraphLibrary::getInstance();
        $ogGroups = array("Inserted", "Skipped", "Removed");

        $html = '
            <div id="social-meta" style="position:fixed; bottom:45px; display:none;">';
        foreach ($ogGroups as $group)
        {
            $groupName = 'tags' . $group;
            $tags = $this->getOGTagList("Inserted", $openGraphLibrary->$groupName, true);
            $html .=
                    <<<EOT
                                    <h3>{$group}</h3>
                <div>
                    <p>
                        {$tags}
                    </p>
                </div>
EOT;
        }
        $html .= "</div>";
        return $html;
    }

    protected function generateJavascript()
    {
        return "display: function ()
                    {
                        if (jfbcJQuery('#social-meta').css('display') == 'none')
                        {
                            jfbcJQuery('#social-meta').accordion();
                            jfbcJQuery('#social-meta').css('display', 'block');
                            jfbcJQuery('#social-meta').accordion('refresh');
                        }
                        else
                            jfbcJQuery('#social-meta').css('display', 'none');
                    }";
    }

    private function getOGTagList($type, $tags, $showValues)
    {
        $html = "None";
        if (count($tags))
        {
            $html = '<table class="table table-striped" width="90%">
            <thead><tr><th width="10%">' . $type . ' Tag</th>';
            if ($showValues)
                $html .= '<th width="70%">Value</th>';
            $html .= '<th width="20%">Origin</th></tr></thead><tbody>';

            foreach ($tags as $ogTag)
            {
                foreach ($ogTag->value as $ogTagValue)
                {
                    $html .= '<tr><td>' . $ogTag->name . '</td>';
                    if ($showValues)
                        $html .= '<td>' . $ogTagValue . '</td>';
                    $html .= '<td>' . $ogTag->origin . '</td></tr>';
                }
            }
            $html .= "</tbody></table>";
        }
        return $html;
    }

}