<?php

namespace Braine;

use Elementor\Core\Base\Document;
use ElementorPro\Modules\QueryControl\Module as QueryControlModule;
use ElementorPro\Plugin;

/**
 * Plugin Name:       Template para Posts Não Encontrados
 * Description:       Create a section in Elementor "Posts Widget" and "Loop Grid Widget" for set template when not found posts.
 * Version:           1.0.0
 * Requires PHP:      7.4
 * Author:            Saulo Braine
 * Author URI:        https://braine.dev
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       braine-template-for-not-found-posts
 * Domain Path:       /languages
 * */

/* ACTIVATION */
register_activation_hook(__FILE__, function () {
    /* UPDATE PERMALINKS */
    flush_rewrite_rules();
});

/* DEACTIVATION */
register_deactivation_hook(__FILE__, function () {
    /* UPDATE PERMALINKS */
    flush_rewrite_rules();
});

final class Braine_TemplateForNotFoundPosts {
    /**
     * Plugin Version
     *
     * @since 1.0.0
     *
     * @var string The plugin version.
     */
    public const VERSION = '1.0.0';

    /**
     * Minimum Elementor Version
     *
     * @since 1.0.0
     *
     * @var string Minimum Elementor version required to run the plugin.
     */
    public const MINIMUM_ELEMENTOR_VERSION = '3.2.1';

    /**
     * Minimum PHP Version
     *
     * @since 1.0.0
     *
     * @var string Minimum PHP version required to run the plugin.
     */
    public const MINIMUM_PHP_VERSION = '7.3';

    /**
     * Template ID
     *
     * @since 1.0.0
     *
     * @var string ID of Template for Not Found Posts.
     */
    private $not_found_template_id;

    /**
     * Instance
     *
     * @since 1.0.0
     *
     * @access private
     * @static
     *
     * @var Elementor_Test_Extension The single instance of the class.
     */
    private static $_instance = null;

    /**
     * Instance
     *
     * Ensures only one instance of the class is loaded or can be loaded.
     *
     * @since 1.0.0
     *
     * @access public
     * @static
     *
     * @return Elementor_Test_Extension An instance of the class.
     */
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function __construct() {
        add_action('plugins_loaded', [$this, 'on_plugins_loaded']);
    }

    /**
     * Load Textdomain
     *
     * Load plugin localization files.
     *
     * Fired by `init` action hook.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function i18n() {
        load_plugin_textdomain('braine-template-for-not-found-posts');
    }

    /**
     * On Plugins Loaded
     *
     * Checks if Elementor has loaded, and performs some compatibility checks.
     * If All checks pass, inits the plugin.
     *
     * Fired by `plugins_loaded` action hook.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function on_plugins_loaded() {
        if ($this->is_compatible()) {
            add_action('elementor/init', [$this, 'init']);
        }
    }

    /**
     * Compatibility Checks
     *
     * Checks if the installed version of Elementor meets the plugin's minimum requirement.
     * Checks if the installed PHP version meets the plugin's minimum requirement.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function is_compatible() {
        // Check if Elementor installed and activated
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_main_plugin']);
            return false;
        }

        // Check for required Elementor version
        if (!version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_elementor_version']);
            return false;
        }

        // Check for required PHP version
        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_php_version']);
            return false;
        }

        return true;
    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have Elementor installed or activated.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function admin_notice_missing_main_plugin() {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor */
            esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'braine-template-for-not-found-posts'),
            '<strong>' . esc_html__('Elementor - Not Found Template', 'braine-template-for-not-found-posts') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'braine-template-for-not-found-posts') . '</strong>'
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have a minimum required Elementor version.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function admin_notice_minimum_elementor_version() {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'braine-template-for-not-found-posts'),
            '<strong>' . esc_html__('Elementor Test Extension', 'braine-template-for-not-found-posts') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'braine-template-for-not-found-posts') . '</strong>',
            self::MINIMUM_ELEMENTOR_VERSION
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have a minimum required PHP version.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function admin_notice_minimum_php_version() {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'braine-template-for-not-found-posts'),
            '<strong>' . esc_html__('Elementor Test Extension', 'braine-template-for-not-found-posts') . '</strong>',
            '<strong>' . esc_html__('PHP', 'braine-template-for-not-found-posts') . '</strong>',
            self::MINIMUM_PHP_VERSION
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    /**
     * Update Posts Widget
     *
     * Add control of existing Elementor Widget Posts.
     *
     * Fired by `init` action hook.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function update_posts_widget($element, $args) {
        $element->start_controls_section(
            'not_fount_template_section',
            [
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                'label' => __('Not Found Template', 'braine-template-for-not-found-posts'),
            ]
        );

        $document_types = Plugin::elementor()->documents->get_document_types([
            'show_in_library' => true,
        ]);

        $element->add_control(
            'not_found_template_id',
            [
                'label' => __('Choose Template', 'braine-template-for-not-found-posts'),
                'type' => QueryControlModule::QUERY_CONTROL_ID,
                'label_block' => true,
                'autocomplete' => [
                    'object' => QueryControlModule::QUERY_OBJECT_LIBRARY_TEMPLATE,
                    'query' => [
                        'meta_query' => [
                            [
                                'key' => Document::TYPE_META_KEY,
                                'value' => array_keys($document_types),
                                'compare' => 'IN',
                            ],
                        ],
                    ],
                ],
            ]
        );

        $element->end_controls_section();
    }

    /**
     * Initialize the plugin
     *
     * Load the plugin only after Elementor (and other plugins) are loaded.
     * Load the actions required to run the plugin.
     *
     * Fired by `plugins_loaded` action hook.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function init() {
        $this->i18n();

        add_action('elementor/element/posts/section_pagination/after_section_end', [$this, 'update_posts_widget'], 10, 2);

        add_action('elementor/element/loop-grid/section_pagination/after_section_end', [$this, 'update_posts_widget'], 10, 2);

        add_action('elementor/frontend/widget/before_render', function (\Elementor\Element_Base $element) {
            if (!$element->get_settings('not_found_template_id')) :
                return;
            endif;

            $this->not_found_template_id = $element->get_settings('not_found_template_id');

            add_action('elementor/query/query_results', function ($query) {
                $total = $query->found_posts;
                if ($total == 0) {
                    echo Plugin::elementor()->frontend->get_builder_content_for_display($this->not_found_template_id);
                }
            });
        });
    }
}

Braine_TemplateForNotFoundPosts::instance();
