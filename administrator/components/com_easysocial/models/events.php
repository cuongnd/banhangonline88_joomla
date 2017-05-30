<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

FD::import('admin:/includes/model');

class EasySocialModelEvents extends EasySocialModel
{
    public function __construct($config = array())
    {
        parent::__construct('events', $config);
    }

    public function initStates()
    {
        // Direction, search, limit, limitstart is handled by parent::initStates();
        parent::initStates();

        // Override ordering default value
        $ordering = $this->getUserStateFromRequest('ordering', 'a.id');
        $this->setState('ordering', $ordering);

        // Init other parameters
        $type = $this->getUserStateFromRequest('type', 'all');
        $state = $this->getUserStateFromRequest('state', 'all');

        $this->setState('type', $type);
        $this->setState('state', $state);
    }

    /**
     * Returns array of SocialEvent object for backend listing.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     * @return array    Array of SocialEvent object.
     */
    public function getItems()
    {
        $db = FD::db();
        $sql = $db->sql();

        $sql->select('#__social_clusters', 'a');
        $sql->column('a.id');

        $search = $this->getState('search');

        if (!empty($search)) {
            $sql->where('a.title', '%' . $search . '%', 'LIKE');
        }

        $state = $this->getState('state');
        if ($state !== 'all') {
            $sql->where('a.state', $state);
        }

        $type = $this->getState('type');
        if ($type !== 'all') {
            $sql->where('a.type', $type);
        }

        $sql->order($this->getState('ordering'), $this->getState('direction'));

        $sql->leftjoin('#__social_clusters_categories', 'b');
        $sql->on('a.category_id', 'b.id');

        $sql->where('a.cluster_type', SOCIAL_TYPE_EVENT);

        $this->setTotal($sql->getTotalSql());

        $result = $this->getDataColumn($sql->getSql());

        if (empty($result)) {
            return array();
        }

        // Result is an array of ids, we directly use this instead of looping through the result to bind to SocialEvent object since FD::event() is array-ids-ready
        $events = FD::event($result);

        return $events;
    }

    /**
     * Returns array of SocialEvent object for frontend listing.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     * @param  array    $options    Array of options.
     * @return array                Array of SocialEvent object.
     */
    public function getEvents($options = array())
    {
        $db = FD::db();

        $q = array();

        // $sql->select('#__social_clusters', 'a');
        // $sql->column('a.id', 'id', 'distinct');

        if (!empty($options['location'])) {
            // If this is a location based search, then we want to include distance column
            $searchUnit = strtoupper(FD::config()->get('general.location.proximity.unit','mile'));

            $unit = constant('SOCIAL_LOCATION_UNIT_' . $searchUnit);
            $radius = constant('SOCIAL_LOCATION_RADIUS_' . $searchUnit);

            $lat = $options['latitude'];
            $lng = $options['longitude'];

            // ($radius * acos(cos(radians($lat)) * cos(radians(latitude)) * cos(radians(longitude) - radians($lng)) + sin(radians($lat)) * sin(radians(latitude)))) as distance

            // If there is a distance provided, then we need to put the distance column into a subquery in order to filter condition on it
            if (!empty($options['distance'])) {
                $distance = $options['distance'];

                $lat1 = $lat - ($distance / $unit);
                $lat2 = $lat + ($distance / $unit);

                $lng1 = $lng - ($distance / abs(cos(deg2rad($lat)) * $unit));
                $lng2 = $lng + ($distance / abs(cos(deg2rad($lat)) * $unit));

                $q[] = "SELECT `a`.`id`, `a`.`distance` FROM (
                    SELECT `x`.*, ($radius * acos(cos(radians($lat)) * cos(radians(`x`.`latitude`)) * cos(radians(`x`.`longitude`) - radians($lng)) + sin(radians($lat)) * sin(radians(`x`.`latitude`)))) AS `distance` FROM `#__social_clusters` AS `x` WHERE `x`.`cluster_type` = " . $db->q(SOCIAL_TYPE_EVENT) . " AND (cast(`x`.`latitude` AS DECIMAL(10, 6)) BETWEEN $lat1 AND $lat2) AND (cast(`x`.`longitude` AS DECIMAL(10, 6)) BETWEEN $lng1 AND $lng2)
                ) AS `a`";
            } else {
                $q[] = "SELECT DISTINCT `a`.`id`, ($radius * acos(cos(radians($lat)) * cos(radians(`a`.`latitude`)) * cos(radians(`a`.`longitude`) - radians($lng)) + sin(radians($lat)) * sin(radians(`a`.`latitude`)))) AS `distance` FROM `#__social_clusters` AS `a`";
            }
        } else {
            $q[] = "SELECT DISTINCT `a`.`id` AS `id` FROM `#__social_clusters` AS `a`";
        }

        // $sql->leftjoin('#__social_events_meta', 'b');
        // $sql->on('a.id', 'b.cluster_id');

