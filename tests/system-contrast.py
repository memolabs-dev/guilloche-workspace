"""Contraste des rôles sémantiques, dans les deux thèmes, lus dans settings/_tokens.scss."""
from pathlib import Path
import re, json

observed = dict(re.findall(r'^\$([\w-]+):\s*(#[0-9a-fA-F]{3,6});', Path('assets/styles/settings/_brand.scss').read_text(), re.M))
scss = re.sub(r'#\{brand\.\$([\w-]+)\}', lambda m: observed.get(m.group(1), m.group(0)), Path('assets/styles/settings/_tokens.scss').read_text())


def block(selector):
    start = scss.index(selector)
    body = scss[scss.index('{', start) + 1:]
    return dict(re.findall(r'--([\w-]+):\s*(#[0-9a-fA-F]{3,6});', body[:body.index('\n}')]))


def luminance(color):
    value = color.lstrip('#')
    if len(value) == 3:
        value = ''.join(ch * 2 for ch in value)
    rgb = [int(value[i:i + 2], 16) / 255 for i in (0, 2, 4)]
    return sum((c / 12.92 if c <= .04045 else ((c + .055) / 1.055) ** 2.4) * w for c, w in zip(rgb, [.2126, .7152, .0722]))


TEXT = 4.5
UI = 3
pairs = [
    ('ink', 'paper', TEXT), ('ink', 'sheet-raised', TEXT), ('ink-2', 'paper', TEXT), ('ink-2', 'sheet-sunken', TEXT),
    ('ink-3', 'paper', TEXT), ('ink-3', 'sheet', TEXT), ('ink-3', 'sheet-raised', TEXT),
    ('on-action', 'action', TEXT), ('on-action', 'action-hover', TEXT), ('action-ink', 'action-soft', TEXT), ('action-ink', 'paper', TEXT),
    ('copper', 'copper-soft', TEXT), ('copper', 'sheet-raised', TEXT), ('blue', 'blue-soft', TEXT), ('green', 'green-soft', TEXT),
    ('red', 'red-soft', TEXT), ('red', 'sheet-raised', TEXT), ('neutral', 'neutral-soft', TEXT),
    ('intaglio-ink', 'intaglio', TEXT), ('intaglio-ink-2', 'intaglio', TEXT), ('intaglio-copper', 'intaglio', TEXT),
    ('control-line', 'sheet-raised', UI), ('focus', 'paper', UI), ('focus', 'sheet-raised', UI),
]
report = []
light = block(':root {')
dark = {**light, **block("[data-theme='dark'] {")}
for theme, values in [('light', light), ('dark', dark)]:
    for fg, bg, minimum in pairs:
        a, b = sorted([luminance(values[fg]), luminance(values[bg])])
        ratio = (b + .05) / (a + .05)
        report.append({'theme': theme, 'pair': f'{fg}/{bg}', 'ratio': round(ratio, 2), 'minimum': minimum, 'pass': ratio >= minimum})
        print(theme, f'{fg}/{bg}', round(ratio, 2), 'PASS' if ratio >= minimum else 'FAIL')
Path('.agent/project').mkdir(parents=True, exist_ok=True)
Path('.agent/project/system-contrast.json').write_text(json.dumps(report, indent=2) + '\n')
assert all(row['pass'] for row in report)
