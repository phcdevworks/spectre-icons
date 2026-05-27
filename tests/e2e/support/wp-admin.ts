import { expect, type Page } from '@playwright/test';

const adminUser = process.env.SPECTRE_E2E_ADMIN_USER ?? 'admin';
const adminPassword = process.env.SPECTRE_E2E_ADMIN_PASSWORD ?? 'password';

async function dismissAdminOverlays(page: Page) {
  const dismissors = [
    page.getByRole('button', { name: /^Got it$/i }),
    page.getByRole('button', { name: /^Close$/i }),
    page.getByRole('link', { name: /^Dismiss$/i }),
    page.locator('#wp-pointer-0 button, #wp-pointer-0 a').first(),
    page.locator('.e-notice__dismiss').first(),
    page.locator('.elementor-guide-button-skip').first(),
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
}

export async function gotoAdmin(page: Page, path: string) {
  await loginToWordPress(page);
  await page.goto(`/wp-admin/${path.replace(/^\//, '')}`, { waitUntil: 'domcontentloaded' });
}

export async function expectPluginActive(page: Page, pluginName: string) {
  await gotoAdmin(page, 'plugins.php');

  const pluginRowByData = page
    .locator('tr[data-plugin="spectre-icons/spectre-icons.php"], tr[data-slug="spectre-icons"]')
    .first();
  const pluginRowByName = page.locator('tr').filter({ hasText: pluginName }).first();
  const pluginRow = (await pluginRowByData.count()) ? pluginRowByData : pluginRowByName;

  if (await pluginRow.count()) {
    await expect(pluginRow).toBeVisible();
    await expect(pluginRow).toHaveClass(/active/);
    await expect(pluginRow.getByRole('link', { name: /Deactivate/i })).toBeVisible();
    return;
  }

  // Some wp-env / WordPress combinations can omit the mounted plugin from the
  // installed plugins table while still loading it. The settings page is
  // registered by Spectre Icons, so reaching it proves the plugin is active.
  await openSpectreIconsSettings(page);
}

export async function openSpectreIconsSettings(page: Page) {
  await gotoAdmin(page, 'options-general.php?page=spectre-icons-elementor');
  await dismissAdminOverlays(page);
  await expect(page.getByRole('heading', { name: /Spectre Icons/i }).first()).toBeVisible();
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

export async function openMyIconsPage(page: Page) {
  await gotoAdmin(page, 'options-general.php?page=spectre-icons-my-icons');
  await dismissAdminOverlays(page);
}

/**
 * Upload an SVG file to the My Icons library.
 *
 * @param page        Playwright page.
 * @param svgContent  Raw SVG string to upload.
 * @param filename    Filename to use for the upload (must end in .svg).
 * @returns The derived icon slug shown in the UI after upload.
 */
export async function uploadMyIcon(page: Page, svgContent: string, filename: string): Promise<void> {
  await openMyIconsPage(page);

  const fileInput = page.locator('input[type="file"][accept*="svg"], input[type="file"][name*="svg"]').first();
  await expect(fileInput).toBeAttached();

  const buffer = Buffer.from(svgContent);
  await fileInput.setInputFiles({
    name: filename,
    mimeType: 'image/svg+xml',
    buffer,
  });

  // Upload starts automatically when files are selected.
  const slug = filename.replace(/\.svg$/i, '').toLowerCase().replace(/[^a-z0-9]/g, '-');
  await page.waitForSelector(`[data-slug="${slug}"], .spectre-icon-item[data-icon="${slug}"], .spectre-icons-icon-list li`, { timeout: 10_000 });
}
