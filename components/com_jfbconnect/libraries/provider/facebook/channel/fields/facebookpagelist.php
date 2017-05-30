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

class JFormFieldFacebookPageList extends JFormFieldList
{
    public $type = 'FacebookPageList';

    protected function getOptions()
    {
        $options = array();
        $options[] = JHtml::_('select.option', "--", "-- ".JText::_('COM_JFBCONNECT_CHANNEL_FACEBOOK_PAGE_SELECT_LABEL')." --");

        $jid = $this->form->getValue('attribs.user_id');
        $uid = JFBCFactory::usermap()->getProviderUserId($jid, 'facebook');
        $access_token = JFBCFactory::usermap()->getUserAccessToken($jid, 'facebook');
        $params['access_token'] = $access_token;
        $pages = JFBCFactory::provider('facebook')->api('/' . $uid . '/accounts/', $params, true, 'GET');

        if (isset($pages['data']) && count($pages['data']) > 0)
        {
            foreach ($pages['data'] as $p)
            {
                $options[] = JHtml::_('select.option', strtolower($p['id']), $p['name'] . " (" . $p['category'] . ')');
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
                if (!JFBCFactory::provider('facebook')->hasScope($uid, 'manage_pages'))
                    return '<div class="jfbc-error">'.JText::_('COM_JFBCONNECT_CHANNEL_FACEBOOK_PERM_MANAGE_PAGES_ERROR_LABEL').'</div>';
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