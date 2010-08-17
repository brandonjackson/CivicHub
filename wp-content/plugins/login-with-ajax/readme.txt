=== Login With Ajax ===
Contributors: netweblogic
Tags: Login, Ajax, Redirect, BuddyPress, MU, WPMU, sidebar, admin, widget
Requires at least: 2.8
Tested up to: 3.0
Stable tag: 3.0b2

Add smooth ajax login and registration effects to your blog and choose where users get redirected upon login/logout. Supports SSL, MU, and BuddyPress.

== Description ==

Login With Ajax is for sites that need user logins or registrations and would like to avoid the normal wordpress login pages, this plugin adds the capability of placing a login widget in the sidebar with smooth AJAX login effects.

Some of the features:

* AJAX Login without refreshing your screen.
* AJAX Registration without refreshing your screen.
* AJAX Registration Password retrieval without refreshing your screen.
* Compatible with Wordpress, Wordpress MU and BuddyPress (BuddyPress supports logins only, no registrations yet).
* Will work with forced SSL logins.
* Customizable, upgrade-safe widgets.
* Redirect users to custom URLs on Login and Logout
* Redirect users with different roles to custom URLs
* shortcode and template tags available
* Fallback mechanism, will still work on javascript-disabled browsers
* Widget specific option to show link to profile page
* Now translatable (currently only Spanish is available, please contact me to contribute)

If you have any problems with the plugins, please visit our [http://netweblogic.com/forums/](support forums) for further information and provide some feedback first, we may be able to help. It's considered rude to just give low ratings and nothing reason for doing so.

If you find this plugin useful and would like to say thanks, a link, digg, or some other form of recognition to the plugin page on our blog would be appreciated.

= Translated Languages Available =

Here's a list of currently translated languages. Translations that have been submitted are greatly appreciated and hopefully make this plugin a better one. If you'd like to contribute, please have a look at the POT file in the langs folder and send us your translations.

* Finnish - Jaakko Kangosjärvi
* Russian - Виталий Капля - [http://dropbydrop.org.ua/](http://dropbydrop.org.ua/)
* French - Geoffroy Deleury [http://wall.clan-zone.dk](http://wall.clan-zone.dk)
* German - Linus Metzler
* Chinese - Simon Lau - [http://fashion-bop.com](http://fashion-bop.com)
* Italian - Marco aka teethgrinder
* Romanian - Gabriel Berzescu
* Danish - Christian B.
* Dutch - Sjors Spoorendonk
* Brazilian - Humberto S. Ribeiro, Diogo Goncalves, Fabiano Arruda
* Turkish - Mesut Soylu
* Polish - Ryszard Rysz
* Lithuanian - Gera Dieta - [http://www.kulinare.lt/](http://www.kulinare.lt/)

== Installation ==

1. Upload this plugin to the `/wp-content/plugins/` directory and unzip it, or simply upload the zip file within your wordpress installation.

2. Activate the plugin through the 'Plugins' menu in WordPress

3. If you want login/logout redirections, go to Settings > Login With Ajax in the admin area and fill out the form.

4. Add the login with ajax widget to your sidebar, or use login_with_ajax() in your template.

5. Happy logging in!

== Notes ==

=Note that registrations will not work on wordpress due to the customizations they make on the registration process, we will try to come up with a solution for this asap=

You can use the shortcode [login-with-ajax] or [lwa] and template tag login_with_ajax() with these options :

* is_widget='true'|'false' - By default it's set to false, if true it uses the $before_widget/$after_widget variables.
* profile_link='true'|'false' - By default it's set to false, if true, a profile link to wp-admin appears.

When creating customized themes for your widget, there are a few points to consider:

* Start by copying the contents /yourpluginpath/login-with-ajax/widget/ to /yourthemepath/plugins/login-with-ajax/
* If you have a child theme, you can place the customizations in the child or parent folder (you should probably want to put it in the child folder).
* If you want to customize the login-with-ajax.js javascript, you can also copy that into the same folder above (/yourthemepath/plugins/login-with-ajax/).
* Unless you change the javascript, make sure you wrap your widget with an element with id="login-with-ajax" or "LoginWithAjax". If you use the $before_widget ... variables, this should be done automatically depending on your theme. I recommend you just wrap a div with id="LoginWithAjax" for fuller compatability across themes.
* To force SSL, see [http://codex.wordpress.org/Administration_Over_SSL]("this page"). The plugin will automatically detect the wordpress settings.

= Important information if upgrading from 1.2 and you have a customized ajax login widget =

If you customized the widget, two small changes were made to the default login and logout templates which you should copy over if you'd like the remember password feature to work. The change requires that you add the ID attribute "LoginWithAjax_Links_Remember" to the remember password link. Also, you need to copy the new element and contents of the <form> below the first one with the ID "LoginWithAjax_Remember" and ensure you don't have another element with that ID in your template. Sorry, first and last time that will happen :)


== Screenshots ==

1. Add a  fully customizable login widget to your sidebars.

2. Smoothen the process via ajax login, avoid screen refreshes on failures.

3. If your login is unsuccessful, user gets notified without loading a new page!

4. Customizable login/logout redirection settings.

5. Choose what your users see once logged in.

== Frequently Asked Questions ==

= How do I use SSL with this plugin? =
Yes, see the notes section.

= Do you have a shortcode or template tag? =
Yes, see the notes section.

For further questions and answers (or to submit one yourself) go to our [http://netweblogic.com/forums/](support forums). 


== Changelog ==

= 2.1 =
* Added translation POT files.
* Spanish translation (quick/poor attempt on my part, just to get things going)
* Fixed result bug on [http://netweblogic.com/forums/topic/undefined-error-on-logging-in-with-wp-29]
* Fixed bug on [http://wordpress.org/support/topic/355406]

= 2.1.1 =
* Added Finnish, Russian and French Translations
* Made JS success message translatable
* Fixed encoding issue (e.g. # fails in passwords) in the JS

= 2.1.2 =
* Added German Translations
* Fixed JS url encoding issue

= 2.1.3 =
* Added Italian Translations
* Added space in widget after "Hi" when logged in.
* CSS compatability with themes improvement.

= 2.1.4 =
* Added Chinese Translations
* CSS compatability with themes improvement.

= 2.1.5 =
* Changed logged in widget to fix avatar display issue for both BuddyPress and WP. (Using ID instead of email for get_avatar and changed depreciated BP function).
* Added Danish Translation

= 2.2 =
* Added Polish, Turkish and Brazilian Translation
* Fixed buddypress avatar not showing when logged in
* Removed capitalization of username in logged in widget
* Fixed all other known bugs
* Added placeholders for redirects (e.g. %USERNAME% for username when logged in)
* Added seamless login, screen doesn't refresh upon successful login.

= 2.21 =
* Redirect bug fix
* Hopefully fixed encoding issue

= 3.0b =
* Various bug fixes
* Improved JavaScript code
* Ajax Registration Option

= 3.0 (coming soon) =
* Option to choose from various widget templates.