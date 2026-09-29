import { test, expect } from '@playwright/test';

test.describe('Autentikasi dan Navigasi Dasar Peran', () => {
    
    test('Pengguna tidak login diarahkan ke halaman login', async ({ page }) => {
        await page.goto('/');
        await expect(page).toHaveURL(/.*login/);
    });

    test('Student alur lengkap: login dan navigasi', async ({ page }) => {
        await page.goto('/login');
        await page.fill('#email', 'student@example.com');
        await page.fill('#password', 'password');
        await page.click('button[type="submit"]');

        await expect(page).toHaveURL(/.*student\/dashboard/);
        await expect(page.locator('h2')).toContainText('Selamat datang');

        // Buka menu pengajuan
        await page.goto('/student/proposals');
        await expect(page.locator('h2').first()).toContainText('Pengusulan Proposal');
    });

    test('Operator alur lengkap: login dan navigasi', async ({ page }) => {
        await page.goto('/login');
        await page.fill('#email', 'operator@example.com');
        await page.fill('#password', 'password');
        await page.click('button[type="submit"]');

        await expect(page).toHaveURL(/.*operator\/dashboard/);
        
        // Buka menu penugasan reviewer
        await page.goto('/operator/reviewer-assignments');
        await expect(page.locator('h2').first()).toContainText('Penugasan Reviewer');
    });

    test('Supervisor alur lengkap: login dan navigasi', async ({ page }) => {
        await page.goto('/login');
        await page.fill('#email', 'supervisor@example.com');
        await page.fill('#password', 'password');
        await page.click('button[type="submit"]');

        await expect(page).toHaveURL(/.*supervisor\/dashboard/);
        await expect(page.locator('h2')).toContainText('Halo,');

        // Buka daftar bimbingan
        await page.goto('/supervisor/proposals');
        await expect(page.locator('h2').first()).toContainText('Proposal Bimbingan');
    });
});
