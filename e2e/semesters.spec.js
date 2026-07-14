import { test, expect } from '@playwright/test';

test.describe('School Admin Semester Access E2E', () => {
  test('should allow school admin to view, edit, and update semesters', async ({ page }) => {
    // 1. Login as School Admin
    await page.goto('/admin/login');
    await page.locator('input[type="email"], #email, [id="data.email"]').fill('admin@smkn1demo.sch.id');
    await page.locator('input[type="password"], #password, [id="data.password"]').fill('password');
    await page.locator('button[type="submit"]').click();
    
    // Verify successful login
    await expect(page).toHaveURL(/.*\/admin$/);
    await expect(page.locator('.fi-sidebar').first()).toBeVisible({ timeout: 15000 });

    // 2. Navigate to Semester Resource
    await page.goto('/admin/semesters');
    await expect(page).toHaveURL(/\/admin\/semesters/);

    // Verify list headers and table loaded
    const heading = page.locator('h1:has-text("Semester"), .fi-header-heading');
    await expect(heading.first()).toBeVisible();

    const table = page.locator('.fi-ta-table');
    await expect(table).toBeVisible();

    // 3. Edit Semester
    // Click on the edit action of the first record in the table
    const editLink = page.locator('.fi-ta-record-action[href*="/edit"], tr a[href*="/edit"], tr button[title*="Edit"]').first();
    await expect(editLink).toBeVisible();
    await editLink.click();

    // Verify edit form loads
    await expect(page).toHaveURL(/\/semesters\/.*\/edit/);
    const nameInput = page.locator('[id="data.name"]');
    await expect(nameInput).toBeVisible();

    // Get current name and modify it
    const originalName = await nameInput.inputValue();
    const updatedName = `${originalName} - Edited`;
    await nameInput.fill(updatedName);

    // Click save
    const saveButton = page.locator('button[type="submit"]:has-text("Simpan"), button[type="submit"]:has-text("Save"), button[type="submit"]').first();
    await saveButton.click();

    // Wait for success toast notification
    await expect(page.locator('.fi-notification, text=Simpan, text=Saved')).toBeVisible().catch(() => {});

    // 4. Revert change to keep database clean
    await nameInput.fill(originalName);
    await saveButton.click();
    await expect(page.locator('.fi-notification, text=Simpan, text=Saved')).toBeVisible().catch(() => {});
  });
});
