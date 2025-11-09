<?php
// Guest information block component.
// Include this file where you want to encourage unauthenticated users to sign in.
// Styling adapted to existing Tailwind palette (no custom charcoal/linen tokens defined).
?>
<div id="guest-inform" class="flex w-full flex-col gap-3 rounded-[15px] border border-gray-200 bg-[#FAF7F2] px-6 py-5 text-sm text-gray-700 shadow-md relative">
	<button type="button" aria-label="Dismiss guest information" class="absolute top-2 right-2 h-6 w-6 flex items-center justify-center rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100" data-dismiss>
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M6 18L18 6"/></svg>
	</button>
	<div class="pr-4">
		<p class="text-[0.65rem] font-semibold uppercase tracking-[0.25em] text-gray-500">Guest preview</p>
		<p class="mt-1 text-sm leading-relaxed text-gray-700">You’re browsing as a guest. Log in to share your recipes, save favorites, and engage with the community.</p>
	</div>
	<div class="flex flex-wrap gap-2">
		<a href="/blog/public/login.php" class="inline-flex flex-1 items-center justify-center rounded-[15px] bg-[#ff6347] text-white px-4 py-2 text-[0.75rem] font-semibold shadow hover:bg-[#e5573e] active:translate-y-px" aria-label="Log in to your account">Log in</a>
		<a href="/blog/public/register.php" class="inline-flex flex-1 items-center justify-center rounded-[15px] border border-[#ff6347]/30 bg-transparent px-4 py-2 text-[0.75rem] font-semibold text-[#ff6347] hover:bg-[#ff6347]/10 active:translate-y-px focus:outline-none focus:ring-2 focus:ring-[#ff6347]/40" aria-label="Create a new account">Create account</a>
	</div>
</div>
