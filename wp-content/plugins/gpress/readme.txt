=== gPress ===
Contributors: msmalley, jimmynguyc, pressbuddies
Tags: buddypress, geotagging, geo-tagging, geo, gpress, gmaps, google maps, google, maps, map, mapping
Requires at least: WordPress 3.0 / BuddyPress 1.2.5.2
Tested up to: WordPress 3.0 / BuddyPress 1.2.5.2
Stable tag: 0.2.4
Donate link: http://pressbuddies.com/sponsorships/

gPress adds new geo-relevant layers to the press platforms so you can geo-tag your surroundings or develop your own location-based services...

== Description ==

gPress adds new geo-relevant layers to WordPress, allowing you to create your own location-based services or to keep track of your own personal geo-tagged journies. Even in its beta state, you can presently geo-tag posts using native WordPress Mobile Applications, or create new geo-located places using custom post types, featured images and descriptions, add geoRSS functionality and integrated with BuddyPress and Foursquare...

For a live demonstration; please visit our [Demo Site](http://smalley.my/gpress/)

The future of gPress is one where you can develop your own completely customizable, fully-interactive and entirely immersible geo-relevant social-layers using combinations of movements, actions and consequences whilst geo-tagging, trailing and interacting with locations, people and objects. Create new geo-relevant states of social-roleplay by defining language and lingo, customizing context and integrating with other social-networks.

However, to-date, the following milestones have already been completed:

1. 	Places (with Image + Description)
2. 	Types of Places and Place Tags
3. 	geoRSS Support (for places)
4. 	Favorite Place Widget
5. 	Recent Places Widget
6. 	Map Options per Place + Geo-Tagged Post
7. 	Support for Native Mobile Applications
8. 	Ability to Geo-Tag Posts (if using Mobile App)
9.  gPress Options Page with Component Control
10. General Map, Brand and Credit Settings
11. Custom Heights per Place, Post and Widget
12. geoRSS Support for Geo-Tagged Posts
13. Extended geoRSS Support for Place Info
14. Custom Markers per Place, Post and Widget
15. Shortcodes and Inline WYSIWYG Text-Editor Assistance
16. Multiple Places in Single Map (via Shortcodes)
17. Marker Clustering for Maps with Multiple Markers
18. Foursquare Integration (view Friends + Your Locations)
19. BuddyPress Integration (Geo-Tagged Sitewide Posts)
20. BuddyPress Front-End Geo-Settings Page
21. BuddyPress User Profile Locations
22. Foursquare Sidebar Widgets
23. Check for CSS, PHP and JS in "custom" Folder
24. Complete Customisation of Language and Lingo

You can also check-out our [Roadmap](http://wordpress.org/extend/plugins/gpress/other_notes/) and [Change-Log](http://wordpress.org/extend/plugins/gpress/changelog/)

For more information; please visit us at [PressBuddies](http://pressbuddies.com/projects/geopress/)

== Installation ==

1. Deactivate Geolocation (if already in use), as gPress does everything Geolocation does, and more, and the two plugins cannot be used together...

2. Check you have WordPress 3.0+
3. Download the plugin
4. Unzip and upload to plugins folder
5. Activate the plugin
6. Smile at how easy life with WordPress can be!

7. To geo-tag posts with mobile-integration, you will need to download one of the mobile applications from Automattic and then enable XML-RPC from the Settings > Writing tab in the wp-admin

We have some more BASIC documentation available at:
http://smalley.my/gpress/documentation/

== Frequently Asked Questions ==

= Why does nothing happen after I activate the plugin...? =

gPress will only work with WordPress 3.0 (tested on rc3 or more), and once activated, the only new addition you will see at this point is the new places tab in the administrative back-end, right below the Posts tab and above the Media tab. If you activate gPress on anything less than WP 3.0, this additional tab will not show-up.

= Do I need to apply for a Google Maps API Key...? =

Since gPress uses Google Maps API 3 it does NOT require an API key.

= Why does the styling not match my theme...? =

To be honest, at this point, we have ONLY tested the plugin using the default Twenty Ten theme. We will run more tests in different themes as we reach version 0.2 onwards, but at this stage, we're focusing on functionality and testing that functionality by using the Twenty Ten theme...

= Does gPress work with other plugins...? =

gPress and Geolocation cannot be used together. gPress does everything Geolocation does, and more, but the two of them cannot be used together. Other than that, we have not yet had any reports of other problems, but please let us know if you run into any...

== Screenshots ==

1. This is a demonstration of the administrative panel for adding new places...
2. This is the public end-view for a place, as seen in the Twenty Ten theme...

== Upgrade Notice ==

= 0.2.4 =
MAJOR UPDATE - FULLY TRANSLATABLE VIA POT / MO

== Changelog ==

= 0.2.4 =
* ADDED Full Translation Capability for Everything
* NEED COMMUNITY CONTRIBUTIONS FOR MO FILES TO INCLUDE

= 0.2.3.2 =
* ADDED Checks for custom.php/css/js from custom folder
* ADDED Lingo Switcher (change places to venues)
* STARTED POT - NEED TO UPLOAD TO REPO TO TEST

= 0.2.3.1 =
* FIXED Mobile Compatability Bug

= 0.2.3 =
* ADDED Foursquare Sidebar Widgets
* IMPROVED Homepage Loop Options
* IMPROVED Session Starts (for 4sq)

= 0.2.2.2 =
* FIXED Minor Bug Avoiding PHP.ini Change
* ADDED Advanced Option for Removing Foursquare
* ADDED BuddyPress Option for Showing Location (as Text)
* ADDED BuddyPress Control for Locations on Profiles

= 0.2.2.1 =
* ADDED Overflow Scrolling for Large Content
* ADDED Option to Remove jQuery 1.4.2 from Theme
* CHANGED Foursquare session_start Sequence

= 0.2.2 =
* IMPROVED Core Functions for Future Growth
* IMPROVED Animated Map Panning
* ADDED User Locations to BP Profiles

= 0.2.1.5 =
* REMOVED JS Scripts from RSS Descriptions
* IMPROVED Permalink Refreshing for Places

= 0.2.1.4 =
* FIXED JS ERRORS ON THEME PAGES

= 0.2.1.4 =
* FIXED BUG - Fixed errors with JS on Homepage

= 0.2.1.3 =
* FIXED BUG - Fixed errors with JS on Admin Pages

= 0.2.1.2 =
* CHANGED - Short Content Option to Content Option
* FIXED BUG - With apostrophes in Foursquare titles
* FIXED BUG - With empty Lat / Lng from Foursquare

= 0.2.1.1 =
* FIXED BUG - Code no longer displayed in excerpts
* ADDED OPTION - Force maps to the end of excerpts
* ADDED OPTION - Remove maps from content less than 255

= 0.2.1 =
* FIXED BUG - User without geo-tagged posts will NOT display map!

= 0.2 =
* ADDED Functionality for Multiple Places on Single Map
* ADDED Marker Clustering Functionality for Places
* ADDED Foursquare Integration (GET Friends + Your Locations)
* MODIFIED CSS (now using Blocks not Floats)
* MODIFIED Shortcode Generation (all RETURNED not ECHOED) 
* ADDED Advanced Options to gPress TinyMCE Ad-Hoc Maps
* ADDED TinyMCE Support for Foursquare
* ADDED Sitewide Geo-Tagged Posts to BuddyPress Profiles
* ADDED Forced Geo-Tagged Posting Functionality

= 0.1.9.9 =
* ADDED WYSIWYG Text-Editor Button for Adding Ad-Hoc Maps

= 0.1.9.8 =
* FIXED Bug for Post + Widget Marker Links
* ADDED Shortcode Functionality for AD-Hoc Maps in Posts + Pages

= 0.1.9.7 =
* FIXED Bug that removed page content
* FIXED Bug that did not display default map height in widget

= 0.1.9.6 =
* FIXED Missing Place Titles

= 0.1.9.5 =
* ADDED Default Marker Options (for posts, places and widgets)
* IMPROVED Map Functions (much cleaner, now using option arrays)

= 0.1.9.4 =
* ADDED Homepage Loop Control (Post + Places, just Post or just Places)

= 0.1.9.3 =
* ADDED geoRSS Support for Geo-Tagged Posts
* ADDED Place Address, Image and Description to geoRSS
* ADDED Advanced Settings for Map Options
* ADDED Ability to Customise Height per Map
* ADDED Custom Marker Icons per Map Place and Post
* ADDED Custom Marker Shadows per Map Place and Post
* ADDED Map Options to Back-End Map Forms too!
* ADDED Map Options to Favorite Place Widget too!

= 0.1.9.2 =
* CRITICAL UPDATE - FIXED errors due to faulty SVN with 0.1.9.1

= 0.1.9.1 =
* CRITICAL UPDATE - FIXED an error with super_admin options

= 0.1.9 =
* ADDED WordPress 3.0 Compatible
* ADDED gPress Options Framework
* ADDED gPress Component Control
* ADDED General Settings
* ADDED Advanced Settings for Map Options
* ADDED Brand Settings
* ADDED Credits Settings
* IMPROVED Icons for Admin

= 0.1.8.6 =
* Corrected Typos
* NEW Markers for Favorite Places and Posts
* Ability to EDIT (Location, Type and Zoom) of Geo-Tagged Posts
* Only show geo-edit form if geo_ fields are being used

= 0.1.8.5 =
* Added Support for Automattic's Mobile Applications
* This allows you to geo-tag posts

= 0.1.8.4 =
* PRIORITY fix for Empty Map Options

= 0.1.8.3 =
* CSS changes to places with images only that are smaller than window size
* Added Map Options (Map Type and Zoom Control per Place)

= 0.1.8.2 =
* Renamed 'Place Types' to 'Types of Places'
* Corrected typos in README.txt

= 0.1.8.1 (Minor Patch) =
* Fixed faulty filter for the_content that removed non-place content

= 0.1.8 =
* Markers are now centered upon initialising and only pan AFTER clicking on them
* Added geoRSS Places to RSS Feed (with geoRSS support for places)
* Added Recent Places Widget
* Added Favorite Place Widget

= 0.1.7 =
* FIXED - Place Type + Place Tag Pages (Multiple Maps on Same Page)

= 0.1.6 =
* Optimised CSS for Map Pages

= 0.1.5 =
* Optimised images to reduce plugin size
* Improved trim_me function for place descriptions

= 0.1.4 =
* Important CSS Changes to Places (Public View)
* Moved Place Types and Tags into InfoBox Associated with Place

= 0.1.3 =
* MAJOR Improvement to Non-WP 3.0+ Activations - Preventing Errors by Skipping ALL Functionality
* gpress-core/meta/places.php - Created accurate character count for future Twitter intgeration
* gpress.php - Added IF / ELSE to check version number and if less than WP 3.0, the plugin will not do anything
* GENERAL Improvements to Styling of Places UI (Back-End)

= 0.1.2 =
* gpress-maps.php - Improved functionality for handling empty descriptions, images, or both
* gpress-maps.php - Better styling of images and descriptions based on mapCanvas width()

= 0.1.1 =
* CSS fixes only...

= 0.1 =
* This was our initial commit...

== The Neverending Roadmap ==

gPress will ultimately consist of several CORE modules and also be Buddypress compatible, but for now, with the limited free time and resources we have to work on this personal project, we are focusing on the following key-components:

== Milestones Already Completed ==
1. 	Places (with Image + Description)
2. 	Types of Places and Place Tags
3. 	geoRSS Support (for places)
4. 	Favorite Place Widget
5. 	Recent Places Widget
6. 	Map Options per Place + Geo-Tagged Post
7. 	Support for Native Mobile Applications
8. 	Ability to Geo-Tag Posts (if using Mobile App)
9.  gPress Options Page with Component Control
10. General Map, Brand and Credit Settings
11. Custom Heights per Place, Post and Widget
12. geoRSS Support for Geo-Tagged Posts
13. Extended geoRSS Support for Place Info
14. Custom Markers per Place, Post and Widget
15. Shortcodes and Inline WYSIWYG Text-Editor Assistance
16. Multiple Places in Single Map (via Shortcodes)
17. Marker Clustering for Maps with Multiple Markers
18. Foursquare Integration (view Friends + Your Locations)
19. BuddyPress Integration (Geo-Tagged Sitewide Posts)
20. BuddyPress Front-End Geo-Settings Page
21. BuddyPress User Profile Locations
22. Foursquare Sidebar Widgets
23. Check for CSS, PHP and JS in "custom" Folder
24. Complete Customisation of Language and Lingo

== Highest Priority - planned for gPress v0.2+ ==
1.  gPress Options / Menu Items for different User Levels
2.  Better Options for Displaying Maps (jQuery Dialog)
3.  Additional BuddyPress Integration as follows:
4.  Group Maps (with Group Members)
5.  Introduce New Geo-Tagged Components as follows:
6.  Geo-Tagged Blogs
7.  Geo-Tagged Comments
8.  Geo-Tagged Photos
9.  Geo-Tagged Activity Streams
10. Further Social-Media Integration as follows:
11. Foursquare Venues + Tips (GET and PUSH)
12. Foursquare PUSH Functionality (Create Venues + Checkins)
13. Twitter / Facebook Integration
14. BP Members, Groups and Blogs Directory Maps
15. Search Functionality for Front-End Maps
16. Search Functionality as Option for Widgets
17. Template Control of Map (Open, Closed, Sidebar)
18. Importing / Exporting Locations (CSV)
19. Easy Custom Styling / Marker Management

== Future Modules Include - gPress v0.3+ ==
1. Action Hooks and Filters with Documentation for Developers
2. Movements (move with linear, non-linear and stealth motion)
3. Actions (interact with locations, people and objects)
4. Consequences (customize the way things interact with each other)
5. Markers (leave references points, check-points and crumbs)
6. Locations (places can be public, private or communal)
7. People (relationships with acquaintances, strangers and characters)
8. Objects (collect and trade rewards, commodities and equipment)
9. Download Trail History as KML File

== gPress v1+ ==
1. Launch standalone website promoting gPress
2. Start developing web-service for hosted gPress installs
3. Develop gPress plugin and API frameworks
4. Develop method for networking different geo-environments