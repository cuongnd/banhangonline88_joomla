<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once DISCUSS_ADMIN_ROOT . '/views.php';
require_once DISCUSS_CLASSES . '/json.php';
jimport( 'joomla.utilities.utility' );

class EasyDiscussViewMigrators extends EasyDiscussAdminView
{
	public function communitypolls()
	{
		$ajax 	= DiscussHelper::getHelper( 'Ajax' );

		// Migrate Community Poll categories
		$categories	= $this->getCPCategories();
		$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_COMMUNITY_POLLS_TOTAL_CATEGORIES' , count( $categories) ) , 'communitypolls' );

		$json 	= new Services_JSON();
		$items 	= array();

		foreach( $categories as $category )
		{
			$items[]	= $category->id;
		}

		$ajax->resolve( $items );
	}

	public function kunena()
	{
		$ajax		= new Disjax();

		// @task: Get list of categories from Kunena first.
		$categories	= $this->getKunenaCategories();

		// @task: Add some logging
		$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_KUNENA_TOTAL_CATEGORIES' , count( $categories ) ) , 'kunena' );

		$json	= new Services_JSON();
		$items	= array();

		foreach( $categories as $category )
		{
			$items[]	= $category->id;
		}

		$data	= $json->encode( $items );

		// @task: Start migration process, passing back to the AJAX methods
		$ajax->script( 'runMigrationCategory("kunena", ' . $data . ');' );

		return $ajax->send();
	}

	public function jomsocialgroups()
	{
		$ajax		= new Disjax();

		$groups 	= $this->getJomSocialGroups();

		// @task: Add some logging
		$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_JOMSOCIAL_TOTAL_GROUPS' , count( $groups ) ) , 'jomsocialgroups' );


		$json	= new Services_JSON();
		$items	= array();

		foreach( $groups as $group )
		{
			$items[]	= $group->id;
		}

		$data	= $json->encode( $items );

		// @task: Start migration process, passing back to the AJAX methods
		$ajax->script( 'runMigrationCategory("jomsocialgroups", ' . $data . ');' );

		return $ajax->send();
	}

	public function getJomSocialGroups()
	{
		$db 	= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__community_groups' );
		$db->setQuery( $query );
		$result 	= $db->loadObjectList();

		return $result;
	}

	public function showMigrationButton( &$ajax )
	{
		$ajax->script( 'discussQuery(".migrator-button").show();' );
	}

	public function jomsocialgroupsCategoryItem( $current , $groups )
	{
		$ajax	= new Disjax();

		$group 		= $this->getJomSocialGroup( $current );

		// @task: Skip the category if it has already been migrated.
		if( $this->migrated( 'com_community' , $current , 'groups') && $groups != 'done' )
		{
			$data	= $this->json_encode( $groups );
			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_JOMSOCIAL_GROUP_MIGRATED_SKIPPING' , $group->name ) , 'jomsocialgroups' );
			$ajax->script( 'runMigrationCategory("jomsocialgroups" , ' . $data . ');' );
			return $ajax->send();
		}

		// @task: Create the category
		$category	= DiscussHelper::getTable( 'Category' );
		$this->mapJomsocialCategory( $group , $category );
		$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_JOMSOCIAL_GROUP_MIGRATED' , $group->name ) , 'jomsocialgroups' );

		$data	= $this->json_encode( $groups );

		// @task: If categories is no longer an array, then it most likely means that there's nothing more to process.
		if( $groups == 'done' )
		{
			$this->log( $ajax , JText::_( 'COM_EASYDISCUSS_MIGRATORS_CATEGORY_MIGRATION_COMPLETED' ) , 'jomsocialgroups' );

			$posts		= $this->getJomsocialPostsIds();
			$data		= $this->json_encode( $posts );

			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_JOMSOCIAL_TOTAL_DISCUSSIONS' , count( $posts ) ) , 'jomsocialgroups' );

			// @task: Run migration for post items.
			$ajax->script( 'runMigrationItem("jomsocialgroups" , ' . $data . ');' );
			return $ajax->send();
		}

		$ajax->script( 'runMigrationCategory("jomsocialgroups" , ' . $data . ');' );

		$ajax->send();
	}

