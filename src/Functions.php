<?php
/**
 * Global Helper Functions for WordPress Constants
 *
 * Provides WordPress-style global functions for common constant management patterns.
 * These functions wrap the ArrayPress Constant utilities for better developer experience.
 *
 * @package ArrayPress\ConstantUtils
 * @since   1.0.0
 * @author  ArrayPress
 * @license GPL-2.0-or-later
 */

use ArrayPress\ConstantUtils\Constant;

if ( ! function_exists( 'wp_define_constant' ) ) {
	/**
	 * Define a constant if it's not already defined.
	 *
	 * This function provides a safe way to define constants without accidentally
	 * overwriting existing ones. Follows WordPress naming conventions.
	 *
	 * @param string $name  The name of the constant.
	 * @param mixed  $value The value of the constant.
	 *
	 * @return bool True if the constant was defined, false if it was already defined.
	 *
	 * @since 1.0.0
	 *
	 * @example
	 * // Safely define API key
	 * wp_define_constant('API_KEY', 'secret-123');
	 *
	 * // Won't overwrite if already defined
	 * wp_define_constant('WP_DEBUG', true); // Returns false if WP_DEBUG exists
	 */
	function wp_define_constant( string $name, $value ): bool {
		return Constant::define( $name, $value );
	}
}

if ( ! function_exists( 'wp_get_constant' ) ) {
	/**
	 * Get the value of a defined constant with optional default.
	 *
	 * Provides a safe way to retrieve constant values with fallback defaults.
	 * More convenient than checking defined() and constant() separately.
	 *
	 * @param string $name    The name of the constant.
	 * @param mixed  $default The default value to return if the constant is not defined.
	 *
	 * @return mixed The value of the constant or the default value.
	 *
	 * @since 1.0.0
	 *
	 * @example
	 * // Get API key with fallback
	 * $api_key = wp_get_constant('API_KEY', 'default-key');
	 *
	 * // Get debug setting
	 * $debug = wp_get_constant('WP_DEBUG', false);
	 */
	function wp_get_constant( string $name, $default = null ) {
		return Constant::get( $name, $default );
	}
}

if ( ! function_exists( 'wp_setup_plugin_constants' ) ) {
	/**
	 * Set up common plugin constants automatically.
	 *
	 * Defines the standard constants that most WordPress plugins need,
	 * including version, file paths, URLs, and plugin basename.
	 *
	 * @param string $prefix  The prefix for the constants (will be converted to uppercase).
	 * @param string $file    The main plugin file path (__FILE__ from main plugin file).
	 * @param string $version The plugin version.
	 *
	 * @return array Array of constants that were successfully defined.
	 *
	 * @since 1.0.0
	 *
	 * @example
	 * // In your main plugin file
	 * wp_setup_plugin_constants('MYPLUGIN', __FILE__, '1.2.3');
	 *
	 * // Creates: MYPLUGIN_PLUGIN_VERSION, MYPLUGIN_PLUGIN_FILE,
	 * // MYPLUGIN_PLUGIN_DIR, MYPLUGIN_PLUGIN_URL, MYPLUGIN_PLUGIN_BASE
	 */
	function wp_setup_plugin_constants( string $prefix, string $file, string $version ): array {
		return Constant::setup_plugin( $prefix, $file, $version );
	}
}