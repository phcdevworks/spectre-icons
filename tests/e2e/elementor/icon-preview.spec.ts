import { test, expect } from '@playwright/test';
import { addIconWidget, openIconPicker, openNewPageInElementor, selectFirstRenderedSpectreIcon } from '../support/elementor';

test.describe('Elementor icon preview', () => {
  test('Selecting a Spectre icon updates the Elementor preview', async ({ page }) => {
    await openNewPageInElementor(page);
    await addIconWidget(page);
    await openIconPicker(page);

    await expect(page.locator('body')).toContainText('Spectre');

    await selectFirstRenderedSpectreIcon(page);

    const renderedSvg = page.locator('.elementor-widget-icon svg, .elementor-icon svg, svg').first();
    await expect(renderedSvg).toBeVisible();

    await expect(page).toHaveScreenshot('elementor-icon-preview.png', {
      animations: 'disabled',
    });
  });
});
