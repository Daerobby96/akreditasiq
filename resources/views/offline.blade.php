<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="theme-color" content="#8b5cf6">

    <title>Akreditasi Cerdas - Offline</title>

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            text-align: center;
            padding: 2rem;
            max-width: 400px;
        }

        .icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 2rem;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }

        h1 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        p {
            margin-bottom: 2rem;
            opacity: 0.9;
            line-height: 1.6;
        }

        .button {
            display: inline-block;
            background: white;
            color: #667eea;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.2s;
        }

        .button:hover {
            transform: translateY(-2px);
        }

        .features {
            margin-top: 2rem;
            text-align: left;
        }

        .feature {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .feature-icon {
            width: 20px;
            height: 20px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.5rem;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">📱</div>
        <h1>Koneksi Offline</h1>
        <p>Anda sedang offline. Beberapa fitur mungkin tidak tersedia, namun Anda masih dapat mengakses konten yang telah di-cache.</p>

        <a href="/" class="button">Kembali ke Dashboard</a>

        <div class="features">
            <h3 style="margin-bottom: 1rem; font-size: 1.1rem;">Fitur Offline:</h3>
            <div class="feature">
                <div class="feature-icon">📄</div>
                <span>Lihat dokumen yang telah di-cache</span>
            </div>
            <div class="feature">
                <div class="feature-icon">💬</div>
                <span>Baca komentar yang tersimpan</span>
            </div>
            <div class="feature">
                <div class="feature-icon">📊</div>
                <span>Akses data analitik tersimpan</span>
            </div>
            <div class="feature">
                <div class="feature-icon">🔄</div>
                <span>Data akan disinkronkan saat online</span>
            </div>
        </div>
    </div>

    <script>
        // Check network status and reload when back online
        window.addEventListener('online', function() {
            // Reload the page when connection is restored
            window.location.reload();
        });

        // Show a message when coming back online
        window.addEventListener('online', function() {
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.ready.then(function(registration) {
                    // Trigger background sync if supported
                    if ('sync' in registration) {
                        registration.sync.register('background-sync-documents');
                        registration.sync.register('background-sync-comments');
                    }
                });
            }
        });
    </script>
</body>
</html>