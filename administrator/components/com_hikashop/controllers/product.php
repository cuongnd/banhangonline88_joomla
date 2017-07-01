<?php/** * @package    HikaShop for Joomla! * @version    2.6.3 * @author    hikashop.com * @copyright    (C) 2010-2016 HIKARI SOFTWARE. All rights reserved. * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html */defined('_JEXEC') or die('Restricted access');?><?phpclass ProductController extends hikashopController{    var $toggle = array('product_published' => 'product_id');    var $type = 'product';    var $pkey = 'product_category_id';    var $main_pkey = 'product_id';    var $table = 'product_category';    var $groupMap = 'category_id';    var $orderingMap = 'ordering';    var $groupVal = 0;    function __construct($config = array())    {        parent::__construct($config);        $this->display = array(            'unpublish', 'publish',            'listing', 'show', 'cancel',            'selectcategory', 'addcategory',            'selectrelated', 'addrelated',            'getprice', 'addimage', 'selectimage', 'addfile', 'selectfile', 'file_entry',            'variant', 'updatecart', 'export',            'galleryimage', 'galleryselect',            'selection', 'useselection',            'getTree', 'findTree', '',            'get_code',            'add_product_vatgia_to_database',            'ajax_get_list_product_by_category_id',            'jsonp_add_product_vatgia_to_database'        );        $this->modify = array_merge($this->modify, array(            'managevariant', 'variants', 'save_translation', 'copy', 'characteristic'        ));        $this->modify_views = array_merge($this->modify_views, array(            'edit_translation', 'priceaccess', 'unpublish', 'publish'        ));        if (JRequest::getInt('variant'))            $this->publish_return_view = 'variant';    }    function edit()    {        JRequest::setVar('hidemainmenu', 1);        JRequest::setVar('layout', 'form');        if (JRequest::getInt('legacy', 0) == 1)            JRequest::setVar('layout', 'form_legacy');        return $this->display();    }    function priceaccess()    {        JRequest::setVar('layout', 'priceaccess');        return parent::display();    }    function get_code()    {        $input = JFactory::getApplication()->input;        $post = json_decode(file_get_contents('php://input'));        $title = $post->title;        $productHelper = hikashop_get('helper.product');        $title=$productHelper->get_code($title);        echo new JResponseJson($title, null, false, $input->get('ignoreMessages', true, 'bool'));    }    function add_product_vatgia_to_database()    {        $db = JFactory::getDbo();        $input = JFactory::getApplication()->input;        $post=(object)$input->getArray();        $product=(object)$post->product;        $product->product_description=base64_decode($product->product_description);        $product->src_image=rawurldecode($product->src_image);        $response=new stdClass();        if($product->src_image==''){            $response->error=1;            $response->m="no image";        }elseif($product->product_price==''){            $response->error=1;            $response->m="no product_price";        }elseif($product->product_name==''){            $response->error=1;            $response->m="no product_name";        }else {            $query = $db->getQuery(true);            $query->select('product_id')                ->from('#__hikashop_product')                ->where('product_name=' . $query->q($product->product_name));            $product_id = $db->setQuery($query)->loadResult();            if ($product_id > 0) {                $response->error=1;                $response->m="exists product_name id:$product_id";            }else{                $vendor_name = $product->vendor_name;                $vendor_name = strtolower(trim($vendor_name));                $query->clear();                $query->select('vendor_id')                    ->from('#__hikamarket_vendor')                    ->where('LOWER(vendor_name)=' . $query->q($vendor_name));                $vendor_id = $db->setQuery($query)->loadResult();                $productHelper = hikashop_get('helper.product');                $discountHelper = hikashop_get('class.discount');                $product->product_code = $productHelper->get_code($product->product_name,9999);                $query->clear();                $query->insert('#__hikashop_product')                    ->set('product_id=0')                    ->set('product_name=' . $query->q($product->product_name))                    ->set('product_description=' . $query->q($product->product_description))                    ->set('product_code=' . $query->q($product->product_code))                    ->set('product_meta_description=' . $query->q($product->meta_description))                    ->set('product_keywords=' . $query->q($product->product_keywords))                    ->set('product_published=1')                    ->set('product_total_vote='.rand (10, 5000))                    ->set('product_quantity=-1')                    ->set('product_vendor_id=' . (int)$vendor_id)                    ->set('product_type=' . $query->q('main'))                    ->set('product_created=' . $query->q(JFactory::getDate()->toSql()))                    ->set('product_alias=' . $query->q($productHelper->get_alias($product->product_name)));                $db->setQuery($query)->execute();                $product_id = $db->insertid();                $query->clear();                $query->insert('#__hikashop_product_category')                    ->set('category_id=' . (int)$product->category_id)                    ->set('product_id=' . (int)$product_id);                $db->setQuery($query)->execute();                $query->clear();                $query->insert('#__hikashop_price')                    ->set('price_id=0')                    ->set('price_currency_id=175')                    ->set('price_product_id=' . (int)$product_id)                    ->set('price_value=' . (float)$product->product_price)                    ->set('price_min_quantity=0')                    ->set('price_access=' . $query->q('all'));                $db->setQuery($query)->execute();                //if promotion                if($product->price_promotion){                    $discount_start =JFactory::getDate()->getTimestamp();                    $discount_end=JFactory::getDate($product->price_promotion_time)->getTimestamp();                    $query->clear();                    $query->insert('#__hikashop_discount')                        ->set('discount_id=0')                        ->set('discount_published=1')                        ->set('discount_target_vendor=1')                        ->set('discount_type=' . $query->q('discount'))                        ->set('discount_start=' . $discount_start)                        ->set('discount_end=' . $discount_end)                        ->set('discount_code=' . $query->q($discountHelper->render_discount_code()))                        ->set('discount_access=' . $query->q('all'))                        ->set('discount_flat_amount=' . (float)($product->product_price-$product->price_promotion))                        ->set('discount_product_id=' . (int)$product_id)                        ->set('discount_currency_id=175' )                        ;                    $db->setQuery($query)->execute();                }                //download image                $file_image_product_name = basename($product->src_image);                $file_image_product_name = str_replace('-', '_', $file_image_product_name);                $file_image_product_path = 'images/com_hikashop/upload' . DS . $file_image_product_name;                $response->image_path = JUri::root() . $file_image_product_path;                if (!JFile::write(JPATH_ROOT . DS . $file_image_product_path, file_get_contents($product->src_image))) {                    $response->wrire_error = 1;                }                $query->clear();                $query->insert('#__hikashop_file')                    ->set('file_id=0')                    ->set('file_name=' . $query->q($product->product_name))                    ->set('file_type=' . $query->q('product'))                    ->set('file_ref_id=' . (int)$product_id)                    ->set('file_path=' . $query->q($file_image_product_name));                $db->setQuery($query)->execute();            }        }        echo json_encode($response);        die;    }    function jsonp_add_product_vatgia_to_database()    {        $config=JFactory::getConfig();        $is_localhost=$config->get('is_localhost',0);        if($is_localhost) {            $db = JFactory::getDbo();            $input = JFactory::getApplication()->input;            $ch = curl_init();            $post = (object)$input->getArray();            $product=(object)$post->product;            //download image            $product->src_image=rawurldecode($product->src_image);            $file_image_product_name = basename($product->src_image);            $file_image_product_name = str_replace('-', '_', $file_image_product_name);            $file_image_product_path = 'images/com_hikashop/upload' . DS . $file_image_product_name;            if (!JFile::write(JPATH_ROOT . DS . $file_image_product_path, file_get_contents($product->src_image))) {            }            $post->product['src_image_content']='@'.JPATH_ROOT . DS . $file_image_product_path;            $post->task = 'front_end_jsonp_add_product_vatgia_to_database';            $post = http_build_query($post);            curl_setopt($ch, CURLOPT_URL, "http://banhangonline88.com/index.php");            curl_setopt($ch, CURLOPT_POST, 1);            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);// in real life you should use something like:// curl_setopt($ch, CURLOPT_POSTFIELDS,//          http_build_query(array('postvar1' => 'value1')));// receive server response ...            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);            $server_output = curl_exec($ch);            curl_close($ch);            echo $server_output;            //JFile::delete(JPATH_ROOT . DS . $file_image_product_path);        }        die;    }    function edit_translation()    {        JRequest::setVar('layout', 'edit_translation');        if (JRequest::getInt('legacy', 0) == 1)            JRequest::setVar('layout', 'edit_translation_legacy');        return parent::display();    }    function save_translation()    {        $product_id = hikashop_getCID('product_id');        $productClass = hikashop_get('class.product');        $element = $productClass->get($product_id);        if (!empty($element->product_id)) {            $translationHelper = hikashop_get('helper.translation');            $translationHelper->getTranslations($element);            $translationHelper->handleTranslations('product', $element->product_id, $element);        }        $document = JFactory::getDocument();        $document->addScriptDeclaration('window.top.hikashop.closeBox();');    }    function managevariant()    {        $id = $this->store();        if ($id) {            JRequest::setVar('cid', $id);            $this->variant();        } else {            $this->edit();        }    }    function updatecart()    {        echo '<textarea style="width:100%" rows="5"><a class="hikashop_html_add_to_cart_link" href="' . HIKASHOP_LIVE . 'index.php?option=' . HIKASHOP_COMPONENT . '&ctrl=product&task=updatecart&quantity=1&checkout=1&product_id=' . JRequest::getInt('cid') . '">' . JText::_('ADD_TO_CART') . '</a></textarea>';    }    function save()    {        $result = parent::store();        if (!$result)            return $this->edit();        if (JRequest::getBool('variant')) {            JRequest::setVar('cid', JRequest::getInt('parent_id'));            $this->variant();        } else {            $this->listing();        }    }    function save2new()    {        $result = $this->store(true);        if ($result)            JRequest::setVar('product_id', 0);        return $this->edit();    }    function copy()    {        $products = JRequest::getVar('cid', array(), '', 'array');        $result = true;        if (!empty($products)) {            $importHelper = hikashop_get('helper.import');            foreach ($products as $product) {                if (!$importHelper->copyProduct($product))                    $result = false;            }        }        if ($result) {            $app = JFactory::getApplication();            if (!HIKASHOP_J30)                $app->enqueueMessage(JText::_('HIKASHOP_SUCC_SAVED'), 'success');            else                $app->enqueueMessage(JText::_('HIKASHOP_SUCC_SAVED'));        }        return $this->listing();    }    function variant()    {        hikashop_nocache();        JRequest::setVar('layout', 'variant');        $legacy = JRequest::getInt('legacy', 0);        if ($legacy)            JRequest::setVar('layout', 'variant_legacy');        if (JRequest::getCmd('tmpl', '') == 'component') {            ob_end_clean();            parent::display();            exit;        }        return parent::display();    }    public function variants()    {        hikashop_nocache();        JRequest::setVar('layout', 'form_variants');        $product_id = JRequest::getInt('product_id', 0);        $subtask = JRequest::getCmd('subtask', '');        if (!empty($subtask)) {            switch ($subtask) {                case 'setdefault':                    $variant_id = JRequest::getInt('variant_id');                    $productClass = hikashop_get('class.product');                    $ret = $productClass->setDefaultVariant($product_id, $variant_id);                    break;                case 'publish':                    $variant_id = JRequest::getInt('variant_id');                    $productClass = hikashop_get('class.product');                    $ret = $productClass->publishVariant($variant_id);                    break;                case 'add':                case 'duplicate':                    JRequest::checkToken('request') || die('Invalid Token');                    JRequest::setVar('layout', 'form_variants_add');                    break;                case 'delete';                    JRequest::checkToken('request') || die('Invalid Token');                    $cid = JRequest::getVar('cid', array(), '', 'array');                    if (empty($cid)) {                        ob_end_clean();                        echo '0';                        exit;                    }                    $productClass = hikashop_get('class.product');                    $ret = $productClass->deleteVariants($product_id, $cid);                    ob_end_clean();                    if ($ret !== false)                        echo $ret;                    else                        echo '0';                    exit;                case 'populate':                    JRequest::checkToken('request') || die('Invalid Token');                    JRequest::setVar('layout', 'form_variants_add');                    $productClass = hikashop_get('class.product');                    $data = JRequest::getVar('data', array(), '', 'array');                    if (isset($data['variant_duplicate'])) {                        $cid = JRequest::getVar('cid', array(), '', 'array');                        JArrayHelper::toInteger($cid);                        $ret = $productClass->duplicateVariant($product_id, $cid, $data);                    } else                        $ret = $productClass->populateVariant($product_id, $data);                    if ($ret !== false) {                        ob_end_clean();                        echo $ret;                        exit;                    }                    break;            }        }        if (JRequest::getCmd('tmpl', '') == 'component') {            ob_end_clean();            parent::display();            exit;        }        return parent::display();    }    public function characteristic()    {        if (!hikashop_acl('product/edit/variants'))            return false;        $product_id = hikashop_getCID('product_id');        $subtask = JRequest::getCmd('subtask', '');        if (empty($subtask)) {        }        $productClass = hikashop_get('class.product');        switch ($subtask) {            case 'add':                JRequest::checkToken() || die('Invalid Token');                $characteristic_id = JRequest::getInt('characteristic_id', 0);                $characteristic_value_id = JRequest::getInt('characteristic_value_id', 0);                $ret = $productClass->addCharacteristic($product_id, $characteristic_id, $characteristic_value_id);                ob_end_clean();                if ($ret === false)                    echo '-1';                else                    echo (int)$ret;                exit;            case 'remove':                JRequest::checkToken() || die('Invalid Token');                $characteristic_id = JRequest::getInt('characteristic_id', 0);                $ret = $productClass->removeCharacteristic($product_id, $characteristic_id);                ob_end_clean();                if ($ret === false)                    echo '-1';                else                    echo (int)$ret;                exit;        }        exit;    }    function export()    {        JRequest::setVar('layout', 'export');        return parent::display();    }    function orderdown()    {        $this->getGroupVal();        return parent::orderdown();    }    function orderup()    {        $this->getGroupVal();        return parent::orderup();    }    function saveorder()    {        $this->getGroupVal();        return parent::saveorder();    }    function getGroupVal()    {        $app = JFactory::getApplication();        $this->groupVal = $app->getUserStateFromRequest(HIKASHOP_COMPONENT . '.product.filter_id', 'filter_id', 0, 'string');        if (!is_numeric($this->groupVal)) {            $categoryClass = hikashop_get('class.category');            $categoryClass->getMainElement($this->groupVal);        }    }    function selectcategory()    {        JRequest::setVar('layout', 'selectcategory');        return parent::display();    }    function addcategory()    {        JRequest::setVar('layout', 'addcategory');        return parent::display();    }    function selectrelated()    {        JRequest::setVar('layout', 'selectrelated');        return parent::display();    }    function addrelated()    {        JRequest::setVar('layout', 'addrelated');        return parent::display();    }    function addimage()    {        $this->_saveFile();        JRequest::setVar('layout', 'addimage');        return parent::display();    }    function selectimage()    {        JRequest::setVar('layout', 'selectimage');        return parent::display();    }    function addfile()    {        $ret = $this->_saveFile();        if ($ret)            JRequest::setVar('layout', 'addfile');        else            JRequest::setVar('layout', 'selectfile');        return parent::display();    }    function getSizeFile($url)    {        if (substr($url, 0, 4) != 'http')            return @filesize($url);        static $regex = '/^Content-Length: *+\K\d++$/im';        if (!$fp = @fopen($url, 'rb'))            return false;        if (isset($http_response_header) && preg_match($regex, implode("\n", $http_response_header), $matches))            return (int)$matches[0];        return strlen(stream_get_contents($fp));    }    function _saveFile()    {        $file = new stdClass();        $file->file_id = hikashop_getCID('file_id');        $formData = JRequest::getVar('data', array(), '', 'array');        foreach ($formData['file'] as $column => $value) {            hikashop_secureField($column);            $file->$column = strip_tags($value);        }        unset($file->file_path);        $filemode = 'upload';        if (!empty($formData['filemode']))            $filemode = $formData['filemode'];        if (!empty($file->file_id))            $filemode = null;        $fileClass = hikashop_get('class.file');        JRequest::setVar('cid', 0);        switch ($filemode) {            case 'upload':                if (empty($file->file_id)) {                    $ids = $fileClass->storeFiles($file->file_type, $file->file_ref_id);                    if (is_array($ids) && !empty($ids)) {                        $file->file_id = array_shift($ids);                        if (isset($file->file_path))                            unset($file->file_path);                    } else                        return false;                }                break;            case 'path':            default:                if (isset($formData['filepath']))                    $file->file_path = trim($formData['filepath']);                if (isset($formData['file']['file_path']))                    $file->file_path = trim($formData['file']['file_path']);                if (empty($file->file_id) && (substr($file->file_path, 0, 7) == 'http://' || substr($file->file_path, 0, 8) == 'https://')) {                    $parts = explode('/', $file->file_path);                    $name = array_pop($parts);                    $config =& hikashop_config();                    $uploadFolder = ltrim(JPath::clean(html_entity_decode($config->get('uploadfolder'))), DS);                    $uploadFolder = rtrim($uploadFolder, DS) . DS;                    $secure_path = JPATH_ROOT . DS . $uploadFolder;                    if (!file_exists($secure_path . $name)) {                        $data = @file_get_contents($file->file_path);                        if (empty($data)) {                            $app = JFactory::getApplication();                            $app->enqueueMessage('The file could not be retrieved.');                            return false;                        }                        JFile::write($secure_path . $name, $data);                    } else {                        $size = $this->getSizeFile($file->file_path);                        if ($size != filesize($secure_path . $name)) {                            $name = $size . '_' . $name;                            if (!file_exists($secure_path . $name))                                JFile::write($secure_path . $name, file_get_contents($file));                        }                    }                    $file->file_path = $name;                }                break;        }        if (isset($file->file_path)) {            $app = JFactory::getApplication();            if (strpos($file->file_path, '..') !== false) {                $app->enqueueMessage('Invalid data', 'error');                return false;            }            $firstChar = substr($file->file_path, 0, 1);            $isVirtual = in_array($firstChar, array('#', '@'));            $isLink = (substr($file->file_path, 0, 7) == 'http://' || substr($file->file_path, 0, 8) == 'https://');            if (!$isLink && !$isVirtual) {                $app = JFactory::getApplication();                $config = hikashop_config();                if ($firstChar == '/' || preg_match('#:[\/\\\]{1}#', $file->file_path)) {                    $clean_filename = JPath::clean($file->file_path);                    $secure_path = $config->get('uploadsecurefolder');                    if ((JPATH_ROOT != '') && strpos($clean_filename, JPath::clean(JPATH_ROOT)) !== 0 && strpos($clean_filename, JPath::clean($secure_path)) !== 0) {                        $app->enqueueMessage('The file path you entered is an absolute path but it is outside of your upload folder: ' . JPath::clean($secure_path), 'error');                        return false;                    }                    if (!file_exists($file->file_path)) {                        $app->enqueueMessage('The file path you entered is an absolute path but it doesn\'t exist.', 'error');                        return false;                    }                } else {                    $secure_path = $config->get('uploadsecurefolder');                    $clean_filename = JPath::clean($secure_path . '/' . $file->file_path);                    if (!JFile::exists($clean_filename) && (JPATH_ROOT == '' || !JFile::exists(JPATH_ROOT . DS . $clean_filename))) {                        $app->enqueueMessage('File does not exists', 'error');                        return false;                    }                }            }        }        if (isset($file->file_ref_id) && empty($file->file_ref_id))            unset($file->file_ref_id);        if (isset($file->file_limit)) {            $limit = (int)$file->file_limit;            if ($limit == 0 && $file->file_limit !== 0 && $file->file_limit != '0')                $file->file_limit = -1;            else                $file->file_limit = $limit;        }        JPluginHelper::importPlugin('hikashop');        $dispatcher = JDispatcher::getInstance();        $do = true;        $dispatcher->trigger('onHikaBeforeFileSave', array(&$file, &$do));        if (!$do)            return false;        if (empty($file->file_path) && empty($file->file_id)) {            return false;        }        $status = $fileClass->save($file);        if (empty($file->file_id)) {            $file->file_id = $status;        }        JRequest::setVar('cid', $file->file_id);        $dispatcher->trigger('onHikaAfterFileSave', array(&$file));        return true;    }    function selectfile()    {        JRequest::setVar('layout', 'selectfile');        return parent::display();    }    function galleryimage()    {        JRequest::setVar('layout', 'galleryimage');        return parent::display();    }    public function file_entry()    {        if (!hikashop_acl('product/edit'))            return false; // hikashop_deny('product', JText::sprintf('HIKAM_ACTION_DENY', JText::_('HIKAM_ACT_PRODUCT_EDIT')));        JRequest::setVar('layout', 'form_file_entry');        parent::display();        exit;    }    function galleryselect()    {        $formData = JRequest::getVar('data', array(), '', 'array');        $filesData = JRequest::getVar('files', array(), '', 'array');        $fileClass = hikashop_get('class.file');        $file = new stdClass();        foreach ($formData['file'] as $column => $value) {            hikashop_secureField($column);            $file->$column = strip_tags($value);        }        $file->file_path = reset($filesData);        if (isset($file->file_ref_id) && empty($file->file_ref_id)) {            unset($file->file_ref_id);        }        $status = $fileClass->save($file);        if (empty($file->file_id)) {            $file->file_id = $status;        }        JRequest::setVar('cid', $file->file_id);        JRequest::setVar('layout', 'addimage');        return parent::display();    }    function getprice()    {        $price = JRequest::getVar('price');        $productClass = hikashop_get('class.product');        $price = hikashop_toFloat($price);        $tax_id = JRequest::getInt('tax_id');        $conversion = JRequest::getInt('conversion');        $currencyClass = hikashop_get('class.currency');        $config =& hikashop_config();        $main_tax_zone = explode(',', $config->get('main_tax_zone', 1346));        $newprice = $price;        if (count($main_tax_zone) && !empty($tax_id) && !empty($price) && !empty($main_tax_zone)) {            $function = 'getTaxedPrice';            if ($conversion) {                $function = 'getUntaxedPrice';            }            $newprice = $currencyClass->$function($price, array_shift($main_tax_zone), $tax_id, 5);        }        echo $newprice;        exit;    }    function remove()    {        $cids = JRequest::getVar('cid', array(), '', 'array');        $variant = JRequest::getInt('variant');        $class = hikashop_get('class.' . $this->type);        $num = $class->delete($cids);        $app = JFactory::getApplication();        $app->enqueueMessage(JText::sprintf('SUCC_DELETE_ELEMENTS', $num), 'message');        if ($variant) {            JRequest::setVar('cid', JRequest::getInt('parent_id'));            return $this->variant();        }        return $this->listing();    }    function selection()    {        JRequest::setVar('layout', 'selection');        return parent::display();    }    function useselection()    {        JRequest::setVar('layout', 'useselection');        return parent::display();    }    public function getUploadSetting($upload_key, $caller = '')    {        if (!hikashop_acl('product/edit'))            return false;        $product_id = JRequest::getInt('product_id', 0);        if (empty($upload_key))            return false;        $upload_value = null;        $upload_keys = array(            'product_image' => array(                'type' => 'image',                'view' => 'form_image_entry',                'file_type' => 'product',            ),            'product_file' => array(                'type' => 'file',                'view' => 'form_file_entry',                'file_type' => 'file'            ),        );        if (empty($upload_keys[$upload_key]))            return false;        $upload_value = $upload_keys[$upload_key];        $config = hikashop_config(false);        $options = array();        if ($upload_value['type'] == 'image') {            $options['upload_dir'] = $config->get('uploadfolder');            $options['processing'] = 'resize';        } else            $options['upload_dir'] = $config->get('uploadsecurefolder');        $options['max_file_size'] = null;        $product_type = JRequest::getCmd('product_type', 'product');        if (!in_array($product_type, array('product', 'variant')))            $product_type = 'product';        return array(            'limit' => 1,            'type' => $upload_value['type'],            'layout' => 'product',            'view' => $upload_value['view'],            'options' => $options,            'extra' => array(                'product_id' => $product_id,                'file_type' => $upload_value['file_type'],                'product_type' => $product_type            )        );    }    public function manageUpload($upload_key, &$ret, $uploadConfig, $caller = '')    {        if (empty($ret))            return;        $config = hikashop_config();        $product_id = (int)$uploadConfig['extra']['product_id'];        $file_type = 'product';        if (!empty($uploadConfig['extra']['file_type']))            $file_type = $uploadConfig['extra']['file_type'];        $sub_folder = '';        if (!empty($uploadConfig['options']['sub_folder']))            $sub_folder = str_replace('\\', '/', $uploadConfig['options']['sub_folder']);        if ($file_type == 'product')            $ret->params->product_type = JRequest::getCmd('product_type', 'product');        if ($caller == 'upload' || $caller == 'addimage') {            $file = new stdClass();            $file->file_description = '';            $file->file_name = $ret->name;            $file->file_type = $file_type;            $file->file_ref_id = $product_id;            $file->file_path = $sub_folder . $ret->name;            if (strpos($file->file_name, '.') !== false) {                $file->file_name = substr($file->file_name, 0, strrpos($file->file_name, '.'));            }            if ($file_type != 'product') {                $file->file_free_download = $config->get('upload_file_free_download', false);                $file->file_limit = 0;            }            $fileClass = hikashop_get('class.file');            $status = $fileClass->save($file, $file_type);            $ret->file_id = $status;            $ret->params->file_id = $status;            if ($file_type != 'product') {                $ret->params->file_free_download = $file->file_free_download;                $ret->params->file_limit = $file->file_limit;                $ret->params->file_size = @filesize($uploadConfig['upload_dir'] . @$uploadConfig['options']['sub_folder'] . $file->file_name);            }            return;        }        if ($caller == 'galleryselect') {            $file = new stdClass();            $file->file_type = 'product';            $file->file_ref_id = $product_id;            $file->file_path = $sub_folder . $ret->name;            $fileClass = hikashop_get('class.file');            $status = $fileClass->save($file);            $ret->file_id = $status;            $ret->params->file_id = $status;            return;        }    }    function getTree()    {        while (ob_get_level())            @ob_end_clean();        $category_id = JRequest::getInt('category_id', 0);        $displayFormat = JRequest::getVar('displayFormat', '');        $search = JRequest::getVar('search', null);        $nameboxType = hikashop_get('type.namebox');        $options = array(            'start' => $category_id,            'displayFormat' => $displayFormat        );        $ret = $nameboxType->getValues($search, $this->type, $options);        if (!empty($ret)) {            echo json_encode($ret);            exit;        }        echo '[]';        exit;    }    function findTree()    {        return $this->getTree();    }}