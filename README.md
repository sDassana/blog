# The Cookie

A simple PHP + MySQL recipe sharing app using Tailwind CSS (CDN). Includes login/register, profile, add/edit recipes with images, likes, comments, search, and pagination.

## Requirements

- PHP 8.x
- MySQL/MariaDB
- Web server (Apache via XAMPP is fine)
- Composer (optional – this project currently has no PHP dependencies, `composer.json` is empty)

## Quick start (XAMPP on Windows)

1. Place this repository under your XAMPP htdocs (e.g. `C:\xampp\htdocs\blog`).
2. Ensure Apache and MySQL are running.
3. Copy `.env.example` to `.env` and set your DB credentials:

   ```env
   DB_HOST=127.0.0.1
   DB_NAME=recipe_blog_app
   DB_USER=root
   DB_PASS=
   DB_PORT=3306
   ```

4. Create the database named in `DB_NAME` (default: `recipe_blog_app`).
5. Ensure required tables exist (see "Database tables" below). If you don't have a schema yet, I can help generate one based on the app.
6. Visit the app in your browser:
   - http://localhost/blog/public/view_recipes.php
   - Auth pages are under `/blog/public/login.php` and `/blog/public/register.php`.

## Configuration and security

- Never commit your real `.env`. The repo ignores it via `.gitignore`.
- A safe `.env.example` is provided for local setup.
- Logs and uploads are not committed; only their directories are tracked using `.gitkeep`:
  - `logs/` (ignored except for `.gitkeep`)
  - `public/uploads/` (ignored except for `.gitkeep`)
- If using GitHub, consider using your `users.noreply.github.com` email to avoid exposing a private address.

## Where things live

- Public entrypoints: `public/` (e.g., `view_recipes.php`, `recipe.php`, `login.php`)
- Shared UI: `public/partials/topbar.php`, `public/partials/footer.php`
- Controllers: `src/controllers/`
- Helpers: `src/helpers/`
- Config and DB: `config/config.php` (loads `.env` and creates `$pdo`)
- Assets: `public/assets/` (put your `brand.png` here)
- Uploads: `public/uploads/`
- Logs: `logs/`

## Database tables (expected by the app)

Based on current queries, you will need these tables:

- `user` (id, username, email, password_hash, role, about, avatar, created_at, ...)
- `recipe` (id, user_id, title, category, tags, image_main, created_at, ...)
- `recipe_ingredients` (id, recipe_id, ingredient_name, quantity)
- `recipe_steps` (id, recipe_id, step_number, step_description, step_image)
- `recipe_likes` (id, recipe_id, user_id, created_at)
- `recipe_comments` (id, recipe_id, user_id, comment_text, created_at)

If you want, I can scaffold a SQL schema and seed script to match the code.

## Development notes

- Tailwind CSS is loaded via CDN in each page. No build step required.
- Theming: white background, creamy off-white navbar (`#FAF7F2`), tomato accent (`#ff6347`), and 15px-rounded primary controls.
- Images are uploaded to `public/uploads/` and referenced relative to `public/`.
- Error details are logged to `logs/errors.log`; users see a friendly 500 when DB fails.

## Troubleshooting

- 500 errors when loading a page: check `logs/errors.log` and DB credentials in `.env`.
- Search query errors: ensure tables exist and `recipe.tags` column is present.
- Upload issues: verify `public/uploads/` is writable by the web server.

## License

This project is for educational/demo purposes. Add a proper license if you plan to open source.
