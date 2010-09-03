=== LBAK User Tracking ===
Contributors: samwho
Author: Sam Rose
Author URL: http://lbak.co.uk/
Donate link: http://donate.lbak.co.uk/
Tags: tracking, logging, activity, lbak, auditing, record
Requires at least: 3.0.1
Tested up to: 3.0.1
Stable tag: 1.2.2

An extensive, customisable, fully featured user tracking plugin.

== Description ==

The LBAK User Tracking plugin is a fully featured, page by page tracking plugin for your blog. Every time a page is visited on your blog, the plugin logs a whole host of information on the person who visited the page.

The plugin is totally customisable from what you decide to track to what you decide you want to see. There is an optional dashboard widget included for a quick overview of recent visitors as well as a search feature for you to accurately sift through your tracking log and find the results you are looking for.

*Data that is tracked by default:*

* IP address (including an attempt to locate IP addresses from behind a proxy)
* The HTTP referrer (what page the user was viewing before they clicked)
* The time of the click
* User ID
* User Level (admin, editor, so on)
* Display Name
* Browser
* Operating System
* The page they visited

*Data that is not tracked by default but can be enabled:*

* GET variables
* POST variables
* Cookies

Please be sure to read the "Other Notes" section to view your responsibilities
in using this plugin.

== Your Responsibility ==

Upon installing this plugin you need to realise that you are potentially logging
a lot of sensitive information on your users. You accept that by installing this
plugin you are accepting not to misuse or abuse this data in any way. I will not
take responsibility of any case of misuse of data caused by this plugin. That is
the sole responsibility of the person who installed the plugin.

== Installation ==

If you are not installing through the built in WordPress method then please
follow the following steps:

1. Download the plugin .zip file.
2. Extract the .zip file into /wp-content/plugins/ (make sure that the plugin
is located in a file called "lbak-user-tracking")
3. Go to your plugins page on your WordPress and click "Activite" under the
LBAK User Tracking plugin.
4. You're good to go! If you want to edit any settings click on "User Tracking"
under the "Tools" menu.

== Important Notes ==

This plugin uses software developed and maintained by Gary Keith. He allows
this plugin to check for updates on his site once a week. If this condition is
violated by you (which requires manual editing of the code, there is no other
way) then it is possible that you will be banned from accessing his website.

== Changelog ==

= 1.2.2 =

* Improved the browscap updating function.

= 1.2.1 =

* Fixed an issue with the event scheduling.

= 1.2 =

* Fixed the button that adds User IDs to the User ID ignore list.
* Revamped the look of the settings page.
* Added a Help/FAQ menu.
* Added a whole new stats and caching system the runs on a schedule and has
dashboard widgets.
* Browser definitions file now updates weekly.
* Made the donate button link to my donate page.
* Added an option to stop tracking admin users. Less clutter.

= 1.0.1 =

* Fixed a problem with loading external stylesheets and javascript.

= 1.0 =

* First stable release.