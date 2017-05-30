<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

defined('_JEXEC') or die();

jimport( 'joomla.filesystem.file' );

class AdsmanagerTableContents extends JTable
{
	var $id= null;
	var $userid= null; 
	var $ad_headline=null;
	var $ad_text=null;
	var $email=null;
	var $date_created = null;
	var $date_modified = null;
	var $expiration_date = null;
    var $publication_date = null;
	var $recall_mail_sent = null;
	var $metadata_description = null;
	var $metadata_keywords = null;
	var $published = null;
	var $images = null;
	
	var $errors = null;
	var $data = null;
    var $new_ad = false;
			
    function __construct(&$db)
    {
    	parent::__construct( '#__adsmanager_ads', 'id', $db );
    }
    
	function replaceBannedWords($text,$bannedwords,$replaceword) {
    	foreach($bannedwords as $bannedword) {
    		if ($bannedword != "") {
    			// preg_replace can returns NULL if bad bannedword, we should test return value before assign to $text;
    			$text2 = preg_replace("/\b$bannedword\b/u", $replaceword, $text);
				if ($text2 != NULL)
					$text = $text2;
    		}
    	}
    	return $text;
    }

	function getErrors() {
    	return $this->errors;
    }
    
	function getData() {
    	return $this->data;
    }
    
    function map() {
    	foreach($this->data['fields'] as $name => $value) {
    		$this->$name = $value;
    	}
    	if (isset($this->data['paid'])) {
	    	foreach($this->data['paid'] as $name => $value) {
	    		$this->$name = $value;
	    	}
    	}
    	unset($this->data);
    }
    
