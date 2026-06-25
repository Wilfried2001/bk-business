#!/usr/bin/env python3
import re
from pathlib import Path

root = Path('e:/Etudes/Projets/bk-business/app/views')
files = list(root.rglob('*.php')) + list(root.rglob('*.html'))
btn_pattern = re.compile(r"\bclass=\'([^\']*\\bbtn(?:-[^\\' ]*)?[^\\']*)\'|\bclass=\"([^\"]*\\bbtn(?:-[^\" ]*)?[^\"]*)\"")
form_control_pattern = re.compile(r"\bclass=\'([^\']*\\bform-control[^\\']*)\'|\bclass=\"([^\"]*\\bform-control[^\"]*)\"")
form_select_pattern = re.compile(r"\bclass=\'([^\']*\\bform-select[^\\']*)\'|\bclass=\"([^\"]*\\bform-select[^\"]*)\"")

results = []
for f in files:
    text = f.read_text(encoding='utf-8')
    for m in btn_pattern.finditer(text):
        cls = m.group(1) or m.group(2)
        results.append((str(f), cls, 'BUTTON'))
    for m in form_control_pattern.finditer(text):
        cls = m.group(1) or m.group(2)
        results.append((str(f), cls, 'FORM_CONTROL'))
    for m in form_select_pattern.finditer(text):
        cls = m.group(1) or m.group(2)
        results.append((str(f), cls, 'FORM_SELECT'))

from collections import defaultdict
by_file = defaultdict(list)
for path, cls, typ in results:
    by_file[path].append((cls, typ))

print('Files with matches:', len(by_file))
for path, items in sorted(by_file.items()):
    print('\n==', path)
    for cls, typ in items[:10]:
        print(' -', typ, '->', cls)
    if len(items) > 10:
        print('   ...', len(items)-10, 'more')
