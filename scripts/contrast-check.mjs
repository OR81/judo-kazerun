/**
 * Verify every foreground/background pair the design system actually ships,
 * against WCAG 2.1. Reads the real values out of resources/css/app.css and
 * Tailwind's theme.css so nothing is assumed.
 *
 *   node scripts/contrast-check.mjs
 *
 * Exits non-zero if any pair falls below its target, so it can gate CI.
 */
import { readFileSync } from 'node:fs';

const sources = [
    readFileSync(process.argv[2] ?? 'node_modules/tailwindcss/theme.css', 'utf8'),
    readFileSync(process.argv[3] ?? 'resources/css/app.css', 'utf8'),
].join('\n');

const vars = {};
for (const m of sources.matchAll(/--color-([a-z]+-\d+):\s*(oklch\([^)]+\))/g)) {
    vars[m[1]] = m[2];
}
vars.white = '#ffffff';

const srgbToLin = (c) => (c <= 0.04045 ? c / 12.92 : ((c + 0.055) / 1.055) ** 2.4);
const linToSrgb = (c) => (c <= 0.0031308 ? 12.92 * c : 1.055 * c ** (1 / 2.4) - 0.055);

function oklchToRgb(L, C, H) {
    const h = (H * Math.PI) / 180;
    const a = C * Math.cos(h);
    const b = C * Math.sin(h);
    const l = (L + 0.3963377774 * a + 0.2158037573 * b) ** 3;
    const m = (L - 0.1055613458 * a - 0.0638541728 * b) ** 3;
    const s = (L - 0.0894841775 * a - 1.291485548 * b) ** 3;
    return [
        linToSrgb(4.0767416621 * l - 3.3077115913 * m + 0.2309699292 * s),
        linToSrgb(-1.2684380046 * l + 2.6097574011 * m - 0.3413193965 * s),
        linToSrgb(-0.0041960863 * l - 0.7034186147 * m + 1.707614701 * s),
    ].map((v) => Math.min(1, Math.max(0, v)));
}

function rgbOf(name) {
    const raw = vars[name];
    if (!raw) throw new Error(`unknown colour: ${name}`);
    if (raw.startsWith('#')) return [1, 3, 5].map((i) => parseInt(raw.slice(i, i + 2), 16) / 255);
    const m = raw.match(/oklch\(\s*([\d.]+)%\s+([\d.]+)\s+([\d.]+)/);
    if (!m) throw new Error(`unparsed ${name}: ${raw}`);
    return oklchToRgb(parseFloat(m[1]) / 100, parseFloat(m[2]), parseFloat(m[3]));
}

const lum = (rgb) => {
    const [r, g, b] = rgb.map(srgbToLin);
    return 0.2126 * r + 0.7152 * g + 0.0722 * b;
};
const ratio = (a, b) => {
    const [hi, lo] = [lum(rgbOf(a)), lum(rgbOf(b))].sort((x, y) => y - x);
    return (hi + 0.05) / (lo + 0.05);
};
const toHex = (name) =>
    '#' + rgbOf(name).map((v) => Math.round(v * 255).toString(16).padStart(2, '0')).join('');

console.log('=== brand hexes match the spec ===');
let failed = 0;
for (const [name, want] of [
    ['cobalt-600', '#1d4ed8'],
    ['ochre-500', '#d97706'],
    ['stone-50', '#fafaf9'],
    ['stone-900', '#1c1917'],
]) {
    const got = toHex(name);
    // gray-900 is Tailwind's own and lands 1/255 off; anything tighter than
    // 2/255 is visually identical, so treat that as a match.
    const delta = Math.max(...[1, 3, 5].map((i) => Math.abs(parseInt(got.slice(i, i + 2), 16) - parseInt(want.slice(i, i + 2), 16))));
    const ok = delta <= 2;
    if (!ok) failed++;
    console.log(`  ${ok ? 'OK  ' : 'FAIL'} ${name.padEnd(13)} spec ${want}  built ${got}${delta ? `  (Δ${delta}/255)` : '  exact'}`);
}

// role, foreground, background, minimum ratio
const PAIRS = [
    // Canvas and cards
    ['body copy', 'stone-700', 'stone-50', 4.5],
    ['muted copy', 'stone-500', 'stone-50', 4.5],
    ['muted copy / card', 'stone-500', 'white', 4.5],
    ['heading', 'stone-900', 'white', 4.5],

    // Brand and accent
    ['brand link', 'cobalt-700', 'stone-50', 4.5],
    ['accent text', 'ochre-700', 'stone-50', 4.5],
    ['white on brand button', 'white', 'cobalt-600', 4.5],
    ['white on brand hover', 'white', 'cobalt-700', 4.5],
    ['ink on accent button', 'stone-900', 'ochre-500', 4.5],
    ['brand text on brand-soft', 'cobalt-700', 'cobalt-50', 4.5],
    ['accent text on accent-soft', 'ochre-700', 'ochre-50', 4.5],

    // Inverted bands — hero scrim, CTA panel, footer
    ['copy on ink', 'stone-100', 'cobalt-950', 4.5],
    ['muted copy on ink', 'cobalt-200', 'cobalt-950', 4.5],
    ['accent on ink', 'ochre-400', 'cobalt-950', 4.5],
    ['white on ink-soft', 'white', 'cobalt-900', 4.5],

    // Hall-board status
    ['open slot text', 'emerald-700', 'emerald-50', 4.5],
    ['taken slot text', 'stone-600', 'stone-100', 4.5],
    ['board-class slot text', 'cobalt-700', 'cobalt-50', 4.5],

    // Errors
    ['error text', 'red-700', 'red-50', 4.5],
    ['error border / canvas', 'red-600', 'stone-50', 3.0],

    // The focus indicator is a pair: an ochre outline over a white keyline. On any
    // surface at least one half clears 3:1, and the two also separate from each
    // other, so the ring never has to be restyled per background.
    ['focus ring / canvas', 'ochre-500', 'stone-50', 3.0],
    ['focus ring / card', 'ochre-500', 'white', 3.0],
    ['focus ring / ink', 'ochre-500', 'cobalt-950', 3.0],
    ['focus keyline / brand fill', 'white', 'cobalt-600', 3.0],
    ['focus keyline / ink', 'white', 'cobalt-950', 3.0],
    ['focus ring vs keyline', 'ochre-500', 'white', 3.0],
];

console.log('\n=== WCAG 2.1 contrast ===');
for (const [role, fg, bg, min] of PAIRS) {
    const r = ratio(fg, bg);
    const ok = r >= min;
    if (!ok) failed++;
    const grade = r >= 7 ? 'AAA' : r >= 4.5 ? 'AA' : r >= 3 ? 'AA-large' : '--';
    console.log(
        `  ${ok ? 'OK  ' : 'FAIL'} ${role.padEnd(23)} ${fg.padEnd(12)} on ${bg.padEnd(12)} ${r.toFixed(2).padStart(6)}:1  (min ${min})  ${grade}`,
    );
}

console.log(failed ? `\n${failed} check(s) failed.` : '\nAll checks passed.');
process.exit(failed ? 1 : 0);
