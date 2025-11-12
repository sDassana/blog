# The Cookie – Recipe Sharing Blog

The Cookie is a modern, responsive recipe-sharing platform built with PHP 8 and MySQL. It powers a full publishing workflow with user authentication, Markdown-enabled recipe instructions, likes, comments, and search-friendly recipe discovery — Built from scratch as part of the IN2120 Web Application Development assignment using pure PHP and Tailwind CSS.

## Live Demo

- **Website:** https://thecookie.lovestoblog.com

## Overview

The Cookie is a full-stack recipe platform where users can:

- Secure registration, login, logout, and session handling
- Rich recipe authoring with Markdown, cover images, ingredients, and step-by-step instructions
- Like/unlike interactions and comment discussions on recipes
- Search and pagination for effortless recipe discovery
- Save favorite recipes for quick access
- Responsive, accessibility-aware UI that adapts to any device
- User profile management (update name, email, password)

## Tech Stack

| Layer           | Technology                                                  |
| --------------- | ----------------------------------------------------------- |
| Frontend        | HTML5, Tailwind CSS, JavaScript                             |
| Backend         | PHP 8+ (procedural with sessions)                           |
| Database        | MySQL                                                       |
| Hosting         | InfinityFree free hosting service                           |
| Domain          | .lovestoblog.com subdomain                                  |
| Version Control | Git + GitHub                                                |
| Security        | HTTPS (Auto-SSL), PDO prepared statements, session tokens   |

## Core Features

### Authentication and Authorization

- Register/login/logout using `password_hash()` and `password_verify()`
- Secure sessions and cookie handling
- Password recovery with secure recovery words

### Recipe CRUD Operations

- Create, read, update, delete recipes
- Upload and display recipe cover images stored in `/public/uploads`
- Markdown formatting for recipe descriptions and steps
- Multi-ingredient and multi-step recipe structure

### Likes and Comments

- Like or unlike recipes via a dedicated `recipe_likes` table
- Display dynamic like counts
- Comment system with user attribution and timestamps
- Delete own comments

### Search and Save

- Search recipes by title
- Paginated recipe listings (12 per page)
- Save/unsave favorite recipes for quick access

### User Profiles

- Update display name, email, and password
- View own recipes on personal dashboard
- Profile picture upload support

### Responsive UI

- Tailwind CSS utility-first design
- Mobile-friendly navigation with dynamic login/register state
- Clean recipe cards with hover effects

## Project Structure

```
c:\xampp\htdocs\blog\
├── README.md
├── recipe_blog.sql            # Database schema / seeds (if present)
├── structure.md
├── package.json               # Tailwind / PostCSS build scripts
├── postcss.config.js
├── tailwind.config.js
├── index.php                  # Public entry (optional route)
├── config\
│   └── config.php             # DB config / bootstrap (edit for local DB creds)
├── logs\
│   └── .gitkeep
├── public\
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
│   ├── assets\
│   │   └── icons\
│   ├── css\
│   │   ├── app.css            # Compiled Tailwind output (generated)
│   │   ├── override.css
│   │   └── tw-input.css       # Tailwind source/input file
│   ├── js\
│   │   ├── file-input.js
│   │   └── markdown.js
│   ├── partials\
│   │   ├── footer.php
│   │   ├── header.php
│   │   └── topbar.php
│   └── uploads\
│       └── (user uploaded images)
└── src\
    ├── controllers\
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
    └── helpers\
        ├── flash.php
        ├── markdown.php
        ├── recovery_words.php
        ├── redirect.php
        └── session.php
```

## Security and Configuration

Database credentials configured in `config/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'recipe_blog');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_PORT', '3306');
```

Additional safeguards:

- PDO prepared statements for all database operations
- Password hashing using `password_hash()` and `password_verify()`
- Session regeneration on login to prevent fixation
- File upload validation (type, size, sanitized names)
- SSL certificate automatically provisioned by InfinityFree

## Database Schema

Tables:

- `user` (`id`, `username`, `email`, `password`, `display_name`, `bio`, `profile_picture`, `created_at`)
- `recipe` (`id`, `user_id`, `title`, `description`, `prep_time`, `cook_time`, `servings`, `image`, `created_at`, `updated_at`)
- `recipe_ingredients` (`id`, `recipe_id`, `ingredient_text`, `sort_order`)
- `recipe_steps` (`id`, `recipe_id`, `step_number`, `instruction`)
- `recipe_likes` (`id`, `recipe_id`, `user_id`, `created_at`)
- `recipe_saves` (`id`, `recipe_id`, `user_id`, `created_at`)
- `recipe_comments` (`id`, `recipe_id`, `user_id`, `comment`, `created_at`)

Relationships:

- One-to-many: `user` → `recipe`
- One-to-many: `recipe` → `recipe_ingredients`
- One-to-many: `recipe` → `recipe_steps`
- One-to-many: `recipe` → `recipe_comments`
- Many-to-many: `user` ↔ `recipe` through `recipe_likes`
- Many-to-many: `user` ↔ `recipe` through `recipe_saves`


## Testing Checklist

| Area     | Test                                                                 |
| -------- | -------------------------------------------------------------------- |
| Auth     | Register/login/logout with valid and invalid credentials            |
| Profile  | Update display name, email, and password                             |
| CRUD     | Create, edit, delete recipe with images, ingredients, steps         |
| Likes    | Like/unlike updates and persists correctly                           |
| Saves    | Save/unsave recipes and view saved recipes page                      |
| Comments | Add and delete comments under recipes                                |
| Search   | Search by recipe title with pagination                               |
| UI       | Responsive layout on mobile and desktop devices                      |
| Database | Tables populated correctly without orphaned records                  |
| Files    | Image uploads work and are stored in `public/uploads/`               |

## Deployment Details (Production)

- Hosting: InfinityFree (free PHP hosting)
- Domain: `thecookie.lovestoblog.com`
- Database: Imported via phpMyAdmin
- `.env`: Uploaded with production credentials
- SSL: Automatically enabled through InfinityFree
- HTTPS: Enforced via `.htaccess` redirect

## Developer

- Sathnuwan Dassana
- University of Moratuwa
- BSc (Hons) in Information Technology & Management
- Level 02, Semester 01
- IN2120 – Web Application Development

## License and Usage

Project created for educational purposes as part of coursework. You may explore, learn from, and extend the code. Please credit Sathnuwan Dassana when reusing the project or its design.

## Acknowledgements

- Tailwind CSS for rapid interface development
- Parsedown library for Markdown rendering
- Official PHP and MySQL documentation
- GitHub Student Developer Pack
- GitHub for version control

## Final Deployment

- **Production:** https://thecookie.lovestoblog.com
  Secure, responsive, and feature-complete — ready for recipe sharing.