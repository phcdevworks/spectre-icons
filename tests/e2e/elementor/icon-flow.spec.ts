import { test, expect } from '@playwright/test';
import {
  addIconWidget,
  getEditorPreviewIcon,
  openIconPicker,
  openNewPageInElementor,
  openPublishedPage,
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
