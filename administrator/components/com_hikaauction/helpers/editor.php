<?php
/**
 * @package	HikaShop for Joomla!
 * @version	1.2.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class hikaauctionEditorHelper {

	protected $dbHelper;
	protected $db;
	protected static $cpt = 0;

	public $width = '100%';
	public $height = '500';
	public $cols = 100;
	public $rows = 20;
	public $editor = null;
	public $name = '';
	public $content = '';
	public $id = 'jform_articletext';

	public function __construct() {
		$this->dbHelper = hikaauction::get('helper.database');
		$this->db = $this->dbHelper->get();

		$this->setEditor();
		$this->options = array('pagebreak');
		$config = hikaauction::config();
		$readmore = $config->get('readmore', 0);
		if(!$readmore)
			$this->options[] = 'readmore';
	}

	public function setDescription() {
		$this->width = 700;
		$this->height = 200;
		$this->cols = 80;
		$this->rows = 10;
	}

	public function setContent($var) {
		$name = $this->myEditor->get('_name');
		$function = 'try{'.$this->myEditor->setContent($this->name,$var).' }catch(err){alert("Error using the setContent function of the wysiwyg editor");}';
		switch($name) {
			case 'jce':
				return ' try{JContentEditor.setContent("'.$this->name.'",'.$var.'); }catch(err){try{WFEditor.setContent("'.$this->name.'",'.$var.')}catch(err){'.$function.'} }';
			case 'fckeditor':
				return ' try{FCKeditorAPI.GetInstance("'.$this->name.'").SetHTML('.$var.'); }catch(err){'.$function.'} ';
			case 'jckeditor':
				return ' try{oEditor.setData('.$var.');}catch(err){(!oEditor) ? CKEDITOR.instances.'.$this->name.'.setData($var) : (oEditor.insertHtml='.$var.');}';
			case 'ckeditor':
				return ' try{CKEDITOR.instances.'.$this->name.'.setData('.$var.'); }catch(err){'.$function.'} ';
			case 'artofeditor':
				return ' try{CKEDITOR.instances.'.$this->name.'.setData('.$var.'); }catch(err){'.$function.'} ';
		}
		return $function;
	}

	public function getContent() {
		return $this->myEditor->getContent($this->name);
	}

	public function display() {
		if(!HIKAAUCTION_J16)
			return $this->myEditor->display($this->name, $this->content, $this->width, $this->height, $this->cols, $this->rows, $this->options);

		$id = $this->id;
		if(self::$cpt >= 1 && $this->id == 'jform_articletext') {
			$id = $this->id . '_' . self::$cpt;
		}
		self::$cpt++;
		return $this->myEditor->display($this->name, $this->content, $this->width, $this->height, $this->cols, $this->rows, $this->options, $id);
	}

	public function jsCode() {
		return $this->myEditor->save($this->name);
	}

	public function displayCode($name, $content) {
		if($this->hasCodeMirror()) {
			$this->setEditor('codemirror');
		} else {
			$this->setEditor('none');
		}
		$this->myEditor->setContent($name,$content);
		if(!HIKAAUCTION_J16)
			return $this->myEditor->display($name, $content, $this->width, $this->height, $this->cols, $this->rows, false);

		$id = $this->id;
		if(self::$cpt >= 1 && $this->id == 'jform_articletext') {
			$id = $this->id . '_' . self::$cpt;
		}
		self::$cpt++;
		return $this->myEditor->display($name, $content, $this->width, $this->height, $this->cols, $this->rows, false, $id);
	}

	public function setEditor($editor=''){
		if(empty($editor)) {
			$config = hikaauction::config();
			$this->editor = $config->get('editor',null);
			if(empty($this->editor))
				$this->editor = null;
		} else {
			$this->editor = $editor;
		}
		$this->myEditor = JFactory::getEditor($this->editor);
		$this->myEditor->initialise();
	}

	public function hasCodeMirror() {
		static $has = null;
		if(isset($has))
			return $has;

		$query = $this->dbHelper->getQuery(true);
		if(!HIKAAUCTION_J16) {
			$query->select('elements')
				->from(hikaauction::table('joomla.plugins'))
				->where('elements = '.$query->quote('codemirror').' AND folder = '.$query->quote('editors').' AND published = 1');
		} else {
			$query->select('element')
				->from(hikaauction::table('joomla.extensions'))
				->where('element = '.$query->quote('codemirror').' AND folder = '.$query->quote('editors').' AND enabled = 1 AND type = '.$query->quote('plugin'));
		}
		$this->db->setQuery($query);
		$editor = $this->db->loadResult();
		$has = !empty($editor);
		return $has;
	}
}
