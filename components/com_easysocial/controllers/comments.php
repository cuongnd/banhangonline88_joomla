<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2012 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* @author       Jason Rey <jasonrey@stackideas.com>
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

FD::import( 'site:/controllers/controller' );

class EasySocialControllerComments extends EasySocialController
{
    /**
     * Allows caller to save a comment.
     *
     * @since   1.0
     * @access  public
     */
    public function save()
    {
        // Check for request forgeries.
        FD::checkToken();

        // Only registered users are allowed here.
        FD::requireLogin();

        // Get the view
        $view = $this->getCurrentView();

        // Get the current logged in user
        $my = FD::user();

        // Check for permission first
        $access = FD::access();

        if (!$access->allowed('comments.add')) {
            return $view->call( __FUNCTION__ );
        }

        $element = $this->input->get('element', '', 'string');
        $group = $this->input->get('group', '', 'string');
        $verb = $this->input->get('verb', '', 'string');
        $uid = $this->input->get('uid', 0, 'int');

        $input = JRequest::getVar( 'input', '' , 'post' , 'none' , JREQUEST_ALLOWRAW );
        $data = JRequest::getVar( 'data', array() );
        $streamid = JRequest::getVar( 'streamid', '' );
        $parent = JRequest::getInt( 'parent', 0 );

        $compositeElement = $element . '.' . $group . '.' . $verb;

        $table = FD::table('comments');

        $table->element = $compositeElement;
        $table->uid = $uid;
        $table->comment = $input;
        $table->created_by = FD::user()->id;
        $table->created = FD::date()->toSQL();
        $table->parent = $parent;
        $table->params = $data;
        $table->stream_id = $streamid;

        $state = $table->store();

        if (!$state) {
            return $view->setMessage($table->getError(), SOCIAL_MSG_ERROR);
        }

        if ($streamid) {
            $doUpdate = true;
            if ($element == 'photos') {
                $sModel = FD::model('Stream');
                $totalItem = $sModel->getStreamItemsCount($streamid);

                if ($totalItem > 1) {
                    $doUpdate = false;
                }
            }

            if ($doUpdate) {
                $stream = FD::stream();
                $stream->updateModified( $streamid );
            }
        }

        // Process mentions for this comment
        $mentions = isset($data['mentions']) && !empty($data['mentions']) ? $data['mentions'] : array();

        if ($mentions) {

            // Get the permalink to the comments
            $permalink  = $table->getPermalink();

            foreach ($mentions as $row) {

                $mention = json_decode($row);

                $tag = FD::table('Tag');
                $tag->offset = $mention->start;
                $tag->length = $mention->length;
                $tag->type = $mention->type;

                if ($tag->type == 'hashtag') {
                    $tag->title = $mention->value;
                }

                // Name tagging
                if ($tag->type == 'entity') {
                    
                    $parts = explode(':', $mention->value);

                    if (count($parts) != 2) {
                        continue;
                    }
                    
                    $entityType = $parts[0];
                    $entityId = $parts[1];

                    // Do not allow tagging to happen if they are not friends
                    $tag->item_id = $entityId;
                    $tag->item_type = $entityType;
                }

                $tag->creator_id = $my->id;
                $tag->creator_type = SOCIAL_TYPE_USER;

                $tag->target_id = $table->id;
                $tag->target_type = 'comments';

                $tag->store();

                if ($tag->type == 'entity') {

                    // Notify recipients that they are mentioned in a comment
                    $emailOptions   = array(
                        'title'         => 'COM_EASYSOCIAL_EMAILS_USER_MENTIONED_YOU_IN_A_COMMENT_SUBJECT',
                        'template'      => 'site/comments/mentions',
                        'permalink'     => $permalink,
                        'actor'         => $my->getName(),
                        'actorAvatar'   => $my->getAvatar(SOCIAL_AVATAR_SQUARE),
                        'actorLink'     => $my->getPermalink(false, true),
                        'message'       => $table->comment
                    );

                    $systemOptions  = array(
                        'uid'           => $table->stream_id,
                        'context_type'  => 'comments.user.tagged',
                        'context_ids'   => $table->id,
                        'type'          => 'comments',
                        'url'           => $permalink,
                        'actor_id'      => $my->id,
                        'target_id'     => $tag->item_id,
                        'aggregate'     => false,
                        'content'       => $table->comment
                    );

                    // Send notification to the target
                    $state = FD::notify('comments.tagged', array($tag->item_id), $emailOptions, $systemOptions);
                }
            }
        }

        $dispatcher = FD::dispatcher();

        $comments = array(&$table);
        $args = array( &$comments );

        // @trigger: onPrepareComments
        $dispatcher->trigger($group, 'onPrepareComments', $args);

        return $view->call(__FUNCTION__, $table);
    }

