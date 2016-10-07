<?php
/*
 * Plugin Name: Alt Meta Box
 * Version: 1.0
 * Plugin URI: http://www.alt-design.net/
 * Description: Super simple meta box demonstration
 * Author: John Wilson
 * Author URI: http://www.johnwilsononline.com/
 * Text Domain: alt
 * Domain Path: /languages/
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main Alt_Meta_Box class.
 */

class Alt_Meta_Box {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->includes();

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	/**
	 * Enqueue admin styles & scripts
	 */
	public function admin_scripts( $screen ){
		wp_register_style( 'alt-admin-style', plugin_dir_url( __FILE__ ) . 'assets/css/admin-styles.css', array(), '0.1' );
		wp_enqueue_style( 'alt-admin-style' );

		wp_register_script( 'alt-admin-scripts', plugin_dir_url( __FILE__ ) . 'assets/js/admin-scripts.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-draggable', 'jquery-ui-sortable' ), '0.1', true );
		wp_enqueue_script( 'alt-admin-scripts' );
	}

	/**
	 * Include required core files.
	 */
	public function includes() {
		include_once( 'includes/custom-metabox.php' );
	}

}

return new Alt_Meta_Box();