@extends('Layouts.apps')

@section('title', 'Features - Schedo')

@section('content')
<div class="bg-white py-16 sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-900">Fitur-Fitur Schedo</h1>
            <p class="mt-4 text-lg text-gray-600">Temukan semua fitur yang akan membantu meningkatkan produktivitas Anda</p>
        </div>

        <div class="mt-16 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Detailed Feature Cards -->
            <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200">
                <div class="w-12 h-12 feature-icon rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Smart To-Do List</h3>
                <p class="text-gray-600">Kelola tugas dengan mudah, set prioritas, dan pantau progress Anda secara real-time.</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200">
                <div class="w-12 h-12 feature-icon rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Advanced Scheduling</h3>
                <p class="text-gray-600">Atur jadwal dengan calendar yang intuitif dan dapatkan reminder otomatis.</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200">
                <div class="w-12 h-12 feature-icon rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Pomodoro Timer</h3>
                <p class="text-gray-600">Tingkatkan fokus dengan teknik pomodoro yang terbukti efektif.</p>
            </div>
        </div>
    </div>
</div>
@endsection