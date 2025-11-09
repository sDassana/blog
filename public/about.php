<?php
// About page describing the project and contact options. Include config to bootstrap session/user state.
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php $pageTitle = 'About · The Cookie Lovestoblog'; include __DIR__ . '/partials/header.php'; ?>
  </head>
  <body class="min-h-screen bg-white text-gray-800">
    <?php include __DIR__ . '/partials/topbar.php'; ?>
    <main class="max-w-3xl mx-auto px-4 py-10 mb-16">
      <div class="bg-white rounded-xl shadow border border-gray-200 p-8">
        <h1 class="text-2xl font-bold text-center mb-4">About Us</h1>
        <p class="leading-relaxed mb-3">Welcome to <strong>The Cookie Lovestoblog</strong> — your cozy corner for sharing, discovering, and celebrating delicious homemade recipes from all over the world!</p>
        <p class="leading-relaxed mb-3">Our goal is to make it easy for food lovers to share their passion, whether it’s a secret cookie recipe, a classic curry, or your grandmother’s famous drink. Everyone can read and enjoy recipes, and registered users can post, comment, and like their favorites.</p>
        <p class="leading-relaxed">Built with love using PHP, MySQL, and modern web tooling.</p>
      </div>

      <!-- Contact Us Section -->
      <section id="contact" class="mt-8 bg-white rounded-xl shadow border border-gray-200 p-8">
        <h2 class="text-xl font-semibold mb-4">Contact Us</h2>
        <p class="text-gray-700 mb-6">We’d love to hear from you. Reach out using any of the methods below.</p>

        <div class="grid gap-4 sm:grid-cols-2">
          <div class="rounded-[15px] border border-gray-200 p-4 bg-[#FAF7F2]">
            <h3 class="font-semibold mb-1">Email</h3>
            <a href="mailto:hello@thecookielovestoblog.com" class="text-[#ff6347] hover:underline">hello@thecookielovestoblog.com</a>
          </div>
          <div class="rounded-[15px] border border-gray-200 p-4 bg-[#FAF7F2]">
            <h3 class="font-semibold mb-1">Phone</h3>
            <a href="tel:+10000000000" class="text-[#ff6347] hover:underline">+1 (000) 000‑0000</a>
          </div>
          <div class="sm:col-span-2 text-center">
            <h3 class="font-semibold mb-2">Social</h3>
            <div class="flex flex-wrap items-center justify-center gap-4">
              <a href="#" target="_blank" rel="noopener" aria-label="Facebook" class="inline-flex items-center justify-center hover:opacity-80">
                <img src="/blog/public/assets/icons/facebook.png" alt="" class="h-6 w-6 object-contain" />
              </a>
              <a href="#" target="_blank" rel="noopener" aria-label="Instagram" class="inline-flex items-center justify-center hover:opacity-80">
                <img src="/blog/public/assets/icons/instagram.png" alt="" class="h-6 w-6 object-contain" />
              </a>
              <a href="#" target="_blank" rel="noopener" aria-label="X" class="inline-flex items-center justify-center hover:opacity-80">
                <img src="/blog/public/assets/icons/x.png" alt="" class="h-6 w-6 object-contain" />
              </a>
              <a href="https://www.linkedin.com/in/sdassana/" target="_blank" rel="noopener" aria-label="LinkedIn" class="inline-flex items-center justify-center hover:opacity-80">
                <img src="/blog/public/assets/icons/linkedin.png" alt="" class="h-6 w-6 object-contain" />
              </a>
            </div>
          </div>
        </div>
      </section>
    </main>
    <?php include __DIR__ . '/partials/footer.php'; ?>
  </body>
</html>
