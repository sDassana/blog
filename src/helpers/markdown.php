<?php
// Lightweight Markdown to HTML converter (safe subset)
// - Escapes all HTML first to prevent XSS
// - Supports: headings (#..######), bold ** ** / __ __, italics * */_ _,
//   inline code `code`, code fences ``` ``` (no highlighting),
//   links [text](https://...), unordered/ordered lists, blockquotes, line breaks

function md_to_html(?string $text): string {
    if ($text === null || $text === '') {
        return '';
    }

    // Normalize line endings and escape HTML
    $text = str_replace(["\r\n", "\r"], "\n", $text);
    $escaped = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

    // 1) Extract fenced code blocks into placeholders to avoid inline processing inside
    $codeBlocks = [];
    $escaped = preg_replace_callback('/```([a-zA-Z0-9_-]+)?\n([\s\S]*?)\n```/m', function ($m) use (&$codeBlocks) {
        $langClass = isset($m[1]) && $m[1] !== '' ? ' class="language-' . htmlspecialchars($m[1], ENT_QUOTES, 'UTF-8') . '"' : '';
        $code = $m[2]; // already escaped
        $html = '<pre class="rounded-lg bg-gray-50 border border-gray-200 p-3 overflow-x-auto"><code' . $langClass . '>' . $code . '</code></pre>';
        $token = '__CODEBLOCK_' . count($codeBlocks) . '__';
        $codeBlocks[] = $token . $html;
        return $token;
    }, $escaped);

    // Split into lines to handle block structures
    $lines = explode("\n", $escaped);
    $html = [];
    $inUl = false; $inOl = false; $inBlockquote = false;

    $flushLists = function() use (&$html, &$inUl, &$inOl) {
        if ($inUl) { $html[] = '</ul>'; $inUl = false; }
        if ($inOl) { $html[] = '</ol>'; $inOl = false; }
    };

    foreach ($lines as $line) {
        $trim = ltrim($line);

        // Blockquote
        if (preg_match('/^>\s?(.*)$/', $trim, $m)) {
            $content = $m[1];
            if (!$inBlockquote) { $flushLists(); $html[] = '<blockquote class="border-l-4 border-gray-300 pl-3 text-gray-700 italic">'; $inBlockquote = true; }
            $html[] = $content;
            continue;
        } else if ($inBlockquote && $trim === '') {
            // allow blank lines within blockquote
            $html[] = '<br />';
            continue;
        } else if ($inBlockquote) {
            // end blockquote on first non-quoted line
            $html[] = '</blockquote>';
            $inBlockquote = false;
        }

        // Headings
        if (preg_match('/^(#{1,6})\s+(.*)$/', $trim, $m)) {
            $flushLists();
            $level = strlen($m[1]);
            $content = $m[2];
            $html[] = "<h$level class=\"font-semibold mt-3 mb-1\">$content</h$level>";
            continue;
        }

        // Ordered list
        if (preg_match('/^[0-9]+\.\s+(.*)$/', $trim, $m)) {
            if ($inUl) { $html[] = '</ul>'; $inUl = false; }
            if (!$inOl) { $html[] = '<ol class="list-decimal list-inside space-y-1">'; $inOl = true; }
            $html[] = '<li>' . $m[1] . '</li>';
            continue;
        }

        // Unordered list
        if (preg_match('/^[-*]\s+(.*)$/', $trim, $m)) {
            if ($inOl) { $html[] = '</ol>'; $inOl = false; }
            if (!$inUl) { $html[] = '<ul class="list-disc list-inside space-y-1">'; $inUl = true; }
            $html[] = '<li>' . $m[1] . '</li>';
            continue;
        }

        // Blank line breaks lists
        if ($trim === '') {
            $flushLists();
            $html[] = '<br />';
            continue;
        }

        // Paragraph text line (will join via <br /> between lines)
        $flushLists();
        $html[] = $trim;
    }

    // Close any open blocks
    if ($inBlockquote) { $html[] = '</blockquote>'; }
    if ($inUl) { $html[] = '</ul>'; }
    if ($inOl) { $html[] = '</ol>'; }

    $out = implode("\n", $html);

    // 2) Extract inline code spans into placeholders
    $inlineCodes = [];
    $out = preg_replace_callback('/`([^`\n]+)`/', function ($m) use (&$inlineCodes) {
        $token = '__INCODE_' . count($inlineCodes) . '__';
        $inlineCodes[] = $token . '<code class="bg-gray-100 rounded px-1 py-0.5">' . $m[1] . '</code>';
        return $token;
    }, $out);

    // 3) Inline transforms (limited to single line to avoid runaway matches)
    $out = preg_replace('/\[([^\]]+)\]\((https?:[^)\s]+)\)/', '<a href="$2" class="text-[#ff6347] underline" target="_blank" rel="noopener noreferrer">$1</a>', $out);
    $out = preg_replace('/\*\*([^*\n]+)\*\*/', '<strong>$1</strong>', $out);
    $out = preg_replace('/__([^_\n]+)__/', '<strong>$1</strong>', $out);
    // Italic without lookbehind for compatibility; ensure not preceded by the same marker
    $out = preg_replace('/(^|[^*])\*([^*\n]+)\*(?!\*)/', '$1<em>$2</em>', $out);
    $out = preg_replace('/(^|[^_])_([^_\n]+)_(?!_)/', '$1<em>$2</em>', $out);

    // 4) Restore inline code spans
    foreach ($inlineCodes as $stored) {
        $pos = strpos($stored, '<');
        $token = substr($stored, 0, $pos);
        $content = substr($stored, $pos);
        $out = str_replace($token, $content, $out);
    }

    // 5) Restore code blocks
    foreach ($codeBlocks as $stored) {
        $pos = strpos($stored, '<');
        $token = substr($stored, 0, $pos);
        $content = substr($stored, $pos);
        $out = str_replace($token, $content, $out);
    }

    return $out;
}

?>
