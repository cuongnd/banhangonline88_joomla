<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @build-date      2014/10/03
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('sourcecoast.articleContent');
jimport('sourcecoast.openGraphPlugin');

class plgOpenGraphCustom extends OpenGraphPlugin
{
    protected function init()
    {
        // Pretty name for the extensions that this plugin supports
        $this->extensionName = "Custom Object";
        // This plugin works on all pages of the site. If it should work only in certain extensions, add them here (com_content, etc)
        // $this->supportedComponents[] = '';

        // Define all types of pages this component can create and would be 'objects'
        #$this->addSupportedObject("Pretty name for display", "system_name");

        $this->addSupportedObject("Custom Object Type", "custom");

        // Add actions that are built-in to the component that would need to be 'hooked' into (commenting, voting, etc).
        // Things that trigger just by loading the page should not be defined here unless extra logic is required
        // Actions such as clicking a custom button only intended for sharing to the Timeline should *not* be here.
        // For those, simply define the action in the admin area and add the proper Javascript to the button.

        // ie. Don't define reading, share, or Like an article (those are not tasks/features of com_content)
        // Do define things like voting for an article (that's a feature built-into com_content
        #$this->addSupportedAction("Vote", "vote");
    }

    /* Method to determine the best ogObject instance the passed in queryVars would apply to, if any.
     *
     * @param array $queryVars  Associative array of query variable for a given page. Values *must* be filtered if they are to be used in DB calls
     *
     * @return ogObject     The best ogObject definition found, or null if none
     */
    protected function findObjectType($queryVars)
    {
        $object = null;
        // Get all the custom objects the admin has created
        $types = $objectTypes = $this->getObjects('custom');

        // Loop over each definition to see if this page fits the criteria
        foreach ($types as $type)
        {
            $matchParams = $type->params->get('og_query_parameters');
            $matchParams = explode("\n", $matchParams);
            $match = true;
            foreach ($matchParams as $mp)
            {
                $pair = explode("=", $mp);
                if (array_key_exists($pair[0], $queryVars) && ($queryVars[$pair[0]] == trim($pair[1])))
                    continue;
                else
                {
                    $match = false;
                    break;
                }
            }
            if ($match)
            {
                $object = $type;
                break;
            }
        }

        // Return whatever was found, or null if nothing
        return $object;
    }

    /* Method to set the Open Graph tags on the page according to the Object that was found in findObjectType
    * This method can use the $this->object variable to get whatever information it needs on how the tags should be set for this type of view
     * The Open Graph 'type' should *not* be set here. All other tags are fair game.
    */
    protected function setOpenGraphTags()
    {
        $tags = $this->object->params->get('og_open_graph_tags');
        $tags = explode("\n", $tags);
        foreach ($tags as $tag)
        {
            $pair = explode("=", $tag);
            // Add this open graph tag to the page. If the final parameter is false, other objects or {SCOpenGraph} tags can overwrite the value.
            //  If true, this tag is final.
            if ($pair[0] != "")
                $this->addOpenGraphTag($pair[0], trim($pair[1]), false);
        }
    }

    /************* DEFINED ACTIONS CALLS *******************/

    /*    protected function checkActionAfterRoute($action)
    {
        // See the Open Graph content plugin for an example of a defined action
    }*/

}