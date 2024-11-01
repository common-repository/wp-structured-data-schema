<?php
/**
 * Plugin Name:     Schema Plugin For Divi, Gutenberg & Shortcodes
 * Description:     Quickly integrate JSON-LD schema and improve your website's search engine ranking.
 * Author:          wpt00ls
 * Author URI:      https://wptools.app
 * Text Domain:     wp-structured-data-schema
 * Domain Path:     /languages
 * Version:         4.3.0
 *
  * @package         Wp_Structured_Data_Schema_For_Divi
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/freemius.php';

$loader = \WP_Tools\Schema\Loader::get_instance();

$loader['plugin_name']    = 'Schema Plugin For Divi, Gutenberg & Shortcodes';
$loader['plugin_version'] = '4.3.0';
$loader['plugin_dir']     = __DIR__;
$loader['plugin_url']     = plugins_url( '/' . basename( __DIR__ ) );
$loader['plugin_file']    = __FILE__;
$loader['plugin_slug']    = 'wp-structured-data-schema';

$loader->run();
