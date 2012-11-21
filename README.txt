=== Business Hours Plugin  ===
Contributors: MZAWeb, thinkwolfpack
Donate link: http://mzaweb.com
Tags: working hours, working, business hours, business, hours
Requires at least: 3.1
Tested up to: 3.3.1
Stable tag: 1.3.2

Business Hours lets you show to your visitors the time you open and close your business each day of the week.

== Description ==
The Business Hours Plugin allows you to post your daily working hours and show it to your visitors:

*	In a configurable and templatable widget.
*	In a page / post using shortcodes

You'll be able to choose between showing only today's working hours, or a table with the hours for each day of the week.
If you want to show only today's working hours, the plugin will check your timezone settings to calculate which day to show.

Also, Business Hours is 100% translatable to your language.
We've included language files for:

*	Spanish
*	Dutch

If you want to help us translate this plugin to your language, please drop us a line or contact us in the [support forum](http://wordpress.org/tags/business-hours-plugin#postform)


== Installation ==

1. Upload the plugin to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings->Working Hours to configure your schedule
4. Go to Appearance->Widgets and add the "Working Hours" widget in the sidebar you want.

== Frequently Asked Questions ==

1. What timezone is the plugin using to determine when the day changes?

This plugins uses the timezone configured in WordPress settings. ( Settings->General )

2. Shortcodes what??

The shortcode you need to use is this:

`[businesshours closed="TEXT FOR WHEN ITS CLOSED"]TEMPLATE[/businesshours]`

As TEMPLATE you should use **{{TodayOpen}}** for today's open hours and **{{TodayClose}}** for closing hours.

For example:

`[businesshours closed="Today is closed."]Today we work from {{TodayOpen}} to {{TodayClose}}[/businesshours]`

Also there's another shortcode that allows you to show the full week table:

`[businesshoursweek]`

You can also use

`[businesshoursweek collapsible="true"]`

to have the list collapsed by default and a link to open it.


== Screenshots ==

1. Working Hours
2. Widget settings
3. Front-End: Closed
4. Front-End: Working

== Upgrade Notice ==

= 1.3.2 =
* Fixed how the plugin handles weekdays names localization
* Added dutch language files

= 1.3.1 =
* Added Spanish language files
* Fixed some localization related issues

= 1.3 =
* Added the shortcodes
* Added localization and english lang file
* Added text-align=center; to: .business_hours_table_closed, .business_hours_table_heading, .business_hours_table_open and .business_hours_table_close
* Fix: Spelling collapsable for collapsible.

= 1.2 =
* Added an optional collapsable table in the widget showing the working hours for all weekdays.
* Fixed the mess of different names (working hours, open hours, etc) in favour of Business Hours.
* Added support email
* WordPress 3.3 compatible.

= 1.1 =
* Some minor bug fixes

= 1.0.2 =
* Fixed screenshots

= 1.0.1 =
* Fixed plugin info in php header

= 1.0 =
* First release