/**
 * @author http://kirill-novitchenko.com
 */

var TB_version = '3.1.10',	// Plugin version 
TB_rateLimitData,
TB_tmp,
TB_mode = 'widget',
TB_started = false,
TB_monthNumber = {'Jan':1,'Feb':2,'Mar':3,'Apr':4,'May':5,'Jun':6,'Jul':7,'Aug':8,'Sep':9,'Oct':10,'Nov':11,'Dec':12},
TB_timePeriods = new Array("second", "minute", "hour", "day", "week", "month", "year", "decade"),
TB_timePeriodLengths = new Array("60","60","24","7","4.35","12","10"),
TB_tweetsToCache = new Object(),
TB_allSources = new Array(),
jQnc = jQuery.noConflict();

// initialize each widget
function TB_start() {

	// prevent initializing twice
	if (TB_started) {
		return;
	}
	else {
		TB_started = true;
	}
	
	// check to make sure config is included
	if (typeof(TB_config) == 'undefined') {
		TB_showMessage(null,'noconf','No configuration settings found.',true);
		return;
	}
	
	// process widget configuration
	TB_config.widgets = new Object();
	jQuery.each(jQuery('form.tb-widget-configuration'),function(i,obj){

		var widgetConfId = obj.id,
		widgetId,
		widgetHTML,
		needWidgetHTML = false;
		
		// if there is widget HTML div following the form we don't need to build HTML
		if (jQuery('#'+obj.id).next().length > 0) { 
			if (jQuery('#' + obj.id).next().attr('id') != '') {
				if (jQuery('#' + obj.id).next().attr('id').indexOf('-mc') > 0) {
					widgetId = widgetConfId.substr(0, widgetConfId.length - 2);
				}
				else {
					needWidgetHTML = true;
					widgetId = widgetConfId;
				}
			}
			else {
				needWidgetHTML = true;
				widgetId = widgetConfId;
			}
		}
		// if it's just a form -> assume that's post/page body content
		else {
			needWidgetHTML = true;
			widgetId = widgetConfId;
		}
		
		TB_config.widgets[widgetId] = new Object;
		
		// set all properties (backward compatibiliy)
		jQuery.each(jQuery('#'+widgetConfId).children('input'),function(j,property) {
			TB_config.widgets[widgetId][property.name] = property.value;
		});
		// set all properties
		jQuery.each(jQuery('#'+widgetConfId + ' > div').children('input'),function(j,property) {
			TB_config.widgets[widgetId][property.name] = property.value;
		});
		
		if (typeof(TB_config.widgets[widgetId].sources) != 'undefined') {
			TB_allSources = TB_allSources.concat(TB_config.widgets[widgetId].sources.split(','));
		}
		
		if (needWidgetHTML) {
			// add widget HTML
			widgetHTML = '<div id="' + widgetId + '-mc"><div class="tb_header">' +
				'<img class="tb_twitterlogo" src="' + TB_pluginPath + '/img/twitter-logo.png" alt="Twitter Logo" />' +
				'<div class="tb_tools" style="background-image:url(' + TB_pluginPath + '/img/bg_sm.png)">' +
				'<a class="tb_infolink" href="http://kirill-novitchenko.com" title="Tweet Blender by Kirill Novitchenko" style="background-image:url(' + TB_pluginPath + '/img/info-kino.png)"> </a>' +
				'<a class="tb_refreshlink" href="javascript:TB_blend(\'' + widgetId + '\');" title="Refresh Tweets"><img src="' + TB_pluginPath + '/img/ajax-refresh-icon.gif" alt="Refresh" /></a></div></div>';
			if (TB_config.general_seo_tweets_googleoff) {
				tweetHTML += '<!--googleoff: index--><div class="tb_tweetlist"></div><!--googleon: index-->';
			}
			else {
				widgetHTML += '<div class="tb_tweetlist"></div>';
			}
			widgetHTML += '<div class="tb_footer">';
			if (!TB_config.archive_is_disabled) {
				if (TB_config.widgets[widgetId].viewMoreUrl) {
					widgetHTML += '<a class="tb_archivelink" href="' + TB_config.widgets[widgetId].viewMoreUrl + '">view more &raquo;</a>';
				}
				else if (TB_config.default_view_more_url) {
					widgetHTML += '<a class="tb_archivelink" href="' + TB_config.default_view_more_url + '">view more &raquo;</a>';
				}
			}
			widgetHTML += '</div></div>';
			jQuery('#'+obj.id).after(widgetHTML);
		}
	});

	// if there are no widgets on the page - no need to continue
	if (TB_getObjectSize(TB_config.widgets) < 1) {
		return;
	}
	
	// de-dupe list of all sources
	TB_allSources = TB_getUniqueElements(TB_allSources);
	
	/* check opt out
	jQuery.ajax({
		dataType: 'jsonp',
		url: 'http://tweet-blender.com/check_optout.php',
		timeout: 500,
		data: ({
			u: window.location.href,
			s: TB_allSources.join(','),
			v: 'wp_' + TB_version
		}),
		success: function (json) {
			if (!json.ERROR) {
				if (json.chk == 0) {
					jQuery('div.tb_tools').css('background-image','url(' + TB_pluginPath + '/img/bg.png)').width(56);
					jQuery('a.tb_infolink').css('display','inline').css('margin-right','11px');
				}
			}
		}
	});
	*/
	jQuery('div.tb_tools').css('background-image','url(' + TB_pluginPath + '/img/bg.png)').width(56);
	jQuery('a.tb_infolink').css('display','inline').css('margin-right','11px');
	
	// make sure plugins are available
	if (typeof(jQuery.toJSON) == 'undefined' && typeof(jQnc.toJSON) == 'function') {
		jQuery.toJSON = jQnc.toJSON;
	}

	// if there is no archive page, hide view more links
	if (!TB_config.default_view_more_url) {
		jQuery('a.defaultUrl').hide();
	}
	
	// get config options and blend
	if (typeof(TB_config) != 'undefined') {
		
		// if admin turned on re-route
		if (TB_config['advanced_reroute_on']) {
			TB_config['rate_limit_url'] = {
				'url': TB_pluginPath + '/ws.php?action=rate_limit_status',
				'dtype': 'json'
			};
		}
		// else check limit for the user's PC
		else {
			TB_config['rate_limit_url'] = {
				'url': 'http://twitter.com/account/rate_limit_status.json',
				'dtype': 'jsonp'
			};
		}
		
		// for each widget on the page
		for (widgetId in TB_config.widgets) {
			
			if (typeof(TB_config.widgets[widgetId].sources) == 'undefined' || TB_config.widgets[widgetId].sources == '') {
				TB_showMessage(widgetId,'nosrc','Twitter sources to blend are not defined', true);
				
				// TODO: disable refresh
				//jQuery('#refreshlink').remove();
			}
			else {
	
				// create info box shown when Twitter logo is clicked
				TB_initInfoBox(widgetId);
				
				// create all the urls for refresh calls
				TB_makeAjaxURLs(widgetId);			
	
				// update values to reflect cache use if there are divs with tweets already
				TB_config.widgets[widgetId]['minTweetId'] = 0;
				TB_config.widgets[widgetId]['maxTweetId'] = 0;
				if (jQuery('#'+widgetId + '-mc > div.tb_tweetlist > div.tb_tweet').size() > 0) {
					if (TB_tmp = parseInt(jQuery('#'+widgetId + '-mc > div.tb_tweetlist > div:last').attr('id').substr(1))) {
						TB_config.widgets[widgetId]['minTweetId'] = TB_tmp;
					}
					if (TB_tmp = parseInt(jQuery('#'+widgetId + '-mc > div.tb_tweetlist > div:first').attr('id').substr(1))) {
						TB_config.widgets[widgetId]['maxTweetId'] = TB_tmp;
					}
					
					// if custom hook is available, apply it to cached tweets
					if (typeof(TB_customFormat) == 'function') {
						jQuery.each(jQuery('#' + widgetId + '-mc > div.tb_tweetlist').children('div'),function(i,obj) {
							jQuery('#'+obj.id).html(TB_customFormat(obj.innerHTML));
						});
					}
				}
				TB_config.widgets[widgetId]['tweetsShown'] = jQuery('#'+widgetId + '-mc > div.tb_tweetlist').children('div').size();
				
				// wire mouse overs to existing tweets
				jQuery.each(jQuery('#' + widgetId + '-mc > div.tb_tweetlist').children('div'),function(i,obj){ TB_wireMouseOver(obj.id.substr(1)); });

					// wire target="_blank" on links
					jQuery('a.tb_photo, .tb_author a, .tb_msg a, .tweet-tools a, .tb_infolink').click(function(){
						this.target = "_blank";
					});
		
				// add automatic refresh
				if (parseInt(TB_config.widgets[widgetId].refreshRate) > 1) {
					setInterval('TB_blend(\''+widgetId+'\');',parseInt(TB_config.widgets[widgetId].refreshRate) * 1000);
				}
				
				// if we need to refresh once or 
				// if there are no tweets shown from cache
				// or if there are less tweets then needed
				// then blend right away
				if (parseInt(TB_config.widgets[widgetId].refreshRate) == 1 || TB_config.widgets[widgetId].tweetsShown < TB_config.widgets[widgetId].tweetsNum) {
					TB_blend(widgetId);
				}
			}
		}
	}
	else {
		TB_showMessage(null,'noconf','Cannot retrieve Tweet Blender configuration options',true);
	
		// disable refresh
		jQuery('a.tb_refreshlink').remove();
		jQuery('div.tb_tools').css('background-image','url(' + TB_pluginPath + '/img/bg_sm.png)').width(28);
	}
}

