// JS-164.js
function closeModal(modalId){
    $('#' + modalId).modal('hide');
}

// === View Task ===
function openView(row){
    const t = $(row).data();
    $('#viewTitle').text(t.title);
    $('#viewDesc').text(t.desc || '-');
    $('#viewPriority').text(t.priority.charAt(0).toUpperCase() + t.priority.slice(1));
    $('#viewCategory').text(t.category);
    $('#viewDeadline').text(t.deadline);
    $('#viewStatus').text(t.status.charAt(0).toUpperCase() + t.status.slice(1));
    $('#viewModal').modal('show');
}

// === Edit Task ===
function openEdit(row){
    const t = $(row).data();
    $('#editTaskId').val(t.id);
    $('#editTitle').val(t.title);
    $('#editDesc').val(t.desc);
    $('#editPriority').val(t.priority);
    $('#editCategory').val(t.category);
    $('#editDeadline').val(t.deadline !== '-' ? t.deadline : '');
    $('#editStatus').val(t.status);
    $('#editModal').modal('show');
}

// === Delete Task ===
// Fungsi global agar bisa dipanggil dari tombol delete
function deleteTask(taskId, row){
    if(!confirm('Are you sure you want to delete this task?')) return;

    $.post('process/task_delete.php', {task_id: taskId}, function(resp){
        if(resp.success){
            // Hapus row dari tabel
            $(row).remove();

            // Update counter
            $('#totalTasks').text(parseInt($('#totalTasks').text()) - 1);

            // Jika status pending, kurangi juga pendingTasks
            const status = $(row).data('status');
            if(status === 'pending'){
                $('#pendingTasks').text(parseInt($('#pendingTasks').text()) - 1);
            } else if(status === 'completed'){
                $('#completedTasks').text(parseInt($('#completedTasks').text()) - 1);
            }

            // Jika tabel kosong, tampilkan empty-state
            if($('#tasksTbody tr').length === 0){
                $('#tasksTbody').html('<tr><td colspan="6"><div class="empty-state"><h4>No tasks yet</h4><div>Add your first task using the <strong>New Task</strong> button.</div></div></td></tr>');
            }

        } else {
            alert(resp.message);
        }
    }, 'json').fail(function(){
        alert('Error deleting task.');
    });
}


// === Edit form submit ===
$('#editTaskForm').on('submit', function(e){
    e.preventDefault();
    $.post('process/task_edit.php', $(this).serialize(), function(resp){
        if(resp.success){
            const t = resp.task;
            const row = $('tr[data-id="'+t.id+'"]');
            row.data(t);
            row.find('.task-title').text(t.title);
            row.find('.task-desc').text(t.description.length>120?t.description.substr(0,120)+'...':t.description);
            row.find('.priority-badge').text(t.priority.charAt(0).toUpperCase()+t.priority.slice(1))
                .removeClass('priority-low priority-medium priority-high')
                .addClass(t.priority==='low'?'priority-low':(t.priority==='high'?'priority-high':'priority-medium'));
            row.find('.category-pill')
            .text(t.category.charAt(0).toUpperCase() + t.category.slice(1)) 
            .removeClass('category-work category-personal category-uncategorized') 
            .addClass(
                t.category.toLowerCase() === 'work' ? 'category-work' :
                t.category.toLowerCase() === 'personal' ? 'category-personal' :
                'category-uncategorized'
            );
            row.find('td').eq(4).text(t.deadline);
            row.find('input[type="checkbox"]').prop('checked', t.status==='completed');
            $('#editModal').modal('hide');
        } else {
            alert(resp.message);
        }
    }, 'json');
});

// add task
// === Add Task ===
$(document).ready(function(){

    // Submit Add Task Form via AJAX
    $('#addTaskForm').on('submit', function(e){
        e.preventDefault(); // cegah reload halaman

        $.ajax({
            url: 'process/task_add.php',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(resp){
                if(resp.success){
                    const t = resp.task;

                    // Buat class priority
                    let pClass = t.priority==='low' ? 'priority-low' : (t.priority==='high' ? 'priority-high' : 'priority-medium');
                    // Buat class category
                    let cClass = t.category==='work' ? 'category-work' : (t.category==='personal' ? 'category-personal' : 'category-uncategorized');

                    let newRow = `<tr data-id="${t.id}" data-title="${t.title}" data-desc="${t.description}" data-priority="${t.priority}" data-category="${t.category}" data-deadline="${t.deadline}" data-status="${t.status}">
                        <td class="checkbox-td"><input type="checkbox" ${t.status==='completed'?'checked':''}></td>
                        <td>
                            <div class="task-title">${t.title}</div>
                            ${t.description ? '<div class="task-desc">'+(t.description.length>120 ? t.description.substr(0,120)+'...' : t.description)+'</div>' : ''}
                        </td>
                        <td><span class="priority-badge ${pClass}">${t.priority.charAt(0).toUpperCase()+t.priority.slice(1)}</span></td>
                        <td><span class="category-pill ${cClass}">${t.category.charAt(0).toUpperCase()+t.category.slice(1)}</span></td>
                        <td>${t.deadline || '-'}</td>
                        <td class="actions">
                            <button class="action-btn view-btn" onclick="openView(this.closest('tr'))" title="View">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                            <button class="action-btn edit-btn" onclick="openEdit(this.closest('tr'))" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6-6m2 2l-6 6m-2 2H6v-3"/>
                                </svg>
                            </button>
                            <button class="action-btn delete-btn" onclick="deleteTask(${t.id})" title="Delete">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </td>
                    </tr>`;

                    // Tambahkan task baru di atas
                    $('#tasksTbody').prepend(newRow);

                    // Update counters
                    $('#totalTasks').text(parseInt($('#totalTasks').text()) + 1);
                    if(t.status === 'pending'){
                        $('#pendingTasks').text(parseInt($('#pendingTasks').text()) + 1);
                    } else if(t.status === 'completed'){
                        $('#completedTasks').text(parseInt($('#completedTasks').text()) + 1);
                    }

                    // Reset form & tutup modal
                    $('#addTaskForm')[0].reset();
                    $('#addTaskModal').modal('hide');

                    // Hapus empty state jika ada
                    $('#tasksTbody .empty-state').closest('tr').remove();
                } else {
                    alert(resp.message);
                }
            },
            error: function(err){
                console.error(err);
                alert('Error adding task.');
            }
        });
    });

});


