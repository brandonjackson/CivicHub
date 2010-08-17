<?php

// add hook for scripts needed for admin page
add_action( 'admin_print_scripts', 'tb_admin_load_scripts' );
function tb_admin_load_scripts() {
	// include our scripts only on our own admin page
	if ($_GET['page'] == 'tweet-blender/admin-page.php') {
		wp_enqueue_script('jq', '/' . PLUGINDIR . '/tweet-blender/js/jquery-1.3.2.min.js');
		wp_enqueue_script('jq-ui', '/' . PLUGINDIR . '/tweet-blender/js/jquery-ui.js');
		wp_enqueue_script('jq-ui-tabs', '/' . PLUGINDIR . '/tweet-blender/js/ui.tabs.js');
	}
}

// register admin page
add_action('admin_menu', 'tb_admin_menu');
function tb_admin_menu() {
	add_options_page('Tweet Blender Settings', 'Tweet Blender', 8, __FILE__, 'tb_admin_page');
}
function tb_admin_page() {
 
 	global $tb_option_names, $tb_option_names_system, $tb_keep_tweets_options, $tb_languages, $cache_clear_results;

    // Read in existing option values from database
	$tb_o = get_option('tweet-blender');
		
	// set defaults
	if ($tb_o['archive_tweets_num'] < 1) {
		$tb_o['archive_tweets_num'] = 20;
	}

	// get API limit info
	$api_limit_data = null;
	if ($json_data = tb_get_server_rate_limit_json($tb_o)) {
		$json = new Services_JSON();
    	$api_limit_data = $json->decode($json_data);
	}

	// perform maintenance
	if ($tb_o['archive_keep_tweets'] > 0) {
		tb_db_cache_clear('WHERE DATEDIFF(now(),created_at) > ' . $tb_o['archive_keep_tweets']);
	}
					
    // See if the user has posted us some information
    if( $_POST['tb_new_data'] == 'Y' ) {

		// check nonce
		check_admin_referer('tweet-blender_settings-save');

		// if we are disabling cache - clear it
		if (!$tb_o['advanced_disable_cache'] && $_POST['advanced_disable_cache']) {
			tb_db_cache_clear();
		}
		// if we are clearing individual cached sources
		if ($_POST['delete_cache_src']) {
			$cleared_sources = array();
			foreach ($_POST['delete_cache_src'] as $del_src) {
				tb_db_cache_clear("WHERE source='$del_src'");
				$cleared_sources[] = $del_src;
			}
			if (sizeof($cleared_sources) > 0 ) {
				$cache_clear_results = 'Cleared cached tweets for ' . implode(', ',$cleared_sources);
			}
		}
		
		// check if we are rerouting with oAuth
		if ($_POST['advanced_reroute_on'] && $_POST['advanced_reroute_type'] == 'oauth') {
			
			if(!isset($tb_o['oauth_access_token'])) {
				// Create TwitterOAuth object and get request token
				$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
				 
				// Get request token
				$request_token = $connection->getRequestToken(get_bloginfo('url') . '/' . PLUGINDIR . "/tweet-blender/lib/twitteroauth/callback.php");
				 
				if ($connection->http_code == 200) {
					// Save request token to session
					$tb_o['oauth_token'] = $token = $request_token['oauth_token'];
					$tb_o['oauth_token_secret'] = $request_token['oauth_token_secret'];
					update_option('tweet-blender',$tb_o);
					
					$errors[] = "To take advantage of a whitelisted account with oAuth please <a href='javascript:tAuth(\"" . $connection->getAuthorizeURL($token) . "\")' title='Authorize Twitter Access'>use your Twitter account to authorize access</a>.";
				}
				else {
					$errors[] = "Not able to form oAuth authorization request URL. HTTP status code: " . $connection->http_code;
				}					
			}
		}

		if(sizeof($errors) > 0) {
			$message = '<div class="error"><strong><ul><li>' . join('</li><li>',$errors) . '</li></ul>' . $cache_clear_results . '</strong></div>';
			$tb_o = $_POST;
		}
		else {
			// reset oAuth tokens
			if ($_POST['reset_oauth']) {
				unset($tb_o['oauth_access_token']);
			}

			// unset archive page ID if archive is disabled
			if($_POST['archive_is_disabled']) {
				unset($tb_o['archive_page_id']);
				unset($tb_o['archive_is_disabled']);
			}

			// cycle through all possible options
			foreach($tb_option_names as $opt) {
				// if option was set by user - store it
				if(isset($_POST[$opt])) {
					$tb_o[$opt] = $_POST[$opt];
				}
				// else if option was not one that user controls - unset it
				elseif (!array_key_exists($opt,$tb_option_names_system)) {
					$tb_o[$opt] = '';
				}
			}
			
			update_option('tweet-blender',$tb_o);
	        // Put an options updated message on the screen
			$message = '<div class="updated"><p><strong>Settings saved. ' . $cache_clear_results . '</strong></p></div>';
		}	

    }
?>

<link type="text/css" href="<?php echo plugins_url('tweet-blender/css/jquery-ui/jquery-ui.css'); ?>" rel="stylesheet" /> 
<style type="text/css">
a.info-icon {
	-moz-opacity:.30; filter:alpha(opacity=30); opacity:.30;
}

a.info-icon:hover {
	-moz-opacity:1; filter:alpha(opacity=100); opacity:1;
}

a.info-icon img {
	vertical-align: middle;
}

#admin-links {
	float:right;
	display:inline;
	padding-right:15px;
	text-align:center;
}

