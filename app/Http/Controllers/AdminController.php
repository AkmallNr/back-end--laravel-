<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'google_id']);

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $data['profile_picture'] = basename($path);
        }

        $user->update($data);

        return redirect()->route('admin.users')->with('success', 'User updated successfully');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->projects()->exists() || $user->tasks()->exists() || $user->schedule()->exists()) {
            return redirect()->route('admin.users')->with('error', 'Cannot delete user with associated projects, tasks, or schedules.');
        }

        if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully');
    }

    // Group (Project) Management
    public function listGroup()
    {
        $groups = Project::with('user')->get();
        return view('admin.groups.index', compact('groups'));
    }

    public function editGroup($id)
    {
        $group = Project::findOrFail($id);
        $users = User::all();
        return view('admin.groups.edit', compact('group', 'users'));
    }

    public function updateGroup(Request $request, $id)
    {
        $group = Project::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after:startDate',
            'userId' => 'required|exists:users,id',
        ]);

        $group->update($request->only(['name', 'description', 'startDate', 'endDate', 'userId']));

        return redirect()->route('admin.groups')->with('success', 'Group updated successfully');
    }

    public function deleteGroup($id)
    {
        $group = Project::findOrFail($id);

        if ($group->tasks()->exists()) {
            return redirect()->route('admin.groups')->with('error', 'Cannot delete group with associated tasks.');
        }

        $group->delete();

        return redirect()->route('admin.groups')->with('success', 'Group deleted successfully');
    }

    // Task Management
    public function listTask()
    {
        $tasks = Task::with('user', 'project')->get();
        return view('admin.tasks.index', compact('tasks'));
    }

    public function editTask($id)
    {
        $task = Task::findOrFail($id);
        $users = User::all();
        $projects = Project::all();
        return view('admin.tasks.edit', compact('task', 'users', 'projects'));
    }

    public function updateTask(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'required|date',
            'reminder' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|boolean',
            'completed_at' => 'nullable|date',
            'projectId' => 'nullable|exists:projects,id',
            'userId' => 'required|exists:users,id',
        ]);

        $task->update($request->only(['name', 'description', 'deadline', 'reminder', 'priority', 'status', 'completed_at', 'projectId', 'userId']));

        return redirect()->route('admin.tasks')->with('success', 'Task updated successfully');
    }

    public function deleteTask($id)
    {
        $task = Task::findOrFail($id);

        if ($task->attachments()->exists()) {
            foreach ($task->attachments as $attachment) {
                if ($attachment->file_path && Storage::disk('public')->exists($attachment->file_path)) {
                    Storage::disk('public')->delete($attachment->file_path);
                }
                $attachment->delete();
            }
        }

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
            'notes' => 'nullable|string',
            'repeat' => 'nullable|string',
            'day' => 'nullable|string',
            'startTime' => 'required|date_format:H:i',
            'endTime' => 'required|date_format:H:i|after:startTime',
            'userId' => 'required|exists:users,id',
        ]);

        $schedule->update($request->only(['name', 'notes', 'repeat', 'day', 'startTime', 'endTime', 'userId']));

        return redirect()->route('admin.schedules')->with('success', 'Schedule updated successfully');
    }

    public function deleteSchedule($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        return redirect()->route('admin.schedules')->with('success', 'Schedule deleted successfully');
    }
}