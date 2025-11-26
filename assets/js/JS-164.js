// JS-164.js

function closeModal(modalId){
    $('#' + modalId).modal('hide');
}

// === Fungsi Toggle Status (Checkbox) ===
function toggleStatus(taskId, checkbox) {
    const isChecked = $(checkbox).is(':checked');
    const newStatus = isChecked ? 'completed' : 'pending';
    const row = $(checkbox).closest('tr');

    $.post('process/task_status.php', { task_id: taskId, status: newStatus }, function(resp){
        if(resp.success){
            row.attr('data-status', newStatus);
            row.data('status', newStatus);
            updateCountersAfterStatusChange(isChecked);
        } else {
            alert('Gagal mengubah status: ' + resp.message);
            $(checkbox).prop('checked', !isChecked); 
        }
    }, 'json').fail(function(xhr, status, error){
        console.error("Status Update Error:", xhr.responseText);
        alert('Koneksi error atau JSON tidak valid (Cek Console)');
        $(checkbox).prop('checked', !isChecked);
    });
}

function updateCountersAfterStatusChange(isNowCompleted) {
    let pending = parseInt($('#pendingTasks').text()) || 0;
    let completed = parseInt($('#completedTasks').text()) || 0;

    if (isNowCompleted) {
        $('#pendingTasks').text(Math.max(0, pending - 1));
        $('#completedTasks').text(completed + 1);
    } else {
        $('#pendingTasks').text(pending + 1);
        $('#completedTasks').text(Math.max(0, completed - 1));
    }
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
    // Kita ambil data langsung dari atribut HTML (paling akurat)
    const rowEl = $(row);
    
    $('#editTaskId').val(rowEl.attr('data-id'));
    $('#editTitle').val(rowEl.attr('data-title'));
    $('#editDesc').val(rowEl.attr('data-desc'));
    $('#editPriority').val(rowEl.attr('data-priority'));
    
    // Set Dropdown Kategori
    // Pastikan value ini sesuai dengan value="ID" di opsi select
    $('#editCategory').val(rowEl.attr('data-category-id')); 
    
    const deadline = rowEl.attr('data-deadline');
    $('#editDeadline').val(deadline !== '-' ? deadline : '');
    $('#editStatus').val(rowEl.attr('data-status'));
    
    $('#editModal').modal('show');
}

// === Delete Task ===
function deleteTask(taskId){
    if(!confirm('Are you sure you want to delete this task?')) return;

    const row = $('tr[data-id="' + taskId + '"]');

    $.post('process/task_delete.php', {task_id: taskId}, function(resp){
        if(resp.success){
            row.remove();
            let total = parseInt($('#totalTasks').text()) || 0;
            $('#totalTasks').text(Math.max(0, total - 1));

            const status = row.attr('data-status');
            if(status === 'pending'){
                let p = parseInt($('#pendingTasks').text()) || 0;
                $('#pendingTasks').text(Math.max(0, p - 1));
            } else if(status === 'completed'){
                let c = parseInt($('#completedTasks').text()) || 0;
                $('#completedTasks').text(Math.max(0, c - 1));
            }

            if($('#tasksTbody tr').length === 0){
                $('#tasksTbody').html('<tr><td colspan="6"><div class="empty-state"><h4>No tasks yet</h4><div>Add your first task using the <strong>New Task</strong> button.</div></div></td></tr>');
            }
        } else {
            alert(resp.message);
        }
    }, 'json').fail(function(xhr, status, error){
        console.error("Delete Error:", xhr.responseText);
        alert('Gagal menghapus task.');
    });
}

