@extends('Layouts.apps')

@section('content')
<!-- Hero Section -->
<section class="relative bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto">
        <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
            <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                <div class="sm:text-center lg:text-left">
                    <h1 class="text-4xl tracking-tight font-bold text-gray-900 sm:text-5xl md:text-6xl">
                        <span class="block">Atur</span>
                        <span class="block text-orange-400">Tugas</span>
                        <span class="block">, 
                            <span class="text-orange-400">Jadwal</span>,
                        </span>
                        <span class="block">dan 
                            <span class="text-orange-400">Fokusmu</span>
                        </span>
                        <span class="block">dengan 
                            <span class="text-blue-500">Mudah</span>!
                        </span>
                    </h1>
                    <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                        Dapatkan kemudahan dalam mengatur aktivitas harian anda
                    </p>
                    <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                        <div class="rounded-md shadow">
                            <a href="#" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-orange-400 hover:bg-orange-500 md:py-4 md:text-lg md:px-10 transition-colors">
                                Download Now
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
        <div class="h-56 w-full sm:h-72 md:h-96 lg:w-full lg:h-full flex items-center justify-center">
            <!-- Phone Mockup -->
            <div class="relative">
                <img src="{{ asset('images/phone_mockup.png') }}" 
                     alt="Schedo App Mockup" 
                     class="w-80 h-auto max-w-full object-contain drop-shadow-2xl">
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-12">Features</h2>
        </div>

        <div class="mt-10">
            <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-3">
                <!-- To-Do List Feature -->
                <div class="text-center">
                    <div class="mx-auto w-16 h-16 feature-icon rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">To-Do List</h3>
                    <p class="mt-2 text-base text-gray-500">
                        Buat dan kelola daftar tugas dengan mudah. Tandai tugas yang sudah selesai dan tetap terorganisir dengan fitur yang intuitif.
                    </p>
                </div>

                <!-- Scheduling Feature -->
                <div class="text-center">
                    <div class="mx-auto w-16 h-16 feature-icon rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Scheduling</h3>
                    <p class="mt-2 text-base text-gray-500">
                        Atur jadwal harian dan mingguan dengan mudah. Dapatkan notifikasi untuk acara yang tersimpan sehingga tidak ada yang terlewat.
                    </p>
                </div>

                <!-- Pomodoro Feature -->
                <div class="text-center">
                    <div class="mx-auto w-16 h-16 feature-icon rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Pomodoro</h3>
                    <p class="mt-2 text-base text-gray-500">
                        Tingkatkan produktivitas dengan teknik pomodoro. Kelola waktu dengan timer yang akan membantu Anda tetap fokus dalam bekerja.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="bg-gray-50">
    <div class="max-w-2xl mx-auto text-center py-16 px-4 sm:py-20 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900">
            <span class="block">Siap untuk meningkatkan</span>
            <span class="block text-orange-400">produktivitas Anda?</span>
        </h2>
        <p class="mt-4 text-lg leading-6 text-gray-500">
            Download Schedo sekarang dan rasakan perbedaannya dalam mengatur hidup Anda.
        </p>
        <a href="#" class="mt-8 w-full inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-orange-400 hover:bg-orange-500 sm:w-auto transition-colors">
            Download Sekarang
        </a>
    </div>
</section>
@endsection