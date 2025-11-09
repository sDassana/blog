C:\\xampp\\htdocs\\blog
│
├─ .env                                ← environment variables (DB creds, secrets; not committed)
├─ .env.example                        ← sample env file to copy to .env
├─ .git/                               ← Git metadata
├─ .gitignore                          ← files/folders Git should ignore
├─ .htaccess                           ← Apache rules (routing, security for subfolders)
├─ composer.json                       ← PHP dependencies (if/when using Composer)
├─ index.php                           ← entry point (can route or redirect)
├─ package.json                        ← Node/Tailwind build scripts
├─ package-lock.json                   ← Locked Node deps
├─ postcss.config.js                   ← PostCSS/Autoprefixer config
├─ tailwind.config.js                  ← Tailwind config (content paths, theme)
├─ README.md                           ← Project readme
├─ structure.md                        ← This file: human-friendly project tree
│
├─ config\                             ← App configuration (non-public)
│  └─ config.php                       ← Loads .env, initializes PDO ($pdo), logging on failure
│
├─ logs\                               ← Application logs (non-public)
│  ├─ .gitkeep                         ← Keeps folder in Git
│  └─ errors.log                       ← Error log from DB/connect/runtime issues
│
├─ public\                             ← Web root: files directly served by Apache
│  ├─ 404.php                          ← Not found page
│  ├─ 500.php                          ← Server error page
│  ├─ about.php                        ← About/Contact page
│  ├─ add_recipe.php                   ← Add new recipe form (Markdown preview, images)
│  ├─ dashboard.php                    ← User dashboard (saved/own recipes etc.)
│  ├─ edit_recipe.php                  ← Edit existing recipe form
│  ├─ forgot_password.php              ← Start password reset flow (email/words)
│  ├─ login.php                        ← Login screen (uses themed public UI)
│  ├─ recipe.php                       ← Single recipe view (ingredients, steps, likes)
│  ├─ register.php                     ← Account registration form
│  ├─ saved_recipes.php                ← User’s saved/bookmarked recipes
│  ├─ view_recipes.php                 ← Listing/search page (hero, quotes, cards)
│  ├─ assets\                          ← Images and static assets
│  │  ├─ brand.png                     ← Site brand/logo image
│  │  ├─ hero-cookies.svg              ← Decorative hero background vectors
│  │  ├─ README.txt                    ← Notes about asset usage
│  │  └─ icons\                        ← Social icons
│  │     ├─ facebook.png
│  │     ├─ instagram.png
│  │     ├─ linkedin.png
│  │     └─ x.png
│  ├─ css\                             ← Compiled and source CSS for the site
│  │  ├─ app.css                       ← Compiled output (Tailwind build target)
│  │  └─ tw-input.css                  ← Tailwind input (with @tailwind directives)
│  ├─ js\                              ← Client-side JavaScript
│  │  ├─ file-input.js                 ← Modern file input styling/behavior
│  ├─ partials\                        ← Shared UI components
│  │  ├─ footer.php                    ← Site footer
│  │  └─ topbar.php                    ← Top navigation bar
│  └─ uploads\                         ← User-uploaded files (protected by .htaccess)
│     ├─ .gitkeep                      ← Keeps folder in Git
│     ├─ .htaccess                     ← Prevents script execution, protects files
│     ├─ recipe_*.jpg/png              ← Main recipe images (generated names)
│     └─ step_*.jpg/png                ← Step-by-step images (generated names)
│
└─ src\                                ← Application (backend) code
	├─ controllers\                     ← Request handlers for forms and AJAX
	│  ├─ add_comment.php               ← Add a comment to a recipe
	│  ├─ add_recipe.php                ← Persist a new recipe + uploads
	│  ├─ delete_comment.php            ← Delete a user’s comment
	│  ├─ delete_recipe.php             ← Delete user-owned recipe
	│  ├─ login.php                     ← Authenticate user (sets session)
	│  ├─ logout.php                    ← Destroy session and redirect
	│  ├─ register.php                  ← Create new user account
	│  ├─ reset_password.php            ← Handle password reset (token/words)
	│  ├─ toggle_like.php               ← Like/unlike a recipe (AJAX JSON)
	│  ├─ toggle_save.php               ← Save/unsave a recipe (AJAX JSON)
	│  ├─ update_email.php              ← Update account email
	│  ├─ update_password.php           ← Change password
	│  ├─ update_profile.php            ← Update profile display name
	│  └─ update_recipe.php             ← Persist edits to a recipe
	├─ helpers\                         ← Small reusable utilities
	│  ├─ flash.php                     ← Flash messages in session
	│  ├─ markdown.php                  ← Server-side Markdown (safe subset)
	│  ├─ recovery_words.php            ← Generate/validate 5 recovery words
	│  └─ redirect.php                  ← Safe redirects with optional anchors
