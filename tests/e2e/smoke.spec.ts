import { test, expect } from '@playwright/test';
import { expectPluginActive, openSpectreIconsSettings } from './support/wp-admin';

test.describe('WordPress admin smoke', () => {
  test('Spectre Icons is active and exposes its settings screen', async ({ page }) => {
    await expectPluginActive(page, 'Spectre Icons');
    await openSpectreIconsSettings(page);

    await expect(page.getByLabel('Lucide Icons')).toBeVisible();
    await expect(page.getByLabel('Font Awesome')).toBeVisible();
  });
});
