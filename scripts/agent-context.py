#!/usr/bin/env python3
"""Print locked frontend context and verify the retained upstream skill snapshot."""
import hashlib
import json
from pathlib import Path

root = Path(__file__).resolve().parents[1]
composer = json.loads((root / 'composer.lock').read_text())
php_packages = {item['name']: item['version'] for item in composer['packages'] + composer.get('packages-dev', [])}
npm = json.loads((root / 'package-lock.json').read_text())['packages']
print('Locked PHP packages (runtime may differ):')
for name in ['symfony/framework-bundle', 'twig/twig', 'symfony/stimulus-bundle', 'symfony/ux-turbo', 'symfony/ux-twig-component', 'symfony/ux-live-component', 'symfony/webpack-encore-bundle']:
    print(f"  {name}: {php_packages.get(name, 'NOT INSTALLED')}")
print('Locked JS packages:')
for name in ['@symfony/webpack-encore', '@hotwired/stimulus', '@hotwired/turbo', 'sass']:
    print(f"  {name}: {npm.get('node_modules/' + name, {}).get('version', 'NOT INSTALLED')}")
snapshot = root / '.agent/upstream/symfony-ux-skills'
manifest = json.loads((snapshot / 'provenance.json').read_text())
failures = []
for name, expected in manifest['files_sha256'].items():
    path = (snapshot / name).resolve()
    if not path.is_relative_to(snapshot.resolve()):
        failures.append(f'outside snapshot: {name}')
    elif not path.is_file() or hashlib.sha256(path.read_bytes()).hexdigest() != expected:
        failures.append(name)
if failures:
    raise SystemExit('UPSTREAM SNAPSHOT CHECK FAILED: ' + ', '.join(failures))
print(f"Upstream reference snapshot: {manifest['commit']}, {len(manifest['files_sha256'])} files, SHA-256 OK")
print('Read AGENTS.md and docs/research/agent-guides-2026-10-27.md before applying external examples.')
