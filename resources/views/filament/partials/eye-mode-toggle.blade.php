<div class="flex items-center gap-2 px-2">
    <button type="button" id="eye-mode-toggle" title="Ganti Mode Tampilan (Terang / Gelap / Eye Care)" class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition border border-gray-300 dark:border-gray-700 cursor-pointer shadow-sm">
        <span id="eye-mode-icon">☀️</span>
        <span id="eye-mode-label">Mode Terang</span>
    </button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('eye-mode-toggle');
        const icon = document.getElementById('eye-mode-icon');
        const label = document.getElementById('eye-mode-label');
        const html = document.documentElement;

        function updateUI(theme) {
            if (theme === 'dark') {
                html.classList.add('dark');
                html.setAttribute('data-theme', 'dark');
                if (icon) icon.textContent = '🌙';
                if (label) label.textContent = 'Mode Gelap';
                document.body.style.filter = 'none';
            } else if (theme === 'sepia') {
                html.classList.remove('dark');
                html.setAttribute('data-theme', 'sepia');
                if (icon) icon.textContent = '👓';
                if (label) label.textContent = 'Eye Care';
                document.body.style.filter = 'sepia(0.2) contrast(1.03) brightness(0.98)';
            } else {
                html.classList.remove('dark');
                html.setAttribute('data-theme', 'light');
                if (icon) icon.textContent = '☀️';
                if (label) label.textContent = 'Mode Terang';
                document.body.style.filter = 'none';
            }
        }

        let currentTheme = localStorage.getItem('sppg_theme') || 'light';
        updateUI(currentTheme);

        if (btn) {
            btn.addEventListener('click', function () {
                if (currentTheme === 'light') {
                    currentTheme = 'dark';
                } else if (currentTheme === 'dark') {
                    currentTheme = 'sepia';
                } else {
                    currentTheme = 'light';
                }
                localStorage.setItem('sppg_theme', currentTheme);
                updateUI(currentTheme);
            });
        }
    });
</script>
