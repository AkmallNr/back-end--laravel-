<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            height: 100vh;
            background-color: #f8f9fa;
            padding: 20px 0;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .sidebar .nav-link {
            color: #333;
            padding: 15px 25px;
            margin: 5px 15px;
            border-radius: 10px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            background-color: #e9ecef;
            color: #333;
        }
        .sidebar .nav-link.active {
            background-color: #f4a261;
            color: white;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
        }
        .main-content {
            padding: 30px;
        }
        .header {
            background: white;
            padding: 20px 30px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: between;
            align-items: center;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2a9d8f;
        }
        .user-info {
            color: #333;
            font-weight: 500;
        }
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .btn-edit {
            background-color: #2196f3;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            margin-right: 5px;
        }
        .btn-delete {
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
        }
        .btn-edit:hover, .btn-delete:hover {
            opacity: 0.8;
        }
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #2a9d8f, #264653);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        .logout-btn {
            color: #dc3545;
            margin-top: auto;
        }
        .logout-btn:hover {
            background-color: #f8d7da;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar d-flex flex-column">
                    <div class="d-flex align-items-center px-3 mb-4">
                        <div class="logo-icon">
                            <img src="{{ asset('images/logo.png') }}" alt="logo" style="height: 40px;">
                        </div>
                    </div>
                    
                    <nav class="nav flex-column flex-grow-1">
                        <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" href="{{ route('admin.users') }}">
                            <i class="fas fa-users"></i>
                            List User
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.groups*') ? 'active' : '' }}" href="{{ route('admin.groups') }}">
                            <i class="fas fa-layer-group"></i>
                            List Group
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.tasks*') ? 'active' : '' }}" href="{{ route('admin.tasks') }}">
                            <i class="fas fa-tasks"></i>
                            List Task
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.schedules*') ? 'active' : '' }}" href="{{ route('admin.schedules') }}">
                            <i class="fas fa-calendar-alt"></i>
                            List Schedule
                        </a>
                    </nav>
                    
                    <div class="px-3">
                        <form method="POST" action="{{ route('home') }}">
                            @csrf
                            <button type="submit" class="nav-link logout-btn w-100 text-start border-0 bg-transparent">
                                <i class="fas fa-sign-out-alt"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 px-0">
                <div class="header d-flex justify-content-between align-items-center">
                    <div class="logo">
                        Admin Dashboard
                    </div>
                    <div class="user-info">
                        Hello! {{ Auth::user()->name ?? 'Admin' }}
                    </div>
                </div>
                
                <div class="main-content">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Confirm delete
        function confirmDelete(id, type) {
            if (confirm('Are you sure you want to delete this ' + type + '?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
</body>
</html>
