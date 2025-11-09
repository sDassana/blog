\\xampp\\htdocs\\blog
│
├─ .env                                ← Environment variables (DB creds, secrets)
├─ .env.example                        ← Template for creating a local .env
├─ .git/                               ← Git metadata
├─ .gitignore                          ← Files/folders Git should ignore
├─ .htaccess                           ← Apache rules (HTTPS redirect, security)
├─ index.php                           ← Entry point/redirector for public routes
├─ package.json                        ← Node/Tailwind build scripts
├─ package-lock.json                   ← Locked Node dependency versions
├─ postcss.config.js                   ← PostCSS + Autoprefixer configuration
├─ README.md                           ← Project documentation
├─ recipe_blog.sql                     ← Database schema/seed export
├─ structure.md                        ← This file (project layout guide)
├─ tailwind.config.js                  ← Tailwind content paths/theme overrides
├─ config\                             ← Application configuration (non-public)
│  └─ config.php                       ← Loads .env, boots PDO, session + error handling
├─ logs\                               ← Application logs (non-public)
│  ├─ .gitkeep                         ← Keeps folder versioned
│  └─ errors.log                       ← Database/Runtime error output
├─ node_modules\                       ← Installed Node packages (generated)
├─ public\                             ← Web root served by Apache
│  ├─ 404.php                          ← Not-found view
│  ├─ 500.php                          ← Server-error fallback
│  ├─ about.php                        ← About & contact information
│  ├─ add_recipe.php                   ← Recipe creation form (EasyMDE Markdown)
│  ├─ dashboard.php                    ← Member dashboard & quick actions
│  ├─ edit_recipe.php                  ← Recipe edit form
│  ├─ forgot_password.php              ← Password reset (email entry)
│  ├─ inform_block.php                 ← Reusable guest call-to-action card
│  ├─ login.php                        ← Sign-in screen
│  ├─ partials\                        ← Shared layout components
│  │  ├─ footer.php                    ← Global footer (sticky layout)
│  │  ├─ header.php                    ← <head> fragment (fonts, CSS links)
│  │  └─ topbar.php                    ← Fixed navigation bar + search
│  ├─ recipe.php                       ← Single recipe view (steps, comments)
│  ├─ recovery_words.php               ← Recovery words entry form
│  ├─ recover_account.php              ← Account recovery via words
│  ├─ register.php                     ← Sign-up form
│  ├─ saved_recipes.php                ← Saved/bookmarked recipes list
│  ├─ view_recipes.php                 ← Recipe listing + hero experience
│  ├─ assets\                          ← Static imagery/icons
│  │  ├─ brand.png                     ← Logo asset
│  │  ├─ hero1.svg / hero2.svg         ← Desktop hero artwork (left/right)
│  │  ├─ hero3x.svg / hero4x.svg       ← Mobile hero artwork
│  │  └─ icons\                        ← Social icons (facebook.png, instagram.png, linkedin.png, x.png)
│  ├─ css\                             ← Stylesheets served to the browser
│  │  ├─ app.css                       ← Compiled Tailwind output
│  │  ├─ override.css                  ← Layout overrides (sticky footer, etc.)
│  │  └─ tw-input.css                  ← Tailwind input (source before build)
│  ├─ js\                              ← Client-side helpers
│  │  ├─ file-input.js                 ← File picker UX
│  │  └─ markdown.js                   ← EasyMDE configuration helpers
│  └─ uploads\                         ← User-uploaded media (guarded by .htaccess)
│     ├─ .gitkeep                      ← Keeps directory in version control
│     ├─ .htaccess                     ← Blocks script execution, direct listing
│     ├─ recipe_*.jpg/png              ← Main recipe images
│     └─ step_*.jpg/png                ← Step-by-step images
└─ src\                                ← Backend application code
	├─ controllers\                    ← Form/AJAX handlers
	│  ├─ add_comment.php              ← Persist new comment
	│  ├─ add_recipe.php               ← Store recipe + assets
	│  ├─ auth\                        ← Auth flows (frontend routes post here)
	│  │  ├─ login.php                 ← Login controller
	│  │  ├─ logout.php                ← Logout controller
	│  │  ├─ recover_account.php       ← Start recovery via words
	│  │  ├─ register.php              ← Registration controller
	│  │  ├─ reset_password.php        ← Complete reset via recovery words
	│  │  └─ set_recovery_words.php    ← Store recovery words post-registration
	│  ├─ delete_comment.php           ← Remove a comment
	│  ├─ delete_recipe.php            ← Delete owned/admin recipe (JSON response)
	│  ├─ login.php                    ← Legacy login endpoint (frontend uses auth/)
	│  ├─ logout.php                   ← Legacy logout (deprecated by auth/)
	│  ├─ register.php                 ← Legacy register (deprecated by auth/)
	│  ├─ reset_password.php           ← Password reset handler
	│  ├─ toggle_like.php              ← Toggle like state (JSON)
	│  ├─ toggle_save.php              ← Toggle save state (JSON)
	│  ├─ update_email.php             ← Change account email
	│  ├─ update_password.php          ← Change password
	│  ├─ update_profile.php           ← Update display name
	│  └─ update_recipe.php            ← Persist recipe edits
	└─ helpers\                        ← Reusable helper utilities
		├─ flash.php                   ← Session-based flash messaging
		├─ markdown.php                ← Markdown-to-HTML (safe subset)
		├─ recovery_words.php          ← Recovery word generation + validation
		├─ redirect.php                ← Safe redirect helper
		└─ session.php                 ← Session bootstrap/guard helpers
