<?php
/**
 * @package ZT Tabs module
 * @author DucNA
 * @copyright (C) 2014- ZooTemplate.Com
 * @license PHP files are GNU/GPL
 **/
defined ( '_JEXEC' ) or die ( 'Restricted access' );
$user = JFactory::getUser ();
$db = JFactory::getDBO ();
require_once (JPATH_SITE . DS . 'components' . DS . 'com_content' . DS . 'helpers' . DS . 'route.php');

class moZTTabsHelper{
    var  $config;

    function createdDirThumb(){
        $thumbImgParentFolder = JPATH_BASE.DS.'cache'.DS.'zt-assets';
        if(!JFolder::exists($thumbImgParentFolder)){
            JFolder::create($thumbImgParentFolder);
        }
    }

    function __construct($params){
        //jimport('joomla.filesystem.file');
        //  configuration array
        $this->config =array(
            'arrayTabs'=>array(),
            'maxNumberCategory' => 5,
            'showIntroImage' => 1,
            'itemsOrdering' => 'default',
            'introTextLength' => '200',

            'tab_style' => '',
            'tab_alignment' => '',
            'title_position' => '',
            'effect_type' => '',
            'tWidth'=> '',
            'tHeight' =>'',
            'tab_maxItem' =>'',



            'intro_image_width' => '100',
            'intro_image_height' => '100'
        );
        $this->parsedData =array(
            'arrayTabs'=>array(),
            'showReadMore'=>1,

            'tab_style' => '',
            'tab_alignment' => '',
            'title_position' => '',
            'effect_type' => '',
            'tWidth'=> '',
            'tHeight' =>'',
            'intro_image_width' => '100',
            'intro_image_height' => '100',
            'tab_maxItem' =>'',

            'showIntroImage'=>1

        );
        //get the config default
        $this->config['arrayTabs']= explode(',',$params->get('jv_selection',''));
        $this->config['maxNumberCategory']= $params->get('categoryID-maxItem',5);
        $this->config['showIntroImage']= $params->get('jv_selection','');
        $this->config['itemsOrdering']= $params->get('categoryID-ordering','');
        $this->config['introTextLength']= $params->get('intro_length','');

        $this->config['tab_style']= $params->get('tab_style','zt_style4');
        $this->config['title_position']= $params->get('title_position','top');

        $this->config['tab_alignment']= $params->get('tab_alignment','left');
        $this->config['effect_type']= $params->get('effect_type','fade');
        $this->config['showIntroImage']= $params->get('showIntroImage','');

        $this->config['tWidth']= $params->get('tWidth','auto');
        $this->config['tHeight']= $params->get('tHeight','auto');

        $this->config['intro_image_width']= $params->get('intro_image_width','100');
        $this->config['intro_image_height']= $params->get('intro_image_height','100');
        $this->config['tab_maxItem']= $params->get('tab_maxItem',5);

    }
    //check published module and category in arrTabs
    function checkData($tab) {
        $tableName = '';
        if($tab[0]=='module'){
            $tableName = '#__modules';
        }elseif($tab[0]=='category') {
            $tableName = '#__categories';
        }
        // Get a db connection.
        $db = JFactory::getDbo();

        // Create a new query object.
        $query = $db->getQuery(true);
        $query
            ->select($db->quoteName('published'))
            ->from($db->quoteName($tableName,'c'))
            ->where($db->quoteName('c.id')." = ".$db->quote($tab[1]));

        $db->setQuery($query);
        return $db->loadResult();
    }

    function parseData(){
        $tab_maxItem= $this->config['tab_maxItem'];
        $arrTab = $this->config['arrayTabs'];
        $count = count($arrTab);
        if($count>$tab_maxItem){
            $count = $tab_maxItem;
        }
        for($i=0; $i<$count; $i++) {
            $tab = explode('_',$arrTab[$i]);
            if($this->checkData($tab)){
                $this->parsedData['arrayTabs'][$i] = $tab;
            }
        }

        // $this->parsedData['arrayTabs'] hien ca module va category ra, 0,1,2 laf module; 3 la category
        $this->parsedData['tab_style'] = $this->config['tab_style'];
        $this->parsedData['title_position'] = $this->config['title_position'];

        $this->parsedData['tab_alignment'] = $this->config['tab_alignment'];
        $this->parsedData['effect_type'] = $this->config['effect_type'];
        $this->parsedData['showIntroImage'] = $this->config['showIntroImage'];

        $this->parsedData['tWidth']= ($this->config['tWidth']=='auto')?  $this->config['tWidth']: $this->config['tWidth'].'px';
        $this->parsedData['tHeight']= ($this->config['tHeight']=='auto')?  $this->config['tHeight']: $this->config['tHeight'].'px';
        $this->parsedData['intro_image_width'] = $this->config['intro_image_width'].'px';
        $this->parsedData['intro_image_height'] = $this->config['intro_image_height'].'px';

        $this->parsedData['tab_maxItem'] = $this->config['tab_maxItem'];





    }

    function renderLayout(){
        //include necessary view
        require(JModuleHelper::getLayoutPath('mod_zt_tabs','zt_default'));
    }

    //Get title module by id input
    function getCategoryTileById($catId){
        $db = JFactory::getDBO ();
        $sql ="SELECT title
				FROM #__categories AS c
				WHERE c.published = 1  AND c.id ='".$catId."'";
        $db->setQuery($sql);
        return $db->loadResult();
    }
    //End get title Category

