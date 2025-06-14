@extends('admin.layout')

@section('content')
<div class="table-container">
    <h2 class="mb-4">All Task</h2>
    
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
                @forelse($tasks as $task)
                <tr>
                    <td>{{ $task->id }}</td>
                    <td>{{ $task->name }}</td>
                    <td>{{ Str::limit($task->description, 30) }}</td>
                    <td>{{ $task->deadline ? date('Y-m-d', strtotime($task->deadline)) : '-' }}</td>
                    <td>
                        <span class="badge bg-{{ $task->priority == 'high' ? 'danger' : ($task->priority == 'medium' ? 'warning' : 'success') }}">
                            {{ ucfirst($task->priority) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-{{ $task->status == 'completed' ? 'success' : ($task->status == 'in_progress' ? 'primary' : 'secondary') }}">
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>
                    </td>
                    <td>{{ $task->completed_at ? date('Y-m-d', strtotime($task->completed_at)) : '-' }}</td>
                    <td>{{ $task->user->name ?? 'No User' }}</td>
                    <td>
                        <a href="{{ route('admin.tasks.edit', $task->id) }}" class="btn btn-edit btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-delete btn-sm" onclick="confirmDelete({{ $task->id }}, 'task')">
                            <i class="fas fa-trash"></i>
                        </button>
                        
                        <form id="delete-form-{{ $task->id }}" action="{{ route('admin.tasks.delete', $task->id) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-4">No tasks found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection