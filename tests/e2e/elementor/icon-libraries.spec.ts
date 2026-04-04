import { test, expect } from '@playwright/test';
import { openNewPageInElementor, addIconWidget, openIconPicker, expectLibraryTabHidden, expectLibraryTabVisible } from '../support/elementor';
import { openSpectreIconsSettings, saveSettings, setLibraryEnabled } from '../support/wp-admin';

test.describe('Elementor icon libraries', () => {
  test.describe.configure({ mode: 'serial' });

  test.afterEach(async ({ page }) => {
    await openSpectreIconsSettings(page);
    await setLibraryEnabled(page, 'Lucide Icons', true);
    await setLibraryEnabled(page, 'Font Awesome', true);
    await saveSettings(page);
  });

  test('Elementor icon picker shows bundled Spectre library tabs', async ({ page }) => {
    await openSpectreIconsSettings(page);
    await setLibraryEnabled(page, 'Lucide Icons', true);
    await setLibraryEnabled(page, 'Font Awesome', true);
    await saveSettings(page);

    await openNewPageInElementor(page);
    await addIconWidget(page);
    await openIconPicker(page);

    await expect(page.locator('body')).toContainText('Spectre');
    await expectLibraryTabVisible(page, 'spectre-lucide');
    await expectLibraryTabVisible(page, 'spectre-fontawesome');
  });

  test('Disabled libraries are removed from the Elementor picker', async ({ page }) => {
    await openSpectreIconsSettings(page);
    await setLibraryEnabled(page, 'Lucide Icons', true);
    await setLibraryEnabled(page, 'Font Awesome', false);
    await saveSettings(page);

    await openNewPageInElementor(page);
    await addIconWidget(page);
    await openIconPicker(page);

    await expectLibraryTabVisible(page, 'spectre-lucide');
    await expectLibraryTabHidden(page, 'spectre-fontawesome');
  });
});
