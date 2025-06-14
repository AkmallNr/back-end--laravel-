@extends('admin.layout')

@section('content')
<div class="table-container">
    <h2 class="mb-4">All Schedule</h2>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Deadline</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Completed At</th>
                    <th>Belong to</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $schedule)
                <tr>
                    <td>{{ $schedule->id }}</td>
                    <td>{{ $schedule->name }}</td>
                    <td>{{ Str::limit($schedule->description, 30) }}</td>
                    <td>{{ $schedule->deadline ? date('Y-m-d', strtotime($schedule->deadline)) : '-' }}</td>
                    <td>
                        <span class="badge bg-{{ $schedule->priority == 'high' ? 'danger' : ($schedule->priority == 'medium' ? 'warning' : 'success') }}">
                            {{ ucfirst($schedule->priority) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-{{ $schedule->status == 'completed' ? 'success' : ($schedule->status == 'in_progress' ? 'primary' : 'secondary') }}">
                            {{ ucfirst(str_replace('_', ' ', $schedule->status)) }}
                        </span>
                    </td>
                    <td>{{ $schedule->completed_at ? date('Y-m-d', strtotime($schedule->completed_at)) : '-' }}</td>
                    <td>{{ $schedule->user->name ?? 'No User' }}</td>
                    <td>
                        <a href="{{ route('admin.schedules.edit', $schedule->id) }}" class="btn btn-edit btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-delete btn-sm" onclick="confirmDelete({{ $schedule->id }}, 'schedule')">
                            <i class="fas fa-trash"></i>
                        </button>
                        
                        <form id="delete-form-{{ $schedule->id }}" action="{{ route('admin.schedules.delete', $schedule->id) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-4">No schedules found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection