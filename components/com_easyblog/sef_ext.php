<?php
/**
* EasyBlog component extension for SEF Advance
*
* This extension will give the SEF Advance style URLs to the EasyBlog component
* Place this file (sef_ext.php) in the main component directory
*
* Copyright (C) 2010 StackIdeas, http://www.stackideas.com, All rights reserved.
**/

require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php');
require_once (JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'router.php');

class sef_easyblog {

	/**
	* Creates the SEF Advance URL out of the request
	* Input: $string, string, The request URL (index.php?option=com_example&Itemid=$Itemid)
	* Output: $sefstring, string, SEF Advance URL ($var1/$var2/)
	**/
	function create ($string)
	{
        global $database;
        if (empty($database)) {
            // Joomla! 1.5 native
            $database = EasyBlogHelper::db();
        }
        // $string == "index.php?option=com_mydir&Itemid=$Itemid
        //            &catid=$catid&id=$id"
        $sefstring = '';

// 		$itemid = '';
// 		if (preg_match('/&amp;Itemid=/i',$string))
// 		{
// 			$temp 	= explode('&amp;Itemid=', $string);
// 			$temp 	= explode('&', $temp[1]);
// 			$itemid = $temp[0];
// 		}

		$config = EasyBlogHelper::getConfig();

		if (preg_match('/&amp;view=/i',$string))
		{
			$temp 	= explode('&amp;view=', $string);
            $temp 	= explode('&', $temp[1]);
            $view 	= $temp[0];

			if( $view != 'entry' || $config->get( 'main_sef' ) != 'simple' )
			{
				$sefstring .= sefencode($view).'/';
			}
        }

        if (preg_match('/&amp;id=/i',$string))
		{
            $id = sef_easyblog::getVarValue('id', $string);

			JTable::addIncludePath( JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'tables' );

			$idname = '';

			if(!empty($id) && isset($view))
			{
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
						$idname = '';
				}
			}

            $idname = empty($idname)? $id : $idname;
        }

        if (preg_match('/&amp;controller=/i',$string))
		{
            $temp		= explode('&amp;controller=', $string);
            $temp		= explode('&', $temp[1]);
            $controller	= $temp[0];

            $sefstring .= '?controller='.$controller;

            if (preg_match('/&amp;task=/i',$string))
			{
	            $temp 	= explode('&amp;task=', $string);
	            $temp 	= explode('&', $temp[1]);
	            $task	= $temp[0];

	            $sefstring .= '&task='.$task;
	        }

            if (preg_match('/&amp;tmpl=/i',$string))
			{
	            $temp 	= explode('&amp;tmpl=', $string);
	            $temp 	= explode('&', $temp[1]);
	            $tmpl	= $temp[0];

	            $sefstring .= '&tmpl='.$tmpl;
	        }

            if (preg_match('/&amp;browse=/i',$string))
			{
	            $temp 	= explode('&amp;browse=', $string);
	            $temp 	= explode('&', $temp[1]);
	            $browse	= $temp[0];

	            $sefstring .= '&browse='.$browse;
	        }

            if (preg_match('/&amp;from=/i',$string))
			{
	            $temp 	= explode('&amp;from=', $string);
	            $temp 	= explode('&', $temp[1]);
	            $from = $temp[0];

	            $sefstring .= '&from='.$from;
	        }

			if (preg_match('/&amp;status=/i',$string))
			{
	            $temp 	= explode('&amp;status=', $string);
	            $temp 	= explode('&', $temp[1]);
	            $from = $temp[0];

	            $sefstring .= '&status='.$from;
	        }

			if (preg_match('/&amp;blogId=/i',$string))
			{
	            $temp 	= explode('&amp;blogId=', $string);
	            $temp 	= explode('&', $temp[1]);
	            $from = $temp[0];

	            $sefstring .= '&blogId='.$from;
	        }
        }

        $add_idname = true;
        if (preg_match('/&amp;layout=/i',$string))
		{
            $temp 	= explode('&amp;layout=', $string);
            $temp 	= explode('&', $temp[1]);
            $layout = $temp[0];

            if($layout == 'statistic')
            {
				if(!empty($idname))
				{
					$sefstring .= $idname.'/';
					$add_idname = false;
				}

				$sefstring .= sefencode($layout).'/';

				if((preg_match('/&amp;stat=/i',$string)))
				{
					$stat 		= sef_easyblog::getVarValue('stat', $string);
					$sefstring	.= sefencode($stat).'/';
				}

				if((preg_match('/&amp;catid=/i',$string)))
				{
					$catid 		= sef_easyblog::getVarValue('catid', $string);
					$sefstring	.= EasyBlogRouter::getCategoryPermalink($catid).'/';
				}

				if((preg_match('/&amp;tagid=/i',$string)))
				{
					$tagid 		= sef_easyblog::getVarValue('tagid', $string);
					$sefstring	.= EasyBlogRouter::getTagPermalink($tagid).'/';
				}
			}
			else
			{
				$sefstring .= sefencode($layout).'/';
			}
        }