// === Edit Form Submit (BAGIAN KRUSIAL) ===
$('#editTaskForm').on('submit', function(e){
    e.preventDefault();
    
    $.post('process/task_edit.php', $(this).serialize(), function(resp){
        console.log("Response Edit:", resp); // Cek ini di Console Browser jika gagal
        
        if(resp.success){
            const t = resp.task;
            // Cari baris tabel berdasarkan ID task
            const row = $('tr[data-id="'+t.id+'"]');
            
            if(row.length === 0) {
                alert("Task berhasil disimpan di database, tapi baris tabel tidak ditemukan. Silakan refresh.");
                location.reload();
                return;
            }

            // 1. UPDATE ATTRIBUTES HTML (Penting untuk edit selanjutnya)
            row.attr('data-title', t.title);
            row.attr('data-desc', t.description);
            row.attr('data-priority', t.priority);
            row.attr('data-category-id', t.category_id);
            row.attr('data-category', t.category);
            row.attr('data-deadline', t.deadline || '-');
            row.attr('data-status', t.status);
            
            // 2. UPDATE VISUAL TABEL
            row.find('.task-title').text(t.title);
            row.find('.task-desc').text(t.description.length > 120 ? t.description.substr(0,120)+'...' : t.description);
            
            // Update Badge Priority
            row.find('.priority-badge')
                .text(t.priority.charAt(0).toUpperCase() + t.priority.slice(1))
                .removeClass('priority-low priority-medium priority-high')
                .addClass('priority-' + t.priority);

            // Update Category Pill
            row.find('.category-pill')
                .text(t.category)
                .removeClass('category-work category-personal category-uncategorized')
                .addClass('category-uncategorized'); // Sederhanakan class warna dulu agar tidak error

            row.find('td').eq(4).text(t.deadline || '-');
            row.find('input[type="checkbox"]').prop('checked', t.status === 'completed');
            
            $('#editModal').modal('hide');
            
            // Feedback visual sukses (Opsional)
            // alert("Berhasil diubah!"); 
        } else {
            alert('Gagal update: ' + resp.message);
        }
    }, 'json')
    .fail(function(xhr, status, error){
        // JIKA MASUK SINI, BERARTI ADA SPASI/ERROR DI FILE PHP
        console.error("Edit Failed:", xhr.responseText);
        alert('Terjadi kesalahan sistem! Cek Console (F12) untuk detailnya.');
    });
});

// === Add Task ===
$(document).ready(function(){
    $('#addTaskForm').on('submit', function(e){
        e.preventDefault(); 

        $.ajax({
            url: 'process/task_add.php',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(resp){
                if(resp.success){
                    const t = resp.task;
                    let pClass = t.priority==='low' ? 'priority-low' : (t.priority==='high' ? 'priority-high' : 'priority-medium');
                    
                    let newRow = `
                    <tr data-id="${t.id}" 
                        data-title="${t.title}" 
                        data-desc="${t.description}" 
                        data-priority="${t.priority}" 
                        data-category-id="${t.category_id}" 
                        data-category="${t.category}" 
                        data-deadline="${t.deadline}" 
                        data-status="${t.status}">
                        
                        <td class="checkbox-td">
                            <input type="checkbox" onchange="toggleStatus(${t.id}, this)" ${t.status==='completed'?'checked':''}>
                        </td>
                        <td>
                            <div class="task-title">${t.title}</div>
                            ${t.description ? '<div class="task-desc">'+(t.description.length>120 ? t.description.substr(0,120)+'...' : t.description)+'</div>' : ''}
                        </td>
                        <td><span class="priority-badge ${pClass}">${t.priority.charAt(0).toUpperCase()+t.priority.slice(1)}</span></td>
                        <td><span class="category-pill">${t.category}</span></td>
                        <td>${t.deadline || '-'}</td>
                        <td class="actions">
                            <button class="action-btn view-btn" onclick="openView(this.closest('tr'))" title="View"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                            <button class="action-btn edit-btn" onclick="openEdit(this.closest('tr'))" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6-6m2 2l-6 6m-2 2H6v-3"/></svg></button>
                            <button class="action-btn delete-btn" onclick="deleteTask(${t.id})" title="Delete"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                        </td>
                    </tr>`;

                    $('#tasksTbody').prepend(newRow);
                    let total = parseInt($('#totalTasks').text()) || 0;
                    $('#totalTasks').text(total + 1);
                    if(t.status === 'pending'){
                        let p = parseInt($('#pendingTasks').text()) || 0;
                        $('#pendingTasks').text(p + 1);
                    } else {
                        let c = parseInt($('#completedTasks').text()) || 0;
                        $('#completedTasks').text(c + 1);
                    }
                    $('#addTaskForm')[0].reset();
                    $('#addTaskModal').modal('hide');
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