    public function update()
    {
        // Check for request forgeries.
        FD::checkToken();

        // Only registered users are allowed here.
        FD::requireLogin();

        // Get the view
        $view = FD::view('comments', false);

        // Check for permission first
        $access = FD::access();

        $id = JRequest::getInt('id', 0);

        $table = FD::table( 'comments' );
        $state = $table->load( $id );

        if (!$state) {
            $view->setMessage($table->getError(), SOCIAL_MSG_ERROR);
            return $view->call(__FUNCTION__);
        }

        if (!($access->allowed('comments.edit') || ($access->allowed('comments.editown') && $table->isAuthor()))) {
            $view->setMessage(JText::_('COM_EASYSOCIAL_COMMENTS_NOT_ALLOWED_TO_EDIT') , SOCIAL_MSG_ERROR);
            return $view->call(__FUNCTION__);
        }

        $input = JRequest::getVar('input', null, 'POST', 'none', JREQUEST_ALLOWRAW);

        $mentions = FD::input()->get('mentions', '', 'var');

        $newData = array(
            'comment' => $input
        );

        $state = $table->update($newData);

        if (!$state) {
            $view->setMessage($table->getError(), SOCIAL_MSG_ERROR);
        }

        // Get existing tags and cross check
        $existingTags = FD::model('tags')->getTags($table->id, 'comments');

        // Store the currently used tags id in order to cross reference and delete from $existingTags later
        $usedTags = array();

        if (!empty($mentions)) {

            $my = FD::user();

            // Get the permalink to the comments
            $permalink = $table->getPermalink();

            foreach ($mentions as $row) {

                $mention = (object) $row;

                $tag = FD::table('Tag');

                $state = false;

                // Try to load existing tag first first
                if ($mention->type === 'entity') {
                    list($entityType, $entityId) = explode(':', $mention->value);

                    $state = $tag->load(array(
                        'offset' => $mention->start,
                        'length' => $mention->length,
                        'type' => $mention->type,
                        'target_id' => $table->id,
                        'target_type' => 'comments',
                        'item_type' => $entityType,
                        'item_id' => $entityId
                    ));

                    if (!$state) {
                        $tag->item_id = $entityId;
                        $tag->item_type = $entityType;
                    }
                }

                if ($mention->type === 'hashtag') {
                    $state = $tag->load(array(
                        'offset' => $mention->start,
                        'length' => $mention->length,
                        'type' => $mention->type,
                        'target_id' => $table->id,
                        'target_type' => 'comments',
                        'title' => $mention->value
                    ));

                    if (!$state) {
                        $tag->title = $mention->value;
                    }
                }

                // If state is false, means this is a new tag
                $isNew = !$state;

                // Only assign this properties if it is a new tag
                if ($isNew) {
                    $tag->offset = $mention->start;
                    $tag->length = $mention->length;
                    $tag->type = $mention->type;
                    $tag->target_id = $table->id;
                    $tag->target_type = 'comments';
                }

                // If this is not a new tag, then we store the id into $usedTags
                if (!$isNew) {
                    $usedTags[] = $tag->id;
                }

                // Regardless of new or old, we reassign the creator because it might be the admin editing the comment
                $tag->creator_id = $my->id;
                $tag->creator_type = SOCIAL_TYPE_USER;

                $tag->store();

                if ($isNew) {
                    if ($tag->type == 'entity') {
                        // Notify recipients that they are mentioned in a comment
                        $emailOptions = array(
                            'title'         => 'COM_EASYSOCIAL_EMAILS_USER_MENTIONED_YOU_IN_A_COMMENT_SUBJECT',
                            'template'      => 'site/comments/mentions',
                            'permalink'     => $permalink,
                            'actor'         => $my->getName(),
                            'actorAvatar'   => $my->getAvatar(SOCIAL_AVATAR_SQUARE),
                            'actorLink'     => $my->getPermalink(false, true),
                            'message'       => $table->comment
                        );

                        $systemOptions = array(
                            'uid'           => $table->stream_id,
                            'context_type'  => 'comments.user.tagged',
                            'context_ids'   => $table->id,
                            'type'          => 'comments',
                            'url'           => $permalink,
                            'actor_id'      => $my->id,
                            'target_id'     => $tag->item_id,
                            'aggregate'     => false,
                            'content'       => $table->comment
                        );

                        // Send notification to the target
                        FD::notify('comments.tagged', array($tag->item_id), $emailOptions, $systemOptions);
                    }
                }
            }
        }

        // Now we do a tag clean up to ensure tags that are not in used are deleted properly
        foreach ($existingTags as $existingTag) {
            if (!in_array($existingTag->id, $usedTags)) {
                $existingTag->delete();
            }
        }

        $view->call(__FUNCTION__, $table);
    }

