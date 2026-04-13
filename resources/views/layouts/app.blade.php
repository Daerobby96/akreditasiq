<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- PWA Meta Tags -->
        <meta name="theme-color" content="#8b5cf6">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="Akreditasi Cerdas">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="msapplication-TileColor" content="#8b5cf6">
        <meta name="msapplication-config" content="/browserconfig.xml">

        <!-- Favicon and Icons -->
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="manifest" href="/manifest.json">

        <title>{{ config('app.name', 'Akreditasi Cerdas') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Preload critical resources -->
        <link rel="dns-prefetch" href="//fonts.bunny.net">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>
    <body class="font-sans antialiased {{ request()->is('/') ? 'bg-gradient-to-br from-indigo-50 via-white to-purple-50' : 'bg-slate-50 dark:bg-slate-950' }}">

        <!-- Mobile Network Status Indicator -->
        <div id="network-status"
             class="fixed top-0 left-0 right-0 z-50 bg-yellow-500 text-yellow-900 px-4 py-2 text-center text-sm font-medium transform transition-transform duration-300 -translate-y-full"
             style="display: none;">
            <div class="flex items-center justify-center space-x-2">
                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>Menyinkronkan data offline...</span>
            </div>
        </div>

        <div class="min-h-screen {{ request()->is('/') ? '' : 'dark:bg-slate-950' }}">
            <livewire:layout.navigation />

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <div x-data="{ 
            show: false, 
            message: '', 
            type: 'info' 
        }"
        x-on:notify.window="
            show = true; 
            message = $event.detail.message; 
            type = $event.detail.type || 'info';
            setTimeout(() => show = false, 3000);
        "
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2"
        class="fixed bottom-5 right-5 z-[100]"
        style="display: none;"
        >
            <div :class="{
                'bg-emerald-500': type === 'success',
                'bg-rose-500': type === 'error',
                'bg-indigo-500': type === 'info'
            }" class="px-6 py-3 rounded-2xl shadow-2xl text-white flex items-center space-x-3 backdrop-blur-md bg-opacity-90">
                <svg x-show="type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span x-text="message" class="text-sm font-bold"></span>
            </div>
        </div>

        <!-- PWA Service Worker Registration -->
        <script>
            // PWA Install Prompt
            let deferredPrompt;
            const installButton = document.querySelector('#install-button');

            window.addEventListener('beforeinstallprompt', (e) => {
                // Prevent the mini-infobar from appearing on mobile
                e.preventDefault();
                // Stash the event so it can be triggered later
                deferredPrompt = e;

                // Show install button if it exists
                if (installButton) {
                    installButton.style.display = 'block';
                }

                // Notify Livewire component
                if (window.livewire) {
                    window.livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).set('showInstallPrompt', true);
                }
            });

            window.addEventListener('appinstalled', (evt) => {
                console.log('PWA was installed successfully');
                deferredPrompt = null;

                // Hide install button
                if (installButton) {
                    installButton.style.display = 'none';
                }
            });

            // Register service worker
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('/sw.js')
                        .then(function(registration) {
                            console.log('[SW] Registered successfully:', registration.scope);

                            // Handle updates
                            registration.addEventListener('updatefound', function() {
                                const newWorker = registration.installing;
                                newWorker.addEventListener('statechange', function() {
                                    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                        // New version available
                                        showUpdateNotification();
                                    }
                                });
                            });
                        })
                        .catch(function(error) {
                            console.log('[SW] Registration failed:', error);
                        });
                });
            }

            // Network status monitoring
            let isOnline = navigator.onLine;

            window.addEventListener('online', function() {
                isOnline = true;
                hideNetworkStatus();
                showSyncNotification();

                // Trigger background sync
                if ('serviceWorker' in navigator && 'sync' in window.ServiceWorkerRegistration.prototype) {
                    navigator.serviceWorker.ready.then(function(registration) {
                        registration.sync.register('background-sync-documents');
                        registration.sync.register('background-sync-comments');
                    });
                }
            });

            window.addEventListener('offline', function() {
                isOnline = false;
                showNetworkStatus();
            });

            function showNetworkStatus() {
                const statusEl = document.getElementById('network-status');
                statusEl.style.display = 'block';
                setTimeout(() => {
                    statusEl.style.transform = 'translateY(0)';
                }, 100);
            }

            function hideNetworkStatus() {
                const statusEl = document.getElementById('network-status');
                statusEl.style.transform = 'translateY(-100%)';
                setTimeout(() => {
                    statusEl.style.display = 'none';
                }, 300);
            }

            function showSyncNotification() {
                // Show sync success notification
                const event = new CustomEvent('notify', {
                    detail: {
                        message: 'Data berhasil disinkronkan!',
                        type: 'success'
                    }
                });
                window.dispatchEvent(event);
            }

            function showUpdateNotification() {
                // Show PWA update notification
                const event = new CustomEvent('notify', {
                    detail: {
                        message: 'Versi baru tersedia! Refresh untuk update.',
                        type: 'info'
                    }
                });
                window.dispatchEvent(event);
            }

            // Mobile-specific enhancements
            if ('standalone' in window.navigator && window.navigator.standalone) {
                // Running in iOS PWA mode
                document.body.classList.add('ios-pwa');
            }

            // Handle mobile viewport height issues
            function setVH() {
                let vh = window.innerHeight * 0.01;
                document.documentElement.style.setProperty('--vh', `${vh}px`);
            }

            setVH();
            window.addEventListener('resize', setVH);
            window.addEventListener('orientationchange', setVH);

            // Touch gesture handling for mobile
            let touchStartY = 0;
            document.addEventListener('touchstart', function(e) {
                touchStartY = e.touches[0].clientY;
            });

            document.addEventListener('touchmove', function(e) {
                const touchY = e.touches[0].clientY;
                const diff = touchStartY - touchY;

                // Prevent overscroll on mobile
                if ((window.scrollY === 0 && diff < 0) || (window.innerHeight + window.scrollY >= document.body.offsetHeight && diff > 0)) {
                    e.preventDefault();
                }
            }, { passive: false });

            // Handle back button for PWA
            window.addEventListener('popstate', function(event) {
                // Custom back button handling if needed
            });
        </script>
        <!-- Global AI Assistant -->
        <livewire:chat-assistant />

    </body>
</html>
