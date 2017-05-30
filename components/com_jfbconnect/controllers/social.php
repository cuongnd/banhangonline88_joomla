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

class JFBConnectControllerSocial extends JFBConnectController
{

    function display($cachable = false, $urlparams = false)
    {
        exit;
    }

    public function comment()
    {
        JSession::checkToken('get') or die();

        $input = JFactory::getApplication()->input;
        $href = $input->get('href', '', 'string');
        $href = urldecode($href);

        $type = $input->get('type');
        $title = $input->get('title');

        if (!$href || $href == "undefined" || !$type)
            exit;

        $this->awardPoints('facebook.comment.' . $type, $href);

        // Check if admin email should be sent
        if ($type != "create" || !JFBCFactory::config()->getSetting('social_notification_comment_enabled'))
            exit;

        $subject = JText::_('COM_JFBCONNECT_NEW_COMMENT_SUBJECT');
        $body = JText::sprintf('COM_JFBCONNECT_NEW_COMMENT_BODY', $this->getPoster(), $title, $href);

        $this->sendEmail($subject, $body);
        exit;
    }

    public function share()
    {
        JSession::checkToken('get') or die();

        $input = JFactory::getApplication()->input;
        $href = $input->get('href', '', 'string');
        $id = $input->get('id', '', 'string');
        $href = urldecode($href);

        $provider = $input->get('provider', '', 'string');
        $share = $input->get('share', '', 'string');
        $type = $input->get('type');
        $title = $input->get('title');

        if ((!$id && (!$href || $href == "undefined")) || !$provider || !$share || !$type)
            exit;

        $this->awardPoints($provider . '.' . $share . '.' . $type, $href);

        // Check if admin email should be sent
        if ($provider != 'facebook' || $type != "create" || !JFBCFactory::config()->getSetting('social_notification_like_enabled'))
            exit;

        $subject = JText::_('COM_JFBCONNECT_NEW_LIKE_SUBJECT');
        $body = JText::sprintf('COM_JFBCONNECT_NEW_LIKE_BODY', $this->getPoster(), $title, $href);

        $this->sendEmail($subject, $body);
        exit;
    }

    private function awardPoints($name, $href)
    {
        $point = new JFBConnectPoint();
        $point->set('name', $name);
        $point->set('key', $href);
        $point->award();
    }

    private function getPoster()
    {
        $user = JFactory::getUser();
        if ($user->guest)
            return "Guest";
        else
            return $user->get('username');
    }

    private function sendEmail($subject, $body)
    {
        $toname = JFBCFactory::config()->getSetting('social_notification_email_address');
        $toname = explode(',', $toname);
        // Don't send emails to no one :)
        if ($toname[0] == "")
            return;

        $app = JFactory::getApplication();
        $sitename = $app->getCfg('sitename');
        $mailfrom = $app->getCfg('mailfrom');
        $fromname = $app->getCfg('fromname');
        $subject = $subject . " - " . $sitename;

        JFactory::getMailer()->sendMail($mailfrom, $fromname, $toname, $subject, $body);
    }


}
