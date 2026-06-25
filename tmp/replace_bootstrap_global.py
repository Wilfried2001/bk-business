#!/usr/bin/env python3
import re
from pathlib import Path

root = Path('e:/Etudes/Projets/bk-business/app/views')

# Mapping from bootstrap class -> replacement classes (Tailwind)
mapping = {
    'btn-sm': 'px-2 py-1 text-xs',
    'btn-primary': 'bg-gradient-to-r from-[#0d47a1] to-[#1565c0] text-white',
    'btn-danger': 'bg-red-600 text-white',
    'btn-success': 'bg-green-600 text-white',
    'btn-outline-primary': 'border border-[#0d47a1] text-[#0d47a1] bg-transparent hover:bg-[#eef4ff]',
    'btn-outline-secondary': 'border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50',
    'btn-outline-warning': 'border border-[#f57f17] text-[#f57f17] bg-transparent hover:bg-[#fff7ed]',
    'btn-outline-danger': 'border border-[#d32f2f] text-[#d32f2f] bg-transparent hover:bg-[#fff1f0]',
    'btn-outline-success': 'border border-[#388e3c] text-[#388e3c] bg-transparent hover:bg-[#f1fbf2]',
    'btn-outline-light': 'border border-white text-white bg-transparent hover:bg-white/10',
    'btn-outline-secondary': 'border border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50',
    'btn': 'inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide',
    'w-100': 'w-full',
    'form-control': 'border rounded-md px-3 py-2 text-sm w-full',
    'form-select': 'border rounded-md px-3 py-2 text-sm w-full bg-white',
    'form-control-sm': 'border rounded-md px-2 py-1 text-xs w-full',
    'row': 'flex flex-wrap -mx-3',
    'g-3': 'gap-4',
    'col-md-12': 'w-full px-3',
    'col-12': 'w-full px-3',
    'col-md-6': 'md:w-1/2 px-3',
    'col-md-4': 'md:w-1/3 px-3',
    'col-md-3': 'md:w-1/4 px-3',
    'col-md-8': 'md:w-2/3 px-3',
    'd-none': 'hidden',
    'd-lg-none': 'lg:hidden',
    'd-md-table-cell': 'hidden md:table-cell',
    'table-responsive': 'overflow-x-auto',
    'badge': 'inline-flex items-center px-2 py-1 rounded',
    'text-muted': 'text-gray-500',
    'text-end': 'text-right',
    'me-2': 'mr-2',
    'ms-2': 'ml-2',
}

class_attr_re = re.compile(r'(class\s*=\s*\"([^\"]*)\"|class\s*=\s*\'([^\']*)\')')

files_changed = []
results = []
for path in root.rglob('*'):
    if path.suffix.lower() not in ('.php', '.html'):
        continue
    text = path.read_text(encoding='utf-8')
    new_text = text
    offset = 0
    changes = []
    for m in class_attr_re.finditer(text):
        full = m.group(0)
        cls = m.group(2) if m.group(2) is not None else m.group(3)
        if '<?' in cls or '<?= ' in cls or '<?=' in cls:
            continue
        classes = cls.split()
        new_classes = []
        replaced = False
        for c in classes:
            if c in mapping:
                new = mapping[c]
                new_classes.extend(new.split())
                replaced = True
            else:
                new_classes.append(c)
        if replaced:
            new_cls_str = ' '.join(dict.fromkeys(new_classes))
            # replace exact class=... occurrence
            new_full = full.replace(cls, new_cls_str)
            start, end = m.span()
            new_text = new_text[:start+offset] + new_full + new_text[end+offset:]
            offset += len(new_full) - (end - start)
            changes.append((full, new_full))
    if changes:
        results.append((str(path), len(changes), changes))

# print dry-run summary
print('Dry-run replacements found in', len(results), 'files')
for path, count, changes in results:
    print('\n==', path, '-', count, 'replacements')
    for old, new in changes[:10]:
        print(' -', old, '=>', new)
    if len(changes) > 10:
        print('   ...', len(changes)-10, 'more')

# If run with --apply, perform changes
import sys
if '--apply' in sys.argv:
    for path, count, changes in results:
        text = Path(path).read_text(encoding='utf-8')
        new_text = text
        for old, new in changes:
            new_text = new_text.replace(old, new)
        Path(path).write_text(new_text, encoding='utf-8')
        files_changed.append(path)
    print('\nApplied changes to', len(files_changed), 'files')
    for f in files_changed:
        print(' *', f)
