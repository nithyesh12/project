<?php require_once 'layout_header.php'; ?>

<div class="header">
    <div>
        <h1>Manage Users</h1>
        <p style="color: var(--text-light); margin-top: 0.5rem;">View and manage registered farmers and users.</p>
    </div>
</div>

<div class="card table-wrapper">
    <table id="usersTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email Address</th>
                <th>Joined Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr><td colspan="5" style="text-align: center;">Loading users...</td></tr>
        </tbody>
    </table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', loadUsers);

    async function loadUsers() {
        const tbody = document.querySelector('#usersTable tbody');
        try {
            const res = await fetch('../api/admin_users.php?action=list');
            const result = await res.json();
            
            if (result.status === 'success') {
                tbody.innerHTML = '';
                if(result.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align: center;">No users found.</td></tr>';
                    return;
                }
                
                result.data.forEach(user => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>#${user.id}</td>
                        <td style="font-weight: 500;">${user.first_name} ${user.last_name}</td>
                        <td>${user.email}</td>
                        <td>${new Date(user.created_at).toLocaleDateString()}</td>
                        <td>
                            <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})">
                                <i class="fa-solid fa-trash"></i> Delete
                            </button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            }
        } catch(e) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: red;">Failed to load users.</td></tr>';
        }
    }

    async function deleteUser(id) {
        if(confirm(`Are you sure you want to delete user #${id}? This action cannot be undone.`)) {
            try {
                const res = await fetch('../api/admin_users.php?action=delete', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({id})
                });
                const data = await res.json();
                if(data.status === 'success') {
                    loadUsers();
                } else {
                    alert(data.message);
                }
            } catch(e) {
                alert("Error deleting user.");
            }
        }
    }
</script>

<?php require_once 'layout_footer.php'; ?>
