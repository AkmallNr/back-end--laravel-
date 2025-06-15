@extends('admin.layout')

@section('content')
<div class="table-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit User</h2>
        <a href="{{ route('admin.users') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
    
    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="google_id" class="form-label">Google ID</label>
                    <input type="text" class="form-control @error('google_id') is-invalid @enderror" 
                           id="google_id" name="google_id" value="{{ old('google_id', $user->google_id) }}">
                    @error('google_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="profile_picture" class="form-label">Profile Picture</label>
                    <input type="file" class="form-control @error('profile_picture') is-invalid @enderror" 
                           id="profile_picture" name="profile_picture" accept="image/*">
                    @if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture))
                        <div class="mt-2">
                            <img src="{{ Storage::url($user->profile_picture) }}" alt="{{ $user->name }}" style="width: 100px; height: 100px; border-radius: 50%;">
                        </div>
                    @endif
                    @error('profile_picture')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update User
            </button>
            <a href="{{ route('admin.users') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection