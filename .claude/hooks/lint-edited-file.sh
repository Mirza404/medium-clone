#!/usr/bin/env bash
# PostToolUse hook (Write|Edit) — runs Laravel Pint on the just-edited PHP
# file and blocks on style violations, giving fast local feedback before it
# ever reaches CI. Analogous to x-clone's ESLint-based hook of the same name.
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
file_path="$(cat | jq -r '.tool_input.file_path // .tool_response.filePath // empty')"

[ -z "$file_path" ] && exit 0

case "$file_path" in
  *.php) ;;
  *) exit 0 ;;
esac

[ -f "$file_path" ] || exit 0

pint_bin="$REPO_ROOT/vendor/bin/pint"
[ -x "$pint_bin" ] || exit 0

if output="$("$pint_bin" "$file_path" --test 2>&1)"; then
  exit 0
fi

{
  echo "$output"
  echo
  echo "Pint failed on $file_path. Fix the style errors above before continuing (or run ./vendor/bin/pint on the file)."
} >&2
exit 2
