<div x-data="{ 
        darkMode: @entangle('darkMode'),
        init() {
            // Check for saved preference or default to light mode
            this.darkMode = localStorage.getItem('dark-mode') === 'true' || 
                           (localStorage.getItem('dark-mode') === null && window.matchMedia('(prefers-color-scheme: dark)').matches);
            this.updateTheme();
        },
        updateTheme() {
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('dark-mode', 'true');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('dark-mode', 'false');
            }
        }
    }" 
    x-init="init()" 
    @dark-mode-toggled.window="darkMode = $event.detail.darkMode; updateTheme()">
    
    <button @click="$wire.toggleDarkMode(); updateTheme()" 
            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
        <span x-show="!darkMode" class="flex items-center">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
            </svg>
            Dark
        </span>
        <span x-show="darkMode" class="flex items-center">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
            </svg>
            Light
        </span>
    </button>
</div>