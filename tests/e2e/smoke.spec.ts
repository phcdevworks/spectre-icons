import { test, expect } from '@playwright/test';

test('Spectre icon selection updates Elementor preview', async ({ page }) => {
  await page.goto('http://localhost:8888/wp-login.php');
  await page.fill('#user_login', 'admin');
  await page.fill('#user_pass', 'password');
  await page.click('#wp-submit');
  await page.waitForURL(/wp-admin/);

  await page.goto('http://localhost:8888/wp-admin/post-new.php?post_type=page');
  await page.waitForLoadState('networkidle');

  await page.getByRole('link', { name: /Edit with Elementor/i }).click();
  await page.waitForLoadState('networkidle');

  // Replace these selectors with the real Elementor selectors in your install.
  await page.getByPlaceholder('Search Widget...').fill('icon');
  await page.getByText('Icon', { exact: true }).click();

  await page.locator('[data-setting="selected_icon"]').click();

  await expect(page.locator('body')).toContainText('Spectre');

  await page.locator('.spectre-icon-item').first().click();

  await expect(page.locator('svg')).toBeVisible();

  await expect(page).toHaveScreenshot('spectre-icon-preview.png', {
    animations: 'disabled'
  });
});
