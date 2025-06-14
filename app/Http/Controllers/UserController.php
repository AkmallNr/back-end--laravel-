<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Group;
use App\Models\Project;
use App\Models\Task;
use App\Models\Quote;
use App\Models\Attachment;
use App\Http\Resources\UserResource;
use App\Http\Resources\GroupResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\TaskResource;
use App\Http\Resources\QuoteResource;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Kreait\Firebase\Auth as FirebaseAuth;


class UserController extends Controller
{
    protected $firebaseAuth;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(config('firebase.credentials'));
        $this->firebaseAuth = $factory->createAuth();
    }

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

        // Ambil semua proyek yang terkait langsung dengan user (groupId bisa null atau tidak ada)
        $directProjects = Project::where('userId', $userId)->get();

        // Ambil proyek dari grup yang terkait dengan user
        $groups = $user->groups;
        $groupProjects = collect();

        foreach ($groups as $group) {
            $groupProjects = $groupProjects->merge($group->projects);
        }

        // Gabungkan proyek langsung dan proyek dari grup, hapus duplikat jika ada
        $allProjects = $directProjects->merge($groupProjects)->unique('id');

        // Kembalikan koleksi proyek dalam format resource
        return ProjectResource::collection($allProjects);
    }

    public function getTaskByUser($userId)
    {
        // Cari user berdasarkan userId dengan eager loading relasi groups, projects, dan tasks
        $user = User::with('groups.projects.tasks')->find($userId);

        // Cek apakah user ditemukan
        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // Ambil semua tasks dari proyek-proyek di semua grup
        $tasks = $user->groups->flatMap(function ($group) {
            return $group->projects->flatMap(function ($project) {
                return $project->tasks;
            });
        });

        // Kembalikan koleksi tasks sebagai TaskResource
        return TaskResource::collection($tasks);
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

    public function getSchedule($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $schedule = $user->schedule;
        return ScheduleResource::collection($schedule);
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


    public function addSchedule(Request $request, $userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $schedule = new Schedule($request->all());
        $user->schedule()->save($schedule);

        return new ScheduleResource($schedule);
    }

    // 🔹 Memperbarui Group berdasarkan userId dan groupId
    public function updateGroup(Request $request, $userId, $groupId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $group = $user->groups()->find($groupId);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'icon' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $group->update($request->only(['name', 'icon']));
        return new GroupResource($group);
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

    public function updateSchedule(Request $request, $userId, $scheduleId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $schedule = $user->schedule()->find($scheduleId);
        if (!$schedule) {
            return response()->json(['message' => 'Quote not found'], Response::HTTP_NOT_FOUND);
        }


        $schedule->update($request->all());
        return new ScheduleResource($schedule);
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

    public function deleteSchedule($userId, $scheduleId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $schedule = $user->schedule()->find($scheduleId);
        if (!$schedule) {
            return response()->json(['message' => 'Quote not found'], Response::HTTP_NOT_FOUND);
        }

        $schedule->delete();
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
        Log::info('Group ID: ' . $groupId);
        Log::info('User ID: ' . $userId);
        Log::info('Project Group User ID: ' . $project->group->user->id);

        if ($project->group->id != $groupId || $project->group->user->id != $userId) {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        $tasks = $project->tasks()->with('attachments')->get();
        return response()->json([
            'data' => TaskResource::collection($tasks)
        ], Response::HTTP_OK);
    }

    public function getTaskById($userId, $groupId, $projectId, $taskId)
    {
        $project = Project::find($projectId);

        if (!$project) {
            return response()->json(['message' => 'Project not found'], Response::HTTP_NOT_FOUND);
        }

        Log::info('Project Group ID: ' . $project->group->id);
        Log::info('Group ID: ' . $groupId);
        Log::info('User ID: ' . $userId);
        Log::info('Project Group User ID: ' . $project->group->user->id);

        if ($project->group->id != $groupId || $project->group->user->id != $userId) {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        try {
            $task = Task::where('id', $taskId)
                ->where('projectId', $projectId) // Perhatikan 'project_id', bukan 'projectId'
                ->with('attachments')
                ->firstOrFail();

            return response()->json([
                'data' => [new TaskResource($task)] // Wrap dalam array untuk konsistensi
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error("Gagal memuat tugas: {$e->getMessage()}", ['exception' => $e]);
            return response()->json(['error' => 'Gagal memuat tugas'], Response::HTTP_NOT_FOUND);
        }
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
    public function addProjectToGroup(Request $request, $userId, $groupId = null)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'startDate' => 'nullable|date',
            'endDate' => 'nullable|date|after_or_equal:startDate',
        ]);

        // Jika groupId diberikan, pastikan grup ada dan terkait dengan user
        $group = null;
        if ($groupId) {
            $group = $user->groups()->find($groupId);
            if (!$group) {
                return response()->json(['message' => 'Group not found or not associated with user'], Response::HTTP_NOT_FOUND);
            }
        }

        // Buat proyek baru
        $project = new Project($validated);
        $project->userId = $userId; // Set userId
        $project->groupId = $groupId; // Set groupId (bisa null)

        // Simpan proyek
        $project->save();

        return new ProjectResource($project);
    }

    public function addProjectToUser(Request $request, $userId)
    {
        return $this->addProjectToGroup($request, $userId, null);
    }

    // 🔹 Menambahkan Task ke Project
    public function addTaskToProject(Request $request, $userId, $groupId, $projectId)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'nullable|date',
            'reminder' => 'nullable|date',
            'priority' => 'required|in:Rendah,Normal,Tinggi',
            'attachment' => 'nullable|array',
            'attachment.*' => 'string', // URL file atau link
            'status' => 'boolean',
            'quote_id' => 'nullable|exists:quotes,id',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());
            return response()->json([
                'message' => $validator->errors()->first(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Verifikasi project, group, dan user
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

        // Buat task
        $task = new Task([
            'projectId' => $projectId,
            'name' => $request->name,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'reminder' => $request->reminder,
            'priority' => $request->priority,
            'status' => $request->status ?? false,
            'quote_id' => $request->quote_id,
        ]);
        $project->tasks()->save($task);

        // Simpan attachment jika ada
        if ($request->has('attachment') && is_array($request->attachment)) {
            foreach ($request->attachment as $attachmentUrl) {
                Attachment::create([
                    'taskId' => $task->id,
                    'file_name' => basename($attachmentUrl), // Ambil nama file dari URL
                    'file_url' => $attachmentUrl,
                ]);
            }
        }

        // Kembalikan task dengan relasi attachments
        return new TaskResource($task->load('attachments'));
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
    public function updateTask($userId, $groupId, $projectId, $taskId, \Illuminate\Http\Request $request)
    {
        $project = Project::find($projectId);

        if (!$project) {
            return response()->json(['message' => 'Project not found'], Response::HTTP_NOT_FOUND);
        }

        if ($project->group->id != $groupId || $project->group->user->id != $userId) {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        try {
            $task = Task::where('id', $taskId)
                ->where('projectId', $projectId)
                ->firstOrFail();

            $task->update([
                'name' => $request->name,
                'description' => $request->description,
                'deadline' => $request->deadline,
                'reminder' => $request->reminder,
                'priority' => $request->priority,
                'status' => $request->status ?? $task->status,
                'quote_id' => $request->quoteId,
                // completed_at diatur otomatis oleh model
            ]);

            // Handle attachments if needed
            if ($request->has('attachment')) {
                foreach ($request->attachment as $url) {
                    $task->attachments()->create([
                        'file_url' => $url,
                        'file_name' => basename($url),
                    ]);
                }
            }

            return new TaskResource($task->load('attachments'));
        } catch (\Exception $e) {
            Log::error("Gagal memperbarui tugas: {$e->getMessage()}", ['exception' => $e]);
            return response()->json(['error' => 'Gagal memperbarui tugas'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // 🔹 Update Project
    public function updateProject(Request $request, $userId, $groupId, $projectId)
    {
        $project = Project::find($projectId);
        // if (!$project || $project->group->id != $groupId || $project->group->user->id != $userId) {
        //     return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        // }

        if ($request->has('groupId')) {
            $newGroupId = $request->input('groupId');
            $newGroup = \App\Models\Group::find($newGroupId);
            if (!$newGroup || $newGroup->user->id != $userId) {
                Log::info("New group validation failed: newGroupId=$newGroupId, userId=$userId, newGroupUserId=" . ($newGroup->user->id ?? 'null'));
                return response()->json(['message' => 'Forbidden: You do not have access to the new group'], Response::HTTP_FORBIDDEN);
            }
            $project->groupId = $newGroupId;
        }

        $project->update($request->only(['name', 'description', 'startDate', 'endDate']));
        $project->save();

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
                'message' => 'Invalid credentials'
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
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
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
        if ($request->hasFile('profile_picture')) {
            $filename = time() . '.' . $request->file('profile_picture')->extension();
            $path = $request->file('profile_picture')->storeAs('profile_pictures', $filename, 'public');

            // Simpan nama file ke database
            $user->profile_picture = $filename;
            $user->save();

            // Kembalikan respons dengan URL yang benar
            return response()->json([
                'message' => 'Profile picture updated',
                'profile_picture' => asset('storage/profile_pictures/' . $filename)
            ], Response::HTTP_OK);
        }

        return response()->json([
            'message' => 'No image uploaded'
        ], Response::HTTP_BAD_REQUEST);
    }

public function loginWithGoogle(Request $request)
{
    // Validasi input
    $validator = Validator::make($request->all(), [
        'token' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    $firebaseToken = $request->input('token');

    try {
        // Verifikasi token Firebase
        $verifiedIdToken = $this->firebaseAuth->verifyIdToken($firebaseToken);
        $firebaseUser = $verifiedIdToken->claims()->all();

        // Ambil data pengguna dari token
        $uid = $firebaseUser['sub'];
        $email = $firebaseUser['email'];
        $name = $firebaseUser['name'] ?? 'Unknown';

        // Log data dari Firebase
        Log::info('Firebase user data:', [
            'uid' => $uid,
            'email' => $email,
            'name' => $name,
        ]);

        // Cari pengguna berdasarkan google_id
        $existingUser = User::where('google_id', $uid)->first();

        if ($existingUser) {
            // Jika pengguna sudah ada, perbarui data kecuali profile_picture
            $updateData = [
                'name' => $name,
                'email' => $email,
            ];

            $existingUser->update($updateData);
            $user = $existingUser;
        } else {
            // Jika pengguna belum ada, buat pengguna baru dengan profile_picture null
            $user = User::create([
                'google_id' => $uid,
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(\Illuminate\Support\Str::random(16)),
                'profile_picture' => null, // Set profile_picture ke null saat pertama kali membuat akun
            ]);
        }

        // Log data pengguna setelah disimpan
        Log::info('User data after save:', [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'google_id' => $user->google_id,
            'profile_picture' => $user->profile_picture,
        ]);

        // Kembalikan respons dengan data pengguna
        return new UserResource($user);
    } catch (\Exception $e) {
        Log::error('Firebase login error: ' . $e->getMessage());
        return response()->json([
            'message' => 'Login gagal: ' . $e->getMessage(),
        ], Response::HTTP_UNAUTHORIZED);
    }
}

    public function upload(Request $request)
    {
        try {
            $request->validate([
                'file' => 'nullable|file|mimes:jpeg,png,pdf|max:10240', // Maks 10MB
                'link' => 'nullable|url',
            ]);

            $response = [];

            // Jika ada file yang diunggah
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = Str::random(10) . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('uploads', $fileName, 'public'); // Simpan di storage/public/uploads
                $fileUrl = Storage::url($path);

                $response['file_url'] = url($fileUrl);
                $response['file_name'] = $file->getClientOriginalName();
            }

            // Jika ada link
            if ($request->has('link')) {
                $response['link'] = $request->input('link');
            }

            return response()->json([
                'success' => true,
                'data' => $response,
                'message' => 'File atau link berhasil diunggah'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTaskByProject($userId, $groupId, $projectId)
    {
        // Validasi bahwa project ada dan sesuai dengan user serta group
        $project = Project::where('id', $projectId)
            ->where('groupId', $groupId)
            ->whereHas('group', function ($query) use ($userId) {
                $query->where('userId', $userId);
            })
            ->firstOrFail();

        // Ambil semua task yang terkait dengan project, dengan relasi attachments
        $tasks = Task::with('attachments')
            ->where('projectId', $projectId)
            ->get();

        // Kembalikan response dalam bentuk TaskResource
        return TaskResource::collection($tasks);
    }

    public function getWeeklyCompletedTasks($userId)
    {
        try {
            // Tentukan awal dan akhir minggu (Senin hingga Minggu)
            $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
            $endOfWeek = Carbon::now()->endOfWeek(Carbon::SUNDAY);

            // Ambil tugas selesai untuk user dalam rentang mingguan
            $tasks = Task::whereHas('project.group.user', function ($query) use ($userId) {
                $query->where('id', $userId);
            })
                ->where('status', true)
                ->whereBetween('completed_at', [$startOfWeek, $endOfWeek])
                ->get();

            // Inisialisasi hasil per hari
            $result = [
                'Monday' => 0,
                'Tuesday' => 0,
                'Wednesday' => 0,
                'Thursday' => 0,
                'Friday' => 0,
                'Saturday' => 0,
                'Sunday' => 0,
            ];

            // Hitung tugas per hari
            foreach ($tasks as $task) {
                $day = Carbon::parse($task->completed_at)->dayName;
                if (isset($result[$day])) {
                    $result[$day]++;
                }
            }

            // Format rentang tanggal
            $dateRange = $startOfWeek->format('d') . ' - ' . $endOfWeek->format('d M');

            return response()->json([
                'data' => [
                    'date_range' => $dateRange,
                    'tasks' => $result,
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error("Gagal menghitung tugas selesai mingguan: {$e->getMessage()}", ['exception' => $e]);
            return response()->json(['error' => 'Gagal menghitung tugas selesai'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getSchedulesByDateRange($userId, Request $request)
    {
        try {
            // Log data yang diterima
            Log::debug("Received request: userId=$userId, startDate={$request->input('startDate')}, endDate={$request->input('endDate')}");

            // Validasi input
            $validated = $request->validate([
                'startDate' => 'required|date_format:d/m/Y',
                'endDate' => 'required|date_format:d/m/Y|after_or_equal:startDate',
            ]);

            // Parse tanggal menggunakan createFromFormat
            $startDate = Carbon::createFromFormat('d/m/Y', $validated['startDate'])->startOfDay();
            $endDate = Carbon::createFromFormat('d/m/Y', $validated['endDate'])->endOfDay();

            Log::debug("Fetching schedules for userId: $userId, startDate: $startDate, endDate: $endDate");

            // Query untuk mengambil jadwal
            $schedules = Schedule::where('userId', $userId)
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereRaw("TO_DATE(SUBSTRING(\"startTime\", 1, 10), 'DD/MM/YYYY') >= ?", [$startDate])
                        ->whereRaw("TO_DATE(SUBSTRING(\"endTime\", 1, 10), 'DD/MM/YYYY') <= ?", [$endDate]);
                })
                ->get();

            if ($schedules->isEmpty()) {
                return response()->json([
                    'message' => 'No schedules found for the given date range.',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'message' => 'Schedules retrieved successfully.',
                'data' => $schedules
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Validation failed: " . json_encode($e->errors()));
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error("Error fetching schedules: {$e->getMessage()}");
            return response()->json([
                'message' => 'An error occurred while fetching schedules.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}


