<?php
/**
 * Plugin Name: Admin Dashboard To-Do Notes
 * Description: Adds a stylish to-do list widget to the WordPress admin dashboard.
 * Version: 1.0.0
 * Author: Vaibhav Singh
 * Author URI: https://github.com/vaibhav-pratap
 * License: GPL2+
 * Requires PHP: 8.0
 * Text Domain: admin-todo-notes
 */

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'includes/class-admin-todo-notes.php';

add_action('plugins_loaded', ['AdminTodoNotes\\AdminTodoNotes', 'init']);
