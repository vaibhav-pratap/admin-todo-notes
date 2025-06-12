<?php

namespace AdminTodoNotes;

if (!defined('ABSPATH')) exit;

class AdminTodoNotes {
    public static function init(): void {
        add_action('admin_enqueue_scripts', [self::class, 'enqueue_assets']);
        add_action('wp_dashboard_setup', [self::class, 'add_dashboard_widget']);
        add_action('admin_post_admin_todo_notes_save', [self::class, 'save_notes']);
    }

    public static function enqueue_assets(): void {
        wp_enqueue_style(
            'admin-todo-notes-style',
            plugin_dir_url(__FILE__) . '../assets/style.css',
            [],
            '1.0.0'
        );
    }

    public static function add_dashboard_widget(): void {
        wp_add_dashboard_widget(
            'admin_todo_notes_widget',
            __('📝 My To-Do Notes', 'admin-todo-notes'),
            [self::class, 'render_widget']
        );
    }

    public static function render_widget(): void {
        if (!current_user_can('edit_posts')) {
            echo esc_html__('You do not have permission to view this.', 'admin-todo-notes');
            return;
        }

        $user_id = get_current_user_id();
        $notes = get_user_meta($user_id, '_admin_todo_notes', true);

        ?>
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" class="todo-notes-form">
            <?php wp_nonce_field('admin_todo_notes_save_action', 'admin_todo_notes_nonce'); ?>
            <input type="hidden" name="action" value="admin_todo_notes_save">
            <textarea name="admin_todo_notes" rows="8" placeholder="Write your tasks here..."><?php echo esc_textarea($notes); ?></textarea>
            <p><button type="submit" class="button button-primary"><?php esc_html_e('💾 Save Notes', 'admin-todo-notes'); ?></button></p>
        </form>
        <?php
    }

    public static function save_notes(): void {
        if (
            !current_user_can('edit_posts') ||
            !isset($_POST['admin_todo_notes_nonce']) ||
            !wp_verify_nonce($_POST['admin_todo_notes_nonce'], 'admin_todo_notes_save_action')
        ) {
            wp_die(__('Security check failed.', 'admin-todo-notes'));
        }

        $notes = sanitize_textarea_field($_POST['admin_todo_notes'] ?? '');
        update_user_meta(get_current_user_id(), '_admin_todo_notes', $notes);

        wp_redirect(admin_url('index.php'));
        exit;
    }
}
