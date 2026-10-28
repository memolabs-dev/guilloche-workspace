#!/usr/bin/env bash
set -euo pipefail
if [[ $# -ne 1 ]]; then
    echo "Usage: verify-guilloche-kit.sh path/to/checkout" >&2
    exit 2
fi
REFERENCE="$1"
test -f "$REFERENCE/dist/guilloche.css"
for recipe in alert badge button card description-list field form-section input page-header select stat table textarea timeline; do
    src_dir="$REFERENCE/kits/guilloche/$recipe/templates/components"
    for source in "$src_dir"/*.html.twig; do
        name="$(basename "$source")"
        diff -q "$source" "templates/components/Kit/$name" >/dev/null || {
            echo "Kit component drift: $recipe/$name" >&2;exit 1;
        }
    done
done
cmp "$REFERENCE/dist/guilloche.css" public/guilloche/guilloche.css
cmp "$REFERENCE/kits/guilloche/theme/assets/fonts.css" public/guilloche/fonts.css
diff -qr "$REFERENCE/kits/guilloche/theme/assets/fonts" public/guilloche/fonts >/dev/null
cmp "$REFERENCE/harness/assets/styles/patterns.scss" public/kit-lab/patterns.css
echo "OK: 14 Guilloche Twig recipes, CSS, fonts and patterns match the upstream revision."
