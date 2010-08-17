<?php

// options configurable via admin page
$tb_option_names = array(
	// general configuration options
	'general_timestamp_format','general_link_screen_names','general_link_hash_tags','general_link_urls','general_seo_tweets_googleoff','general_seo_footer_googleoff',
	// options related to widget
	'widget_show_user','widget_show_photos','widget_show_source','widget_show_reply_link','widget_show_follow_link','widget_show_header','widget_check_sources',
	// options related to archive page
	'archive_show_user','archive_show_photos','archive_show_source','archive_tweets_num','archive_is_disabled','archive_show_reply_link','archive_show_follow_link','archive_auto_page','archive_keep_tweets',
	// advanced options
	'advanced_reroute_on','advanced_show_limit_msg','advanced_disable_cache','advanced_reroute_type','advanced_no_search_api',
	// filtering
	'filter_lang','filter_hide_mentions','filter_hide_replies','filter_location_name','filter_location_dist','filter_location_dist_units','filter_bad_strings',
	// database
	'db_version'
);

$tb_option_names_system = array(
	'db_version'
);

// refresh periods in seconds
$tb_refresh_periods = array(
	'Manual' => 0,
	'Only once (on load)' => 1,
	'Every 5 seconds' => 5,
	'Every 10 seconds' => 10,
	'Every 15 seconds' => 15,
	'Every 20 seconds' => 20,
	'Every 30 seconds' => 30,
	'Every minute' => 60,
	'Every 2 minutes' => 120,
	'Every 5 minutes' => 300,
	'Every 10 minutes' => 600,
	'Every 20 minutes' => 1200,
);

$tb_keep_tweets_options = array(
	'Do not delete them' => 0,
	'1 day'	=> 1,
	'2 days' => 2,
	'3 days' => 3,
	'1 week' => 7,
	'2 weeks' => 14,
	'1 month' => 30
);

