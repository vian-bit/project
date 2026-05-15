@extends('layouts.app')
@section('title', 'Add User')
@section('content')
<div class="gh-card max-w-xl">
    <div class="gh-card-header">
        <h1 class="font-header" style="letter-spacing:0.1em;">Add New User</h1>
    </div>
    <div class="gh-card-body">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf

            <div class="mb-4">
                <label class="gh-label">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="gh-input" required>
                @error('name')<p class="text-xs mt-1" style="color:#b91c1c;">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="gh-label">Email</label>
                <div class="flex items-center overflow-hidden rounded-md border transition" style="border-color:var(--gray-300);">
                    <input type="text" id="emailPrefix" oninput="syncEmail()"
                        value="{{ old('email') ? explode('@', old('email'))[0] : '' }}"
                        placeholder="username"
                        class="flex-1 px-3 py-2.5 text-sm focus:outline-none" style="background:transparent; color:var(--brown-900);" required>
                    <span class="px-3 py-2.5 text-sm border-l select-none" style="background:var(--cream-200); color:var(--gray-500); border-color:var(--cream-200); font-size:0.8rem;">@grandhika.com</span>
                </div>
                <input type="hidden" name="email" id="emailFull" value="{{ old('email', '') }}">
                @error('email')<p class="text-xs mt-1" style="color:#b91c1c;">{{ $message }}</p>@enderror
            </div>
            <script>
                function syncEmail() {
                    const prefix = document.getElementById('emailPrefix').value.trim();
                    document.getElementById('emailFull').value = prefix ? prefix + '@grandhika.com' : '';
                }
                syncEmail();
            </script>

            <div class="mb-4">
                <label class="gh-label">
                    WhatsApp Number
                    <span class="normal-case font-normal" style="color:var(--gray-300); letter-spacing:0;">(contoh: 08123456789)</span>
                </label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" class="gh-input">
                @error('phone')<p class="text-xs mt-1" style="color:#b91c1c;">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="gh-label">Password</label>
                <input type="password" name="password" class="gh-input" required>
                @error('password')<p class="text-xs mt-1" style="color:#b91c1c;">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="gh-label">Role</label>
                <select name="role" id="roleSelect" onchange="handleRoleChange(this.value)" class="gh-select" required>
                    <option value="">Select Role</option>
                    @if(Auth::user()->isSuperuser())
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    @endif
                    <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                </select>
                @error('role')<p class="text-xs mt-1" style="color:#b91c1c;">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="gh-label">Department</label>
                <select name="department_id" class="gh-select" required>
                    <option value="">Select Department</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
                @error('department_id')<p class="text-xs mt-1" style="color:#b91c1c;">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="gh-label">User Type</label>
                <select name="user_type" id="userTypeSelect" class="gh-select">
                    <option value="">Select Type</option>
                    @foreach($userTypes as $type)
                    <option value="{{ $type->code }}" data-role="{{ $type->code === 'admin' ? 'admin' : 'user' }}"
                        {{ old('user_type') === $type->code ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
                @error('user_type')<p class="text-xs mt-1" style="color:#b91c1c;">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <label class="gh-label">Start Date</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}" class="gh-input">
                @error('start_date')<p class="text-xs mt-1" style="color:#b91c1c;">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<script>
function handleRoleChange(role) {
    const sel = document.getElementById('userTypeSelect');
    Array.from(sel.options).forEach(opt => {
        if (!opt.value) return;
        const forAdmin = opt.dataset.role === 'admin';
        opt.style.display = (role === 'admin' ? forAdmin : !forAdmin) ? '' : 'none';
    });
    const selected = sel.options[sel.selectedIndex];
    if (selected && selected.style.display === 'none') sel.value = '';
    if (role === 'admin') {
        const adminOpt = sel.querySelector('option[data-role="admin"]');
        if (adminOpt) sel.value = adminOpt.value;
    }
}
handleRoleChange(document.getElementById('roleSelect').value);
</script>
@endsection
