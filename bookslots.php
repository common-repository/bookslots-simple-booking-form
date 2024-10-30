<?php
/**
 * The plugin bootstrap file
 *
 * @link              https://github.com/davidtowoju
 * @since             0.1.0
 * @package           Bookslots
 *
 * @wordpress-plugin
 * Plugin Name:       BookSlots - Simple Booking Form
 * Plugin URI:        https://pluginette.com/product/bookslots/
 * Description:       A simple yet powerful stand-alone booking form for bookings, appointments and reservations.
 * Version:           0.1.6
 * Author:            David Towoju
 * Author URI:        https://github.com/davidtowoju
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bookslots
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 0.1.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BOOKSLOTS_VERSION', '0.1.6' );
define( 'BOOKSLOTS_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'BOOKSLOTS_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'BOOKSLOTS_BASE', plugin_basename( __FILE__ ) );
define( 'BOOKSLOTS_VIEWS', BOOKSLOTS_DIR_PATH . 'resources/views/' );
define( 'BOOKSLOTS_PARTIALS', BOOKSLOTS_DIR_PATH . 'views/partials/' );
define( 'BOOKSLOTS_ASSETS', BOOKSLOTS_DIR_URL . 'assets/' );
define( 'BOOKSLOTS_RESOURCES', BOOKSLOTS_DIR_URL . 'resources/' );


if ( ! file_exists( BOOKSLOTS_DIR_PATH . '/vendor/autoload.php' ) ) {
	return;
}

require BOOKSLOTS_DIR_PATH . '/vendor/autoload.php';
require BOOKSLOTS_DIR_PATH . '/app/Includes/helper.php';

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
$bookslots = new Bookslots\Core();


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bookslots-activator.php
 */
register_activation_hook( __FILE__, array( $bookslots, 'activate_bookslots' ) );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bookslots-deactivator.php
 */
register_deactivation_hook( __FILE__, array( $bookslots, 'deactivate_bookslots' ) );


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1.0
 */
$bookslots->register();