.form-table {
	clear:none;
	display:inline;
}

.setting-description {
	font-style:italic;
	font-size:80%;
}
.pass {
	color:#00FF00;
}
.fail {
	color:#FF0000;
}

.ui-tabs .ui-tabs-hide {
     display: none;
}

#icon-tweetblender {
	-moz-background-clip:border;
	-moz-background-inline-policy:continuous;
	-moz-background-origin:padding;
	background:transparent url(<?php bloginfo('wpurl'); ?>/wp-content/plugins/tweet-blender/img/tweetblender-logo_32x32.png) no-repeat;
}

</style>

<script type="text/javascript">

var TB_monthNumber = {'Jan':1,'Feb':2,'Mar':3,'Apr':4,'May':5,'Jun':6,'Jul':7,'Aug':8,'Sep':9,'Oct':10,'Nov':11,'Dec':12},
TB_timePeriods = new Array("second", "minute", "hour", "day", "week", "month", "year", "decade"),
TB_timePeriodLengths = new Array("60","60","24","7","4.35","12","10"),
ajaxURLs = new Array(),
screenNamesCount = 0;
  
// make tabs
jQuery(document).ready(function(){
    var tabsElement = jQuery("#tabs").tabs({
	    show:function(event, ui) {
			
			// find out index
			var tabsEl = jQuery('#tabs').tabs();
			var selectedTabIndex = tabsEl.tabs('option', 'selected');

	        jQuery('#tb_tab_index').val(selectedTabIndex);
	        return true;
	    }
	});
	
	// reopen last used tab
    tabsElement.tabs('select', <?php if($_POST['tb_tab_index']) { echo $_POST['tb_tab_index']; } else { echo 0; } ?>);

	// bind event handler to disable archive checkbox
	jQuery('#archive_is_disabled').click(function() {
		if (jQuery('#archive_is_disabled').is(':checked')) {
			jQuery('#archivesettings tr').slice(1).hide();
		}
		else {
			jQuery('#archivesettings tr').slice(1).show();
		}
	});

	// check limit for admin's PC
	jQuery.ajax({
		url: 'http://twitter.com/account/rate_limit_status.json',
		dataType: 'jsonp',
		success: function(json){
			var hitsLeftHtml = '';
			if (json.remaining_hits > 0) {
				hitsLeftHtml = 	'<span class="pass">' + json.remaining_hits + '</span>';
			}
			else {
				hitsLeftHtml = '<span class="fail">0</span>';
			}
			jQuery('#locallimit').html('Max is ' + json.hourly_limit + '/hour &middot; You have ' + hitsLeftHtml + ' left &middot; Next reset ' + TB_verbalTime(TB_str2date(json.reset_time)));
		},
		error: function(){
			jQuery('#locallimit').html('<span class="fail">Check failed</span>');
		}
	});	

	
	// if there were any problems, highlight the Status tab
	if(jQuery('span.fail').length > 0) {
		jQuery('#statustab a').children('span').addClass('fail');
	}

});


	// Twitter oAuth window
	function tAuth(url) {
		var tWin = window.open(url,'tWin','width=800,height=410,toolbar=0,location=1,status=0,menubar=0,resizable=1');
	}
	
	function TB_str2date(dateString) {
	
		var dateObj = new Date(),
		dateData = dateString.split(/[\s\:]/);
		
		// if it's a search format
		if (dateString.indexOf(',') >= 0) {
			// $wday,$mday, $mon, $year, $hour,$min,$sec,$offset
			dateObj.setUTCFullYear(dateData[3],TB_monthNumber[""+dateData[2]]-1,dateData[1]);
			dateObj.setUTCHours(dateData[4],dateData[5],dateData[6]);
		}
		// if it's a user feed format
		else {
			// $wday,$mon,$mday,$hour,$min,$sec,$offset,$year
			dateObj.setUTCFullYear(dateData[7],TB_monthNumber[""+dateData[1]]-1,dateData[2]);
			dateObj.setUTCHours(dateData[3],dateData[4],dateData[5]);
		}
	
		return dateObj;
	}

	function TB_verbalTime(dateObj) {
	   
	    var j,
		now = new Date(),
		difference,
		verbalTime,
		prefix = '',
		postfix = '';
		
		if (now.getTime() > dateObj.getTime()) {
			difference = Math.round((now.getTime() - dateObj.getTime()) / 1000);
			postfix = ' ago';
		}
		else {
			difference = Math.round((dateObj.getTime() - now.getTime()) / 1000);
			prefix = 'in ';
		}
			
	   
	    for(j = 0; difference >= TB_timePeriodLengths[j] && j < TB_timePeriodLengths.length; j++) {
	        difference = difference / TB_timePeriodLengths[j];
	    }
	    difference = Math.round(difference);
	   
	    verbalTime = TB_timePeriods[j];
	    if (difference != 1) {
	        verbalTime += 's';
	    }
	   
	    return prefix + difference + ' ' + verbalTime + postfix;
	}
	
