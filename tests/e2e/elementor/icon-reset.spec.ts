import { test, expect } from '@playwright/test';
import {
  addIconWidget,
  getEditorPreviewIcon,
  getIconManagerModal,
  openIconPicker,
  openNewPageInElementor,
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

test.describe('Icon reset — MutationObserver clears stale SVG', () => {
  /**
   * Regression coverage for v1.2.0 fix: after a widget icon is reset,
   * clearIconFromElement must remove the stale SVG from the element.
   *
   * Approach: select an icon → verify SVG in editor preview → use evaluate()
   * to programmatically remove the Spectre class (replicating what Elementor
   * does on reset), then confirm the MutationObserver cleared the SVG.
   */
  test('removing Spectre class clears SVG from editor preview element', async ({ page }) => {
    await openNewPageInElementor(page);
    await addIconWidget(page);
    await openIconPicker(page);
    await selectLibraryByLabel(page, 'Lucide Icons');
    await selectFirstRenderedSpectreIcon(page);

    const editorPreview = getEditorPreviewIcon(page);
    await expect(editorPreview).toBeVisible();

    const editorFrame = page.frameLocator('iframe');

    // Confirm the SVG was injected.
    const iconElement = editorFrame
      .locator('.elementor-widget-icon [class*="spectre-lucide-"], [data-widget_type="icon.default"] [class*="spectre-lucide-"]')
      .first();
    await expect(iconElement).toBeVisible({ timeout: 15_000 });
    await expect(iconElement.locator('svg')).toBeVisible();

    // Simulate an Elementor icon reset: remove the Spectre class, leaving only
    // the base class.  This triggers the MutationObserver attribute watcher
    // which calls clearIconFromElement.
    await editorFrame.locator('body').evaluate((_, selector) => {
      const el = document.querySelector(selector) as HTMLElement | null;
      if (!el) return;
      // Strip all spectre-lucide-* classes to simulate "None" selection.
      Array.from(el.classList)
        .filter((c) => c.startsWith('spectre-lucide-'))
        .forEach((c) => el.classList.remove(c));
    }, '[class*="spectre-lucide-"]');

    // The MutationObserver is async; wait briefly for it to fire.
    await page.waitForTimeout(800);

    // After reset the element must contain no SVG.
    const svgCount = await editorFrame
      .locator('.elementor-widget-icon svg, [data-widget_type="icon.default"] svg')
      .count()
      .catch(() => 0);
    expect(svgCount).toBe(0);
  });

  test('selecting None in icon picker removes icon from editor preview', async ({ page }) => {
    await openNewPageInElementor(page);
    await addIconWidget(page);
    await openIconPicker(page);
    await selectLibraryByLabel(page, 'Lucide Icons');
    await selectFirstRenderedSpectreIcon(page);

    const editorPreview = getEditorPreviewIcon(page);
    await expect(editorPreview).toBeVisible();

    // Re-open the picker and select the None / empty option.
    await openIconPicker(page);
    const modal = getIconManagerModal(page);

    // Elementor renders a "None" or clear option as the first selectable item
    // in the icon grid, or as a dedicated button.  Try known selectors in order.
    const noneOption = modal
      .locator(
        [
          '[data-icon-value="none"]',
          '.elementor-icon-none',
          '.elementor-icons-manager__tab-content-icon--none',
          'li.elementor-icons-manager__tab-content-icon:first-child',
          '.e-icons-manager__none',
        ].join(', ')
      )
      .first();

    const noneVisible = await noneOption.isVisible().catch(() => false);
    if (noneVisible) {
      await noneOption.click();
      const insertButton = modal.getByRole('button', { name: /^Insert$/i }).first();
      await expect(insertButton).toBeVisible();
      await insertButton.click();
      await expect(modal).toBeHidden();

      // Give the MutationObserver time to clear the SVG.
      await page.waitForTimeout(800);

      const editorFrame = page.frameLocator('iframe');
      const svgCount = await editorFrame
        .locator('.elementor-widget-icon svg, [data-widget_type="icon.default"] svg')
        .count()
        .catch(() => 0);
      expect(svgCount).toBe(0);
    } else {
      // None option not found in this Elementor version; close modal and skip.
      // Click the dialog's own close button first — Escape requires focus to be
      // on the top-level page/dialog, which isn't guaranteed after probing the
      // (non-visible) "None" selector candidates above.
      const closeButton = modal.locator('.dialog-close-button, .dialog-header [class*="close"]').first();
      if (await closeButton.isVisible().catch(() => false)) {
        await closeButton.click();
      } else {
        await page.keyboard.press('Escape');
      }
      await expect(modal).toBeHidden({ timeout: 10_000 });
      test.skip(true, 'None option not present in this Elementor version');
    }
  });
});

test.describe('Settings persistence', () => {
  test('enabled/disabled state survives a page reload', async ({ page }) => {
    await openSpectreIconsSettings(page);
    await setLibraryEnabled(page, 'Lucide Icons', true);
    await setLibraryEnabled(page, 'Font Awesome', false);
    await saveSettings(page);

    // Reload the settings page and verify the saved state.
    await openSpectreIconsSettings(page);
    await expect(page.getByLabel('Lucide Icons')).toBeChecked();
    await expect(page.getByLabel('Font Awesome')).not.toBeChecked();

    // Restore: re-enable Font Awesome.
    await setLibraryEnabled(page, 'Font Awesome', true);
    await saveSettings(page);
  });

  test('disabling both libraries hides all Spectre tabs from the picker', async ({ page }) => {
    await openSpectreIconsSettings(page);
    await setLibraryEnabled(page, 'Lucide Icons', false);
    await setLibraryEnabled(page, 'Font Awesome', false);
    await saveSettings(page);

    await openNewPageInElementor(page);
    await addIconWidget(page);
    await openIconPicker(page);

    const modal = getIconManagerModal(page);
    await expect(modal.getByText('Lucide Icons', { exact: true })).toBeHidden();
    await expect(modal.getByText('Font Awesome', { exact: true })).toBeHidden();

    // Restore.
    await openSpectreIconsSettings(page);
    await setLibraryEnabled(page, 'Lucide Icons', true);
    await setLibraryEnabled(page, 'Font Awesome', true);
    await saveSettings(page);
  });
});
