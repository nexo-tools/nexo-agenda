// Nexo brand-asset generator (PARAMETERIZED TEMPLATE).
// Derives favicons, touch icons, web-manifest icons and the Open Graph card
// from a single mark SVG. Copy this into a tool's `scripts/`, edit the CONFIG
// block for that tool, ensure `sharp` + `png-to-ico` are devDependencies, then:
//   node scripts/generate-brand-assets.mjs
//
// Canonical origin: ~/alvaro/templates/nexo-brand (extends nexo-links' original,
// now tool-parameterized with the Nexo violet OG card). The mark is the design
// source of truth (from templates/nexo-brand/marks/<tool>.svg).

import sharp from 'sharp';
import pngToIco from 'png-to-ico';
import { readFileSync, writeFileSync, copyFileSync } from 'node:fs';

// ---------------------------------------------------------------------------
// CONFIG — edit per tool.
const CONFIG = {
  mark: 'resources/brand/isotype.svg', // this tool's Nexo mark (copied from nexo-brand/marks)
  label: 'Nexo Agenda',                // wordmark shown on the OG card
  tagline: 'Reservas online para tu negocio.',
  publicDir: 'public',
};
// Brand constants (do not edit — from the Nexo palette).
const VIOLET_500 = '#8b5cf6';
const VIOLET_600 = '#7c3aed';
const INK = '#0b0b12';       // near-black violet-tinted OG background
const FG = '#f8fafc';        // slate-50
const MUTED = '#a5b4cf';     // muted slate/violet
const FONT = 'ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif';
// ---------------------------------------------------------------------------

const mark = readFileSync(CONFIG.mark);
const p = (f) => `${CONFIG.publicDir}/${f}`;

const sizes = {
  'favicon-16.png': 16,
  'favicon-32.png': 32,
  'apple-touch-icon.png': 180,
  'icon-192.png': 192,
  'icon-512.png': 512,
};

for (const [file, size] of Object.entries(sizes)) {
  await sharp(mark).resize(size, size).png().toFile(p(file));
  console.log(`✓ ${p(file)}`);
}

// Explicit sizes keep the .ico small (the default embeds a 256px layer).
writeFileSync(p('favicon.ico'), await pngToIco([p('favicon-16.png'), p('favicon-32.png')]));
console.log(`✓ ${p('favicon.ico')}`);

copyFileSync(CONFIG.mark, p('favicon.svg'));
console.log(`✓ ${p('favicon.svg')}`);

// Open Graph card (1200x630): the mark tile + wordmark + tagline on dark violet.
const markInner = mark.toString()
  .replace(/^[\s\S]*?<svg[^>]*>/, '')
  .replace(/<\/svg>\s*$/, '');

const og = `<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="630" viewBox="0 0 1200 630">
  <defs>
    <radialGradient id="glow" cx="0.18" cy="0.2" r="0.9">
      <stop offset="0" stop-color="${VIOLET_600}" stop-opacity="0.35"/>
      <stop offset="1" stop-color="${INK}" stop-opacity="0"/>
    </radialGradient>
  </defs>
  <rect width="1200" height="630" fill="${INK}"/>
  <rect width="1200" height="630" fill="url(#glow)"/>
  <svg x="90" y="150" width="150" height="150" viewBox="0 0 48 48">${markInner}</svg>
  <text x="270" y="270" font-family="${FONT}" font-size="84" font-weight="700" fill="${FG}">${CONFIG.label}</text>
  <text x="272" y="340" font-family="${FONT}" font-size="36" fill="${MUTED}">${CONFIG.tagline}</text>
  <text x="90" y="560" font-family="${FONT}" font-size="26" fill="${MUTED}">Part of the Nexo ecosystem · open source</text>
</svg>`;

await sharp(Buffer.from(og)).png().toFile(p('og-image.png'));
console.log(`✓ ${p('og-image.png')}`);
