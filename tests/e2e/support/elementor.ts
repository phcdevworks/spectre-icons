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

  await Promise.all([
    page.waitForURL(/elementor/i),
    editWithElementor.click(),
  ]);

  await expect(page.getByPlaceholder('Search Widget...')).toBeVisible();
}

export async function addIconWidget(page: Page) {
  const widgetSearch = page.getByPlaceholder('Search Widget...');
  await widgetSearch.fill('icon');
  await page.getByText('Icon', { exact: true }).click();
}

export async function openIconPicker(page: Page) {
  const trigger = page
    .getByRole('button', { name: /Icon Library/i })
    .or(page.locator('[data-setting="selected_icon"]').locator('..'))
    .or(page.getByText('Icon Library', { exact: true }))
    .first();

  await expect(trigger).toBeVisible();
  await trigger.click();
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

export async function ensureLibraryTabVisible(page: Page, librarySlug: string) {
  const tab = getLibraryTab(page, librarySlug);
  await expect(tab).toBeVisible();
  await tab.click();
}

export async function selectLibraryByLabel(page: Page, label: string) {
  const modal = getIconManagerModal(page);
  const tab = modal.getByText(label, { exact: true }).first();

  await expect(tab).toBeVisible();
  await tab.click();
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

export async function selectFirstRenderedSpectreIcon(page: Page) {
  const modal = getIconManagerModal(page);
  const iconCandidate = modal
    .locator('.spectre-icon-item, .spectre-icon--rendered, [class*="spectre-lucide-"], [class*="spectre-fa-"]')
    .first();

  await expect(iconCandidate).toBeVisible();
  await iconCandidate.click();

  const insertButton = modal.getByRole('button', { name: /^Insert$/i }).first();
  await expect(insertButton).toBeVisible();
  await insertButton.click();
  await expect(modal).toBeHidden();
}

export function getEditorPreviewIcon(page: Page): Locator {
  return page
    .frameLocator('iframe')
    .locator('[data-widget_type="icon.default"], .elementor-widget-icon')
    .first();
}

export async function publishElementorPage(page: Page) {
  const publishButton = page
    .locator(
      [
        '#elementor-panel-saver-button-publish',
        '.elementor-panel-footer-saver-publish button',
        'button:has-text("Publish")',
        'button:has-text("Update")',
      ].join(', ')
    )
    .first();

  await expect(publishButton).toBeVisible();
  await publishButton.click();

  const viewPageLink = page
    .getByRole('link', { name: /view page|have a look/i })
    .or(page.getByRole('button', { name: /view page|have a look/i }))
    .first();

  await expect(viewPageLink).toBeVisible({ timeout: 30_000 });
}

export async function openPublishedPage(page: Page) {
  const viewPageLink = page
    .getByRole('link', { name: /view page|have a look/i })
    .or(page.getByRole('button', { name: /view page|have a look/i }))
    .first();

  await expect(viewPageLink).toBeVisible();

  if (await viewPageLink.evaluate((element) => element.tagName.toLowerCase() === 'a')) {
    const href = await viewPageLink.getAttribute('href');

    if (!href) {
      throw new Error('Published page link did not expose an href.');
    }

    await page.goto(href, { waitUntil: 'domcontentloaded' });
    return;
  }

  await viewPageLink.click();
  await page.waitForLoadState('domcontentloaded');
}
