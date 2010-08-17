=== Plugin Name ===
Contributors: Justin_K
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=T88Y2AZ53836U
Tags: facebook connect, facebook, connect, widget, login, logon, wordpress, buddypress
Requires at least: 2.5
Tested up to: 2.9.2
Stable tag: 1.3.2

A LoginLogout widget with Facebook Connect button, offering hassle-free login for your readers. Clean and extensible.  Supports BuddyPress.


== Description ==

The simple concept behind WP-FB AutoConnect is to offer an easy-to-use, no-thrills widget that lets readers login to your blog with either their Facebook account or local blog credentials. Although many "Facebook Connect" plugins do exist, most of them are either overly complex and difficult to customize, or fail to provide a seamless experience for new  visitors. I wrote this plugin to provide what the others didn't:

* No user interaction is required - the login process is transparent to new and returning users alike.
* Existing WP users who connect with FB retain the same local user accounts as before.
* New visitors will be given new WP user accounts, which can be retained even if you remove the plugin.
* Custom logging options can notify you whenever someone connects with Facebook.
* Custom actions allow you to modify connecting users according to their Facebook accounts.
* No contact with Facebook servers after the login completes - so no slow pageloads.
* Simple, well-documented source makes it easy to extend and customize.
* Won't bloat your database with duplicate user accounts, extra fields, or unnecessary complications.
* Built-in BuddyPress support.

