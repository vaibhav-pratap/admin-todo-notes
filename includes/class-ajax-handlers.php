<?php
defined('ABSPATH') || exit;

class AdminTodo_AjaxHandler {

    public static function init(): void {
        add_action('wp_ajax_admin_todo_add_task', [self::class, 'add_task']);
        add_action('wp_ajax_admin_todo_delete_task', [self::class, 'delete_task']);
        add_action('wp_ajax_admin_todo_update_task', [self::class, 'update_task']);
        add_action('wp_ajax_admin_todo_reorder_tasks', [self::class, 'reorder_tasks']);
    }

    private static function verify(): void {
        check_ajax_referer('admin_todo_nonce', 'nonce');
    }

    public static function add_task(): void {
        self::verify();

        $content = $_POST['content'] ?? '';
        $status = $_POST['status'] ?? 'pending';
        $due_date = $_POST['due_date'] ?? '';

        if (empty($content)) {
            wp_send_json_error(__('Task content is required.', ADTODO_TEXT_DOMAIN));
        }

        $task_id = AdminTodo_TaskManager::add_task($content, $status, $due_date);
        wp_send_json_success(['id' => $task_id]);
    }

    public static function delete_task(): void {
        self::verify();

        $task_id = $_POST['task_id'] ?? '';
        if (empty($task_id)) {
            wp_send_json_error(__('Task ID missing.', ADTODO_TEXT_DOMAIN));
        }

        AdminTodo_TaskManager::delete_task($task_id);
        wp_send_json_success();
    }

    public static function update_task(): void {
        self::verify();

        $task_id = $_POST['task_id'] ?? '';
        $updates = [];

        if (!empty($_POST['content'])) {
            $updates['content'] = $_POST['content'];
        }

        if (!empty($_POST['status'])) {
            $updates['status'] = $_POST['status'];
        }

        if (!empty($_POST['due_date'])) {
            $updates['due_date'] = $_POST['due_date'];
        }

        if (empty($task_id) || empty($updates)) {
            wp_send_json_error(__('Invalid update request.', ADTODO_TEXT_DOMAIN));
        }

        AdminTodo_TaskManager::update_task($task_id, $updates);
        wp_send_json_success();
    }

    public static function reorder_tasks(): void {
        self::verify();

        $order = $_POST['order'] ?? [];
        if (!is_array($order)) {
            wp_send_json_error(__('Invalid order format.', ADTODO_TEXT_DOMAIN));
        }

        AdminTodo_TaskManager::reorder_tasks($order);
        wp_send_json_success();
    }
}

// Hook into WordPress
add_action('init', ['AdminTodo_AjaxHandler', 'init']);
