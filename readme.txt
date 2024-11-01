=== Plugin Name ===
Contributors:rfrankel
Donate link: http://wp-super-heatmap.swampedpublishing.com/
Tags: heatmap, heat map, click map, clickmap, analytics
Requires at least: 3.0
Tested up to: 3.2.1
Stable tag: 0.1.0

This plugin tracks user clicks and creates a heatmap for your website. All data is stored locally and no third-party service is used. Completely free!

== Description ==

This plugin was created to give WordPress users a simple way of creating heatmaps for their website without any cost and without using third-party services.  All of the click-track data is stored locally and the heatmap is also calculated on your own server.  I tried to make the interface as simple as possible and anyone should be able to use this plugin without much trouble.  

Please note that this is currently an Alpha release of this plugin and please report any bugs to [our GitHub repository.](https://github.com/ryan-frankel/wp_super_heatmap/issues?sort=created&direction=desc&state=open)

You can also visit this plugins [homepage to leave feedback](http://wp-super-heatmap.swampedpublishing.com/) and to also get more detailed information about the plugin.  If you have ideas to improve the plugin please leave your comments on that page.

== Installation ==

1. Upload the '`wp_super_heatmap`' folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Click on the '`WP Super Heatmap`' Menu under the 'Settings' tab in your WordPress administration area.
4. Enable click tracking by switching '`Turn Click Tracking On/Off`' to *ON*.
5. Wait anywhere from 1-7 days to track clicks.
6. Enable the heatmap display buy turning '`Turn Heatmap Display Button On/Off`' to *ON*.
7. While logged in as an administrator you will see a '`Display Heatmap`' tab in the front-end.  Simply click this and your heatmap will be calculated.  Please note that if you have a large number of clicks this process may take a few minutes.

== Frequently Asked Questions ==

= How long does it take to process each pages heatmap? =

Once you click on display heatmap the processing time varies depending on how many clicks you have had on your site.  For a few hundred clicks the processing will take a few seconds.  or a few thousand clicks it could take a few minutes.  As with most heatmap tools you don't want to run your heatmap for more then about a week to get good results.

= Can I change my websites HTML/Layout while I am tracking clicks? =

Since WP Super Heatmap actually uses the structure of your Website to track clicks it is highly **not recommended** to modify your HTML/Layout while tracking clicks.  If you would like to change the structure or layout of your site you should clear the database and start a new heatmap.

== Screenshots ==

1. The Administration panel for WP Super Heatmap.
2. This is the second screen shot

== Changelog ==
= 0.1.0 =
Added a fix for target="_blank" not working on links.

= 0.0.2 =
* Fixed bug regarding nested link following.

= 0.0.1 =
* Aplha release.  Initial version of the plugin.

== Upgrade Notice ==

= 0.1.0 =
Fixed target='_blank' error not opening in a new tab/window.  Upgrade to 0.1.0 is highly recommended.

= 0.0.2 =
* Fixed link bug.  You should upgrade immediately. 

= 0.0.1 =
* Alpha release, no upgrade information.

