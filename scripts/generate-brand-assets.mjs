// Regenerates every brand asset from resources/brand/isotype.svg.
// Usage: node scripts/generate-brand-assets.mjs
import { copyFile, mkdir, readFile, writeFile } from 'node:fs/promises';
import pngToIco from 'png-to-ico';
import sharp from 'sharp';

const BRAND = '#0d9488';
const INK = '#0f172a';
const SURFACE = '#f8fafc';
const SRC = 'resources/brand/isotype.svg';

const render = (svg, size, opts = {}) =>
    sharp(Buffer.isBuffer(svg) ? svg : Buffer.from(svg), { density: 300 })
        .resize(size, size, { fit: 'contain', background: opts.background ?? { r: 0, g: 0, b: 0, alpha: 0 } })
        .png();

const isotype = await readFile(SRC);

// A padded variant for opaque icons (apple-touch, maskable): mark at 62% on brand bg.
const padded = (background, scale = 0.62) => `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
  <rect width="64" height="64" fill="${background}"/>
  <g transform="translate(${(64 * (1 - scale)) / 2} ${(64 * (1 - scale)) / 2}) scale(${scale})">
    ${isotype.toString().replace(/<\/?svg[^>]*>/g, '').replaceAll(BRAND, '#ffffff')}
  </g>
</svg>`;

await mkdir('public/icons', { recursive: true });
await mkdir('public/og', { recursive: true });

await copyFile(SRC, 'public/favicon.svg');
await render(isotype, 192).toFile('public/icons/icon-192.png');
await render(isotype, 512).toFile('public/icons/icon-512.png');
await render(padded(BRAND), 512).toFile('public/icons/icon-maskable-512.png');
await render(padded(BRAND, 0.7), 180).toFile('public/apple-touch-icon.png');

const favPngs = await Promise.all([16, 32, 48].map((s) => render(isotype, s).toBuffer()));
await writeFile('public/favicon.ico', await pngToIco(favPngs));

const og = `
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="630" viewBox="0 0 1200 630">
  <rect width="1200" height="630" fill="${SURFACE}"/>
  <rect x="0" y="618" width="1200" height="12" fill="${BRAND}"/>
  <g transform="translate(120 195) scale(3.75)">${isotype.toString().replace(/<\/?svg[^>]*>/g, '')}</g>
  <text x="420" y="300" font-family="-apple-system, 'Helvetica Neue', Arial, sans-serif"
        font-size="88" font-weight="700" fill="${INK}">Nexo Agenda</text>
  <text x="420" y="380" font-family="-apple-system, 'Helvetica Neue', Arial, sans-serif"
        font-size="40" fill="#475569">Reservas online para tu negocio</text>
</svg>`;
await sharp(Buffer.from(og), { density: 96 }).png().toFile('public/og/og-default.png');

console.log('Brand assets regenerated.');
