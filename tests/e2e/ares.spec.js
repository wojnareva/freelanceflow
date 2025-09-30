import { test, expect } from '@playwright/test';

// This test verifies that entering a valid IČO autocompletes company data via ARES.

test('Client form autocompletes company by IČO', async ({ page, baseURL }) => {
  // 1. Login (create user via factory route or bypass if app has guest access). For demo assume we can register quickly.
  await page.goto(`${baseURL}/register`);

  await page.fill('input[name="name"]', 'Playwright User');
  const email = `playwright${Date.now()}@example.com`;
  await page.fill('input[name="email"]', email);
  await page.fill('input[name="password"]', 'Password1234');
  await page.fill('input[name="password_confirmation"]', 'Password1234');
  await page.click('text=Register');

  // 2. Navigate to create client form
  await page.goto(`${baseURL}/clients/create`);

  // 3. Type valid IČO
  await page.fill('input[placeholder="12345678"]', '25655701');

  // 4. Wait for success indicator text
  await expect(page.locator('text=Údaje firmy byly načteny z registru')).toBeVisible({ timeout: 10000 });

  // 5. Verify company name field filled automatically
  const companyValue = await page.inputValue('input[id="company"]');
  expect(companyValue).not.toBe('');
});