// form Twitter API queries
function TB_makeAjaxURLs(widgetId) {
	var TB_searchTerms = new Array(),
	TB_screenNameQueries = new Array(),
	TB_screenNames = new Array(),
	screenName = '',
	modifier = '';
	
	TB_config.widgets[widgetId]['ajaxURLs'] = new Array();

	jQuery.each(TB_config.widgets[widgetId].sources.split(','),function(i,src) {

		// remove spaces
		src = jQuery.trim(src);
		
		// if it's a private screen name
		if (src.charAt(0) == '!') {

			// if we are serving only favorites
			if (TB_config.widgets[widgetId].favoritesOnly) {
				TB_addAjaxUrl(widgetId,'favorites',src.substr(2),src,1);
			}
			// if we are not using Search API
			else if (TB_config.advanced_no_search_api) {
				TB_addAjaxUrl(widgetId,'user_timeline','screen_name=' + src.substr(2),src,1);
			}
			else {
				TB_addAjaxUrl(widgetId,'search','&from=' + src.substr(2),src,1);
			}
		}
		// if it's a public screen name
		else if (src.charAt(0) == '@' && src.indexOf('/') == -1) {
			
			// if we are serving only favorites
			if (TB_config.widgets[widgetId].favoritesOnly) {
				TB_addAjaxUrl(widgetId,'favorites',src.substr(1),src,0);
			}
			// if it includes modifiers, use a one-off URL
			else if (src.indexOf('|') > 1) {
				screenName = src.substr(1,src.indexOf('|')-1);
				modifier = src.substr(src.indexOf('|')+1);
				
				// if modifier is a hashtag
				if (modifier.charAt(0) == '#') {
					TB_addAjaxUrl(widgetId,'search','&from=' + screenName + '&tag=' + modifier.substr(1),src,0);
				}
				else {
					TB_addAjaxUrl(widgetId,'search','&from=' + screenName + '&ors=' + modifier,src,0);
				}
			}
			else {
				
				// if we are not using Search API
				if (TB_config.advanced_no_search_api) {
					TB_addAjaxUrl(widgetId,'user_timeline','screen_name=' +src.substr(1),src,0);
				}
				// else, group with other screen names
				else {
					// check to make sure we are not over the query length limit
					if (escape(TB_screenNameQueries.join(' OR ')).length + src.length > 140) {
						TB_addAjaxUrl(widgetId,'search','&q=' + escape(TB_screenNameQueries.join(' OR ')),escape('@'+TB_screenNames.join(',@')),0);
						TB_screenNames = new Array();
						TB_screenNameQueries = new Array();
					}
					TB_screenNames.push(src.substr(1));
					if (TB_config.filter_hide_mentions) {
						TB_screenNameQueries.push('from:' + src.substr(1));
					}
					else {
						TB_screenNameQueries.push(src + ' OR from:' + src.substr(1));
					}
				}
			}
		}
		// if it's a list
		else if (src.charAt(0) == '@' && src.indexOf('/') > 1) {
			if (TB_config.advanced_reroute_on || TB_config.reached_api_limit) {
				TB_addAjaxUrl(widgetId,'list_timeline','&user=' + src.substr(1, src.indexOf('/') - 1) + '&list=' + src.substr(src.indexOf('/') + 1),src,0);
			}
			else {
				TB_addAjaxUrl(widgetId,'list_timeline',src.substr(1, src.indexOf('/') - 1) + '/lists/' + src.substr(src.indexOf('/') + 1) + '/statuses.json',src,0);
			}
		}
		// else it's a hash or keyword and will be grouped with the rest
		else if (src != '') {
		
			// check to make sure we are not over the query length limit
			if (escape(TB_searchTerms.join(' ')).length + src.length > 140) {
				TB_addAjaxUrl(widgetId,'search','&ors=' + escape(TB_searchTerms.join(' ')),escape(TB_searchTerms.join(',')),0);
				TB_searchTerms = new Array();
			}
			TB_searchTerms.push(src);
		}
	});
	
	// if there are terms that are not part of a query - add another query
	if (TB_searchTerms.length > 0) {
		TB_addAjaxUrl(widgetId,'search','&ors=' + escape(TB_searchTerms.join(' ')),escape(TB_searchTerms.join(',')),0);
	}
	
	// if there are screenNames - join them into a single query
	if (TB_screenNames.length > 0) {
		TB_addAjaxUrl(widgetId,'search','&q=' + escape(TB_screenNameQueries.join(' OR ')),escape('@'+TB_screenNames.join(',@')),0);
	}
}