	public function communitypollsCategoryItem()
	{
		$ajax 		= DiscussHelper::getHelper( 'Ajax' );
		$current 	= JRequest::getVar( 'current' );
		$categories	= JRequest::getVar( 'categories' );

		$cpCategory	= $this->getCPCategory( $current );

		// @task: If categories is no longer an array, then it most likely means that there's nothing more to process.
		if( !$categories && !$current )
		{
			$this->log( $ajax , JText::_( 'COM_EASYDISCUSS_MIGRATORS_CATEGORY_MIGRATION_COMPLETED' ) , 'communitypolls' );

			$posts		= $this->getCPPostsIds();

			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_COMMUNITY_POLLS_TOTAL_POLLS' , count( $posts ) ) , 'communitypolls' );

			// @task: Run migration for post items.
			$ajax->migratePolls( $posts );

			return $ajax->resolve( 'done' , true );
		}

		// @task: Skip the category if it has already been migrated.
		if( $this->migrated( 'com_communitypolls' , $current , 'category') )
		{
			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_KUNENA_CATEGORY_MIGRATED_SKIPPING' , $cpCategory->title ) , 'communitypolls' );
		}
		else
		{
			// @task: Create the category
			$category	= DiscussHelper::getTable( 'Category' );
			$this->mapCPCategory( $cpCategory , $category );
			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_COMMUNITY_POLLS_CATEGORY_MIGRATED' , $cpCategory->title ) , 'communitypolls' );
		}

		$ajax->resolve( $categories , false );
	}

	public function kunenaCategoryItem( $current = "" , $categories = "" )
	{
		$ajax	= new Disjax();

		$kCategory	= $this->getKunenaCategory( $current );

		// @task: If categories is no longer an array, then it most likely means that there's nothing more to process.
		if( $current == 'done' )
		{
			$this->log( $ajax , JText::_( 'COM_EASYDISCUSS_MIGRATORS_CATEGORY_MIGRATION_COMPLETED' ) , 'kunena' );

			$posts		= $this->getKunenaPostsIds();
			$data		= $this->json_encode( $posts );

			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_KUNENA_TOTAL_POSTS' , count( $posts ) ) , 'kunena' );

			// @task: Run migration for post items.
			$ajax->script( 'runMigrationItem("kunena" , ' . $data . ');' );
			return $ajax->send();
		}

		// @task: Skip the category if it has already been migrated.
		if( $this->migrated( 'com_kunena' , $current , 'category') )
		{
			$data	= $this->json_encode( $categories );
			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_KUNENA_CATEGORY_MIGRATED_SKIPPING' , $kCategory->name ) , 'kunena' );
			$ajax->script( 'runMigrationCategory("kunena" , ' . $data . ');' );
			return $ajax->send();
		}

		// @task: Create the category
		$category	= DiscussHelper::getTable( 'Category' );
		$this->mapKunenaCategory( $kCategory , $category );
		$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_KUNENA_CATEGORY_MIGRATED' , $kCategory->name ) , 'kunena' );




		$data	= $this->json_encode( $categories );

		$ajax->script( 'runMigrationCategory("kunena" , ' . $data . ');' );

		$ajax->send();
	}

	public function jomsocialgroupsPostItem( $current , $items )
	{
		$ajax	= new Disjax();

		// @task: Map main discussion from group with EasyDiscuss
		$discussion	= $this->getJomsocialPost( $current );
		$item		= DiscussHelper::getTable( 'Post' );

		// @task: Skip the category if it has already been migrated.
		if( $this->migrated( 'com_community' , $current , 'discussions') && $items != 'done' )
		{
			$data	= $this->json_encode( $items );
			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_POST_MIGRATED_SKIPPING' , $discussion->title ) , 'jomsocialgroups' );
			$ajax->script( 'runMigrationItem("jomsocialgroups" , ' . $data . ');' );
			return $ajax->send();
		}


		$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_POST_MIGRATED' , $discussion->title ) , 'jomsocialgroups' );
		$this->mapJomsocialItem( $discussion , $item );

		// @task: Once the post is migrated successfully, we'll need to migrate the child items.
		$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_POST_REPLIES_MIGRATED' , $discussion->title ) , 'jomsocialgroups' );
		$this->mapJomsocialItemChilds( $discussion , $item );


		// @task: If categories is no longer an array, then it most likely means that there's nothing more to process.
		if( !is_array( $items ) )
		{
			$this->log( $ajax , JText::_( 'COM_EASYDISCUSS_MIGRATORS_MIGRATION_COMPLETED' ) , 'jomsocialgroups' );
			$this->showMigrationButton( $ajax );
			return $ajax->send();
		}

		$data	= $this->json_encode( $items );

		$ajax->script( 'runMigrationItem("jomsocialgroups" , ' . $data . ');' );

		$ajax->send();
	}

