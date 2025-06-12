jQuery(document).ready(function ($) {
    const $form = $('#admin-todo-form');
    const $list = $('#admin-todo-list');

    // Add Task
    $form.on('submit', function (e) {
        e.preventDefault();

        const content = $('#admin-todo-content').val().trim();
        const due_date = $('#admin-todo-due-date').val();

        if (!content) return;

        $.post(AdminTodoVars.ajax_url, {
            action: 'admin_todo_add_task',
            content,
            due_date,
            nonce: AdminTodoVars.nonce
        }, function (res) {
            if (res.success) {
                location.reload(); // Quick solution: refresh to get updated list
            } else {
                alert(res.data || 'Error adding task.');
            }
        });
    });

    // Delete Task
    $list.on('click', '.admin-todo-delete', function () {
        if (!confirm(AdminTodoVars.i18n.confirm_delete)) return;

        const $item = $(this).closest('.admin-todo-item');
        const task_id = $item.data('id');

        $.post(AdminTodoVars.ajax_url, {
            action: 'admin_todo_delete_task',
            task_id,
            nonce: AdminTodoVars.nonce
        }, function (res) {
            if (res.success) {
                $item.fadeOut(200, function () { $(this).remove(); });
            } else {
                alert(res.data || 'Error deleting task.');
            }
        });
    });

    // Make tasks sortable
    $list.sortable({
        handle: '.todo-content',
        update: function () {
            const order = $list.children('.admin-todo-item').map(function () {
                return $(this).data('id');
            }).get();

            $.post(AdminTodoVars.ajax_url, {
                action: 'admin_todo_reorder_tasks',
                order,
                nonce: AdminTodoVars.nonce
            });
        }
    });

    // (Optional) You can add inline editing here later
});
