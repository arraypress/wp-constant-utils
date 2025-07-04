<?php
/**
 * Constants Utilities
 *
 * This class provides utility functions for defining, managing, and interacting with constants
 * in a WordPress plugin or theme context. It offers methods for safely defining constants,
 * setting up common plugin constants, retrieving constant values, and performing various
 * constant-related operations to streamline development and improve code organization.
 *
 * @package       ArrayPress\ConstantUtils
 * @copyright     Copyright 2024, ArrayPress Limited
 * @license       GPL-2.0-or-later
 * @version       1.0.0
 * @author        David Sherlock
 */

declare( strict_types=1 );

namespace ArrayPress\ConstantUtils;

class Constant {

	/**
	 * Define a constant if it's not already defined.
	 *
	 * @param string $name  The name of the constant.
	 * @param mixed  $value The value of the constant.
	 *
	 * @return bool True if the constant was defined, false if it was already defined.
	 */
	public static function define( string $name, $value ): bool {
		if ( ! defined( $name ) ) {
			define( $name, $value );

			return true;
		}

		return false;
	}

	/**
	 * Define multiple constants at once.
	 *
	 * @param array $constants An associative array of constant names and values.
	 *
	 * @return array An array of constant names that were successfully defined.
	 */
	public static function define_multiple( array $constants ): array {
		$defined = [];
		foreach ( $constants as $name => $value ) {
			if ( self::define( $name, $value ) ) {
				$defined[] = $name;
			}
		}

		return $defined;
	}

	/**
	 * Get the value of a defined constant.
	 *
	 * @param string $name    The name of the constant.
	 * @param mixed  $default The default value to return if the constant is not defined.
	 *
	 * @return mixed The value of the constant or the default value.
	 */
	public static function get( string $name, $default = null ) {
		return defined( $name ) ? constant( $name ) : $default;
	}

	/**
	 * Get all defined constants with a specific prefix.
	 *
	 * @param string $prefix The prefix to filter constants by.
	 *
	 * @return array An associative array of constant names and their values.
	 */
	public static function get_all_with_prefix( string $prefix ): array {
		$constants      = get_defined_constants( true );
		$user_constants = $constants['user'] ?? [];

		return array_filter( $user_constants, function ( $key ) use ( $prefix ) {
			return strpos( $key, $prefix ) === 0;
		}, ARRAY_FILTER_USE_KEY );
	}

	/**
	 * Get the names of all constants with a specific prefix.
	 *
	 * @param string $prefix The prefix to filter constants by.
	 *
	 * @return array An array of constant names.
	 */
	public static function get_all_names_by_prefix( string $prefix ): array {
		return array_keys( self::get_all_with_prefix( $prefix ) );
	}

	/**
	 * Set up common plugin constants.
	 *
	 * This method defines the standard constants that most WordPress plugins need,
	 * including version, file paths, URLs, and plugin basename.
	 *
	 * @param string $prefix  The prefix for the constants (will be converted to uppercase).
	 * @param string $file    The main plugin file path (__FILE__ from main plugin file).
	 * @param string $version The plugin version.
	 *
	 * @return array Array of constants that were successfully defined.
	 */
	public static function setup_plugin( string $prefix, string $file, string $version ): array {
		$prefix = strtoupper( $prefix );

		return self::define_multiple( [
			"{$prefix}_PLUGIN_VERSION" => $version,
			"{$prefix}_PLUGIN_FILE"    => $file,
			"{$prefix}_PLUGIN_BASE"    => plugin_basename( $file ),
			"{$prefix}_PLUGIN_DIR"     => plugin_dir_path( $file ),
			"{$prefix}_PLUGIN_URL"     => plugin_dir_url( $file ),
		] );
	}