$tb_languages = array(
' ' => 'any language',
'ab' => 'Abkhazian',
'ae' => 'Avestan',
'af' => 'Afrikaans',
'ak' => 'Akan',
'am' => 'Amharic',
'an' => 'Aragonese',
'ar' => 'Arabic',
'as' => 'Assamese',
'av' => 'Avaric',
'ay' => 'Aymara',
'az' => 'Azerbaijani',
'ba' => 'Bashkir',
'be' => 'Belarusian',
'bg' => 'Bulgarian',
'bh' => 'Bihari',
'bi' => 'Bislama',
'bm' => 'Bambara',
'bn' => 'Bengali',
'bo' => 'Tibetan',
'br' => 'Breton',
'bs' => 'Bosnian',
'ca' => 'Catalan; Valencian',
'ce' => 'Chechen',
'ch' => 'Chamorro',
'co' => 'Corsican',
'cr' => 'Cree',
'cs' => 'Czech',
'cv' => 'Chuvash',
'cy' => 'Welsh',
'da' => 'Danish',
'de' => 'German',
'en' => 'English',
'eo' => 'Esperanto',
'es' => 'Spanish; Castilian',
'et' => 'Estonian',
'eu' => 'Basque',
'fa' => 'Persian',
'ff' => 'Fulah',
'fi' => 'Finnish',
'fj' => 'Fijian',
'fo' => 'Faroese',
'fr' => 'French',
'fy' => 'Western Frisian',
'ga' => 'Irish',
'gl' => 'Galician',
'gn' => 'Guarani',
'gu' => 'Gujarati',
'gv' => 'Manx',
'ha' => 'Hausa',
'he' => 'Hebrew',
'hi' => 'Hindi',
'ho' => 'Hiri Motu',
'hr' => 'Croatian',
'ht' => 'Haitian; Haitian Creole',
'hu' => 'Hungarian',
'hy' => 'Armenian',
'hz' => 'Herero',
'id' => 'Indonesian',
'ie' => 'Interlingue; Occidental',
'ig' => 'Igbo',
'ii' => 'Sichuan Yi; Nuosu',
'ik' => 'Inupiaq',
'io' => 'Ido',
'is' => 'Icelandic',
'it' => 'Italian',
'iu' => 'Inuktitut',
'ja' => 'Japanese',
'jv' => 'Javanese',
'ka' => 'Georgian',
'kg' => 'Kongo',
'ki' => 'Kikuyu; Gikuyu',
'kj' => 'Kuanyama; Kwanyama',
'kk' => 'Kazakh',
'kl' => 'Kalaallisut; Greenlandic',
'km' => 'Central Khmer',
'kn' => 'Kannada',
'ko' => 'Korean',
'kr' => 'Kanuri',
'ks' => 'Kashmiri',
'ku' => 'Kurdish',
'kv' => 'Komi',
'kw' => 'Cornish',
'ky' => 'Kirghiz; Kyrgyz',
'la' => 'Latin',
'lb' => 'Luxembourgish; Letzeburgesch',
'lg' => 'Ganda',
'li' => 'Limburgan; Limburger; Limburgish',
'ln' => 'Lingala',
'lo' => 'Lao',
'lt' => 'Lithuanian',
'lu' => 'Luba-Katanga',
'lv' => 'Latvian',
'mg' => 'Malagasy',
'mh' => 'Marshallese',
'mi' => 'Maori',
'mk' => 'Macedonian',
'ml' => 'Malayalam',
'mn' => 'Mongolian',
'mr' => 'Marathi',
'ms' => 'Malay',
'mt' => 'Maltese',
'my' => 'Burmese',
'na' => 'Nauru',
'ne' => 'Nepali',
'ng' => 'Ndonga',
'nl' => 'Dutch; Flemish',
'no' => 'Norwegian',
'oj' => 'Ojibwa',
'om' => 'Oromo',
'or' => 'Oriya',
'os' => 'Ossetian; Ossetic',
'pa' => 'Panjabi; Punjabi',
'pi' => 'Pali',
'pl' => 'Polish',
'ps' => 'Pushto; Pashto',
'pt' => 'Portuguese',
'qu' => 'Quechua',
'rm' => 'Romansh',
'rn' => 'Rundi',
'ro' => 'Romanian; Moldavian; Moldovan',
'ru' => 'Russian',
'rw' => 'Kinyarwanda',
'sa' => 'Sanskrit',
'sc' => 'Sardinian',
'sd' => 'Sindhi',
'se' => 'Northern Sami',
'sg' => 'Sango',
'si' => 'Sinhala; Sinhalese',
'sk' => 'Slovak',
'sl' => 'Slovenian',
'sm' => 'Samoan',
'sn' => 'Shona',
'so' => 'Somali',
'sq' => 'Albanian',
'sr' => 'Serbian',
'ss' => 'Swati',
'su' => 'Sundanese',
'sv' => 'Swedish',
'sw' => 'Swahili',
'ta' => 'Tamil',
'te' => 'Telugu',
'tg' => 'Tajik',
'th' => 'Thai',
'ti' => 'Tigrinya',
'tk' => 'Turkmen',
'tl' => 'Tagalog',
'tn' => 'Tswana',
'to' => 'Tonga (Tonga Islands)',
'tr' => 'Turkish',
'ts' => 'Tsonga',
'tt' => 'Tatar',
'tw' => 'Twi',
'ty' => 'Tahitian',
'ug' => 'Uighur; Uyghur',
'uk' => 'Ukrainian',
'ur' => 'Urdu',
'uz' => 'Uzbek',
've' => 'Venda',
'vi' => 'Vietnamese',
'yi' => 'Yiddish',
'yo' => 'Yoruba',
'za' => 'Zhuang; Chuang',
'zh' => 'Chinese',
'zu' => 'Zulu'
);

function tb_get_url_content($url)
{
  $string = '';
  
  # preferred way is to use curl
  if (function_exists('curl_init')){
    $ch = curl_init();
  
      curl_setopt ($ch, CURLOPT_URL, $url);
      curl_setopt ($ch, CURLOPT_HEADER, 0);
  
      ob_start();
  
      curl_exec ($ch);
      curl_close ($ch);
      $string = ob_get_contents();
  
      ob_end_clean();
  }
  # plan B is to use file_get_contents
  elseif (function_exists('file_get_contents')) {
    $string = @file_get_contents($url);   
  }
  # fallback is to use fopen
  else {
    if ($fh = fopen($url, 'rb')) {
      clearstatcache();
      if ($fsize = @filesize($url)) {
        $string = fread($fh, $fsize);
      }
      else {
          while (!feof($fh)) {
            $string .= fread($fh, 8192);
          }
      }
      fclose($fh);
    }
  }
    return $string;    
}

