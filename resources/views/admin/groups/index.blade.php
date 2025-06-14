@extends('admin.layout')

@section('content')
<div class="table-container">
    <h2 class="mb-4">All Group</h2>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Belong to</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($groups as $group)
                <tr>
                    <td>{{ $group->id }}</td>
                    <td>{{ $group->name }}</td>
                    <td>{{ Str::limit($group->description, 50) }}</td>
                    <td>{{ $group->start_date ? date('Y-m-d', strtotime($group->start_date)) : '-' }}</td>
                    <td>{{ $group->end_date ? date('Y-m-d', strtotime($group->end_date)) : '-' }}</td>
                    <td>{{ $group->user->name ?? 'No User' }}</td>
                    <td>
                        <a href="{{ route('admin.groups.edit', $group->id) }}" class="btn btn-edit btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-delete btn-sm" onclick="confirmDelete({{ $group->id }}, 'group')">
                            <i class="fas fa-trash"></i>
                        </button>
                        
                        <form id="delete-form-{{ $group->id }}" action="{{ route('admin.groups.delete', $group->id) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4">No groups found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection