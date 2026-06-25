import pathlib, re

def main():
    path = pathlib.Path('app/views/layouts/navbar.php')
    txt = path.read_text(encoding='utf-8')
    print('file length', len(txt))
    # search for bi icon classes
    icon_matches = re.findall(r'class=["\']([^"']*\bbi\b[^"']*)["\']', txt)
    print('icon matches count', len(icon_matches))
    for m in icon_matches[:10]:
        print('icon class:', m)
    # search for data-bs patterns
    bs_matches = re.findall(r'data-bs-[^=]+=["\'][^"']*["\']', txt)
    print('data-bs count', len(bs_matches))
    print(bs_matches[:20])

if __name__ == '__main__':
    main()
