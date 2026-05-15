@extends('layouts.app')
@section('title', 'Edit User')
@section('content')
<div class="gh-card max-w-xl">
    <div class="gh-card-header">
        <h1 class="font-header" style="letter-spacing:0.1em;">Edit User</h1>
    </div>
    <div class="gh-card-body">
        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf @method('PUT')

            <div class="mb-4">
                <label class="gh-label">Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="gh-input" required>
                @error('name')<p class="text-xs mt-1" style="color:#b91c1c;">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="gh-label">Email</label>
                <div class="flex items-center overflow-hidden rounded-md border" style="border-color:var(--gray-300);">
                    <input type="text" id="emailPrefix" oninput="syncEmail()"
                        value="{{ old('email', explode('@', $user->email)[0]) }}"
                        placeholder="username"
                        class="flex-1 px-3 py-2.5 text-sm focus:outline-none" style="background:transparent; color:var(--brown-900);" required>
                    <span class="px-3 py-2.5 text-sm border-l select-none" style="background:var(--cream-200); color:var(--gray-500); border-color:var(--cream-200); font-size:0.8rem;">@grandhika.com</span>
                </div>
                <input type="hidden" name="email" id="emailFull" value="{{ old('email', $user->email) }}">
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
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx" class="gh-input">
                @error('phone')<p class="text-xs mt-1" style="color:#b91c1c;">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="gh-label">
                    WhatsApp LID
                    <span class="normal-case font-normal" style="color:var(--gray-300); letter-spacing:0;">(isi otomatis saat user chat ke bot)</span>
                </label>
                <input type="text" name="wa_lid" value="{{ old('wa_lid', $user->wa_lid) }}" placeholder="contoh: 239603793526837"
                    class="gh-input" style="background:var(--cream-100);">
                @if($user->wa_lid)
                <p class="text-xs mt-1 flex items-center gap-1" style="color:#065f46;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    LID tersimpan — bot WA sudah bisa mengenali nomor ini
                </p>
                @else
                <p class="text-xs mt-1" style="color:var(--gray-300);">Kosong — terisi otomatis saat user pertama kali chat ke bot</p>
                @endif
                @error('wa_lid')<p class="text-xs mt-1" style="color:#b91c1c;">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="gh-label">
                    Password
                    <span class="normal-case font-normal" style="color:var(--gray-300); letter-spacing:0;">(kosongkan untuk tidak mengubah)</span>
                </label>
                <input type="password" name="password" class="gh-input">
                @error('password')<p class="text-xs mt-1" style="color:#b91c1c;">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="gh-label">Department</label>
                <select name="department_id" class="gh-select" required>
                    <option value="">Select Department</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ $user->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
                @error('department_id')<p class="text-xs mt-1" style="color:#b91c1c;">{{ $message }}</p>@enderror
            </div>

            @if($user->role == 'user')
            <div class="mb-4">
                <label class="gh-label">User Type</label>
                <select name="user_type" class="gh-select">
                    <option value="">Select Type</option>
                    @foreach($userTypes->where('code', '!=', 'admin') as $type)
                    <option value="{{ $type->code }}" {{ $user->user_type == $type->code ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
                @error('user_type')<p class="text-xs mt-1" style="color:#b91c1c;">{{ $message }}</p>@enderror
            </div>
            <div class="mb-4">
                <label class="gh-label">Start Date</label>
                <input type="date" name="start_date" value="{{ old('start_date', $user->start_date?->format('Y-m-d')) }}" class="gh-input">
                @error('start_date')<p class="text-xs mt-1" style="color:#b91c1c;">{{ $message }}</p>@enderror
            </div>
            @endif

            <div class="mb-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }}
                        class="w-4 h-4 rounded" style="accent-color:var(--brown-600);">
                    <span class="text-sm font-bold" style="color:var(--gray-500); letter-spacing:0.04em;">Active</span>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
