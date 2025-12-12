<?php
/**
 * Plugin Name: d-mind WP Searchfield
 * Description: Macht ein SVG-Icon als Suchfeld ins Hauptmenü, accessible, mit Overlay-Suchformular
 * Version: 1.0
 * Author: Jens Fuchs
 * Author URI: https://www.d-mind.de
 */

if (!defined('ABSPATH')) {
	exit;
}

if (!defined('DMIND_SEARCHFIELD_PLUGIN_DIR')) {
	define('DMIND_SEARCHFIELD_PLUGIN_DIR', 'dmind-wp-searchfield');
}

function dmind_searchfield_page(): void {
	add_submenu_page(
		'options-general.php',      // Parent menu slug
		'Suchfeld',            // Page title
		'Suchfeld',            // Menu title (Fehlte zuvor)
		'manage_options',           // Capability
		'dmind-wp-searchfield',      // Menu slug
		'dmind_searchfield'       // Callback function
	);
}

add_action('admin_menu', 'dmind_searchfield_page');

// Funktion für die Admin-Seite
function dmind_searchfield(): void {
	echo '<div class="wrap">';
	echo '<h1>' . __('Suchfeld-Einstellungen', DMIND_SEARCHFIELD_PLUGIN_DIR) . '</h1>';
	echo '<form method="post" action="options.php">';
	settings_fields('dmind_searchfield_settings_group'); // Name der Einstellungsgruppe
	do_settings_sections('dmind-wp-searchfield'); // Slug der Seite
	submit_button();
	echo '</form>';
	echo '</div>';
}

function dmind_searchfield_settings() {
	register_setting('dmind_searchfield_settings_group', 'dmind_searchform_extraclass');
	register_setting('dmind_searchfield_settings_group', 'dmind_searchform_hook');
	register_setting('dmind_searchfield_settings_group', 'dmind_searchform_hook_icon');

	add_settings_section(
		'dmind_searchform_section',
		'Settings',
		null,
		'dmind-wp-searchfield'
	);

	add_settings_field(
		'dmind_searchform_hook',
		'Hook, wo das Suchfeld eingebunden wird, welches bei Klick eingeblendet wird',
		'dmind_searchform_hook_callback',
		'dmind-wp-searchfield',
		'dmind_searchform_section'
	);

	add_settings_field(
		'dmind_searchform_hook_icon',
		'Hook, wo das Suchicon (Lupe) eingebunden wird (optional, wenn leer, wird es ins Menü eingebunden)',
		'dmind_searchform_hook_icon_callback',
		'dmind-wp-searchfield',
		'dmind_searchform_section'
	);

	add_settings_field(
		'dmind_searchform_extraclass',
		'Extra-CSS-klasse für das Menü-Element',
		'dmind_searchform_extraclass_callback',
		'dmind-wp-searchfield',
		'dmind_searchform_section'
	);
}
add_action('admin_init', 'dmind_searchfield_settings');

function dmind_searchform_extraclass_callback() {
	$value = get_option('dmind_searchform_extraclass', '');
	echo '<input type="text" name="dmind_searchform_extraclass" value="' . esc_attr($value) . '" />';
}

function dmind_searchform_hook_callback() {
	$value = get_option('dmind_searchform_hook', '');
	echo '<input type="text" name="dmind_searchform_hook" value="' . esc_attr($value) . '" />';
}

function dmind_searchform_hook_icon_callback() {
	$value = get_option('dmind_searchform_hook_icon', '');
	echo '<input type="text" name="dmind_searchform_hook_icon" value="' . esc_attr($value) . '" />';
}


/**
 * Enqueue scripts and styles
 */
