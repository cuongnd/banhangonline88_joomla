<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'string.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'date.php' );

class EasyBlogViewBlogger extends EasyBlogView
{
	var $err	= null;

    function showSubscription($bloggerId)
    {
        $my     = JFactory::getUser();
        $ejax	= new Ejax();

        $tpl = new CodeThemes();
        $tpl->set('my', $my );
        $tpl->set('blogId', $bloggerId );

        $options      = new stdClass();
        $options->title   = JText::_('COM_EASYBLOG_SUBSCRIBE_BLOGGER');
        $options->content = $tpl->fetch( 'blogger.subscription.box.php' );

      	$ejax->dialog( $options );
      	$ejax->send();
      	return;
    }

    function addSubscription( $post )
    {
		$ejax			= new Ejax();
		$mainframe		= JFactory::getApplication();
		$my				= JFactory::getUser();
		$config 		= EasyBlogHelper::getConfig();
		$isModerate		= false;

		$userId			= $post['userid'];
		$email  		= $post['email'];
		$bloggerId		= $post['id'];

        if(JString::trim($email) == '')
        {
          $ejax->alert(JText::_('COM_EASYBLOG_SUBSCRIPTIONS_EMAIL_IS_EMPTY'), JText::_('COM_EASYBLOG_ERROR') , '450', 'auto');
          $ejax->send();
          return;
        }

        //check if userid not empty (site member)
            //check the userid exists in table. If exists, just do a update incase user email has been updated.
        //if userid empty (non-site member)
            //check if the email address already in table.
                //if yes, show message say the email already in.
                //if not, add the email into table

        $model	= $this->getModel( 'Blogger' );
        $sid    = '';

        if($userId == 0)
        {
          $sid = $model->isBloggerSubscribedEmail($bloggerId, $email);
          if($sid != '')
          {
              //user found.
              // show message.
              $ejax->alert( JText::_('COM_EASYBLOG_SUBSCRIBE_BLOGGER_ALREADY_SUBSCRIBED'), JText::_('COM_EASYBLOG_WARNING') , '450', 'auto');
              $ejax->send();
              return;

          }
          else
          {
            $model->addBloggerSubscription($bloggerId, $email);
          }
        }
        else
        {
          $sid = $model->isBloggerSubscribedUser($bloggerId, $userId);

          if($sid != '')
          {
              // user found.
              // update the email address
              $model->updateBloggerSubscriptionEmail($sid, $email);

          }
          else
          {
            //add new subscription.
            $model->addBloggerSubscription($bloggerId, $email, $userId);
          }
        }

        $ejax->alert( JText::_('COM_EASYBLOG_SUBSCRIBE_BLOGGER_SUCCESS'), JText::_('COM_EASYBLOG_INFO') , '450', 'auto');
        $ejax->send();
        return;

//        echo '<pre>';
//        print_r($post);
//        echo '</pre>';
//        exit;

    }

}
