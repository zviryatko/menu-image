<?php
/**
 * @package Menu_Image
 * @version 1.0
 * @licence GPLv2
 */

/*
Plugin Name: Menu Image
Plugin URI: http://html-and-cms.com/portfolio/menu-image/
Description: Provide uploading images to menu item
Author: Alex Davyskiba aka Zviryatko
Version: 1.0
Author URI: http://makeyoulivebetter.org.ua/
*/

/*  Copyright 2013  Zviryatko  (email : sanya.davyskiba@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function menu_image_init() {
	add_post_type_support('nav_menu_item', array('thumbnail'));
}

add_action('init', 'menu_image_init');

function menu_image_nav_menu_manage_columns($columns) {
	return $columns + array('image' => __('Image', 'menu-image'));
}

add_filter('manage_nav-menus_columns', 'menu_image_nav_menu_manage_columns', 11);

function menu_image_save_post_action($post_id, $post) {
	if (isset($_POST['menu_item_image_size'][$post_id]) && !empty($_POST['menu_item_image_size'][$post_id])) {
		update_post_meta($post_id, '_menu_item_image_size', esc_sql($_POST['menu_item_image_size'][$post_id]));
	}

	if (!empty($_FILES["menu-item-image_$post_id"])) {
		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		require_once(ABSPATH . "wp-admin" . '/includes/file.php');
		require_once(ABSPATH . "wp-admin" . '/includes/media.php');

		$attachment_id = media_handle_upload("menu-item-image_$post_id", $post_id);
		if ($attachment_id) {
			set_post_thumbnail($post, $attachment_id);
		}
	}
	elseif (isset($_POST['menu_item_remove_image'][$post_id]) && !empty($_POST['menu_item_remove_image'][$post_id])) {
		$attachment_id = get_post_thumbnail_id($post_id);
		delete_post_thumbnail($attachment_id);
		wp_delete_attachment($attachment_id);
		delete_post_meta('_menu_item_image_size', $post_id);
	}
}

add_action('save_post', 'menu_image_save_post_action', 10, 2);

function menu_image_edit_nav_menu_walker_filter() {
	return 'Menu_Image_Walker_Nav_Menu_Edit';
}

add_filter('wp_edit_nav_menu_walker', 'menu_image_edit_nav_menu_walker_filter');

require_once(ABSPATH . 'wp-admin/includes/nav-menu.php');
class Menu_Image_Walker_Nav_Menu_Edit extends Walker_Nav_Menu_Edit {

	function start_el(&$output, $item, $depth, $args) {
		global $_wp_nav_menu_max_depth;
		$_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

		$indent = ($depth) ? str_repeat("\t", $depth) : '';

		ob_start();
		$item_id      = esc_attr($item->ID);
		$removed_args = array(
			'action',
			'customlink-tab',
			'edit-menu-item',
			'menu-item',
			'page-tab',
			'_wpnonce',
		);

		$original_title = '';
		if ('taxonomy' == $item->type) {
			$original_title = get_term_field('name', $item->object_id, $item->object, 'raw');
			if (is_wp_error($original_title)) {
				$original_title = FALSE;
			}
		}
		elseif ('post_type' == $item->type) {
			$original_object = get_post($item->object_id);
			$original_title  = $original_object->post_title;
		}

		$classes = array(
			'menu-item menu-item-depth-' . $depth,
			'menu-item-' . esc_attr($item->object),
			'menu-item-edit-' . ((isset($_GET['edit-menu-item']) && $item_id == $_GET['edit-menu-item']) ? 'active' : 'inactive'),
		);

		$title = $item->title;

		if (!empty($item->_invalid)) {
			$classes[] = 'menu-item-invalid';
			/* translators: %s: title of menu item which is invalid */
			$title = sprintf(__('%s (Invalid)'), $item->title);
		}
		elseif (isset($item->post_status) && 'draft' == $item->post_status) {
			$classes[] = 'pending';
			/* translators: %s: title of menu item in draft status */
			$title = sprintf(__('%s (Pending)'), $item->title);
		}

		$title = empty($item->label) ? $title : $item->label;

		$image_size = get_post_meta($item_id, '_menu_item_image_size', TRUE);
		?>
	<li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode(' ', $classes); ?>">
		<dl class="menu-item-bar">
			<dt class="menu-item-handle">
				<span class="item-title"><?php echo esc_html($title); ?></span>
					<span class="item-controls">
						<span class="item-type"><?php echo esc_html($item->type_label); ?></span>
						<span class="item-order hide-if-js">
							<a href="<?php
							echo wp_nonce_url(
								add_query_arg(
									array(
										'action'    => 'move-up-menu-item',
										'menu-item' => $item_id,
									),
									remove_query_arg($removed_args, admin_url('nav-menus.php'))
								),
								'move-menu_item'
							);
							?>" class="item-move-up"><abbr title="<?php esc_attr_e('Move up'); ?>">&#8593;</abbr></a>
							|
							<a href="<?php
							echo wp_nonce_url(
								add_query_arg(
									array(
										'action'    => 'move-down-menu-item',
										'menu-item' => $item_id,
									),
									remove_query_arg($removed_args, admin_url('nav-menus.php'))
								),
								'move-menu_item'
							);
							?>" class="item-move-down"><abbr title="<?php esc_attr_e('Move down'); ?>">&#8595;</abbr></a>
						</span>
						<a class="item-edit" id="edit-<?php echo $item_id; ?>" title="<?php esc_attr_e('Edit Menu Item'); ?>" href="<?php
						echo (isset($_GET['edit-menu-item']) && $item_id == $_GET['edit-menu-item']) ? admin_url('nav-menus.php') : add_query_arg('edit-menu-item', $item_id, remove_query_arg($removed_args, admin_url('nav-menus.php#menu-item-settings-' . $item_id)));
						?>"><?php _e('Edit Menu Item'); ?></a>
					</span>
			</dt>
		</dl>

		<div class="menu-item-settings" id="menu-item-settings-<?php echo $item_id; ?>">
			<?php if ('custom' == $item->type) : ?>
				<p class="field-url description description-wide">
					<label for="edit-menu-item-url-<?php echo $item_id; ?>">
						<?php _e('URL'); ?><br/>
						<input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]" value="<?php echo esc_attr($item->url); ?>"/>
					</label>
				</p>
			<?php endif; ?>
			<p class="description description-thin">
				<label for="edit-menu-item-title-<?php echo $item_id; ?>">
					<?php _e('Navigation Label'); ?><br/>
					<input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr($item->title); ?>"/>
				</label>
			</p>

			<p class="description description-thin">
				<label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">
					<?php _e('Title Attribute'); ?><br/>
					<input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr($item->post_excerpt); ?>"/>
				</label>
			</p>

			<p class="field-link-target description">
				<label for="edit-menu-item-target-<?php echo $item_id; ?>">
					<input type="checkbox" id="edit-menu-item-target-<?php echo $item_id; ?>" value="_blank" name="menu-item-target[<?php echo $item_id; ?>]"<?php checked($item->target, '_blank'); ?> />
					<?php _e('Open link in a new window/tab'); ?>
				</label>
			</p>

			<p class="field-css-classes description description-thin">
				<label for="edit-menu-item-classes-<?php echo $item_id; ?>">
					<?php _e('CSS Classes (optional)'); ?><br/>
					<input type="text" id="edit-menu-item-classes-<?php echo $item_id; ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $item_id; ?>]" value="<?php echo esc_attr(implode(' ', $item->classes)); ?>"/>
				</label>
			</p>

			<p class="field-image description description-wide">
				<?php if (!has_post_thumbnail($item_id)) : ?>
					<label for="edit-menu-item-image-<?php echo $item_id; ?>">
						<?php _e('Image', 'menu-image'); ?><br/>
						<input type="file" name="menu-item-image_<?php echo $item_id; ?>" id="edit-menu-item-image-<?php echo $item_id; ?>"/>
					</label>
				<?php else: ?>
					<?php print get_the_post_thumbnail($item_id, $image_size); ?><br/>
					<?php $sizes = get_intermediate_image_sizes(); ?>
					<label><?php _e("Size", 'menu-image'); ?>
						<select name="menu_item_image_size[<?php echo $item_id; ?>]">
							<?php foreach ($sizes as $size) : ?>
								<?php $selected = ($image_size == $size) ? ' selected="selected"' : ''; ?>
								<option value="<?php echo $size; ?>"<?php echo $selected; ?>><?php echo ucfirst($size); ?></option>
							<?php endforeach; ?>
						</select>
					</label>
					<br />
					<label><?php _e("Remove image", 'menu-image'); ?> <input type="checkbox" name="menu_item_remove_image[<?php echo $item_id; ?>]"/></label>
				<?php endif; ?>
			</p>
			<p class="field-xfn description description-thin">
				<label for="edit-menu-item-xfn-<?php echo $item_id; ?>">
					<?php _e('Link Relationship (XFN)'); ?><br/>
					<input type="text" id="edit-menu-item-xfn-<?php echo $item_id; ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id; ?>]" value="<?php echo esc_attr($item->xfn); ?>"/>
				</label>
			</p>

			<p class="field-description description description-wide">
				<label for="edit-menu-item-description-<?php echo $item_id; ?>">
					<?php _e('Description'); ?><br/>
					<textarea id="edit-menu-item-description-<?php echo $item_id; ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo $item_id; ?>]"><?php echo esc_html($item->description); // textarea_escaped ?></textarea>
					<span class="description"><?php _e('The description will be displayed in the menu if the current theme supports it.'); ?></span>
				</label>
			</p>

			<div class="menu-item-actions description-wide submitbox">
				<?php if ('custom' != $item->type && $original_title !== FALSE) : ?>
					<p class="link-to-original">
						<?php printf(__('Original: %s'), '<a href="' . esc_attr($item->url) . '">' . esc_html($original_title) . '</a>'); ?>
					</p>
				<?php endif; ?>
				<a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php
				echo wp_nonce_url(
					add_query_arg(
						array(
							'action'    => 'delete-menu-item',
							'menu-item' => $item_id,
						),
						remove_query_arg($removed_args, admin_url('nav-menus.php'))
					),
					'delete-menu_item_' . $item_id
				); ?>"><?php _e('Remove'); ?></a> <span class="meta-sep"> | </span> <a class="item-cancel submitcancel" id="cancel-<?php echo $item_id; ?>"
				                                                                       href="<?php    echo esc_url(add_query_arg(array('edit-menu-item' => $item_id, 'cancel' => time()), remove_query_arg($removed_args, admin_url('nav-menus.php'))));
				                                                                       ?>#menu-item-settings-<?php echo $item_id; ?>"><?php _e('Cancel'); ?></a>
			</div>

			<input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]" value="<?php echo $item_id; ?>"/>
			<input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr($item->object_id); ?>"/>
			<input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]" value="<?php echo esc_attr($item->object); ?>"/>
			<input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr($item->menu_item_parent); ?>"/>
			<input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]" value="<?php echo esc_attr($item->menu_order); ?>"/>
			<input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]" value="<?php echo esc_attr($item->type); ?>"/>
		</div>
		<!-- .menu-item-settings-->
		<ul class="menu-item-transport"></ul>
		<?php
		$output .= ob_get_clean();
	}
}

function menu_image_nav_menu_item_filter($item_output, $item, $depth, $args) {
	$attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
	$attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
	$attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
	$attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';

	$image_size = get_post_meta($item->ID, '_menu_item_image_size', TRUE);
	$image      = get_the_post_thumbnail($item->ID, $image_size);

	$item_output = $args->before;
	$item_output .= '<a' . $attributes . '>';
	$item_output .= $image . $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
	$item_output .= '</a>';
	$item_output .= $args->after;
	return $item_output;
}

add_filter('walker_nav_menu_start_el', 'menu_image_nav_menu_item_filter', 10, 4);