</script>

<div class="wrap">
	<div id="icon-tweetblender" class="icon32"><br/></div><h2><?php _e('Tweet Blender', 'mt_trans_domain' ); ?></h2>

	<?php echo $message;  echo "<!-- $log_msg -->"; ?>
	 
	<form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<input type="hidden" id="tb_new_data" name="tb_new_data" value="Y">
	<input type="hidden" id="tb_tab_index" name="tb_tab_index" value="">
	<?php
	if ( function_exists('wp_nonce_field') )
		wp_nonce_field('tweet-blender_settings-save');
	?>

	<div id="tabs">
    <ul style="height:35px;">
        <li><a href="#tab-1"><span>General</span></a></li>
        <li><a href="#tab-2"><span>Widgets</span></a></li>
        <li><a href="#tab-3"><span>Archive</span></a></li>
        <li><a href="#tab-4"><span>Filters</span></a></li>
        <li><a href="#tab-5"><span>Advanced</span></a></li>
        <li id="statustab"><a href="#tab-6"><span>Status</span></a></li>
        <li><a href="#tab-7"><span>Help</span></a></li>
    </ul>

    <div id="tab-1">
    <!-- General settings -->
		<table class="form-table">
		<tr valign="top">
			<th class="th-full" colspan="2" scope="row">
			<label for="general_link_urls">
			<input type="checkbox" name="general_link_urls"<?php if ($tb_o['general_link_urls']) echo " checked"; ?>>
			<?php _e("Link http &amp; https URLs insde tweet text", 'mt_trans_domain' ); ?>
			</label>
			</th>
		</tr>
		<tr valign="top">
			<th class="th-full" colspan="2" scope="row">
			<label for="general_link_screen_names">
			<input type="checkbox" name="general_link_screen_names"<?php if ($tb_o['general_link_screen_names']) echo " checked"; ?>>
			<?php _e('Link @screenname inside tweet text', 'mt_trans_domain' ); ?>
			</label>
			</th>
		</tr>
		<tr valign="top">
			<th class="th-full" colspan="2" scope="row">
			<label for="general_link_hash_tags">
			<input type="checkbox" name="general_link_hash_tags"<?php if ($tb_o['general_link_hash_tags']) echo " checked"; ?>>
			<?php _e("Link #hashtags insde tweet text", 'mt_trans_domain' ); ?>
			</label>
			</th>
		</tr>
		<tr valign="top">
			<th class="th-full" colspan="2" scope="row">
			<h3>SEO</h3>
			<label for="general_seo_tweets_googleoff">
			<input type="checkbox" name="general_seo_tweets_googleoff"<?php if ($tb_o['general_seo_tweets_googleoff']) echo " checked"; ?>>
			<?php _e('Wrap all tweets with googleoff/googleon tags to prevent indexing', 'mt_trans_domain' ); ?>
			</label>
			</th>
		</tr>
		<tr valign="top">
			<th class="th-full" colspan="2" scope="row">
			<label for="general_seo_footer_googleoff">
			<input type="checkbox" name="general_seo_footer_googleoff"<?php if ($tb_o['general_seo_footer_googleoff']) echo " checked"; ?>>
			<?php _e('Wrap footer with date and time in all tweets with googleoff/googleon tags to prevent indexing', 'mt_trans_domain' ); ?>
			</label>
			</th>
		</tr>
		</table>
	</div>

    <div id="tab-2">
    <!-- Widgets Settings -->
		<table class="form-table">
		<tr valign="top">
			<th class="th-full" colspan="2" scope="row">
			<label for="widget_check_sources">
			<input type="checkbox" name="widget_check_sources"<?php if ($tb_o['widget_check_sources']) echo " checked"; ?>>
			<?php _e("Check and verify sources when widget settings are saved", 'mt_trans_domain' ); ?>
			</label>
			</th>
		</tr>
		<tr valign="top">
			<th class="th-full" colspan="2" scope="row">
			<label for="widget_show_header">
			<input type="checkbox" name="widget_show_header"<?php if ($tb_o['widget_show_header']) echo " checked"; ?>>
			<?php _e("Show header with Twitter logo and refresh link for each widget", 'mt_trans_domain' ); ?>
			</label>
			</th>
		</tr>
		<tr valign="top">
			<th class="th-full" colspan="2" scope="row">
			<label for="widget_show_photos">
			<input type="checkbox" name="widget_show_photos"<?php if ($tb_o['widget_show_photos']) echo " checked"; ?>>
			<?php _e("Show user's photo for each tweet", 'mt_trans_domain' ); ?>
			</label>
			</th>
		</tr>
		<tr valign="top">
			<th class="th-full" colspan="2" scope="row">
			<label for="widget_show_user">
			<input type="checkbox" name="widget_show_user"<?php if ($tb_o['widget_show_user']) echo " checked"; ?>>
			<?php _e("Show author's username for each tweet", 'mt_trans_domain' ); ?>
			</label>
			</th>
		</tr>
		<tr valign="top">
			<th class="th-full" colspan="2" scope="row">
			<label for="widget_show_source">
			<input type="checkbox" name="widget_show_source"<?php if ($tb_o['widget_show_source']) echo " checked"; ?>>
			<?php _e("Show tweet source for each tweet", 'mt_trans_domain' ); ?>
			</label>
			</th>
		</tr>
		<tr valign="top">
			<th class="th-full" colspan="2" scope="row">
			<label for="widget_show_reply_link">
			<input type="checkbox" name="widget_show_reply_link"<?php if ($tb_o['widget_show_reply_link']) echo " checked"; ?>>
			<?php _e("Show reply link for each tweet (on mouse over)", 'mt_trans_domain' ); ?>
			</label>
			</th>
		</tr>
		<tr valign="top">
			<th class="th-full" colspan="2" scope="row">
			<label for="widget_show_follow_link">
			<input type="checkbox" name="widget_show_follow_link"<?php if ($tb_o['widget_show_follow_link']) echo " checked"; ?>>
			<?php _e("Show follow link for each tweet (on mouse over)", 'mt_trans_domain' ); ?>
			</label>
			</th>
		</tr>
		</table>
	</div>
	
    <div id="tab-3">
	<!-- Archive Page Settings -->
		<table class="form-table" id="archivesettings">
		<tr valign="top">
			<th class="th-full" colspan="2" scope="row">
			<label for="archive_is_disabled">
			<input type="checkbox" id="archive_is_disabled" name="archive_is_disabled"<?php if ($tb_o['archive_is_disabled']) echo " checked"; ?>>
			<?php _e('Disable archive page', 'mt_trans_domain' ); ?> 
			</label>
			</th>
		</tr>
		<tr valign="top">
			<th class="th-full" colspan="2" scope="row">
			<label for="archive_auto_page">
			<input type="checkbox" id="archive_auto_page" name="archive_auto_page"<?php if ($tb_o['archive_auto_page']) echo " checked"; ?>>
			<?php _e('Create archive page automatically', 'mt_trans_domain' ); ?> 
			</label>
			</th>
		</tr>
		<tr valign="top"<?php if ($tb_o['archive_is_disabled']) echo 'style="display:none;"'; ?>>
			<th scope="row"><label for="archive_tweets_num"><?php _e('Maximum number of tweets to show', 'mt_trans_domain' ); ?>:
			</label></th>
			<td>
			<select name="archive_tweets_num">
				<?php
				for ($i = 10; $i <= 90; $i+=10) {
					echo '<option value="' . $i . '"';
					if ($i == $tb_o['archive_tweets_num']) {
						echo ' selected';
					}
					echo '>' . $i . '</option>';
				}
				for ($i = 100; $i <= 500; $i+=100) {
					echo '<option value="' . $i . '"';
					if ($i == $tb_o['archive_tweets_num']) {
						echo ' selected';
					}
					echo '>' . $i . '</option>';
				}
			?></select></td>
		</tr>
		<tr valign="top"<?php if ($tb_o['archive_is_disabled']) echo 'style="display:none;"'; ?>>
			<th scope="row"><label for="archive_keep_tweets"><?php _e('Automatically remove tweets that are older than', 'mt_trans_domain' ); ?>:
			</label></th>
			<td>
			<select name="archive_keep_tweets">
			<?php
				foreach ($tb_keep_tweets_options as $name => $days) {
					echo '<option value="' . $days . '"';
					if ($days == $tb_o['archive_keep_tweets']) {
						echo ' selected';
					}
					echo '>' . $name . '</option>';
				}
			?></select></td>
		</tr>
		<tr valign="top"<?php if ($tb_o['archive_is_disabled']) echo 'style="display:none;"'; ?>>
			<th class="th-full" colspan="2" scope="row">
			<label for="archive_show_photos">
			<input type="checkbox" name="archive_show_photos"<?php if ($tb_o['archive_show_photos']) echo " checked"; ?>>
			<?php _e("Show user's photo for each tweet", 'mt_trans_domain' ); ?>
			</label>
			</th>
		</tr>
		<tr valign="top">
			<th class="th-full" colspan="2" scope="row">
			<label for="archive_show_user">
			<input type="checkbox" name="archive_show_user"<?php if ($tb_o['archive_show_user']) echo " checked"; ?>>
			<?php _e("Show author's username for each tweet", 'mt_trans_domain' ); ?>
			</label>
			</th>
		</tr>
		<tr valign="top"<?php if ($tb_o['archive_is_disabled']) echo ' style="display:none;"'; ?>>
			<th class="th-full" colspan="2" scope="row">
			<label for="archive_show_source">
			<input type="checkbox" name="archive_show_source"<?php if ($tb_o['archive_show_source']) echo " checked"; ?>>
			<?php _e("Show tweet source", 'mt_trans_domain' ); ?>
			</label>
			</th>
		</tr>
		<tr valign="top"<?php if ($tb_o['archive_is_disabled']) echo ' style="display:none;"'; ?>>
			<th class="th-full" colspan="2" scope="row">
			<label for="archive_show_reply_link">
			<input type="checkbox" name="archive_show_reply_link"<?php if ($tb_o['archive_show_reply_link']) echo " checked"; ?>>
			<?php _e("Show reply link for each tweet (on mouse over)", 'mt_trans_domain' ); ?>
			</label>
			</th>
		</tr>
		<tr valign="top"<?php if ($tb_o['archive_is_disabled']) echo ' style="display:none;"'; ?>>
			<th class="th-full" colspan="2" scope="row">
			<label for="archive_show_follow_link">
			<input type="checkbox" name="archive_show_follow_link"<?php if ($tb_o['archive_show_follow_link']) echo " checked"; ?>>
			<?php _e("Show follow link for each tweet (on mouse over)", 'mt_trans_domain' ); ?>
			</label>
			</th>
		</tr>
		</table>
	</div>
	
	<div id="tab-4">
	<!-- Filtering -->
		<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="filter_lang"><?php _e('Show only tweets in ', 'mt_trans_domain' ); ?>:</label></th>
			<td>
			<select name="filter_lang">
				<?php
				foreach ($tb_languages as $code => $lang) {
					echo '<option value="' . $code . '"';
					if ($code == $tb_o['filter_lang']) {
						echo ' selected';
					}
					echo '>' . $lang . '</option>';
				}
			?></select>
			</td>
		</tr>
		<tr valign="top">
	 		<th class="th-full" colspan="2" scope="row">
			<input type="checkbox" name="filter_hide_replies"<?php if ($tb_o['filter_hide_replies']) echo " checked"; ?>>
			<label for="filter_hide_replies"><?php _e("Hide tweets that are in reply to other tweets", 'mt_trans_domain' ); ?></label>
			</th>
		</tr>
		<tr valign="top">
	 		<th class="th-full" colspan="2" scope="row">
			<input type="checkbox" name="filter_hide_mentions"<?php if ($tb_o['filter_hide_mentions']) echo " checked"; ?>>
			<label for="filter_hide_mentions"><?php _e("Hide mentions of users, only show tweets from users themselves", 'mt_trans_domain' ); ?></label>
			</th>
		</tr>
		<!-- FUTURE: location-based selection
		<tr>
			<th scope="row"><label for="filter_location_name"><?php _e('Show only tweets near this place ', 'mt_trans_domain' ); ?>:</label></th>
			<td><input type="text" size="30" name="filter_location_name" value="<?php echo $tb_o['filter_location_name']; ?>"><br/>
				<label for="filter_location_dist">Within </label>
				<select name="filter_location_dist">
				<?php
				foreach (array(5,10,15,20,50,100,200,500) as $dist) {
					echo '<option value="' . $dist . '"';
					if ($dist == $tb_o['filter_location_dist']) {
						echo ' selected';
					}
					echo '>' . $dist . '</option>';
				}
				?></select>
				<select name="filter_location_dist_units">
				<?php
				foreach (array('mi' => 'miles','km' => 'kilometers') as $du => $dist_units) {
					echo '<option value="' . $du . '"';
					if ($du == $tb_o['filter_location_dist_units']) {
						echo ' selected';
					}
					echo '>' . $dist_units . '</option>';
				}
				?></select>
			</td>
		</tr>
		-->
		<tr valign="top">
			<th scope="row"><label for="filter_bad_strings"><?php _e('Exclude tweets that contain these users, words or hashtags', 'mt_trans_domain' ); ?>: </label>
			</th>
			<td valign="top">
				<textarea id="filter_bad_strings" name="filter_bad_strings" rows=2 cols=60 wrap="soft"><?php echo $tb_o['filter_bad_strings']; ?></textarea> 
				<br/>
				<span class="setting-description">You can use single keywords, usernames, or phrases. Enclose phrases in quotes. Do not use @ for screen names. Separate with commas. Example: #spam,badword,"entire bad phrase",badUser,anotherBadUser,#badHashTag</span>
			</td>
		</tr>
		</table>
	</div>
	
	<div id="tab-5">
	<!-- Advanced Settings -->
		<table class="form-table">
		<tr valign="top">
			<th class="th-full" colspan="2" scope="row">
			<label for="advanced_reroute_on">
			<input type="checkbox" name="advanced_reroute_on"<?php if ($tb_o['advanced_reroute_on']) echo " checked"; ?>>
			<?php _e('Re-route Twitter traffic through this server', 'mt_trans_domain' ); ?> 
			</label> (<input type="radio" value="oauth" name="advanced_reroute_type"<?php if ($tb_o['advanced_reroute_type'] == 'oauth') echo " checked"; ?>> user account based with oAuth <input type="radio" value="direct" name="advanced_reroute_type"<?php if ($tb_o['advanced_reroute_type'] == 'direct') echo " checked"; ?>> IP based)<br/>
			<span class="setting-description">This option allows you to reroute all API calls to Twitter via your server. This is to be used ONLY if your server is a white-listed server that has higher connection allowance than each individual user.  Each user can make up to 150 Twitter API connections per hour. Each visitor to your site will have their own limit i.e. their own 150. Checking the box will make all visitors to the site use your server's connection limit, not their own limit. If you did not prearranged with Twitter to have that limit increased that means that it will be 150 for ALL visitors - be careful.</span>
			</th>
		</tr>
		<tr valign="top">
			<th class="th-full" colspan="2" scope="row">
			<label for="advanced_show_limit_msg">
			<input type="checkbox" name="advanced_show_limit_msg"<?php if ($tb_o['advanced_show_limit_msg']) echo " checked"; ?>>
			<?php _e('Notify user when Twitter API connection limit is reached', 'mt_trans_domain' ); ?> 
			</label><br/>
			<span class="setting-description">
				When the API connection limit is reached and there is no cached data Tweet Blender can't show new tweets. If you check this box the plugin will show a message to user that will tell them that limit has been reached. In addition, the message will show how soon fresh tweets will be available again. If you don't check the box the message will not be shown - the tweets just won't be refreshed until plugin is able to get fresh data.
			</span>
			</th>
		</tr>
		<tr valign="top">
			<th class="th-full" colspan="2" scope="row">
			<label for="advanced_disable_cache">
			<input type="checkbox" name="advanced_disable_cache"<?php if ($tb_o['advanced_disable_cache']) echo " checked"; ?>>
			<?php _e('Disable data caching', 'mt_trans_domain' ); ?> 
			</label><br/>
			<span class="setting-description">
				Every time Tweet Blender refreshes, it stores data it receives from Twitter into a special cache on your server. Once a user reaches his API connection limit TweetBlender starts using cached data. Cached data is centralized and is updated by all users so even if one user is at a limit s/he can still get fresh tweets as cache is updated by other users that haven't yet reached their limit. If you don't want to cache data (to save bandwidth or for some other reason) then check this box. <b>WARNING: clears all cached tweets</b>.
			</span>
			</th>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="general_timestamp_format"><?php _e('Timestamp Format', 'mt_trans_domain' ); ?>:
			</label></th>
			<td><input type="text" name="general_timestamp_format" value="<?php echo $tb_o['general_timestamp_format']; ?>"> <span class="setting-description"><br/>
				leave blank = verbose from now ("4 minutes ago")<br/>
				h = 12-hour format of an hour with leading zeros ("08")<br/>
				i = Minutes with leading zeros ("01")<br/>
				s = Seconds, with leading zeros ("01")<br/>
				<a href="http://kirill-novitchenko.com/2009/05/configure-timestamp-format/">additional format options &raquo;</a>
			</span></td>
		</tr>
		<?php if(isset($tb_o['oauth_access_token'])) { ?>
		<tr valign="top">
			<th class="th-full" colspan="2" scope="row">
			<label for="reset_oauth">
			<input type="checkbox" name="reset_oauth" value="1">
			<?php _e('Clear oAuth Access Tokens', 'mt_trans_domain' ); ?> 
			</label><br/>
			<span class="setting-description">
				To get tweets from private users Tweet Blender needs to login to twitter using your credentials. Once you authorize access, the special tokens are stored in the configuration settings. This is NOT a username or password. Your username/password is NOT stored.  The tokens are tied to a specific Twitter account so if you changed your account or would like to use another account for authentication check this box to have previously saved tokens cleared.
			</span>
			</th>
		</tr>
		<?php } ?>
		<tr valign="top">
			<th class="th-full" colspan="2" scope="row">
			<label for="advanced_no_search_api">
			<input type="checkbox" name="advanced_no_search_api" value="1"<?php if ($tb_o['advanced_no_search_api']) echo " checked"; ?>>
			<?php _e('Do not use search API for screen names', 'mt_trans_domain' ); ?> 
			</label><br/>
			<span class="setting-description">
				To get tweets for screen names Tweet Blender relies on Twitter Search API; however, sometimes Twitter's search does not return any tweets for a particular account due to some complex internal relevancy rules. If you see tweets for a user when looking at http://twitter.com/{someusername} but you do not see tweets for that same user when you look at http://search.twitter.com/search?q={@someusername} you can try to check this box and have Tweet Blender switch to a different API to retrieve recent tweets. <b>Important: screen names with modifiers (e.g. @user|#topic) will still use Search API.</b>
			</span>
			</th>
		</tr>
		</table>
	</div>

	<div id="tab-6">
	<!-- Status -->
		<table class="form-table">
		<!-- tr>
			<th>API requests per refresh:</hd>
			<td id="numrequests"></td>
		</tr -->
		<tr>
			<th>API requests from blog server:</th>
			<td><?php
				if ($api_limit_data) {
					echo 'Max is ' . $api_limit_data->hourly_limit . '/hour &middot; ';
					if ($api_limit_data->remaining_hits > 0) {
						echo 'You have <span class="pass">' . $api_limit_data->remaining_hits . '</span> left &middot; ';
					}
					else {
						echo 'You have <span class="fail">0</span> left &middot; ';
					}
					echo "Next reset " . tb_verbal_time($api_limit_data->reset_time_in_seconds);
				}
				else {
					echo '<span class="fail">Check failed</span>';
				}
				if ($tb_o['advanced_reroute_on'] && $tb_o['advanced_reroute_type'] == 'oauth') {
					echo '<br/>checked with user account (oAuth)';
				}
				else {
					echo '<br/>checked with IP of your server (' . $_SERVER['SERVER_ADDR'] . ')';
				}
			?></td>
		</tr>
		<tr>
			<th>API requests from your computer:</th>
			<td id="locallimit"></td>
		</tr>
		<tr>
			<th>oAuth Access Tokens:</th>
			<td><?php 
			if(isset($tb_o['oauth_access_token'])) {
				echo '<span class="pass">present</span>';
			}
			elseif ($have_private_sources && !isset($tb_o['oauth_access_token'])) {
				echo '<span class="fail">not present</span>';
			}
			else {
				echo 'not needed';
			}
			?></td>
		</tr>
		<tr>
			<th>Cache:</th>
			<td>
				<?php	
				
				if ($cached_sources = tb_get_cache_stats()) {
					//print_r($cached_sources);
					// Output each opened file and then close
					foreach ((array)$cached_sources as $cache_src) {
						$s = '';
						if ($cache_src->tweets_num != 1) {
							$s = 's';
						}
						echo '<input type="checkbox" name="delete_cache_src[]" value="' . $cache_src->source . '" /> ';					
				       	echo urldecode($cache_src->source) . " - " . $cache_src->tweets_num . " tweet$s - updated " . tb_verbal_time($cache_src->last_update) . '<br/>';
					}
				}
				elseif ($tb_o['advanced_disable_cache'] == false) {
					echo '<span class="fail">no cached tweets found and caching is ON</span>';
				}
				else {
					echo '<span class="pass">no cached tweets found and caching is OFF</span>';
				}

				?>
				<label for="delete_cache_src[]">&nbsp;&uarr; Check boxes above to clear cached tweets from the database</label>
			</td>
		</tr>
		</table>
	</div>

	<div id="tab-7">
	Get Satisfaction Community: <a href="http://getsatisfaction.com/tweet_blender">http://getsatisfaction.com/tweet_blender</a><br/>
	Facebook Fan Page: <a href="http://www.facebook.com/pages/Tweet-Blender/96201618006">http://www.facebook.com/pages/Tweet-Blender/96201618006</a><br/>
	Twitter: <a href="http://twitter.com/tweetblender">http://twitter.com/tweetblender</a><br/>
	Homepage: <a href="http://www.tweet-blender.com">http://www.tweet-blender.com</a><br/>
	</div>

	</div>

	<p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save Settings', 'mt_trans_domain' ) ?>" />
	</p>
</form>
</div>

<?php
 
}

function tb_get_cache_stats() {
	global $wpdb;
	$table_name = $wpdb->prefix . "tweetblender";
	
	$sql = "SELECT source, COUNT(*) AS tweets_num, UNIX_TIMESTAMP(MAX(created_at)) AS last_update FROM " . $table_name . " GROUP BY source";
	$results = $wpdb->get_results($sql);
	
	return $results;
}

?>