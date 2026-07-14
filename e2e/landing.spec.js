import { test, expect } from '@playwright/test';

test.describe('Landing Page', () => {
  test('should load landing page successfully and verify elements', async ({ page }) => {
    // Navigate to the landing page
    await page.goto('/');

    // Verify the page title
    await expect(page).toHaveTitle(/SIMPAD/);

    // Verify that the logo/brand name or main heading is present
    const heading = page.locator('h1');
    await expect(heading).toBeVisible();

    // Verify that the login portal links are present
    const loginLinks = page.locator('a[href="/admin/login"]');
    await expect(loginLinks.first()).toBeVisible();

    // Verify some sections like features or statistics exist
    const statsSection = page.locator('section');
    await expect(statsSection.first()).toBeVisible();
  });
});
