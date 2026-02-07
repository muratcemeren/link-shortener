<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            bg: '#0f172a',
                            card: '#1e293b'
                        }
                    }
                }
            }
        }
        // Auto dark mode
        if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>

<body class="bg-gray-50 dark:bg-dark-bg min-h-screen flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-9xl font-bold text-gray-900 dark:text-white mb-4">404</h1>
        <h2 class="text-3xl font-semibold text-gray-700 dark:text-gray-300 mb-4">Link Not Found</h2>
        <p class="text-gray-600 dark:text-gray-400 mb-8">
            The short link you're looking for doesn't exist or has been deleted.
        </p>
        <a href="/url-shortener/"
            class="inline-block px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-md">
            Go to Dashboard
        </a>
    </div>
</body>

</html>