This plugin is a great starting point for coders looking to add customized Facebook integration to their blogs.  For complete information, see the [plugin's homepage](http://www.justin-klein.com/projects/wp-fb-autoconnect).


== Installation ==

To allow your users to login with their Facebook accounts, you must first setup an Application for your site:

1. Visit [www.facebook.com/developers/createapp.php](http://www.facebook.com/developers/createapp.php)
2. Type in a name (i.e. the name of your blog). This is what Facebook will show on the login popup.
3. Note the API Key and Secret; you'll need them in a minute.
4. Click the "Connect" tab and enter your site's URL under "Connect URL."  Note: http://example.com/ and http://www.example.com/ are *not* the same - be sure this matches Settings -&gt; General -&gt; Wordpress Address.
5. Click the "Advanced" tab and enter your site's domain under "Email Domain" (i.e. example.com). This is only required if you want to be able to access your users' email addresses (optional).
6. Click "Save Changes."

Then you can install the plugin:

1. Download the latest version from [here](http://wordpress.org/extend/plugins/wp-fb-autoconnect/), unzip it, and upload the extracted files to your plugins directory.
2. Login to your Wordpress admin panel and activate the plugin.
3. Navigate to Settings -> WP-FB AutoConn.
4. Enter your Application's API Key and Secret (obtained above), and click "Save."
5. If you're using BuddyPress, a Facebook button will automatically be added to its built-in login panel.  If not, navigate to Appearance -&gt; Widgets and add the WP-FB AutoConnect widget to your sidebar. 

That's it - users should now be able to use the widget to login to your blog with their Facebook accounts.

For more information on exactly how this plugin's login process works and how it can be customized, see the [homepage](http://www.justin-klein.com/projects/wp-fb-autoconnect).


== Frequently Asked Questions ==

[FAQ](http://www.justin-klein.com/projects/wp-fb-autoconnect#faq)


== Screenshots ==

[Screenshots](http://www.justin-klein.com/projects/wp-fb-autoconnect#demo)


== Changelog ==
= 1.3.2 (2010-08-15) =
* Do not fetch Facebook profile picture if not present (revert to default WP/BP avatar)

= 1.3.1 (2010-08-14) =
* Fixed the "Object of class WP_Error could not be converted to string" bug

= 1.3.0 (2010-08-08) =
* Update Facebook API; PHP5 is now the minimum requirement
* This should (hopefully) fix the conflict with newer OpenGraph plugins (i.e. Like Button)

= 1.2.5 (2010-08-08) =
* New Feature: Use Facebook profile pictures as Wordpress avatars
* Code reorganization; BuddyPress code is now in Main.php, avatars are fetched in _process_login.php, etc.

= 1.2.4 (2010-08-07) =
* Reorganize options a bit to make a separate "Buddypress" section
* Made "Replace BuddyPress avatars with Facebook profile pictures" as optional
* Use htmlspecialchars so the widget will validate when redirect_to contains special chars

= 1.2.3 (2010-08-04) =
* Get rid of PHP short tags

= 1.2.2 (2010-07-24) =
* Added "Disable nonce check" to debug options (not recommended - see FAQS on the plugin page) 

= 1.2.1 (2010-07-14) =
* Oops! I made a commit error in 1.2.0.

= 1.2.0 (2010-07-14) =
* BuddyPress usernames generated via "First Name + Last Name" instead of "Name" (as reported [here](http://www.justin-klein.com/projects/wp-fb-autoconnect/comment-page-6#comment-12258))
* Facebook profile images are automatically displayed as BuddyPress avatars

= 1.1.9 (2010-05-28) =
* Again redo how the "Require Email" option is enforced
* Add option to publish new user registration announcement on user's walls (prompts for permission on connect)

= 1.1.8 (2010-05-17) =
* Added action wpfb_inserted_user to run *after* a user is inserted
* Fixed "Require Email" option

= 1.1.7 (2010-04-11) =
* Minor change: Use wp_generate_password() for autogenerated passwords

= 1.1.6 (2010-03-28) =
* Fixed to work on sites with over 1,000 existing users.

= 1.1.5 (2010-03-23) =
* Add an error check for a very rare bug; If the plugin is working on your site, you may skip this upgrade. 

= 1.1.4 (2010-03-23) =
* Include version number in login logs
* Slightly more descriptive error message in login logs
* Sanitize autogenerated usernames for BuddyPress
* Add "Show full log on error" option
* Add "Remove All Settings" (uninstall) option

= 1.1.3 (2010-03-22) =
* Check if other plugins have already included the Facebook API

= 1.1.2 (2010-03-21) =
* Logging: On failure, show the accumulated log up to the point of failure
* Logging: Show REQUEST variables
* Main: Add optional params to jfb_output_facebook_callback() and jfb_output_facebook_instapopup() so the default callback name can be overridden, allowing multiple login-handlers with different redirects and different email policies
* Main: auto-submitted login form's name based on the js callback name, to support multiple handlers
* Autologin: Fixed issue if both a button an autopopup were on the same page
* Include license

= 1.1.1 (2010-03-19) =
* Hopefully fix a crash on sites with more than 1,000 existing users
* Fix bug on some PHP4 configurations

= 1.1.0 (2010-03-18) =
* BuddyPress option is automatically enabled for BP installations
* Add wpfb_insert_user filter to run just before inserting an auto-created user
* Improved support for BuddyPress: use "pretty" usernames to fix profile links
* Include client IP in connection logs
* Cleanups/revisions to connection logs

= 1.0.8 (2010-03-18) =
* Add option to include Buddypress-specific filters
* Cleanup the Admin panel & update documentation

= 1.0.7 (2010-03-17) =
* Fix email hash-lookup for blogs with over 1,000 existing users

= 1.0.6 (2010-03-17) =
* Oops - Add support for PHP4 (really this time)

= 1.0.5 (2010-03-17) =
* Add support for PHP4

= 1.0.4 (2010-03-17) =
* Include the Facebook javascript in jfb_output_facebook_init() instead of wp_head
* Redirect form not generated by JS (this was leftover from an older version of the plugin...)
* Only check email hashes if there are actually existing users on the blog 
* Add wpfb_connect hook that runs BEFORE a login is allowed
* If email privilege is denied on first connect, but subsequently allowed, the user's auto-generated account will have its email updated to the correct one.
* Added uption to REQUIRE email address (not just prompt for it)
* XHTML Validation fix
* Small typo in the Widget

= 1.0.3 (2010-03-16) =
* Hopefully fix the "Call to undefined function wp_insert_user()" bug

= 1.0.2 (2010-03-16) =
* Fix API_Key validation check - should work properly now!

= 1.0.1 (2010-03-16) =
* Convert PHP short tags to long tags for server compatability

= 1.0.0 (2010-03-16) =
* First Release