    public function load()
    {
        // Check for request forgeries.
        FD::checkToken();

        // Only registered users are allowed here.
        FD::requireLogin();

        // Get the view
        $view = FD::view( 'comments', false );

        // Check for permission first
        $access = FD::access();

        if( !$access->allowed( 'comments.read' ) )
        {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_COMMENTS_NOT_ALLOWED_TO_READ' ) , SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__ );
        }

        $element    = JRequest::getString( 'element', '' );
        $group      = JRequest::getString( 'group', SOCIAL_APPS_GROUP_USER );
        $verb       = JRequest::getString( 'verb', 'null' );
        $uid        = JRequest::getInt( 'uid', 0 );
        $start      = JRequest::getInt( 'start', '' );
        $limit      = JRequest::getInt( 'length', '' );
        $parent     = JRequest::getInt( 'parent', 0 );

        $compositeElement = $element . '.' . $group . '.' . $verb;

        $options    = array( 'element' => $compositeElement, 'uid' => $uid, 'start' => $start, 'limit' => $limit, 'parentid' => $parent );

        $model      = FD::model( 'comments' );

        $comments   = $model->getComments( $options );

        if( !$comments )
        {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_COMMENTS_ERROR_RETRIEVING_COMMENTS' ) , SOCIAL_MSG_ERROR );
        }

        $view->call( __FUNCTION__, $comments );
    }

    public function delete()
    {
        // Check for request forgeries.
        FD::checkToken();

        // Only registered users are allowed here.
        FD::requireLogin();

        // Get the view
        $view = FD::view( 'comments', false );

        // Check for permission first
        $access = FD::access();

        // Get the comment id
        $id     = JRequest::getInt( 'id', 0 );

        // Get the current logged in user
        $my     = FD::user();

        // Load the comment object
        $table  = FD::table( 'comments' );
        $state = $table->load( $id );

        if (!$state) {
            $view->setMessage( $table->getError(), SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__ );
        }

        // There are cases where the app may need to allow the user to delete the comments.
        $apps   = FD::apps();
        $apps->load(SOCIAL_TYPE_USER);

        $args           = array(&$table, &$my);
        $dispatcher     = FD::dispatcher();
        $allowed        = $dispatcher->trigger(SOCIAL_TYPE_USER, 'canDeleteComment', $args);

        if ($my->isSiteAdmin() || $access->allowed('comments.delete') || ($table->isAuthor() && $access->allowed('comments.deleteown')) || in_array(true, $allowed)) {

            $state  = $table->delete();

            if (!$state) {
                $view->setMessage( $table->getError(), SOCIAL_MSG_ERROR );
            }

        } else {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_COMMENTS_NOT_ALLOWED_TO_DELETE' ) , SOCIAL_MSG_ERROR );
        }

        return $view->call( __FUNCTION__ );
    }

