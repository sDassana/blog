<?php // topbar assumes session already started via config include ?>

<!-- Global styles moved to /public/css/app.css via Tailwind build -->

<nav class="fixed inset-x-0 top-0 z-50 bg-[#FAF7F2] text-black shadow">
  <div class="mx-auto max-w-6xl px-4">
    <div class="flex h-14 items-center justify-between gap-3">
      <a href="/blog/public/view_recipes.php" class="flex items-center gap-2 hover:opacity-90" aria-label="The Cookie Lovestoblog home">
        <img src="/blog/public/assets/brand.png" alt="The Cookie" class="h-12 md:h-14 w-auto object-contain" />
        <span class="sr-only">The Cookie Lovestoblog</span>
      </a>

      <form action="/blog/public/view_recipes.php" method="get" class="hidden md:flex items-center gap-2 flex-1 justify-center">
        <input type="text" name="search" placeholder="Search recipes..." class="w-1/2 rounded-[15px] border border-gray-300 px-3 py-1.5 text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#ff6347]/40" />
        <button type="submit" class="rounded-[15px] bg-[#ff6347] hover:bg-[#e5573e] text-white px-4 py-1.5">Search</button>
      </form>

      <div class="flex items-center gap-2">
        <?php if(isset($_SESSION['user_id'])): ?>
          <a href="/blog/public/dashboard.php" class="inline-flex items-center gap-2 rounded-[15px] bg-[#ff6347] text-white px-3 py-1.5 hover:bg-[#e5573e]">
            
            <span>Profile</span>
          </a>
        <?php else: ?>
          <a href="/blog/public/login.php" class="inline-flex items-center rounded-[15px] bg-[#ff6347] text-white px-3 py-1.5 hover:bg-[#e5573e]">Login</a>
          <a href="/blog/public/register.php" class="inline-flex items-center rounded-[15px] border border-[#ff6347]/30 text-[#ff6347] hover:bg-[#ff6347]/10 px-3 py-1.5">Register</a>
        <?php endif; ?>
      </div>
    </div>
    <form action="/blog/public/view_recipes.php" method="get" class="md:hidden pb-3">
      <div class="relative">
        <input type="text" name="search" placeholder="Search recipes..." class="w-full rounded-[15px] border border-gray-300 pr-11 pl-3 py-2 text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#ff6347]/40" />
        <button type="submit" aria-label="Search" title="Search" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-[#ff6347]">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m1.1-5.4a6.75 6.75 0 11-13.5 0 6.75 6.75 0 0113.5 0z" />
          </svg>
        </button>
      </div>
    </form>
  </div>
</nav>

<!-- Spacer below fixed navbar: taller on small screens to account for mobile search row -->
<div class="h-20 md:h-14"></div>
