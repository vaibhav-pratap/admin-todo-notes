<?php
defined('ABSPATH') || exit;

class AdminTodo_TaskManager {
    const META_KEY = '_admin_dashboard_todo_notes';

    /**
     * Get all tasks for a user.
     */
    public static function get_tasks($user_id = null): array {
        $user_id = $user_id ?: get_current_user_id();
        $tasks = get_user_meta($user_id, self::META_KEY, true);
        $tasks = is_array($tasks) ? $tasks : [];

        foreach ($tasks as &$task) {
            $task['tags'] = self::extract_tags($task['content']);
            $task['overdue'] = !empty($task['due_date']) && strtotime($task['due_date']) < time();
        }

        return $tasks;
    }

    /**
     * Save all tasks for a user.
     */
    private static function save_tasks($user_id, array $tasks): void {
        update_user_meta($user_id, self::META_KEY, array_values($tasks));
    }

    /**
     * Add a new task.
     */
    public static function add_task($content, $status = 'pending', $due_date = ''): string {
        $user_id = get_current_user_id();
        $tasks = self::get_tasks($user_id);

        $task_id = uniqid('task_', true);
        $tasks[] = [
            'id' => $task_id,
            'content' => sanitize_text_field($content),
            'status' => sanitize_text_field($status),
            'due_date' => sanitize_text_field($due_date),
            'created_at' => current_time('mysql'),
        ];

        self::save_tasks($user_id, $tasks);
        return $task_id;
    }

    /**
     * Delete a task.
     */
    public static function delete_task($task_id): void {
        $user_id = get_current_user_id();
        $tasks = self::get_tasks($user_id);

        $filtered = array_filter($tasks, fn($t) => $t['id'] !== $task_id);
        self::save_tasks($user_id, $filtered);
    }

    /**
     * Update a task’s status or content.
     */
    public static function update_task($task_id, $updates = []): void {
        $user_id = get_current_user_id();
        $tasks = self::get_tasks($user_id);

        foreach ($tasks as &$task) {
            if ($task['id'] === $task_id) {
                foreach ($updates as $key => $value) {
                    if (in_array($key, ['content', 'status', 'due_date'])) {
                        $task[$key] = sanitize_text_field($value);
                    }
                }
                break;
            }
        }

        self::save_tasks($user_id, $tasks);
    }

    /**
     * Reorder tasks by passed ID array.
     */
    public static function reorder_tasks(array $ordered_ids): void {
        $user_id = get_current_user_id();
        $tasks = self::get_tasks($user_id);

        $map = [];
        foreach ($tasks as $task) {
            $map[$task['id']] = $task;
        }

        $sorted = [];
        foreach ($ordered_ids as $id) {
            if (isset($map[$id])) {
                $sorted[] = $map[$id];
            }
        }

        self::save_tasks($user_id, $sorted);
    }

    /**
     * Extract hashtags from content (e.g., #urgent).
     */
    public static function extract_tags(string $content): array {
        preg_match_all('/#(\w+)/', $content, $matches);
        return $matches[1] ?? [];
    }
}
