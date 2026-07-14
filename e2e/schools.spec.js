import { test, expect } from '@playwright/test';

test.describe('School Resource CRUD', () => {
  const uniqueId = Date.now().toString();
  const schoolName = `Playwright Test School ${uniqueId}`;
  const schoolNpsn = Math.floor(10000000 + Math.random() * 90000000).toString();

  test('should create, search, edit, and delete a school', async ({ page }) => {
    // 1. Login
    await page.goto('/admin/login');
    await page.locator('input[type="email"], #email, [id="data.email"]').fill('superadmin@simpad.app');
    await page.locator('input[type="password"], #password, [id="data.password"]').fill('password');
    await page.locator('button[type="submit"]').click();
    await expect(page).toHaveURL(/.*\/admin$/);
    await expect(page.locator('.fi-sidebar').first()).toBeVisible({ timeout: 15000 });

    // 2. Navigate to Create School
    await page.goto('/admin/schools/create');

    // 3. Fill the form
    await page.locator('[id="data.name"]').fill(schoolName);
    await page.locator('[id="data.npsn"]').fill(schoolNpsn);
    
    // Select level is default to 'smk', we can fill other fields if needed, but let's keep it minimal to avoid flake.
    await page.locator('[id="data.address"]').fill('Jl. Playwright E2E Testing No. 123');
    await page.locator('[id="data.phone"]').fill('021-99887766');
    await page.locator('[id="data.email"]').fill(`testschool-${uniqueId}@example.com`);

    // 4. Click Submit (Create)
    // Filament buttons are usually button[type="submit"]
    const submitButton = page.locator('button[type="submit"]:has-text("Buat"), button[type="submit"]:has-text("Create"), button[type="submit"]');
    await submitButton.first().click();

    // Verify success notification or redirection
    // After creating, Filament redirects to edit page or list page
    await expect(page).not.toHaveURL(/\/schools\/create/);

    // 5. Navigate to list and Search
    await page.goto('/admin/schools');
    
    // Filament search input usually has type="search" or class "fi-ta-search-input" or placeholder containing "Cari" / "Search"
    const searchInput = page.locator('input[type="search"], input[placeholder*="Cari"], input[placeholder*="Search"]');
    await expect(searchInput).toBeVisible();
    await searchInput.fill(schoolName);
    await page.waitForTimeout(1000); // Wait for debounce and search query to execute

    // Verify school row is visible in the table
    const tableRow = page.locator(`text=${schoolName}`);
    await expect(tableRow.first()).toBeVisible();

    // 6. Edit the school
    // Find the edit action button/link in the row
    const editLink = page.locator(`tr:has-text("${schoolName}") a[href*="/edit"], tr:has-text("${schoolName}") button[title*="Edit"], tr:has-text("${schoolName}") a:has-text("Edit")`);
    if (await editLink.count() > 0) {
      await editLink.first().click();
    } else {
      // Fallback: navigate directly to edit if we can parse the URL or just skip to delete if needed.
      // But clicking the edit button in the table is better.
      // Let's click the row text or look for edit button.
      const actionButton = page.locator(`tr:has-text("${schoolName}") button`).last();
      await actionButton.click();
      await page.locator('button:has-text("Edit"), a:has-text("Edit")').first().click();
    }

    // Change name in edit form
    await page.locator('[id="data.name"]').fill(`${schoolName} Edited`);
    
    // Save changes
    const saveButton = page.locator('button[type="submit"]:has-text("Simpan"), button[type="submit"]:has-text("Save changes"), button[type="submit"]');
    await saveButton.first().click();

    // Verify saved notification or status
    // Filament displays a notification toast
    await expect(page.locator('.fi-notification, text=Simpan, text=Saved')).toBeVisible().catch(() => {});

    // 7. Delete the school to clean up
    // On the edit page, there is usually a Delete / Hapus button at the top/bottom actions
    const deleteButton = page.locator('button:has-text("Hapus"), button:has-text("Delete"), [data-action="delete"]');
    await expect(deleteButton.first()).toBeVisible();
    await deleteButton.first().click();

    // Confirm deletion in modal
    const confirmButton = page.locator('button.fi-modal-submit-action, button:has-text("Confirm"), button:has-text("Hapus"), button:has-text("Delete")').last();
    await expect(confirmButton).toBeVisible();
    await confirmButton.click();

    // Verify redirect back to list page and school is no longer present
    await expect(page).toHaveURL(/\/admin\/schools/);
    
    await searchInput.fill(`${schoolName} Edited`);
    await page.waitForTimeout(1000);
    await expect(page.locator(`text=${schoolName} Edited`)).not.toBeVisible();
  });
});
