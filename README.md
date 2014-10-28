# Menu Image #
**Contributors:** zviryatko  
**Tags:** menu, image, field, hover, wpml  
**Donate link:** http://makeyoulivebetter.org.ua/buy-beer  
**Requires at least:** 3.5.1  
**Tested up to:** 4.0  
**Stable tag:** 2.4  
**License:** GPLv2 or later  
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html  

Adds a field to load the image in a menu item and displays the image inside the link in the menu before the text.
Now WPML compliant.

## Description ##

Adds a field to load the image in a menu item and displays the image inside the link in the menu before the text.
Now you can upload the second image and set to the mouse over/out effect.
And also change position of title or hide title if need.
And... load images via media uploader!
Now WPML compliant!

## Installation ##

1. Upload `menu-image` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to `/wp-admin/nav-menus.php`
4. Edit exist menu item or add new menu item and just upload image than click `Save Menu`
5. See your menu on site
6. (WMPL users only) Goto WPML > WP Menus Sync and click to `Sync`

## Frequently Asked Questions ##

### How to wrap menu link text in `span` html element ###

Where you show your menu with function `<?php wp_nav_menu(); ?>` as param you can add `array('link_before' => '<span>', 'link_after' => '</span>')`.
It makes css markup easier.

### How to add another size for the image? ###

To add a new size (or remove an old one) add a function to the `menu_image_default_sizes` filter. For example

`
<?php
add_filter( 'menu_image_default_sizes', function($sizes){

  // remove the default 36x36 size
  unset($sizes['menu-36x36']);

  // add a new size
  $sizes['menu-50x50'] = array(50,50);

  // return $sizes (required)
  return $sizes;

});
?>
`

## Screenshots ##

### 1. Admin screen ###
![Admin screen](http://ps.w.org/menu-image/assets/screenshot-1.png)

### 2. Menu preview in standard twenty-thirteen theme ###
![Menu preview in standard twenty-thirteen theme](http://ps.w.org/menu-image/assets/screenshot-2.png)


## Changelog ##

### 2.4 ###
* Fix compatibility with some modules and themes to according to [this topic](http://shazdeh.me/2014/06/25/custom-fields-nav-menu-items/)
* Fix Jetpack Phonon frontend bug

### 2.3 ###
* WPML menus sync support. Thanx @pabois for [feature request](http://wordpress.org/support/topic/very-good-wpml-compliant)

### 2.2 ###
* Added grunt-wp-readme-to-markdown npm package for converting readme to markdown for github users.

### 2.1 ###
* Fix [set-image-button-not-working](http://wordpress.org/support/topic/set-image-button-not-working)
* Fix [vertical-align-when-using-mouseover-image](ttp://wordpress.org/support/topic/vertical-align-when-using-mouseover-image)

### 2.0 ###
* Added support of media uploader.
* Fixed php strict warnings.
* Added .ico image support, thanks to [ivol84](https://github.com/ivol84)

### 1.3 ###
* Added ability to set title position, an example: before, after image or hide

### 1.2 ###
* Fix styles for hovered image

### 1.1 ###
* Added style file with vertical align of menu image item by default
* Added ability to upload image that which will be replaced on hover
* Added default image sizes for menu items: 24x24, 36x36 and 48x48

## Upgrade Notice ##

### 2.4 ###
If your are using Jetpack Phonon module now menu icons will be look good.

### 2.3 ###
If your are using WPML plugin, now when you sync menus, images will synced too.

### 2.0 ###
WARNING! You need to re-select the images! Now, with media uploader support, it's easy peasy.
Media uploader support. Upload once, use many times!

### 1.2 ###
Now you can change title text position

### 1.1 ###
Now you can upload image that replaced default on mouse hover
