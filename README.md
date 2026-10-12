# RenKit — Laravel Development Toolkit

RenKit is a lightweight, modern, and developer-friendly Laravel toolkit and module generator created by **RendyAFS**.

Inspired by Filament's ergonomics, but tailored for pure Tailwind CSS, customizable Blade component wrappers, optional multi-language localization, and seamless hybrid client-side architectures.

---

## Features
- **Instant CRUD Generator**: Generate complete, functional CRUD modules in seconds (Model, Migration, Controller, Request, Resource, Views, Scripts, Route, Menu).
- **Two CRUD Flavors**:
  - `--simple`: Rapid, single-page feel Modal CRUD (Preline overlay + client-side DataTable or CardGrid).
  - `--resource`: Robust Full Page CRUD with dedicated Action classes, separate create/edit views, and full form flow.
- **Two UI Layout Options**:
  - `--datatable`: Tabular data presentation with search, filter, and pagination (`<x-datatable>`).
  - `--cardgrid`: Modern responsive card grid layout with live search, filters, and dynamic card rendering (`<x-cardgrid>`).
- **Tailwind CSS & Publishable Theme Palette**: Clean Tailwind CSS utility classes styled via `:root` CSS variables (`--color-primary`, `--color-dark`, etc.).
- **Pluggable UI Component Wrappers**: `<x-modal>`, `<x-datatable>`, `<x-cardgrid>`, `<x-button-loading>`, and `<x-custom-alert>`.
- **Optional Multi-Language Localization (i18n)**: UI components and generators adapt automatically to the active locale (`id` / `en`) with reliable, zero-config fallbacks.
- **Modular Route Isolation**: Automatically generates and registers routes in `routes/web/{module}.php`.
- **Automatic Menu Integration**: Safely integrates new modules into `config/menu.php` with Spatie Permission compatibility.
- **Environment Doctor**: Built-in `php artisan renkit:doctor` to verify project readiness.

---

## Installation

```bash
composer require rendyafs/renkit
php artisan renkit:install
```

During installation, RenKit provides an interactive prompt asking whether to activate **Multi-Language Localization** (`lang/id` & `lang/en`):
```text
Would you like to enable Multi-Language Localization (lang/id and lang/en)? [yes/no]
```
You can also pass non-interactive flags:
- `php artisan renkit:install --lang`: Activate localization and auto-publish starter dictionaries.
- `php artisan renkit:install --no-lang`: Keep localization disabled in `config/renkit.php`.

---

## Artisan Commands & Interactive Workflow

When creating a module via `php artisan renkit:module {Name}` without flags, RenKit guides you through a two-step interactive prompt:

1. **Step 1: Choose CRUD Architecture Mode**
   - `simple` — Simple Modal CRUD (Modal overlay + form)
   - `resource` — Full Page CRUD (Separate create/edit pages + Action class)
2. **Step 2: Choose View UI Layout**
   - `datatable` — DataTable (`<x-datatable>`, Tabular list with live search & filters)
   - `cardgrid` — CardGrid (`<x-cardgrid>`, Responsive cards with live search & filters)

```bash
# Interactive generator
php artisan renkit:module Product

# Or specify flags directly to bypass prompts
php artisan renkit:module Product --simple --datatable
php artisan renkit:module Product --simple --cardgrid
php artisan renkit:module Product --resource --datatable
php artisan renkit:module Product --resource --cardgrid
php artisan renkit:module Tag --simple --no-lang
```

| Command | Description |
| :--- | :--- |
| `php artisan renkit:about` | Show current RenKit version, configuration, and enabled features. |
| `php artisan renkit:doctor` | Run health checks on dependencies, configs, and environment. |
| `php artisan renkit:install` | Run setup wizard (prompts for localization preference and publishes config). |
| `php artisan renkit:publish` | Publish theme colors, Blade components, JS utilities, stubs, or lang files. |
| `php artisan renkit:module User` | Interactively select CRUD mode (Simple/Resource) then UI layout (DataTable/CardGrid). |
| `php artisan renkit:module User --simple` | Generate modal-based CRUD with default DataTable. |
| `php artisan renkit:module Workshop --simple --cardgrid` | Generate modal-based CRUD with CardGrid view (`<x-cardgrid>`). |
| `php artisan renkit:module Role --resource` | Generate full-page CRUD with dedicated Action class. |
| `php artisan renkit:module Product --resource --cardgrid` | Generate full-page CRUD with CardGrid layout. |
| `php artisan renkit:module Tag --simple --no-lang` | Generate module skipping translation generation. |
| `php artisan renkit:module Audit --simple --no-menu` | Generate module without registering in `config/menu.php`. |

---

## Multi-Language Localization (Optional)