        $q[] = "LEFT JOIN `#__social_events_meta` AS `b` ON `a`.`id` = `b`.`cluster_id`";

        if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {

            // $sql->leftjoin( '#__social_block_users' , 'bus');
            // $sql->on( 'a.creator_uid' , 'bus.user_id' );
            // $sql->on( 'bus.target_id', JFactory::getUser()->id );
            // $sql->isnull('bus.id');

            $q[] = "LEFT JOIN `#__social_block_users` AS `bus`";
            $q[] = "ON `a`.`creator_uid` = `bus`.`user_id`";
            $q[] = "AND `bus`.`target_id` = '" . JFactory::getUser()->id . "'";
            $q[] = "AND `bus`.`id` IS NULL";
        }

        if (isset($options['guestuid'])) {
            // $sql->leftjoin('#__social_clusters_nodes', 'c');
            // $sql->on('a.id', 'c.cluster_id');

            $q[] = "LEFT JOIN `#__social_clusters_nodes` AS `c`";
            $q[] = "ON `a`.`id` = `c`.`cluster_id`";
        }

        // $sql->where('a.cluster_type', SOCIAL_TYPE_EVENT);

        $q[] = "WHERE `a`.`cluster_type` = " . $db->q(SOCIAL_TYPE_EVENT);

        // Filter by event type
        if (isset($options['type']) && $options['type'] !== 'all') {
            if (is_array($options['type'])) {
                if (count($options['type']) === 1) {
                    // $sql->where('a.type', $options['type'][0]);

                    $q[] = "AND `a`.`type` = " . $db->q($options['type'][0]);
                } else {
                    // $sql->where('a.type', $options['type'], 'IN');

                    $q[] = "AND `a`.`type` IN (" . implode(',', $db->q($options['type'])) . ")";
                }
            } else {
                // $sql->where('a.type', $options['type']);

                $q[] = "AND `a`.`type` = " . $db->q($options['type']);
            }
        }

        // Filter by category id
        if (isset($options['category']) && $options['category'] !== 'all') {
            // $sql->where('a.category_id', $options['category']);
            $q[] = "AND `a`.`category_id` = " . $db->q($options['category']);
        }

        // Filter by featured
        if (isset($options['featured']) && $options['featured'] !== 'all') {
            // $sql->where('a.featured', (int) $options['featured']);

            $q[] = "AND `a`.`featured` = " . $db->q((int) $options['featured']);
        }

        // Filter by creator
        if (isset($options['creator_uid'])) {
            // $sql->where('a.creator_uid', $options['creator_uid']);

            // $sql->where('a.creator_type', isset($options['creator_type']) ? $options['creator_type'] : SOCIAL_TYPE_USER);

            $q[] = "AND `a`.`creator_uid` = " . $db->q($options['creator_uid']);
            $q[] = "AND `a`.`creator_type` = " . $db->q(isset($options['creator_type']) ? $options['creator_type'] : SOCIAL_TYPE_USER);
        }

        // Filter by state
        if (isset($options['state'])) {
            // $sql->where('a.state', $options['state']);

            $q[] = "AND `a`.`state` = " . $db->q($options['state']);
        }

        // Filter by guest state
        if (isset($options['guestuid'])) {
            // $sql->where('c.uid', $options['guestuid']);

            $q[] = "AND `c`.`uid` = " . $db->q($options['guestuid']);

            if (isset($options['gueststate']) && $options['gueststate'] !== 'all') {
                // $sql->where('c.state', $options['gueststate']);

                $q[] = "AND `c`.`state` = " . $db->q($options['gueststate']);
            }
        }

        // Time filter
        // Filter by past, ongoing, or upcoming
        $now = FD::date()->toSql();
        if (!empty($options['past'])) {
            // $sql->where('b.end', $now, '<=');

            $q[] = "AND `b`.`end` <= " . $db->q($now);
        }
        if (!empty($options['ongoing'])) {
            // $sql->where('b.start', $now, '<=');
            // $sql->where('b.end', $now, '>=');

            $q[] = "AND `b`.`start` <= " . $db->q($now);
            $q[] = "AND `b`.`end` >= " . $db->q($now);
        }
        if (!empty($options['upcoming'])) {
            // $sql->where('b.start', $now, '>=');

            $q[] = "AND `b`.`start` >= " . $db->q($now);
        }

        // Manual filter by start and end range
        if (!empty($options['start-before'])) {
            // $sql->where('b.start', $options['start-before'], '<=');
            $q[] = "AND `b`.`start` <= " . $db->q($options['start-before']);
        }
        if (!empty($options['start-after'])) {
            // $sql->where('b.start', $options['start-after'], '>=');
            $q[] = "AND `b`.`start` >= " . $db->q($options['start-after']);
        }
        if (!empty($options['end-before'])) {
            // $sql->where('b.end', $options['end-before'], '<=');
            $q[] = "AND `b`.`end` <= " . $db->q($options['end-before']);
        }
        if (!empty($options['end-after'])) {
            // $sql->where('b.end', $options['end-after'], '>=');
            $q[] = "AND `b`.`end` >= " . $db->q($options['end-after']);
        }

