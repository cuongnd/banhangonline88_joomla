<?php
/**
 * @package ZT Tabs module
 * @author DucNA
 * @copyright (C) 2014- ZooTemplate.Com
 * @license PHP files are GNU/GPL
 **/
defined( '_JEXEC' ) or die( 'Access Deny' );
$document = JFactory::getDocument();
$uri = JURI::getInstance();

$document->addStyleSheet($uri->root().'modules/mod_zt_tabs/assets/css/tabs.css');
$document->addStyleSheet($uri->root().'modules/mod_zt_tabs/assets/css/style.css');
$document->addStyleSheet('//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css');

    $tabStyle =  $this->parsedData['tab_style'];
    $titlePosition =   $this->parsedData['title_position'];

if(isset($tabStyle) &&isset($titlePosition)) {
    $document->addStyleSheet($uri->root().'modules/mod_zt_tabs/assets/css/'.$tabStyle.'-'.$titlePosition.'.css');

}

$jversion = new JVersion;
$current_version = $jversion->getShortVersion();
if (version_compare($current_version, '3.0.0') <= 0){
    $document->addScript($uri->root().'modules/mod_zt_tabs/assets/vendor/jquery/jquery-1.9.1.js');
    $document->addScript($uri->root().'modules/mod_zt_tabs/assets/vendor/jquery/jquery.noConflict.js');
    $document->addScript($uri->root().'modules/mod_zt_tabs/assets/vendor/bootstrap/js/bootstrap.js');
    $document->addScript($uri->root().'modules/mod_zt_tabs/assets/vendor/jquery/fixConflict.js');
}
?>

<?php
    $tab_alignment = '';
    if($this->parsedData['title_position']== 'top' or $this->parsedData['title_position']== 'bot') {
        $tab_alignment = $this->parsedData['tab_alignment'];
    }
    $tabs =  $this->parsedData['arrayTabs'];

    $str = '';
    $str .= '';
    $str .= '<div id="zt-module-tabs">';
    //list title tabs
    $strUl = '';
    $strUl .= '<ul class="nav nav-tabs zt-tabs '.$tab_alignment.'">';

    foreach($tabs as $key=>$tab) {
        $strUl .= ($key==0)? '<li class="active">' : '<li>';
        $strUl .= '<a href="#'.$tab[0].$tab[1].'" data-toggle="tab" style="width:'.$this->parsedData['tWidth'].'; height:'.$this->parsedData['tHeight'].'">';

        if($tab[0] == 'module') {
            $strUl .= $this->getModuleTitleById($tab[1]);
        } elseif($tab[0] == 'category') {
            $strUl .= $this->getCategoryTileById($tab[1]);
        }
        $strUl .= '</a>';
        $strUl .= '</li>';

    }
//end foreach - title
    $strUl .= '</ul>';


    //end list title tabs
    //list content tabs
    $strContent = '';
    $strContent .= '<div class="tab-content">';

    foreach($tabs as $key=>$tab) {



        if($key==0) {
            if($this->parsedData['effect_type'] =='fade') {
                $effect = 'fade in active';
            } else {
                $effect = 'active';
            }

        } else {
            if($this->parsedData['effect_type'] =='fade') {
                $effect = 'fade';
            } else {
                $effect = '';
            }
        }
        //var_dump($effect.' '.$active);
        // content module
        if($tab[0] == 'module') {
            $strContent .= '<div class="tab-pane '.$effect.'" id="'.$tab[0].$tab[1].'">';

            $moduleItem = $this->parseTabModuleById($tab[1]);
            $showModule =JModuleHelper::renderModule ( $moduleItem );
            $strContent .= $showModule;
            $strContent .= '</div>';


        }elseif($tab[0] == 'category'){
            //content category
            $listArticles = $this->getListContentArticle($tab[1]);
            if(isset($listArticles) and count($listArticles)!=0){
                $strContent .= '<div class="tab-pane '.$effect.'" id="'.$tab[0].$tab[1].'">';

                $countList = count($listArticles) -1;
                foreach($listArticles as $keyList=>$list){
                    $borderArticle =($keyList == $countList)?' style="border: none" ': '';
                    $strContent .= '<div class="zt-article" '.$borderArticle.'>';
                    if($this->parsedData['showIntroImage']=='display') {
                        if(isset($list->introImage) and $list->introImage !=''){
                            $strContent .= '<div class="zt-intro-img">';
                            $strContent .= '<a href="'.$list->link.'">';
                            $strContent .= '<img src="'.$uri->root().$list->introImage.'"  width="'.$this->parsedData['intro_image_width'].'" height="'.$this->parsedData['intro_image_height'].'" />';
                            $strContent .= '</a>';
                            $strContent .= '</div>';//end intro-img
                        }
                    }


                    // container
                    $strContent .= '<div class="zt-container">';
                    // title
                    $strContent .= '<div class="zt-title">';
                    $strContent .= '<h4>';
                    $strContent .= '<a href="'.$list->link.'">';
                    $strContent .= $list->title;
                    $strContent .= '</a>';
                    $strContent .= '</h4>';
                    $strContent .= '</div>';//end title

                    // intro-text
                    $strContent .= '<div class="zt-intro-text">';
                    $strContent .= $list->introText;
                    $strContent .= '</div>';//end intro-text

                    // created day
                    $strContent .= '<div class="zt-create">';
                    $strContent .= '<p>'.$list->created.'<i class="fa fa-pencil"></i> '.$list->user.'</p>';
                    $strContent .= '</div>';

                    $strContent .= '</div>';//end container
                    $strContent .= '</div>';//end article


                }
                $strContent .= '</div>';
            }

        }
    }
    $strContent .= '</div>';
    //end foreach - content
    //end list content tabs
if($titlePosition =='bot') {
    $str.= $strContent . $strUl ;

} else {
    $str.= $strUl . $strContent  ;

}
    $str .= '</div>'; // end div - zt-module
    $str .= '';

echo $str;
?>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('#zt-tabs a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        });
    });

</script>