	function bindContent($post,$files,$conf,$model,$plugins)
    {
    	
    	$app = JFactory::getApplication();
    	
    	$this->bind($post);
    	
    	if ($this->id == 0) {
    		$this->new_ad = true;
    	} else {
			$query = "SELECT content FROM #__adsmanager_pending_ads WHERE contentid = $this->id";
			$this->_db->setQuery($query);
			$currentpendingchanges = $this->_db->loadResult();
            if($currentpendingchanges != null){
				$currentpendingchanges = @json_decode($currentpendingchanges);
	            
	            if(isset($currentpendingchanges->published)) {
	                $this->published = $currentpendingchanges->published;
	            }
	            if(isset($currentpendingchanges->new_ad)) {
	                $this->new_ad = $currentpendingchanges->new_ad;
	            }
			}
    	
		}
    	
    	$this->data = array();
    	$this->errors = array();
    			
		if (function_exists("getMaxCats"))
			$maxcats = getMaxCats($conf->nbcats);
		else
			$maxcats = $conf->nbcats;
			
		if ($maxcats > 1)
		{
			$selected_cats = $post["selected_cats"];
			if (!is_array($selected_cats)) {
				$c = array();
				$c[0] = $selected_cats;
				$selected_cats = $c;
			}
			if (count($selected_cats) > $maxcats)
			{
				$selected_cats = array_slice ($selected_cats, 0, $maxcats);
			}
			$this->data['categories'] = $selected_cats;
		}
		else
		{
			$category = $post["category"];
			$this->data['categories'] = array();
			$this->data['categories'][0] = intval($category);
		}
			
		//get fields
		$this->_db->setQuery( "SELECT * FROM #__adsmanager_fields WHERE published = 1");
		$fields = $this->_db->loadObjectList();
		foreach($fields as $key => $field) {
			$fields[$key]->options = json_decode($fields[$key]->options);
		}
		if ($this->_db -> getErrorNum()) {
			$this->errors[] = $this->_db -> stderr();
			return false;
		}	
		
		$query = "UPDATE #__adsmanager_ads ";
		
		$bannedwords = str_replace("\r","",$conf->bannedwords);
		$bannedwords = explode("\n",$bannedwords);
		$replaceword = $conf->replaceword;
		
		$data['fields'] = array();
		foreach($fields as $field)
		{ 	
			//If admin edit only should leave the loop otherwise value is reseted to empty string
			if (($app->isAdmin() == false)&&(@$field->options->edit_admin_only == 1))
				continue;
			
			if ($field->type == "multiselect")
			{	
				$value = JRequest::getVar($field->name, array());
				$this->data['fields'][$field->name] = $value;
			}
			else if (($field->type == "multicheckbox")||($field->type == "multicheckboximage"))
			{
				$value = $value = JRequest::getVar($field->name, array());
				$this->data['fields'][$field->name] = $value;
			}
			else if ($field->type == "file")
			{
				if (isset($files[$field->name]) and !$files[$field->name]['error'] ) {
					jimport( 'joomla.filesystem.file' );
					
					if (is_file(JPATH_ROOT."/images/com_adsmanager/files/".$field->name)) {
						JFile::delete(JPATH_ROOT."/images/com_adsmanager/files/".$field->name);
					}
				
					$filename = $files[$field->name]['name'];
					
					$extension = JFile::getExt($filename);
					$name = md5(rand(1,100000).$filename);
					
					if (strpos($extension,"php") !== false) {
						$extension = 'txt';
					}
					$filename = $name.".".$extension;				
					JFile::upload($files[$field->name]['tmp_name'],
								JPATH_ROOT."/images/com_adsmanager/files/".$filename);	
					$this->data['fields'][$field->name] = $filename;					
				} else {
					if (JRequest::getInt("delete_".$field->name) == 1) {
						if (is_file(JPATH_ROOT."/images/com_adsmanager/files/".$field->name)) {
							JFile::delete(JPATH_ROOT."/images/com_adsmanager/files/".$field->name);
						}
						$this->data['fields'][$field->name] = "";
					}
				}
			}
			else if ($field->type == "editor")
			{
				$value = JRequest::getVar($field->name, '', 'post', 'string', JREQUEST_ALLOWHTML);
				$this->data['fields'][$field->name] = $this->replaceBannedWords($value,$bannedwords,$replaceword);
			}
            else if ($field->type == "price") {
                $value = JRequest::getVar($field->name, '');
                $value = str_replace(',', '.', $value);
                $value = str_replace(' ', '', $value);
                $this->data['fields'][$field->name] = $value;
            }
			//Plugins
			else if (isset($plugins[$field->type]))
			{
				$value = $plugins[$field->type]->onFormSave($this,$field);
				if ($value !== null)
					$this->data['fields'][$field->name] = $value;
			}
			else
			{
				$value = JRequest::getVar($field->name, '');
				$this->data['fields'][$field->name] =$this->replaceBannedWords($value,$bannedwords,$replaceword);
			}	
		}
		
		$this->data['images']= array();	
		$this->data['delimages']= array();

		$current_images = json_decode($this->images);
		if ($current_images == null)
			$current_images = array();

		$image_index = 0;

		$pending = JRequest::getInt('pending',0);
		if ($pending && (count($currentpendingchanges->images) > 0)) {
			foreach($currentpendingchanges->images as $img) {
				$this->data['images'][] = $img;
				if ($img->index > $image_index )
					$image_index = $img->index;
			}
		}
		
		$deleted_images = JRequest::getVar("deleted_images", "" );
		$deleted_images = explode(',',$deleted_images);
		foreach($current_images as $i => $img) {
			if (in_array($img->index,$deleted_images)) {
				$this->data['delimages'][] = $img;
			} else {
				if ($img->index > $image_index )
					$image_index = $img->index;
			}
		}
		foreach($this->data['images'] as $i => $img) {
			if (in_array($img->index,$deleted_images)) {
                if(is_file(JPATH_IMAGES_FOLDER."/waiting/".$img->image)) {
                    JFile::delete(JPATH_IMAGES_FOLDER."/waiting/".$img->image);
                }
                if(is_file(JPATH_IMAGES_FOLDER."/waiting/".$img->thumbnail)) {
                    JFile::delete(JPATH_IMAGES_FOLDER."/waiting/".$img->thumbnail);
                }
                if(is_file(JPATH_IMAGES_FOLDER."/waiting/".$img->medium)) {
                    @JFile::delete(JPATH_IMAGES_FOLDER."/waiting/".$img->medium);
                }
				unset($this->data['images'][$i]);
			}
		}
		
		$nb_images = count($current_images) - count($this->data['delimages']);
		
		$nbMaxImages = $conf->nb_images;
			
		if (function_exists("getMaxPaidSystemImages")) {
			$nbMaxImages += getMaxPaidSystemImages();
		}

		$uploader_count = JRequest::getInt('imagesupload_count',0);
		
		$targetDir = JPATH_IMAGES_FOLDER."/uploaded";
		$dir = JPATH_IMAGES_FOLDER."/waiting/";
		
		$orderlisttmp = explode(',',JRequest::getString('orderimages',""));
		$orderlist = array();
		foreach($orderlisttmp as $value) {
			$orderlist[] = str_replace('li_img_','',$value);
		}
		for($i = 0 ; $i < $uploader_count && $nb_images <  $nbMaxImages ; $i++) {
			$uploader_tmpname = JRequest::getString('imagesupload_'.$i.'_tmpname',0);
			$uploader_id = JRequest::getString('imagesupload_'.$i.'_id',0);
			$uploader_name = JRequest::getString('imagesupload_'.$i.'_name',0);
			$uploader_status = JRequest::getString('imagesupload_'.$i.'_status',0);

			$tmpfile = sha1(microtime(true).mt_rand(10000,90000)).".jpg";
			$thumb_tmpfile = sha1(microtime(true).mt_rand(10000,90000)).".jpg";
			$medium_tmpfile = sha1(microtime(true).mt_rand(10000,90000)).".jpg";
		//	var_dump($targetDir."/".$uploader_tmpname);
			//var_dump($uploader_status);
            if (($uploader_status == "done")&&(file_exists($targetDir."/".$uploader_tmpname))) {
        		try {
					$error = $model->createImageAndThumb($targetDir."/".$uploader_tmpname,
					   							$tmpfile,$thumb_tmpfile,
												$conf->max_width,
												$conf->max_height,
												$conf->max_width_t,
												$conf->max_height_t,
												$conf->tag,
												$dir,
												$uploader_name,
												$conf->max_width_m,
												$conf->max_height_m,
												$medium_tmpfile
												);
					
                    if(is_file($targetDir."/".$uploader_tmpname)) {
                        JFile::delete($targetDir."/".$uploader_tmpname);
                    }
		
					if ($error != null) {
						$this->errors[] = $error;
					} else {
						$image_index++;
						$nb_images++;
						$newimg = new stdClass();
						$newimg->index = $image_index;
						$newimg->image = $tmpfile;
						$newimg->thumbnail = $thumb_tmpfile;
						$newimg->medium = $medium_tmpfile;
						$this->data['images'][] = $newimg;
						//echo $uploader_id."<br/>";
						foreach($orderlist as $key => $val) {
							if ($val == $uploader_id) {
								$orderlist[$key] = $image_index;
							}
						}
					}
				} catch (Exception $e) {
                    if(is_file($targetDir."/".$uploader_tmpname)) {
                        JFile::delete($targetDir."/".$uploader_tmpname);
                    }
					$this->errors[] = $e->getMessage();
				}
        	} 
  	 	}
  	 	//exit();
  	 	
		$this->data['orderimages'] = $orderlist;
		
		$this->update_validation = @$conf->update_validation;

		$app = JFactory::getApplication();
		if (($app->isAdmin() == false)&&($this->id == 0)) {
			$this->date_created = date("Y-m-d H:i:s");
            $delta = $conf->ad_duration;
            if ($delta == 0) {
            	$this->expiration_date = null;
            } else {
				$this->expiration_date = date("Y-m-d H:i:s",time()+($delta*24*3600));
            }
			if ($conf->auto_publish == 1)
				$this->published = 1;
			else
				$this->published = 0;
		} else if ($this->id != 0) {
			if ($this->update_validation) {
				$this->published = 0;
			}
		}
		
        if($this->id == 0){
            if(!isset($conf->publication_date) || $conf->publication_date == 0) {
                $this->publication_date = date("Y-m-d H:i:s");
            } else {
                $this->publication_date = $this->publication_date.' 00:00:00';
            }
            if($app->isAdmin() == false){
                $this->expiration_date = date("Y-m-d H:i:s",  strtotime($this->publication_date)+($delta*24*3600));
            } 
        }else{
            if($app->isAdmin() == true){
                $this->publication_date = $this->publication_date.' 00:00:00';
            }
        }
		
				
		if (count($this->errors) > 0)
			return false;
		else
			return true;
	}

