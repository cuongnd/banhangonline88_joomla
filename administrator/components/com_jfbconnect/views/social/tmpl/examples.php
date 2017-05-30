<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');

$filter_provider = $this->filter_provider;
$providersWithWidgets = JFBCFactory::getAllWidgetProviderNames();
?>
<p><?php echo JText::_('COM_JFBCONNECT_SOCIAL_EXAMPLES_DESC');?></p>
<p><?php echo JText::_('COM_JFBCONNECT_SOCIAL_EXAMPLES_DESC2');?></p>

<div id="filter-bar" class="btn-toolbar">
    <div class="btn-group pull-left">
        <select name="filter_provider" id="filter_provider" class="input-large" onchange="this.form.submit()">
            <option value="all">- <?php echo JText::_('COM_JFBCONNECT_SOCIAL_EXAMPLES_SELECT_PROVIDER_TYPE');?> -</option>
            <?php foreach($providersWithWidgets as $providerName):?>
                <option value="<?php echo strtolower($providerName); ?>" <?php if (strtolower($filter_provider) == strtolower($providerName)) echo 'selected="selected"'; ?>><?php echo ucfirst($providerName); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
<div class="clearfix"> </div>

<?php
//set filter to empty to show all
if($filter_provider == 'all') $filter_provider = '';

foreach($providersWithWidgets as $providerName)
{
    if($filter_provider && $filter_provider != $providerName) continue;

    $widgets = JFBCFactory::getAllWidgets($providerName);
    foreach($widgets as $widget)
    {
        echo '<p> </p><h3>'.ucfirst($providerName) . ' - '.$widget->getName().'</h3>';
        if(isset($widget->examples))
            echo implode('<br/>', $widget->examples).'<br/><br/>';
        echo '<table class="table table-striped">
            <tr>
                <th>'.JText::_('COM_JFBCONNECT_SOCIAL_EXAMPLES_PARAMETER').'</th>
                <th>'.JText::_('COM_JFBCONNECT_SOCIAL_OPTIONS').'</th>
                <th>'.JText::_('COM_JFBCONNECT_SOCIAL_EXAMPLES_DESCRIPTION').'</th>
            </tr>';

        $xmlFile = JPATH_ROOT .'/components/com_jfbconnect/libraries/provider/'.$providerName.'/widget/'.$widget->getSystemName().'.xml';
        $rawXml = simplexml_load_file($xmlFile);

        $fields = $rawXml->xpath("//field");
        foreach($fields as $field)
        {
            $options = array();
            $attributes = $field->attributes();

            if ($attributes['jfbcExamplesHide'] && strval($attributes['jfbcExamplesHide'] == '1'))
                continue;

            echo '<tr><td>'.strval($attributes['name']).'</td>';

            $fieldType = strval($attributes['type']);
            if($fieldType == 'radio' || $fieldType == 'list')
            {
                echo '<td>';
                $path = "//field[@name='" . strval($attributes['name'])."']/option";
                $rawField = $rawXml->xpath($path);
                foreach ($rawField as $val)
                {
                    $attrbs = $val->attributes();
                    $value = strval($attrbs['value']);
                    if($value == '1')
                        $options[]='true';
                    else if($value == '0')
                        $options[]='false';
                    $options[] = $value;
                }

                echo implode($options, ', ');
                echo '</td>';
            }
            else if ($fieldType == 'hidden')
            {
                // If this isn't specifically hidden, we're going to call it a text field so it's setable in the Easy-Tag
                echo '<td>text</td>';
            }
            else
                echo '<td>'.$fieldType.'</td>';
            echo '<td>'.JText::_($attributes['description']).'</td></tr>';
        }
        echo '</table>';
    }
}
?>
<p></p>
<h3>Graph:</h3>
<?php echo JText::_('COM_JFBCONNECT_SOCIAL_EXAMPLES_GRAPH_URL');?><br/><br/>
<?php echo JText::_('COM_JFBCONNECT_SOCIAL_EXAMPLES_EXAMPLE');?>: {SCOpenGraph url=http://www.sourcecoast.com}<br/>
<?php echo JText::_('COM_JFBCONNECT_SOCIAL_EXAMPLES_EXAMPLE');?>: {SCOpenGraph image=http://www.sourcecoast.com/images/stories/extensions/jfbconnect/home_jfbconn.jpg}<br/>
<?php echo JText::_('COM_JFBCONNECT_SOCIAL_EXAMPLES_EXAMPLE');?>: {SCOpenGraph description=Facebook connect integration for Joomla! Let users register and log into your site with their Facebook credentials.}
<br/><br/><strong><em><?php echo JText::_('COM_JFBCONNECT_SOCIAL_EXAMPLES_GRAPH_INSTRUCTIONS');?></em></strong><br/>
<br/>
<table class="table table-striped">
    <tr>
        <th><?php echo JText::_('COM_JFBCONNECT_SOCIAL_EXAMPLES_PARAMETER');?></th>
        <th><?php echo JText::_('COM_JFBCONNECT_SOCIAL_EXAMPLES_EXAMPLE');?></th>
    </tr>
    <tr>
        <td class="even">title</td>
        <td class="even">title=JFBConnect</td>
    </tr>
    <tr>
        <td class="odd">type</td>
        <td class="odd">type=company</td>
    </tr>
    <tr>
        <td class="even">url</td>
        <td class="even">url=http://joomla-facebook.com</td>
    </tr>
    <tr>
        <td class="odd">image</td>
        <td class="odd">image=http://www.sourcecoast.com/images/stories/extensions/jfbconnect/home_jfbconn.jpg</td>
    </tr>
    <tr>
        <td class="even">site_name</td>
        <td class="even">site_name=SourceCoast</td>
    </tr>
    <tr>
        <td class="odd">description</td>
        <td class="odd">description=Joomla Facebook Connect integration, payment systems, and custom Joomla development
            based in Austin, TX
        </td>
    </tr>
</table>
<p></p>