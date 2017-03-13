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

class JFormFieldLinkedinCompanyList extends JFormFieldList
{
    public $type = 'LinkedinCompanyList';
    private $companies;

    protected function getOptions()
    {
        $options = array();
        $options[] = JHtml::_('select.option', "--", "-- Select a Company --");
        foreach ($this->companies->values as $c)
        {
            $options[] = JHtml::_('select.option', strtolower($c->id), $c->name);
        }

        return $options;
    }

    function getInput()
    {
        $jid = $this->form->getValue('attribs.user_id');
        if ($jid)
        {
            $uid = JFBCFactory::usermap()->getProviderUserId($jid, 'linkedin');
            if ($uid)
            {
                $access_token = JFBCFactory::usermap()->getUserAccessToken($jid, 'linkedin');
                $params['access_token'] = $access_token;
                $liLibrary = JFBCFactory::provider('linkedin');
                $liLibrary->client->setToken((array)$access_token);

                $url = 'https://api.linkedin.com/v1/companies/?is-company-admin=true&start=0&count=20';
                try
                {
                    $companies = $liLibrary->client->query($url);
                    if ($companies->code == '200')
                    {
                        $this->companies = json_decode($companies->body);
                    }
                    return parent::getInput();
                }
                catch (Exception $e)
                {
                    return '<div class="jfbc-error">'.JText::_('COM_JFBCONNECT_CHANNEL_LINKEDIN_PERM_TOKEN_EXPIRED_LABEL').'</div>';
                }
            }
            else
            {
                return '<div class="jfbc-error">'.JText::_('COM_JFBCONNECT_CHANNEL_LINKEDIN_PERM_USER_AUTH_ERROR_LABEL').'</div>';
            }

        }
        else
            return '<div class="jfbc-error">'.JText::_('COM_JFBCONNECT_CHANNEL_SELECT_USER_ERROR_LABEL').'</div>';
    }
}