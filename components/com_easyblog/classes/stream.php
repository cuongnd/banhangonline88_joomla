<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR .'helper.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR .'date.php' );

class EasyBlogStream
{
	var $aggContextList 	= null;
	var $currentDateRange   = null;

	public function __construct()
	{

		$this->aggContextList = array('tag-add'				=> 'add',
	                            	  'category-add'		=> 'add',
									  'blogger-subscribe'	=> 'subscribe',
									  'post-subscribe'    	=> 'subscribe');

	}


	private function _insertStream( $data )
	{
		if( isset( $data->actor_id ) &&
			isset( $data->context_type ) &&
			isset( $data->context_id ) &&
			isset( $data->verb ) )
		{
		    $date 	= EasyBlogHelper::getDate();
			$tbl	= EasyBlogHelper::getTable( 'Stream' , 'Table' );

			$tbl->actor_id		= $data->actor_id;
			$tbl->target_id		= ( isset( $data->target_id ) ) ? $data->target_id : '0' ;
			$tbl->context_type	= $data->context_type;
			$tbl->context_id	= $data->context_id;
			$tbl->verb			= $data->verb;
			$tbl->source_id		= ( isset( $data->source_id ) ) ? $data->source_id : '0' ;
			$tbl->created		= $date->toMySQL();
			$tbl->uuid			= ( isset( $data->uuid ) ) ? $data->uuid : '' ;

			$tbl->store();

			return $tbl->id;
		}

		return false;
	}

	private function _buildQuery( $userId, $limit = 0, $startdate = '', $isCnt = false )
	{
		$db 	= EasyBlogHelper::db();

		$dates	= $this->_getDateRange($userId, $limit, $startdate);
		$this->currentDateRange = $dates;

		//$limitQuery = '';
		$limitQuery = ' and (a.`created` >= ' . $db->Quote( $dates['startdate'] ) . ' and a.`created` <= ' . $db->Quote( $dates['enddate'] ) . ')';

		$query  = '';

		if( $isCnt )
		{
			$query  = ' select COUNT(1) FROM (';
		}
		else
		{
			$query  = ' select * FROM (';
		}

		$operation  = '( UNIX_TIMESTAMP( \'' . EasyBlogHelper::getDate()->toMySQL() . '\' ) - UNIX_TIMESTAMP( a.`created`) )';

	    $query  .= ' (select ';

		if( $isCnt )
		{
			$query  .= ' a.id';
		}
		else
		{
			$query  .= ' FLOOR( ' . $operation. ' ) AS minsecdiff,';
		    $query  .= ' FLOOR( ' . $operation. ' / 60 / 60) AS hourdiff,';
			$query  .= ' FLOOR( ' . $operation. ' / 60 / 60 / 24) AS daydiff,';
			$query  .= ' a.`id`, a.`actor_id`, a.`target_id`, concat( a.`context_type`, ' . $db->Quote('-') . ', a.`verb`) as `context_type`,';
			$query  .= ' a.`context_type` as `plain_context_type`, a.`context_id`, a.`verb`, a.`source_id`, a.`created`, a.`uuid`';
		}

	    $query  .= ' from `#__easyblog_stream` as a';
		$query  .= ' 	INNER JOIN `#__users` as b ON b.id = a.actor_id';
		$query  .= ' where a.actor_id = ' . $db->Quote($userId);
		$query	.= $limitQuery;
		$query  .= ' )';

		$query  .= ' UNION ';

	    $query  .= ' (select ';

		if( $isCnt )
		{
			$query  .= ' a.id';
		}
		else
		{
			$query  .= ' FLOOR( ' . $operation. ' ) AS minsecdiff,';
		    $query  .= ' FLOOR( ' . $operation. ' / 60 / 60) AS hourdiff,';
			$query  .= ' FLOOR( ' . $operation. ' / 60 / 60 / 24) AS daydiff,';
			$query  .= ' a.`id`, a.`actor_id`, a.`target_id`, concat( a.`context_type`, ' . $db->Quote('-') . ', a.`verb`) as `context_type`,';
			$query  .= ' a.`context_type` as `plain_context_type`, a.`context_id`, a.`verb`, a.`source_id`, a.`created`, a.`uuid`';
		}

	    $query  .= ' from `#__easyblog_stream` as a';
		$query  .= ' 	INNER JOIN `#__users` as b ON b.id = a.target_id';
		$query  .= ' where a.target_id = ' . $db->Quote($userId);
		$query  .= ' and a.actor_id != ' . $db->Quote($userId);
		$query	.= $limitQuery;
		$query  .= ' )';
		$query  .= ' ) as x';

		if(! $isCnt )
	    	$query  .= ' order by x.created desc';

		return $query;
	}

