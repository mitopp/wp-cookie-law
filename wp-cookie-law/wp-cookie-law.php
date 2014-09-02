<?php
/**
 * Plugin Name: WP Cookie Law
 * Description: A simple cookie law implementation for WordPress.
 * Version:     1.0.1
 * Author:      Michael Topp
 * Author URI:  http://codeschubser.de
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path: languages
 * Text Domain: wp-cookie-law
 */

/**
 * @package     Plugin | WP Cookie Law
 * @subpackage  WordPress Plugins
 * @author      Michael Topp <blog@codeschobser.de>
 * @version     $Id: wp-cookie-law.php,v 0.0.1 02.09.2014 09:57:50 mitopp Exp $;
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) )
{
	die;
}

if ( ! class_exists( 'WP_Cookie_Law' ) )
{
    class WP_Cookie_Law
    {
        /**
         * Plugin version, used for cache-busting of style and script file references.
         * @since   0.0.1
         * @var     string
         */
        const VERSION = '1.0.1';
        /**
         * Instance of this class.
         * @static
         * @access  protected
         * @since   1.0.0
         * @var     object
         */
        protected static $instance = null;
        /**
         * Unique identifier for your plugin.
         * The variable name is used as the text domain when internationalizing strings
         * of text. Its value should match the Text Domain file header in the main
         * plugin file.
         * @access  protected
         * @since   1.0.0
         * @var     string
         */
        protected $plugin_slug = 'wp-cookie-law';

        /**
         * Initialize the plugin by setting localization and loading public scripts
         * and styles.
         * @access  private
         * @since   1.0.0
         * @return  void
         */
        private function __construct()
        {
            // Load plugin text domain
            add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

            // Load public-facing style sheet and JavaScript.
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        }

        /**
         * Get a singleton instance of this class.
         * @static
         * @access  public
         * @since   1.0.0
         * @return  object
         */
        public static function get_instance()
        {
            if ( null === self::$instance )
            {
                self::$instance = new self;
            }

            return self::$instance;
        }
        /**
         * Load the plugin text domain for translation.
         * @access  public
         * @since   1.0.0
         * @return  void
         */
        public function load_plugin_textdomain()
        {
            #wp_die( dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
            load_plugin_textdomain(
                $this->plugin_slug,
                false,
                dirname( plugin_basename( __FILE__ ) ) . '/languages/'
            );
        }
        /**
         * Register and enqueue public-facing style sheet.
         * @access  public
         * @since   1.0.0
         * @return  void
         */
        public function enqueue_styles()
        {
            wp_enqueue_style(
                $this->plugin_slug . '-styles',
                plugins_url( 'assets/css/wp-cookie-law.min.css', __FILE__ ),
                array(),
                self::VERSION
            );
        }
        /**
         * Register and enqueues public-facing JavaScript files in footer.
         * @access  public
         * @since   1.0.0
         * @return  void
         */
        public function enqueue_scripts()
        {
            wp_enqueue_script(
                'jquery-cookie',
                plugins_url( 'assets/js/jquery.cookie.min.js', __FILE__ ),
                array( 'jquery' ),
                self::VERSION,
                true
            );
            wp_register_script(
                $this->plugin_slug . '-script',
                plugins_url( 'assets/js/wp-cookie-law.min.js', __FILE__ ),
                array( 'jquery', 'jquery-cookie' ),
                self::VERSION,
                true
            );
            wp_localize_script(
                $this->plugin_slug . '-script',
                self::uglify( $this->plugin_slug ) . '_text',
                array(
                    'message'   => __( 'By using our website, you agree to the use of our cookies.', $this->plugin_slug ),
                    'button'    => __( 'OK', $this->plugin_slug )
                )
            );
            wp_enqueue_script( $this->plugin_slug . '-script' );
        }

        /**
         * Convert string string.
         * @static
         * @access  private
         * @since   1.0.1
         * @param   string      $string
         * @return  string
         */
        private static function uglify( $string )
        {
            return str_replace( '-', '_', $string );
        }
    }
}

add_action( 'plugins_loaded', array( 'WP_Cookie_Law', 'get_instance' ) );