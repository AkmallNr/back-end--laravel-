<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group;
use App\Models\Task;
use App\Models\Schedule;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // User Management
    public function listUser()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'google_id' => 'nullable|string|max:255',
        ]);

        $user->update($request->only(['name', 'email', 'google_id']));
        
        return redirect()->route('admin.users')->with('success', 'User updated successfully');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        
        return redirect()->route('admin.users')->with('success', 'User deleted successfully');
    }

    // Group Management
    public function listGroup()
    {
        $groups = Group::with('user')->get();
        return view('admin.groups.index', compact('groups'));
    }

    public function editGroup($id)
    {
        $group = Group::findOrFail($id);
        $users = User::all();
        return view('admin.groups.edit', compact('group', 'users'));
    }

    public function updateGroup(Request $request, $id)
    {
        $group = Group::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'belong_to' => 'required|exists:users,id',
        ]);

        $group->update($request->all());
        
        return redirect()->route('admin.groups')->with('success', 'Group updated successfully');
    }

    public function deleteGroup($id)
    {
        $group = Group::findOrFail($id);
        $group->delete();
        
        return redirect()->route('admin.groups')->with('success', 'Group deleted successfully');
    }

    // Task Management
    public function listTask()
    {
        $tasks = Task::with('user')->get();
        return view('admin.tasks.index', compact('tasks'));
    }

    public function editTask($id)
    {
        $task = Task::findOrFail($id);
        $users = User::all();
        return view('admin.tasks.edit', compact('task', 'users'));
    }

    public function updateTask(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'required|date',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed',
            'completed_at' => 'nullable|date',
            'belong_to' => 'required|exists:users,id',
        ]);

        $task->update($request->all());
        
        return redirect()->route('admin.tasks')->with('success', 'Task updated successfully');
    }

    public function deleteTask($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();
        
        return redirect()->route('admin.tasks')->with('success', 'Task deleted successfully');
    }

    // Schedule Management
    public function listSchedule()
    {
        $schedules = Schedule::with('user')->get();
        return view('admin.schedules.index', compact('schedules'));
    }

    public function editSchedule($id)
    {
        $schedule = Schedule::findOrFail($id);
        $users = User::all();
        return view('admin.schedules.edit', compact('schedule', 'users'));
    }

    public function updateSchedule(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'required|date',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed',
            'completed_at' => 'nullable|date',
            'belong_to' => 'required|exists:users,id',
        ]);

        $schedule->update($request->all());
        
        return redirect()->route('admin.schedules')->with('success', 'Schedule updated successfully');
    }

    public function deleteSchedule($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();
        
        return redirect()->route('admin.schedules')->with('success', 'Schedule deleted successfully');
    }
}