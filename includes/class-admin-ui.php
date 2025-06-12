<?php
defined('ABSPATH') || exit;

class AdminTodo_UI {

    /**
     * Registers the Dashboard Widget.
     */
    public static function register_dashboard_widget(): void {
        wp_add_dashboard_widget(
            'admin_dashboard_todo_notes_widget',
            __('My To-Do Notes', ADTODO_TEXT_DOMAIN),
            [self::class, 'render_widget']
        );
    }

    /**
     * Renders the dashboard widget HTML via the view template.
     */
    public static function render_widget(): void {
        $tasks = AdminTodo_TaskManager::get_tasks();
        include ADTODO_PLUGIN_DIR . 'templates/dashboard-widget.php';
    }
}
