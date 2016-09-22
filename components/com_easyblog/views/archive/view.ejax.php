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
require_once(EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'calendar.php');
require_once(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'archive.php');

class EasyBlogViewArchive extends EasyBlogView
{
	var $err	= null;

    function loadCalendar($position='module', $itemId = '0', $size='small', $type='blog', $timestamp='')
    {
        $my     = JFactory::getUser();
		$ajax	= new Ejax();
		$config = EasyBlogHelper::getConfig();
		$tpl	= new CodeThemes();
		$model	= new EasyBlogModelArchive();
		$allowed 	= array( 'module' , 'component' );

		if( !in_array( $position , $allowed ) )
		{
			return;
		}
		
		require_once(JPATH_ROOT . '/components/com_easyblog/router.php');
		
		if( !empty( $itemId ) )
		{
		    JRequest::setVar( 'Itemid', $itemId);
		}

		//get date.
		$date	= EasyBlogCalendarHelper::processDate($timestamp);

		//get the required data to build the calendar.
		$data	= EasyBlogCalendarHelper::prepareData($date);

		//get the postcount
		//$postCount 	= $model->getArchivePostCountByMonth($date['month'], $date['year']);
		$postData	= $model->getArchivePostByMonth( $date[ 'month' ] , $date[ 'year' ] );


		switch($position)
		{
			case 'module';
				$namespace	= 'mod_easyblogcalendar';
				$preFix		= 'mod_easyblog';
                $ajax->script('mod_easyblogcalendar.calendar.setItemId("' . $itemId . '");');
				break;
			case 'component';
				$namespace	= 'eblog';
				$preFix		= 'com_easyblog';
				break;
		}

		$previous	= $namespace.'.calendar.reload( \'archive\' , \'loadCalendar\', \''.$position.'\', \''.$itemId.'\', \''.$size.'\', \''.$type.'\', \''.$data->previous.'\');';
		$next 		= $namespace.'.calendar.reload( \'archive\' , \'loadCalendar\', \''.$position.'\', \''.$itemId.'\', \''.$size.'\', \''.$type.'\', \''.$data->next.'\');';

		$tpl->set('calendar', $data );
		$tpl->set('date', $date );
		$tpl->set('postData', $postData );
		$tpl->set('previous', $previous );
		$tpl->set('next', $next );
		$tpl->set('namespace', $namespace );
		$tpl->set('preFix', $preFix );
		$tpl->set('itemId', $itemId );

		$layout = $tpl->fetch( 'calendar.'.$size.'.php' );

		$ajax->assign('easyblogcalendar-'.$position.'-wrapper', $layout);

    	$ajax->send();
    	return;
    }
}