	public function getDateData()
	{
		return $this->currentDateRange;
	}

	public function _getDateRange($userId, $limit = 0, $startdate = '')
	{
		$db = EasyBlogHelper::db();

		$arrDate    	= array(
							'startdate'	=> '',
							'enddate'	=> '');

		$query  = 'select MAX(`created`) as `recent_date` FROM `#__easyblog_stream` as a';
		$query  .= ' where (a.target_id = ' . $db->Quote($userId);
		$query  .= ' or a.actor_id = ' . $db->Quote($userId) . ')';
		if( !empty($startdate) )
		{
			$query	.= ' and a.`created` < ' . $db->Quote( $startdate );
		}

		$db->setQuery( $query );
		$recentDate = $db->loadResult();

		if( !empty($recentDate) )
		{
			$tmp    = explode(' ', $recentDate);
			$recentDate = $tmp[0] . ' 00:00:01';

			$today  	= $db->Quote( $recentDate );
			$previous 	= 'date_add(' . $today . ', interval - ' . ( $limit - 1 ) . ' day)';

			$query  = 'select ' . $today . ' as `today`, ' . $previous . ' as `previous`';
			$db->setQuery( $query );
			$result = $db->loadObject();

			// formating the the time
			unset( $tmp );
			$tmp	= explode(' ' , $result->previous);
			$sdate   = $tmp[0] . ' 00:00:01';

			unset( $tmp );
			$tmp	= explode(' ' , $result->today);
			$edate  = $tmp[0] . ' 23:59:59';

			$arrDate['startdate'] 	= $sdate;
			$arrDate['enddate'] 	= $edate;
		}

		return $arrDate;
	}

