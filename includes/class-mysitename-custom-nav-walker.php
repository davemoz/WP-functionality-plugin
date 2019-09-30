<?php
/**
 * Custom Nav Walker with FontAwesome icon support for  social media and cart items.
 * by Aurooba Ahmed
 * This requires Font Awesome(!!enqueued with theme!!->TBD) and adds in the correct icon by detecting the URL of the menu item.
 * 
 * You can use this by doing a custom wp_nav_menu query:
 * wp_nav_menu(array('items_wrap'=> '%3$s', 'walker' => new MySiteName_Nav_Walker(), 'container'=>false, 'menu_class' => '', 'theme_location'=>'social', 'fallback_cb'=>false ));
 *
 * @package     MySiteName
 */

/**
 * This MySiteName_Nav_Walker
 */
class MySiteName_Nav_Walker extends Walker_Nav_Menu {
	function start_lvl(&$output, $depth = 0, $args = array()) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent\n";
	}
	function end_lvl(&$output, $depth = 0, $args = array()) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent\n";
	}
	function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
		$indent = ($depth) ? str_repeat("\t", $depth) : '';
		$class_names = $value = '';
		$classes = empty($item->classes) ? array() : (array)$item->classes;
		$classes[] = 'menu-item-' . $item->ID;
		$class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
		$class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
		$id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args);
		$id = $id ? ' id="' . esc_attr($id) . '"' : '';
		$title = $item->title;
		$output .= $indent . '<li id="menu-item-' . $item->ID . '"' . $value . $class_names . '>';
		$attributes  = !empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) . '"' : '';
		$attributes .= !empty($item->target)     ? ' target="' . esc_attr($item->target) . '"' : '';
		$attributes .= !empty($item->xfn)        ? ' rel="'    . esc_attr($item->xfn) . '"' : '';
		$attributes .= !empty($item->url)        ? ' href="'   . esc_attr($item->url) . '"' : '';
		if ( class_exists( 'WooCommerce' ) ) {
			global $woocommerce;
			$cart_contents_count = $woocommerce->cart->cart_contents_count;
		}
		if (strpos($item->url, 'facebook') !== false) {
			$item_output .= '<a' . $attributes . '><i class="fab fa-facebook-f"></i>';
			$item_output .= '</a>';
			$item_output .= $args->after;
		} elseif (strpos($item->url, 'twitter') !== false) {
				$item_output .= '<a' . $attributes . '><i class="fab fa-twitter">';
				$item_output .= '</i></a>';
				$item_output .= $args->after;
			} elseif (strpos($item->url, 'instagram') !== false) {
				$item_output .= '<a' . $attributes . '><i class="fab fa-instagram">';
				$item_output .= '</i></a>';
				$item_output .= $args->after;
			} elseif (strpos($item->url, 'pinterest') !== false) {
				$item_output .= '<a' . $attributes . '><i class="fab fa-pinterest-p">';
				$item_output .= '</i></a>';
				$item_output .= $args->after;
			} elseif (strpos($item->url, 'linkedin') !== false) {
				$item_output .= '<a' . $attributes . '><i class="fab fa-linkedin-in">';
				$item_output .= '</i></a>';
				$item_output .= $args->after;
			} elseif (strpos($item->url, 'snapchat') !== false) {
				$item_output .= '<a' . $attributes . '><i class="fab fa-snapchat-ghost">';
				$item_output .= '</i></a>';
				$item_output .= $args->after;
			} elseif (strpos($item->url, 'plus.google') !== false) {
				$item_output .= '<a' . $attributes . '><i class="fab fa-google-plus-g">';
				$item_output .= '</i></a>';
				$item_output .= $args->after;
			} elseif (strpos($item->url, 'youtube') !== false) {
				$item_output .= '<a' . $attributes . '><i class="fab fa-youtube">';
				$item_output .= '</i></a>';
				$item_output .= $args->after;
			} elseif (strpos($item->url, 'vimeo') !== false) {
				$item_output .= '<a' . $attributes . '><i class="fab fa-vimeo-v">';
				$item_output .= '</i></a>';
				$item_output .= $args->after;
			} elseif ( ( stripos($item->title, 'cart') !== false ) && class_exists( 'WooCommerce' ) ) {
				$item_output .= '<a' . $attributes . ' class="menu-item-cart"><i class="fas fa-shopping-cart">';
				$item_output .= '</i>';
				$item_output .= $cart_contents_count == 0 ? '</a>' : '<span id="cart-count">'. $cart_contents_count .'</span></a>';
				$item_output .= $args->after;
			} else {
				$item_output .= '<a' . $attributes . '>' . $title;
				$item_output .= '</a>';
				$item_output .= $args->after;
			}
			$output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
		}
		function end_el(&$output, $item, $depth = 0, $args = array()) {
			$output .= "\n";
		}
}