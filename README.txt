=== Business Hours Plugin  ===
Contributors: MZAWeb
Donate link: http://danieldvork.in
Tags: working hours, working, business hours, business, hours, stores, branches
Requires at least: 3.1
Tested up to: 3.5
Stable tag: 2.0

Business Hours lets you show to your visitors the time you open and close your business each day of the week.
You can setup open and close hours for each day of the week, and then create exceptions for specific dates (holidays, etc)

== Description ==
The Business Hours Plugin allows you to post your daily working hours and show it to your visitors:

*	In a configurable and templatable widget.
*	In a page / post using shortcodes

You'll be able to choose between showing only today's working hours, or a collapsible table with the hours for each day of the week.
If you want to show only today's working hours, the plugin will check your timezone settings to calculate which day to show.

Also, Business Hours is 100% translatable to your language.
We've included language files for:

*	Spanish
*	Dutch

If you want to help me translate this plugin to your language, please drop me a line or contact me in the [support forum](http://wordpress.org/tags/business-hours-plugin#postform)


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

1. Business Hours Settings
2. Exceptions Settings
3. Widget Settings
4. Front-End Widget: Closed
5. Front-End Widget: Working
6. Front-End Widget: Full week, with an exception

== Upgrade Notice ==

= 2.0 =
Introducing Exceptions (Holidays, etc). Also, this version is (almost) a complete rewrite. Faster, more secure and with a lot of improvements.

== Changelog ==

= 2.0 =
* Feature: Allow for exceptions (for holidays, etc)
* Enhancement: Allow users to overide views templates
* Enhancement: Allow to show the hours table fixed in the widget (without collapsible)
* Enhancement: Cleanup widget admin. Hide templating fields. Clarify some texts.
* Enhancement: Cleanup settings admin. Sexier and more intuitive.
* Enhancement: The plugin will respect the "Week Starts On" value from Settings->General to show the days
* Enhancement: Works with WordPress 3.5
* Enhancement: General cleaning and improve architecture.
* Enhancement: Improve code quality ( a lot! )
* Enhancement: Improve loading speed
* Bugfix: Fixed how I'm handling timezones to account for DST
* Bugfix: Fixed minor bugs for WordPress 3.4.2
* Bugfix: Added missing getText calls in hardcoded strings
* New filter: *business-hours-closed-text*
* New filter: *business-hours-open-hour*
* New filter: *business-hours-close-hour*
* New filter: *business-hours-is-open-today*
* New filter: *business-hours-view-template*
* New filter: *business-hours-collapsible-link-anchor*
* New filter: *business-hours-exceptions-how-many-years*
* New filter: *business-hours-save-settings*
* New filter: *business-hours-row-class*
* New action: *business-hours-settings-page*
* New action: *business-hours-before-row*
* New action: *business-hours-after-row*

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