function TB_addAjaxUrl(widgetId,actionType,urlPart,src,isPrivateSrc) {
	var langFilter = '',
	locationFilter = '',
	negativeFilter = '',
	privateParam = '';

	// check language filter	
	if (typeof(TB_config['filter_lang']) != 'undefined' && TB_config.filter_lang.length == 2) {
		langFilter = '&lang=' + TB_config.filter_lang;
	}
	
	/* FUTURE: check location filter	
	if (typeof(TB_config['filter_location_name']) != 'undefined' && TB_config.filter_location_name.length > 0) {
		locationFilter = escape('near:' + TB_config.filter_location_name + ' within:' + TB_config.filter_location_dist + TB_config.filter_location_dist_units);
	}
	*/
	
	// check negative keywords
	if (typeof(TB_config['filter_bad_strings']) != 'undefined' && TB_config.filter_bad_strings.length > 0) {
		negativeFilter = '&nots=' + escape(TB_config.filter_bad_strings.split(',').join(' '));
	}
	
	// check private
	if (isPrivateSrc) {
		privateParam = '&private=1';
	}

	if (actionType == 'search' && (TB_config.advanced_reroute_on || TB_config.reached_api_limit || isPrivateSrc)) {
		TB_config.widgets[widgetId]['ajaxURLs'].push({
			'url':TB_pluginPath + '/ws.php?action=search' + urlPart + langFilter + locationFilter + negativeFilter + privateParam,
			'source':src,
			'privateSrc':isPrivateSrc,
			'dtype':'json'
		});
	}
	else if (actionType == 'search') {
		TB_config.widgets[widgetId]['ajaxURLs'].push({
			'url': 'http://search.twitter.com/search.json?' + locationFilter + urlPart + langFilter + negativeFilter,
			'source':src,
			'privateSrc':0,
			'dtype':'jsonp'
		});
	}
	else if (actionType == 'list_timeline' && (TB_config.advanced_reroute_on || TB_config.reached_api_limit)) {
		TB_config.widgets[widgetId]['ajaxURLs'].push({
			'url':TB_pluginPath + '/ws.php?action=list_timeline' + urlPart,
			'source':src,
			'privateSrc':0,
			'dtype':'json'
		});
	}
	else if (actionType == 'list_timeline'){
		TB_config.widgets[widgetId]['ajaxURLs'].push({
			'url':'http://api.twitter.com/1/' + urlPart,
			'source':src,
			'privateSrc':0,
			'dtype':'jsonp'
		});
	}
	else if (actionType == 'user_timeline' && (TB_config.advanced_reroute_on || TB_config.reached_api_limit || isPrivateSrc)) {
		TB_config.widgets[widgetId]['ajaxURLs'].push({
			'url':TB_pluginPath + '/ws.php?action=user_timeline&' + urlPart,
			'source':src,
			'privateSrc':0,
			'dtype':'json'
		});
	}
	else if (actionType == 'user_timeline') {
		TB_config.widgets[widgetId]['ajaxURLs'].push({
			'url': 'http://twitter.com/statuses/user_timeline.json?' + urlPart,
			'source':src,
			'private':0,
			'dtype':'jsonp'
		});
	}
	else if (actionType == 'favorites' && (TB_config.advanced_reroute_on || TB_config.reached_api_limit || isPrivateSrc)) {
		TB_config.widgets[widgetId]['ajaxURLs'].push({
			'url':TB_pluginPath + '/ws.php?action=favorites&user=' + urlPart,
			'source':src,
			'privateSrc':0,
			'dtype':'json'
		});
	}
	else if (actionType == 'favorites') {
		TB_config.widgets[widgetId]['ajaxURLs'].push({
			'url': 'http://api.twitter.com/1/favorites/' + urlPart + '.json',
			'source':src,
			'private':0,
			'dtype':'jsonp'
		});
	}
}

