import { test, expect } from '@playwright/test';
import {
  addWidget,
  expandFirstRepeaterItem,
  getEditorPostUrl,
  openIconPicker,
  openNewPageInElementor,
  publishElementorPage,
  selectLibraryByLabel,
  selectFirstRenderedSpectreIcon,
} from '../support/elementor';
import { openSpectreIconsSettings, saveSettings, setLibraryEnabled } from '../support/wp-admin';

test.beforeEach(async ({ page }) => {
  await openSpectreIconsSettings(page);
  await setLibraryEnabled(page, 'Lucide Icons', true);
  await setLibraryEnabled(page, 'Font Awesome', true);
  await saveSettings(page);
});

test.describe('Icon Box widget', () => {
  test('Spectre icon renders in editor preview and frontend', async ({ page }) => {
    await openNewPageInElementor(page);
    await addWidget(page, 'Icon Box');
    await openIconPicker(page);
    await selectLibraryByLabel(page, 'Lucide Icons');
    await selectFirstRenderedSpectreIcon(page);

    const preview = page
      .frameLocator('iframe')
      .locator('[data-widget_type="icon-box.default"] svg, .elementor-widget-icon-box svg')
      .first();
    await expect(preview).toBeVisible();

    await publishElementorPage(page);
    const postUrl = await getEditorPostUrl(page);
    await page.goto(postUrl, { waitUntil: 'domcontentloaded' });
    await page.waitForSelector('.elementor-widget-icon-box svg', { timeout: 15_000 });
    await expect(page.locator('.elementor-widget-icon-box svg').first()).toBeVisible();
  });
});

test.describe('Icon List widget', () => {
  test('Spectre icon renders in editor preview and frontend', async ({ page }) => {
    await openNewPageInElementor(page);
    await addWidget(page, 'Icon List');
    await expandFirstRepeaterItem(page);
    await openIconPicker(page);
    await selectLibraryByLabel(page, 'Lucide Icons');
    await selectFirstRenderedSpectreIcon(page);

    const preview = page
      .frameLocator('iframe')
      .locator('[data-widget_type="icon-list.default"] svg, .elementor-widget-icon-list svg')
      .first();
    await expect(preview).toBeVisible();

    await publishElementorPage(page);
    const postUrl = await getEditorPostUrl(page);
    await page.goto(postUrl, { waitUntil: 'domcontentloaded' });
    await page.waitForSelector('.elementor-widget-icon-list svg', { timeout: 15_000 });
    await expect(page.locator('.elementor-widget-icon-list svg').first()).toBeVisible();
  });
});

test.describe('Social Icons widget', () => {
  test('Spectre icon renders in editor preview and frontend', async ({ page }) => {
    await openNewPageInElementor(page);
    await addWidget(page, 'Social Icons');
    await expandFirstRepeaterItem(page);
    await openIconPicker(page);
    await selectLibraryByLabel(page, 'Lucide Icons');
    await selectFirstRenderedSpectreIcon(page);

    const preview = page
      .frameLocator('iframe')
      .locator('[data-widget_type="social-icons.default"] svg, .elementor-widget-social-icons svg')
      .first();
    await expect(preview).toBeVisible();

    await publishElementorPage(page);
    const postUrl = await getEditorPostUrl(page);
    await page.goto(postUrl, { waitUntil: 'domcontentloaded' });
    await page.waitForSelector('.elementor-widget-social-icons svg', { timeout: 15_000 });
    await expect(page.locator('.elementor-widget-social-icons svg').first()).toBeVisible();
  });
});
