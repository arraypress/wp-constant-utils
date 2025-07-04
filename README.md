# WordPress Constant Utils

A simple WordPress library for safely managing constants in plugins and themes. No more scattered `define()` calls or worrying about overwriting existing constants.

## Installation

```bash
composer require arraypress/wp-constant-utils
```

## Quick Start

```php
// Global functions (no imports needed)
wp_define_constant( 'API_KEY', 'secret-123' );
wp_setup_plugin_constants( 'MYPLUGIN', __FILE__, '1.0.0' );
$api_key = wp_get_constant( 'API_KEY', 'default-key' );

// Or use the class directly
use ArrayPress\ConstantUtils\Constant;

Constant::define( 'API_KEY', 'secret-123' );
Constant::setup_plugin( 'MYPLUGIN', __FILE__, '1.0.0' );
$api_key = Constant::get( 'API_KEY', 'default-key' );
```

## Why Use This?

**Before:**
```php
// Scattered throughout your plugin
define( 'MYPLUGIN_VERSION', '1.0.0' );
define( 'MYPLUGIN_PLUGIN_FILE', __FILE__ );
define( 'MYPLUGIN_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MYPLUGIN_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MYPLUGIN_DEBUG', WP_DEBUG );

// Oops, accidentally overwrite existing constants
define( 'API_KEY', 'new-value' ); // Might overwrite existing value
```

**After:**
```php
// Clean, safe, and organized (choose your style)

// Global functions (recommended)
wp_setup_plugin_constants( 'MYPLUGIN', __FILE__, '1.0.0' );
wp_define_constant( 'API_KEY', 'secret-key' ); // Only defines if not already set

// Or class methods
Constant::setup_plugin( 'MYPLUGIN', __FILE__, '1.0.0' );
Constant::define( 'API_KEY', 'secret-key' );
```

## Essential Methods

### Plugin Setup

**Set up all standard plugin constants:**
```php
// Global function (WordPress-style)
wp_setup_plugin_constants( 'MYPLUGIN', __FILE__, '1.2.3' );

// Or class method
Constant::setup_plugin( 'MYPLUGIN', __FILE__, '1.2.3' );

// Creates:
// MYPLUGIN_PLUGIN_VERSION = '1.2.3'
// MYPLUGIN_PLUGIN_FILE = '/path/to/plugin.php'
// MYPLUGIN_PLUGIN_DIR = '/path/to/plugin/'
// MYPLUGIN_PLUGIN_URL = 'https://site.com/wp-content/plugins/myplugin/'
// MYPLUGIN_PLUGIN_BASE = 'myplugin/myplugin.php'
```

**Add debug constants:**
```php
Constant::setup_debug( 'MYPLUGIN' );

// Creates based on WordPress settings:
// MYPLUGIN_DEBUG = true/false
// MYPLUGIN_DEBUG_LOG = true/false
// MYPLUGIN_SCRIPT_DEBUG = true/false
```

### Safe Definition

**Define constants safely:**
```php
// Global function
wp_define_constant( 'API_KEY', 'secret-123' ); // Returns true if defined

// Or class method
Constant::define( 'API_KEY', 'secret-123' );

// Define multiple at once
Constant::define_multiple( [
	'API_URL'     => 'https://api.example.com',
	'API_TIMEOUT' => 30,
	'CACHE_TIME'  => 3600
] );
```

### Smart Retrieval

**Get constants with fallbacks:**
```php
// Global function
$api_key = wp_get_constant( 'API_KEY', 'fallback-key' );

// Or class method
$api_key = Constant::get( 'API_KEY', 'fallback-key' );

// Get multiple constants
$config = Constant::get_multiple( [ 'API_KEY', 'API_URL' ], 'default' );
// Returns: ['API_KEY' => 'value1', 'API_URL' => 'value2']

// Get all plugin constants
$all_constants = Constant::get_all_with_prefix( 'MYPLUGIN_' );
```

### Environment Configuration

**Different settings per environment:**
```php
$config = [
	'development' => [
		'API_URL'    => 'https://dev-api.example.com',
		'DEBUG_MODE' => true
	],
	'production'  => [
		'API_URL'    => 'https://api.example.com',
		'DEBUG_MODE' => false
	]
];

Constant::setup_environment( 'MYPLUGIN', $config );
// Automatically uses the right config based on wp_get_environment_type()
```

### Validation

