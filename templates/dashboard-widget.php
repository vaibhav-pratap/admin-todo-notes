<?php
defined('ABSPATH') || exit;

?>
<div id="admin-todo-widget">
    <form id="admin-todo-form">
        <input type="text" name="content" id="admin-todo-content" placeholder="<?php esc_attr_e('Add new task...', ADTODO_TEXT_DOMAIN); ?>" required />
        <input type="date" name="due_date" id="admin-todo-due-date" />
        <button type="submit" class="button button-primary"><?php _e('Add Task', ADTODO_TEXT_DOMAIN); ?></button>
    </form>

    <ul id="admin-todo-list">
        <?php if (!empty($tasks)) : ?>
            <?php foreach ($tasks as $task) : ?>
                <li class="admin-todo-item" data-id="<?php echo esc_attr($task['id']); ?>">
                    <div class="todo-top">
                        <span class="todo-status <?php echo esc_attr($task['status']); ?>"><?php echo ucfirst(esc_html($task['status'])); ?></span>
                        <?php if (!empty($task['due_date'])): ?>
                            <span class="todo-due <?php echo $task['overdue'] ? 'overdue' : ''; ?>">
                                <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($task['due_date']))); ?>
                            </span>
                        <?php endif; ?>
                        <button class="admin-todo-delete" title="<?php esc_attr_e('Delete task', ADTODO_TEXT_DOMAIN); ?>">&times;</button>
                    </div>
                    <div class="todo-content">
                        <?php echo esc_html($task['content']); ?>
                        <?php if (!empty($task['tags'])): ?>
                            <div class="todo-tags">
                                <?php foreach ($task['tags'] as $tag): ?>
                                    <span class="todo-tag">#<?php echo esc_html($tag); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php else : ?>
            <li class="admin-todo-empty"><?php _e('No tasks yet. Add one above.', ADTODO_TEXT_DOMAIN); ?></li>
        <?php endif; ?>
    </ul>
</div>
