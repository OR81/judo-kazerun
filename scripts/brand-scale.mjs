/**
 * Regenerate the cobalt/ochre ramps in resources/css/app.css.
 *
 * Tailwind v4 retuned its palette for wider gamut (blue-700 is #1447e6, amber-600
 * is #e17100), so neither matches the brand spec. This borrows Tailwind's ramp
 * *shape* and retargets it so cobalt-600 === #1D4ED8 and ochre-500 === #D97706.
 *
 *   node scripts/brand-scale.mjs
 */
import { readFileSync } from 'node:fs';

const THEME = process.argv[2] ?? 'node_modules/tailwindcss/theme.css';
const theme = readFileSync(THEME, 'utf8');
const STEPS = [50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950];

function readRamp(name) {
    return STEPS.map((s) => {
        const m = theme.match(
            new RegExp(`--color-${name}-${s}:\\s*oklch\\(([\\d.]+)%\\s+([\\d.]+)\\s+([\\d.]+)\\)`),
        );
        if (!m) throw new Error(`missing --color-${name}-${s} in ${THEME}`);
        return [parseFloat(m[1]) / 100, parseFloat(m[2]), parseFloat(m[3])];
    });
}

const srgbToLin = (c) => (c <= 0.04045 ? c / 12.92 : ((c + 0.055) / 1.055) ** 2.4);
const linToSrgb = (c) => (c <= 0.0031308 ? 12.92 * c : 1.055 * c ** (1 / 2.4) - 0.055);

function hexToOklch(hex) {
    const [r, g, b] = [1, 3, 5].map((i) => srgbToLin(parseInt(hex.slice(i, i + 2), 16) / 255));
    const l = Math.cbrt(0.4122214708 * r + 0.5363325363 * g + 0.0514459929 * b);
    const m = Math.cbrt(0.2119034982 * r + 0.6806995451 * g + 0.1073969566 * b);
    const s = Math.cbrt(0.0883024619 * r + 0.2817188376 * g + 0.6299787005 * b);
    const L = 0.2104542553 * l + 0.793617785 * m - 0.0040720468 * s;
    const A = 1.9779984951 * l - 2.428592205 * m + 0.4505937099 * s;
    const B = 0.0259040371 * l + 0.7827717662 * m - 0.808675766 * s;
    let H = (Math.atan2(B, A) * 180) / Math.PI;
    if (H < 0) H += 360;
    return [L, Math.hypot(A, B), H];
}

function oklchToRgb([L, C, H]) {
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
    ];
}

const inGamut = (c) => c.every((v) => v >= -0.001 && v <= 1.001);

/** Pull chroma down until the colour fits inside sRGB at this lightness. */
function fit([L, C, H]) {
    if (inGamut(oklchToRgb([L, C, H]))) return [L, C, H];
    let lo = 0;
    let hi = C;
    for (let i = 0; i < 40; i++) {
        const mid = (lo + hi) / 2;
        if (inGamut(oklchToRgb([L, mid, H]))) lo = mid;
        else hi = mid;
    }
    return [L, lo, H];
}

const hex = (rgb) =>
    '#' + rgb.map((v) => Math.round(Math.min(1, Math.max(0, v)) * 255).toString(16).padStart(2, '0')).join('');

function retarget(rampName, brandHex, anchorStep) {
    const ramp = readRamp(rampName);
    const k = STEPS.indexOf(anchorStep);
    const [La, Ca, Ha] = hexToOklch(brandHex);
    const [Lk, Ck, Hk] = ramp[k];

    const dL = La - Lk;
    const kC = Ca / Ck;
    const dH = Ha - Hk;
    const maxD = Math.max(k, STEPS.length - 1 - k);

    return ramp.map(([L, C, H], i) => {
        // The lightness shift tapers away from the anchor so the ends stay put.
        const w = 1 - Math.abs(i - k) / (maxD + 1);
        const out = fit([Math.min(0.995, Math.max(0.06, L + dL * w)), C * kC, H + dH]);
        return {
            step: STEPS[i],
            hex: hex(oklchToRgb(out)),
            css: `oklch(${(out[0] * 100).toFixed(1)}% ${out[1].toFixed(3)} ${out[2].toFixed(3)})`,
        };
    });
}

let failed = false;
for (const [name, source, brand, anchor] of [
    ['cobalt', 'blue', '#1d4ed8', 600],
    ['ochre', 'amber', '#d97706', 500],
]) {
    const ramp = retarget(source, brand, anchor);
    const got = ramp.find((r) => r.step === anchor).hex;
    if (got !== brand) failed = true;
    console.log(`\n  /* ${name} — anchor ${anchor} = ${got} ${got === brand ? '(exact)' : `MISMATCH want ${brand}`} */`);
    for (const r of ramp) {
        const note = r.step === anchor ? ` /* ${brand.toUpperCase()} — brand */` : '';
        console.log(`  --color-${name}-${r.step}: ${r.css};${note}`);
    }
}

if (failed) {
    console.error('\nAnchor did not reproduce the brand hex exactly.');
    process.exit(1);
}
