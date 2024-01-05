<?php

if (! defined( 'ABSPATH' )) {
    exit;
}

// Define dist directory, base uri, and path
define('DIST_DIR', 'assets/dist');
define('DIST_URI', get_template_directory_uri() . '/' . DIST_DIR);
define('DIST_PATH', get_template_directory() . '/' . DIST_DIR);

// default server address, port, and entry point can be customized in vite.config.js
define('VITE_SERVER', 'http://localhost:5173');
define('VITE__BUILD', file_exists(DIST_PATH . '/.vite/manifest.json'));

// add assets compiled by vite
function add_vite_assets() {
	// default js uri
	$main_js = VITE_SERVER . '/assets/src/js/main.js';

    if (VITE__BUILD) {
	    $manifest = json_decode(file_get_contents(DIST_PATH . '/.vite/manifest.json'), true);

		// enqueue main css
		wp_enqueue_style( 'main', DIST_URI . '/' . $manifest['assets/src/scss/main.scss']['file'],  null, null,  );

		// redefine if build
	    $main_js = DIST_URI . '/' . $manifest['assets/src/js/main.js']['file'];
    }

	// main JS
	wp_register_script('main', $main_js, null, null, true );
	$vars = array(
		'ajaxUrl' => admin_url('admin-ajax.php'),
	);
	wp_localize_script('main', 'siteVars', $vars);
	wp_enqueue_script('main');
}
add_action('wp_enqueue_scripts', 'add_vite_assets', 100);

function vite_head_module_hook() {
	if(!VITE__BUILD) {
		echo '<script type="module" crossorigin src="'. VITE_SERVER .'/@vite/client"></script>';
		echo '<script type="module" crossorigin src="'. VITE_SERVER . '/assets/src/scss/main.scss"></script>';
	}
}
add_action( 'wp_head', 'vite_head_module_hook' );

function add_module_type_attribute($tag, $handle, $src) {
	// The handles of the enqueued scripts we want to modify
	if ('main' === $handle && !VITE__BUILD) {
		return '<script type="module" src="' . esc_url($src) . '" crossorigin></script>';
	}
	return $tag;
}
add_filter('script_loader_tag', 'add_module_type_attribute', 10, 3);
