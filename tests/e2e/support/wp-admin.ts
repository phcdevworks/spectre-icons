import { expect, type Page } from '@playwright/test';

const adminUser = process.env.SPECTRE_E2E_ADMIN_USER ?? 'admin';
const adminPassword = process.env.SPECTRE_E2E_ADMIN_PASSWORD ?? 'password';

async function dismissAdminOverlays(page: Page) {
  const dismissors = [
    page.getByRole('button', { name: /^Got it$/i }),
    page.getByRole('button', { name: /^Close$/i }),
    page.getByRole('link', { name: /^Dismiss$/i }),
    page.locator('#wp-pointer-0 button, #wp-pointer-0 a').first(),
  ];

  for (const locator of dismissors) {
    if (await locator.count()) {
      const first = locator.first();
      if (await first.isVisible().catch(() => false)) {
        await first.click().catch(() => {});
      }
    }
  }
}

export async function loginToWordPress(page: Page) {
  await page.goto('/wp-admin/', { waitUntil: 'domcontentloaded' });

  if (page.url().includes('/wp-admin') && !page.url().includes('/wp-login.php')) {
    return;
  }

  await page.waitForURL(/wp-login\.php/);
  await page.fill('#user_login', adminUser);
  await page.fill('#user_pass', adminPassword);
  await page.click('#wp-submit');
  await page.waitForLoadState('domcontentloaded');

  if (page.url().includes('/wp-login.php')) {
    throw new Error(
      'WordPress login failed. Check SPECTRE_E2E_ADMIN_USER and SPECTRE_E2E_ADMIN_PASSWORD.'
    );
  }

  await page.waitForURL(/wp-admin/);
  await expect(page.locator('#wpadminbar')).toBeVisible();
}

export async function gotoAdmin(page: Page, path: string) {
  await loginToWordPress(page);
  await page.goto(`/wp-admin/${path.replace(/^\//, '')}`, { waitUntil: 'domcontentloaded' });
}

export async function expectPluginActive(page: Page, pluginName: string) {
  await gotoAdmin(page, 'plugins.php');

  const pluginRow = page.locator('tr').filter({ hasText: pluginName }).first();

  await expect(pluginRow).toBeVisible();
  await expect(pluginRow).toHaveClass(/active/);
  await expect(pluginRow.getByRole('link', { name: /Deactivate/i })).toBeVisible();
}

export async function openSpectreIconsSettings(page: Page) {
  await gotoAdmin(page, 'options-general.php?page=spectre-icons-elementor');
  await dismissAdminOverlays(page);
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
  await dismissAdminOverlays(page);
  await page.getByRole('button', { name: /save changes|save/i }).click();
  await page.waitForLoadState('domcontentloaded');
}