        // Nearby filter
        if (!empty($options['location']) && !empty($options['distance'])) {
            $range = isset($options['range']) ? $options['range'] : '<=';
            $q[] = "AND `a`.`distance` $range " . (float) $options['distance'];
        }

        // Group event filter
        if (isset($options['group_id']) && $options['group_id'] !== 'all') {
                $q[] = "AND `b`.`group_id` = " . $db->q($options['group_id']);
        }
        // If no group_id set, then we check against the settings
        // By default we do not want group event in listing
        // If settings state to NOT include group events, then we have to filter by group_id = 0
        if (!isset($options['group_id']) && !FD::config()->get('events.listing.includegroup', false)) {
            $q[] = "AND `b`.`group_id` = " . $db->q(0);
        }

        // Conditions ends here
        // We set the total here first before going into order and limit block
        $sql = $db->sql();
        $sql->raw(implode(' ', $q));
        $this->setTotal($sql->getSql(), true);

        // Ordering
        if (isset($options['ordering'])) {
            $direction = isset($options['direction']) ? $options['direction'] : 'asc';

            switch ($options['ordering']) {
                case 'created':
                    // $sql->order('a.created', $direction);

                    $q[] = "ORDER BY `a`.`created` $direction";
                break;

                default:
                case 'start':
                    // $sql->order('b.start', $direction);

                    $q[] = "ORDER BY `b`.`start` $direction";
                break;

                case 'end':
                    // $sql->order('b.end', $direction);

                    $q[] = "ORDER BY `b`.`end` $direction";
                break;

                case 'distance':
                    $q[] = "ORDER BY `a`.`distance` $direction";
                break;
            }
        }

        // Limit
        if (isset($options['limit'])) {
            $limit = $options['limit'];
            $limitstart = isset($options['limitstart']) ? $options['limitstart'] : JRequest::getInt('limitstart', 0);

            $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

            $this->setState('limit', $limit);
            $this->setState('limitstart', $limitstart);

            // $sql->limit($limitstart, $limit);

            $q[] = "LIMIT $limitstart, $limit";
        }

        $query = implode(' ', $q);

        $sql = $db->sql();
        $sql->raw($query);

        $db->setQuery($sql);

        $result = $db->loadObjectList('id');

        if (empty($result)) {
            return array();
        }

        $ids = array_keys($result);

        // Support for lightweight mode where we only want the ids
        if (isset($options['idonly']) && $options['idonly'] === true) {
            return $ids;
        }

        // FD::event() is array-ids-ready
        $events = FD::event($ids);

        // Manually assign the distance data
        if (!empty($options['location'])) {
            foreach ($events as $event) {
                $event->distance = round($result[$event->id]->distance, 1);
            }
        }

