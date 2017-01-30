<?php
/**
 * @package 	Module Social Share Buttons
 * @version 	1.0
 * @author 		E-max
 * @copyright 	Copyright (C) 2011 - E-max
 * @license 	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

// no direct access
defined('_JEXEC') or die('Restricted access');

class SocialShareButtonsHelper{
     
    public static function getTwitter($params, $url, $title){
        
        $html = "";
        if($params->get("twitterButton")) {
            $html = '
            <div class="social-share-button-mod-tw">
            <a href="http://twitter.com/share" class="twitter-share-button" data-url="' . $url . '" data-text="' . $title . '" data-count="' . $params->get("twitterCounter") . '" data-via="' . $params->get("twitterName") . '" data-lang="' . $params->get("twitterLanguage") . '">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
            </div>
            ';
        }
        
        return $html;
    }
    
    public static function getGooglePlusOne($params, $url, $title){
        $type = "";
        $language = "";
        if($params->get("plusType")) {
            $type = 'size="' . $params->get("plusType") . '"';
        }
        
        if($params->get("plusLocale")) {
            $language = " {lang: '" . $params->get("plusLocale") . "'}";
        }
            
        $html = "";
        if($params->get("plusButton")) {
            $html = '
            <div class="social-share-button-mod-gone">
            <!-- Place this tag in your head or just before your close body tag -->
            <script type="text/javascript" src="http://apis.google.com/js/plusone.js">' . $language . '</script>
            <!-- Place this tag where you want the +1 button to render -->
            <g:plusone ' . $type . ' href="' . $url . '"></g:plusone>
            </div>
            ';
        }
        
        return $html;
    }
    
    public static function getFacebookLike($params, $url, $title){
        
        if($params->get("fbDynamicLocale", 0)) {
            $fbLocale = JFactory::getLanguage();
            $fbLocale = $fbLocale->getTag();
            $fbLocale = str_replace("-","_",$fbLocale);
        } else {
            $fbLocale = $params->get("fbLocale", "en_US");
        }

        $html = "";
        if($params->get("facebookLikeButton")) {
            
            $faces = (!$params->get("facebookLikeFaces")) ? "false" : "true";
            
            $layout = $params->get("facebookLikeType","button_count");
            if(strcmp("box_count", $layout)==0){
                $height = "80";
            } else {
                $height = "25";
            }
            
            if(!$params->get("facebookLikeRenderer")){ // iframe
                $html = '
                <div class="social-share-button-mod-fbl">
                <iframe src="http://www.facebook.com/plugins/like.php?';
                
                if($params->get("facebookLikeAppId")) {
                    $html .= 'app_id=' . $params->get("facebookLikeAppId"). '&amp;';
                }
                
                $html .= '
                href=' . rawurlencode($url) . '&amp;';
                if($params->get("facebookLikeSend")){
                    $html .= 'send="true"&amp;';
                }
				else {
                	$html .= 'send="false"&amp;';
				}
                $html .= 'locale=' . $fbLocale . '&amp;
                layout=' . $layout . '&amp;
                show_faces=' . $faces . '&amp;
                width=' . $params->get("facebookLikeWidth","450") . '&amp;
                action=' . $params->get("facebookLikeAction",'like') . '&amp;
                colorscheme=' . $params->get("facebookLikeColor",'light') . '&amp;
                height='.$height;
                if($params->get("facebookLikeFont")){
                    $html .= "&amp;font=" . $params->get("facebookLikeFont");
                }
                $html .= '
                " scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:' . $params->get("facebookLikeWidth", "450") . 'px; height:' . $height . 'px;" allowTransparency="true"></iframe>
                </div>
                ';
            } else {//XFBML
                $html = '<div class="social-share-button-mod-fbl">';
                
                if($params->get("facebookRootDiv",1)) {
                    $html .= '<div id="fb-root"></div>';
                }
                
               if($params->get("facebookLoadJsLib", 1)) {
                    $html .= '<script src="http://connect.facebook.net/' . $fbLocale . '/all.js#';
                    if($params->get("facebookLikeAppId")){
                        $html .= 'appId=' . $params->get("facebookLikeAppId"). '&amp;'; 
                    }
                    $html .= 'xfbml=1"></script>';
                }
                
                $html .= '
                <fb:like 
                href="' . $url . '" 
                layout="' . $layout . '" 
                show_faces="' . $faces . '" 
                width="' . $params->get("facebookLikeWidth","450") . '" 
                colorscheme="' . $params->get("facebookLikeColor","light") . '" ';
				if($params->get("facebookLikeSend")){
                    $html .= 'send="true" ';
                }
				else {
                	$html .= 'send="false" ';
				}
                $html .= 'action="' . $params->get("facebookLikeAction",'like') . '" ';
                
                if($params->get("facebookLikeFont")){
                    $html .= 'font="' . $params->get("facebookLikeFont") . '"';
                }
                $html .= '></fb:like>
                </div>
                ';
            }
        }
        
        return $html;
    }
    
    public static function getDigg($params, $url, $title){
        $title = html_entity_decode($title,ENT_QUOTES, "UTF-8");
        
        $html = "";
        if($params->get("diggButton")) {
            
            $html = '
            <div class="social-share-button-mod-digg">
            <script type="text/javascript">
(function() {
var s = document.createElement(\'SCRIPT\'), s1 = document.getElementsByTagName(\'SCRIPT\')[0];
s.type = \'text/javascript\';
s.async = true;
s.src = \'http://widgets.digg.com/buttons.js\';
s1.parentNode.insertBefore(s, s1);
})();
</script>
<a 
class="DiggThisButton '.$params->get("diggType","DiggCompact") . '"
href="http://digg.com/submit?url=' . rawurlencode($url) . '&amp;title=' . rawurlencode($title) . '">
</a>
            </div>
            ';
        }
        
        return $html;
    }
    
    public static function getStumbpleUpon($params, $url, $title){
        
        $html = "";
        if($params->get("stumbleButton")) {
            
            $html = '
            <div class="social-share-button-mod-su">
            <script src="http://www.stumbleupon.com/hostedbadge.php?s=' . $params->get("stumbleType",1). '&r=' . rawurlencode($url) . '"></script>
            </div>
            ';
        }
        
        return $html;
    }

    public static function getTumblr($params, $url, $title){
        
        $html = "";
        if($params->get("tumblrButton")) {
            
			if ($params->get("tumblrType") == '1') { $tumblr_style= 'width:81px; background:url(\'http://platform.tumblr.com/v1/share_1.png\')'; }
			else if ($params->get("tumblrType") == '2') { $tumblr_style= 'width:81px; background:url(\'http://platform.tumblr.com/v1/share_1T.png\')'; }
			else if ($params->get("tumblrType") == '3') { $tumblr_style= 'width:61px; background:url(\'http://platform.tumblr.com/v1/share_2.png\')'; }
			else if ($params->get("tumblrType") == '4') { $tumblr_style= 'width:61px; background:url(\'http://platform.tumblr.com/v1/share_2T.png\')'; }
			else if ($params->get("tumblrType") == '5') { $tumblr_style= 'width:129px; background:url(\'http://platform.tumblr.com/v1/share_3.png\')'; }
			else if ($params->get("tumblrType") == '6') { $tumblr_style= 'width:129px; background:url(\'http://platform.tumblr.com/v1/share_3T.png\')'; }
			else if ($params->get("tumblrType") == '7') { $tumblr_style= 'width:20px; background:url(\'http://platform.tumblr.com/v1/share_4.png\')'; }
			else if ($params->get("tumblrType") == '8') { $tumblr_style= 'width:20px; background:url(\'http://platform.tumblr.com/v1/share_4T.png\')'; }
			
            $html = '
            <div class="social-share-button-mod-tum">
			<a href="http://www.tumblr.com/share" title="Share on Tumblr" style="display:inline-block; text-indent:-9999px; overflow:hidden; height:20px; ' . $tumblr_style . ' top left no-repeat transparent;">Share on Tumblr</a>
            </div>
            ';
        }
        
        return $html;
    }
    
    public static function getReddit($params, $url, $title){
        
        $html = "";
        if($params->get("redditButton")) {
            
			if ($params->get("redditType") == '1') { $reddit_style= '<a href="http://www.reddit.com/submit" onclick="window.location = \'http://www.reddit.com/submit?url=\' + encodeURIComponent(window.location); return false"> <img src="http://www.reddit.com/static/spreddit1.gif" alt="submit to reddit" border="0" /> </a>'; }
			else if ($params->get("redditType") == '2') { $reddit_style= '<a href="http://www.reddit.com/submit" onclick="window.location = \'http://www.reddit.com/submit?url=\' + encodeURIComponent(window.location); return false"> <img src="http://www.reddit.com/static/spreddit7.gif" alt="submit to reddit" border="0" /> </a>'; }
			else if ($params->get("redditType") == '3') { $reddit_style= '<script type="text/javascript" src="http://www.reddit.com/static/button/button1.js"></script>'; }
			else if ($params->get("redditType") == '4') { $reddit_style= '<script type="text/javascript" src="http://www.reddit.com/static/button/button2.js"></script>'; }
			
            $html = '
            <div class="social-share-button-mod-red">' . $reddit_style . '</div>
            ';
        }
        
        return $html;
    }

    public static function getPinterest($params, $url, $title){
        
        $html = "";
		
		$article_title = JTable::getInstance("content");
		$article_title->load(JRequest::getInt("id"));
		
		$image_pin = "";
		$pinterestDesc = "";
		
		if ($params->get("pinterestStaticImage")) {
			$image_pin = $params->get("pinterestImage");
		}
		else {
			$first_image = "";
			$text = $article_title->get("introtext");
			$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $text, $matches);
			if ($output) {
				$image_pin = JURI::base().$matches[1][0];				
			}
			else {
				$image_pin = $params->get("pinterestImage");
			}
		}
		
		if ($params->get("pinterestStaticDesc")) {
			$pinterestDesc = $params->get("pinterestDesc");
		}
		else {
			$pinterestDesc = $article_title->get("metadesc");
			if ($pinterestDesc == "") {
				$pinterestDesc = $params->get("pinterestDesc");
			}
		}
		
        if($params->get("pinterestButton")) {
            
            $html = '
            <div class="social-share-button-mod-pin"><a href="http://pinterest.com/pin/create/button/?url=' . $url . '&media=' . $image_pin. '&description='. $pinterestDesc .'" class="pin-it-button" count-layout="' . $params->get("pinterestType","horizontal"). '"><img border="0" src="http://assets.pinterest.com/images/PinExt.png" title="Pin It" /></a><script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js"></script></div>
            ';
        }
        
        return $html;
    }

    public static function getBufferApp($params, $url, $title){
        
        $html = "";
        if($params->get("bufferappButton")) {
            
            $html = '
            <div class="social-share-button-mod-buf"><a href="http://bufferapp.com/add" class="buffer-add-button" data-count="' . $params->get("bufferappType","vertical"). '">Buffer</a><script type="text/javascript" src="http://static.bufferapp.com/js/button.js"></script></div>
            ';
        }
        
        return $html;
    }

    public static function getLinkedIn($params, $url, $title){
        
        $html = "";
        if($params->get("linkedInButton")) {
            
            $html = '
            <div class="social-share-button-mod-lin">
            <script type="text/javascript" src="http://platform.linkedin.com/in.js"></script><script type="in/share" data-url="' . $url . '" data-counter="' . $params->get("linkedInType",'right'). '"></script>
            </div>
            ';
        }
        
        return $html;
    }
    
    public static function getBuzz($params, $url, $title){
        
        $html = "";
        if($params->get("buzzButton")) {
            
            $html = '
            <div class="social-share-button-mod-buzz">
            <a title="Post to Google Buzz" class="google-buzz-button" 
            href="http://www.google.com/buzz/post" 
            data-button-style="' . $params->get("buzzType","small-count"). '" 
            data-url="' . $url . '"
            data-locale="' . $params->get("buzzLocale", "en") . '"></a>
<script type="text/javascript" src="http://www.google.com/buzz/api/button.js"></script>
            </div>
            ';
        }
        
        return $html;
    }

    public static function getReTweetMeMe($params, $url, $title){
        
        $html = "";
        if($params->get("retweetmeButton")) {
            
            $html = '
            <div class="social-share-button-mod-retweetme">
            <script type="text/javascript">
tweetmeme_url = "' . $url . '";
tweetmeme_style = "' . $params->get("retweetmeType") . '";
tweetmeme_source = "' . $params->get("twitterName") . '";
</script>
<script type="text/javascript" src="http://tweetmeme.com/i/scripts/button.js"></script>
            </div>';
        }
        
        return $html;
    }
     
    public static function getFacebookShareMe($params, $url, $title){
            
            $html = "";
            if($params->get("facebookShareMeButton")) {
                
                $html = '
                <div class="social-share-button-mod-fbsh">
                <script>var fbShare = {
    url: "' . $url . '",
    title: "' . $title . '",
    size: "' . $params->get("facebookShareMeType","large"). '",
    badge_text: "' . $params->get("facebookShareMeBadgeText","C0C0C0"). '",
    badge_color: "' . $params->get("facebookShareMeBadge","CC00FF"). '",
    google_analytics: "false"
    }</script>
    <script src="http://widgets.fbshare.me/files/fbshare.js"></script>
                </div>
                ';
            }
            
            return $html;
        }
}