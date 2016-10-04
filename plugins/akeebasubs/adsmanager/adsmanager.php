<?php
/**
 * @package		akeebasubs
 * @copyright	Copyright (c)2012-2014 Juloa / www.Juloa.com
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die();

class plgAkeebasubsAdsmanager extends JPlugin
{
	/**
     * Add a new field to the level form
     * 
     * 
     * @return object
     */
    public function onSubscriptionLevelFormRender($userparams) {
	
		if(isset($userparams->params->published)){
			$published = $userparams->params->published;
		} else {
			$published = 0;
		}
        if(isset($userparams->params->unpublished)){
			$unpublished = $userparams->params->unpublished;
		} else {
			$unpublished = 0;
		}
	
        //publish/unpublish
        $html = 'Publié les annonces de l\'utilisateur sur inscription : <select name="params[published]" id="params_published">';
		if ($published == 1) {
			$html .= "<option value='1' selected>Oui</option>";
			$html .= "<option value='0'>Non</option>";
		} else {
			$html .= "<option value='1'>Oui</option>";
			$html .= "<option value='0' selected>Non</option>";
		}
		$html .="</select>";
		$html .="<br/>";
		$html .="Dépublié les annonces sur expiration : <select name='params[unpublished]' id='params_unpublished'>";
		if ($unpublished == 1) {
			$html .= "<option value='1' selected>Oui</option>";
			$html .= "<option value='0'>Non</option>";
		} else {
			$html .= "<option value='1'>Oui</option>";
			$html .= "<option value='0' selected>Non</option>";
		}
		$html .="</select>";
        $html .="<br/><br/>";
        
        //bring to top/not bring to top
        $html .= 'Faire remonter les annonces de l\'utilisateur sur inscription : <select name="params[bringtotop]" id="params_bringtotop">';
		if ($published == 1) {
			$html .= "<option value='1' selected>Oui</option>";
			$html .= "<option value='0'>Non</option>";
		} else {
			$html .= "<option value='1'>Oui</option>";
			$html .= "<option value='0' selected>Non</option>";
		}
		$html .="</select>";
		$html .="<br/>";
		$html .="Ne plus faire remonter les annonces sur expiration : <select name='params[notbringtotop]' id='params_notbringtotop'>";
		if ($unpublished == 1) {
			$html .= "<option value='1' selected>Oui</option>";
			$html .= "<option value='0'>Non</option>";
		} else {
			$html .= "<option value='1'>Oui</option>";
			$html .= "<option value='0' selected>Non</option>";
		}
		$html .="</select>";
        $html .="<br/><br/>";
        
        //Highlight/not Highlight
        $html .= 'Mettre en évidence les annonces de l\'utilisateur sur inscription : <select name="params[highlight]" id="params_highlight">';
		if ($published == 1) {
			$html .= "<option value='1' selected>Oui</option>";
			$html .= "<option value='0'>Non</option>";
		} else {
			$html .= "<option value='1'>Oui</option>";
			$html .= "<option value='0' selected>Non</option>";
		}
		$html .="</select>";
		$html .="<br/>";
		$html .="Ne plus mettre en évidence les annonces sur expiration : <select name='params[unhighlight]' id='params_unhighlight'>";
		if ($unpublished == 1) {
			$html .= "<option value='1' selected>Oui</option>";
			$html .= "<option value='0'>Non</option>";
		} else {
			$html .= "<option value='1'>Oui</option>";
			$html .= "<option value='0' selected>Non</option>";
		}
		$html .="</select>";
        $html .="<br/><br/>";
        
        //Featured/not Featured
        $html .= 'Mettre à la une les annonces de l\'utilisateur sur inscription : <select name="params[featured]" id="params_featured">';
		if ($published == 1) {
			$html .= "<option value='1' selected>Oui</option>";
			$html .= "<option value='0'>Non</option>";
		} else {
			$html .= "<option value='1'>Oui</option>";
			$html .= "<option value='0' selected>Non</option>";
		}
		$html .="</select>";
		$html .="<br/>";
		$html .="Ne plus mettre à la une les annonces sur expiration : <select name='params[unfeatured]' id='params_unfeatured'>";
		if ($unpublished == 1) {
			$html .= "<option value='1' selected>Oui</option>";
			$html .= "<option value='0'>Non</option>";
		} else {
			$html .= "<option value='1'>Oui</option>";
			$html .= "<option value='0' selected>Non</option>";
		}
		$html .="</select>";
               
        $ret = (object)array(
                        'title' => JText::_('AdsManager Integration'),
                        'html'  => $html
                );
        
        return $ret;
    }

	/**
	 * Called whenever a subscription is modified. Namely, when its enabled status,
	 * payment status or valid from/to dates are changed.
	 */
	public function onAKSubscriptionChange($row, $info)
	{
		$akeebaSubLevelId = $row->akeebasubs_level_id;
            
        include_once(JPATH_LIBRARIES.'/fof/include.php');
        
        $level = FOFModel::getTmpInstance('Levels','AkeebasubsModel')
				->setId($akeebaSubLevelId)
				->getItem();

		if (($row->state == 'C')&&($row->enabled == 1)) {
			if (@$level->params['published'] == 1) {
				$this->publishAds($row->user_id);
			}
            if (@$level->params['bringtotop'] == 1) {
				$this->bringToTopAds($row->user_id);
			}
            if (@$level->params['highlight'] == 1) {
				$this->highlightAds($row->user_id);
			}
            if (@$level->params['featured'] == 1) {
				$this->featuredAds($row->user_id);
			}
		} else {
			if (@$level->params['unpublished'] == 1) {
				$this->unpublishAds($row->user_id);
			}
            if (@$level->params['notbringtotop'] == 1) {
				$this->notbringToTopAds($row->user_id);
			}
            if (@$level->params['unhighlight'] == 1) {
				$this->unhighlightAds($row->user_id);
			}
            if (@$level->params['unfeatured'] == 1) {
				$this->unfeaturedAds($row->user_id);
			}
		}
	}
	
    /**
     * Publish all the ads of the user
     * 
     * @param int $userId
     */
    public function publishAds($userId) {
        
         $db = JFactory::getDbo();

        $query = "UPDATE `#__adsmanager_ads`
                  SET published = 1
                  WHERE userid = ".(int)$userId;

        $db->setQuery($query);
        try {
            $db->execute();
        } catch (Exception $e) {
            echo 'Error when publishing the ads';exit();
        }
    }
    
    /**
     * Bring to top all the ads of the user
     * 
     * @param int $userId
     */
    public function bringToTopAds($userId) {
        
         $db = JFactory::getDbo();

        $query = "UPDATE `#__paidsystem_ads` as pa
                  INNER JOIN `#__adsmanager_ads` as aa
                  ON aa.id = pa.id
                  SET pa.top = 1,
                      pa.top_date = NULL
                  WHERE aa.userid = ".(int)$userId;

        $db->setQuery($query);
        try {
            $db->execute();
        } catch (Exception $e) {
            echo 'Error when bringing to top the ads';exit();
        }
    }
    
    /**
     * Highlight all the ads of the user
     * 
     * @param int $userId
     */
    public function highlightAds($userId) {
        
         $db = JFactory::getDbo();

        $query = "UPDATE `#__paidsystem_ads` as pa
                  INNER JOIN `#__adsmanager_ads` as aa
                  ON aa.id = pa.id
                  SET pa.highlight = 1,
                      pa.highlight_date = NULL
                  WHERE aa.userid = ".(int)$userId;

        $db->setQuery($query);
        try {
            $db->execute();
        } catch (Exception $e) {
            echo 'Error when highlighting the ads';exit();
        }
    }
    
    /**
     * Feature all the ads of the user
     * 
     * @param int $userId
     */
    public function featuredAds($userId) {
        
         $db = JFactory::getDbo();

        $query = "UPDATE `#__paidsystem_ads` as pa
                  INNER JOIN `#__adsmanager_ads` as aa
                  ON aa.id = pa.id
                  SET pa.featured = 1,
                      pa.featured_date = NULL
                  WHERE aa.userid = ".(int)$userId;

        $db->setQuery($query);
        try {
            $db->execute();
        } catch (Exception $e) {
            echo 'Error when featuring the ads';exit();
        }
    }
    
    /**
     * Unpublish all the ads of the user
     * 
     * @param int $userId
     */
    public function unpublishAds($userId) {
        
        $db = JFactory::getDbo();

        $query = "UPDATE `#__adsmanager_ads`
                  SET published = 0
                  WHERE userid = ".(int)$userId;

        $db->setQuery($query);
        try {
            $db->execute();
        } catch (Exception $e) {
            echo 'Error when unpublishing the ads';exit();
        }
    }
    
    /**
     * disable the bring to top for all the ads of the user
     * 
     * @param int $userId
     */
    public function notbringToTopAds($userId) {
        
         $db = JFactory::getDbo();

        $query = "UPDATE `#__paidsystem_ads` as pa
                  INNER JOIN `#__adsmanager_ads` as aa
                  ON aa.id = pa.id
                  SET pa.top = 0
                  WHERE aa.userid = ".(int)$userId;

        $db->setQuery($query);
        try {
            $db->execute();
        } catch (Exception $e) {
            echo 'Error when disabling the bring to top the ads';exit();
        }
    }
    
    /**
     * Unhighlight all the ads of the user
     * 
     * @param int $userId
     */
    public function unhighlightAds($userId) {
        
         $db = JFactory::getDbo();

        $query = "UPDATE `#__paidsystem_ads` as pa
                  INNER JOIN `#__adsmanager_ads` as aa
                  ON aa.id = pa.id
                  SET pa.highlight = 0
                  WHERE aa.userid = ".(int)$userId;

        $db->setQuery($query);
        try {
            $db->execute();
        } catch (Exception $e) {
            echo 'Error when unhighlighting the ads';exit();
        }
    }
    
    /**
     * Unfeature all the ads of the user
     * 
     * @param int $userId
     */
    public function unfeaturedAds($userId) {
        
         $db = JFactory::getDbo();

        $query = "UPDATE `#__paidsystem_ads` as pa
                  INNER JOIN `#__adsmanager_ads` as aa
                  ON aa.id = pa.id
                  SET pa.featured = 0
                  WHERE aa.userid = ".(int)$userId;

        $db->setQuery($query);
        try {
            $db->execute();
        } catch (Exception $e) {
            echo 'Error when unfeaturing the ads';exit();
        }
    }
}