	function savePending() {
    	$row = new JObject();
    	
		if ($this->id == 0) {	
    		$data = new JObject();
    		$data->date_created = $this->date_created;
    		$data->expiration_date = $this->expiration_date;
            $data->publication_date = $this->publication_date;
    		$data->published = 0;
			$data->userid = $this->userid;
			$this->_db->insertObject('#__adsmanager_ads', $data);
			$this->id = (int)$this->_db->insertid();
			
            $this->data['publication_date'] = $this->publication_date;
		}
		
		//if not 0 or 1, do not change published value. It's update case without price bindContent savePending saveContent
		if ($this->published !== null) {
			$this->data['published'] = $this->published;
		}
        $this->data['new_ad'] = $this->new_ad;
		
    	$row->contentid = $this->id;
    	$row->userid = $this->userid;
		$row->date = date("Y-m-d H:i:s");
		
		$this->data['metadata_description'] = $this->metadata_description;
		$this->data['metadata_keywords'] = $this->metadata_keywords;
		
		$row->content = json_encode($this->data);
		
		$query = "DELETE FROM #__adsmanager_pending_ads WHERE contentid = ".(int)$row->contentid;
		$this->_db->setQuery($query);
		$this->_db->query();

		//Insert new record.
		$this->_db->insertObject('#__adsmanager_pending_ads', $row);
		
		return $row->contentid;
    }

