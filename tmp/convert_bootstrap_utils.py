#!/usr/bin/env python3
import re
from pathlib import Path

root = Path('e:/Etudes/Projets/bk-business/app/views')
pattern_replacements = [
    (re.compile(r'\bd-flex\b'), 'flex'),
    (re.compile(r'justify-content-between'), 'justify-between'),
    (re.compile(r'align-items-center'), 'items-center'),
    (re.compile(r'align-items-end'), 'items-end'),
    (re.compile(r'\bd-lg-none\b'), 'lg:hidden'),
    (re.compile(r'\bd-none\s+d-md-table-cell\b'), 'hidden md:table-cell'),
    (re.compile(r'\btable-responsive\b'), 'overflow-x-auto'),
]

files_changed = []
for path in root.rglob('*'):
    if path.suffix.lower() not in ('.php', '.html'):
        continue
    text = path.read_text(encoding='utf-8')
    new = text
    for pat, rep in pattern_replacements:
        new = pat.sub(rep, new)
    if new != text:
        path.write_text(new, encoding='utf-8')
        files_changed.append(str(path))

print('Files changed:', len(files_changed))
for f in files_changed:
    print(f)