	public function communitypollsPostItem()
	{
		$ajax 	= DiscussHelper::getHelper( 'Ajax' );

		$current 	= JRequest::getVar( 'current' );
		$items		= JRequest::getVar( 'items' );


		// Map community polls item with EasyDiscuss item.
		$cpItem 	= $this->getCPPost( $current );
		$item		= DiscussHelper::getTable( 'Post' );

		// @task: Skip the category if it has already been migrated.
		if( $this->migrated( 'com_communitypolls' , $current , 'post') )
		{
			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_POST_MIGRATED_SKIPPING' , $cpItem->id ) , 'communitypolls' );

			return $ajax->resolve( $items );
		}

		$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_COMMUNITY_POLLS_POLL_MIGRATED' , $cpItem->id ) , 'communitypolls' );
		$this->mapCPItem( $cpItem , $item );

		return $ajax->resolve( $items );
	}

	public function kunenaPostItem( $current , $items )
	{
		$ajax	= new Disjax();


		// @task: Map kunena post item with EasyDiscuss items.
		$kItem	= $this->getKunenaPost( $current );
		$item	= DiscussHelper::getTable( 'Post' );

		// @task: Skip the category if it has already been migrated.
		if( $this->migrated( 'com_kunena' , $current , 'post') )
		{
			$data	= $this->json_encode( $items );
			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_POST_MIGRATED_SKIPPING' , $kItem->id ) , 'kunena' );
			$ajax->script( 'runMigrationItem("kunena" , ' . $data . ');' );
			return $ajax->send();
		}


		$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_POST_MIGRATED' , $kItem->id ) , 'kunena' );
		$this->mapKunenaItem( $kItem , $item );

		// @task: Once the post is migrated successfully, we'll need to migrate the child items.
		$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_POST_REPLIES_MIGRATED' , $kItem->id ) , 'kunena' );
		$this->mapKunenaItemChilds( $kItem , $item );

		// @task: If categories is no longer an array, then it most likely means that there's nothing more to process.
		if( !is_array( $items ) )
		{
			$this->log( $ajax , JText::_( 'COM_EASYDISCUSS_MIGRATORS_MIGRATION_COMPLETED' ) , 'kunena' );
			$this->showMigrationButton( $ajax );
			return $ajax->send();
		}


		$data	= $this->json_encode( $items );

		$ajax->script( 'runMigrationItem("kunena" , ' . $data . ');' );

		$ajax->send();
	}

	private function json_encode( $data )
	{
		$json	= new Services_JSON();
		$data	= $json->encode( $data );

		return $data;
	}

	private function json_decode( $data )
	{
		$json	= new Services_JSON();
		$data	= $json->decode( $data );

		return $data;
	}

	private function log( &$ajax , $message , $type )
	{
		if( $ajax instanceof DiscussAjaxHelper )
		{
			$ajax->updateLog( $message );
		}
		else
		{
			$ajax->script( 'appendLog("' . $type . '" , "' . $message . '");' );
		}
	}

	private function mapCPCategory( $cpCategory , &$category )
	{
		$category->set( 'title'			, $cpCategory->title );
		$category->set( 'alias'			, $cpCategory->alias );
		$category->set( 'published'		, $cpCategory->published );
		$category->set( 'parent_id'		, 0 );

		// @task: Since CP does not store the creator of the category, we'll need to assign a default owner.
		$category->set( 'created_by'	, DiscussHelper::getDefaultSAIds() );

		// @TODO: Detect if it has a parent id and migrate according to the category tree.
		$category->store( true );

		$this->added( 'com_communitypolls' , $category->id , $cpCategory->id , 'category' );
	}

	private function mapKunenaCategory( $kCategory , &$category )
	{
		$category->set( 'title'			, $kCategory->name );

		$category->set( 'description'	, $kCategory->description );
		$category->set( 'published'		, $kCategory->published );
		$category->set( 'parent_id'		, 0 );

		// @task: Since Kunena does not store the creator of the category, we'll need to assign a default owner.
		$category->set( 'created_by'	, DiscussHelper::getDefaultSAIds() );

		// @TODO: Detect if it has a parent id and migrate according to the category tree.
		$category->store( true );

		$this->added( 'com_kunena' , $category->id , $kCategory->id , 'category' );
	}