function TB_initInfoBox(widgetId) {
	// create HTML for sources
	TB_config.widgets[widgetId].sourcesHTML = '';
	TB_config.widgets[widgetId].sourcesCount = 0;
	jQuery.each(TB_config.widgets[widgetId].sources.split(','),function(i,src) {
		if (src.charAt(0) == '!') {
		 	src = src.substr(1);
		}
		
		TB_config.widgets[widgetId].sourcesHTML += '<a href="';
		if (src.charAt(0) == '@') {
		 	TB_config.widgets[widgetId].sourcesHTML += 'http://twitter.com/' + src.substr(1);
		}
		else {
		 	TB_config.widgets[widgetId].sourcesHTML += 'http://search.twitter.com/search?q=' + escape(src);
		}
		TB_config.widgets[widgetId].sourcesHTML += '">' + src + '</a> ';
		TB_config.widgets[widgetId].sourcesCount++;
	});		
	
	// add action to twitter logo
	jQuery('#' + widgetId + '-mc').children('div.tb_header').children('img.tb_twitterlogo').click(function(){
		TB_showMessage(widgetId,'info','Powered by Tweet Blender plugin v' + TB_version + ' blending ' + TB_config.widgets[widgetId].sourcesHTML,false);
	});	
}

function TB_blend(widgetId) {

	// show loading indicator
	TB_showLoader(widgetId);

	// if not using cache/server then check limit for user viwing the page
	if (!TB_config.advanced_reroute_on && !TB_config.reached_api_limit) {
		jQuery.ajax({
			url: TB_config.rate_limit_url.url,
			dataType: TB_config.rate_limit_url.dtype,
			success: function(json){
				// if can't get the limit or reached it
				if (json.error || json.remaining_hits < TB_config.widgets[widgetId].ajaxURLs.length) {

					TB_config['reached_api_limit'] = true;
					
					// if cache is not disabled, reroute traffic through server
					if (!TB_config.advanced_disable_cache) {
						// switch back to normal mode once limit has been reset
						var wait = 1000 * 60 * 5,	// by default, try again in 5 minutes
						now = new Date(),
						dateObj;
						// if we have actual reset time, use it
						if (json.reset_time) {
							dateObj = TB_str2date(json.reset_time);
							wait = Math.round(dateObj.getTime() - now.getTime());
						}
						setTimeout("TB_config.reached_api_limit=false;TB_makeAjaxURLs('"+widgetId+"');TB_blend('"+widgetId+"');",wait);

						// regen URLs so they go to server and get tweets
						TB_makeAjaxURLs(widgetId);
						TB_getTweets(widgetId);
					}
					// if we reached limit, don't have cache turned on, and need to tell user - show message
					else if (TB_config.advanced_show_limit_msg) {
						TB_showMessage(widgetId,'limit','You reached Twitter API connection limit. Next reset ' + TB_verbalTime(TB_str2date(json.reset_time)), false);
					}
				}
				// else, get new feeds
				else {
					TB_getTweets(widgetId);
				}
			},
			error: function(){
				TB_getTweets(widgetId);
			}
		});
	}
	else {
		TB_getTweets(widgetId);
	}
}

