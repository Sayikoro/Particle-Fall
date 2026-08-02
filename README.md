# 🎆 Particle Fall

> Falling particle animation for any block by CSS class. WordPress plugin with Elementor support.

<p align="center">
  <img src=".github/preview.png" alt="Particle Fall — WordPress Plugin" width="800">
</p>

---

## Features

- **Canvas particle system** — smooth `requestAnimationFrame` animation with drift, wobble, rotation, and opacity
- **File-based presets** — drop SVG files into `particles/` folder, they appear automatically. Subdirectories = groups
- **presets.json metadata** — optional i18n labels and custom colors per preset
- **Color tinting** — pick any color, particles get tinted via `source-atop` compositing on canvas
- **Live preview** — real-time canvas preview in the admin panel. Change any setting and see the result instantly
- **Dark / Light theme** — toggle with one click, preference saved per-user
- **Bilingual (RU / EN)** — auto-detects browser locale, manual switcher in header
- **Recent particles** — last 12 used particles saved in localStorage for quick access
- **SVG support** — upload SVGs via WordPress media library, safe Blob URL rendering, XSS sanitization
- **Elementor compatible** — just add a CSS class in section → Advanced → CSS Classes
- **Tailwind CSS** — zero custom CSS, entire admin UI via Tailwind CDN (preflight disabled)
- **MutationObserver** — detects dynamically added Elementor content
- **DPR-aware** — crisp rendering on Retina displays (capped at 2x)



## Built-in Presets

| Group | Particles |
|-------|-----------|
| **New Year** | Snowflake, Star |
| **Summer** | Sun, Flower |
| **Autumn** | Leaf, Acorn |
| **Halloween** | Pumpkin, Bat |

---

## Adding Custom Presets

### Quick way

Drop any SVG file into the `particles/` folder:

```
particles/
  my-particle.svg          → shows as "My Particle" (auto-named)
  seasonal/
    hearts.svg              → group "Seasonal", label "Hearts"
```

### With metadata

Create or edit `particles/presets.json`:

```json
{
  "my-particle": {
    "label": { "ru": "Моя частица", "en": "My Particle" },
    "group": { "ru": "Особые", "en": "Custom" },
    "color": "#ff6600"
  },
  "seasonal/hearts": {
    "label": { "ru": "Сердца", "en": "Hearts" },
    "group": { "ru": "Сезонные", "en": "Seasonal" },
    "color": "#e91e63"
  }
}
```

All fields are optional. Missing labels are auto-generated from the filename.

---

## Settings

| Setting | Range | Default | Description |
|---------|-------|---------|-------------|
| CSS Class | text | — | Class name without the dot |
| Preset | dropdown | — | Built-in or custom SVG preset |
| Image | media | — | Custom uploaded image (PNG, GIF, WebP, SVG) |
| Color | hex | — | Tints particles via canvas compositing |
| Count | 5–500 | 50 | Number of particles on screen |
| Speed | 0.5–15 | 3 | Fall speed |
| Size | 2–150 px | 20 | Particle size (with random variation) |

---

## File Structure

```
particle-fall/
├── particle-fall.php          # Main plugin — i18n, presets scanner, admin UI, SVG hooks
├── assets/
│   ├── js/
│   │   ├── admin.js           # Admin — tabs, recent, color picker, live preview, theme toggle
│   │   └── particle-fall.js   # Frontend — canvas particle system, MutationObserver, DPR
│   └── css/
│       └── admin.css          # Empty — all styling via Tailwind CDN
└── particles/
    ├── presets.json            # Optional metadata (i18n labels, groups, colors)
    ├── new-year/
    │   ├── snowflake.svg
    │   └── star.svg
    ├── summer/
    │   ├── sun.svg
    │   └── flower.svg
    ├── autumn/
    │   ├── leaf.svg
    │   └── acorn.svg
    └── halloween/
        ├── pumpkin.svg
        └── bat.svg
```

---

## Technical Details

### Particle System
- **Rendering**: HTML5 Canvas 2D with `requestAnimationFrame`
- **Physics**: per-particle speed, drift (horizontal), wobble (sinusoidal), rotation, opacity
- **Color tinting**: `globalCompositeOperation = 'source-atop'` — draws a color overlay only on existing pixels
- **SVG loading**: `fetch()` → `Blob` → `URL.createObjectURL()` to avoid crossOrigin tainting
- **DPR**: `Math.min(devicePixelRatio, 2)` for crisp rendering without performance hit
- **Dynamic content**: `MutationObserver` watches for Elementor-added elements matching the CSS class

### SVG Security
- Upload mime type whitelisted (`image/svg+xml`)
- `wp_check_filetype_and_ext` filter for proper type detection
- On upload: strips `<script>`, `<?xml?>`, `on*` event handlers, `javascript:`, and dangerous HTML elements (`<iframe>`, `<object>`, `<embed>`, etc.)

### Admin Panel
- **Styling**: Tailwind CSS CDN with `preflight: false` (preserves WP admin styles)
- **Dark mode**: Tailwind `darkMode: 'class'` on root container
- **i18n**: embedded RU/EN string maps (no .po files), auto-detected from `get_user_locale()`, AJAX switcher
- **Color picker**: `wp-color-picker` (Iris)
- **Recent particles**: `localStorage` key `pf_recent`, max 12 items
- **Theme preference**: stored in `wp_options` as `pf_theme`
- **Locale preference**: stored in `wp_options` as `pf_locale`

---

## Requirements

- WordPress 5.0+
- PHP 7.4+
- Modern browser (Canvas API, ES6+)

---

## Changelog

### v1.4.0
- Dark / Light theme toggle with smooth transitions
- Theme preference persisted in database
- Preview canvas always stays dark for better visibility

### v1.3.1
- Fixed range slider `px` suffix rendering bug
- Added collapsible donation banner (DonationAlerts + crypto)

### v1.3.0
- File-based preset auto-discovery via `RecursiveDirectoryIterator`
- `presets.json` for i18n metadata overlay
- Bilingual admin interface (RU / EN) with auto-detection
- "Recent" tab — last 12 particles in localStorage
- Migrated to Tailwind CSS (zero custom CSS)

### v1.1.0
- SVG upload support with XSS sanitization
- Color tinting via `source-atop` compositing
- Live preview canvas in admin panel

### v1.0.0
- Initial release — canvas particle system, PNG/GIF/WebP support, Elementor compatible

---

## License

MIT

---

<p align="center">
  Made with <a href="https://z.ai">Z.ai</a>
</p>
