// Lightweight Markdown to HTML (client-side) - safe subset matching PHP helper
// Escapes HTML, supports headings, bold/italic, inline/code fences, links, lists, blockquotes

function mdEscapeHtml(str) {
  const div = document.createElement('div');
  div.textContent = str == null ? '' : String(str);
  return div.innerHTML;
}

export function mdToHtml(input) {
  if (!input) return '';
  let text = input.replace(/\r\n|\r/g, '\n');
  let esc = mdEscapeHtml(text);

  // 1) Extract fenced code blocks and replace with placeholders to avoid further inline processing inside
  const codeBlocks = [];
  esc = esc.replace(/```([a-zA-Z0-9_-]+)?\n([\s\S]*?)\n```/g, (_, lang, code) => {
    const cls = lang ? ` class="language-${lang}"` : '';
    const html = `<pre class="rounded-lg bg-gray-50 border border-gray-200 p-3 overflow-x-auto"><code${cls}>${code}</code></pre>`;
    const token = `__CODEBLOCK_${codeBlocks.length}__`;
    codeBlocks.push(token + html);
    return token;
  });

  // 2) Block-level parsing on the escaped text with codeblock tokens in place
  const lines = esc.split('\n');
  const out = [];
  let inUl = false, inOl = false, inBq = false;
  const flushLists = () => {
    if (inUl) { out.push('</ul>'); inUl = false; }
    if (inOl) { out.push('</ol>'); inOl = false; }
  };

  for (const raw of lines) {
    const line = raw.replace(/^\s+/, '');

    const bq = line.match(/^>\s?(.*)$/);
    if (bq) {
      if (!inBq) { flushLists(); out.push('<blockquote class="border-l-4 border-gray-300 pl-3 text-gray-700 italic">'); inBq = true; }
      out.push(bq[1]);
      continue;
    } else if (inBq && line === '') {
      out.push('<br />');
      continue;
    } else if (inBq) {
      out.push('</blockquote>');
      inBq = false;
    }

    const h = line.match(/^(#{1,6})\s+(.*)$/);
    if (h) {
      flushLists();
      const level = h[1].length;
      out.push(`<h${level} class="font-semibold mt-3 mb-1">${h[2]}</h${level}>`);
      continue;
    }

    if (/^[0-9]+\.\s+/.test(line)) {
      if (inUl) { out.push('</ul>'); inUl = false; }
      if (!inOl) { out.push('<ol class="list-decimal list-inside space-y-1">'); inOl = true; }
      out.push('<li>' + line.replace(/^[0-9]+\.\s+/, '') + '</li>');
      continue;
    }

    if (/^[-*]\s+/.test(line)) {
      if (inOl) { out.push('</ol>'); inOl = false; }
      if (!inUl) { out.push('<ul class="list-disc list-inside space-y-1">'); inUl = true; }
      out.push('<li>' + line.replace(/^[-*]\s+/, '') + '</li>');
      continue;
    }

    if (line === '') {
      flushLists();
      out.push('<br />');
      continue;
    }

    flushLists();
    out.push(line);
  }

  if (inBq) out.push('</blockquote>');
  if (inUl) out.push('</ul>');
  if (inOl) out.push('</ol>');

  let html = out.join('\n');

  // 3) Protect inline code spans with placeholders
  const inlineCodes = [];
  html = html.replace(/`([^`\n]+)`/g, (_, code) => {
    const token = `__INCODE_${inlineCodes.length}__`;
    inlineCodes.push(token + `<code class="bg-gray-100 rounded px-1 py-0.5">${code}</code>`);
    return token;
  });

  // 4) Inline transforms (avoid spanning multiple lines)
  html = html.replace(/\[([^\]]+)\]\((https?:[^)\s]+)\)/g, '<a href="$2" class="text-[#ff6347] underline" target="_blank" rel="noopener noreferrer">$1</a>');
  html = html.replace(/\*\*([^*\n]+)\*\*/g, '<strong>$1</strong>');
  html = html.replace(/__([^_\n]+)__/g, '<strong>$1</strong>');
  // Italic without lookbehind for broad browser compatibility
  html = html.replace(/(^|[^*])\*([^*\n]+)\*(?!\*)/g, '$1<em>$2</em>');
  html = html.replace(/(^|[^_])_([^_\n]+)_(?!_)/g, '$1<em>$2</em>');

  // 5) Restore inline code spans
  html = inlineCodes.reduce((acc, stored) => {
    const [token, content] = [stored.slice(0, stored.indexOf('<')), stored.slice(stored.indexOf('<'))];
    return acc.split(token).join(content);
  }, html);

  // 6) Restore code blocks
  html = codeBlocks.reduce((acc, stored) => {
    const [token, content] = [stored.slice(0, stored.indexOf('<')), stored.slice(stored.indexOf('<'))];
    return acc.split(token).join(content);
  }, html);

  return html;
}

export function attachLiveMarkdownPreview(textarea, target) {
  if (!textarea || !target) return;
  const update = () => { target.innerHTML = mdToHtml(textarea.value); };
  textarea.addEventListener('input', update);
  update();
}
