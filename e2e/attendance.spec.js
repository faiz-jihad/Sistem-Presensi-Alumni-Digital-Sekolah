import { test, expect } from '@playwright/test';

test.describe('Student Attendance Resource E2E', () => {
  test('should load student attendance list and verify table', async ({ page }) => {
    // 1. Login
    await page.goto('/admin/login');
    await page.locator('input[type="email"], #email, [id="data.email"]').fill('superadmin@simpad.app');
    await page.locator('input[type="password"], #password, [id="data.password"]').fill('password');
    await page.locator('button[type="submit"]').click();
    await expect(page).toHaveURL(/.*\/admin$/);
    await expect(page.locator('.fi-sidebar').first()).toBeVisible({ timeout: 15000 });

    // 2. Navigate to Student Attendance List
    await page.goto('/admin/student-attendances');
    await expect(page).toHaveURL(/\/admin\/student-attendances/);

    // Verify list headers or tables are loaded
    const heading = page.locator('h1:has-text("Presensi"), h1:has-text("Attendance")');
    await expect(heading.or(page.locator('.fi-header-heading'))).toBeVisible();

    // Verify table structure or empty state is visible
    const table = page.locator('.fi-ta-table, .fi-ta-empty-state');
    await expect(table).toBeVisible();
  });
});
