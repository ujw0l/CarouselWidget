=== Carousel Widget ===
Contributors: ujw0l
Tags: carousel, widget, portfolio 
Requires at least: 3.4+
Tested up to: 5.5
Stable tag: 2.5.0
License: GPLv2

Plugin that provides users option to display carousel widget for sidebar where user can add custom link for the site.

== Description ==

This plugin lets you to display carousel widget on sidebar with the themes that supports sidebar (it does not work properly on footer). You can set custom height, width and autoplay through widget settings.

It also lets users to add the bigger carousel on the page. User can navigate to next and previous slide. You can set custom height, custom width and autoplay. 

== Installation ==

1. Upload the folder ` carouselwidget` and its contents to the `/wp-content/plugins/` directory or use the wordpress plugin installer
2. Activate the plugin through the 'Plugins' menu in WordPress
3. A new "Carousel Widget" will be available under Appearance > Widgets, where you can add it to your sidebar
4. At the Setting section on dashboard ‘Carousel Widget’ will appear where developer can add images and information they wish to display on carousel.  
5.To add carousel on page use shortcode [inpage_carousel].
6. To add custom height and/or width  [inpage_carousel heigth="custom height inpx" width="custom width in px"]
7. To set carousel on autoplay use  [inpage_carousel auto_play="true" ] ;

= Uninstall =

1. Deactivate Carousel Widget in the 'Plugins' menu in Wordpress.
2. After Deactivation a 'Delete' link appears below the plugin name, follow the link and confim with 'Yes, Delete these files'.
3. This will delete all the plugin files from the server as well as erasing all options the plugin has stored in the database.

== Frequently Asked Questions ==

= Where can I use this plugin? =

You can use this plugin to display images on sidebar and on page with option to autoplay.

== Screenshots ==

1.  Screenshot of the carousel
2.  Screenshot of widget
3.  Screenshot of settings/info section
4.  Screenshot of Item list on carousel
5.  Screenshot of carousel demo in backend

== Changelog ==

= 1.0.0 =
* This is first stable version 
* Many upgrades to follow

= 1.1.0 =
* If upgrading  also update image urls in order for it to work 
* It has lot better background compared to last version
* You can also view the demo of both carousel and widget at backend
* Better display of items in database
* Better form validation in backend
* Lots of minor bug fixes
* more featurs to follow in future releases
* You might need change some of the CSS manually to make it work with your theme. 

= 2.0.0 =
* Roundabout jQuery removed
* Runs on carousel library written invanilla js
* Better Admin Uni
* User can choose height width and auto play option 

= 2.0.2 =
*Deactivation bug fix

= 2.5.0 =
*Bug fixes
*Added image carousel for inpage
*Carousel widget href moved to center
*CTC overlay update