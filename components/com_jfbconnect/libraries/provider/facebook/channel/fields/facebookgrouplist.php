<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldFacebookGroupList extends JFormFieldList
{
    public $type = 'FacebookGroupList';

    protected function getOptions()
    {
        $options = array();
        $options[] = JHtml::_('select.option', "--", "-- ".JText::_('COM_JFBCONNECT_CHANNEL_FACEBOOK_GROUP_SELECT_LABEL')." --");

        $jid = $this->form->getValue('attribs.user_id');
        $uid = JFBCFactory::usermap()->getProviderUserId($jid, 'facebook');
        $access_token = JFBCFactory::usermap()->getUserAccessToken($jid, 'facebook');
        $params['access_token'] = $access_token;
        $groups = JFBCFactory::provider('facebook')->api('/' . $uid . '/groups/', $params, true, 'GET');

        if (isset($groups['data']) && count($groups['data']) > 0)
        {
            foreach ($groups['data'] as $g)
            {
                $options[] = JHtml::_('select.option', strtolower($g['id']), $g['name']);
            }
        }
        return $options;
    }

    function getInput()
    {
        $jid = $this->form->getValue('attribs.user_id');
        if ($jid)
        {
            $uid = JFBCFactory::usermap()->getProviderUserId($jid, 'facebook');
            if ($uid)
            {
                if (!JFBCFactory::provider('facebook')->hasScope($uid, 'user_groups'))
                    return '<div class="jfbc-error">'.JText::_('COM_JFBCONNECT_CHANNEL_FACEBOOK_PERM_USER_GROUPS_ERROR_LABEL').'</div>';
                else if (!JFBCFactory::provider('facebook')->hasScope($uid, 'publish_actions'))
                    return '<div class="jfbc-error">'.JText::_('COM_JFBCONNECT_CHANNEL_FACEBOOK_PERM_PUBLISH_ACTIONS_ERROR_LABEL').'</div>';
                else
                    return parent::getInput();
            }
            else
            {
                return '<div class="jfbc-error">'.JText::_('COM_JFBCONNECT_CHANNEL_FACEBOOK_PERM_PAGE_USER_AUTH_ERROR_LABEL').'</div>';
            }

        }
        else
            return '<div class="jfbc-error">'.JText::_('COM_JFBCONNECT_CHANNEL_SELECT_USER_ERROR_LABEL').'</div>';
    }
}