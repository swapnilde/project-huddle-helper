<?php

/**
 * The plugin bootstrap file
 *
 * @link              https://www.brainstormforce.com
 * @since             1.0.0
 * @package           Project_Huddle_Helper
 *
 * @wordpress-plugin
 * Plugin Name:       ProjectHuddle Helper
 * Plugin URI:        https://projecthuddle.com
 * Description:       This plugin is used to help with the ProjectHuddle external integrations.
 * Version:           1.0.0
 * Author:            Brainstorm Force
 * Author URI:        https://www.brainstormforce.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       project-huddle-helper
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'PROJECT_HUDDLE_HELPER_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 */
function activate_project_huddle_helper() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-project-huddle-helper-activator.php';
	Project_Huddle_Helper_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_project_huddle_helper() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-project-huddle-helper-deactivator.php';
	Project_Huddle_Helper_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_project_huddle_helper' );
register_deactivation_hook( __FILE__, 'deactivate_project_huddle_helper' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks, etc
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-project-huddle-helper.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_project_huddle_helper() {

	$plugin = new Project_Huddle_Helper();
	$plugin->run();

}
run_project_huddle_helper();
