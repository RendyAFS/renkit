# RenKit — Laravel Development Toolkit

RenKit is a lightweight, modern, and developer-friendly Laravel toolkit and module generator created by **RendyAFS**.

Inspired by Filament's ergonomics and enterprise architecture patterns (Preline UI & Tailwind CSS v4), RenKit provides rapid CRUD generation with database migration introspection, dynamic form fields, interactive replacement, full application shells (sidebar, navbar, floating menu), and optional authentication scaffolding.

---

## 🚀 Key Features

- **Filament-Style Migration Introspection**: RenKit inspects your target table's database migration or live schema:
  - Generates form fields matched to column types (`text`, `email`, `password with eye toggle`, `number`, `textarea`, `switch/toggle`, `datepicker`, `<x-select>`).
  - Generates DataTable headers and columns automatically.
  - Automatically populates Model `$fillable`, `$casts`, and FormRequest validation rules.
  - Generates minimal/empty tables and forms when columns have not been defined yet.
- **Interactive Schema Update & Replacement**:
  - Update your migration and model later, re-run `php artisan renkit:module {Name}`, and RenKit prompts to update and replace the module files with the latest schema!
- **Complete Application Shell**:
  - Full modern layouts (`layouts/main.blade.php`, `layouts/lang.blade.php`).
  - Preline UI Navigation: responsive drawer sidebar with floating modal mode, multi-level accordion menu (`<x-sidebar-menu />`), top navbar, and automatic `config/menu.php` integration.
  - Dark mode (`<x-toggle-theme />`) & language switchers (`<x-toggle-language />`).
- **Optional Authentication Scaffolding**:
  - Fortify-compatible modern Preline UI Auth views: Login, Register, Layout, and Slideshow showcase.
- **Frontend Toolchain Setup**:
  - Automated `package.json` updates (Tailwind CSS v4, Preline UI, Lucide icons, Alpine.js, DataTables).
  - Preconfigured `vite.config.js` with auto-globbing for modular page scripts (`resources/js/pages/**/*.js` and `resources/js/utils/*.js`).
- **Two CRUD Flavors**:
  - `--simple`: Rapid, single-page feel Modal CRUD (Preline overlay + client-side DataTable or CardGrid).
  - `--resource`: Robust Full Page CRUD with dedicated Action classes, separate create/edit views, and full form flow.
- **Two UI Layout Options**:
  - `--datatable`: Tabular data presentation with search, filter, and pagination (`<x-datatable>`).
  - `--cardgrid`: Modern responsive card grid layout with live search, filters, and dynamic card rendering (`<x-cardgrid>`).

---

## 📦 Installation

```bash
composer require rendyafs/renkit
php artisan renkit:install
```

During installation, RenKit guides you through an interactive setup wizard:
1. **Frontend Toolchain & Preline UI**: Sets up Tailwind CSS v4, Preline, Vite config, themes, and JS utilities.
2. **Authentication Scaffolding**: Optionally installs Fortify authentication views and provider.
3. **Master Layout & Sidebar Shell**: Publishes `layouts/main.blade.php`, `<x-sidebar>`, `<x-navbar>`, and `config/menu.php`.
4. **Multi-Language Localization**: Optionally enables `lang/id` & `lang/en` dictionaries.

You can also pass non-interactive flags:
```bash
php artisan renkit:install --toolchain --auth --lang
```

Compile assets:
```bash
npm install && npm run build
```

---

## ⚡ Artisan Commands & Workflow

### 1. Generating a Module
```bash
php artisan renkit:module User --simple
```

RenKit inspects the migration (e.g. `create_users_table.php`), extracts columns (`name`, `email`, `password`, `is_active`), and builds:
- `app/Models/User.php` (`$fillable` & `$casts`)
- `app/Http/Controllers/UserController.php`
- `app/Repositories/UserRepository.php`
- `app/Http/Requests/User/SaveUserRequest.php` (validation rules)
- `app/Http/Resources/UserResource.php`
- `resources/views/user/index.blade.php` (DataTable headers)
- `resources/views/user/form.blade.php` (Preline form inputs)
- `resources/views/user/modal.blade.php`
- `resources/js/pages/user/index.js` (DataTable columns & form submit)
- `routes/web/users.php` (modular route)
- `config/menu.php` (sidebar menu entry)

### 2. Updating After Changing Migration
When you add new columns to your migration and run the command again:
```bash
php artisan renkit:module User --simple
```
RenKit detects the existing module and asks:
```text
Module [User] files already exist. Would you like to update and replace files with the latest migration schema? (yes/no) [no]:
```
Choose `yes` (or pass `--force`) to update the views, FormRequest, and Model to match your latest database schema!

---

## 🛠️ CLI Reference

| Command | Description |
| :--- | :--- |
| `php artisan renkit:about` | Show current RenKit version, configuration, and enabled features. |
| `php artisan renkit:doctor` | Run health checks on dependencies, configs, and environment. |
| `php artisan renkit:install` | Run setup wizard (Toolchain, Auth, Layouts, Localization). |
| `php artisan renkit:publish` | Publish theme colors, layouts, components, JS utilities, auth, stubs, or lang files. |
| `php artisan renkit:module {Name}` | Generate or update CRUD module interactively with schema introspection. |
| `php artisan renkit:module {Name} --simple` | Generate modal-based CRUD. |
| `php artisan renkit:module {Name} --resource` | Generate full-page CRUD with separate create/edit views. |
| `php artisan renkit:module {Name} --cardgrid` | Generate CardGrid layout instead of DataTable. |
| `php artisan renkit:module {Name} --force` | Overwrite existing files directly with latest migration schema. |

---

## 📄 License
The MIT License (MIT). Developed by **RendyAFS**.