	function bindPending($contentid) 
	{

		$query = " SELECT * ".
				" FROM #__adsmanager_pending_ads".
				" WHERE contentid = ".intval($contentid);
		$db = JFactory::getDbo();
		$this->_db->setQuery($query);
		$pending = $this->_db->loadObject();
		if ($pending == null)
			return false;
		
		$this->data = json_decode($pending->content,true);
		foreach($pending as $key => $val) {
			$this->key = $val;
		}
		$this->metadata_description = $this->data['metadata_description'];
		$this->metadata_keywords = $this->data['metadata_keywords'];

		foreach($this->data['images'] as $key => $image) {
			$this->data['images'][$key] = (object) $image;
		}

		foreach($this->data['delimages'] as $key => $image) {
			$this->data['delimages'][$key] = (object) $image;
		}

		//update_validation
		//TODO
		$this->data['publication_date'] = $this->publication_date;
		
		if (isset($this->data['published'])) {
			$this->published = $this->data['published'];
		}

		//TODO outrouver hardcoder
		$this->update_validation = 1;
	}
    /**
     * No args just to be compliant with joomla API
     * @param unknown_type $src
     * @param unknown_type $orderingFilter
     * @param unknown_type $ignore
     */
	function saveContent($src, $orderingFilter = '', $ignore = '')
    {
    	$row = new JObject();
    	
    	if ($this->id != 0) {
    		$row->id = $this->id;
    	}
    	//new_ad
    	$app = JFactory::getApplication();
    	if (($app->isAdmin() == true) ||
    		(($app->isAdmin() == false)&&(@$this->new_ad == true))) {
    		$row->date_created = $this->date_created;
    		$row->expiration_date = $this->expiration_date;
            $row->publication_date = $this->publication_date;	
    	} 
    	
    	//In case of bindContent / update Ad, $this->published is not set
    	if ($this->published !== null) {
    		$row->published = $this->published;
    	}
    	
    	$row->date_modified = date('Y-m-d H:i:s');
    		
    	$row->userid = $this->userid;
    	
		foreach($this->data['fields'] as $name => $value) {
			if (is_array($value))
				$v = ','.implode(',',$value).',';
			else
				$v = $value;
			$row->$name = $v;
		}
		
		
		
		$row->metadata_description = $this->metadata_description;
		$row->metadata_keywords = $this->metadata_keywords;

		//Insert new record.
		if ($this->id == 0) {
			$ret = $this->_db->insertObject('#__adsmanager_ads', $row);
			$contentid = (int)$this->_db->insertid();
		} else {
			$ret = $this->_db->updateObject('#__adsmanager_ads', $row,'id');	
			$contentid = $this->id;
		}		
		
		// Category
		$query = "DELETE FROM #__adsmanager_adcat WHERE adid = $contentid";
		$this->_db->setQuery($query);
		$this->_db->query();
		
		foreach($this->data['categories'] as $cat) {
			$query = "INSERT INTO #__adsmanager_adcat(adid,catid) VALUES ($contentid,$cat)";
			$this->_db->setQuery($query);
			$this->_db->query();
			$this->catid = $cat;
		}
		
		//Images		
		$dir = JPATH_IMAGES_FOLDER."/waiting/";
		$dirfinal = JPATH_IMAGES_FOLDER."/";
		
		$current_images = json_decode($this->images);
		if ($current_images == null)
			$current_images = array();
		if (!is_array($current_images))
			$current_images = get_object_vars($current_images);
		
    	foreach($this->data['delimages'] as $image) {
            if(is_file(JPATH_IMAGES_FOLDER."/".$image->image)) {
                JFile::delete(JPATH_IMAGES_FOLDER."/".$image->image);
            }
            if(is_file(JPATH_IMAGES_FOLDER."/".$image->thumbnail)) {
                JFile::delete(JPATH_IMAGES_FOLDER."/".$image->thumbnail);
            }
			if(is_file(JPATH_IMAGES_FOLDER."/".$image->medium)) {
                @JFile::delete(JPATH_IMAGES_FOLDER."/".$image->medium);
            }
			foreach($current_images as $key => $img) {
				if ($img->index == $image->index) {
					unset($current_images[$key]);
					break;
				}
			}
		}
		
		if (!is_array($current_images))
			$current_images = get_object_vars($current_images);
		sort($current_images);

		jimport( 'joomla.filter.output' );
		//True to force transliterate
		$imgtitle = TTools::stringURLSafe($row->ad_headline,true);
		if ($imgtitle == "") {
			$imgtitle="image";
		}
		
   		foreach($this->data['images'] as &$image) {	
			$src  =$dir.$image->image;
			$dest =$dirfinal.$imgtitle."_".$contentid."_".$image->index.".jpg";			 
			JFile::move($src,$dest);
			$image->image = $imgtitle."_".$contentid."_".$image->index.".jpg";
			
			$src  =$dir.$image->thumbnail;
			$dest =$dirfinal.$imgtitle."_".$contentid."_".$image->index."_t.jpg";		
			JFile::move($src,$dest);
			$image->thumbnail = $imgtitle."_".$contentid."_".$image->index."_t.jpg";	
			
			$src  =$dir.$image->medium;
			$dest =$dirfinal.$imgtitle."_".$contentid."_".$image->index."_m.jpg";		
			JFile::move($src,$dest);
			$image->medium = $imgtitle."_".$contentid."_".$image->index."_m.jpg";
			
			$current_images[]= $image;
		}
		
		$orderlist = $this->data['orderimages'];
		$newlistimages = array();
		foreach($orderlist as $o) {
			foreach($current_images as $image) {
				if ($image->index == $o)
					$newlistimages[] = $image;
			}
		}
		
		$row = new JObject();
    	$row->id = $contentid;
    	$row->images = json_encode($newlistimages);
    	$this->images = $newlistimages;
		$ret = $this->_db->updateObject('#__adsmanager_ads', $row,'id');	
		
		if (function_exists('savePaidAd')) {
			savePaidAd($this,$contentid);
		}
		
		$this->id = $contentid;

        $this->_db->setQuery( "DELETE FROM #__adsmanager_pending_ads WHERE contentid=".intval($contentid));
        $this->_db->query();
        
		cleanAdsManagerCache();
    }
    
