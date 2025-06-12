<?php
/**
 * Plugin Name: Admin Dashboard To-Do Notes
 * Description: A modern task manager for the WordPress admin dashboard — includes tags, due dates, statuses, drag-and-drop sorting, and per-user tasks.
 * Version:     1.0.0
 * Author:      Vaibhav Singh
 * Author URI:  https://github.com/vaibhav-pratap
 * Text Domain: admin-todo
 * Domain Path: /languages
 */

defined('ABSPATH') || exit;

// Plugin Constants
const ADTODO_VERSION       = '1.0.0';
const ADTODO_PLUGIN_DIR    = __DIR__ . '/';
const ADTODO_PLUGIN_URL    = plugin_dir_url(__FILE__);
const ADTODO_TEXT_DOMAIN   = 'admin-todo';

// Load required files
require_once ADTODO_PLUGIN_DIR . 'includes/class-task-manager.php';
require_once ADTODO_PLUGIN_DIR . 'includes/class-ajax-handlers.php';
require_once ADTODO_PLUGIN_DIR . 'includes/class-admin-ui.php';

final class AdminTodo {

    /**
     * Initialize the plugin
     */
    public static function init(): void {
        self::load_textdomain();
        self::init_hooks();
    }

    /**
     * Load translation files
     */
    private static function load_textdomain(): void {
        load_plugin_textdomain(ADTODO_TEXT_DOMAIN, false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    /**
     * Register plugin hooks
     */
    private static function init_hooks(): void {
        add_action('plugins_loaded', [self::class, 'load']);
        add_action('admin_enqueue_scripts', [self::class, 'enqueue_assets']);
        add_action('wp_dashboard_setup', ['AdminTodo_UI', 'register_dashboard_widget']);
        AdminTodo_AjaxHandlers::init();
    }

    /**
     * Ensure plugin files load after other plugins
     */
    public static function load(): void {
        // Placeholder if we need to hook something on plugins_loaded
    }

    /**
     * Enqueue CSS/JS assets on the dashboard only
     */
    public static function enqueue_assets(): void {
        $screen = get_current_screen();

        if (!is_admin() || $screen->base !== 'dashboard') {
            return;
        }

        // Styles
        wp_enqueue_style(
            'admin-todo-style',
            ADTODO_PLUGIN_URL . 'assets/css/style.css',
            [],
            ADTODO_VERSION
        );

        // Scripts
        wp_enqueue_script(
            'admin-todo-script',
            ADTODO_PLUGIN_URL . 'assets/js/todo.js',
            ['jquery', 'jquery-ui-sortable'],
            ADTODO_VERSION,
            true
        );

        // Localized data for JS
        wp_localize_script('admin-todo-script', 'AdminTodoVars', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('admin_todo_nonce'),
            'i18n'     => [
                'confirm_delete' => __('Are you sure you want to delete this task?', ADTODO_TEXT_DOMAIN),
            ],
        ]);
    }
}

// Bootstrap the plugin
AdminTodo::init();
