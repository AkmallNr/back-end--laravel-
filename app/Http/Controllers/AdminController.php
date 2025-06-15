<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Schedule;
use App\Models\Admin;
use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin')->except(['showLoginForm', 'login']); // Gunakan guard admin
    }

    // Admin Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::guard('admin')->attempt(['username' => $credentials['username'], 'password' => $credentials['password']], $request->has('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/admin/groups')->with('success', 'Login successful!');
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('username', 'remember'));
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout(); // Logout menggunakan guard admin
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/login')->with('success', 'Logout successful!');
    }

    public function listUsers()
    {
        $users = User::all(); // Ambil semua pengguna
        return view('admin.users.index', compact('users')); // Pastikan ada view 'admin/users/index.blade.php'
    }

    public function listGroup()
    {
        $groups = Group::all(); // Ambil semua grup tanpa relasi admin
        return view('admin.groups.index', compact('groups'));
    }

    public function editGroup($id)
    {
        $group = Project::findOrFail($id);
        $admins = Admin::all();
        $users = User::all();
        return view('admin.groups.edit', compact('group', 'admins', 'users'));
    }

    public function updateGroup(Request $request, $id)
    {
        $group = Project::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after:startDate',
            'adminId' => 'nullable|exists:admins,id',
            'userId' => 'nullable|exists:users,id',
        ]);

        $group->update($request->only(['name', 'description', 'startDate', 'endDate', 'adminId', 'userId']));

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
        $tasks = Task::with('user', 'project')->get(); // Hapus 'admin' dari with
        return view('admin.tasks.index', compact('tasks'));
    }

    public function editTask($id)
    {
        $task = Task::findOrFail($id);
        $admins = Admin::all();
        $users = User::all();
        $projects = Project::all();
        return view('admin.tasks.edit', compact('task', 'admins', 'users', 'projects'));
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
            'adminId' => 'nullable|exists:admins,id',
            'userId' => 'nullable|exists:users,id',
        ]);

        $task->update($request->only(['name', 'description', 'deadline', 'reminder', 'priority', 'status', 'completed_at', 'projectId', 'adminId', 'userId']));

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
        $schedules = Schedule::with('user')->get(); // Hapus 'admin' dari with
        return view('admin.schedules.index', compact('schedules'));
    }

    public function editSchedule($id)
    {
        $schedule = Schedule::findOrFail($id);
        $admins = Admin::all();
        $users = User::all();
        return view('admin.schedules.edit', compact('schedule', 'admins', 'users'));
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
            'adminId' => 'nullable|exists:admins,id',
            'userId' => 'nullable|exists:users,id',
        ]);

        $schedule->update($request->only(['name', 'notes', 'repeat', 'day', 'startTime', 'endTime', 'adminId', 'userId']));

        return redirect()->route('admin.schedules')->with('success', 'Schedule updated successfully');
    }

    public function deleteSchedule($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        return redirect()->route('admin.schedules')->with('success', 'Schedule deleted successfully');
    }
}