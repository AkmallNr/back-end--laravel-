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
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/login')->with('success', 'Logout successful!');
    }

    public function listGroup()
    {
        $groups = Group::all();
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
            'belong_to' => 'required|exists:users,id',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $group->update([
            'name' => $request->name,
            'userId' => $request->belong_to, // Sesuaikan dengan kolom relasi di model Group
            'description' => $request->description,
            'startDate' => $request->start_date,
            'endDate' => $request->end_date,
        ]);

        return redirect()->route('admin.groups')->with('success', 'Group updated successfully');
    }

    public function deleteGroup($id)
    {
        $group = Group::findOrFail($id);

        if ($group->projects()->exists()) {
            return redirect()->route('admin.groups')->with('error', 'Cannot delete group with associated projects.');
        }

        $group->delete();

        return redirect()->route('admin.groups')->with('success', 'Group deleted successfully');
    }

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
            'belong_to' => 'required|exists:users,id',
            'description' => 'nullable|string',
            'deadline' => 'required|date',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|boolean',
            'completed_at' => 'nullable|date',
            'projectId' => 'nullable|exists:projects,id',
        ]);

        $task->update([
            'name' => $request->name,
            'userId' => $request->belong_to, // Sesuaikan dengan kolom relasi di model Task
            'description' => $request->description,
            'deadline' => $request->deadline,
            'priority' => $request->priority,
            'status' => $request->status,
            'completed_at' => $request->completed_at,
            'projectId' => $request->projectId,
        ]);

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
            'belong_to' => 'required|exists:users,id',
            'notes' => 'nullable|string',
            'startTime' => 'required|date_format:H:i',
            'endTime' => 'required|date_format:H:i|after:startTime',
        ]);

        $schedule->update([
            'name' => $request->name,
            'userId' => $request->belong_to, // Sesuaikan dengan kolom relasi di model Schedule
            'notes' => $request->notes,
            'startTime' => $request->startTime,
            'endTime' => $request->endTime,
        ]);

        return redirect()->route('admin.schedules')->with('success', 'Schedule updated successfully');
    }

    public function deleteSchedule($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        return redirect()->route('admin.schedules')->with('success', 'Schedule deleted successfully');
    }

    public function listUsers()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $admins = Admin::all(); // Jika diperlukan
        return view('admin.users.edit', compact('user', 'admins'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'google_id' => 'nullable|string',
            'profile_picture' => 'nullable|image|max:2048', // Maksimal 2MB
        ]);

        $data = $request->only(['name', 'email', 'google_id']);

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $data['profile_picture'] = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        $user->update($data);

        return redirect()->route('admin.users')->with('success', 'User updated successfully');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully');
    }
}