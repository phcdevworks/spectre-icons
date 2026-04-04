import { test, expect } from '@playwright/test';
import { openSpectreIconsSettings, saveSettings, setLibraryEnabled } from '../support/wp-admin';

test.describe('Main admin integration', () => {
  test('Spectre Icons settings list the bundled icon libraries', async ({ page }) => {
    await openSpectreIconsSettings(page);

    await expect(page.getByLabel('Lucide Icons')).toBeVisible();
    await expect(page.getByLabel('Font Awesome')).toBeVisible();
  });

  test('Spectre Icons settings persist enabled library choices', async ({ page }) => {
    await openSpectreIconsSettings(page);

    await setLibraryEnabled(page, 'Lucide Icons', true);
    await setLibraryEnabled(page, 'Font Awesome', false);
    await saveSettings(page);

    await expect(page.getByLabel('Lucide Icons')).toBeChecked();
    await expect(page.getByLabel('Font Awesome')).not.toBeChecked();

    await setLibraryEnabled(page, 'Font Awesome', true);
    await saveSettings(page);

    await expect(page.getByLabel('Font Awesome')).toBeChecked();
  });
});
