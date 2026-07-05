(function () {
  const NS = "http://www.w3.org/2000/svg";

  const base = {
    "alert-circle": ['<circle cx="12" cy="12" r="10"/>', '<line x1="12" y1="8" x2="12" y2="12"/>', '<line x1="12" y1="16" x2="12.01" y2="16"/>'],
    "alert-triangle": ['<path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z"/>', '<line x1="12" y1="9" x2="12" y2="13"/>', '<line x1="12" y1="17" x2="12.01" y2="17"/>'],
    "arrow-left": ['<path d="m12 19-7-7 7-7"/>', '<path d="M19 12H5"/>'],
    "arrow-left-right": ['<path d="M8 7 3 12l5 5"/>', '<path d="M3 12h18"/>', '<path d="m16 7 5 5-5 5"/>'],
    "arrow-up": ['<path d="m5 12 7-7 7 7"/>', '<path d="M12 19V5"/>'],
    "bar-chart": ['<line x1="12" y1="20" x2="12" y2="10"/>', '<line x1="18" y1="20" x2="18" y2="4"/>', '<line x1="6" y1="20" x2="6" y2="16"/>'],
    "bell": ['<path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>', '<path d="M13.7 21a2 2 0 0 1-3.4 0"/>'],
    "bot": ['<rect x="5" y="8" width="14" height="10" rx="2"/>', '<path d="M12 8V4"/>', '<path d="M8 12h.01"/>', '<path d="M16 12h.01"/>', '<path d="M9 16h6"/>'],
    "bot-message-square": ['<rect x="5" y="7" width="14" height="10" rx="2"/>', '<path d="M12 7V3"/>', '<path d="M9 11h.01"/>', '<path d="M15 11h.01"/>', '<path d="M8 21l3-4"/>'],
    "briefcase": ['<rect x="2" y="7" width="20" height="14" rx="2"/>', '<path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>', '<path d="M2 13h20"/>'],
    "building": ['<rect x="4" y="3" width="16" height="18" rx="2"/>', '<path d="M9 21v-6h6v6"/>', '<path d="M8 7h.01M12 7h.01M16 7h.01M8 11h.01M12 11h.01M16 11h.01"/>'],
    "building-2": ['<path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18"/>', '<path d="M6 12H4a2 2 0 0 0-2 2v8h20v-8a2 2 0 0 0-2-2h-2"/>', '<path d="M10 6h4M10 10h4M10 14h4"/>'],
    "calculator": ['<rect x="4" y="2" width="16" height="20" rx="2"/>', '<line x1="8" y1="6" x2="16" y2="6"/>', '<path d="M8 10h.01M12 10h.01M16 10h.01M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"/>'],
    "calendar": ['<rect x="3" y="4" width="18" height="18" rx="2"/>', '<path d="M16 2v4M8 2v4M3 10h18"/>'],
    "chart-line": ['<path d="M3 3v18h18"/>', '<path d="m7 15 4-4 3 3 5-7"/>'],
    "chart-no-axes-column": ['<path d="M5 21V10"/>', '<path d="M12 21V3"/>', '<path d="M19 21v-7"/>'],
    "check": ['<path d="M20 6 9 17l-5-5"/>'],
    "check-circle": ['<circle cx="12" cy="12" r="10"/>', '<path d="m9 12 2 2 4-4"/>'],
    "compass": ['<circle cx="12" cy="12" r="10"/>', '<path d="m16 8-2.5 5.5L8 16l2.5-5.5Z"/>'],
    "dollar-sign": ['<path d="M12 2v20"/>', '<path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6"/>'],
    "download": ['<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>', '<path d="M7 10l5 5 5-5"/>', '<path d="M12 15V3"/>'],
    "edit-2": ['<path d="M17 3a2.8 2.8 0 1 1 4 4L7 21l-4 1 1-4Z"/>'],
    "eye": ['<path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12Z"/>', '<circle cx="12" cy="12" r="3"/>'],
    "file-plus": ['<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/>', '<path d="M14 2v6h6"/>', '<path d="M12 18v-6M9 15h6"/>'],
    "file-text": ['<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/>', '<path d="M14 2v6h6"/>', '<path d="M16 13H8M16 17H8M10 9H8"/>'],
    "filter": ['<path d="M22 3H2l8 9v7l4 2v-9Z"/>'],
    "gauge": ['<path d="M12 14l4-4"/>', '<path d="M3.3 19a9 9 0 1 1 17.4 0"/>'],
    "history": ['<path d="M3 12a9 9 0 1 0 3-6.7"/>', '<path d="M3 3v6h6"/>', '<path d="M12 7v5l3 2"/>'],
    "inbox": ['<path d="M22 12h-6l-2 3h-4l-2-3H2"/>', '<path d="m5.5 4-3 8v6a2 2 0 0 0 2 2h15a2 2 0 0 0 2-2v-6l-3-8Z"/>'],
    "info": ['<circle cx="12" cy="12" r="10"/>', '<path d="M12 16v-4"/>', '<path d="M12 8h.01"/>'],
    "key": ['<circle cx="7.5" cy="15.5" r="5.5"/>', '<path d="m12 11 9-9"/>', '<path d="m17 7 3 3"/>'],
    "list": ['<path d="M8 6h13M8 12h13M8 18h13"/>', '<path d="M3 6h.01M3 12h.01M3 18h.01"/>'],
    "list-check": ['<path d="M11 6h10M11 12h10M11 18h10"/>', '<path d="m3 6 1 1 3-3M3 12h.01M3 18h.01"/>'],
    "lock": ['<rect x="3" y="11" width="18" height="11" rx="2"/>', '<path d="M7 11V7a5 5 0 0 1 10 0v4"/>'],
    "log-in": ['<path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>', '<path d="m10 17 5-5-5-5"/>', '<path d="M15 12H3"/>'],
    "log-out": ['<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>', '<path d="m16 17 5-5-5-5"/>', '<path d="M21 12H9"/>'],
    "mail": ['<rect x="3" y="5" width="18" height="14" rx="2"/>', '<path d="m3 7 9 6 9-6"/>'],
    "menu": ['<path d="M4 6h16M4 12h16M4 18h16"/>'],
    "moon": ['<path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"/>'],
    "message-circle": ['<path d="M21 11.5a8.4 8.4 0 0 1-9 8.4 8.5 8.5 0 0 1-4-.9L3 21l1.7-4.6A8.5 8.5 0 1 1 21 11.5Z"/>'],
    "message-square": ['<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2Z"/>'],
    "package": ['<path d="m21 8-9-5-9 5 9 5 9-5Z"/>', '<path d="M3 8v8l9 5 9-5V8"/>', '<path d="M12 13v8"/>'],
    "package-plus": ['<path d="m21 8-9-5-9 5 9 5 9-5Z"/>', '<path d="M3 8v8l9 5 9-5V8"/>', '<path d="M12 13v8"/>', '<path d="M16 16h4M18 14v4"/>'],
    "percent": ['<path d="M19 5 5 19"/>', '<circle cx="6.5" cy="6.5" r="2.5"/>', '<circle cx="17.5" cy="17.5" r="2.5"/>'],
    "pie-chart": ['<path d="M21 12a9 9 0 1 1-9-9v9Z"/>', '<path d="M12 3a9 9 0 0 1 9 9h-9Z"/>'],
    "plus-circle": ['<circle cx="12" cy="12" r="10"/>', '<path d="M12 8v8M8 12h8"/>'],
    "receipt": ['<path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2Z"/>', '<path d="M8 7h8M8 11h8M8 15h5"/>'],
    "receipt-text": ['<path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2Z"/>', '<path d="M8 7h8M8 11h8M8 15h5"/>'],
    "rotate-ccw": ['<path d="M3 12a9 9 0 1 0 3-6.7"/>', '<path d="M3 3v6h6"/>'],
    "save": ['<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"/>', '<path d="M17 21v-8H7v8"/>', '<path d="M7 3v5h8"/>'],
    "send-horizontal": ['<path d="m3 3 18 9-18 9 4-9Z"/>', '<path d="M7 12h14"/>'],
    "settings": ['<circle cx="12" cy="12" r="3"/>', '<path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.6-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1A2 2 0 1 1 7.1 4l.1.1a1.7 1.7 0 0 0 1.9.3h.1a1.7 1.7 0 0 0 1-1.6V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.6h.1a1.7 1.7 0 0 0 1.9-.3l.1-.1A2 2 0 1 1 20 7.1l-.1.1a1.7 1.7 0 0 0-.3 1.9v.1a1.7 1.7 0 0 0 1.6 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5.8Z"/>'],
    "shield": ['<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/>'],
    "sun": ['<circle cx="12" cy="12" r="4.5"/>', '<path d="M12 2v2"/>', '<path d="M12 20v2"/>', '<path d="M4.93 4.93l1.41 1.41"/>', '<path d="M17.66 17.66l1.41 1.41"/>', '<path d="M2 12h2"/>', '<path d="M20 12h2"/>', '<path d="M4.93 19.07l1.41-1.41"/>', '<path d="M17.66 6.34l1.41-1.41"/>'],
    "sliders-horizontal": ['<path d="M21 4h-7M10 4H3M21 12h-9M8 12H3M21 20h-5M12 20H3"/>', '<circle cx="12" cy="4" r="2"/>', '<circle cx="10" cy="12" r="2"/>', '<circle cx="14" cy="20" r="2"/>'],
    "sparkles": ['<path d="m12 3 1.8 4.2L18 9l-4.2 1.8L12 15l-1.8-4.2L6 9l4.2-1.8Z"/>', '<path d="M19 15l.8 1.7L21 17.5l-1.2.8L19 20l-.8-1.7-1.2-.8 1.2-.8Z"/>'],
    "table": ['<rect x="3" y="4" width="18" height="16" rx="2"/>', '<path d="M3 10h18M9 4v16"/>'],
    "toggle-right": ['<rect x="2" y="7" width="20" height="10" rx="5"/>', '<circle cx="16" cy="12" r="3"/>'],
    "trash": ['<path d="M3 6h18"/>', '<path d="M8 6V4h8v2"/>', '<path d="M19 6l-1 14H6L5 6"/>', '<path d="M10 11v6M14 11v6"/>'],
    "trending-up": ['<path d="m3 17 6-6 4 4 8-8"/>', '<path d="M14 7h7v7"/>'],
    "user": ['<path d="M20 21a8 8 0 0 0-16 0"/>', '<circle cx="12" cy="7" r="4"/>'],
    "user-circle": ['<circle cx="12" cy="12" r="10"/>', '<circle cx="12" cy="10" r="3"/>', '<path d="M7 20a5 5 0 0 1 10 0"/>'],
    "user-plus": ['<path d="M16 21a7 7 0 0 0-14 0"/>', '<circle cx="9" cy="7" r="4"/>', '<path d="M19 8v6M16 11h6"/>'],
    "users": ['<path d="M16 21a7 7 0 0 0-14 0"/>', '<circle cx="9" cy="7" r="4"/>', '<path d="M22 21a6 6 0 0 0-5-5.9"/>', '<path d="M16 3.1a4 4 0 0 1 0 7.8"/>'],
    "wallet": ['<path d="M19 7V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-2"/>', '<path d="M16 12h6v5h-6a2.5 2.5 0 0 1 0-5Z"/>'],
    "workflow": ['<rect x="3" y="3" width="6" height="6" rx="1"/>', '<rect x="15" y="15" width="6" height="6" rx="1"/>', '<path d="M9 6h3a3 3 0 0 1 3 3v6"/>'],
    "x": ['<path d="M18 6 6 18M6 6l12 12"/>'],
    "x-circle": ['<circle cx="12" cy="12" r="10"/>', '<path d="m15 9-6 6M9 9l6 6"/>'],
    "activity": ['<path d="M22 12h-4l-3 9L9 3l-3 9H2"/>'],
  };

  function renderIcon(node) {
    const name = node.getAttribute("data-lucide");
    const parts = base[name] || ['<circle cx="12" cy="12" r="9"/>', '<path d="M12 8v8M8 12h8"/>'];
    const svg = document.createElementNS(NS, "svg");
    svg.setAttribute("viewBox", "0 0 24 24");
    svg.setAttribute("fill", "none");
    svg.setAttribute("stroke", "currentColor");
    svg.setAttribute("stroke-width", "2");
    svg.setAttribute("stroke-linecap", "round");
    svg.setAttribute("stroke-linejoin", "round");
    svg.setAttribute("aria-hidden", "true");
    svg.setAttribute("data-lucide-icon", name || "");
    const sourceClass = typeof node.className === "string" ? node.className.trim() : "";
    svg.setAttribute("class", sourceClass ? `app-icon ${sourceClass}` : "app-icon");
    svg.innerHTML = parts.join("");
    node.replaceWith(svg);
  }

  function replace(root) {
    (root || document).querySelectorAll("[data-lucide]").forEach(renderIcon);
  }

  window.AppIcons = { replace };
  window.lucide = { replace };
})();
