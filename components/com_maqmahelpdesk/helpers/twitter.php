<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package   MaQma_Helpdesk
 * @copyright (C) 2006-2012 Components Lab, Lda.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class HelpdeskTwitter 
{

	function twitter() {
		$this->response = array();
		$this->timeout  = 5;
	}

	function shortenUrl($url,$length) {
		$output = substr($url, 0, $length);
		if(isset($url[$length]))
			$output .= '...';
		return $output;
	}

	function timeAgo($timestamp,$output = 'less than a minute ago') {
		$timestamp = time() - $timestamp;
		$units = array(604800=>'week',86400=>'day',3600=>'hour',60=>'minute');
		foreach($units as $seconds => $unit) {
			if($seconds<=$timestamp) {
				$value = floor($timestamp/$seconds);
				$output = 'about '.$value.' '.$unit.($value == 1 ? NULL : 's').' ago';
				break;
			}
		}
		return $output;
	}

	function makeUrls($text) {
		$text = preg_replace("/((http(s?):\/\/)|(www\.))([\w\.]+)([a-zA-Z0-9?&%.;:\/=+_-]+)/ie", "'<a href=\"http$3://$4$5$6\" target=\"_blank\">' . \$this->shortenUrl(\"$2$4$5$6\",25) . '</a>'", $text);
		$text = preg_replace("/(?<=\A|[^A-Za-z0-9_])@([A-Za-z0-9_]+)(?=\Z|[^A-Za-z0-9_])/", "<a href='http://twitter.com/$1' target='_blank'>$0</a>", $text);
		$text = preg_replace("/(?<=\A|[^A-Za-z0-9_])#([A-Za-z0-9_]+)(?=\Z|[^A-Za-z0-9_])/", "<a href='http://twitter.com/search?q=%23$1' target='_blank'>$0</a>", $text);
		return $text;
	}

	function makeUrlIsgd($url) {
		$url = 'http://is.gd/api.php?longurl=' . $url;
		return file_get_contents($url);
	}

	function shortenUrls($text) {
		$text = preg_replace("/((http(s?):\/\/)|(www\.))([\w\.]+)([a-zA-Z0-9?&%.;:\/=+_-]+)/ie", "'' . \$this->makeUrlIsgd(\"$2$4$5$6\") . ''", $text);
		return $text;
	}

	function limitStatus() {
		$url = 'http://twitter.com/account/rate_limit_status.json';
		return $this->objectify($this->fetch($url));
	}

	function search($text,$rows = 25,$since) {
		$url = 'http://search.twitter.com/search.json?q=' . urlencode($text) . '&rpp=' . urlencode($rows) . '&since_id=0&result_type=recent';
		return $this->fetch($url);
	}

	function fetch($url,$post = false) {
		$ch = curl_init($url);
		if($post !== false) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		#curl_setopt($ch, CURLOPT_NOBODY, 0);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, 'API Browser');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		$output = curl_exec($ch);
		$this->response = curl_getinfo($ch);
		curl_close($ch);
		if((int)$this->response['http_code'] == 200) {
			return $this->objectify($output);
		}
		else {
			return 'Error';
		}
	}

	function objectify($data) {
		return (object) json_decode($data);
	}

}
