import { expect, type Page } from '@playwright/test';

const adminUser = process.env.SPECTRE_E2E_ADMIN_USER ?? 'admin';
const adminPassword = process.env.SPECTRE_E2E_ADMIN_PASSWORD ?? 'password';

export async function loginToWordPress(page: Page) {
  await page.goto('/wp-login.php');

  if (page.url().includes('/wp-admin')) {
    return;
  }

  await page.fill('#user_login', adminUser);
  await page.fill('#user_pass', adminPassword);
  await page.click('#wp-submit');
  await page.waitForLoadState('networkidle');

  if (page.url().includes('/wp-login.php')) {
    throw new Error(
      'WordPress login failed. Check SPECTRE_E2E_ADMIN_USER and SPECTRE_E2E_ADMIN_PASSWORD.'
    );
  }

  await page.waitForURL(/wp-admin/);
}

export async function gotoAdmin(page: Page, path: string) {
  await loginToWordPress(page);
  await page.goto(`/wp-admin/${path.replace(/^\//, '')}`);
  await page.waitForLoadState('networkidle');
}

export async function openSpectreIconsSettings(page: Page) {
  await gotoAdmin(page, 'options-general.php?page=spectre-icons-elementor');
  await expect(page.getByRole('heading', { level: 1, name: /Spectre Icons/i })).toBeVisible();
}

export async function setLibraryEnabled(page: Page, label: string, enabled: boolean) {
  const checkbox = page.getByLabel(label);

  if (enabled) {
    await checkbox.check();
  } else {
    await checkbox.uncheck();
  }
}

export async function saveSettings(page: Page) {
  await page.getByRole('button', { name: /save changes|save/i }).click();
  await page.waitForLoadState('networkidle');
}
