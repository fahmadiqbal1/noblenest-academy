import fs from 'node:fs/promises';
import path from 'node:path';
import sharp from 'sharp';
import pngToIco from 'png-to-ico';

const projectRoot = process.cwd();
const sourceFile = path.join(projectRoot, 'public', 'brand', 'noblenest-logo.svg');
const brandName = 'NobleNest Global Academy';
const brandSubtitle = 'Family-first learning, beautifully delivered';
const outputs = [
    { file: 'favicon-16x16.png', size: 16 },
    { file: 'favicon-32x32.png', size: 32 },
    { file: 'apple-touch-icon.png', size: 180 },
    { file: 'android-chrome-192x192.png', size: 192 },
    { file: 'android-chrome-512x512.png', size: 512 },
];

function asDataUri(buffer, mimeType) {
        return `data:${mimeType};base64,${buffer.toString('base64')}`;
}

function socialCardSvg(logoDataUri, heading, subheading, eyebrow) {
        return `
<svg width="1200" height="630" viewBox="0 0 1200 630" fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect width="1200" height="630" rx="36" fill="#F4EFE7"/>
    <rect x="24" y="24" width="1152" height="582" rx="30" fill="url(#panel)"/>
    <circle cx="1088" cy="120" r="146" fill="#F2A541" fill-opacity="0.12"/>
    <circle cx="109" cy="570" r="170" fill="#0D5C63" fill-opacity="0.10"/>
    <circle cx="1000" cy="540" r="110" fill="#0D5C63" fill-opacity="0.10"/>
    <rect x="76" y="90" width="170" height="170" rx="36" fill="#FFFFFF" fill-opacity="0.82"/>
    <image href="${logoDataUri}" x="87" y="101" width="148" height="148"/>
    <text x="286" y="168" fill="#0D5C63" font-size="72" font-weight="800" font-family="Manrope, Arial, sans-serif">NobleNest</text>
    <text x="289" y="220" fill="#A56E4D" font-size="36" font-weight="700" font-family="Manrope, Arial, sans-serif" letter-spacing="2">Global Academy</text>
    <text x="82" y="320" fill="#0D5C63" font-size="26" font-weight="800" font-family="Manrope, Arial, sans-serif" letter-spacing="2">${eyebrow}</text>
    <text x="82" y="396" fill="#18222F" font-size="70" font-weight="800" font-family="Space Grotesk, Manrope, Arial, sans-serif">${heading}</text>
    <text x="82" y="472" fill="#18222F" font-size="70" font-weight="800" font-family="Space Grotesk, Manrope, Arial, sans-serif">${subheading}</text>
    <text x="82" y="540" fill="#5F6C7B" font-size="30" font-weight="500" font-family="Manrope, Arial, sans-serif">Courses, onboarding, AI guidance, and role-aware learning journeys in one experience.</text>
    <rect x="82" y="538" width="316" height="52" rx="26" fill="#0D5C63" fill-opacity="0.08"/>
    <text x="114" y="572" fill="#0D5C63" font-size="24" font-weight="700" font-family="Manrope, Arial, sans-serif">noblenest.global</text>
    <defs>
        <linearGradient id="panel" x1="64" y1="42" x2="1106" y2="618" gradientUnits="userSpaceOnUse">
            <stop stop-color="#FFFDF9"/>
            <stop offset="1" stop-color="#EEF4F6"/>
        </linearGradient>
    </defs>
</svg>`;
}

async function main() {
    const svgBuffer = await fs.readFile(sourceFile);
    const publicDir = path.join(projectRoot, 'public');

    for (const output of outputs) {
        const targetFile = path.join(publicDir, output.file);
        await sharp(svgBuffer)
            .resize(output.size, output.size)
            .png()
            .toFile(targetFile);
    }

    const icoBuffer = await pngToIco([
        path.join(publicDir, 'favicon-16x16.png'),
        path.join(publicDir, 'favicon-32x32.png'),
    ]);

    await fs.writeFile(path.join(publicDir, 'favicon.ico'), icoBuffer);

    const logoDataUri = asDataUri(svgBuffer, 'image/svg+xml');
    const socialCardBuffer = Buffer.from(
        socialCardSvg(
            logoDataUri,
            'Family-first learning,',
            'beautifully delivered.',
            'Adaptive learning platform',
        ),
    );

    await sharp(socialCardBuffer)
        .png()
        .toFile(path.join(publicDir, 'og-image.png'));

    await sharp(socialCardBuffer)
        .resize(1600, 900)
        .png()
        .toFile(path.join(publicDir, 'social-preview.png'));

    const pageCards = [
        {
            file: 'og-home.png',
            heading: 'Family-first learning,',
            subheading: 'beautifully delivered.',
            eyebrow: 'Adaptive learning platform',
        },
        {
            file: 'og-marketplace.png',
            heading: 'Find expert teachers',
            subheading: 'and live courses.',
            eyebrow: 'Marketplace',
        },
        {
            file: 'og-login.png',
            heading: 'Return to your',
            subheading: 'learning workspace.',
            eyebrow: 'Secure access',
        },
        {
            file: 'og-register.png',
            heading: 'Create your account',
            subheading: 'and get started.',
            eyebrow: 'Students, parents, teachers, admins',
        },
    ];

    for (const pageCard of pageCards) {
        await sharp(Buffer.from(socialCardSvg(logoDataUri, pageCard.heading, pageCard.subheading, pageCard.eyebrow)))
            .png()
            .toFile(path.join(publicDir, pageCard.file));
    }

    const manifest = {
        name: brandName,
        short_name: 'NobleNest',
        description: brandSubtitle,
        icons: [
            {
                src: '/android-chrome-192x192.png',
                sizes: '192x192',
                type: 'image/png',
            },
            {
                src: '/android-chrome-512x512.png',
                sizes: '512x512',
                type: 'image/png',
            },
        ],
        theme_color: '#0d5c63',
        background_color: '#f4efe7',
        display: 'standalone',
    };

    await fs.writeFile(
        path.join(publicDir, 'site.webmanifest'),
        `${JSON.stringify(manifest, null, 2)}\n`,
        'utf8',
    );

        const browserConfig = `<?xml version="1.0" encoding="utf-8"?>
<browserconfig>
    <msapplication>
        <tile>
            <square150x150logo src="/android-chrome-192x192.png"/>
            <TileColor>#0d5c63</TileColor>
        </tile>
    </msapplication>
</browserconfig>
`;

        await fs.writeFile(path.join(publicDir, 'browserconfig.xml'), browserConfig, 'utf8');

    console.log('Brand asset set generated successfully.');
}

main().catch((error) => {
    console.error(error);
    process.exitCode = 1;
});