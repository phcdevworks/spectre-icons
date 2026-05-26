import * as path from 'path';
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
} from '../support/elementor';
import { uploadMyIcon } from '../support/wp-admin';

const TEST_SVG = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
  <circle cx="12" cy="12" r="10"/>
  <line x1="12" y1="8" x2="12" y2="16"/>
  <line x1="8" y1="12" x2="16" y2="12"/>
</svg>`;

test.describe('My Icons (spectre-user) frontend render', () => {
  test('uploaded icon renders as inline SVG on the published frontend', async ({ page }) => {
    // 1. Upload a custom SVG to My Icons.
    await uploadMyIcon(page, TEST_SVG, 'test-cross.svg');

    // 2. Open a new Elementor page and pick the My Icons tab.
    await openNewPageInElementor(page);
    await addIconWidget(page);
    await openIconPicker(page);

    const modal = getIconManagerModal(page);
    await expect(modal.getByText('My Icons', { exact: true })).toBeVisible({
      timeout: 10_000,
    });
    await selectLibraryByLabel(page, 'My Icons');

    // 3. Select the first icon from My Icons.
    const myIconCandidate = modal
      .locator('[class*="spectre-user-"]')
      .first();
    await expect(myIconCandidate).toBeVisible({ timeout: 10_000 });
    await myIconCandidate.click();

    const insertButton = modal.getByRole('button', { name: /^Insert$/i }).first();
    await expect(insertButton).toBeVisible();
    await insertButton.click();
    await expect(modal).toBeHidden();

    // Editor preview must show the icon.
    const editorPreview = getEditorPreviewIcon(page);
    await expect(editorPreview).toBeVisible({ timeout: 10_000 });

    // 4. Publish and navigate to the frontend.
    await publishElementorPage(page);
    const postUrl = await getEditorPostUrl(page);
    await page.goto(postUrl, { waitUntil: 'domcontentloaded' });

    // 5. The frontend must render inline SVG — NOT an empty wrapper.
    //    spectre-user icons are PHP-rendered, so the SVG must be present in
    //    the initial HTML without waiting for JS injection.
    const frontendWrapper = page
      .locator('[class*="spectre-user-"]')
      .first();
    await expect(frontendWrapper).toBeVisible({ timeout: 15_000 });

    const innerHTML = await frontendWrapper.innerHTML();
    expect(innerHTML).toContain('<svg');
    expect(innerHTML).not.toBe('');
  });

  test('My Icons tab is visible in Elementor picker after upload', async ({ page }) => {
    await uploadMyIcon(page, TEST_SVG, 'test-plus.svg');

    await openNewPageInElementor(page);
    await addIconWidget(page);
    await openIconPicker(page);

    const modal = getIconManagerModal(page);
    await expect(modal.getByText('My Icons', { exact: true })).toBeVisible({
      timeout: 10_000,
    });
  });

  test('My Icons icon renders inline SVG in editor preview', async ({ page }) => {
    await uploadMyIcon(page, TEST_SVG, 'test-circle.svg');

    await openNewPageInElementor(page);
    await addIconWidget(page);
    await openIconPicker(page);

    const modal = getIconManagerModal(page);
    await selectLibraryByLabel(page, 'My Icons');

    const candidate = modal.locator('[class*="spectre-user-"]').first();
    await expect(candidate).toBeVisible({ timeout: 10_000 });
    await candidate.click();

    const insertButton = modal.getByRole('button', { name: /^Insert$/i }).first();
    await insertButton.click();
    await expect(modal).toBeHidden();

    const editorPreview = getEditorPreviewIcon(page);
    await expect(editorPreview).toBeVisible({ timeout: 10_000 });

    const previewHtml = await editorPreview.innerHTML();
    expect(previewHtml).toContain('spectre-user-');
  });
});
