=== LBAK User Tracking ===
Contributors: samwho
Author: Sam Rose
Author URL: http://lbak.co.uk/
Donate link: http://donate.lbak.co.uk/
Tags: tracking, logging, activity, lbak, auditing, record
Requires at least: 3.0
Tested up to: 3.0.1
Stable tag: 1.7.8

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

= 1.7.8 =

* Attempting to fix a bug about function declaration clashes.

= 1.7.7 =

* Fixed a bug with curl_setopt_array not existing on servers running older
version of PHP.

= 1.7.6 =

* Fixed an apparent javascript conflict bug. This should hopefully fix the
drag and drop errors many of you have been having with other plugins.

= 1.7.5 =

* Fixed a bug that created massive amounts of errors due to attempting to
duplicate entries in the browser cache database table.
* Fixed the page visits pie chart to match the recent changes to how page
names are logged.
* Changed the pie charts to only display the top 15 and categorise the rest
as "other". This is necessary due to a URI limit on the Google Charts API.

= 1.7.4 =

* Fixed 2 bugs, 1 was an undefined function call and the other was unvalidated
data.

= 1.7.3 =

* Fixed a pretty large bug that stopped pages with "pretty" links from being
logged properly. As a result, the way pages have been logged have been changed
and this will reflect on your page views stats but they should be back up to
scratch in no time :)
* Fixed a security bug in the dashboard widget, thanks to cyberczar in the
thread http://wordpress.org/support/topic/plugin-lbak-user-tracking-exposure-of-admin-credentials.

= 1.7.2 =

* Fixed yet another issue with stats logging. 

= 1.7.1 =

* Fixed another issue with stats logging.

= 1.7 =

* Fixed some issues with stats logging caused by recent changes.
* Added a new stats and error logging function to help me improve the plugin.

= 1.6 =

* Changed the layout of the settings page.
* Changed the way stats tracking works. Old data no longer gets deleted.
* Significant internal changes to the code layout.

= 1.5.1 =

* Small changes to the tooltip system. Hovering the page view tooltip left
instead of right and added word wrapping to stop overflow.

= 1.5 =

* Added in a new tooltip system to help display more info easier.

= 1.4.2 =

* Fixed a divide by zero on the stats page.

= 1.4.1 =

* Important bug fix regarding the stats page.

= 1.4 =

* Added the new interactive FAQ section.
* Fixed the database management scheduler.

= 1.3 =

* Added in the database management options.

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

== Frequently Asked Questions ==

The frequently asked questions can be found either inside the plugin or at
http://lbak.co.uk/faq.php?step=get&tag=lbakut

== Screenshots ==

1. A screenshot of the LBAK User Tracking optional dashboard widget.
2. The stats page allows you to add some stats widgets to the dashboard.
3. The LBAK User Tracking search feature that allows you to search your
entire activity log database for what you need.
4. The database management page for when you feel you have a little bit too much
data.