// Keyboard shortcuts for FreelanceFlow
document.addEventListener('DOMContentLoaded', function() {
    // Key mappings
    const shortcuts = {
        // Navigation shortcuts
        'g+d': () => window.location.href = '/dashboard',
        'g+p': () => window.location.href = '/projects', 
        'g+c': () => window.location.href = '/clients',
        'g+t': () => window.location.href = '/time-tracking',
        'g+i': () => window.location.href = '/invoices',
        'g+e': () => window.location.href = '/expenses',
        'g+r': () => window.location.href = '/reports',
        
        // Quick actions
        'n+p': () => navigateToCreate('/projects/create'),
        'n+c': () => navigateToCreate('/clients/create'),
        'n+i': () => navigateToCreate('/invoices/create'),
        'n+e': () => navigateToCreate('/expenses/create'),
        
        // Global actions
        '?': showKeyboardShortcuts,
        'escape': hideKeyboardShortcuts,
        '/': focusSearch,
        
        // Theme toggle
        'ctrl+shift+l': toggleDarkMode,
        
        // Time tracking
        'space': toggleTimer,
    };

    let pressedKeys = [];
    let lastKeyTime = 0;
    const keyTimeout = 2000; // 2 seconds between key combinations

    document.addEventListener('keydown', function(e) {
        // Don't trigger shortcuts when typing in inputs
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.isContentEditable) {
            return;
        }

        const currentTime = Date.now();
        
        // Reset if too much time has passed
        if (currentTime - lastKeyTime > keyTimeout) {
            pressedKeys = [];
        }
        
        lastKeyTime = currentTime;

        // Handle special keys
        let keyPressed = '';
        
        if (e.ctrlKey && e.shiftKey) {
            keyPressed = `ctrl+shift+${e.key.toLowerCase()}`;
        } else if (e.ctrlKey) {
            keyPressed = `ctrl+${e.key.toLowerCase()}`;
        } else if (e.altKey) {
            keyPressed = `alt+${e.key.toLowerCase()}`;
        } else if (e.key === 'Escape') {
            keyPressed = 'escape';
        } else if (e.key === ' ') {
            keyPressed = 'space';
            e.preventDefault(); // Prevent page scroll
        } else {
            keyPressed = e.key.toLowerCase();
        }

        pressedKeys.push(keyPressed);
        
        // Check for combinations
        const combination = pressedKeys.join('+');
        
        if (shortcuts[combination]) {
            e.preventDefault();
            shortcuts[combination]();
            pressedKeys = []; // Reset after successful combination
        }
        
        // Single key shortcuts
        if (shortcuts[keyPressed]) {
            e.preventDefault();
            shortcuts[keyPressed]();
            pressedKeys = [];
        }
        
        // Keep only the last 2 keys for combinations
        if (pressedKeys.length > 2) {
            pressedKeys = pressedKeys.slice(-2);
        }
    });

    // Helper functions
    function navigateToCreate(url) {
        // Try to click create button first, fallback to navigation
        const createButton = document.querySelector('[href*="create"], button[wire\\:click*="create"]');
        if (createButton) {
            createButton.click();
        } else {
            window.location.href = url;
        }
    }

    function focusSearch() {
        const searchInput = document.querySelector('input[placeholder*="search" i], input[placeholder*="Search" i]');
        if (searchInput) {
            searchInput.focus();
        }
    }

    function toggleDarkMode() {
        const darkModeToggle = document.querySelector('[x-data*="darkMode"], button[wire\\:click*="darkMode"]');
        if (darkModeToggle) {
            darkModeToggle.click();
        }
    }

    function toggleTimer() {
        const timerButton = document.querySelector('button[wire\\:click*="startTimer"], button[wire\\:click*="stopTimer"]');
        if (timerButton) {
            timerButton.click();
        }
    }

    function showKeyboardShortcuts() {
        // Create modal if it doesn't exist
        let modal = document.getElementById('keyboard-shortcuts-modal');
        
        if (!modal) {
            modal = createShortcutsModal();
            document.body.appendChild(modal);
        }
        
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function hideKeyboardShortcuts() {
        const modal = document.getElementById('keyboard-shortcuts-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function createShortcutsModal() {
        const modal = document.createElement('div');
        modal.id = 'keyboard-shortcuts-modal';
        modal.className = 'fixed inset-0 z-50 hidden';
        
        modal.innerHTML = `
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="hideKeyboardShortcuts()"></div>
                
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                Keyboard Shortcuts
                            </h3>
                            <button onclick="hideKeyboardShortcuts()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white mb-2">Navigation</h4>
                                <div class="space-y-1 text-sm text-gray-600 dark:text-gray-300">
                                    <div class="flex justify-between"><span>Dashboard</span><kbd class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">g + d</kbd></div>
                                    <div class="flex justify-between"><span>Projects</span><kbd class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">g + p</kbd></div>
                                    <div class="flex justify-between"><span>Clients</span><kbd class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">g + c</kbd></div>
                                    <div class="flex justify-between"><span>Time Tracking</span><kbd class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">g + t</kbd></div>
                                    <div class="flex justify-between"><span>Invoices</span><kbd class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">g + i</kbd></div>
                                    <div class="flex justify-between"><span>Expenses</span><kbd class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">g + e</kbd></div>
                                    <div class="flex justify-between"><span>Reports</span><kbd class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">g + r</kbd></div>
                                </div>
                            </div>
                            
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white mb-2">Quick Actions</h4>
                                <div class="space-y-1 text-sm text-gray-600 dark:text-gray-300">
                                    <div class="flex justify-between"><span>New Project</span><kbd class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">n + p</kbd></div>
                                    <div class="flex justify-between"><span>New Client</span><kbd class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">n + c</kbd></div>
                                    <div class="flex justify-between"><span>New Invoice</span><kbd class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">n + i</kbd></div>
                                    <div class="flex justify-between"><span>New Expense</span><kbd class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">n + e</kbd></div>
                                </div>
                            </div>
                            
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white mb-2">Global</h4>
                                <div class="space-y-1 text-sm text-gray-600 dark:text-gray-300">
                                    <div class="flex justify-between"><span>Focus Search</span><kbd class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">/</kbd></div>
                                    <div class="flex justify-between"><span>Toggle Dark Mode</span><kbd class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Ctrl + Shift + L</kbd></div>
                                    <div class="flex justify-between"><span>Toggle Timer</span><kbd class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Space</kbd></div>
                                    <div class="flex justify-between"><span>Show Shortcuts</span><kbd class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">?</kbd></div>
                                    <div class="flex justify-between"><span>Close Modal</span><kbd class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Esc</kbd></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Make hideKeyboardShortcuts globally available
        window.hideKeyboardShortcuts = hideKeyboardShortcuts;
        
        return modal;
    }

    // Show a subtle notification when shortcuts are available
    setTimeout(() => {
        showShortcutsHint();
    }, 3000);

    function showShortcutsHint() {
        // Only show hint if no modal is open and user hasn't seen it recently
        if (localStorage.getItem('shortcuts-hint-seen') === 'true') {
            return;
        }

        const hint = document.createElement('div');
        hint.className = 'fixed bottom-4 left-4 bg-blue-600 text-white px-4 py-2 rounded-lg shadow-lg z-40 animate-slide-in-left';
        hint.innerHTML = `
            <div class="flex items-center space-x-2">
                <span class="text-sm">💡 Press <kbd class="bg-blue-500 px-1 rounded">?</kbd> for keyboard shortcuts</span>
                <button onclick="this.parentElement.parentElement.remove()" class="text-blue-200 hover:text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        
        document.body.appendChild(hint);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (hint.parentElement) {
                hint.classList.add('animate-notification-out');
                setTimeout(() => hint.remove(), 300);
            }
        }, 5000);
        
        // Mark as seen
        localStorage.setItem('shortcuts-hint-seen', 'true');
    }
});

// Export for use in other modules
window.KeyboardShortcuts = {
    showShortcuts: () => document.getElementById('keyboard-shortcuts-modal')?.classList.remove('hidden'),
    hideShortcuts: () => document.getElementById('keyboard-shortcuts-modal')?.classList.add('hidden')
};