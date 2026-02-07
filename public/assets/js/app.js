/**
 * URL Shortener - Main JavaScript
 * Vanilla JS with no dependencies
 */

// Dark mode toggle
const darkModeToggle = () => {
    const html = document.documentElement;
    const isDark = html.classList.contains('dark');

    if (isDark) {
        html.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    } else {
        html.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    }
};

// Initialize dark mode from localStorage
const initDarkMode = () => {
    const theme = localStorage.getItem('theme');
    if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    }
};

// Shorten URL function
const shortenUrl = async () => {
    const urlInput = document.getElementById('urlInput');
    const resultDiv = document.getElementById('result');
    const loadingDiv = document.getElementById('loading');
    const errorDiv = document.getElementById('error');

    const url = urlInput.value.trim();

    if (!url) {
        showError('Please enter a URL');
        return;
    }

    // Show loading
    loadingDiv.classList.remove('hidden');
    resultDiv.classList.add('hidden');
    errorDiv.classList.add('hidden');

    try {
        const response = await fetch('/url-shortener/public/api/shorten', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ url })
        });

        const data = await response.json();

        loadingDiv.classList.add('hidden');

        if (data.success) {
            document.getElementById('shortUrl').textContent = data.short_url;
            document.getElementById('shortUrl').href = data.short_url;
            resultDiv.classList.remove('hidden');
            urlInput.value = '';  // Clear input
            loadUrlList();  // Refresh the list
        } else {
            showError(data.error || 'Failed to shorten URL');
        }
    } catch (error) {
        loadingDiv.classList.add('hidden');
        showError('Network error. Please try again.');
        console.error('Error:', error);
    }
};

// Copy to clipboard
const copyToClipboard = async (text) => {
    try {
        await navigator.clipboard.writeText(text);
        showNotification('Copied to clipboard!');
    } catch (error) {
        // Fallback for older browsers
        const input = document.createElement('input');
        input.value = text;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        showNotification('Copied to clipboard!');
    }
};

// Show notification
const showNotification = (message) => {
    const notification = document.createElement('div');
    notification.className = 'toast-success fixed top-6 right-6 z-50';
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
};

// Show error
const showError = (message) => {
    const errorDiv = document.getElementById('error');
    errorDiv.textContent = message;
    errorDiv.classList.remove('hidden');
};

// Load URL list
const loadUrlList = async () => {
    try {
        const response = await fetch('/url-shortener/public/api/urls');
        const data = await response.json();

        const tbody = document.getElementById('urlTableBody');
        tbody.innerHTML = '';

        if (data.urls && data.urls.length > 0) {
            data.urls.forEach(url => {
                const row = createUrlRow(url);
                tbody.appendChild(row);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center py-8 text-gray-500 dark:text-gray-400">No URLs yet. Create your first short link above!</td></tr>';
        }
    } catch (error) {
        console.error('Error loading URLs:', error);
    }
};

// Create URL row
const createUrlRow = (url) => {
    const tr = document.createElement('tr');
    tr.className = 'transition-all duration-300';

    const baseUrl = window.location.origin + '/url-shortener/public/';
    const shortUrl = baseUrl + url.short_code;
    const createdDate = new Date(url.created_at).toLocaleDateString();

    tr.innerHTML = `
        <td class="px-8 py-5">
            <div class="flex items-center gap-3">
                <a href="${shortUrl}" target="_blank" class="text-neon-cyan hover:text-neon-lime font-mono font-semibold text-base transition-colors">
                    ${url.short_code}
                </a>
                <button onclick="copyToClipboard('${shortUrl}')" class="opacity-50 hover:opacity-100 transition-opacity">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </button>
            </div>
        </td>
        <td class="px-8 py-5">
            <div class="max-w-md truncate text-sm" style="color: #8b95a8;" title="${url.original_url}">
                ${url.original_url}
            </div>
        </td>
        <td class="px-8 py-5">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold" style="background: rgba(0, 245, 255, 0.15); color: #00f5ff; border: 1px solid rgba(0, 245, 255, 0.3);">
                ${url.click_count || 0} clicks
            </span>
        </td>
        <td class="px-8 py-5 text-sm" style="color: #8b95a8;">
            ${createdDate}
        </td>
    `;

    return tr;
};

// Load stats
const loadStats = async () => {
    try {
        const response = await fetch('/url-shortener/public/api/stats');
        const data = await response.json();

        document.getElementById('totalUrls').textContent = data.total_urls || 0;
        document.getElementById('totalClicks').textContent = data.total_clicks || 0;
        document.getElementById('clicksToday').textContent = data.clicks_today || 0;
    } catch (error) {
        console.error('Error loading stats:', error);
    }
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    initDarkMode();
    loadUrlList();
    loadStats();

    // Refresh stats every 30 seconds
    setInterval(() => {
        loadStats();
        loadUrlList();
    }, 30000);
});

// Handle Enter key in input
const handleKeyPress = (event) => {
    if (event.key === 'Enter') {
        shortenUrl();
    }
};
