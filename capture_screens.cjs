const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

(async () => {
    const dir = path.resolve(__dirname, 'screenshots_ppt');
    if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });

    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({
        viewport: { width: 1366, height: 820 },
        deviceScaleFactor: 2
    });
    const page = await context.newPage();

    // 1. Login page
    console.log('Navigating to login...');
    await page.goto('http://127.0.0.1:8000/');
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: path.join(dir, '01_login.png') });
    console.log('Saved 01_login.png');

    // 2. Login as Admin
    await page.fill('input[name="email"]', 'admin@gjp.co.id');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/dasbor');
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: path.join(dir, '02_dashboard_admin.png') });
    console.log('Saved 02_dashboard_admin.png');

    // 3. Rekap Kerja
    await page.goto('http://127.0.0.1:8000/rekap-kerja');
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: path.join(dir, '03_rekap_kerja.png') });
    console.log('Saved 03_rekap_kerja.png');

    // 4. Ritase
    await page.goto('http://127.0.0.1:8000/ritase');
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: path.join(dir, '04_ritase.png') });
    console.log('Saved 04_ritase.png');

    // 5. Payroll
    await page.goto('http://127.0.0.1:8000/payroll');
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: path.join(dir, '05_payroll.png') });
    console.log('Saved 05_payroll.png');

    // 6. Slip Gaji
    const slipLink = await page.$('a[href*="/slip-gaji/"]');
    if (slipLink) {
        await slipLink.click();
        await page.waitForLoadState('networkidle');
        await page.screenshot({ path: path.join(dir, '06_slip_gaji.png') });
        console.log('Saved 06_slip_gaji.png');
    } else {
        console.log('Slip link not found directly, checking payroll calculation');
    }

    // 7. Laporan
    await page.goto('http://127.0.0.1:8000/laporan');
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: path.join(dir, '07_laporan.png') });
    console.log('Saved 07_laporan.png');

    // 8. Logout and Login as Supervisor
    await context.clearCookies();
    await page.goto('http://127.0.0.1:8000/');
    await page.waitForLoadState('networkidle');
    await page.fill('input[name="email"]', 'supervisor@gjp.co.id');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/dasbor');
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: path.join(dir, '08_dashboard_supervisor.png') });
    console.log('Saved 08_dashboard_supervisor.png');

    // 9. Logout and Login as Karyawan
    await context.clearCookies();
    await page.goto('http://127.0.0.1:8000/');
    await page.waitForLoadState('networkidle');
    await page.fill('input[name="email"]', 'karyawan@gjp.co.id');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/dasbor');
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: path.join(dir, '09_dashboard_karyawan.png') });
    console.log('Saved 09_dashboard_karyawan.png');

    await browser.close();
    console.log('All screenshots successfully saved to:', dir);
})();
