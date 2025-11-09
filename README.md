# The Cookie – PHP Recipe Sharing Blog

The Cookie is a modern, responsive recipe sharing platform built with PHP 8 and MySQL. It features user authentication, profile management, recipe CRUD with images and steps, likes, comments, search with pagination, and built in pure PHP with a lightweight structure and no framework dependencies.

## Live Demo

- Website: Coming soon 

## Overview

Users can:

- Register, login, logout, and manage profiles (display name, email, password)
- Create, edit, and delete recipes with main images, ingredients, and step-by-step photos
- Like/unlike recipes and post comments
- Browse with search (title/tags)
- Navigate a responsive, accessible UI that adapts to Desktop and Mobile devices

## Tech Stack

| Layer           | Technology                                                  |
| --------------- | ----------------------------------------------------------- |
| Frontend        | HTML5, Tailwind CSS, JavaScript                             |
| Backend         | PHP 8+ (procedural with sessions)                           |
| Database        | MySQL (MariaDB for infinityfree)                            |
| Hosting         | Apache (XAMPP locally; any PHP host in production)          |
| Version Control | Git + GitHub                                                |
| Security        | `.env` config, prepared statements, server-side logging     |

## Core Features

### Authentication and Profile

- Register/login/logout using `password_hash()` and `password_verify()`
- Secure sessions and flash messaging
- Profile fields: display name

### Recipe CRUD

- Create, read, update, and delete recipes
- Main image upload stored under `public/uploads/`
- Ingredients list and ordered steps with optional step images

### Likes and Comments

- Like or unlike recipes; live like count
- Comment system with user attribution and timestamps

### Search and Pagination

- Search by recipe title or tags (safe prepared statements)
- Paginated listing (15 per page) with context-preserving back links

### Responsive UI and Theming

- Tailwind CSS utility-first design (compiled via PostCSS/Tailwind CLI)
- Theme: white background, creamy off-white navbar (`#FAF7F2`), tomato accent (`#ff6347`), 15px-rounded primary controls

## Project Structure

```
blog/
├── .env.example
├── .gitignore
├── .htaccess
├── composer.json
├── index.php
├── package.json                      # Tailwind/PostCSS build scripts
├── package-lock.json
├── postcss.config.js                 # PostCSS/Autoprefixer config
├── tailwind.config.js                # Tailwind config (content paths/theme)
├── README.md
├── structure.md                      # Full tree with descriptions
├── config/
│   └── config.php                    # Loads .env, creates $pdo, logs errors
├── logs/
│   ├── errors.log                    # Runtime error log (ignored in Git)
│   └── .gitkeep
├── public/
│   ├── 404.php
│   ├── 500.php
│   ├── about.php
│   ├── add_recipe.php
│   ├── dashboard.php
│   ├── edit_recipe.php
│   ├── forgot_password.php
│   ├── login.php
│   ├── recipe.php
│   ├── register.php
│   ├── saved_recipes.php
│   ├── view_recipes.php
│   ├── assets/
│   │   ├── brand.png
│   │   ├── hero-cookies.svg
│   │   └── icons/
│   │       ├── facebook.png
│   │       ├── instagram.png
│   │       ├── linkedin.png
│   │       └── x.png
│   ├── css/
│   │   ├── app.css                   # Tailwind compiled output
│   │   └── tw-input.css              # Tailwind input (with @tailwind)
│   ├── js/
│   │   ├── file-input.js
│   ├── partials/
│   │   ├── footer.php
│   │   └── topbar.php
│   └── uploads/
│       ├── .htaccess                 # Blocks script execution
│       ├── .gitkeep
│       ├── recipe_*.jpg/png          # Main recipe images (generated names)
│       └── step_*.jpg/png            # Step-by-step images (generated names)
└── src/
    ├── controllers/
    │   ├── add_comment.php
    │   ├── add_recipe.php
    │   ├── delete_comment.php
    │   ├── delete_recipe.php
    │   ├── login.php
    │   ├── logout.php
    │   ├── register.php
    │   ├── reset_password.php
    │   ├── toggle_like.php
    │   ├── toggle_save.php
    │   ├── update_email.php
    │   ├── update_password.php
    │   ├── update_profile.php
    │   └── update_recipe.php
    ├── helpers/
    │   ├── flash.php
    │   ├── markdown.php
    │   ├── recovery_words.php
    │   └── redirect.php
```

## Security and Configuration

Environment variables in `.env` (kept out of Git):

```env
DB_HOST=127.0.0.1
DB_NAME=recipe_blog_app
DB_USER=root
DB_PASS=
DB_PORT=3306
```

Additional safeguards:

- `.gitignore` prevents committing `.env`, logs, and uploaded files; only `.gitkeep` placeholders are tracked
- Prepared statements for all DB operations (via PDO)
- Error details are logged to `logs/errors.log`; users see a generic message
- Consider enforcing HTTPS in production via web server config or `.htaccess`
- `public/uploads/` is protected by an `.htaccess` that blocks script execution; uploaded filenames are sanitized and saved with safe, MIME-validated extensions

## Database Schema (expected)

Tables used by the app:

- `user` (`id`, `username`, `email`, `password_hash`, `role`, `created_at`)
- `recipe` (`id`, `user_id`, `title`, `category`, `tags`, `image_main`, `created_at`)
- `recipe_ingredients` (`id`, `recipe_id`, `ingredient_name`, `quantity`)
- `recipe_steps` (`id`, `recipe_id`, `step_number`, `step_description`, `step_image`)
- `recipe_likes` (`id`, `recipe_id`, `user_id`, `created_at`)
- `recipe_comments` (`id`, `recipe_id`, `user_id`, `comment_text`, `created_at`)

Relationships:

- One-to-many: `user` → `recipe`
- One-to-many: `recipe` → `recipe_ingredients`, `recipe_steps`, `recipe_comments`
- Many-to-many (logical): `user` ↔ `recipe` through `recipe_likes`

If you need, we can generate a SQL schema and seed data matching this codebase.

## Testing Checklist

| Area     | Test                                                                 |
|----------|----------------------------------------------------------------------|
| Auth     | Register/login/logout with valid/invalid credentials                 |
| Profile  | Update display name, email, and password                             |
| CRUD     | Create/edit/delete recipe with images, ingredients, steps            |
| Likes    | Like/unlike updates the count and UI                                 |
| Comments | Add/delete comments; correct attribution and timestamps              |
| Search   | Search by title/tags; pagination returns correct pages               |
| UI       | Responsive layout; accessible controls and color contrast            |
| Errors   | DB failures logged; user sees friendly error page                    |

## Deployment

- Local: XAMPP (Apache + MySQL) — place under `htdocs` and configure `.env`
- Production: any PHP hosting with MySQL — configure virtual host and `.env`
- Ensure `public/uploads/` is writable by the web server
- Enforce HTTPS and secure session settings in production (you can use an `.htaccess` redirect)
- Optional: compatible with popular free/shared PHP hosts (for example, InfinityFree); remember to set correct DB credentials and enable SSL if available

## Developer

- Sathnuwan Dassana

## License and Usage

This project is created for learning and portfolio purposes. You’re welcome to explore, learn from, and extend the code. If you reuse substantial parts of the design or implementation, please credit “Sathnuwan Dassana – The Cookie.”

## Acknowledgements

- Tailwind CSS for rapid UI development
- Official PHP and MySQL documentation
- GitHub Student Developer Pack (optional tools and credits)

## Final Deployment

- Production: add your live URL here once deployed


