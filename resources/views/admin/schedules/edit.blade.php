@extends('admin.layout')

@section('content')
<div class="table-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Schedule</h2>
        <a href="{{ route('admin.schedules') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
    
    <form action="{{ route('admin.schedules.update', $schedule->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name', $schedule->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="belong_to" class="form-label">Belong to</label>
                    <select class="form-select @error('belong_to') is-invalid @enderror" id="belong_to" name="belong_to" required>
                        <option value="">Select User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('belong_to', $schedule->userId) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('belong_to')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        
        <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea class="form-control @error('notes') is-invalid @enderror" 
                      id="notes" name="notes" rows="3">{{ old('notes', $schedule->notes) }}</textarea>
            @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="startTime" class="form-label">Start Time</label>
                    <input type="time" class="form-control @error('startTime') is-invalid @enderror" 
                           id="startTime" name="startTime" value="{{ old('startTime', $schedule->startTime) }}" required>
                    @error('startTime')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="endTime" class="form-label">End Time</label>
                    <input type="time" class="form-control @error('endTime') is-invalid @enderror" 
                           id="endTime" name="endTime" value="{{ old('endTime', $schedule->endTime) }}" required>
                    @error('endTime')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Schedule
            </button>
            <a href="{{ route('admin.schedules') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection