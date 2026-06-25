import pathlib
import re

root = pathlib.Path('e:/Etudes/Projets/bk-business')
files = list(root.glob('app/views/**/*.php')) + [root / 'login.html', root / 'app/controllers/TestAgentController.php']

icon_map = {
    'person-circle': 'user-circle',
    'person': 'user',
    'people': 'users',
    'box-arrow-in-right': 'log-in',
    'box-arrow-right': 'log-out',
    'x-lg': 'x',
    'arrow-left-right': 'arrows-left-right',
    'calendar3': 'calendar',
    'calendar2': 'calendar',
    'speedometer2': 'gauge',
    'gear': 'settings',
    'graph-up': 'trending-up',
    'graph-up-arrow': 'trending-up',
    'journal-plus': 'file-plus',
    'check-lg': 'check',
    'shield-lock': 'shield',
    'cash-coin': 'dollar-sign',
    'chat-left-text': 'message-square',
    'card-text': 'file-text',
    'funnel': 'filter',
    'box-seam': 'package',
    'boxes': 'package',
    'list-task': 'list-check',
    'person-plus': 'user-plus',
    'briefcase-fill': 'briefcase',
    'wallet2': 'wallet',
    'exclamation-circle': 'alert-circle',
    'exclamation-triangle': 'alert-triangle',
    'x-circle': 'x-circle',
    'arrow-left': 'arrow-left',
    'arrow-up': 'arrow-up',
    'percent': 'percent',
    'download': 'download',
    'file-earmark-text': 'file-text',
    'shield': 'shield',
    'pencil': 'edit-2',
    'pencil-square': 'edit-2',
    'save': 'save',
    'lock': 'lock',
    'key': 'key',
    'info-circle': 'info',
    'bell': 'bell',
    'table': 'table',
    'pie-chart': 'pie-chart',
    'robot': 'robot',
    'check-circle': 'check-circle',
    'activity': 'activity',
    'sliders': 'sliders',
    'building': 'building',
    'buildings': 'building',
    'inbox': 'inbox',
}

icon_pattern = re.compile(
    r'<i\b([^>]*)\bclass=(?P<quote>["\'])(?P<classes>.*?\bbi\b.*?)(?P=quote)([^>]*)>',
    re.S,
)

def replace_icon_tag(match):
    before_attrs = match.group(1) or ''
    classes = match.group('classes')
    after_attrs = match.group(4) or ''
    tokens = re.split(r'\s+', classes.strip())
    lucide_name = None
    preserved = []

    for token in tokens:
        if token == 'bi':
            continue
        if token.startswith('bi-'):
            icon_value = token[3:]
            if icon_value.startswith('<?='):
                lucide_name = icon_value
            else:
                lucide_name = icon_map.get(icon_value, icon_value)
            continue
        preserved.append(token)

    if not lucide_name:
        return match.group(0)

    quote = '"' if '"' not in lucide_name else "'"
    data_lucide = f'data-lucide={quote}{lucide_name}{quote}'
    attrs = ' '.join([before_attrs.strip(), after_attrs.strip()]).strip()
    if preserved:
        attrs = f'{attrs} class="{" ".join(preserved)}"' if attrs else f'class="{" ".join(preserved)}"'

    return f'<i {attrs} {data_lucide}>' if attrs else f'<i {data_lucide}>'

for path in files:
    if not path.exists():
        continue
    text = path.read_text(encoding='utf-8')
    new = icon_pattern.sub(replace_icon_tag, text)
    new = re.sub(r'data-bs-toggle="tooltip"', '', new)
    new = re.sub(r'data-bs-toggle=\'tooltip\'', '', new)
    new = new.replace('data-bs-toggle="dropdown"', 'data-toggle="dropdown"')
    new = new.replace('data-bs-toggle="collapse"', 'data-toggle="collapse"')
    new = new.replace('data-bs-toggle="modal"', 'data-toggle="modal"')
    new = new.replace('data-bs-target="', 'data-target="')
    new = new.replace('data-bs-dismiss="alert"', 'data-dismiss="alert"')
    new = new.replace('data-bs-dismiss="modal"', 'data-dismiss="modal"')
    new = new.replace('data-bs-dismiss="toast"', 'data-dismiss="toast"')
    new = new.replace("data-bs-toggle='dropdown'", "data-toggle='dropdown'")
    new = new.replace("data-bs-toggle='collapse'", "data-toggle='collapse'")
    new = new.replace("data-bs-toggle='modal'", "data-toggle='modal'")
    new = new.replace("data-bs-target='", "data-target='")
    new = new.replace("data-bs-dismiss='alert'", "data-dismiss='alert'")
    new = new.replace("data-bs-dismiss='modal'", "data-dismiss='modal'")
    new = new.replace("data-bs-dismiss='toast'", "data-dismiss='toast'")
    new = new.replace('class="modal fade"', 'class="modal"')
    new = new.replace("class='modal fade'", "class='modal'")
    new = new.replace('class="modal fade show"', 'class="modal show"')
    new = new.replace("class='modal fade show'", "class='modal show'")
    if new != text:
        path.write_text(new, encoding='utf-8')
        print('Updated', path)