function tb_verbal_time($timestamp) {
    $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
    $lengths = array("60","60","24","7","4.35","12","10");
   
    $now = time();
	$prefix = ""; $suffix = "";
	if ($timestamp < $now) {
		$suffix = " ago";
	}
	else {
		$prefix = "in ";
	}
	$difference = abs($now - $timestamp);
   
    for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
        $difference /= $lengths[$j];
    }
    $difference = round($difference);
   
    if($difference != 1) {
        $periods[$j] .= "s";
    }
   
    return $prefix . "$difference $periods[$j]" . $suffix;
}

// search: Wed, 27 May 2009 15:52:40 +0000
// user feed: Thu May 21 00:09:16 +0000 2009
function tb_str2time($date_string) {
	$mnum = array(
		'Jan' => 1,'Feb' => 2,'Mar' => 3,'Apr' => 4,'May' => 5,'Jun' => 6,
		'Jul' => 7,'Aug' => 8,'Sep' => 9 ,'Oct' => 10,'Nov'=>11,'Dec'=>12);
		
	if (strpos($date_string, ',') !== false) {
		list($wday,$mday, $mon, $year, $hour,$min,$sec,$offset) = preg_split('/[\s\:]/',$date_string);
	}
	else {
		list($wday,$mon,$mday,$hour,$min,$sec,$offset,$year) = preg_split('/[\s\:]/',$date_string);
	}
	
	return gmmktime($hour,$min,$sec,$mnum[$mon],$mday,$year);
}

function tb_wrap_javascript($script_content) {
	return "\n\r".'<script type="text/javascript">' . $script_content . '</script>'."\n\r";
}