    function deleteContent($adid,$conf,$plugins)
    {		
    	$adid = (int) $adid;
    	
		$this->_db->setQuery("SELECT * FROM #__adsmanager_ads WHERE id=$adid");
		$ad = $this->_db->loadObject();
		
		$this->_db->setQuery("DELETE FROM #__adsmanager_adcat WHERE adid=$adid");
		$this->_db->query();
		
		/*$this->_db->setQuery( "UPDATE #__adsmanager_ads SET published=0,recall_mail_sent = 0 WHERE id = $adid");
		$this->_db->query();
		
		$this->_db->setQuery( "INSERT INTO #__adsmanager_adcat (adid,catid) VALUES ($adid,$conf->archive_catid)");
		$this->_db->query();
		*/
		
		$this->_db->setQuery("DELETE FROM #__adsmanager_ads WHERE id=$adid");
		$this->_db->query();
		
		$this->_db->setQuery( "SELECT name FROM #__adsmanager_fields WHERE `type` = 'file'");
		$file_fields = $this->_db->loadObjectList();
		foreach($file_fields as $file_field)
		{
			$filename = "\$ad->".$file_field->name;
			eval("\$filename = \"$filename\";");
			if ( is_file(JPATH_ROOT."/images/com_adsmanager/files/".$filename)) {
				JFile::delete(JPATH_ROOT."/images/com_adsmanager/files/".$filename);
			}
		}
	
		$current_images = json_decode($ad->images);
		if ($current_images == null)
			$current_images = array();
	
		foreach($current_images as $img)
		{	
			$pict = JPATH_IMAGES_FOLDER."/".$img->image;
			if ( is_file( $pict)) {
				JFile::delete($pict);
			}
			$pic = JPATH_IMAGES_FOLDER."/".$img->thumbnail;
			if ( is_file( $pic)) {
				JFile::delete($pic);
			}
			$pic = JPATH_IMAGES_FOLDER."/".$img->medium;
			if ( is_file( $pic)) {
				JFile::delete($pic);
			}
		}
		
		//get fields
		$this->_db->setQuery( "SELECT * FROM #__adsmanager_fields WHERE published = 1");
		$fields = $this->_db->loadObjectList();
		foreach($fields as $key => $field) {
			$fields[$key]->options = json_decode($fields[$key]->options);
		}
		
		foreach($fields as $field)
		{
			if (isset($plugins[$field->type]))
			{
				//The two first parameter should not be used any word, there are keep to prevent old plugins not working (backward compatiblity)
				$value = $plugins[$field->type]->onDelete(0,$adid,$field,$ad);
			}
		}
		
		if (function_exists('deletePaidAd')){
			deletePaidAd($adid);
		}
		
    }
    
    /**
     * Add an entry in the favorite table if it doesn't already exist
     * 
     * @param integer $userId
     * @return boolean
     */
    function favorite($userId) {
        
        $query = "SELECT adid 
                  FROM #__adsmanager_favorite 
                  WHERE adid = ".$this->id." 
                  AND userid = ".(int)$userId;
        
        $this->_db->setQuery($query);
        $nbResult = $this->_db->loadObject();
        
        if($nbResult != null){
            echo '0'; //Add already in the fav
        } else {
            $query = "INSERT INTO #__adsmanager_favorite(adid,userid) VALUES ($this->id,$userId)";
            $this->_db->setQuery($query);
            $this->_db->query();

            echo '1';
        }
        exit();
    }
    
    /**
     * Remove the ad from the favorite list of the user
     * 
     * @param integer $userId
     */
    function deleteFavorite($userId,$mode = 0) {
        $query = "DELETE FROM #__adsmanager_favorite
                  WHERE adid = ".$this->id."
                  AND   userid = ".(int)$userId;
        $this->_db->setQuery($query);
        $this->_db->query();
        if($mode){
            echo 1;
            exit();
        }
    }
}
