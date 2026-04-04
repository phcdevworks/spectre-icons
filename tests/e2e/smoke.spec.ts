import { test, expect } from '@playwright/test';

test('Spectre Icons plugin is active', async ({ page }) => {
  await page.goto('http://localhost:8888/wp-login.php');

  await page.fill('#user_login', 'admin');
  await page.fill('#user_pass', 'password');
  await page.click('#wp-submit');

  await page.waitForURL(/wp-admin/);

  await page.goto('http://localhost:8888/wp-admin/plugins.php');
  await page.waitForLoadState('networkidle');

  await expect(page.locator('#the-list')).toContainText('Elementor');
  await expect(page.locator('#the-list')).toContainText('Spectre Icons');
});