	private function _getStream( $userId, $limit = 0, $startlimit = 0 )
	{
		$db = EasyBlogHelper::db();

		$query = $this->_buildQuery($userId, $limit, $startlimit);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	private function _aggregateStream( $userId, $streamData )
	{
	    $aggContextList = $this->aggContextList;

	    $story  			= array();
	    $translatedStory    = array();
	    $timeFrame  		= 15; //min

	    if( count($streamData) > 0)
	    {
	        foreach( $streamData as $stream)
	        {

		        $stream->daydiff	= ( $stream->daydiff < 0) ? 0 : $stream->daydiff;
		        $stream->hourdiff	= ( $stream->hourdiff < 0) ? 0 : $stream->hourdiff;
		        $stream->minsecdiff	= ( $stream->minsecdiff < 0) ? 0 : $stream->minsecdiff;

                $day    = $stream->daydiff;
                $hour   = $stream->hourdiff;
                $min    = 0;

                $useDay    	= $stream->daydiff;
                $useHour    = $stream->hourdiff;
                $useMin    	= $stream->minsecdiff;


				$useActor	= ( $userId == $stream->target_id ) ? $stream->target_id : $stream->actor_id;
                $useActor   = $useActor . '-' . $stream->context_type;

				$actorId    = ( $userId == $stream->target_id ) ? $stream->target_id : $stream->actor_id;


	            if( (array_key_exists($stream->context_type, $aggContextList) === true) && ($aggContextList[ $stream->context_type ] == $stream->verb) )
	            {

					if( isset( $story[ $day ] ) )
					{
					    // same day

	                    if( isset( $story[ $day ][ $stream->hourdiff ] ) )
	                    {
	                        //same hour
	                        $lastEventHour  = max( array_keys( $story[ $day ] ) );
	                        $lastEventTime  = max( array_keys( $story[ $day ][ $stream->hourdiff ] ) );
	                        $lastEvent    	= array_pop( $story[ $day ][ $stream->hourdiff ] );
	                        $contextKey 	= max(array_keys($lastEvent));
							//$contextKey 		= array_pop(array_keys($lastEvent));

							if( (array_key_exists($contextKey, $aggContextList) === true) )
							{
								$time		= $this->_processAggregation($userId, $stream, $lastEventHour, $lastEventTime, $lastEvent, $contextKey, $story, false);
								$useHour 	= $time[0];
								$useMin   	= $time[1];

								$story      = $time[2];
							}
							else
							{
								$story[ $day ][ $lastEventHour ][$lastEventTime] = $lastEvent;
							}

	                        unset($lastEvent);
	                    }
	                    else
	                    {
	                        // diff hours
	                        $lastEventHour  	= max( array_keys( $story[ $day ] ) );
							$tmpPrevHourEvent   = $story[ $day ][$lastEventHour];
	                        $lastEventTime  	= max( array_keys( $tmpPrevHourEvent ) );
	                        $prevHourEvent    	= array_pop( $story[ $day ][$lastEventHour]);
	                        $contextKey 		= max(array_keys($prevHourEvent));
							//$contextKey 		= array_pop(array_keys($prevHourEvent));

							// if( (array_key_exists($contextKey, $aggContextList) === true) && ($aggContextList[ $contextKey ] == $prevEventContextVerbKey) )
							if( (array_key_exists($contextKey, $aggContextList) === true) )
							{
	                            $time		= $this->_processAggregation($userId, $stream, $lastEventHour, $lastEventTime, $prevHourEvent, $contextKey, $story, true);
								$useHour 	= $time[0];
								$useMin   	= $time[1];

								$story      = $time[2];
							}
							else
							{
								$story[ $day ][$lastEventHour][$lastEventTime] = $prevHourEvent;
							}

                            unset($prevHourEvent);
                        }
					}

				}

				// if different day, we add in directly.
                $sourceId   = ( empty($stream->source_id) ) ? $stream->context_id : $stream->context_id . '-' . $stream->source_id;
                $story[ $day ][ $useHour ][$useMin][$stream->context_type][ $stream->verb ][ $useActor ]['data'][] = array( $actorId => $sourceId, 'stream_object' => $stream );

	        }

			// echo '<pre>'; print_r( $story ); echo '</pre>';exit;

			$translatedStory    = $this->_translateStory( $userId, $story );
	    }

		return $translatedStory;

	}

	private function _processAggregation( $userId, $stream, $lastEventHour, $lastEventTime, $lastEvent, $contextKey, $story, $isHourDiff )
	{
		$aggContextList = $this->aggContextList;
		$day    = $stream->daydiff;
		$hour   = $stream->hourdiff;
		$min    = 0;

		$useDay    = $stream->daydiff;
		$useHour   = $stream->hourdiff;
		$useMin    = $stream->minsecdiff;

		// $useActor   = $stream->actor_id . '-' . $stream->context_type;

		$useActor	= ( $userId == $stream->target_id ) ? $stream->target_id : $stream->actor_id;
        $useActor   = $useActor . '-' . $stream->context_type;

		$actorId    = ( $userId == $stream->target_id ) ? $stream->target_id : $stream->actor_id;



		$hourIndex    = ($isHourDiff) ? $lastEventHour : $stream->hourdiff;

		 if( ( array_key_exists($contextKey, $aggContextList) === true ) )
		 {
		    if( isset( $lastEvent[$stream->context_type][$stream->verb] ) ) // same event here.
		    {

				//update the last event time to current one.
				if( isset( $lastEvent[$stream->context_type][ $stream->verb ][ $useActor ] ) )
				{

					$lastEventData  = $lastEvent[$stream->context_type][ $stream->verb ];
					//now put back the last event.
					foreach( $lastEventData as $key => $val)
					{
						foreach($val as $keyItem => $valItem)
						{
							foreach( $valItem as $item)
							{
								$story[ $day ][ $hourIndex ][$lastEventTime][$stream->context_type][ $stream->verb ][ $key ]['data'][] = $item;
							}
						}
					}

				   	// update the minutes to use the lowest value (lowest == more recent)
				   	$useHour	= $hourIndex;
				   	$useMin		= $lastEventTime;

				}
				else
				{
				 	$story[ $day ][ $hourIndex ][$lastEventTime]  = $lastEvent;

				}

				unset($lastEventData);
			}
		    else
		    {
			  	$story[ $day ][ $hourIndex ][$lastEventTime]  = $lastEvent;
		    }

		}
		else
		{
			$story[ $day ][ $hourIndex ][$lastEventTime]  = $lastEvent;
		}//contextkey

		$result = array($useHour, $useMin, $story);

		return $result;

	}

	private function _translateStory( $userId, $story )
	{
		$config 		= EasyBlogHelper::getConfig();
	    $aggContextList = $this->aggContextList;
	    $dayKeys    	= array_keys( $story );

		// echo '<pre>';print_r($story);echo '</pre>';exit;


		$myStory    = array();

		for( $i = 0; $i < count( $dayKeys ) ; $i++)
		{
		    $day    	= $dayKeys[ $i ];
		    $events  	= $story[ $day ];

		    $eventString    = '';

		    $storyString    = '';
		    $dayString    	= '';

		    $hourKeys   = array_keys( $events );

		    foreach( $hourKeys as $hour)
		    {
		        $event  = $events[ $hour ];

		        $minKeys   = array_keys( $event );

		        //echo '<pre>';print_r($minKeys);echo '</pre>';exit;

		        foreach($minKeys as $min)
		        {
		            $activity   = $event[ $min ];
		            $contextKey = array_keys( $activity );

					//echo '<pre>';print_r($activity);echo '</pre>';exit;

					// since now the min  is second base. we need to convert it into minute.
					$min    = $min / 60;
					$min	= floor($min);

		            foreach( $contextKey as $context )
		            {
		                $activityVerbs	= $activity[ $context ];
		                $verbs			= array_keys( $activityVerbs );

						foreach( $verbs as $verb )
						{

			           		$verbActors 	= array_keys($activityVerbs[ $verb ]);

							// echo '<pre>';print_r($verbActors);echo '</pre>';exit;

						    foreach($verbActors as $va)
							{
				                $itemData       = $activityVerbs[ $verb ][ $va ][ 'data' ];

				                $actor          = array();
				                $target         = array();

				                //foreach( $itemData as $item )
				                for( $a = 0; $a < count($itemData); $a++)
				                {
				                    $item   = $itemData[$a];
				                    $key    = array_keys( $item );

									//echo '<pre>';print_r($item[ 'stream_object' ]);echo '</pre>';exit;

									if( $item[ 'stream_object' ]->target_id == $userId )
									{
										$actor[]    = $userId;
									}
									else
									{
				                    	$actor[]   	= $key[0];
									}

				                    $target[]   	= array( $item[ $key[0] ], $item[ 'stream_object' ]);

								}

								//remove duplicate actorId
								$actor	= array_unique( $actor );

				                // get actor names.
				                $actorString    = '';
								$cntActor       = count($actor);
								$j              = 0;

								foreach( $actor as $actorId)
				                {
				                    // check if the target is user. if yes, get the name
									// TODO : check on the source_type
									//      : for now treat it as user

									$user   	= JFactory::getUser( $actorId );
									$profile	= EasyBlogHelper::getTable( 'Profile', 'Table' );
									$profile->load( $actorId );


									if( empty( $actorString ) )
									{
									    $actorString  = $profile->getName();
									}
									else
									{
									    if( ($j + 1) == $cntActor)
									    {
									        $actorString  = $actorString . JText::_('COM_EASYBLOG_STREAM_AND') . $profile->getName();
									    }
									    else
									    {
									        $actorString  = $actorString . ', ' . $profile->getName();
									    }
									}

									$j++;
								}//end $actor

								$timeformat = $config->get( 'layout_streamtimeformat', '%I:%M %p');

								$date = EasyBlogDateHelper::getDate( '-' . $min . ' mins' );

								// today
								if( $day == 0)
								{
									if( $min > 60 )
									{
										$dayString  = $hour . JText::_( 'COM_EASYBLOG_STREAM_X_HOURS_AGO');
									} else if( $min <= 0)
									{
									    $dayString  = JText::_( 'COM_EASYBLOG_STREAM_LESS_THAN_ONE_MIN_AGO' );
									}
									else
									{
									    $dayString  = $min . JText::_( 'COM_EASYBLOG_STREAM_X_MINS_AGO');
									}
								}
								elseif ( $day == 1 )
								{
									$time		= EasyBlogDateHelper::getDate('-' . $min . ' mins');

									$dayString  = JText::_( 'COM_EASYBLOG_STREAM_YESTERDAY_AT') . $time->toFormat($timeformat);
								}
								elseif( $day > 1 && $day <= 7)
								{
									$dayString		= $date->toFormat( '%A ' ) . ' ' . JText::_( 'COM_EASYBLOG_AT' ) . ' ' . $date->toFormat( $timeformat );
								}
								else
								{
									$dayString		= $date->toFormat( '%b %d' ) . ' ' . JText::_( 'COM_EASYBLOG_AT' ) . ' ' . $date->toFormat( $timeformat );
								}


								$itemObj    			= new stdClass();
								$itemObj->actor  		= $actorString;
								$itemObj->streamFormat  = 'full';
								$itemObj->contextType  	= $context;
								$itemObj->verb  		= $verb;
								$itemObj->target  		= $target;
								$itemObj->friendlyTS	= $dayString;

								$itemObj->story	= $actorString . ' ' . $verb . 'ed ' . count($target) . ' ' . $context . ' @ ' . $dayString;

								$streamHTML = $this->_htmlContent($userId, $context, count($target), $itemObj);

								$myStory[]  = $streamHTML;

					            unset($target);

							}//end verbActors


						} // foreach verbs


		            }// contentkey

		        } //minkey

		    }//hourkeys

		}

		// $myStory    = array_reverse( $myStory );

		// echo '<pre>';print_r($myStory);echo '</pre>';

		return $myStory;
	}

	private function _htmlContent( $userId, $contextString, $itemCount, $itemObj)
	{
		$db         = EasyBlogHelper::db();
		$verb       = $itemObj->verb;
		$streamHTML = 'Failed retrieve content.';

		$contextArr	= explode( '-', $contextString);
		$context    = $contextArr[0];

		switch($context)
		{
			case 'comment':

				$item     	= $itemObj->target[0][1];
				$actor      = $itemObj->actor;
				$extra      = '';

				$obj    	= unserialize( $item->uuid );

				if( $item->target_id == $userId )
				{
					$actor  = $obj->commentauthor;
					$extra  = '_TARGET';
				}
				else
				{
					$actor  = JText::_( 'COM_EASYBLOG_STREAM_YOU_AS_ACTOR' );
				}


				$commentId  = $item->context_id;
				$blogId   	= $item->source_id;

				$comment   		= EasyBlogCommentHelper::parseBBCode($obj->comment);
				$blogLink   	= EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $blogId  ) . '#comment-' . $commentId;
				$title      	= JText::sprintf( 'COM_EASYBLOG_STREAM_COMMENT_ADD' . $extra, $actor, $blogLink, $obj->blogtitle, $obj->blogtitle);
				$commentDate    = EasyBlogDateHelper::getDate( $item->created );

				$filename       = 'stream.comment.' . $verb . '.php';
				$streamTheme	= new CodeThemes();

				$streamTheme->set( 'title'			, $title);
				$streamTheme->set( 'time'			, $itemObj->friendlyTS);
				$streamTheme->set( 'commentDate'	, $commentDate->toFormat());
				$streamTheme->set( 'comment'		, $comment);
				$streamHTML = $streamTheme->fetch( $filename );

				break;



			case 'profile':

				$filename       = 'stream.profile.' . $verb . '.php';
				$streamTheme	= new CodeThemes();

				// $actor      = $itemObj->actor;
				$actor  = JText::_( 'COM_EASYBLOG_STREAM_YOU_AS_ACTOR' );


				$streamTheme->set( 'actor'			, $actor);
				$streamTheme->set( 'time'			, $itemObj->friendlyTS);
				$streamHTML = $streamTheme->fetch( $filename );


				break;

			case 'blogger':

				$streamTheme	= new CodeThemes();
				$extra  		= '';

				$actor      = $itemObj->actor;
				$targets	= $itemObj->target;

				$actorArr    = array();
				$targetArr   = array();


				if( count( $targets ) > 1 )
				{
					$stringTitle    = array();
					$cnt            = 0;
					$swap           = false;

					foreach( $targets as $target )
					{
						$item  		= $target[1];

						$obj        = unserialize( $item->uuid );

						if( $item->target_id == $userId )
						{
							$actorArr[]     = $obj->subscribername . '(' . $obj->subscriberemail . ')';
							$targetArr[]    = $obj->bloggername;

							// we knwo this userId is now target. Get the correct theme file.
							$extra = '.target';
						}
						else
						{
							$bloggerLink    = EasyBlogRouter::_( 'index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $item->context_id  );
							$tmpString  	= '<a href="' . $bloggerLink. '">' . $obj->bloggername . '</a>';

							//$actorArr[]     = $obj->subscribername;
							$actorArr[]     = JText::_( 'COM_EASYBLOG_STREAM_YOU_AS_ACTOR' );
							$targetArr[]    = $tmpString;
						}
					}

				}
				else
				{
					$item  		= $targets[0][1];
					$obj        = unserialize( $item->uuid );

					if( $item->target_id == $userId )
					{
						$actorArr[]      = $obj->subscribername . '(' . $obj->subscriberemail . ')';
						$targetArr[]     = $obj->bloggername;

						// we knwo this userId is now target. Get the correct theme file.
						$extra = '.target';
					}
					else
					{
						$bloggerLink   	= EasyBlogRouter::_( 'index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $item->context_id  );
						$title  		= '<a href="' . $bloggerLink. '">' . $obj->bloggername . '</a>';

						// $actorArr[]     = $obj->subscribername;
						$actorArr[]     = JText::_( 'COM_EASYBLOG_STREAM_YOU_AS_ACTOR' );
						$targetArr[]    = $title;
					}
				}

				$targetArr	= array_unique($targetArr);
				$actorArr	= array_unique($actorArr);

				$stringActor    	= '';
				$stringTarget    	= '';

				// actors
				$cnt = 0;
				foreach( $actorArr as $item)
				{
					if( empty( $stringActor ) )
					{
						$stringActor  = $item;
					}
					else
					{
						$stringActor  .=  ( ($cnt + 1) == count($actorArr) ) ? ' and ' . $item : ', ' . $item;
					}

					$cnt++;
				}

				//targets
				$cnt = 0;
				foreach( $targetArr as $item)
				{
					if( empty( $stringTarget ) )
					{
						$stringTarget  = $item;
					}
					else
					{
						$stringTarget  .=  ( ($cnt + 1) == count($targetArr) ) ? ' and ' . $item : ', ' . $item;
					}

					$cnt++;
				}

				$streamTheme->set( 'actor'			, $stringActor);
				$streamTheme->set( 'target'			, $stringTarget);
				$streamTheme->set( 'actorCnt'		, count($actorArr) );
				$streamTheme->set( 'time'			, $itemObj->friendlyTS);


				$filename       = 'stream.blogger.' . $verb . $extra . '.php';

				$streamHTML = $streamTheme->fetch( $filename );


				break;


			case 'post':

				$streamTheme	= new CodeThemes();
				$extra  		= '';

				$actor      = $itemObj->actor;
				$targets	= $itemObj->target;


				/* old codes */
// 				if( count( $targets ) > 1 )
// 				{
// 					$stringTitle    = '';
// 					$cnt            = 0;
// 					foreach( $targets as $target )
// 					{
// 						$item  		= $target[1];
// 						$blogLink   = EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $item->context_id  );
// 						$tmpString  = '<a href="' . $blogLink. '">' . $item->uuid . '</a>';
//
// 						if( empty( $stringTitle ) )
// 						{
// 							$stringTitle  = $tmpString;
// 						}
// 						else
// 						{
// 							$stringTitle  .=  ( ($cnt + 1) == count($targets) ) ? ' and ' . $tmpString : ', ' . $tmpString;
// 						}
//
// 						$cnt++;
// 					}
//
// 					$title  = $stringTitle;
//
// 				}
// 				else
// 				{
// 					$item  		= $targets[0][1];
// 					$blogLink   = EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $item->context_id  );
// 					$title  = '<a href="' . $blogLink. '">' . $item->uuid . '</a>';
// 				}
				/* old codes ended */


				$actorArr    = array();
				$targetArr   = array();


				if( count( $targets ) > 1 )
				{
					$stringTitle    = array();
					$cnt            = 0;
					$swap           = false;

					foreach( $targets as $target )
					{
						$item  		= $target[1];
						$obj        = @unserialize( $item->uuid );
						if( $obj === false )
						{
							$obj    = new stdClass();
							$obj->blogtitle = $item->uuid;
						}

						if( $item->target_id == $userId )
						{
							$actorArr[]     = $obj->subscribername;
							// we knwo this userId is now target. Get the correct theme file.
							$extra = '.target';
						}
						else
						{
							// $actorArr[]     = $itemObj->actor;
							$actorArr[]     = JText::_( 'COM_EASYBLOG_STREAM_YOU_AS_ACTOR' );
						}

						$blogLink   = EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $item->context_id  );
						$title  = '<a href="' . $blogLink. '">' . $obj->blogtitle . '</a>';

						$targetArr[]    = $title;
					}

				}
				else
				{
					$item  		= $targets[0][1];
					$obj        = @unserialize( $item->uuid );

					if( $obj === false )
					{
						$obj    = new stdClass();
						$obj->blogtitle = $item->uuid;
					}

					if( $item->target_id == $userId )
					{
						$actorArr[]     = $obj->subscribername;
						// we knwo this userId is now target. Get the correct theme file.
						$extra = '.target';
					}
					else
					{
						//$actorArr[]     = $itemObj->actor;
						$actorArr[]     = JText::_( 'COM_EASYBLOG_STREAM_YOU_AS_ACTOR' );

					}

					$blogLink   = EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $item->context_id  );
					$title  = '<a href="' . $blogLink. '">' . $obj->blogtitle . '</a>';

					$targetArr[]    = $title;
				}

				$targetArr	= array_unique($targetArr);
				$actorArr	= array_unique($actorArr);

				$stringActor    	= '';
				$stringTarget    	= '';

				// actors
				$cnt = 0;
				foreach( $actorArr as $item)
				{
					if( empty( $stringActor ) )
					{
						$stringActor  = $item;
					}
					else
					{
						$stringActor  .=  ( ($cnt + 1) == count($actorArr) ) ? JText::_('COM_EASYBLOG_STREAM_AND') . $item : ', ' . $item;
					}

					$cnt++;
				}

				//targets
				$cnt = 0;
				foreach( $targetArr as $item)
				{
					if( empty( $stringTarget ) )
					{
						$stringTarget  = $item;
					}
					else
					{
						$stringTarget  .=  ( ($cnt + 1) == count($targetArr) ) ? JText::_('COM_EASYBLOG_STREAM_AND') . $item : ', ' . $item;
					}

					$cnt++;
				}


				$filename       = 'stream.post.' . $verb . $extra . '.php';

				$streamTheme->set( 'actor'			, $stringActor);
				$streamTheme->set( 'target'			, $stringTarget);
				$streamTheme->set( 'actorCnt'		, count($actorArr) );

				$streamTheme->set( 'time'			, $itemObj->friendlyTS);
				$streamHTML = $streamTheme->fetch( $filename );


				break;
			case 'tag':
			case 'category':

				$jtext    	= ( $context == 'category' ) ? 'CATEGORY' : 'TAG';
				$view    	= ( $context == 'category' ) ? 'categories' : 'tags';
				$layout    	= ( $context == 'category' ) ? 'listings' : 'tag';


				//$actor      = $itemObj->actor;
				$actor		= JText::_( 'COM_EASYBLOG_STREAM_YOU_AS_ACTOR' );
				$items     	= $itemObj->target;
				$categories = '';

				if( $verb   == 'delete' )
				{
					$total  	= count($items);
					$i          = 0;

					foreach($items as $itemArr)
					{
						$item   = $itemArr[1];

						if( empty($categories) )
						{
							$categories = $item->uuid;
						}
						else
						{
							if( ($i + 1) == $total )
							{
								$categories .= JText::_('COM_EASYBLOG_STREAM_AND') . $item->uuid;
							}
							else
							{
								$categories .= ', ' . $item->uuid;
							}
						}
					}
				}
				else
				{

					$total  	= count($items);
					$i          = 0;

					foreach($items as $itemArr)
					{
						$item   = $itemArr[1];

						$catLink    = EasyBlogRouter::_('index.php?option=com_easyblog&view='.$view.'&layout='.$layout.'&id=' . $item->context_id);

						if( empty($categories) )
						{
							$categories = '<a href="' . $catLink. ' " title="' . EasyBlogHelper::getHelper( 'String' )->escape( $item->uuid ) . '">' . $item->uuid . '</a>';
						}
						else
						{
							if( ($i + 1) == $total )
							{
								$categories .= JText::_('COM_EASYBLOG_STREAM_AND') . '<a href="' . $catLink. ' " title="' . EasyBlogHelper::getHelper( 'String' )->escape( $item->uuid ) . '">' . $item->uuid . '</a>';
							}
							else
							{
								$categories .= ', <a href="' . $catLink. ' " title="' . EasyBlogHelper::getHelper( 'String' )->escape( $item->uuid )  . '">' . $item->uuid . '</a>';
							}
						}

						$i++;
					}//end foreach
				}

				$title	= JText::sprintf( 'COM_EASYBLOG_STREAM_'.$jtext.'_' . strtoupper( $verb ), $actor, $categories );

				$filename       = 'stream.'.strtolower($jtext).'.' . $verb . '.php';
				$streamTheme	= new CodeThemes();

				$streamTheme->set( 'title'			, $title);
				$streamTheme->set( 'time'			, $itemObj->friendlyTS);
				$streamHTML = $streamTheme->fetch( $filename );

				break;
			default:
				$streamHTML = $itemObj->story;
				break;
		}

		return $streamHTML;
	}

