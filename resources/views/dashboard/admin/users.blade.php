@extends('layouts.app')

@section('title', 'Manage Users — KoreSearch')

@section('content')

<div class="dashboard-layout">

    @include('partials.admin-sidebar')

    <div class="dashboard-main">

        <div class="dash-header">
            <h1 class="dash-title">Manage Users</h1>
            <p class="dash-subtitle">Create, edit roles, and manage users</p>
        </div>

        <div class="dash-section">
            <div class="section-header">
                <h2 class="dash-section-title">All Users</h2>
                <button class="btn btn-primary" id="createUserBtn">+ Create User</button>
            </div>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="role-badge role-{{ $user->getRoleNames()->first() ?? $user->role }}">
                                        {{ ucfirst($user->getRoleNames()->first() ?? $user->role) }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn-action btn-action-role" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" data-user-role="{{ $user->getRoleNames()->first() ?? $user->role }}" title="Change Role">🔑</button>
                                        <button class="btn-action btn-action-password" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" title="Change Password">🔒</button>
                                        @if($user->id !== Auth::id())
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete user {{ $user->name }}?')" style="display:inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-action btn-action-delete" title="Delete User">🗑️</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $users->links('vendor.pagination.custom') }}
            </div>
        </div>

        <div class="modal-overlay hidden" id="createUserModal">
    <div class="modal-content modal-sm">
        <button class="modal-close" id="createUserModalClose">&times;</button>
        <h2 class="modal-title">Create New User</h2>
        <form method="POST" action="{{ route('admin.users.store') }}" id="createUserForm">
            @csrf
            <div class="form-group">
                <label class="form-label" for="createName">Name</label>
                <input type="text" name="name" id="createName" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="createEmail">Email</label>
                <input type="email" name="email" id="createEmail" class="form-input" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="createPassword">Password</label>
                    <input type="password" name="password" id="createPassword" class="form-input" minlength="8" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="createPasswordConfirm">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="createPasswordConfirm" class="form-input" minlength="8" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="createRole">Role</label>
                <select name="role" id="createRole" class="form-select" required>
                    <option value="">Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="instructor">Instructor</option>
                    <option value="student">Student</option>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" id="createUserModalCancel">Cancel</button>
                <button type="submit" class="btn btn-primary">Create User</button>
            </div>
        </form>
    </div>
</div>

    </div>
</div>

<div class="modal-overlay hidden" id="roleModal">
    <div class="modal-content modal-sm">
        <button class="modal-close" id="roleModalClose">&times;</button>
        <h2 class="modal-title">Change Role</h2>
        <p class="modal-subtitle" id="roleModalUser">User</p>
        <form method="POST" action="" id="roleForm">
            @csrf @method('PATCH')
            <div class="form-group">
                <label class="form-label" for="roleSelect">New Role</label>
                <select name="role" id="roleSelect" class="form-select" required>
                    <option value="admin">Admin</option>
                    <option value="instructor">Instructor</option>
                    <option value="student">Student</option>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" id="roleModalCancel">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Role</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay hidden" id="passwordModal">
    <div class="modal-content modal-sm">
        <button class="modal-close" id="passwordModalClose">&times;</button>
        <h2 class="modal-title">Change Password</h2>
        <p class="modal-subtitle" id="passwordModalUser">User</p>
        <form method="POST" action="" id="passwordForm">
            @csrf @method('PATCH')
            <div class="form-group">
                <label class="form-label" for="newPassword">New Password</label>
                <input type="password" name="password" id="newPassword" class="form-input" minlength="8" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="newPasswordConfirm">Confirm Password</label>
                <input type="password" name="password_confirmation" id="newPasswordConfirm" class="form-input" minlength="8" required>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" id="passwordModalCancel">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Password</button>
            </div>
        </form>
    </div>
</div>

@include('partials.dashboard-scripts')

<script>
(function() {
    var roleBtns = document.querySelectorAll('.btn-action-role');
    var roleModal = document.getElementById('roleModal');
    var roleForm = document.getElementById('roleForm');
    var roleSelect = document.getElementById('roleSelect');
    var roleModalUser = document.getElementById('roleModalUser');

    roleBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = btn.getAttribute('data-user-id');
            var name = btn.getAttribute('data-user-name');
            var role = btn.getAttribute('data-user-role');
            roleForm.action = '/admin/users/' + id + '/role';
            roleSelect.value = role;
            roleModalUser.textContent = name;
            roleModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });
    });

    var passwordBtns = document.querySelectorAll('.btn-action-password');
    var passwordModal = document.getElementById('passwordModal');
    var passwordForm = document.getElementById('passwordForm');
    var passwordModalUser = document.getElementById('passwordModalUser');

    passwordBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = btn.getAttribute('data-user-id');
            var name = btn.getAttribute('data-user-name');
            passwordForm.action = '/admin/users/' + id + '/password';
            passwordModalUser.textContent = name;
            passwordModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });
    });

    var createUserBtn = document.getElementById('createUserBtn');
    var createUserModal = document.getElementById('createUserModal');

    if (createUserBtn && createUserModal) {
        createUserBtn.addEventListener('click', function() {
            createUserModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });
    }

    function closeModal(modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    document.getElementById('roleModalClose').addEventListener('click', function() { closeModal(roleModal); });
    document.getElementById('roleModalCancel').addEventListener('click', function() { closeModal(roleModal); });
    document.getElementById('passwordModalClose').addEventListener('click', function() { closeModal(passwordModal); });
    document.getElementById('passwordModalCancel').addEventListener('click', function() { closeModal(passwordModal); });

    var createUserModalClose = document.getElementById('createUserModalClose');
    var createUserModalCancel = document.getElementById('createUserModalCancel');
    if (createUserModalClose) createUserModalClose.addEventListener('click', function() { closeModal(createUserModal); });
    if (createUserModalCancel) createUserModalCancel.addEventListener('click', function() { closeModal(createUserModal); });

    roleModal.addEventListener('click', function(e) { if (e.target === roleModal) closeModal(roleModal); });
    passwordModal.addEventListener('click', function(e) { if (e.target === passwordModal) closeModal(passwordModal); });
    if (createUserModal) createUserModal.addEventListener('click', function(e) { if (e.target === createUserModal) closeModal(createUserModal); });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (!roleModal.classList.contains('hidden')) closeModal(roleModal);
            if (!passwordModal.classList.contains('hidden')) closeModal(passwordModal);
            if (createUserModal && !createUserModal.classList.contains('hidden')) closeModal(createUserModal);
        }
    });
})();
</script>

@endsection
