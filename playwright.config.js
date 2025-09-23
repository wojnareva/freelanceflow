// Playwright config for Laravel dev server
import { defineConfig } from '@playwright/test';

export default defineConfig({
  timeout: 60000,
  webServer: {
    command: 'php artisan serve --port=8000 --no-reload',
    port: 8000,
    reuseExistingServer: true,
    timeout: 120 * 1000,
  },
  use: {
    baseURL: 'http://127.0.0.1:8000',
    trace: 'on-first-retry',
    headless: true,
  },
});