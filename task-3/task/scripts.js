// JavaScript for the Online Task Management System

// Validate Task Creation Form
const taskForm = document.querySelector('form');
if (taskForm) {
    taskForm.addEventListener('submit', (e) => {
        const title = document.querySelector('input[name="title"]').value.trim();
        const description = document.querySelector('textarea[name="description"]').value.trim();

        if (title === "" || description === "") {
            alert("Please fill out all fields before submitting.");
            e.preventDefault();
        }
    });
}

// Dynamic Task Status Update
const statusSelects = document.querySelectorAll('select[name="status"]');
statusSelects.forEach((select) => {
    select.addEventListener('change', (e) => {
        const taskId = select.dataset.taskId;
        const newStatus = select.value;

        fetch('Update_task_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: taskId, status: newStatus }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    alert('Task status updated successfully!');
                } else {
                    alert('Failed to update task status.');
                }
            })
            .catch((error) => {
                console.error('Error updating status:', error);
            });
    });
});

// Confirm Task Deletion
const deleteLinks = document.querySelectorAll('a[href*="Delete_task.php"]');
deleteLinks.forEach((link) => {
    link.addEventListener('click', (e) => {
        if (!confirm("Are you sure you want to delete this task?")) {
            e.preventDefault();
        }
    });
});
