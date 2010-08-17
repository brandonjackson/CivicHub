=== BuddyPress Wiki Component ===
Contributors: Aekeron
Tags: buddypress, wiki, education, collaboration, group
Requires at least: WordPress 3.0.1, BuddyPress 1.2.5.2
Tested up to: WordPress 3.0.1 / BuddyPress 1.2.5.2
Stable tag: 1.0

This plugin provides site and group based wiki functionality for a Buddypress installation.

== Description ==

Optional: Install the bp-fadmin plugin to enable group administrators to move wiki pages between groups and have quick access to change the view/edit/etc settings of the wiki pages in all their groups.  http://wordpress.org/extend/plugins/bp-fadmin/

DO NOT INSTALL THIS PLUGIN ON A LIVE SITE.  THIS IS A DEVELOPMENT VERSION ONLY.

This plugin provides site and group based wiki functionality for a Buddypress installation.
This is a completely new version of the BuddyPress Group Wikis plugin.  

The plugin is now based on the BuddyPress Skeleton Component and has been rewritten from the ground up to take advantage of internationalisation support, BuddyPress standards for ajax, function hooks and several months of BuddyPress/wordpress experience (as compared to very little before writing the original group wiki plugin).

Key features as compared to the original plugin:

* No multi-blog functionality required (utilises WP3.x's "custom post types" methods).
* Support for custom templating of pages.
* i18n support.
* Built in such a way to allow easy extension to support site-wide wikis and namespaces.
* Uses proper BuddyPress/Wordpress post submit and ajax methods.
* User controllable wiki page deletion.
* Group wiki data cleaned up properly on group deletion.
* Better text diff methods.
* More stuff.

DEVELOPMENT VERSION - NOT ALL FUNCTIONALITY AVAILABLE YET

As always, support can be obtained and feedback can be given at:

http://namoo.co.uk

== Installation ==

Download the plugin files to your plugin directory and activate.

== Changelog ==

= 0.9.7 ( August 14th 2010 ) =

* Fixed error with frontend admin and non-admins having admin powers.  Oops.

= 0.9.6 ( August 8th 2010 ) =

* Fixed dumb error of including a file twice.

= 0.9.5 ( August 8th 2010 ) =

* Huge cleanup of rubbish code base to enable me to concentrate on what needs doing and refactor.

= Version 0.9.4 ( July 30th 2010 ) =

* bp-fadmin support.  See: http://wordpress.org/extend/plugins/bp-fadmin/

== Notes ==

License.txt - contains the licensing details for this component.