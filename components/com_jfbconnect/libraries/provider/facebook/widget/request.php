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

class JFBConnectProviderFacebookWidgetRequest extends JFBConnectProviderFacebookWidget
{
    var $name = "Request";
    var $systemName = "request";
    var $className = "jfbcrequest";
    var $tagName = "jfbcrequest";
    var $examples = array (
        '{JFBCRequest request_id=1 link_image=http://www.sourcecoast.com/templates/sourcecoast/images/logo.png}',
        '{JFBCRequest request_id=1 link_text=Invite Friends}'
    );

    protected function getTagHtml()
    {
        $requestID = $this->getParamValue('request_id');
        $linkText = $this->getParamValue('link_text');
        $linkImage = $this->getParamValue('link_image');

        $tagString = '';
        if ($requestID != '')
        {
            JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_jfbconnect/models');
            $requestModel = JModelLegacy::getInstance('Request', "JFBConnectModel");
            $request = $requestModel->getData($requestID);

            if ($request && $request->published)
            {
                $message = str_replace("\r\n", " ", $request->message);
                $linkValue = $linkText;
                if ($linkImage != '')
                    $linkValue = '<img src="' . $linkImage . '" alt="' . $request->title . ' "/>';

                $tagString = '<a href="javascript:void(0)" onclick="jfbc.request.popup(' . $requestID . '); return false;">' . $linkValue . '</a>';
                $tagString .=
                    <<<EOT
                        <script type="text/javascript">
    var jfbcRequests = Object.prototype.toString.call(jfbcRequests) == "[object Array]" ? jfbcRequests : [];
    var jfbcRequest = new Object;
    jfbcRequest.title = "{$request->title}";
    jfbcRequest.message = "{$message}";
    jfbcRequest.destinationUrl = "{$request->destination_url}";
    jfbcRequest.thanksUrl = "{$request->thanks_url}";
    jfbcRequests[{$requestID}] = jfbcRequest;
</script>
EOT;
            }
        }
        return $tagString;
    }
}