add_action('wp_enqueue_scripts', function () {
	$csspath = WP_PLUGIN_DIR . '/'.DMIND_SEARCHFIELD_PLUGIN_DIR.'/assets/css/';
	$filescss = preg_grep('/^([^.])/', scandir($csspath));
	foreach ($filescss as $filename) {
		$path = $csspath . $filename;
		$pathrel = plugins_url(DMIND_SEARCHFIELD_PLUGIN_DIR) . '/assets/css/' . $filename;
		if (is_file($path)) {
			$extension = pathinfo($path, PATHINFO_EXTENSION);
			if ($extension === 'css') {
				wp_enqueue_style($filename, $pathrel, [], filemtime($path));
			}
		}
	}

	$jspath = WP_PLUGIN_DIR . '/'.DMIND_SEARCHFIELD_PLUGIN_DIR.'/assets/js/';
	$filesjs = preg_grep('/^([^.])/', scandir($jspath));
	foreach ($filesjs as $filename) {
		$path = $jspath . $filename;
		$pathrel = plugins_url(DMIND_SEARCHFIELD_PLUGIN_DIR) . '/assets/js/' . $filename;
		if (is_file($path)) {
			$extension = pathinfo($path, PATHINFO_EXTENSION);
			if ($extension === 'js') {
				wp_enqueue_script($filename, $pathrel, [], filemtime($path), true);
			}
		}
	}
}, 30);

function dmind_search_toggle(): string {
	return '
    <div class="search-toggle d-flex order-3 order-lg-4 ms-3">
        <button class="toggle-search" aria-label="' . __('Suche aus- und einklappen', 'dmind_custom_search') . '">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                <path d="M19 17l-5.15-5.15a7 7 0 1 0-2 2L17 19zM3.5 8A4.5 4.5 0 1 1 8 12.5 4.5 4.5 0 0 1 3.5 8z"/>
            </svg>
        </button>
    </div>
	';
}

function dmind_search_form() {
	$r = '
	 <div id="search-form-container" style="display: none">
	 	 <div class="close-search">&times;</div>
		 <form class="search-container" method="get" action="' . esc_url( home_url( '/' ) ) . '">
	        <label for="searchfield" class="visually-hidden form-label">' . __('Suche nach:', 'dmind_custom_search') . '</label>
	        <input type="search" id="searchfield" class="search-field" name="s" value="' . esc_attr(gget_search_query()) . '" placeholder="' . __('Suche nach...', 'dmind_custom_search') . '">
	        <button aria-label="' . __('Suche starten', 'dmind_custom_search') . '" class="btn btn-outline-secondary" type="submit" id="searchbutton">
	            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
	              <path d="M19 17l-5.15-5.15a7 7 0 1 0-2 2L17 19zM3.5 8A4.5 4.5 0 1 1 8 12.5 4.5 4.5 0 0 1 3.5 8z"/>
	            </svg>
			</button>
	     </form> 
	</div>
	';
	echo $r;
	return;
}

$dmind_searchform_hook = get_option('dmind_searchform_hook', 15);
if ($dmind_searchform_hook) {
	add_action( $dmind_searchform_hook, 'dmind_search_form');
}

$dmind_searchform_hook_icon = get_option('dmind_searchform_hook_icon', 15);
if ($dmind_searchform_hook_icon) {
	add_action( $dmind_searchform_hook_icon, 'dmind_add_search_button');
} else {
	add_filter('wp_nav_menu_items', 'dmind_add_search_button_menu', 9, 2);
}

function dmind_add_search_button() {
	$dmind_searchform_extraclass = get_option('dmind_searchform_extraclass', 15);
	$button = '<div class="dm-search-icon-container '.$dmind_searchform_extraclass.'">'.dmind_search_toggle().'</div>';
	echo $button;
}

function dmind_add_search_button_menu($items, $args) {
	$dmind_searchform_extraclass = get_option('dmind_searchform_extraclass', 15);
	//prohibit the search field by passing an array of allowed menu locations
	$menu_locations = apply_filters('dmind_searchfield_menu_location', []);
	if (!empty($menu_locations) && !in_array($args->theme_location, $menu_locations)) {
		return $items;
	}
	$prepend = apply_filters('dmind_searchfield_prepend', false);
	$button = '<li class="dmind-menu-search-li '.$dmind_searchform_extraclass.'">'.dmind_search_toggle().'</li>';
	if ($prepend) {
		$items = $button . $items;
	} else {
		$items .= $button;
	}
	return $items;
}