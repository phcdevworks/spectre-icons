import { expect, type Locator, type Page } from '@playwright/test';
import { gotoAdmin } from './wp-admin';

export async function openNewPageInElementor(page: Page) {
  await gotoAdmin(page, 'post-new.php?post_type=page');

  await dismissEditorOverlays(page);

  const editWithElementor = page
    .getByRole('button', { name: /Edit with Elementor/i })
    .or(page.getByRole('link', { name: /Edit with Elementor/i }))
    .first();
  await expect(editWithElementor).toBeVisible();
  await editWithElementor.click();
  await page.waitForLoadState('networkidle');
}

export async function addIconWidget(page: Page) {
  const widgetSearch = page.getByPlaceholder('Search Widget...');
  await widgetSearch.fill('icon');
  await page.getByText('Icon', { exact: true }).click();
}

export async function openIconPicker(page: Page) {
  await page.locator('[data-setting="selected_icon"]').click();
  await expect(getIconManagerModal(page)).toBeVisible();
}

export async function dismissEditorOverlays(page: Page) {
  const selectors = [
    page.getByRole('button', { name: /^Close$/i }),
    page.getByRole('button', { name: /^Skip$/i }),
    page.getByRole('button', { name: /^Got it$/i }),
    page.getByRole('button', { name: /^Done$/i }),
    page.locator('[aria-label="Close"]').first(),
  ];

  for (const locator of selectors) {
    if (await locator.count()) {
      const first = locator.first();
      if (await first.isVisible().catch(() => false)) {
        await first.click().catch(() => {});
      }
    }
  }
}

export function getIconManagerModal(page: Page): Locator {
  return page.locator('#elementor-icons-manager-modal');
}

export function getLibraryTab(page: Page, libraryName: string): Locator {
  const modal = getIconManagerModal(page);

  return modal
    .locator(
      [
        `[data-library="${libraryName}"]`,
        `[data-tab="${libraryName}"]`,
        `[data-icon-library="${libraryName}"]`,
        `[data-name="${libraryName}"]`,
        `[data-value="${libraryName}"]`,
        `[href*="${libraryName}"]`,
        `[aria-controls*="${libraryName}"]`,
        `[id*="${libraryName}"]`,
      ].join(', ')
    )
    .first();
}

export async function expectLibraryTabVisible(page: Page, librarySlug: string) {
  await expect(getLibraryTab(page, librarySlug)).toBeVisible();
}

export async function expectLibraryTabHidden(page: Page, librarySlug: string) {
  await expect(getLibraryTab(page, librarySlug)).toHaveCount(0);
}

export async function selectFirstRenderedSpectreIcon(page: Page) {
  const modal = getIconManagerModal(page);
  const iconCandidate = modal
    .locator('.spectre-icon-item, .spectre-icon--rendered, [class*="spectre-lucide-"], [class*="spectre-fa-"]')
    .first();

  await expect(iconCandidate).toBeVisible();
  await iconCandidate.click();
}