	private function mapJomsocialCategory( $group , &$category )
	{
		$category->set( 'title'			, $group->name );

		$category->set( 'description'	, $group->description );
		$category->set( 'published'		, $group->published );
		$category->set( 'parent_id'		, 0 );

		// @task: Since Kunena does not store the creator of the category, we'll need to assign a default owner.
		$category->set( 'created_by'	, $group->ownerid );

		// @TODO: Detect if it has a parent id and migrate according to the category tree.
		$category->store( true );

		$this->added( 'com_community' , $category->id , $group->id , 'groups' );
	}

	private function mapJomsocialItem( $discussion , &$item , &$parent = null )
	{
		require_once JPATH_ROOT . '/components/com_community/libraries/core.php';

		// @task: If this is a child post, we definitely have the item's id.
		if( $parent )
		{
			$item->set( 'parent_id'	, $parent->id );
			$user 		= CFactory::getUser( $discussion->post_by );

			$content 	= $discussion->comment;

			$item->set( 'title' 		, 'RE: ' . $parent->title );
			$item->set( 'created'	 	, DiscussHelper::getDate( $discussion->date )->toMySQL() );
			$item->set( 'replied' 		, DiscussHelper::getDate( $discussion->date )->toMySQL() );
			$item->set( 'category_id' 	, 0 );
			$item->set( 'islock'		, 0 );

			$type 		= 'replies';
		}
		else
		{
			$user 		= CFactory::getUser( $discussion->creator );
			$content 	= $this->getJomsocialMessage( $discussion );

			$item->set( 'parent_id'		, 0 );
			$item->set( 'title' 		, $discussion->title );
			$item->set( 'created'	 	, DiscussHelper::getDate( $discussion->created )->toMySQL() );
			$item->set( 'replied' 		, DiscussHelper::getDate( $discussion->lastreplied )->toMySQL() );
			$item->set( 'category_id' 	, $this->getJomsocialNewCategory( $discussion ) );
			$item->set( 'islock'		, $discussion->lock );

			$type 		= 'discussions';
		}

		$item->set( 'content'		, $content );
		$item->set( 'hits'			, 0 );
		$item->set( 'user_id'		, $user->id );
		$item->set( 'user_type' 	, DISCUSS_POSTER_MEMBER );
		$item->set( 'poster_name'	, $user->getDisplayName() );
		$item->set( 'poster_email'	, $user->email );
		$item->set( 'published'		, DISCUSS_ID_PUBLISHED );

		$item->store();

		$this->added( 'com_community' , $item->id , $discussion->id , $type );
	}

	private function mapCPItem( $cpItem , &$item , &$parent = null )
	{

		$item->set( 'title' 		, $cpItem->title );
		$item->set( 'alias' 		, $cpItem->alias );
		$item->set( 'content'		, $cpItem->description );
		$item->set( 'category_id' 	, $this->getCPNewCategory( $cpItem ) );
		$item->set( 'user_id'		, $cpItem->created_by );
		$item->set( 'user_type' 	, DISCUSS_POSTER_MEMBER );
		$item->set( 'created'	 	, $cpItem->created );
		$item->set( 'modified'	 	, $cpItem->created );
		$item->set( 'parent_id'		, 0 );
		$item->set( 'published'		, DISCUSS_ID_PUBLISHED );
		$item->store();

		// Get poll answers
		$answers 	= $this->getCPAnswers( $cpItem );

		if( $answers )
		{
			// Create a new poll question
			$pollQuestion 		= DiscussHelper::getTable( 'PollQuestion' );
			$pollQuestion->title 	= $cpItem->title;
			$pollQuestion->post_id 	= $item->id;
			$pollQuestion->multiple	= $cpItem->type == 'checkbox' ? true : false;

			$pollQuestion->store();

			foreach( $answers as $answer )
			{
				$poll = DiscussHelper::getTable( 'Poll' );

				$poll->post_id 	= $item->id;
				$poll->value 	= $answer->title;
				$poll->count 	= $answer->votes;

				$poll->store();

				// Get all voters information
				$voters 		= $this->getCPVoters( $answer->id );

				foreach($voters as $voter)
				{
					$pollUser 	= DiscussHelper::getTable( 'PollUser' );
					$pollUser->user_id 	= $voter->voter_id;
					$pollUser->poll_id 	= $poll->id;

					$pollUser->store();
				}
			}
		}


		$this->added( 'com_communitypolls' , $item->id , $cpItem->id , 'post' );
	}

