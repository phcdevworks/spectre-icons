import { test, expect } from '@playwright/test';
import {
  addIconWidget,
  getEditorPostUrl,
  getEditorPreviewIcon,
  getIconManagerModal,
  openIconPicker,
  openNewPageInElementor,
  publishElementorPage,
  selectLibraryByLabel,
  selectFirstRenderedSpectreIcon,
} from '../support/elementor';
import { openSpectreIconsSettings, saveSettings, setLibraryEnabled } from '../support/wp-admin';

test.describe('Elementor Spectre icon flow', () => {
  test('Spectre libraries are available in the picker and render in editor + frontend', async ({ page }) => {
    await openSpectreIconsSettings(page);
    await setLibraryEnabled(page, 'Lucide Icons', true);
    await setLibraryEnabled(page, 'Font Awesome', true);
    await saveSettings(page);

    await openNewPageInElementor(page);
    await addIconWidget(page);
    await openIconPicker(page);

    await expect(page.getByText('Lucide Icons', { exact: true })).toBeVisible();
    await expect(page.getByText('Font Awesome', { exact: true })).toBeVisible();
    await selectLibraryByLabel(page, 'Lucide Icons');

    await selectFirstRenderedSpectreIcon(page);

    const editorPreview = getEditorPreviewIcon(page);
    await expect(editorPreview).toBeVisible();

    await publishElementorPage(page);
    const postUrl = await getEditorPostUrl(page);
    await page.goto(postUrl, { waitUntil: 'domcontentloaded' });

    // SVG is JS-injected — wait for it.
    await page.waitForSelector('.elementor-widget-icon svg, .elementor-icon svg', { timeout: 15_000 });
    const frontendIcon = page.locator('.elementor-widget-icon svg, .elementor-icon svg').first();
    await expect(frontendIcon).toBeVisible();

    const frontendMarkup = await frontendIcon.evaluate(
      (node) => node.parentElement?.outerHTML ?? node.outerHTML
    );
    expect(frontendMarkup).toContain('<svg');
    expect(frontendMarkup).toMatch(/spectre-(lucide|fa|fontawesome)-/i);
  });

  test('disabled library is hidden from picker but existing icons keep rendering', async ({ page }) => {
    // Enable both libraries and place a Lucide icon on a page.
    await openSpectreIconsSettings(page);
    await setLibraryEnabled(page, 'Lucide Icons', true);
    await setLibraryEnabled(page, 'Font Awesome', true);
    await saveSettings(page);

    await openNewPageInElementor(page);
    await addIconWidget(page);
    await openIconPicker(page);
    await selectLibraryByLabel(page, 'Lucide Icons');
    await selectFirstRenderedSpectreIcon(page);

    await publishElementorPage(page);
    const postUrl = await getEditorPostUrl(page);

    // Now disable Lucide Icons.
    await openSpectreIconsSettings(page);
    await setLibraryEnabled(page, 'Lucide Icons', false);
    await saveSettings(page);

    // Re-open Elementor editor — Lucide tab must not be visible in the picker.
    await openNewPageInElementor(page);
    await addIconWidget(page);
    await openIconPicker(page);

    const modal = getIconManagerModal(page);
    await expect(modal.getByText('Lucide Icons', { exact: true })).toBeHidden();
    await expect(modal.getByText('Font Awesome', { exact: true })).toBeVisible();

    // Visit the published page — the existing Lucide icon must still render.
    await page.goto(postUrl, { waitUntil: 'domcontentloaded' });
    await page.waitForSelector('.elementor-widget-icon svg, .elementor-icon svg', { timeout: 15_000 });
    const frontendIcon = page.locator('.elementor-widget-icon svg, .elementor-icon svg').first();
    await expect(frontendIcon).toBeVisible();
  });
});