    public function like()
    {
        // Check for request forgeries.
        FD::checkToken();

        // Only registered users are allowed here.
        FD::requireLogin();

        // Check permission
        $access = FD::access();

        $id = JRequest::getInt( 'id', 0 );

        $table = FD::table( 'comments' );
        $table->load( $id );

        $likes = $table->like();

        $view = FD::view( 'comments', false );

        if( $likes === false )
        {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_COMMENTS_NOT_ABLE_TO_LIKE' ) , SOCIAL_MSG_ERROR );
        }

        $view->call( __FUNCTION__, $likes );
    }

    public function likedUsers()
    {
        // Check for request forgeries.
        FD::checkToken();

        // Only registered users are allowed here.
        FD::requireLogin();

        // Check permission
        $access = FD::access();

        $id = JRequest::getInt( 'id', 0 );

        $likes = FD::likes( $id, 'comments', 'like', SOCIAL_APPS_GROUP_USER );

        $html = $likes->getLikedUsersDialog();

        $view = FD::view( 'comments', false );

        $view->call( __FUNCTION__, $html );
    }

    public function likesText()
    {
        // Check for request forgeries.
        FD::checkToken();

        // Only registered users are allowed here.
        FD::requireLogin();

        // Check permission
        $access = FD::access();

        $id = JRequest::getInt( 'id', 0 );

        $likes = FD::likes( $id, 'comments', 'like', SOCIAL_APPS_GROUP_USER );

        $string = $likes->toHTML();

        $view = FD::view( 'comments', false );

        if( !$state )
        {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_COMMENTS_NOT_ABLE_TO_LIKE' ) , SOCIAL_MSG_ERROR );
        }

        $view->call( __FUNCTION__, $string );
    }

    public function getUpdates()
    {
        $data = JRequest::getVar( 'data', '' );

        // Data comes in with the format of:
        // {
        //  "stream.user": {
        //      "1": {
        //          "total": 10,
        //          "count": 3,
        //          "ids": ["7", "8", "9"]
        //      }
        //  }
        // }

        // var_dump($data);

        $newData    = array();
        $model      = FD::model( 'comments' );

        $updateLimit = FD::config()->get( 'comments.limit', 10 );

        foreach( $data as $key => $blocks )
        {
            $newData[$key] = array();

            foreach( $blocks as $bkeys => $block )
            {
                $tmp = explode('.', $bkeys);
                $streamid = $tmp['0'];
                $uid = isset($tmp['1']) ? $tmp['1'] : '';

                // Construct mandatory options
                $options = array( 'element' => $key, 'limit' => 0, 'parentid' => 0 );

                if ($streamid) {
                    $options['stream_id'] = $streamid;
                }

                if ($uid) {
                    $options['uid'] = $uid;
                }

                $newData[$key][$bkeys] = array(
                    'total' => 0,
                    'count' => 0,
                    'ids'   => array()
                );

                $total  = $block['total'];
                $count  = $block['count'];

                // Get the new total value
                $newData[$key][$bkeys]['total'] = $model->getCommentCount( $options );

                // ids could be non-existent if the passed in array is empty
                $ids    = array();

                if( array_key_exists( 'ids', $block ) && is_array( $block['ids'] ) )
                {
                    $ids = $block['ids'];
                }

                // Limit the count value. Count value that is too large should not proceed because there might be too many comments to check
                if( $count > $updateLimit )
                {
                    $options['start'] = $newData[$key][$bkeys]['total'] - $updateLimit;
                    $options['limit'] = $updateLimit;

                    $ids = array_slice( $ids, -$updateLimit );
                }

                // incoming count != incoming total and ids is not empty, means there are existing comments, then only pull existing comments to check
                // incoming count == incoming total, then get all the comments to check
                if( $count != $total && !empty( $ids ) )
                {
                    $options['commentid'] = $ids[0];
                }

                // Get the comments
                $comments = $model->getComments( $options );

                // Assign the new count value
                $newData[$key][$bkeys]['count'] = count( $comments );

                // Create an array to keep a copy of the ids
                $newIds = array();

                // Check for newly inserted comments
                foreach( $comments as $comment )
                {
                    // Keep a copy of the ids for integrity check later
                    $newIds[] = $comment->id;

                    // If newId is not in the list of ids, means it is a new comment
                    if( !in_array( $comment->id , $ids ) )
                    {
                        $newData[$key][$bkeys]['ids'][$comment->id] = $comment->renderHTML();
                    }
                }

                // If there are existing comments, check for integrity
                if( !empty( $ids ) )
                {
                    foreach( $ids as $id )
                    {
                        $newData[$key][$bkeys]['ids'][$id] = true;

                        // If the id no longer exist, mark for deletion
                        if( !in_array( $id, $newIds ) )
                        {
                            $newData[$key][$bkeys]['ids'][$id] = false;
                        }
                    }
                }
            }
        }

        FD::view( 'comments', false )->call( __FUNCTION__, $newData );
    }

