import fs from 'node:fs/promises';
import path from 'node:path';
import { chromium } from 'playwright';

const baseUrl = process.env.APP_VISUAL_BASE_URL ?? 'http://127.0.0.1:8001';
const screenshotDir = path.join(process.cwd(), 'storage', 'app', 'smoke-test');

async function main() {
    await fs.mkdir(screenshotDir, { recursive: true });

    const browser = await chromium.launch({
        headless: true,
        channel: 'msedge',
    });
    const context = await browser.newContext({
        viewport: { width: 1440, height: 1200 },
        colorScheme: 'light',
    });

    const results = [];

    async function test(name, fn) {
        const page = await context.newPage();
        const started = Date.now();
        try {
            const result = await fn(page);
            results.push({ test: name, status: 'PASS', durationMs: Date.now() - started, ...result });
            console.log(`  ✅ ${name}`);
        } catch (err) {
            await page.screenshot({ path: path.join(screenshotDir, `FAIL-${name.replace(/\s/g, '_')}.png`), fullPage: true });
            results.push({ test: name, status: 'FAIL', error: err.message, durationMs: Date.now() - started });
            console.log(`  ❌ ${name}: ${err.message}`);
        } finally {
            await page.close();
        }
    }

    console.log('\n🔍 Noble Nest Academy — Full Smoke Test\n');

    // ─── Public pages ───
    await test('Home page loads', async (page) => {
        const res = await page.goto(`${baseUrl}/`, { waitUntil: 'networkidle', timeout: 15000 });
        if (res.status() >= 400) throw new Error(`HTTP ${res.status()}`);
        await page.screenshot({ path: path.join(screenshotDir, 'home.png'), fullPage: true });
        return { httpStatus: res.status() };
    });

    await test('Login page loads', async (page) => {
        const res = await page.goto(`${baseUrl}/login`, { waitUntil: 'networkidle', timeout: 15000 });
        if (res.status() >= 400) throw new Error(`HTTP ${res.status()}`);
        await page.screenshot({ path: path.join(screenshotDir, 'login.png'), fullPage: true });
        return { httpStatus: res.status() };
    });

    await test('Register page loads', async (page) => {
        const res = await page.goto(`${baseUrl}/register`, { waitUntil: 'networkidle', timeout: 15000 });
        if (res.status() >= 400) throw new Error(`HTTP ${res.status()}`);
        return { httpStatus: res.status() };
    });

    // ─── Login as Admin ───
    await test('Admin login works', async (page) => {
        await page.goto(`${baseUrl}/login`, { waitUntil: 'networkidle', timeout: 15000 });
        await page.fill('input[name="email"]', 'admin@noblenest.test');
        await page.fill('input[name="password"]', 'Password1!');
        await Promise.all([
            page.waitForURL('**/admin/**', { timeout: 15000 }).catch(() => page.waitForURL('**/', { timeout: 5000 })),
            page.click('button[type="submit"]'),
        ]);
        const url = page.url();
        if (url.includes('/login')) throw new Error('Still on login page — credentials rejected');
        await page.screenshot({ path: path.join(screenshotDir, 'admin-dashboard.png'), fullPage: true });
        return { redirectedTo: url };
    });

    // ─── Admin Analytics (the fixed page) ───
    await test('Admin analytics loads without error', async (page) => {
        // First login
        await page.goto(`${baseUrl}/login`, { waitUntil: 'networkidle', timeout: 15000 });
        await page.fill('input[name="email"]', 'admin@noblenest.test');
        await page.fill('input[name="password"]', 'Password1!');
        await Promise.all([
            page.waitForURL('**/*', { timeout: 15000, waitUntil: 'networkidle' }),
            page.click('button[type="submit"]'),
        ]);

        const res = await page.goto(`${baseUrl}/admin/analytics`, { waitUntil: 'networkidle', timeout: 15000 });
        if (res.status() >= 400) throw new Error(`HTTP ${res.status()}`);

        // Check no PHP error visible
        const body = await page.textContent('body');
        if (body.includes('Undefined array key') || body.includes('ErrorException')) {
            throw new Error('PHP error detected on analytics page');
        }
        await page.screenshot({ path: path.join(screenshotDir, 'admin-analytics.png'), fullPage: true });
        return { httpStatus: res.status() };
    });

    // ─── AI Orchestrator + Modal test ───
    await test('Orchestrator loads & modal inputs are fillable', async (page) => {
        await page.goto(`${baseUrl}/login`, { waitUntil: 'networkidle', timeout: 15000 });
        await page.fill('input[name="email"]', 'admin@noblenest.test');
        await page.fill('input[name="password"]', 'Password1!');
        await Promise.all([
            page.waitForURL('**/*', { timeout: 15000, waitUntil: 'networkidle' }),
            page.click('button[type="submit"]'),
        ]);

        const res = await page.goto(`${baseUrl}/admin/orchestrator`, { waitUntil: 'networkidle', timeout: 15000 });
        if (res.status() >= 400) throw new Error(`HTTP ${res.status()}`);

        // Click "Add AI Provider" button
        await page.click('button[data-bs-target="#addProviderModal"]');
        await page.waitForTimeout(600); // wait for modal animation

        // Try filling the modal form inputs
        const nameInput = page.locator('#addProviderModal input[name="name"]');
        await nameInput.fill('Test Provider');
        const value = await nameInput.inputValue();
        if (value !== 'Test Provider') throw new Error('Could not fill name input in modal');

        const slugInput = page.locator('#addProviderModal input[name="slug"]');
        await slugInput.fill('test-provider');
        const slugValue = await slugInput.inputValue();
        if (slugValue !== 'test-provider') throw new Error('Could not fill slug input in modal');

        await page.screenshot({ path: path.join(screenshotDir, 'orchestrator-modal.png'), fullPage: true });
        return { httpStatus: res.status(), modalWorks: true };
    });

    // ─── Checkout / Pricing page ───
    await test('Checkout page loads with Stripe buttons', async (page) => {
        await page.goto(`${baseUrl}/login`, { waitUntil: 'networkidle', timeout: 15000 });
        await page.fill('input[name="email"]', 'parent@noblenest.test');
        await page.fill('input[name="password"]', 'Password1!');
        await Promise.all([
            page.waitForURL('**/*', { timeout: 15000, waitUntil: 'networkidle' }),
            page.click('button[type="submit"]'),
        ]);

        const res = await page.goto(`${baseUrl}/checkout`, { waitUntil: 'networkidle', timeout: 15000 });
        if (res.status() >= 400) throw new Error(`HTTP ${res.status()}`);

        const body = await page.textContent('body');
        if (!body.includes('Stripe') && !body.includes('Pay with')) {
            throw new Error('Stripe payment buttons not found on checkout page');
        }
        await page.screenshot({ path: path.join(screenshotDir, 'checkout.png'), fullPage: true });
        return { httpStatus: res.status() };
    });

    // ─── Activities page ───
    await test('Activities page loads', async (page) => {
        const res = await page.goto(`${baseUrl}/activities`, { waitUntil: 'networkidle', timeout: 15000 });
        if (res.status() >= 400) throw new Error(`HTTP ${res.status()}`);
        await page.screenshot({ path: path.join(screenshotDir, 'activities.png'), fullPage: true });
        return { httpStatus: res.status() };
    });

    // ─── Marketplace ───
    await test('Marketplace page loads', async (page) => {
        const res = await page.goto(`${baseUrl}/marketplace`, { waitUntil: 'networkidle', timeout: 15000 });
        if (res.status() >= 400) throw new Error(`HTTP ${res.status()}`);
        return { httpStatus: res.status() };
    });

    // ─── Summary ───
    await browser.close();
    const passed = results.filter(r => r.status === 'PASS').length;
    const failed = results.filter(r => r.status === 'FAIL').length;

    console.log(`\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━`);
    console.log(`  ${passed} passed, ${failed} failed out of ${results.length} tests`);
    console.log(`━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n`);

    await fs.writeFile(
        path.join(screenshotDir, 'results.json'),
        JSON.stringify(results, null, 2) + '\n',
        'utf8',
    );

    if (failed > 0) process.exitCode = 1;
}

main().catch((error) => {
    console.error(error);
    process.exitCode = 1;
});