**Check if constants exist:**
```php
// Single constant
if ( Constant::is_defined( 'API_KEY' ) ) {
	// Do something
}

// Check multiple required constants
$required = [ 'DB_NAME', 'DB_USER', 'DB_PASSWORD' ];
if ( Constant::all_defined( $required ) ) {
	// All database constants are set
}

// Check if any caching is configured
$cache_options = [ 'REDIS_HOST', 'MEMCACHED_HOST' ];
if ( Constant::any_defined( $cache_options ) ) {
	// Some form of caching is available
}
```

## Real-World Example

```php
<?php
/**
 * Plugin Name: My Awesome Plugin
 * Version: 1.2.3
 */

// Global functions (recommended for simplicity)
wp_setup_plugin_constants( 'MYAWESOMEPLUGIN', __FILE__, '1.2.3' );

// Or using the class (for advanced usage)
use ArrayPress\ConstantUtils\Constant;

Constant::setup_debug( 'MYAWESOMEPLUGIN' );

// Environment-specific settings
$env_config = [
	'development' => [
		'API_URL'       => 'https://dev-api.example.com',
		'CACHE_ENABLED' => false
	],
	'production'  => [
		'API_URL'       => 'https://api.example.com',
		'CACHE_ENABLED' => true
	]
];
Constant::setup_environment( 'MYAWESOMEPLUGIN', $env_config );

// Additional plugin settings
Constant::setup_additional( 'MYAWESOMEPLUGIN', [
	'CACHE_TIMEOUT' => 3600,
	'MAX_RETRIES'   => 3
] );

// Validate setup
if ( ! Constant::all_defined( [ 'MYAWESOMEPLUGIN_PLUGIN_VERSION', 'MYAWESOMEPLUGIN_API_URL' ] ) ) {
	wp_die( 'Plugin setup failed!' );
}

// Use throughout your plugin (both styles work)
$api_url    = wp_get_constant( 'MYAWESOMEPLUGIN_API_URL' );
$is_debug   = Constant::get( 'MYAWESOMEPLUGIN_DEBUG', false );
$cache_time = wp_get_constant( 'MYAWESOMEPLUGIN_CACHE_TIMEOUT', 1800 );
```

## Configuration File Pattern

**Create a config file for complex setups:**

```php
// config/constants.php
return [
	'development' => [
		'API_URL'       => 'https://dev-api.example.com',
		'DEBUG_QUERIES' => true,
		'CACHE_ENABLED' => false,
		'LOG_LEVEL'     => 'debug'
	],
	'staging'     => [
		'API_URL'       => 'https://staging-api.example.com',
		'DEBUG_QUERIES' => false,
		'CACHE_ENABLED' => true,
		'LOG_LEVEL'     => 'info'
	],
	'production'  => [
		'API_URL'       => 'https://api.example.com',
		'DEBUG_QUERIES' => false,
		'CACHE_ENABLED' => true,
		'LOG_LEVEL'     => 'error'
	]
];

// In your plugin
$config = include plugin_dir_path( __FILE__ ) . 'config/constants.php';
Constant::setup_environment( 'MYPLUGIN', $config );
```

## Global Functions

The library provides convenient WordPress-style functions:

| Function | Description |
|----------|-------------|
| `wp_define_constant($name, $value)` | Safely define a constant |
| `wp_get_constant($name, $default)` | Get constant with fallback |
| `wp_setup_plugin_constants($prefix, $file, $version)` | Set up plugin constants |

## All Class Methods

| Method | Description |
|--------|-------------|
| `define($name, $value)` | Safely define a constant |
| `define_multiple($constants)` | Define multiple constants |
| `get($name, $default)` | Get constant with fallback |
| `get_multiple($names, $default)` | Get multiple constants |
| `get_all_with_prefix($prefix)` | Get all constants with prefix |
| `setup_plugin($prefix, $file, $version)` | Set up plugin constants |
| `setup_environment($prefix, $config)` | Environment-specific constants |
| `setup_debug($prefix)` | WordPress debug constants |
| `setup_additional($prefix, $constants)` | Additional prefixed constants |
| `is_defined($name)` | Check if constant exists |
| `is_equal($name, $value)` | Check constant value |
| `all_defined($names)` | Check if all constants exist |
| `any_defined($names)` | Check if any constants exist |
| `export($prefix)` | Export constants for debugging |

## Requirements

- PHP 7.4+
- WordPress 5.0+

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is licensed under the GPL-2.0-or-later License.

## Support

- [Documentation](https://github.com/arraypress/wp-constant-utils)
- [Issue Tracker](https://github.com/arraypress/wp-constant-utils/issues)