	private function mapKunenaItem( $kItem , &$item , &$parent = null )
	{
		$content	= $this->getKunenaMessage( $kItem );

		$item->set( 'content'		, $content );
		$item->set( 'title' 		, $kItem->subject );
		$item->set( 'category_id' 	, $this->getNewCategory( $kItem ) );
		$item->set( 'user_id'		, $kItem->userid );
		$item->set( 'user_type' 	, DISCUSS_POSTER_MEMBER );
		$item->set( 'hits'			, $kItem->hits );
		$item->set( 'created'	 	, DiscussHelper::getDate( $kItem->time )->toMySQL() );
		$item->set( 'modified' 		, DiscussHelper::getDate( $kItem->time )->toMySQL() );
		$item->set( 'replied' 		, DiscussHelper::getDate( $kItem->time )->toMySQL() );
		$item->set( 'poster_name'	, $kItem->name );
		$item->set( 'parent_id'		, 0 );

		// @task: If this is a child post, we definitely have the item's id.
		if( $parent )
		{
			$item->set( 'parent_id'	, $parent->id );
		}

		$item->set( 'islock'		, $kItem->locked );
		$item->set( 'poster_email'	, $kItem->email );
		$item->set( 'published'		, DISCUSS_ID_PUBLISHED );

		if( !$kItem->userid )
		{
			$item->set( 'user_type' , DISCUSS_POSTER_GUEST );
		}

		$item->store();

		// @task: Get attachments
		$files	= $this->getKunenaAttachments( $kItem );

		if( $files )
		{
			foreach( $files as $kAttachment )
			{
				$attachment	= DiscussHelper::getTable( 'Attachments');

				$attachment->set( 'uid' 	, $item->id );
				$attachment->set( 'size'	, $kAttachment->size );
				$attachment->set( 'title'	, $kAttachment->filename );
				$attachment->set( 'type'	, $item->getType() );
				$attachment->set( 'published',	DISCUSS_ID_PUBLISHED );
				$attachment->set( 'mime'	, $kAttachment->filetype );

				// Regenerate the path

				$isJoomla30 = DiscussHelper::isJoomla30();

				if( $isJoomla30 )
				{
					// JUtility::getHash is deprecated
					$path	= JApplication::getHash( $kAttachment->filename . DiscussHelper::getDate()->toMySQL() );	
				}
				else
				{
					$path	= JUtility::getHash( $kAttachment->filename . DiscussHelper::getDate()->toMySQL() );	
				}


				$attachment->set( 'path'	, $path );

				// Copy files over.
				$config		= DiscussHelper::getConfig();

				$folderPath = DISCUSS_MEDIA . '/' . trim( $config->get( 'attachment_path' ) , DIRECTORY_SEPARATOR ) ;
				$storage	= $folderPath . '/' . $path;
				$kStorage	= JPATH_ROOT . '/' . rtrim( $kAttachment->folder , '/' )  . '/' . $kAttachment->filename;

				// create folder if it not exists
				if(! JFolder::exists( $folderPath ) )
				{
					JFolder::create( $folderPath );
					JFile::copy( DISCUSS_ROOT . '/index.html' , $path . '/index.html' );
				}
				JFile::copy( $kStorage , $storage );

				if( DiscussHelper::getHelper( 'Image' )->isImage( $kAttachment->filename ) )
				{
					require_once DISCUSS_CLASSES . '/simpleimage.php';
					$image	= new SimpleImage;

					$image->load( $kStorage );
					$image->resizeToFill( 160 , 120 );
					$image->save( $storage . '_thumb', $image->image_type);
				}

				// @task: Since Kunena does not store this, we need to generate the own creation timestamp.
				$attachment->set( 'created'	, DiscussHelper::getDate()->toMySQL() );

				$attachment->store();
			}
		}

		//perform cleanup


		$this->added( 'com_kunena' , $item->id , $kItem->id , 'post' );
	}

	private function mapJomsocialItemChilds( $discussion , &$parent )
	{
		$items	= $this->getJomSocialPosts( $discussion );

		if( !$items )
		{
			return false;
		}

		foreach( $items as $discussChildItem )
		{
			$item	= DiscussHelper::getTable( 'Post' );
			$this->mapJomsocialItem( $discussChildItem , $item , $parent );
		}
	}

