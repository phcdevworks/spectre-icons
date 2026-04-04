import { test, expect } from '@playwright/test';
import {
  addIconWidget,
  ensureLibraryTabVisible,
  getEditorPreviewIcon,
  openIconPicker,
  openNewPageInElementor,
  openPublishedPage,
  publishElementorPage,
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

    await expect(page.locator('body')).toContainText('Spectre');
    await ensureLibraryTabVisible(page, 'spectre-lucide');
    await expect(page.locator('[data-library="spectre-fontawesome"], [data-tab="spectre-fontawesome"]')).toBeVisible();

    await selectFirstRenderedSpectreIcon(page);

    const editorPreview = getEditorPreviewIcon(page);
    await expect(editorPreview).toBeVisible();

    const editorPreviewMarkup = await editorPreview.evaluate(
      (node) => node.parentElement?.outerHTML ?? node.outerHTML
    );
    await expect(editorPreviewMarkup).toContain('<svg');
    await expect(editorPreviewMarkup).toMatch(/spectre-(lucide|fa|fontawesome)-/i);

    await publishElementorPage(page);
    await openPublishedPage(page);

    const frontendIcon = page.locator('.elementor-widget-icon svg, .elementor-icon svg').first();
    await expect(frontendIcon).toBeVisible();

    const frontendMarkup = await frontendIcon.evaluate(
      (node) => node.parentElement?.outerHTML ?? node.outerHTML
    );
    await expect(frontendMarkup).toContain('<svg');
    await expect(frontendMarkup).toMatch(/spectre-(lucide|fa|fontawesome)-/i);
  });
});
