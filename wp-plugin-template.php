<?php

/**
 * Plugin Name: WP Plugin Template
 * Version: 1.0.0
 * Plugin URI:
 * Description: This is a simpel WordPress plugin template.
 * Author: Bruno Ferreira
 * Author URI: https://github.com/srbrunoferreira
 * Requires at least: 5.8
 * Tested up to: 5.8
 *
 * @package WordPress
 * @author Bruno Ferreira
 * @since 1.0.0
 */

defined('ABSPATH') or exit;

require_once 'includes/WPPTAdminPage.php';
require_once 'includes/WPPTFrameworkHandler.php';
require_once 'includes/helpers.php';

/**
 * Main class of the plugin.
 *
 * @author Bruno Ferreira <srbrunoferreira@outlook.com>
 * @version 1.0.0
 */
final class WpPluginTemplate
{
    /**
     * Stores a WPPTInterfaceHandler object.
     *
     * @var object
     */
    private $interface;

    /**
     * Stores a WPPTFrameworkHandler object.
     *
     * @var object
     */
    private $frameworkHandler;

    /**
     * Stores the plugin name that will be used accross the plugin.
     *
     * @var string
     */
    private $pluginName;

    /**
     * Stores the plugin slug.
     *
     * @var string
     */
    private $pluginSlug;

    /**
     * Stores the path to the main file of the plugin.
     *
     * @var string
     */
    private $pluginMainFile;

    /**
     * Stores the root path to the plugin folder.
     *
     * @var string
     */
    private $pluginSystemPath;

    /**
     * Stores the root URL path to the plugin folder.
     *
     * @var string
     */
    private $pluginUrlPath;

    public function __construct($pluginName, $pluginSlug)
    {
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'uninstall']);
        register_uninstall_hook(__FILE__, [$this, 'uninstall']);

        $this->pluginName = $pluginName;
        $this->pluginSlug = $pluginSlug;

        $this->pluginMainFile = __FILE__;
        $this->pluginSystemPath = plugin_dir_path(__FILE__);
        $this->pluginUrlPath = plugin_dir_url(__FILE__);

        // Runs the admin setting page.
        $this->interface = new WPPTAdminPage($this->pluginName, $this->pluginSlug, $this->pluginMainFile);
        // You can remove this line unless you want to create something like a shortcode.
        $this->frameworkHandler = new WPPTFrameworkHandler($this->pluginSystemPath, $this->pluginUrlPath);

        // add_action('wp_loaded', [$this->frameworkHandler, 'registerFrameworkFiles']);
        // add_action( 'wp_enqueue_scripts', 'crunchify_print_scripts_styles');
        add_shortcode('wpquasar', [$this->frameworkHandler, 'enqueueFrameworkFiles']);
    }

    /**
     * Executed when the plugin is uninstalled.
     *
     * @return void
     */
    public function uninstall()
    {
    }

    /**
     * Executed when the plugin is activated.
     *
     * @return void
     */
    public function activate()
    {
    }

    /**
     * Executed when the plugin is deactivated.
     *
     * @return void
     */
    public function deactiviate()
    {
    }
}


// Launch the plugin.
new WpPluginTemplate('WPPTemplate', 'wpptemplate');
