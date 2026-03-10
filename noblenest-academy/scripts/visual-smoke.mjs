import fs from 'node:fs/promises';
import path from 'node:path';
import { chromium } from 'playwright';

const baseUrl = process.env.APP_VISUAL_BASE_URL ?? 'http://127.0.0.1:8001';
const screenshotDir = path.join(process.cwd(), 'storage', 'app', 'ui-audit');
const pages = [
    { slug: 'home', url: `${baseUrl}/` },
    { slug: 'noble-home', url: `${baseUrl}/noble` },
    { slug: 'login', url: `${baseUrl}/login` },
    { slug: 'register', url: `${baseUrl}/register` },
    { slug: 'marketplace', url: `${baseUrl}/marketplace` },
];

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

    const summary = [];

    for (const pageConfig of pages) {
        const page = await context.newPage();
        const startedAt = Date.now();

        try {
            const response = await page.goto(pageConfig.url, { waitUntil: 'networkidle', timeout: 30000 });
            await page.screenshot({
                path: path.join(screenshotDir, `${pageConfig.slug}.png`),
                fullPage: true,
            });

            summary.push({
                page: pageConfig.slug,
                status: response?.status() ?? 0,
                title: await page.title(),
                durationMs: Date.now() - startedAt,
            });
        } finally {
            await page.close();
        }
    }

    await browser.close();
    await fs.writeFile(
        path.join(screenshotDir, 'summary.json'),
        `${JSON.stringify(summary, null, 2)}\n`,
        'utf8',
    );

    console.log(JSON.stringify(summary, null, 2));
}

main().catch((error) => {
    console.error(error);
    process.exitCode = 1;
});