        if (preg_match('/&amp;id=/i',$string) && $add_idname) {
            $sefstring .= $idname.'/';
        }

    	if (preg_match('/&amp;blogid=/',$string))
		{
            $temp 	= explode('&amp;blogid=', $string);
			$temp 	= explode('&', $temp[1]);
            $blogid = $temp[0];

            $sefstring .= 'blogid-'.$blogid.'/';
        }

       	if (preg_match('/&amp;format=/i',$string))
		{
            $temp 	= explode('&amp;format=', $string);

            if($temp[1] == 'rss')
            {
				$temp = explode('&', $temp[2]);
			}
            else
            {
				$temp = explode('&', $temp[1]);
			}

            $format = $temp[0];

            $sefstring .= sefencode($format).'/';
        }

        if (preg_match('/&amp;type=/i',$string))
		{
            $temp 	= explode('&amp;type=', $string);
            $temp 	= explode('&', $temp[1]);
            $type = $temp[0];

            $sefstring .= sefencode($type).'/';
        }

        if (preg_match('/&amp;limitstart=/i',$string)) {
			if (preg_match('/&amp;limit=/i',$string)) {
				$temp = explode('&amp;limit=', $string);
				$temp = explode('&', $temp[1]);
				$sefstring .= '/limit-'.$temp[0];
			}
			// category pagination
			$temp = explode('&amp;limitstart=', $string);
			$temp = explode('&', $temp[1]);

			if ($temp[0]!=0 || preg_match('/&amp;limit=/i',$string)) {
				$sefstring .= '/limitstart-'.$temp[0];
			}
		}