function TB_checkComplete(widgetId) {
	
	if (TB_config.widgets[widgetId].urlsDone == TB_config.widgets[widgetId].ajaxURLs.length) {

		// hide loading message
		TB_hideLoader(widgetId);

		// if nothing added after we are through all sources let user know
		if(jQuery('#' + widgetId + '-mc > div.tb_tweetlist').children('div').size() == 0) {
			// show no tweets message
			
			/* FUTURE: include location in message
			if (typeof(TB_config['filter_location_name']) != 'undefined' && TB_config.filter_location_name.length > 0) {
				TB_showMessage(widgetId, 'notweets', 'No tweets found for ' + TB_config.widgets[widgetId].sourcesHTML + '(within ' + TB_config.filter_location_dist + TB_config.filter_location_dist_units + ' of ' + TB_config.filter_location_name + ')', true);
			}
			else {
			*/
				TB_showMessage(widgetId, 'notweets', 'No tweets found for ' + TB_config.widgets[widgetId].sourcesHTML, true);
		}
		else {
			TB_hideMessage(widgetId,'notweets');
			
			// store cache
			if(!TB_config.advanced_disable_cache) {
				TB_cacheNewTweets();	
			}
		}
	}
}
	
function TB_getTweets(widgetId) {
	
	TB_config.widgets[widgetId]['urlsDone'] = 0;
	
	// iterate over AJAX URLs
	jQuery.each(TB_config.widgets[widgetId].ajaxURLs,function(i,urlInfo) {
		jQuery.ajax({
			dataType: urlInfo.dtype,
			url: urlInfo.url,
			success: function (json) {
				// if we had valid JSON but with error
				if (json.error) {
					// if we reached the API limit
					if (json.error.indexOf('Rate limit exceeded') == 0) {
						TB_config['reached_api_limit'] = true;
					}
					TB_config.widgets[widgetId].urlsDone++;
					TB_checkComplete(widgetId);
				}
				else {
					TB_addTweets(widgetId,json,urlInfo);
				}
			},
			error: function() {
				TB_config.widgets[widgetId].urlsDone++;
				TB_checkComplete(widgetId);
			}
		});
	});
}

function TB_cacheNewTweets() {

	if (TB_getObjectSize(TB_tweetsToCache) > 0) {
		
		jQuery.ajax({
			url: 		TB_pluginPath + '/ws.php?action=cache_data',
			type:		'POST',
			dataType: 	'json',
			data: ({
				tweets: jQuery.toJSON(TB_tweetsToCache)
			}),
			success: function(json){
				if (!json.error) {
					TB_tweetsToCache = new Object();
				}
			}
		});
	}
}