function tb_get_server_rate_limit_json($tb_o) {
	
	$url = 'http://twitter.com/account/rate_limit_status.json';
	$params = array('rand' => rand());
	
	// check if it's a private source or if we are rerouting with oAuth
	if ($tb_o['advanced_reroute_on'] && $tb_o['advanced_reroute_type'] == 'oauth') {
		// check to make sure we have the class
		if (!class_exists('TwitterOAuth')) {
			return false;
		}
		// make sure we have oAuth info
		if (!isset($tb_o['oauth_access_token'])){
			return false;
		}
		else {
			// try to get it directly
			$oAuth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $tb_o['oauth_access_token']['oauth_token'],$tb_o['oauth_access_token']['oauth_token_secret']);
			$json_data = $oAuth->OAuthRequest($url, 'GET', $params);
			if ($oAuth->http_code == 200) {
				return $json_data;
			}
			else {
				return false;
			}
		}
	}
	// if not rerouting, access directly
	else {
		// for WP3 we need to explicitly include the class
		if (version_compare(get_bloginfo('version'),'3.0.0','>=')) {
			require_once ABSPATH . '/wp-includes/class-http.php'; 
		}
		$http = new WP_Http;
		$result = $http->request($url);
	
	 	// if we could get it, return data
		if (is_array($result)) {
			if ($result['response']['code'] == 200) {
				$json_data = $result['body'];
				return $json_data;
			}
			else {
				return false;
			}
		}
		elseif (is_object($result)) {
			if ($result->response->code == 200) {
				$json_data = $result->body;
				return $json_data;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
}

function tb_get_cached_tweets_json($sources) {
	global $json;
	
	$tweets = array();
	$tweets = tb_get_cached_tweets($sources, 500);
	foreach ($tweets as $t){
		$tweets[] = $json->decode($t->tweet_json);
	}
	
	return $json->encode($tweets);
}

function tb_save_cache($sources,$tweets) {
	global $wpdb, $json;

	if (is_array($tweets) || (is_object($tweets) && !version_compare(PHP_VERSION, '5.0.0', '<'))) {

		$table_name = $wpdb->prefix . "tweetblender";

		$inserted_cache = false;
		
		// process each tweet
		foreach ($tweets as $id => $tweet) {
			// if sources were not passed in separately than we are caching tweets from browser			
			if (sizeof($sources) == 0) {
				$t = $tweet->t;
				$source = urldecode($tweet->s);
				
				// if there are commas then we have multiple keywords and/or hashtags
				if (strpos($source,',') > 0) {
					$tweet_sources = split(',',$source);
				}
				// else it's an array with just one element
				else {
					$tweet_sources = array($source);
				}
			}
			// if we have multiple sources passed in (i.e. server side caching request)
			else {
				$t = $tweet;
				$tweet_sources = $sources;
			}
	
			// insert the tweet for each source		
			foreach($tweet_sources as $src) {
	
				// TODO: make sure source is in the admin defined set
				// store the tweet only if it matches this particular keyword or hashtag or if this is for list/username
				if (strpos(strtolower($t->text),strtolower($src)) !== false || strpos($src, '@') == 0) {
					
					$wpdb->query("INSERT IGNORE INTO $table_name (id,source,tweet_text,tweet_json) VALUES (" . 
						$wpdb->escape($t->id) . ", '" . 
						$wpdb->escape($src) . "','" . 
						$wpdb->escape($t->text) . "','" .
						$wpdb->escape($json->encode($t)) . "')"
					);
					
					$inserted_cache = true;
				}
			}
		}
		
		return $inserted_cache;	
	}
	else {
		return false;
	}	
}

// creates HTML for the list of tweets using cached tweets
function tb_get_cached_tweets_html($mode,$instance) {

	global $json;
	
	// get options
	$tb_o = get_option('tweet-blender');
	
	// figure out how many to get
	if ($mode == 'archive') {
		$tweets_to_show	= $tb_o['archive_tweets_num'];
		// get data for all sources
		$sources = array();
	}
	else {
		$tweets_to_show	= $instance['widget_tweets_num'];
		// get data for this widget's sources only
		$sources = preg_split('/[\n\r]/m', trim($instance['widget_sources']));
	}


	// get data from DB
	$tweets_html = '';
	$tweets = tb_get_cached_tweets($sources, $tweets_to_show);
	foreach ($tweets as $t){
		$tweet = $json->decode($t->tweet_json);
		$tweets_html .= tb_tweet_html($tweet,$mode,$tb_o);
	}
	
	return $tweets_html;
}

function tb_get_cached_tweets($sources,$tweets_num) {
	global $wpdb;
	$table_name = $wpdb->prefix . "tweetblender";

	$sources_sql = "";
	if (sizeof($sources) > 0) {
		array_walk($sources,create_function('&$v,$k','$v = stripslashes(trim($v));'));
		$sources_sql = "WHERE source IN ('" . join("','",$sources) . "')";
	}

	// get data from DB
	return $wpdb->get_results("SELECT DISTINCT id, tweet_json FROM $table_name $sources_sql ORDER BY id DESC LIMIT $tweets_num");
}

// creates all HTML for a tweet using current configuration
function tb_tweet_html($tweet,$mode = 'widget',$tb_o) {
	$tweetHTML = '';
	
	// add screen name if from_user is given
	if (!isset($tweet->user)) {
		$user = new stdClass();
		if ($tweet->from_user) {
			
			$user->screen_name = $tweet->from_user;
			$tweet->user = $user;
		}
		else {
			$user->screen_name = '';
			$tweet->user = $user;
		}
	}
	
	$tweetHTML .= '<div class="tb_tweet" id="t' . $tweet->id . '">';

	// show photo if requested
	if ($tb_o[$mode . '_show_photos']) {
		// add image url
		if (!$tweet->user->profile_image_url && $tweet->profile_image_url) {
			$tweet->user->profile_image_url = $tweet->profile_image_url;
		}

		$tweetHTML .= '<a class="tb_photo" rel="nofollow" href="http://twitter.com/' . $tweet->user->screen_name . '">';
		$tweetHTML .= '<img src="' . $tweet->user->profile_image_url . '" alt="' . $tweet->user->screen_name . '" />';
		$tweetHTML .= '</a>';
	}

	// show author
	if ($tb_o[$mode . '_show_user']) {
		$tweetHTML .= '<span class="tb_author"><a rel="nofollow" href="http://twitter.com/' . $tweet->user->screen_name . '">' . $tweet->user->screen_name . '</a>: </span> ';
	}
	
	
	$patterns = array(); $replacements = array();
	
	// link URLs if requested
	if ($tb_o['general_link_urls']) {
		$patterns[] = '/(https?:\/\/\S+)/';
		$replacements[] = '<a rel="nofollow" href="$1">$1</a>';
	}

	// link screen names if requested
	if ($tb_o['general_link_screen_names']) {
		$patterns[] = '/\@([\w]+)/';
		$replacements[] = '<a rel="nofollow" href="http://twitter.com/$1">@$1</a>';
	}
	
	// link hashtags if requested
	if ($tb_o['general_link_hash_tags']) {
		$patterns[] = '/\#([\w\-]+)/';
		$replacements[] = '<a rel="nofollow" href="http://search.twitter.com/search?q=%23$1">#$1</a>';
	}
	
	if (sizeof($patterns) > 0) {
		$tweet->text = preg_replace($patterns,$replacements,$tweet->text);
	}

	$tweetHTML .= '<span class="tb_msg">' . $tweet->text . '</span><br/>';

	// start tweet footer with info
	if (empty($tb_o['general_seo_tweets_googleoff']) && $tb_o['general_seo_footer_googleoff']) {
		$tweetHTML .= '<!--googleoff: index-->';
	}
	$tweetHTML .= ' <span class="tb_tweet-info">';
	
	// show timestamp
	$tweetHTML .= '<a rel="nofollow" href="http://twitter.com/' . $tweet->user->screen_name . '/statuses/' . $tweet->id . '">';
	$tweet_date = tb_str2time($tweet->created_at);
	if ($tb_o['general_timestamp_format']) {
		if(!version_compare(PHP_VERSION, '5.1.0', '<')) {
			date_default_timezone_set(get_option('timezone_string'));
		}
		$tweetHTML .= date($tb_o['general_timestamp_format'],$tweet_date);
	}
	else {
		$tweetHTML .= tb_verbal_time($tweet_date);
	} 
	$tweetHTML .= '</a>';
	
	// show source if requested
	if ($tb_o[$mode . '_show_source'] && $tweet->source) {
		$tweetHTML .= ' from ';
		// if source is not url encoded -> use as is
		if (strpos($tweet->source,'&lt;') === false) {
			$tweetHTML .= $tweet->source;
		}
		// else decode
		else {
			$tweetHTML .= html_entity_decode($tweet->source);
		}
	}
	
	// end tweet footer
	$tweetHTML .= '</span>';
	if (empty($tb_o['general_seo_tweets_googleoff']) && $tb_o['general_seo_footer_googleoff']) {
		$tweetHTML .= '<!--googleon: index-->';
	}
	
	// add tweet tools
	if ($tb_o[$mode . '_show_follow_link'] || $tb_o[$mode . '_show_reply_link']) {
		$tweetHTML .= '<div class="tweet-tools" style="display:none;">';
		if ($tb_o[$mode . '_show_reply_link']) {
			$tweetHTML .= '<a rel="nofollow" href="http://twitter.com/home?status=@' . $tweet->user->screen_name . '%20&in_reply_to_status_id=' . $tweet->id . '&in_reply_to=' . $tweet->user->screen_name . '">reply</a>';
		}
		if ($tb_o[$mode . '_show_follow_link'] && $tb_o[$mode . '_show_reply_link']) {
			$tweetHTML .= ' | ';
		}
		if ($tb_o[$mode . '_show_follow_link']) {
			$tweetHTML .= '<a rel="nofollow" href="http://twitter.com/' . $tweet->user->screen_name . '">follow ' . $tweet->user->screen_name . '</a>';
		}
		$tweetHTML .= '</div>'; 
	}

	// end tweet	
	$tweetHTML .= "</div>\n";
 
	return $tweetHTML;
}

function tb_page_exists($id) {
	global $wpdb;
	return $wpdb->get_row("SELECT id FROM $wpdb->posts WHERE id = $id && post_status = 'publish' && post_type = 'page'", 'ARRAY_N');
}

function tb_get_archive_post_id() {
	$tb_o = get_option('tweet-blender');
	// if archive is disabled return null
	if($tb_o['archive_is_disabled']) {
		return null;
	}

	// if we already have page id saved as option, return it
	if ($tb_o && array_key_exists('archive_page_id',$tb_o) && $tb_o['archive_page_id'] > 0 && tb_page_exists($tb_o['archive_page_id'])) {
		return $tb_o['archive_page_id'];
	}
	// else if we have such a page already, get its id and store as option
	else if ($post = get_page_by_path('tweets-archive')) {
		$tb_o['archive_page_id'] = $post->ID;
		update_option('tweet-blender',$tb_o);
		return $tb_o['archive_page_id'];
	}
	// else create such a page (unless an over-ride by user is provided)
	else if ($tb_o['archive_auto_page']) {
		if ($post_id = wp_insert_post(array(
			  'post_status' => 'publish',
			  'post_type' => 'page',
			  'post_author' => 1,
			  'post_title' => 'Twitter Feed',
			  'post_content' => 'Our twitter feed.',
			  'post_name' => 'tweets-archive'
		))) {
			$tb_o['archive_page_id'] = $post_id;
			update_option('tweet-blender',$tb_o);
			return $tb_o['archive_page_id'];
		}
		else {
			return null;
		}
	}
	else {
		return null;
	}
}

?>