        return $sefstring;
    }

	/**
	* Reverts to the query string out of the SEF Advance URL
	* Input:
	*    $url_array, array, The SEF Advance URL split in arrays
	*    $pos, int, The position offset for virtual directories (first virtual directory, which is the component name, begins at $pos+1)
	* Output: $QUERY_STRING, string, query string (var1=$var1&var2=$var2)
	*    Note that this will be added to already defined first part (option=com_example&Itemid=$Itemid)
	**/
	function revert ($url_array, $pos)
	{
		require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php' );

		$config		= EasyBlogHelper::getConfig();
		$seftype	= $config->get('main_sef', 'default');

        global $database;
        if (empty($database)) {
            // Joomla! 1.5 native
            $database = EasyBlogHelper::db();
        }
        $QUERY_STRING = '';

        $view = '';

        $totalSegment = count(array_filter($url_array)) - $pos;

        // If user chooses to use the simple sef setup, we need to add the proper view
		if( $config->get( 'main_sef' ) == 'simple' && $totalSegment == 2 )
		{
			$views	= JFolder::folders( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'views' );

			if( !in_array( $url_array[$pos+2] , $views ) )
			{
				array_splice($url_array, $pos+2, 0, 'entry');
			}
		}

		// We need to remove limitstart from the result
		foreach($url_array as $index => $field )
		{
			if (preg_match('/limitstart-/i', $field))
			{
	            $temp 	= explode('limitstart-', $field);
	            $temp 	= explode('&', $temp[1]);
	            $limitstart = $temp[0];
	            $_GET['limitstart'] = $_REQUEST['limitstart'] = $limitstart;
	            $QUERY_STRING .= '&limitstart='.$limitstart;

	            unset( $url_array[ $index ] );
	        }
		}

        if (isset($url_array[$pos+2]) && $url_array[$pos+2]!='')
		{
            // .../mydir/$category/
            $view = sefdecode($url_array[$pos+2]);
            $_GET['view'] = $_REQUEST['view'] = $view;
            $QUERY_STRING .= "&view=$view";

            if(!empty($view) && file_exists(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'router.php'))
			{
				require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php');
				require_once (JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'router.php');
				$Itemid = EasyBlogRouter::getItemId($view);
			}
			else
			{
				$Itemid = '';
			}
        }

		switch($view)
		{
			case 'entry':
				if ($seftype == 'date') {
				    $sector = 6;
				} elseif ($seftype == 'category') {
					$sector = 4;
				} elseif ($seftype == 'datecategory') {
				    $sector = 7;
				} else {
					$sector = 3;
				}

				if(!empty($url_array[$pos+$sector])){

					$entryId    = '';
					if( $config->get( 'main_sef_unicode' ) )
					{
					    // perform manual split on the string.
					    $permalinkSegment   = $url_array[$pos+$sector];
					    $permalinkArr    	= explode( ':', $permalinkSegment);
					    $id		            = $permalinkArr[0];
					}
					else
					{
						$id = $this->revertPermalink($url_array[$pos+$sector], 'blog');
					}

					$_GET['id'] = $_REQUEST['id'] = $id;
					$QUERY_STRING .= "&id=".$id;
				}

				break;
			case 'blogger':
				if(!empty($url_array[$pos+4]) && $url_array[$pos+4] == 'statistic')
				{
					$_GET['layout'] = $_REQUEST['layout'] = 'statistic';
					$QUERY_STRING .= "&layout=statistic";

					if(!empty($url_array[$pos+3])){

						if( $config->get( 'main_sef_unicode' ) )
						{
						    // perform manual split on the string.
						    $permalinkSegment   = $url_array[$pos+3];
						    $permalinkArr    	= explode( ':', $permalinkSegment);
						    $id		            = $permalinkArr[0];
						}
						else
						{
							$id = $this->revertPermalink($url_array[$pos+3], 'blogger');
						}

						$_GET['id'] = $_REQUEST['id'] = $id;
						$QUERY_STRING .= "&id=".$id;
					}

					if(!empty($url_array[$pos+5])){
						$stat = sefdecode($url_array[$pos+5]);
						$_GET['stat'] = $_REQUEST['stat'] = $stat;
						$QUERY_STRING .= "&stat=".$stat;
					}

					if(!empty($url_array[$pos+6]) && !empty($stat)){
						switch($stat)
						{
							case 'category':
								if( $config->get( 'main_sef_unicode' ) )
								{
								    // perform manual split on the string.
								    $permalinkSegment   = $url_array[$pos+6];
								    $permalinkArr    	= explode( ':', $permalinkSegment);
								    $catid	            = $permalinkArr[0];
								}
								else
								{
									$catid = $this->revertPermalink($url_array[$pos+6], 'category');
								}

								$_GET['catid'] = $_REQUEST['catid'] = $catid;
								$QUERY_STRING .= "&catid=".$catid;
								break;
							case 'tag':
								if( $config->get( 'main_sef_unicode' ) )
								{
								    // perform manual split on the string.
								    $permalinkSegment   = $url_array[$pos+6];
								    $permalinkArr    	= explode( ':', $permalinkSegment);
								    $tagid	            = $permalinkArr[0];
								}
								else
								{
									$tagid = $this->revertPermalink($url_array[$pos+6], 'tag');
								}

								$_GET['tagid'] = $_REQUEST['tagid'] = $tagid;
								$QUERY_STRING .= "&tagid=".$tagid;
								break;
							default:
								// Do nothing .
						}
					}
				}
				else
				{
					if(!empty($url_array[$pos+3])){
						if($url_array[$pos+3]=='listings'){
							$_GET['layout'] = $_REQUEST['layout'] = 'listings';
							$QUERY_STRING .= "&layout=listings";

							if(!empty($url_array[$pos+4])){
								if( $config->get( 'main_sef_unicode' ) )
								{
								    // perform manual split on the string.
								    $permalinkSegment   = $url_array[$pos+4];
								    $permalinkArr    	= explode( ':', $permalinkSegment);
								    $id		            = $permalinkArr[0];
								}
								else
								{
									$id = $this->revertPermalink($url_array[$pos+4], 'blogger');
								}

								$_GET['id'] = $_REQUEST['id'] = $id;
								$QUERY_STRING .= "&id=".$id;
							}
						}
						else if(!empty($url_array[$pos+4]))
						{
							if($url_array[$pos+4]=='feed')
							{
								if( $config->get( 'main_sef_unicode' ) )
								{
								    // perform manual split on the string.
								    $permalinkSegment   = $url_array[$pos+3];
								    $permalinkArr    	= explode( ':', $permalinkSegment);
								    $id		            = $permalinkArr[0];
								}
								else
								{
									$id = $this->revertPermalink($url_array[$pos+3], 'blogger');
								}

								$_GET['id'] = $_REQUEST['id'] = $id;
								$QUERY_STRING .= "&id=".$id;

								$format = $url_array[$pos+4];
								$_GET['format'] = $_REQUEST['format'] = $format;
								$QUERY_STRING .= "&format=".$format;

								$type = $url_array[$pos+5];
								$_GET['type'] = $_REQUEST['type'] = $type;
								$QUERY_STRING .= "&type=".$type;
							}
						}
					}
				}
				break;
			case 'categories':
				if(!empty($url_array[$pos+3])){
					if($url_array[$pos+3]=='listings')
					{
						$_GET['layout'] = $_REQUEST['layout'] = 'listings';
						$QUERY_STRING .= "&layout=listings";

						if(!empty($url_array[$pos+4]))
						{
							if( $config->get( 'main_sef_unicode' ) )
							{
							    // perform manual split on the string.
							    $permalinkSegment   = $url_array[$pos+4];
							    $permalinkArr    	= explode( ':', $permalinkSegment);
							    $id		            = $permalinkArr[0];
							}
							else
							{
								$id = $this->revertPermalink($url_array[$pos+4], 'category');
							}

							$_GET['id'] = $_REQUEST['id'] = $id;
							$QUERY_STRING .= "&id=".$id;
						}
					}
					else if($url_array[$pos+4]=='feed')
					{
						if( $config->get( 'main_sef_unicode' ) )
						{
						    // perform manual split on the string.
						    $permalinkSegment   = $url_array[$pos+3];
						    $permalinkArr    	= explode( ':', $permalinkSegment);
						    $id		            = $permalinkArr[0];
						}
						else
						{
							$id = $this->revertPermalink($url_array[$pos+3], 'category');
						}

						$_GET['id'] = $_REQUEST['id'] = $id;
						$QUERY_STRING .= "&id=".$id;

						$format = $url_array[$pos+4];
						$_GET['format'] = $_REQUEST['format'] = $format;
						$QUERY_STRING .= "&format=".$format;

						$type = $url_array[$pos+5];
						$_GET['type'] = $_REQUEST['type'] = $type;
						$QUERY_STRING .= "&type=".$type;
					}
				}
				break;
			case 'tags':
				if(!empty($url_array[$pos+3])){
					if($url_array[$pos+3]=='tag'){
						$_GET['layout'] = $_REQUEST['layout'] = 'tag';
						$QUERY_STRING .= "&layout=tag";
					}
					if(!empty($url_array[$pos+4])){
						if( $config->get( 'main_sef_unicode' ) )
						{
						    // perform manual split on the string.
						    $permalinkSegment   = $url_array[$pos+4];
						    $permalinkArr    	= explode( ':', $permalinkSegment);
						    $id		            = $permalinkArr[0];
						}
						else
						{
							$id = $this->revertPermalink($url_array[$pos+4], 'tag');
						}

						$_GET['id'] = $_REQUEST['id'] = $id;
						$QUERY_STRING .= "&id=".$id;
					}
				}
				break;
			case 'teamblog':
				if(!empty($url_array[$pos+4]) && $url_array[$pos+4] == 'statistic')
				{
					$_GET['layout'] = $_REQUEST['layout'] = 'statistic';
					$QUERY_STRING .= "&layout=statistic";

					if(!empty($url_array[$pos+3])){

						if(!empty($url_array[$pos+3])){
							if( $config->get( 'main_sef_unicode' ) )
							{
							    // perform manual split on the string.
							    $permalinkSegment   = $url_array[$pos+3];
							    $permalinkArr    	= explode( ':', $permalinkSegment);
							    $id		            = $permalinkArr[0];
							}
							else
							{
								$id = $this->revertPermalink($url_array[$pos+3], 'teamblog');
							}

							$_GET['id'] = $_REQUEST['id'] = $id;
							$QUERY_STRING .= "&id=".$id;
						}

						if(!empty($url_array[$pos+5])){
							$stat = sefdecode($url_array[$pos+5]);
							$_GET['stat'] = $_REQUEST['stat'] = $stat;
							$QUERY_STRING .= "&stat=".$stat;
						}

						if(!empty($url_array[$pos+6]) && !empty($stat)){
							switch($stat)
							{
								case 'category':
									if( $config->get( 'main_sef_unicode' ) )
									{
									    // perform manual split on the string.
									    $permalinkSegment   = $url_array[$pos+6];
									    $permalinkArr    	= explode( ':', $permalinkSegment);
									    $catid	            = $permalinkArr[0];
									}
									else
									{
										$catid = $this->revertPermalink($url_array[$pos+6], 'category');
									}

									$_GET['catid'] = $_REQUEST['catid'] = $catid;
									$QUERY_STRING .= "&catid=".$catid;
									break;
								case 'tag':
									if( $config->get( 'main_sef_unicode' ) )
									{
									    // perform manual split on the string.
									    $permalinkSegment   = $url_array[$pos+6];
									    $permalinkArr    	= explode( ':', $permalinkSegment);
									    $tagid	            = $permalinkArr[0];
									}
									else
									{
										$tagid = $this->revertPermalink($url_array[$pos+6], 'tag');
									}

									$_GET['tagid'] = $_REQUEST['tagid'] = $tagid;
									$QUERY_STRING .= "&tagid=".$tagid;
									break;
								default:
									// Do nothing .
							}
						}
					}
				}
				else
				{
					if(!empty($url_array[$pos+3])){
						if($url_array[$pos+3]=='listings'){
							$_GET['layout'] = $_REQUEST['layout'] = 'listings';
							$QUERY_STRING .= "&layout=listings";
						}
						if(!empty($url_array[$pos+4])){
							if( $config->get( 'main_sef_unicode' ) )
							{
							    // perform manual split on the string.
							    $permalinkSegment   = $url_array[$pos+4];
							    $permalinkArr    	= explode( ':', $permalinkSegment);
							    $id		            = $permalinkArr[0];
							}
							else
							{
								$id = $this->revertPermalink($url_array[$pos+4], 'teamblog');
							}

							$_GET['id'] = $_REQUEST['id'] = $id;
							$QUERY_STRING .= "&id=".$id;
						}
					}
				}
				break;
			case 'dashboard':
				if(!empty($url_array[$pos+3])){
					$_GET['layout'] = $_REQUEST['layout'] = $url_array[$pos+3];
					$QUERY_STRING .= "&layout=".$url_array[$pos+3];

					if($url_array[$pos+3]=='write'){
						if(!empty($url_array[$pos+4])){
							if (preg_match('/blogid-/i', $url_array[$pos+4]))
							{
					            $temp 	= explode('blogid-', $url_array[$pos+4]);
					            $temp 	= explode('&', $temp[1]);
					            $blogid = $temp[0];
					            $_GET['blogid'] = $_REQUEST['blogid'] = $blogid;
					            $QUERY_STRING .= '&blogid='.$blogid;
					        }
						}
					}
				}
				break;
			default:
				$idname = '';
		}

		if(!empty($Itemid))
		{
			$_GET['Itemid'] = $_REQUEST['Itemid'] = $Itemid;
			$QUERY_STRING .= '&Itemid='.$Itemid;
		}

		foreach($url_array as $field)
		{
			if (preg_match('/limitstart-/i', $field))
			{
	            $temp 	= explode('limitstart-', $field);
	            $temp 	= explode('&', $temp[1]);
	            $limitstart = $temp[0];
	            $_GET['limitstart'] = $_REQUEST['limitstart'] = $limitstart;
	            $QUERY_STRING .= '&limitstart='.$limitstart;
	        }
		}

		//echo $QUERY_STRING.'<br/><br/>';

        return $QUERY_STRING;
    }

    function revertPermalink($permalink, $type)
    {
    	global $database;

		if (empty($database))
		{
			// Joomla! 1.5 native
			$database = EasyBlogHelper::db();
		}

    	static $permalinks	= null;

    	if( !isset( $permalinks[ $permalink ] ) )
    	{
			switch($type)
			{
				case 'blog':
					$sql = 'SELECT '.$database->nameQuote('id').' FROM '.$database->nameQuote('#__easyblog_post').' WHERE '.$database->nameQuote('permalink').' = '.$database->quote($permalink);
					break;
				case 'blogger':
					$sql = 'SELECT '.$database->nameQuote('id').' FROM '.$database->nameQuote('#__users').' WHERE username = '.$database->quote( $permalink );
					break;
				case 'category':
					$sql = 'SELECT '.$database->nameQuote('id').' FROM '.$database->nameQuote('#__easyblog_category').' WHERE '.$database->nameQuote('alias').' = '.$database->quote($permalink);
					break;
				case 'tag':
					$sql = 'SELECT '.$database->nameQuote('id').' FROM '.$database->nameQuote('#__easyblog_tag').' WHERE '.$database->nameQuote('alias').' = '.$database->quote($permalink);
					break;
				case 'teamblog':
					$sql = 'SELECT '.$database->nameQuote('id').' FROM '.$database->nameQuote('#__easyblog_team').' WHERE '.$database->nameQuote('alias').' = '.$database->quote($permalink);
					break;
			}
			$database->setQuery($sql);
			$permalinks[ $permalink ]	= $database->loadResult();
		}
		return $permalinks[ $permalink ];
	}

	function getVarValue($var, $string)
	{
		$temp 	= explode('&amp;'.$var.'=', $string);
        $temp 	= explode('&', $temp[1]);
        return $temp[0];
	}
}