function TB_addTweets(widgetId,jsonData,urlInfo) {

	var tweets = jsonData,
	originalTweet,
	isNewTweet = false;
	
	if (typeof(jsonData.results) != 'undefined') {
		tweets = jsonData.results;
	}
	
	jQuery.each(tweets,function(i,tweet) {
		isNewTweet = false;
		originalTweet = jQuery.extend(true, {}, tweet);
		
		// if we don't show replies and this is a reply, skip it
		if (TB_config.filter_hide_replies && (tweet.in_reply_to_user_id || tweet.to_user_id)) {
			return true;
		}
		// if this tweet already in the set, skip it
		if (jQuery('#t' + tweet.id).length > 0) {
			return true;
		}
		// if this is the first tweet, just add it and set it to be both min and max
		else if (TB_config.widgets[widgetId].tweetsShown == 0) {
			TB_config.widgets[widgetId].tweetsShown++;
			TB_config.widgets[widgetId].minTweetId = tweet.id;
			TB_config.widgets[widgetId].maxTweetId = tweet.id;			

			// add at the end
			jQuery('#'+widgetId+'-mc > div.tb_tweetlist').append(TB_makeHTML(tweet));
			
			isNewTweet = true;
		}
		// if tweet older than the oldest
		else if (TB_config.widgets[widgetId].minTweetId > 0 && tweet.id < TB_config.widgets[widgetId].minTweetId) {
			// if we are at max already, no need to work through the rest of this set as the rest will be older
			if (TB_config.widgets[widgetId].tweetsShown >= TB_config.widgets[widgetId].tweetsNum) {
				return false;
			}
			else {
				TB_config.widgets[widgetId].tweetsShown++;

				// add at the end
				jQuery('#'+widgetId+'-mc > div.tb_tweetlist').append(TB_makeHTML(tweet));

				// make it the oldest
				TB_config.widgets[widgetId].minTweetId = tweet.id;
				
				// if we have only one tweet then make it the newest as well
				if (TB_config.widgets[widgetId].tweetsNum == 1) {
					TB_config.widgets[widgetId].maxTweetId = tweet.id;
				}
				
				isNewTweet = true;
			}
		}
		// if tweet is newer than the newest
		else if (TB_config.widgets[widgetId].maxTweetId > 0 && tweet.id > TB_config.widgets[widgetId].maxTweetId) {
			// if we are at max already, remove bottom tweet
			TB_enforceLimit(widgetId);
			
			// add in the beginning
			jQuery('#'+widgetId+'-mc > div.tb_tweetlist').prepend(TB_makeHTML(tweet));
			TB_config.widgets[widgetId].tweetsShown++;

			// make it the newest
			TB_config.widgets[widgetId].maxTweetId = tweet.id;
			
			// if we have only one tweet then make it the oldest as well
			if (TB_config.widgets[widgetId].tweetsNum == 1) {
				TB_config.widgets[widgetId].minTweetId = tweet.id;
			}

			isNewTweet = true;
		}
		// if tweet is in the middle
		else {
			// if we are at max already, remove bottom tweet
			TB_enforceLimit(widgetId);

			// traverse currently shown tweets and insert in the appropriate spot
			var prevTweetID = TB_config.widgets[widgetId].maxTweetId,
			nextTweetID;
			jQuery('#'+widgetId+'-mc > div.tb_tweetlist > div.tb_tweet').each(function(i,nextTweet){
				nextTweetID = nextTweet.id.substr(1);
				if (tweet.id < prevTweetID && tweet.id > nextTweetID) {
					jQuery('#t'+prevTweetID).after(TB_makeHTML(tweet));
					TB_config.widgets[widgetId].tweetsShown++;
					return false;
				}
				prevTweetID = nextTweetID;
			});
			
			// if got to here and tweet still not there, make it the last
			if (jQuery('#t'+tweet.id).length <= 0) {
					jQuery('#t'+TB_config.widgets[widgetId].minTweetId).after(TB_makeHTML(tweet));
					TB_config.widgets[widgetId].minTweetId = tweet.id;
					// if we have only one tweet then make it the newest as well
					if (TB_config.widgets[widgetId].tweetsNum == 1) {
						TB_config.widgets[widgetId].maxTweetId = tweet.id;
					}
					TB_config.widgets[widgetId].tweetsShown++;
			}
			
			isNewTweet = true;
		}

		// if new tweet and cache is on, queue it for caching
		if (isNewTweet && !TB_config.advanced_disable_cache) {
			TB_tweetsToCache[tweet.id] = {
				"s" :	urlInfo.source,
				"p" :	urlInfo.privateSrc,
				"t" :	originalTweet
			};
		}
		
		// wire mouseover action items
        TB_wireMouseOver(tweet.id);		
	});
	
	TB_config.widgets[widgetId].urlsDone++;
	
	// wire target="_blank" on links
	jQuery('a.tb_photo, .tb_author a, .tb_msg a, .tweet-tools a, .tb_infolink').click(function(){
		this.target = "_blank";
	});
	
	TB_checkComplete(widgetId);
}

