// Extracts every __('…') / trans_choice('…') Spanish source string from the
// codebase and builds lang/en.json and lang/pt.json from the maps kept in
// scripts/translations/. Fails loudly if a string has no translation.
// Usage: node scripts/generate-translations.mjs [--check]
import { readFileSync, readdirSync, statSync, writeFileSync } from 'node:fs';
import { join } from 'node:path';

const ROOTS = ['app', 'resources/views'];
const LOCALES = ['en', 'pt'];

function phpFiles(dir) {
    return readdirSync(dir).flatMap((entry) => {
        const full = join(dir, entry);
        return statSync(full).isDirectory() ? phpFiles(full) : full.endsWith('.php') ? [full] : [];
    });
}

const keys = new Set();
const pattern = /(?:__|trans_choice)\(\s*'((?:[^'\\]|\\.)*)'/g;

for (const root of ROOTS) {
    for (const file of phpFiles(root)) {
        for (const match of readFileSync(file, 'utf8').matchAll(pattern)) {
            const key = match[1].replace(/\\'/g, "'");
            // Skip lang-file lookups like nexo.categories.x — only literal texts.
            if (!/^[a-z0-9_.-]+$/.test(key)) keys.add(key);
        }
    }
}

const sorted = [...keys].sort((a, b) => a.localeCompare(b, 'es'));
let failed = false;

for (const locale of LOCALES) {
    const map = JSON.parse(readFileSync(`scripts/translations/${locale}.json`, 'utf8'));
    const missing = sorted.filter((key) => !(key in map));
    const stale = Object.keys(map).filter((key) => !keys.has(key));

    if (missing.length > 0) {
        failed = true;
        console.error(`\n[${locale}] Falta traducir ${missing.length} strings:`);
        missing.forEach((key) => console.error(`  - ${key}`));
    }

    if (stale.length > 0) {
        console.warn(`\n[${locale}] ${stale.length} strings del mapa ya no se usan (limpiar cuando quieras):`);
        stale.forEach((key) => console.warn(`  - ${key}`));
    }

    if (!process.argv.includes('--check') && missing.length === 0) {
        const output = Object.fromEntries(sorted.map((key) => [key, map[key]]));
        writeFileSync(`lang/${locale}.json`, JSON.stringify(output, null, 4) + '\n');
        console.log(`[${locale}] lang/${locale}.json generado con ${sorted.length} strings.`);
    }
}

process.exit(failed ? 1 : 0);