	public function getNextCount( $userId, $startlimit = 0, $limit = 0 )
	{
		$data   = $this->_getStream($userId, $startlimit, $limit);

		if( empty( $data ) )
		{
			return array();
		}

		// lets process the streams.
		$stream = $this->_aggregateStream($userId, $data);

		return $stream;
	}

	public function get( $userId, $limit = 0, $startlimit = 0 )
	{
		$data   = $this->_getStream($userId, $limit, $startlimit);

		if( empty( $data ) )
		{
			return array();
		}

		// lets process the streams.
		$stream = $this->_aggregateStream($userId, $data);

		return $stream;
	}

	public function add( $data )
	{
		$streamId   = '';
	    if( is_array( $data ) )
	    {
	        // array
	        $streamId   = array();
			for($i = 0; $i < count($data); $i++ )
			{
			    $item   = $data[$i];

			    if( is_array( $item ) )
			    {
			        //array
			        // cast into object
			        $object = (object) $item;
			        $streamId[] = $this->_insertStream( $object );
			    }
			    else if( is_object( $item ) )
				{
				    //object
				    $streamId[] = $this->_insertStream( $item );
				}
			}
	    }
	    else if( is_object( $data ) )
	    {
	        // object
	        $streamId = $this->_insertStream( $data );
	    }

	    return $streamId;
	}
}
