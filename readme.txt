=== Plugin Name ===
Contributors: zviryatko
Tags: menu, image, field
Requires at least: 3.5.1
Tested up to: 3.5.1
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds a field to load the image in a menu item and displays the image inside the link in the menu before the text.

== Installation ==

1. Upload `menu-image` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to `/wp-admin/nav-menus.php`
4. Edit exist menu item or add new menu item and just upload image than click `Save Menu`
5. See your menu on site

== Frequently Asked Questions ==

= How to wrap menu link text in `span` html element =

Where you show your menu with function `<?php wp_nav_menu(); ?>` as param you can add `array('link_before' => '<span>', 'link_after' => '</span>')`.
It makes css markup easier.

= How to align text vertically? =

Wrap link text in <span> element and add to your style.css: `.menu li a img { vertical-align: middle; }`.

= How to add another size for image? =

Just register another image size in your theme with function `add_image_size()`.

== Screenshots ==

1. Admin screen
2. Menu preview in standart twenty-twelve theme