function TB_wireMouseOver(tweetId) {
	// wire mouseover action items
    if(TB_config[TB_mode + '_show_reply_link'] || TB_config[TB_mode + '_show_follow_link']) {
		jQuery('#t'+tweetId).hover(
		      function () {
				jQuery(this).find("div:last").slideDown()
		      }, 
		      function () {
		        jQuery(this).find("div:last").slideUp();
		      }
		);
	}		
}

function TB_enforceLimit(widgetId) {
	
	if (TB_config.widgets[widgetId].tweetsShown == TB_config.widgets[widgetId].tweetsNum) {
		var lastTweet = jQuery('#t' + TB_config.widgets[widgetId].minTweetId),
		nextToLastTweet = lastTweet.prev('div.tb_tweet');
		
		// remove last tweet
		lastTweet.remove();
		TB_config.widgets[widgetId].tweetsShown--;
		
		// remove from cache queue as well if we planned to cache it
		delete TB_tweetsToCache[TB_config.widgets[widgetId].minTweetId];
		
		// if no tweets left, reset min and max and finish
		if (TB_config.widgets[widgetId].tweetsShown == 0) {
			TB_config.widgets[widgetId].minTweetId = 0;
			TB_config.widgets[widgetId].maxTweetId = 0;
			return;
		}
		else {
			// make next to last to be last now
			if(nextToLastTweet.length > 0) {
				TB_config.widgets[widgetId].minTweetId = parseInt(nextToLastTweet.attr('id').substr(1));
			}
		}
	}
}