	private function mapKunenaItemChilds( $kItem , &$parent )
	{
		$items	= $this->getKunenaPosts( $kItem );

		if( !$items )
		{
			return false;
		}

		foreach( $items as $kChildItem )
		{
			$item	= DiscussHelper::getTable( 'Post' );
			$this->mapKunenaItem( $kChildItem , $item , $parent );
		}
	}

	private function added( $component , $internalId , $externalId , $type )
	{
		$migrator	= DiscussHelper::getTable( 'Migrators' );
		$migrator->set( 'component' 	, $component );
		$migrator->set( 'external_id'	, $externalId );
		$migrator->set( 'internal_id'	, $internalId );
		$migrator->set( 'type'			, $type );

		return $migrator->store();
	}

	private function getNewCategory( $kItem )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT ' . $db->nameQuote( 'internal_id' ) . ' '
				. 'FROM ' . $db->nameQuote( '#__discuss_migrators' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'external_id' ) . ' = ' . $db->Quote( $kItem->catid ) . ' '
				. 'AND ' . $db->nameQuote( 'type' ) . ' = ' . $db->Quote( 'category' ) . ' '
				. 'AND ' . $db->nameQuote( 'component' ) . ' = ' . $db->Quote( 'com_kunena' );

		$db->setQuery( $query );
		$categoryId	= $db->loadResult();

