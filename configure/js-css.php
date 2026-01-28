<?php

// Define dist directory, base uri, and path
define( 'DIST_DIR', 'assets/dist' );
define( 'DIST_URI', get_template_directory_uri() . '/' . DIST_DIR );
define( 'DIST_PATH', get_template_directory() . '/' . DIST_DIR );

// default server address, port, and entry point can be customized in vite.config.js
define( 'VITE_SERVER', 'http://localhost:5173' );
define( 'VITE_BUILD', file_exists( DIST_PATH . '/.vite/manifest.json' ) );

// add assets bundled by vite
function add_vite_assets() {
	// add your custom js files here
	$js_files = [
		'main' => 'main.js'
	];

	// add your custom scss files here
	$scss_files = [
		'main' => 'main.scss'
	];

	if ( VITE_BUILD ) {
		$manifest = json_decode( file_get_contents( DIST_PATH . '/.vite/manifest.json' ), true );
	}

	foreach ( $js_files as $handle => $file ) {
		$js_uri = VITE_SERVER . '/assets/src/js/' . $file;
		if ( VITE_BUILD ) {
			$js_uri = DIST_URI . '/' . $manifest[ 'assets/src/js/' . $file ]['file'];
		}

		wp_register_script( $handle, $js_uri, null, null, true );
		$vars = array(
//			'ajaxUrl' => admin_url( 'admin-ajax.php' ), // uncomment to use - in your js : siteVars.ajaxUrl
		);
		wp_localize_script( $handle, 'siteVars', $vars );
		wp_enqueue_script( $handle );
	}

	foreach ( $scss_files as $handle => $file ) {
		$css_uri = VITE_SERVER . '/assets/src/scss/' . $file;
		if ( VITE_BUILD ) {
			$css_uri = DIST_URI . '/' . $manifest[ 'assets/src/scss/' . $file ]['file'];
		}

		wp_enqueue_style( $handle, $css_uri, null, null );
	}
}
add_action( 'wp_enqueue_scripts', 'add_vite_assets', 100 );

function vite_client_head_hook() {
	if ( ! VITE_BUILD ) {
		echo '<script type="module" crossorigin src="' . VITE_SERVER . '/@vite/client"></script>';
	}
}
add_action( 'wp_head', 'vite_client_head_hook' );

function add_module_type_attribute( $tag, $handle, $src ) {
	// The handles of the enqueued scripts we want to modify
	if ( 'main' === $handle && ! VITE_BUILD ) {
		return '<script type="module" src="' . esc_url( $src ) . '" crossorigin></script>';
	}

	return $tag;
}
add_filter( 'script_loader_tag', 'add_module_type_attribute', 10, 3 );
add_filter( 'style_loader_tag', 'add_module_type_attribute', 10, 3 );

function cleaning_wordpress() {
    // force all scripts to load in footer
    remove_action('wp_head', 'wp_print_scripts');
    remove_action('wp_head', 'wp_print_head_scripts', 9);
    remove_action('wp_head', 'wp_enqueue_scripts', 1);

	// removing all WP css files enqueued by default
	wp_dequeue_style('wp-block-library');
	wp_dequeue_style('wp-block-library-theme');
	wp_dequeue_style('wc-block-style');
	wp_dequeue_style('global-styles');
	wp_dequeue_style('classic-theme-styles');
}
add_action('wp_enqueue_scripts', 'cleaning_wordpress', 100);