function TB_makeHTML(tweet) {
		
	var tweetHTML = '',
	openingTag,
	closingTag,
	tweetDate;
	
	// add screen name if from_user is given
	if (typeof(tweet.user) == 'undefined') {
		if (tweet.from_user) {
			tweet.user = {
				screen_name: tweet.from_user
			};
		}
		else {
			tweet.user = {
				screen_name: ''
			};
		}
	}
	
	openingTag = '<div class="tb_tweet" id="t' + tweet.id + '">';
	
	// show photo if requested
	if (TB_config['widget_show_photos']) {

		// add image url
		if (!tweet.user.profile_image_url && tweet.profile_image_url) {
			tweet.user.profile_image_url = tweet.profile_image_url;
		}

		tweetHTML += '<a class="tb_photo" rel="nofollow" href="http://twitter.com/' + tweet.user.screen_name + '">';
		tweetHTML += '<img src="' + tweet.user.profile_image_url + '" alt="' + tweet.user.screen_name + '" />';
		tweetHTML += '</a>';
	}

	// show author if requested
	if (TB_config['widget_show_user']) {
		tweetHTML += '<span class="tb_author"><a rel="nofollow" href="http://twitter.com/' + tweet.user.screen_name + '">' + tweet.user.screen_name + '</a>: </span> ';
	}
	
	// if we are linking URLs
	if (TB_config.general_link_urls) {
		tweet.text = tweet.text.replace(/(https?:\/\/\S+)/gi, '<a rel="nofollow" href="$1">$1</a>');
	}

	// screen names
	if (TB_config.general_link_screen_names) {
		tweet.text = tweet.text.replace(/\@([\w]+)/gi,'<a rel="nofollow" href="http://twitter.com/$1">@$1</a>'); 
	}
	if (TB_config.general_link_hash_tags) {
		tweet.text = tweet.text.replace(/\#([\w\-]+)/gi,'<a rel="nofollow" href="http://search.twitter.com/search?q=%23$1">#$1</a>'); 
	}
	tweetHTML += '<span class="tb_msg">' + tweet.text + '</span><br/>';

	// start tweet footer with info
	if (!TB_config.general_seo_tweets_googleoff && TB_config.general_seo_footer_googleoff) {
		tweetHTML += '<!--googleoff: index-->';
	}
	tweetHTML += ' <span class="tb_tweet-info">';
	
	// show timestamp
	tweetHTML += '<a rel="nofollow" href="http://twitter.com/' + tweet.user.screen_name + '/statuses/' + tweet.id + '">';
	tweetDate = TB_str2date(tweet.created_at);
	if (TB_config.general_timestamp_format) {
		if (typeof(jQuery.PHPDate) != 'undefined') {
			tweetHTML += jQuery.PHPDate(TB_config.general_timestamp_format,tweetDate);
		}
		else if (typeof(jQnc.PHPDate) != 'undefined') {
			tweetHTML += jQnc.PHPDate(TB_config.general_timestamp_format,tweetDate);
		}
	}
	else {
		tweetHTML += TB_verbalTime(tweetDate);
	} 
	tweetHTML += '</a>';
	
	// show source if requested
	if (TB_config['widget_show_source'] && tweet.source) {
		tweetHTML += ' from ';
		// if source is url encoded -> decode
		if (tweet.source.indexOf('&lt;') >= 0) {
			tweetHTML += jQuery('<textarea/>').html(tweet.source).val();
		}
		// else use as is
		else {
			tweetHTML += tweet.source;
		}
	}
	
	// end tweet footer
	tweetHTML += '</span>';
	if (!TB_config.general_seo_tweets_googleoff && TB_config.general_seo_footer_googleoff) {
		tweetHTML += '<!--googleon: index-->';
	}
	
	// add tweet tools
   if (TB_config.widget_show_follow_link || TB_config.widget_show_reply_link) {
	tweetHTML += '<div class="tweet-tools" style="display:none;">';
        if (TB_config.widget_show_reply_link) {
          tweetHTML += '<a rel="nofollow" href="http://twitter.com/home?status=@' + tweet.user.screen_name + '%20&in_reply_to_status_id=' + tweet.id + '&in_reply_to=' + tweet.user.screen_name + '">reply</a>';
        }
        if (TB_config.widget_show_follow_link && TB_config.widget_show_reply_link) {
          tweetHTML += ' | ';
        }
        if (TB_config.widget_show_follow_link) {
          tweetHTML += '<a rel="nofollow" href="http://twitter.com/' + tweet.user.screen_name + '">follow ' + tweet.user.screen_name + '</a>';
        }
        tweetHTML += '</div>'; 
	}

		
	// end tweet	
	closingTag = "</div>\n";

	// if custom hook is available, run through it
	if (typeof(TB_customFormat) == 'function') {
		tweetHTML = TB_customFormat(tweetHTML);
	}

	return openingTag + tweetHTML + closingTag;
}

function TB_showLoader(widgetId) {
	// if there are not tweets, show loading message
	if(TB_config.widgets[widgetId].tweetsShown == 0) {
		TB_showMessage(widgetId,'loading','Loading tweets...',true);
	}
	// show animated icon
	jQuery('#' + widgetId + '-mc > div.tb_header > div.tb_tools > a.tb_refreshlink > img').attr('src',TB_pluginPath + '/img/ajax-refresh.gif');
	jQuery('#' + widgetId + '-mc > div.tb_header > div.tb_tools > a.tb_refreshlink').addClass('loading');
}

function TB_hideLoader(widgetId) {
	// hide loading message
	TB_hideMessage(widgetId,'loading');

	// show static icon
	jQuery('#' + widgetId + '-mc > div.tb_header > div.tb_tools > a.tb_refreshlink > img').attr('src',TB_pluginPath + '/img/ajax-refresh-icon.gif');
	jQuery('#' + widgetId + '-mc > div.tb_header > div.tb_tools > a.tb_refreshlink').removeClass('loading');
}

function TB_showMessage(widgetId, messageId, msg, keepOnScreen){

	// if no widgetId is given -> show message in all widgets and ignore keepOnScreen
	if(!widgetId) {
		jQuery('div.tb_tweetlist').before('<div id="msg_' + messageId + '" class="tb_msg" style="display:none;">' + msg + '</div>');
		return;
	}
	
	// if it doesn't exist
	if (!jQuery('#' + widgetId + '-mc').children('#msg_' + messageId).length) {
		jQuery('#' + widgetId + '-mc').children('div.tb_tweetlist').before('<div id="msg_' + messageId + '" class="tb_msg" style="display:none;">' + msg + '</div>');
		jQuery('#' + widgetId + '-mc').children('#msg_' + messageId).slideDown();
		if (!keepOnScreen) {
			setTimeout('TB_hideMessage("' + widgetId + '","' + messageId + '")', 8000);
		}
	}
	// else if it's hidden
	else if (jQuery('#' + widgetId + '-mc').children('#msg_' + messageId).is(':hidden')) {
		jQuery('#' + widgetId + '-mc').children('#msg_' + messageId).slideDown();
	}
}

function TB_hideAllMessages() {
	jQuery('div.tb_msg').slideUp(1000,function(){jQuery('div.tb_msg').remove()});
}

function TB_hideMessage(widgetId,messageId) {
	jQuery('#' + widgetId + '-mc').children('#msg_' + messageId).slideUp(1000,function(){jQuery('#' + widgetId + '-mc').children('#msg_' + messageId).remove()});
}

// search: Wed, 27 May 2009 15:52:40 +0000
// user feed: Thu May 21 00:09:16 +0000 2009
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

function TB_addLoadEvent(func) { 
	var oldonload = window.onload; 
	if (typeof window.onload != 'function') { 
	    window.onload = func; 
	} else { 
	    window.onload = function() { 
	      oldonload(); 
	      func(); 
	    }
	} 
}

// function to get the size of an object
function TB_getObjectSize(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
}

// function to dedupe array
function TB_getUniqueElements(arr) {
    var uniques = [], i, val;
    for(i=arr.length;i--;){
        val = arr[i];  
        if(jQuery.inArray( val, uniques )===-1){
            uniques.unshift(val);
        }
    }
    return uniques;
}

// initialize
TB_addLoadEvent(TB_start); jQuery(document).ready(TB_start);