        return $events;
    }

    /**
     * Returns total number of event based on options filtering for frontend listing.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     * @param  array     $options Options to filter.
     * @return integer            Total number of event.
     */
    public function getTotalEvents($options = array())
    {
        $db = FD::db();
        $sql = $db->sql();

        $sql->select('#__social_clusters', 'a');
        $sql->column('a.id', 'id', 'count distinct');

        $sql->leftjoin('#__social_events_meta', 'b');
        $sql->on('a.id', 'b.cluster_id');

        if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
            $sql->leftjoin( '#__social_block_users' , 'bus');
            $sql->on( 'a.creator_uid' , 'bus.user_id' );
            $sql->on( 'bus.target_id', JFactory::getUser()->id );
            $sql->isnull('bus.id');
        }

        $sql->where('a.cluster_type', SOCIAL_TYPE_EVENT);

        // Filter by event type
        if (isset($options['type']) && $options['type'] !== 'all') {

            if (is_array($options['type'])) {
                if (count($options['type']) === 1) {
                    $sql->where('a.type', $options['type'][0]);
                } else {
                    $sql->where('a.type', $options['type'], 'IN');
                }
            } else {
                $sql->where('a.type', $options['type']);
            }
        }

        // Filter by category id
        if (isset($options['category']) && $options['category'] !== 'all') {
            $sql->where('a.category_id', $options['category']);
        }

        // Filter by featured
        if (isset($options['featured'])) {
            $sql->where('a.featured', (int) $options['featured']);
        }

        // Filter by creator
        if (isset($options['creator_uid'])) {
            $sql->where('a.creator_uid', $options['creator_uid']);

            $sql->where('a.creator_type', isset($options['creator_type']) ? $options['creator_type'] : SOCIAL_TYPE_USER);
        }

        // Filter by state
        if (isset($options['state'])) {
            $sql->where('a.state', $options['state']);
        }

        // Filter by guest state
        if (isset($options['guestuid'])) {
            $sql->leftjoin('#__social_clusters_nodes', 'c');
            $sql->on('a.id', 'c.cluster_id');

            $sql->where('c.uid', $options['guestuid']);

            if (isset($options['gueststate']) && $options['gueststate'] !== 'all') {
                $sql->where('c.state', $options['gueststate']);
            }
        }

        // Time filter
        // Filter by past, ongoing, or upcoming
        if (!empty($options['past'])) {
            $now = FD::date()->toSql();
            $sql->where('b.end', $now, '<=');
        }
        if (!empty($options['ongoing'])) {
            $now = FD::date()->toSql();
            $sql->where('b.start', $now, '<=');
            $sql->where('b.end', $now, '>=');
        }
        if (!empty($options['upcoming'])) {
            $now = FD::date()->toSql();
            $sql->where('b.start', $now, '>=');
        }

        // Manual filter by start and end range
        if (!empty($options['start-before'])) {
            $sql->where('b.start', $options['start-before'], '<=');
        }
        if (!empty($options['start-after'])) {
            $sql->where('b.start', $options['start-after'], '>=');
        }
        if (!empty($options['end-before'])) {
            $sql->where('b.end', $options['end-before'], '<=');
        }
        if (!empty($options['end-after'])) {
            $sql->where('b.end', $options['end-after'], '>=');
        }

        // Group event filter
        // If no group_id set, then we check against the settings
        // By default we do not want group event in listing
        // If settings state to NOT include group events, then we have to filter by group_id = 0
        if (!isset($options['group_id']) && !FD::config()->get('events.listing.includegroup', false)) {
            $sql->where('b.group_id', 0);
        }
        // If there is group id specified, then we filter by group id
        if (isset($options['group_id']) && $options['group_id'] !== 'all') {
            $sql->where('b.group_id', $options['group_id']);
        }

        $db->setQuery($sql);

        $result = $db->loadResult();

        return (int) $result;
    }

    /**
     * Returns the total pending events for backend.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     * @return integer    Number of pending events.
     */
    public function getPendingCount()
    {
        $db = FD::db();
        $sql = $db->sql();

        $sql->select('#__social_clusters');
        $sql->where('cluster_type', SOCIAL_TYPE_EVENT);
        $sql->where('state', SOCIAL_CLUSTER_PENDING);

        $db->setQuery($sql->getTotalSql());

        $result = $db->loadResult();

        return (int) $result;
    }

    /**
     * Main function that initiates the required event's meta data.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     * @param  array     $ids The event ids to load.
     * @return array          Array of event meta datas.
     */
    public function getMeta($ids = array())
    {
        static $loaded = array();

        $loadItems = array();

        foreach ($ids as $id) {
            $id = (int) $id;

            if (!isset($loaded[$id])) {
                $loadItems[] = $id;

                $loaded[$id] = false;
            }
        }

        if (!empty($loadItems)) {
            $db = FD::db();
            $sql = $db->sql();

            $sql->select('#__social_clusters', 'a');
            $sql->column('a.*');
            $sql->column('b.small');
            $sql->column('b.medium');
            $sql->column('b.large');
            $sql->column('b.square');
            $sql->column('b.avatar_id');
            $sql->column('b.photo_id');
            $sql->column('b.storage', 'avatarStorage');
            $sql->column('c.id', 'cover_id');
            $sql->column('c.uid', 'cover_uid');
            $sql->column('c.type', 'cover_type');
            $sql->column('c.photo_id', 'cover_photo_id');
            $sql->column('c.cover_id', 'cover_cover_id');
            $sql->column('c.x', 'cover_x');
            $sql->column('c.y', 'cover_y');
            $sql->column('c.modified', 'cover_modified');
            $sql->leftjoin('#__social_avatars', 'b');
            $sql->on('b.uid', 'a.id');
            $sql->on('b.type', 'a.cluster_type');
            $sql->leftjoin('#__social_covers', 'c');
            $sql->on('c.uid', 'a.id');
            $sql->on('c.type', 'a.cluster_type');

            if (count($loadItems) > 1) {
                $sql->where('a.id', $loadItems, 'IN');
            } else {
                $sql->where('a.id', $loadItems[0]);
            }

            $sql->where('a.cluster_type', SOCIAL_TYPE_EVENT);

            $db->setQuery($sql);

            $events = $db->loadObjectList('id');

            // Use array_replace instead of array_merge because the key of the array is integer, and array_merge won't replace if the key is integer.
            // array_replace is only supported php>5.3

            // $loaded = array_replace($loaded, $events);

            // While array_replace goes by base, replacement
            // Using + changes the order where base always goes last
            $loaded = $events + $loaded;
        }

        $data = array();

        foreach ($ids as $id) {
            $data[] = $loaded[$id];
        }

        return $data;
    }

    /**
     * Retrieves the total number of event guests from a particular event
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return
     */
    public function getTotalAttendees($id)
    {
        $db  = FD::db();
        $sql = $db->sql();

        $sql->select('#__social_clusters_nodes');
        $sql->column('COUNT(1)');
        $sql->where('cluster_id', $id);
        $sql->where('state', SOCIAL_EVENT_GUEST_GOING);

        $db->setQuery($sql);
        $total = $db->loadResult();

        return $total;
    }

    /**
     * Alias method of getGuests to ensure compatibility with Groups model.
     *
     * @since   1.3
     * @access  public
     * @param  integer  $id         The event id.
     * @param  array    $options    Options to filter.
     * @return array                Array of SocialTableEventGuest objects.
     */
    public function getMembers($id, $options = array())
    {
        return $this->getGuests($id, $options);
    }

    /**
     * Retrieves a list of event guests from a particular event.
     *
     * @since   1.3
     * @access  public
     * @param  integer  $id         The event id.
     * @param  array    $options    Options to filter.
     * @return array                Array of SocialTableEventGuest objects.
     */
    public function getGuests($id, $options = array())
    {
        static $cache = array();

        ksort($options);

        $optionskey = serialize($options);

        if (!isset($cache[$id][$optionskey])) {
            $db = FD::db();
            $sql = $db->sql();

            $sql->select('#__social_clusters_nodes', 'a');
            $sql->column('a.*');

            if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
                $sql->leftjoin( '#__social_block_users' , 'bus');
                $sql->on( 'a.uid' , 'bus.user_id' );
                $sql->on( 'bus.target_id', JFactory::getUser()->id );
                $sql->isnull('bus.id');
            }


            $sql->where('a.cluster_id', $id);

            if (isset($options['state'])) {
                $sql->where('a.state', $options['state']);
            }

            if (isset($options['admin'])) {
                $sql->where('a.admin', $options['admin']);
            }

            if (isset($options['exclude'])) {
                $exclude = $options['exclude'];

                if (is_array($exclude)) {
                    if (count($exlude) > 1) {
                        $sql->where('a.uid', $exclude, 'NOT IN');
                    } else {
                        $sql->where('a.uid', $exclude[0], '<>');
                    }
                } else {
                    $sql->where('a.uid', $exclude, '<>');
                }
            }

            if (isset($options['ordering'])) {
                $direction = isset($options['direction']) ? $options['direction'] : 'asc';

                $sql->order($options['ordering'], $direction);
            }

            if (isset($options['limit'])) {
                $limitstart = isset($options['limitstart']) ? $options['limitstart'] : 0;

                $sql->limit($limitstart, $options['limit']);
            }

            $db->setQuery($sql);

            $result = $db->loadObjectList();

            $cache[$id][$optionskey] = $result;
        }

        if (!empty($options['users'])) {
            $users = array();

            foreach ($cache[$id][$optionskey] as $row) {
                $user = FD::user($row->uid);

                $users[] = $user;
            }
        } else {
            $users = $this->bindTable('EventGuest', $cache[$id][$optionskey]);
        }

        return $users;
    }

    /**
     * Generates a unique alias for the group
     *
     * @since   1.3
     * @access  public
     * @param   string  $title      The title of the group.
     * @param   int     $exclude    The integer of the cluster to exclude from checking.
     * @return  string              The generated alias.
     */
    public function getUniqueAlias($title, $exclude = null)
    {
        // Pass this back to Joomla to ensure that the permalink would be safe.
        $alias = JFilterOutput::stringURLSafe($title);

        $model = FD::model('Clusters');

        $i = 2;

        // Set this to a temporary alias
        $tmp = $alias;

        do {
            $exists = $model->clusterAliasExists($alias, $exclude, SOCIAL_TYPE_EVENT);

            if ($exists) {
                $alias  = $tmp . '-' . $i++;
            }

        } while ($exists);

        return $alias;
    }

    /**
     * Creates a new event based on the session.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     * @param  SocialTableStepSession $session The step session.
     * @return SocialEvent                     The SocialEvent object.
     */
    public function createEvent(SocialTableStepSession $session)
    {
        FD::import('admin:/includes/event/event');

        $event = new SocialEvent();
        $event->creator_uid = FD::user()->id;
        $event->creator_type = SOCIAL_TYPE_USER;
        $event->category_id = $session->uid;
        $event->cluster_type = SOCIAL_TYPE_EVENT;
        $event->created = FD::date()->toSql();

        $event->key = md5(JFactory::getDate()->toSql() . FD::user()->password . uniqid());

        $params = FD::registry($session->values);

        // Support for group event
        if ($params->exists('group_id')) {
            $group = FD::group($params->get('group_id'));

            $event->setMeta('group_id', $group->id);
        }

        $data = $params->toArray();

        $customFields = FD::model('Fields')->getCustomFields(array('visible' => SOCIAL_EVENT_VIEW_REGISTRATION, 'group' => SOCIAL_TYPE_EVENT, 'uid' => $session->uid));

        $fieldsLib = FD::fields();

        $args = array(&$data, &$event);

        $callback = array($fieldsLib->getHandler(), 'beforeSave');

        $errors = $fieldsLib->trigger('onRegisterBeforeSave', SOCIAL_FIELDS_GROUP_EVENT, $customFields, $args, $callback);

        if (!empty($errors)) {
            $this->setError($errors);
            return false;
        }

        // Get the current user.
        $my = FD::user();

        $event->state = SOCIAL_CLUSTER_PENDING;

        // If the event is created by site admin or user doesn't need to be moderated, publish event immediately.
        if ($my->isSiteAdmin() || !$my->getAccess()->get('events.moderate')) {
            $event->state = SOCIAL_CLUSTER_PUBLISHED;
        }

        // Trigger apps
        FD::apps()->load(SOCIAL_TYPE_USER);

        $dispatcher  = FD::dispatcher();
        $triggerArgs = array(&$event, &$my, true);

        // @trigger: onEventBeforeSave
        $dispatcher->trigger(SOCIAL_TYPE_USER, 'onEventBeforeSave', $triggerArgs);

        $state = $event->save();

        if (!$state) {
            $this->setError($event->getError());
            return false;
        }

        // Notifies admin when a new event is created
        if ($event->state === SOCIAL_CLUSTER_PENDING || !$my->isSiteAdmin()) {
            $this->notifyAdmins($event);
        }

        // Recreate the event object
        SocialEvent::$instances[$event->id] = null;
        $event = FD::event($event->id);

        // Create a new owner object
        $event->createOwner($my->id);

        // Support for group event
        if ($event->isGroupEvent()) {
            // Check for transfer flag to insert group member as event guest
            $transferMode = isset($data['member_transfer']) ? $data['member_transfer'] : 'invite';

            if (!empty($transferMode) && $transferMode != 'none') {
                $nodeState = SOCIAL_EVENT_GUEST_INVITED;

                if ($transferMode == 'attend') {
                    $nodeState = SOCIAL_EVENT_GUEST_GOING;
                }

                /*

                insert into jos_social_clusters_nodes (cluster_id, uid, type, created, state, owner, admin, invited_by)
                select $eventId as cluster_id, uid, type, $now as created, $nodeState as state, 0 as owner, admin, $userId as invited_by from jos_social_clusters_nodes
                where cluster_id = $groupId
                and state = 1
                and type = 'user'
                and uid not in (select uid from jos_social_clusters_nodes where cluster_id = $eventId and type = 'user')

                */

                $eventId = $event->id;
                $groupId = $event->getMeta('group_id');
                $userId = $my->id;
                $now = FD::date()->toSql();

                $query = "INSERT INTO `#__social_clusters_nodes` (`cluster_id`, `uid`, `type`, `created`, `state`, `owner`, `admin`, `invited_by`) SELECT '$eventId' AS `cluster_id`, `uid`, `type`, '$now' AS `created`, '$nodeState' AS `state`, '0' AS `owner`, `admin`, '$userId' AS `invited_by` FROM `#__social_clusters_nodes` WHERE `cluster_id` = '$groupId' AND `state` = '" . SOCIAL_GROUPS_MEMBER_PUBLISHED . "' AND `type` = '" . SOCIAL_TYPE_USER . "' AND `uid` NOT IN (SELECT `uid` FROM `#__social_clusters_nodes` WHERE `cluster_id` = '$eventId' AND `type` = '" . SOCIAL_TYPE_USER . "')";

                $db = FD::db();
                $sql = $db->sql();
                $sql->raw($query);
                $db->setQuery($sql);
                $db->query();
            }
        }

        // Trigger the fields again
        $args = array(&$data, &$event);

        $fieldsLib->trigger('onRegisterAfterSave', SOCIAL_FIELDS_GROUP_EVENT, $customFields, $args);

        $event->bindCustomFields($data);

        $fieldsLib->trigger('onRegisterAfterSaveFields', SOCIAL_FIELDS_GROUP_EVENT, $customFields, $args);

        if (empty($event->alias)) {
            $event->alias = $this->getUniqueAlias($event->getName());

            $event->save();
        }

        // @trigger: onEventAfterSave
        $triggerArgs = array(&$event, &$my, true);
        $dispatcher->trigger(SOCIAL_TYPE_USER, 'onEventAfterSave' , $triggerArgs);

        return $event;
    }

    /**
     * Notifies administrator when a new event is created.
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return
     */
    public function notifyAdmins($event)
    {
        $params = array(
            'title' => $event->getName(),
            'creatorName' => $event->getCreator()->getName(),
            'creatorLink' => $event->getCreator()->getPermalink(false, true),
            'categoryTitle' => $event->getCategory()->get('title'),
            'avatar' => $event->getAvatar(SOCIAL_AVATAR_LARGE),
            'permalink' => $event->getPermalink(true, true),
            'alerts' => false
        );

        $title = JText::sprintf('COM_EASYSOCIAL_EMAILS_MODERATE_EVENT_CREATED_TITLE', $event->getName());

        $template = 'site/event/created';

        if ($event->state === SOCIAL_CLUSTER_PENDING) {
            $params['reject'] = FRoute::controller('events', array('external' => true, 'task' => 'rejectEvent', 'id' => $event->id, 'key' => $event->key));
            $params['approve'] = FRoute::controller('events', array('external' => true, 'task' => 'approveEvent', 'id' => $event->id, 'key' => $event->key));

            $template = 'site/event/moderate';
        }

        $admins = FD::model('Users')->getSiteAdmins();

        foreach ($admins as $admin) {
            if (!$admin->sendEmail) {
                continue;
            }

            $mailer = FD::mailer();

            $params['adminName'] = $admin->getName();

            // Get the email template.
            $mailTemplate = $mailer->getTemplate();

            // Set recipient
            $mailTemplate->setRecipient($admin->getName(), $admin->email);

            // Set title
            $mailTemplate->setTitle($title);

            // Set the template
            $mailTemplate->setTemplate($template, $params);

            // Set the priority. We need it to be sent out immediately since this is user registrations.
            $mailTemplate->setPriority(SOCIAL_MAILER_PRIORITY_IMMEDIATE);

            // Try to send out email to the admin now.
            $state = $mailer->create($mailTemplate);
        }
    }

    public function getFilters($eventId, $userId = null)
    {
        $db = FD::db();
        $sql = $db->sql();

        $sql->select('#__social_stream_filter');
        $sql->where('uid', $eventId);
        $sql->where('utype', SOCIAL_TYPE_EVENT);

        if (!empty($userId)) {
            $sql->where('user_id', $userId);
        }

        $db->setQuery($sql);

        $result = $db->loadObjectList();

        $filters = $this->bindTable('StreamFilter', $result);

        return $filters;
    }

    public function getFriendsInEvent($eventId, $options = array())
    {
        $db = FD::db();
        $sql = $db->sql();

        $userId = isset($options['userId']) ? $options['userId'] : FD::user()->id;

        $sql->select('#__social_clusters_nodes', 'a');
        $sql->column('a.uid', 'uid', 'distinct');
        $sql->innerjoin('#__social_friends', 'b');
        $sql->on('(');
        $sql->on('(');
        $sql->on('a.uid', 'b.actor_id');
        $sql->on('b.target_id', $userId);
        $sql->on(')');
        $sql->on('(', '', '', 'OR');
        $sql->on('a.uid', 'b.target_id');
        $sql->on('b.actor_id', $userId);
        $sql->on(')');
        $sql->on(')');
        $sql->on('b.state', SOCIAL_STATE_PUBLISHED);
        $sql->where('a.cluster_id', $eventId);

        if (isset($options['published'])) {
            $sql->where('a.state', $options['published']);
        }

        $db->setQuery($sql);
        $result = $db->loadColumn();

        $users = array();

        foreach ($result as $id) {
            $users[] = FD::user($id);
        }

        return $users;
    }

    public function getOnlineGuests($eventId)
    {
        $db = FD::db();
        $sql = $db->sql();

        // Get the session life time so we can know who is really online.
        $lifespan = FD::jConfig()->getValue('lifetime');
        $online = time() - ($lifespan * 60);

        $sql->select('#__session', 'a');
        $sql->column('b.id');
        $sql->innerjoin('#__users', 'b');
        $sql->on('a.userid', 'b.id');
        $sql->innerjoin('#__social_clusters_nodes', 'c');
        $sql->on('c.uid', 'b.id');
        $sql->on('c.type', SOCIAL_TYPE_USER);
        $sql->where('a.time', $online, '>=');
        $sql->where('b.block', 0);
        $sql->where('c.cluster_id', $eventId);
        $sql->group('a.userid');

        $db->setQuery($sql);

        $result = $db->loadColumn();

        if (!$result) {
            return array();
        }

        $users = FD::user($result);

        return $users;
    }

    /**
     * Retrieves a list of news item from a particular event
     *
     * @since   1.3
     * @access  public
     */
    public function getNews($eventId, $options = array())
    {
        $db = FD::db();
        $sql = $db->sql();

        $sql->select('#__social_clusters_news', 'a');
        $sql->where('a.cluster_id', $eventId);

        // If we should exclude specific items
        $exclude = isset($options['exclude']) ? $options['exclude'] : '';

        if ($exclude) {
            $sql->where('a.id', $exclude, 'NOT IN');
        }

        $sql->order('created', 'DESC');

        $limit = isset($options['limit']) ? $options['limit'] : '';

        if ($limit) {
            $this->setState('limit', $limit);

            // Get the limitstart.
            $limitstart = $this->getUserStateFromRequest('limitstart', 0);
            $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

            $this->setState('limitstart', $limitstart);

            // Run pagination here.
            $this->setTotal($sql->getTotalSql());

            $result = $this->getData($sql->getSql());
        } else {
            $db->setQuery($sql);
            $result = $db->loadObjectList();
        }

        $result = $db->loadObjectList();

        $news = $this->bindTable('EventNews', $result);

        return $news;
    }

    /**
     * Deletes all the child events.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     * @param  integer  $parentId   The event parent id to delete.
     * @return boolean              True if successful.
     */
    public function deleteRecurringEvents($parentId)
    {
        $db = FD::db();
        $sql = $db->sql();

        $sql->select('#__social_clusters');
        $sql->column('id');
        $sql->where('cluster_type', SOCIAL_TYPE_EVENT);
        $sql->where('parent_id', $parentId);

        $db->setQuery($sql);

        $result = $db->loadColumn();

        $ids = array();

        foreach ($result as $id) {
            $ids[] = $db->quote($id);
        }

        $ids = implode(',', $ids);

        $sql->clear();

        // Delete stream items
        $query = "DELETE `a`, `b` FROM `#__social_stream_item` AS `a` INNER JOIN `#__social_stream` AS `b` ON `a`.`uid` = `b`.`id` WHERE `b`.`cluster_id` IN ($ids)";

        $sql->raw($query);

        $db->setQuery($sql);

        $db->query();

        $sql->clear();

        // Delete notification items
        $query = "DELETE FROM `#__social_notifications` WHERE (`uid` IN ($ids) AND `type` = 'event') OR (type = 'event' AND `context_ids` IN ($ids))";
        $sql->raw($query);

        $db->setQuery($sql);

        $db->query();

        $sql->clear();

        // Delete event item
        // Delete meta
        // Delete nodes
        // Delete news items
        $query = "DELETE `a`, `b`, `c`, `d` FROM `#__social_clusters` AS `a`";
        $query .= " LEFT JOIN `#__social_clusters_nodes` AS `b` ON `a`.`id` = `b`.`cluster_id`";
        $query .= " LEFT JOIN `#__social_clusters_news` AS `c` ON `a`.`id` = `c`.`cluster_id`";
        $query .= " LEFT JOIN `#__social_events_meta` AS `d` ON `a`.`id` = `d`.`cluster_id`";
        $query .= " WHERE `a`.`parent_id` = $parentId";

        $sql->raw($query);

        $db->setQuery($sql);

        $db->query();

        return true;
    }

    public function createRecurringEvents($parentEvent, $type, $end)
    {
        // 1. Dup event item
        // 2. Dup event meta
        // 3. Dup event nodes
        // 4. Dup event steps
        // 5. Dup event fields

        // Get main table item first
        $table = FD::table('Cluster');
        $table->load($parentEvent->id);

        // Get the meta table
        $meta = FD::table('EventMeta');
        $meta->load(array('cluster_id' => $parentEvent->id));

        $unit = array(
            'daily' => 60*60*24,
            'weekly' => 60*60*24*7,
            'monthly' => 60*60*24*30,
            'yearly' => 60*60*24*365
        );

        $eventStart = $parentEvent->getEventStart();
        $eventEnd = $parentEvent->getEventEnd();

        $startUnix = $eventStart->toUnix();
        $endUnix = $eventEnd->toUnix();

        $duration = $endUnix - $startUnix;

        $recurringEnd = FD::date($end, false);
        $recurringEndUnix = $recurringEnd->toUnix();

        $my = FD::user();

        do {
            // If is daily, then just add $unit['daily'] to the startUnix
            // If is weekly, then just add $unit['weekly'] to the startUnix
            if ($type === 'daily' || $type === 'weekly') {
                $startUnix += $unit[$type];

                $endUnix = $startUnix + $duration;

                // Create a new table record
                $table->id = null;
                $table->created = FD::date()->toSql();
                $table->key = md5($table->created . $my->password . uniqid());
                $table->parent_id = $parentEvent->id;
                $table->store();

                // Create a new meta record
                $meta->id = null;
                $meta->cluster_id = $table->id;
                $meta->start = FD::date($startUnix)->toSql();
                $meta->end = FD::date($endUnix)->toSql();
                $meta->store();
            }

            // If is monthly, this gets a bit tricky
            // Instead of adding the unit, we alter the date by 1 month
            // Then check if the day is valid on that month or not
            // If it is not valid, then fallback to the month's max day
            if ($type === 'monthly') {
                $startSql = $eventStart->toSql();
            }

        } while ($startUnix < $recurringEndUnix);
    }
}
