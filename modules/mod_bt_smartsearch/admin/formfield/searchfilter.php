<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');
jimport('joomla.form.helper'); 
JFormHelper::loadFieldClass('list');

class JFormFieldSearchfilter extends JFormFieldList {
	protected $type = 'Searchfilter';

	protected function getOptions() {	
      
	        $db = JFactory::getDbo();
	        $query = $db->getQuery(true);
	        $query = 'SELECT * FROM #__finder_filters where state =1 ';
	        $db->setQuery($query);
	        $items = $db->loadObjectList();	    
	        $options = array();
	       
	        if ($items){
				$options[] = JHtml::_('select.option','0','None');	
				foreach($items as $item){					
						$options[] = JHtml::_('select.option',$item->filter_id  ,$item->title);
				}
			}else {
			 	$options[] = JHtml::_('select.option','0','None');	
			}
		$options = array_merge(parent::getOptions(), $options);		
		
		return $options;		   
        
	}
}