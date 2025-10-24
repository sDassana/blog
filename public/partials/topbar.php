<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="fixed inset-x-0 top-0 z-50 bg-[#FAF7F2] text-black shadow">
  <div class="mx-auto max-w-6xl px-4">
    <div class="flex h-14 items-center justify-between gap-3">
      <a href="/blog/public/view_recipes.php" class="flex items-center gap-2 hover:opacity-90" aria-label="The Cookie Lovestoblog home">
        <img src="/blog/public/assets/brand.png" alt="The Cookie" class="h-10 sm:h-7 md:h-8 lg:h-12 w-auto object-contain" />
        <span class="sr-only">The Cookie Lovestoblog</span>
      </a>

      <form action="/blog/public/view_recipes.php" method="get" class="hidden md:flex items-center gap-2 flex-1 justify-center">
        <input type="text" name="search" placeholder="Search recipes..." class="w-1/2 rounded-[15px] border border-gray-300 px-3 py-1.5 text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#ff6347]/40" />
        <button type="submit" class="rounded-[15px] bg-[#ff6347] hover:bg-[#e5573e] text-white px-4 py-1.5">Search</button>
      </form>

      <div class="flex items-center gap-2">
        <a href="/blog/public/about.php" class="inline-flex items-center rounded-[15px] border border-[#ff6347]/30 text-[#ff6347] hover:bg-[#ff6347]/10 px-3 py-1.5">About Us</a>
        <?php if(isset($_SESSION['user_id'])): ?>
          <a href="/blog/public/dashboard.php" class="inline-flex items-center gap-2 rounded-[15px] bg-[#ff6347] text-white px-3 py-1.5 hover:bg-[#e5573e]">
            <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="Profile" width="20" height="20" class="inline-block" />
            <span>Profile</span>
          </a>
        <?php else: ?>
          <a href="/blog/public/login.php" class="inline-flex items-center rounded-[15px] bg-[#ff6347] text-white px-3 py-1.5 hover:bg-[#e5573e]">Login</a>
          <a href="/blog/public/register.php" class="inline-flex items-center rounded-[15px] border border-[#ff6347]/30 text-[#ff6347] hover:bg-[#ff6347]/10 px-3 py-1.5">Register</a>
        <?php endif; ?>
      </div>
    </div>
    <form action="/blog/public/view_recipes.php" method="get" class="md:hidden pb-3">
      <input type="text" name="search" placeholder="Search recipes..." class="w-full rounded-[15px] border border-gray-300 px-3 py-2 text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#ff6347]/40" />
    </form>
  </div>
</nav>

<div class="h-14"></div>
