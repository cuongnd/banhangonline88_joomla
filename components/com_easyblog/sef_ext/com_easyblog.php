<?php
/**
 * sh404SEF support for com_easyblog
 * Author : StackIdeas Private Limited
 * contact : support@stackideas.com
 */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php' );

global $sh_LANG;
$config			= EasyBlogHelper::getConfig();

if( class_exists( 'shRouter' ) )
{
	$sefConfig		= shRouter::shGetConfig();
}
else
{
	$sefConfig		= Sh404sefFactory::getConfig();
}

$shLangName		= '';
$shLangIso 		= '';
$title 			= array();
$shItemidString = '';
$dosef 			= shInitializePlugin( $lang, $shLangName, $shLangIso, $option);

if ($dosef == false)
{
	return;
}

// remove common URL from GET vars list, so that they don't show up as query string in the URL
shRemoveFromGETVarsList('option');
shRemoveFromGETVarsList('lang');

// Load language file
$language = JFactory::getLanguage();
$language->load( 'com_easyblog' , JPATH_ROOT );

// start by inserting the menu element title (just an idea, this is not required at all)
$task 	= isset($task) ? @$task : null;
$Itemid	= isset($Itemid) ? @$Itemid : null;

if(!empty($view) && file_exists(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'router.php'))
{
	require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php' );
	require_once (JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'router.php');

	if(!empty($id))
	{
		JTable::addIncludePath( JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'tables' );

		switch($view)
		{
			case 'entry':
				$idname = EasyBlogRouter::getBlogSefPermalink($id);
				break;
			case 'blogger':
				$idname = EasyBlogRouter::getBloggerPermalink($id);
				break;
			case 'categories':
				$idname = EasyBlogRouter::getCategoryPermalink($id);
				break;
			case 'tags':
				$idname = EasyBlogRouter::getTagPermalink($id);
				break;
			case 'teamblog':
				$idname = EasyBlogRouter::getTeamBlogPermalink($id);
				break;
			default:
				$idname ='';
		}
	}

	if(empty($Itemid))
	{
		$Itemid = EasyBlogRouter::getItemId($view);
		shAddToGETVarsList('Itemid' , $Itemid);
	}
}

$easyblogName	= shGetComponentPrefix($option);
$easyblogName	= empty($easyblogName) ?  getMenuTitle($option, $task, $Itemid, null, $shLangName) : $easyblogName;
$easyblogName	= (empty($easyblogName) || $easyblogName == '/') ? 'EasyBlog':$easyblogName;


$title[] = $easyblogName;

$validViews = array('archive','blogger','categories','dashboard','entry','featured',
                    'images','latest','login','myblog','ratings','search','subscription',
					'tags','teamblog','trackback');

$add_idname = true;

if( isset($view) && $view == 'entry' )
{
    unset( $view );
    shRemoveFromGETVarsList( 'view' );
}

if(isset($view))
{
	if( $config->get( 'main_sef' ) != 'simple' || $view != 'entry' )
	{
	    if( in_array($view, $validViews) )
	    {
			$title[] = JText::_( 'COM_EASYBLOG_SH404_VIEW_' . JString::strtoupper( $view ) );
		}
	}

	shRemoveFromGETVarsList('view');
}

if(isset($layout))
{
	if($layout == 'statistic')
	{
		if(!empty($idname))
		{
			$title[]	= $idname;
			$add_idname = false;
			shRemoveFromGETVarsList('id');
		}

		$title[] = JText::_( 'COM_EASYBLOG_SH404_LAYOUT_' . JString::strtoupper( $layout ) );

		if(!empty($stat))
		{
			$title[]	= $stat;
			shRemoveFromGETVarsList('stat');
		}

		if(!empty($catid))
		{
			$title[]	= EasyBlogRouter::getCategoryPermalink($catid);
			shRemoveFromGETVarsList('catid');
		}

		if(!empty($tagid))
		{
			$title[]	= EasyBlogRouter::getTagPermalink($tagid);
			shRemoveFromGETVarsList('tagid');
		}
	}
	else
	{
		$translatedStr	 = JText::_( 'COM_EASYBLOG_SH404_LAYOUT_' . JString::strtoupper( $layout ) );

		if( stristr( $translatedStr , 'COM_EASYBLOG_SH404_LAYOUT_' ) === false )
		{
			$title[] = $translatedStr;
		}
	}

	shRemoveFromGETVarsList('layout');
}

// minor fix when id shouldn't get remove. @2011-06-16 - sam.
if(isset($controller))
{
	//when there is a controller here, most likely we need the id for processing.
	$add_idname = false;
}

if(!empty($id) && $add_idname)
{
	if(!empty($idname))
	{
		$title[] = $idname;
	}

	if(! ( isset( $view ) && isset( $layout ) && $view == 'dashboard' && $layout == 'category') )
		shRemoveFromGETVarsList('id');
}

if(!empty($format))
{
	$title[] = $format;
	shRemoveFromGETVarsList('format');
}

if(!empty($type))
{
	$title[] = $type;
	shRemoveFromGETVarsList('type');
}

if(!empty($query))
{
	$title[] = $query;
	shRemoveFromGETVarsList('query');
}

if(!empty($Itemid))
{
	shRemoveFromGETVarsList('Itemid');
}

if(!empty($limit))
{
	shRemoveFromGETVarsList('limit');
}

if( !empty( $archiveyear ) )
{
	$title[]	= $archiveyear;
	shRemoveFromGETVarsList( 'archiveyear' );
}

if( !empty( $archivemonth ) )
{
	$title[]	= $archivemonth;
	shRemoveFromGETVarsList( 'archivemonth' );
}

if( !empty( $archiveday ) )
{
	$title[]	= $archiveday;
	shRemoveFromGETVarsList( 'archiveday' );
}

if(isset($limitstart))
{
	shRemoveFromGETVarsList('limitstart'); // limitstart can be zero}
}

if(isset($pagestart))
{
	$pagestarttitle = 'page-' . ( $pagestart + 1 );
	$title[]	= $pagestarttitle;
	shRemoveFromGETVarsList('pagestart'); // limitstart can be zero}
}

if( isset($inclusion) )
{
	shRemoveFromGETVarsList('inclusion');
}

// ------------------  standard plugin finalize function - don't change ---------------------------
if ($dosef){
   $string = shFinalizePlugin( $string, $title, $shAppendString, $shItemidString,
      (isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null),
      (isset($shLangName) ? @$shLangName : null));
}
// ------------------  standard plugin finalize function - don't change ---------------------------