    public function getRawComment()
    {
        // Check for request forgeries.
        FD::checkToken();

        // Only registered users are allowed here.
        FD::requireLogin();

        // Get the view
        $view = FD::view( 'comments', false );

        // Check for permission first
        $access = FD::access();

        if( !$access->allowed( 'comments.read' ) )
        {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_COMMENTS_NOT_ALLOWED_TO_READ' ) , SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__ );
        }

        $id = JRequest::getInt( 'id', 0 );

        $table = FD::table( 'comments' );

        $state = $table->load( $id );

        if( !$state )
        {
            $view->setMessage( $table->getError(), SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__ );
        }

        $comment = $table->comment;

        // IMPORTANT:
        // No escaping required here because JS side is doing .val to set the value, and .val is safe from xss

        $view->call( __FUNCTION__, $comment );
    }

    public function getReplies()
    {
        FD::checkToken();

        $view = $this->getCurrentView();

        // Check for permission first
        $access = FD::access();

        if( !$access->allowed( 'comments.read' ) )
        {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_COMMENTS_NOT_ALLOWED_TO_READ' ) , SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__ );
        }

        $id = JRequest::getInt( 'id', 0 );

        if( empty( $id ) )
        {
            return $view->call( __FUNCTION__, array() );
        }

        $start      = JRequest::getInt( 'start', '' );
        $limit      = JRequest::getInt( 'length', '' );

        $model = FD::model( 'comments' );

        $replies = $model->getComments( array( 'parentid' => $id, 'start' => $start, 'limit' => $limit ) );

        $view->call( __FUNCTION__, $replies );
    }

    public function getEditComment()
    {
        $id = FD::input()->getInt('id');

        $comment = FD::table('comments');
        $comment->load($id);

        $tags = FD::model('Tags')->getTags($id, 'comments');

        $overlay = $comment->comment;

        $counter = 0;
        $tmp = array();

        foreach ($tags as $tag) {
            if ($tag->type === 'entity' && $tag->item_type === SOCIAL_TYPE_USER) {
                $user = FD::user($tag->item_id);
                $replace    = '<span data-value="user:' . $tag->item_id . '" data-type="entity">' . $user->getName() . '</span>';
            }

            if ($tag->type === 'hashtag') {
                $replace    = '<span data-value="' . $tag->title . '" data-type="hashtag">' . "#" . $tag->title . '</span>';
            }

            $tmp[$counter] = $replace;

            $replace = '[si:mentions]' . $counter . '[/si:mentions]';

            $overlay = JString::substr_replace($overlay, $replace, $tag->offset, $tag->length);

            $counter++;
        }

        $overlay = FD::string()->escape($overlay);

        foreach ($tmp as $i => $v) {
            $overlay = str_ireplace('[si:mentions]' . $i . '[/si:mentions]', $v, $overlay);
        }

        $theme = FD::themes();
        $theme->set('comment', $comment->comment);
        $theme->set('overlay', $overlay);

        $contents = $theme->output('site/comments/editForm');

        $this->getCurrentView()->call(__FUNCTION__, $contents);
    }
}
