from pathlib import Path
import re
root = Path('.').resolve()
php_files = sorted(root.rglob('*.php'))
changes = []
for path in php_files:
    text = path.read_text(encoding='utf-8')
    lines = text.splitlines()
    modified = False
    if lines and lines[0].strip() == '<?php':
        i = 1
        while i < len(lines) and lines[i].strip() == '':
            i += 1
        if i >= len(lines) or not lines[i].strip().startswith('//') and not lines[i].strip().startswith('/*'):
            header = ["// ============================================================",
                      f"//  {path.relative_to(root).as_posix()} — Fichier commenté",
                      "// ============================================================"]
            lines[1:1] = header + ['']
            modified = True
    out = []
    for idx, line in enumerate(lines):
        stripped = line.lstrip()
        if re.match(r'^(abstract\s+|final\s+)?class\s+\w+', stripped):
            prev = out[-1].strip() if out else ''
            if not prev.startswith('//') and not prev.startswith('/*') and not prev.startswith('*'):
                cname = re.findall(r'^(?:abstract\s+|final\s+)?class\s+(\w+)', stripped)
                if cname:
                    out.append(f'// Classe {cname[0]} : implémente la logique métier pour cette partie de l’application')
                    modified = True
        if re.match(r'^(public|protected|private)\s+function\s+\w+', stripped) or re.match(r'^function\s+\w+', stripped):
            prev_line = out[-1].strip() if out else ''
            if prev_line == '' or not prev_line.startswith('//') and not prev_line.startswith('/*') and not prev_line.startswith('*'):
                fname = re.findall(r'function\s+(\w+)', stripped)[0]
                comment = f'// Méthode {fname} : gère {fname.replace("_", " ")}. '
                if not stripped.startswith('<?php'):
                    out.append(comment)
                    modified = True
        out.append(line)
    if modified:
        path.write_text('\n'.join(out) + ('\n' if text.endswith('\n') else ''), encoding='utf-8')
        changes.append(path.relative_to(root).as_posix())
print('Updated', len(changes), 'files')
for c in changes:
    print(c)