    //Get title module by id input
    public  function getModuleTitleById($moduleId){
        $db = JFactory::getDBO ();
        $sql = "SELECT title FROM #__modules WHERE id=".$moduleId;
        $db->setQuery( $sql );
        return $db->loadResult();
    }
    //End get title Module

    //Get content Module
    function parseTabModuleById($moduleId){
        //echo ' chay den day';
        $modules = & $this->_load ();
        $result ='';
        //var_dump($modules);
        foreach($modules as $module){
            //var_dump($module->id== $moduleId);
            if(((int)$module->id == (int) $moduleId) && $module) $result = $module;
        }

        return $result;
    }
    //End get Content

    //Get content Category
    function getListContentArticle($catId){
        $db = JFactory::getDBO ();
        $nullDate   = $db->getNullDate();
        $date = JFactory::getDate();
        $now = $date->toSql();
        $count = $this->config['maxNumberCategory'];


        // Ordering
        switch ($this->config['itemsOrdering']) {
            case 'm_dsc' :
                $ordering = array('a.modified DESC',' a.created DESC');
                break;
            case 'c_dsc' :
                $ordering = ' a.ordering ';
                break;
            default :
                $ordering = ' a.ordering';
                break;
        }
        $query = $db->getQuery(true);

        $query
            ->select(' a.* ')
            ->select($db->quoteName('c.id','idCategory'))
            ->select($db->quoteName('c.alias','alCategory'))
            ->select($db->quoteName('c.published'))
            ->select($db->quoteName('b.username', 'user'))
            ->from ($db->quoteName('#__content','a'))
            ->join('INNER', $db->quoteName('#__users', 'b') . ' ON (' . $db->quoteName('a.created_by') . ' = ' . $db->quoteName('b.id') . ')')
            ->join('LEFT', $db->quoteName('#__categories', 'c') . ' ON (' . $db->quoteName('a.catid') . ' = ' . $db->quoteName('c.id') . ')')
            ->where($db->quoteName('a.catid') . ' = '.$catId)
            ->where($db->quoteName('a.state') . ' = 1')
            ->where($db->quoteName('c.published') . ' = 1')
            ->where($db->quoteName('a.publish_up').' BETWEEN  '.$db->Quote($nullDate).' AND '.$db->Quote($now))
            ->where($db->quoteName('a.publish_down').' BETWEEN  '.$db->Quote($nullDate).' AND '.$db->Quote($now))
            ->order($ordering)
        ;

        $db->setQuery($query, 0, $count);

        $rows = $db->loadObjectList();


        $lists = array();

        $j = 0;
        if(count($rows)){
            foreach($rows as $row) {
                //var_dump($row);
                $lists[$j] = new stdClass();
                $lists[$j]->id = $row->id;
                $lists[$j]->title = $row->title;
                $lists[$j]->link = JRoute::_(ContentHelperRoute::getArticleRoute($row->id.':'.$row->alias,$row->idCategory.':'.$row->alCategory, $row->id)) ;

                $lists[$j]->user = $row->user;
                $date = strtotime($row->created);
                $lists[$j]->created = date('F j, Y',$date);

                $lists[$j]->introText = $this->introText($row->introtext);
                $img =    json_decode($row->images, true);
                $lists[$j]->introImage = $img['image_intro'];
                $j++;

            }
        }
        return $lists;
    }
    //End get content Category

    function &_load()
    {
        global $app, $itemId;
        static $modules;

        if (isset($modules)) {
            return $modules;
        }

        $user	= JFactory::getUser();
        $db		= JFactory::getDBO();
        $aid	= (!$user->get('aid', 1)) ? 1 : $user->get('aid', 1);

        $modules	= array();

        $wheremenu = isset( $itemId ) ? ' AND ( mm.menuid = '. (int) $itemId .' OR mm.menuid = 0 )' : '';
        $query = 'SELECT id, IF(title="","","") as title , module, position, content, showtitle, params'
            . ' FROM #__modules AS m'
            . ' LEFT JOIN #__modules_menu AS mm ON mm.moduleid = m.id'
            . ' WHERE m.published = 1'
            . ' AND m.access <= '. (int)$aid
            . ' AND m.client_id = '. (int)$app->getClientId()
            . $wheremenu
            . ' ORDER BY position, ordering';
        // var_dump($aid);
        $db->setQuery( $query );

        if (null === ($modules = $db->loadObjectList())) {
            JError::raiseWarning( 'SOME_ERROR_CODE', JText::_( 'Error Loading Modules' ) . $db->getErrorMsg());
            return false;
        }

        $total = count($modules);
        for($i = 0; $i < $total; $i++)
        {
            //determine if this is a custom module
            $file					= $modules[$i]->module;
            $custom 				= substr( $file, 0, 4 ) == 'mod_' ?  0 : 1;
            $modules[$i]->user  	= $custom;
            // CHECK: custom module name is given by the title field, otherwise it's just 'om' ??
            $modules[$i]->name		= $custom ? $modules[$i]->title : substr( $file, 4 );
            $modules[$i]->style		= null;
            $modules[$i]->position	= strtolower($modules[$i]->position);
        }

        return $modules;
    }

    function introText($content){
        $introLength = intval($this->config['introTextLength'] );
        $content = preg_replace("/<img[^>]+\>/i", "", $content);
        $content = preg_replace("#{(.*?)}(.*?){/(.*?)}#s", '', $content);
        // $content = preg_replace("<p></p>", "", $content);
        $length = strlen($content);
        if($length > $introLength){
            $content = substr($content, 0, $introLength);
            $content .= '...';
        }
        return $content;
    }

}
?>