		return $categoryId;
	}

	private function getCPNewCategory( $cpItem )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT ' . $db->nameQuote( 'internal_id' ) . ' '
				. 'FROM ' . $db->nameQuote( '#__discuss_migrators' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'external_id' ) . ' = ' . $db->Quote( $cpItem->category ) . ' '
				. 'AND ' . $db->nameQuote( 'type' ) . ' = ' . $db->Quote( 'category' ) . ' '
				. 'AND ' . $db->nameQuote( 'component' ) . ' = ' . $db->Quote( 'com_communitypolls' );

		$db->setQuery( $query );
		$categoryId	= $db->loadResult();

		return $categoryId;
	}

	private function getJomsocialNewCategory( $discussion )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT ' . $db->nameQuote( 'internal_id' ) . ' '
				. 'FROM ' . $db->nameQuote( '#__discuss_migrators' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'external_id' ) . ' = ' . $db->Quote( $discussion->groupid ) . ' '
				. 'AND ' . $db->nameQuote( 'type' ) . ' = ' . $db->Quote( 'groups' ) . ' '
				. 'AND ' . $db->nameQuote( 'component' ) . ' = ' . $db->Quote( 'com_community' );

		$db->setQuery( $query );
		$categoryId	= $db->loadResult();

		return $categoryId;
	}

	private function getKunenaMessage( $kItem )
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT ' . $db->nameQuote( 'message' ) . ' FROM ' . $db->nameQuote( '#__kunena_messages_text' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'mesid' ) . '=' . $db->Quote( $kItem->id );
		$db->setQuery( $query );

		$message	= $db->loadResult();

		// @task: Replace unwanted bbcode's.
		$message	= preg_replace( '/\[attachment\="?(.*?)"?\](.*?)\[\/attachment\]/ms' , '' , $message );

		return $message;
	}

	private function getJomsocialMessage( $discussion )
	{
		$config 	= DiscussHelper::getConfig();

		if( $config->get( 'layout_editor') == 'bbcode' )
		{
			// Convert content to bbcode
			require_once DISCUSS_HELPERS . '/parser.php';
			return EasyDiscussParser::html2bbcode( $discussion->message );
		}

		return $discussion->message;
	}

	private function getCPAnswers( $cpItem )
	{
		$db 	= DiscussHelper::getDBO();

		$query 	= 'SELECT * FROM `#__jcp_options` WHERE `poll_id`=' . $db->Quote( $cpItem->id );
		$db->setQuery( $query );

		return $db->loadObjectList();
	}

	private function getKunenaPostsIds()
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT `id` FROM ' . $db->nameQuote( '#__kunena_messages' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'parent' ) . '=' . $db->Quote( 0 );
		$db->setQuery( $query );
		return $db->loadResultArray();
	}

	private function getCPPostsIds()
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT `id` FROM ' . $db->nameQuote( '#__jcp_polls' );
		$db->setQuery( $query );
		return $db->loadResultArray();
	}

	private function getJomsocialPostsIds()
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT `id` FROM ' . $db->nameQuote( '#__community_groups_discuss' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'parentid' ) . '=' . $db->Quote( 0 );
		$db->setQuery( $query );
		return $db->loadResultArray();
	}

	private function getKunenaPost( $id )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__kunena_messages' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $id );
		$db->setQuery( $query );
		$item	= $db->loadObject();

		return $item;
	}

	private function getCPPost( $id )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__jcp_polls' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $id );
		$db->setQuery( $query );
		$item	= $db->loadObject();

		return $item;
	}

	private function getCPVoters( $answerId )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__jcp_votes' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'option_id' ) . '=' . $db->Quote( $answerId );
		$db->setQuery( $query );
		$item	= $db->loadObjectList();

		return $item;
	}

	private function getJomsocialPost( $id )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__community_groups_discuss' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $id );
		$db->setQuery( $query );
		$item	= $db->loadObject();

		return $item;
	}

	private function getKunenaAttachments( $kItem )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__kunena_attachments' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'mesid' ) . '=' . $db->Quote( $kItem->id );
		$db->setQuery( $query );
		$attachments	= $db->loadObjectList();

		return $attachments;
	}

	private function getJomsocialPosts( $discussion = null , $category = null )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__community_wall' );

		$query	.= ' WHERE ' . $db->nameQuote( 'contentid' ) . ' = ' . $db->Quote( $discussion->id );
		$query	.= ' AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( 'discussions' );


		$db->setQuery( $query );

		$result	= $db->loadObjectList();

		if( !$result )
		{
			return false;
		}

		return $result;
	}

	private function getKunenaPosts( $kItem = null , $kCategory = null )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__kunena_messages' );

		if( !is_null( $kItem ) )
		{
			$query	.= ' WHERE ' . $db->nameQuote( 'thread' ) . ' = ' . $db->Quote( $kItem->id );
			$query	.= ' AND ' . $db->nameQuote( 'parent') . '!=' . $db->Quote( 0 );
		}


		$db->setQuery( $query );

		$result	= $db->loadObjectList();

		if( !$result )
		{
			return false;
		}

		return $result;
	}

	private function getJomSocialGroup( $id )
	{
		require_once JPATH_ROOT . '/components/com_community/libraries/core.php';

		JTable::addIncludePath( JPATH_ROOT . '/components/com_community/tables' );
		$group 	= JTable::getInstance( 'Group' , 'CTable' );
		$group->load( $id );

		return $group;
	}

	private function getKunenaCategory( $id )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__kunena_categories' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $id );
		$db->setQuery( $query );

		return $db->loadObject();
	}

	private function getCPCategory( $id )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__jcp_categories' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $id );
		$db->setQuery( $query );

		return $db->loadObject();
	}

	/**
	 * Determines if an item is already migrated
	 */
	private function migrated( $component , $externalId , $type )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT COUNT(1) '
				. 'FROM ' . $db->nameQuote( '#__discuss_migrators' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'external_id' ) . ' = ' . $db->Quote( $externalId ) . ' '
				. 'AND ' . $db->nameQuote( 'type' ) . ' = ' . $db->Quote( $type ) . ' '
				. 'AND ' . $db->nameQuote( 'component' ) . ' = ' . $db->Quote( $component );
		$db->setQuery( $query );
		$exists	= $db->loadResult() > 0;

		return $exists;
	}

	/**
	 * Retrieves a list of categories in Kunena
	 *
	 * @param	null
	 * @return	string	A JSON string
	 **/
	private function getKunenaCategories()
	{
		require_once JPATH_ROOT . '/administrator/components/com_kunena/api.php';

		$columnName = 'parent';

		if( class_exists('KunenaForum') && KunenaForum::version() >= '2.0' )
		{
			$columnName = 'parent_id';
		}

		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__kunena_categories' ) . ' '
				. 'ORDER BY ' . $db->nameQuote( $columnName ) . ' ASC';
		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		if( !$result )
		{
			return false;
		}

		return $result;
	}

	/**
	 * Retrieves a list of categories in Community Polls
	 *
	 * @param	null
	 * @return	string	A JSON string
	 **/
	private function getCPCategories()
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__jcp_categories' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'parent_id' ) . ' > ' . $db->Quote( 0 ) . ' '
				. 'ORDER BY ' . $db->nameQuote( 'title' ) . ' ASC';

		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		if( !$result )
		{
			return false;
		}

		return $result;
	}
}