Localization in RenKit is designed to be **completely optional and non-intrusive**. If localization is enabled, RenKit components and generated modules automatically adapt to the application's locale (`App::getLocale()`). If disabled or if translation keys are missing, safe fallback labels (Indonesian/English) are displayed automatically.

### 1. Publishing Language Files

Publish the starter translations to your application's `lang/` directory:

```bash
php artisan renkit:publish --lang
```

This will publish:
- `lang/id/renkit.php` & `lang/en/renkit.php` (Core RenKit dictionary)
- `lang/id/datatable.php` & `lang/en/datatable.php`
- `lang/id/cardgrid.php` & `lang/en/cardgrid.php`
- `lang/id/button-loading.php` & `lang/en/button-loading.php`
- `lang/id/custom-alert.php` & `lang/en/custom-alert.php`
- `lang/id/crud.php` & `lang/en/crud.php`

### 2. How UI Components Adapt Dynamically

Every RenKit component checks the active translation files and falls back gracefully:

- **`<x-datatable>`**: Search input placeholder, filter dropdowns ('Semua'/'All', 'Aktif'/'Active', 'Terhapus'/'Deleted').
- **`<x-cardgrid>`**: Search input, card count info, loading overlay ("Memuat data..." / "Loading data..."), empty state ("Tidak ada data ditemukan" / "No data found"), and pagination navigation buttons.
- **`<x-button-loading>`**: Default button label ("Simpan" / "Save") and loading spinner state ("Menyimpan..." / "Saving...").
- **`<x-custom-alert>`**: Toast titles ("Sukses", "Peringatan", "Error"), delete confirmations, and modal prompt buttons.
- **`<x-modal>`**: Close button accessibility labels and dialog titles.

### 3. Module Generator Translations

When running `php artisan renkit:module {Name}`, RenKit automatically:
1. Appends model labels to `lang/id/models.php` and `lang/en/models.php` (e.g. `'Role' => 'Role'`).
2. Creates dedicated module translation files `lang/id/{name}.php` and `lang/en/{name}.php` with field labels and descriptions.
3. Can be skipped anytime by passing `--no-lang`.

To turn off automatic localization globally, set `RENKIT_LOCALIZATION=false` in `.env` or edit `config/renkit.php`:

```php
'features' => [
    'localization' => env('RENKIT_LOCALIZATION', false),
],
```

---

## Customizing Theme Colors & UI Components

RenKit allows you to publish any part of its UI system for full customization:

```bash
# 1. Publish Theme Colors (CSS Variables) & Loading animation
php artisan renkit:publish --theme

# 2. Publish Blade UI Components (<x-modal>, <x-button-loading>, <x-custom-alert>, <x-datatable>, <x-cardgrid>)
php artisan renkit:publish --components

# 3. Publish JavaScript Utilities (api-provider.js, button-loading.js, alert, loading, cardgrid.js)
php artisan renkit:publish --utils

# 4. Publish Generator Stubs for custom code generation templates
php artisan renkit:publish --stubs

# 5. Publish Localization Files (lang/id and lang/en)
php artisan renkit:publish --lang

# Or publish everything at once:
php artisan renkit:publish --all
```

### Theme Colors (`resources/css/theme.css`)
After publishing, you can adjust the palette in `resources/css/theme.css`:
```css
:root {
    --color-primary: #6d9886;       /* Main brand color */
    --color-light-primary: #b0dbc9;
    --color-secondary: #f2e7d5;
    --color-dark: #282b31;
    --color-light: #f7f7f7;
    --color-danger: #d44e4e;
    --color-success: #66a76b;
    /* ... */
}
```
In Tailwind CSS v4, these are instantly available across your classes:
`bg-(--color-primary)`, `text-(--color-dark)`, `hover:bg-(--color-primary)/80`, etc.
Make sure your `resources/css/app.css` includes `@import "./theme.css";`.

---

## Configuration (`config/renkit.php`)

```php
return [
    'ui' => [
        'framework' => env('RENKIT_UI_FRAMEWORK', 'preline'),
        'use_component_wrappers' => true,
        'datatable_client_side' => true,
    ],

    'features' => [
        'localization' => env('RENKIT_LOCALIZATION', true),
        'theme' => env('RENKIT_THEME', true),
        'permission' => env('RENKIT_PERMISSION', true),
        'menu' => env('RENKIT_MENU_INTEGRATION', true),
    ],

    'localization' => [
        'locales' => ['id', 'en'],
        'default_locale' => env('RENKIT_LOCALE', 'id'),
        'auto_translate_models' => true,
    ],

    'generator' => [
        'default_mode' => 'simple', // 'simple' or 'resource'
        'generate_migration' => true,
        'generate_model' => true,
        'use_soft_deletes' => true,
        'use_userstamps' => true,
    ],
];
```

---

## Author
- **RendyAFS** — [GitHub](https://github.com/RendyAFS)

## License
MIT License.
