# Menu Image #
**Contributors:** @zviryatko  
**Tags:** menu, image, field, hover  
**Donate link:** http://makeyoulivebetter.org.ua/buy-beer  
**Requires at least:** 3.5.1  
**Tested up to:** 3.5.1  
**Stable tag:** 1.3  
**License:** GPLv2 or later  
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html  

Adds a field to load the image in a menu item and displays the image inside the link in the menu before the text.
Now you can upload the second image and set to the mouse over/out effect.
And also change position of title or hide title if need.

## Installation ##

1. Upload `menu-image` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to `/wp-admin/nav-menus.php`
4. Edit exist menu item or add new menu item and just upload image than click `Save Menu`
5. See your menu on site

## Frequently Asked Questions ##

### How to wrap menu link text in `span` html element ###

Where you show your menu with function `<?php wp_nav_menu(); ?>` as param you can add `array('link_before' => '<span>', 'link_after' => '</span>')`.
It makes css markup easier.

### How to add another size for image? ###

Just register another image size in your theme with function `add_image_size()`.

## Screenshots ##

###1. Admin screen###
![Admin screen](http://s-plugins.wordpress.org/menu-image/assets/screenshot-1.png)

###2. Menu preview in standard twenty-twelve theme###
![Menu preview in standard twenty-twelve theme](http://s-plugins.wordpress.org/menu-image/assets/screenshot-2.png)


## Changelog ##

### 1.3 ###
* Added ability to set title position, an example: before, after image or hide

### 1.2 ###
* Fix styles for hovered image

### 1.1 ###
* Added style file with vertical align of menu image item by default
* Added ability to upload image that which will be replaced on hover
* Added default image sizes for menu items: 24x24, 36x36 and 48x48

## Upgrade Notice ##

### 1.2 ###
Now you can change title text position

### 1.1 ###
Now you can upload image that replaced default on mouse hover
