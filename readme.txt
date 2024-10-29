=== Plugin Name ===
Contributors: jeroenweustink
Tags: battlefield 3, bf3, battlefield, stats, statistics, bf3stats.com
Requires at least: 3.0
Tested up to: 3.2.1
Stable tag: 1.1

A widget that shows player data received from the bf3stats.com API

== Description ==

A widget that shows player data received from the bf3stats.com API. There are several options a user can enable and disable in the widget settings.

<strong>ATTENTION</strong>: The data that is shown can be old. This is because bf3stats.com has not got a player update function yet. A soon as there is I will implement it into this widget.
You can use the "Manual Update" link in the widget settings to update the data on bf3stats.com.

Shown data will be:
<ul>
	<li>Progress</li>
	<li>Ranking</li>
	<li>Kill / death ratio</li>
	<li>Win / lose ratio</li>
	<li>Accuracy</li>
	<li>Longest headshot</li>
</ul>

Todo:
<ul>
    <li>Implement automatic player update</li>
    <li>Add cache</li>
</ul>

== Installation ==

1. Upload to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add the widget to your widgetized sections

== Screenshots ==

1. Widget settings
2. Output example on blog

== Changelog ==

= 1.1 =
- Added none file based caching
- Added form option for caching time in minutes
- Preparation for playerupdate function
- Added error handling for API 

= 1.0.1 =
- Fixed error bug
- Code cleanup
- Removed caching (for now)

= 1.0 =
- First release