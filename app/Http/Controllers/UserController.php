<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Group;
use App\Models\Project;
use App\Models\Task;
use App\Models\Quote;
use App\Http\Resources\UserResource;
use App\Http\Resources\GroupResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\TaskResource;
use App\Http\Resources\QuoteResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // 🔹 Mendapatkan semua user
    public function getUsers()
    {
        return UserResource::collection(User::all());
    }

    // 🔹 Mendapatkan semua grup berdasarkan userId
    public function getGroups($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $groups = $user->groups;
        return GroupResource::collection($groups);
    }

    // 🔹 Mendapatkan semua proyek berdasarkan userId
    public function getProjectsByUser($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $groups = $user->groups;
        $projects = collect();

        foreach ($groups as $group) {
            $projects = $projects->merge($group->projects);
        }

        return ProjectResource::collection($projects);
    }

    // 🔹 Mendapatkan semua quotes berdasarkan userId
    public function getQuotes($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $quotes = $user->quotes;
        return QuoteResource::collection($quotes);
    }

    // 🔹 Menambahkan quote
    public function addQuote(Request $request, $userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $quote = new Quote($request->all());
        $user->quotes()->save($quote);

        return new QuoteResource($quote);
    }

    // 🔹 Update quote
    public function updateQuote(Request $request, $userId, $quoteId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $quote = $user->quotes()->find($quoteId);
        if (!$quote) {
            return response()->json(['message' => 'Quote not found'], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'sometimes|required|string|max:500',
            'author' => 'sometimes|required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $quote->update($request->all());
        return new QuoteResource($quote);
    }

    // 🔹 Menghapus quote
    public function deleteQuote($userId, $quoteId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $quote = $user->quotes()->find($quoteId);
        if (!$quote) {
            return response()->json(['message' => 'Quote not found'], Response::HTTP_NOT_FOUND);
        }

        $quote->delete();
        return response()->json(['message' => 'Quote deleted'], Response::HTTP_OK);
    }

    // 🔹 Mendapatkan semua proyek berdasarkan groupId
    public function getProjectsByGroup($userId, $groupId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $group = $user->groups()->find($groupId);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], Response::HTTP_NOT_FOUND);
        }

        $projects = $group->projects;
        return ProjectResource::collection($projects);
    }

    // 🔹 Mendapatkan semua tugas berdasarkan projectId
    public function getTasks($userId, $groupId, $projectId)
    {
        $project = Project::find($projectId);

        if (!$project) {
            return response()->json(['message' => 'Project not found'], Response::HTTP_NOT_FOUND);
        }

        Log::info('Project Group ID: ' . $project->group->id);
        Log::info('Project ID: ' . $groupId);
        Log::info('Project User ID: ' . $userId);
        Log::info('Project Group User ID: ' . $project->group->user->id);

        if ($project->group->id != $groupId || $project->group->user->id != $userId) {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        $tasks = $project->tasks;
        return TaskResource::collection($tasks);
    }

    // 🔹 Membuat user baru
    public function createUser(Request $request)
    {
        $user = User::create($request->all());
        return new UserResource($user);
    }

    // 🔹 Menghapus user berdasarkan ID
    public function deleteUser($userId)
    {
        $user = User::find($userId);
        if ($user) {
            $user->delete();
            return response()->json(['message' => 'User deleted'], Response::HTTP_OK);
        }
        return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
    }

    // 🔹 Menambahkan Group ke User berdasarkan userId
    public function addGroupToUser(Request $request, $userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $group = new Group($request->all());
        $user->groups()->save($group);

        return new GroupResource($group);
    }

    // 🔹 Menambahkan Project ke Group
    public function addProjectToGroup(Request $request, $userId, $groupId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $group = $user->groups()->find($groupId);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], Response::HTTP_NOT_FOUND);
        }

        $project = new Project($request->all());
        $group->projects()->save($project);

        return new ProjectResource($project);
    }

    // 🔹 Menambahkan Task ke Project
    public function addTaskToProject(Request $request, $userId, $groupId, $projectId)
    {
        $project = Project::with('group.user')->find($projectId);

        Log::info('Project and group information:', [
            'project' => $project,
            'group' => $project->group,
            'user' => $project->group->user,
        ]);

        if (!$project || !$project->group || $project->group->id !== (int) $groupId || !$project->group->user || $project->group->user->id !== (int) $userId) {
            Log::info('Forbidden Access:', [
                'projectGroupId' => $project->group->id ?? 'null',
                'projectUserId' => $project->group->user->id ?? 'null',
                'groupId' => $groupId,
                'userId' => $userId,
            ]);
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        $task = new Task($request->all());
        $project->tasks()->save($task);

        return new TaskResource($task);
    }

    // 🔹 Menghapus Group
    public function deleteGroup($userId, $groupId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $group = $user->groups()->find($groupId);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], Response::HTTP_NOT_FOUND);
        }

        $group->delete();
        return response()->json(['message' => 'Group deleted'], Response::HTTP_OK);
    }

    // 🔹 Menghapus Project
    public function deleteProject($userId, $groupId, $projectId)
    {
        $project = Project::find($projectId);
        if (!$project || $project->group->id != $groupId || $project->group->user->id != $userId) {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        $project->delete();
        return response()->json(['message' => 'Project deleted'], Response::HTTP_OK);
    }

    // 🔹 Menghapus Task
    public function deleteTask($userId, $groupId, $projectId, $taskId)
    {
        $task = Task::find($taskId);
        if (!$task || $task->project->id != $projectId || $task->project->group->id != $groupId || $task->project->group->user->id != $userId) {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        $task->delete();
        return response()->json(['message' => 'Task deleted'], Response::HTTP_OK);
    }

    // 🔹 Update Task
    public function updateTask(Request $request, $userId, $groupId, $projectId, $taskId)
    {
        $task = Task::find($taskId);
        if (!$task || $task->project->id != $projectId || $task->project->group->id != $groupId || $task->project->group->user->id != $userId) {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        $task->update($request->all());
        return new TaskResource($task);
    }

    // 🔹 Update Project
    public function updateProject(Request $request, $userId, $groupId, $projectId)
    {
        $project = Project::find($projectId);
        if (!$project || $project->group->id != $groupId || $project->group->user->id != $userId) {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        $project->update($request->all());
        return new ProjectResource($project);
    }

    // 🔹 Register user baru
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'profile_picture' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ];

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $data['profile_picture'] = $path;
        }

        $user = User::create($data);

        return new UserResource($user);
    }

    // 🔹 Login user
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message-Auth' => 'Invalid credentials'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return new UserResource($user);
    }

    // 🔹 Update profile picture
    public function updateProfilePicture(Request $request, $userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Delete old profile picture if it exists
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        // Store new profile picture
        // Periksa apakah ada file yang diupload
        if ($request->hasFile('profile_picture')) {
            // Generate filename unik berdasarkan waktu
            $filename = time() . '.' . $request->profile_picture->extension();

            // Simpan gambar di storage/public
            $path = $request->profile_picture->storeAs('public/profile_pictures', $filename);

            // Simpan nama file gambar ke database
            $user->profile_picture = $filename;
            $user->save();

            // Mengembalikan response dengan URL gambar yang benar
            return response()->json([
                'message' => 'Profile picture updated',
                'profile_picture' => asset('profile_pictures/' . $filename)
            ]);
        }

        return response()->json([
            'message' => 'No image uploaded'
        ], 400); // 400 berarti ada masalah dengan request, bisa jadi tidak ada gambar yang diupload

        return new UserResource($user);
    }
}