	/**
	 * Set up environment-specific constants.
	 *
	 * Define constants based on the current WordPress environment type.
	 * Useful for different configurations per environment.
	 *
	 * @param string $prefix The prefix for the constants (will be converted to uppercase).
	 * @param array  $config Multi-dimensional array with environment-specific values.
	 *                       Format: ['development' => ['DEBUG' => true], 'production' => ['DEBUG' => false]]
	 *
	 * @return array Array of constants that were successfully defined.
	 */
	public static function setup_environment( string $prefix, array $config ): array {
		$prefix = strtoupper( $prefix );
		$env    = function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production';

		if ( ! isset( $config[ $env ] ) ) {
			return [];
		}

		$constants = [];
		foreach ( $config[ $env ] as $name => $value ) {
			$constants["{$prefix}_{$name}"] = $value;
		}

		return self::define_multiple( $constants );
	}

	/**
	 * Set up debug-related constants.
	 *
	 * Define common debug constants based on WordPress debug settings.
	 *
	 * @param string $prefix The prefix for the constants (will be converted to uppercase).
	 *
	 * @return array Array of constants that were successfully defined.
	 */
	public static function setup_debug( string $prefix ): array {
		$prefix = strtoupper( $prefix );

		return self::define_multiple( [
			"{$prefix}_DEBUG"         => defined( 'WP_DEBUG' ) ? WP_DEBUG : false,
			"{$prefix}_DEBUG_LOG"     => defined( 'WP_DEBUG_LOG' ) ? WP_DEBUG_LOG : false,
			"{$prefix}_DEBUG_DISPLAY" => defined( 'WP_DEBUG_DISPLAY' ) ? WP_DEBUG_DISPLAY : true,
			"{$prefix}_SCRIPT_DEBUG"  => defined( 'SCRIPT_DEBUG' ) ? SCRIPT_DEBUG : false,
		] );
	}

	/**
	 * Set up additional constants with prefix.
	 *
	 * @param string $prefix    The prefix for the constants (will be converted to uppercase).
	 * @param array  $constants An associative array of constant names and values.
	 *
	 * @return array Array of constants that were successfully defined.
	 */
	public static function setup_additional( string $prefix, array $constants ): array {
		$prefix = strtoupper( $prefix );

		$prefixed_constants = [];
		foreach ( $constants as $name => $value ) {
			$prefixed_constants["{$prefix}_{$name}"] = $value;
		}

		return self::define_multiple( $prefixed_constants );
	}

	/**
	 * Check if all given constants are defined.
	 *
	 * @param array $names An array of constant names.
	 *
	 * @return bool True if all constants are defined, false otherwise.
	 */
	public static function all_defined( array $names ): bool {
		foreach ( $names as $name ) {
			if ( ! defined( $name ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check if any of the given constants are defined.
	 *
	 * @param array $names An array of constant names.
	 *
	 * @return bool True if any of the constants are defined, false if none are defined.
	 */
	public static function any_defined( array $names ): bool {
		foreach ( $names as $name ) {
			if ( defined( $name ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if a constant is defined.
	 *
	 * @param string $name The name of the constant.
	 *
	 * @return bool True if the constant is defined, false otherwise.
	 */
	public static function is_defined( string $name ): bool {
		return defined( $name );
	}

	/**
	 * Check if a constant's value is equal to a given value.
	 *
	 * @param string $name  The name of the constant.
	 * @param mixed  $value The value to compare against.
	 *
	 * @return bool True if the constant is defined and its value is equal to the given value, false otherwise.
	 */
	public static function is_equal( string $name, $value ): bool {
		return defined( $name ) && constant( $name ) === $value;
	}

	/**
	 * Get multiple constants with their values.
	 *
	 * @param array $names   An array of constant names.
	 * @param mixed $default Default value for undefined constants.
	 *
	 * @return array Associative array of constant names and their values.
	 */
	public static function get_multiple( array $names, $default = null ): array {
		$result = [];
		foreach ( $names as $name ) {
			$result[ $name ] = self::get( $name, $default );
		}

		return $result;
	}

	/**
	 * Export constants to an array for debugging or logging.
	 *
	 * @param string $prefix Optional prefix to filter constants.
	 *
	 * @return array Array of constant names and values.
	 */
	public static function export( string $prefix = '' ): array {
		if ( empty( $prefix ) ) {
			return get_defined_constants( true )['user'] ?? [];
		}

		return self::get_all_with_prefix( $prefix );
	}

}