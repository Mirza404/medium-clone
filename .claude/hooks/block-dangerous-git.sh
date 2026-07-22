#!/usr/bin/env bash
# PreToolUse hook (Bash) — hard-blocks git commands the user reserves for
# themselves: push (any form), rebase, merge, and other history-rewriting
# ops. Claude may still commit locally. Ported from x-clone's hook of the
# same name.
set -euo pipefail

command="$(cat | jq -r '.tool_input.command // empty')"

[ -z "$command" ] && exit 0

deny() {
  echo "BLOCKED: '$1' is reserved for the user. Claude may commit locally but must not run this. Ask the user to run it themselves." >&2
  exit 2
}

# git push (any form, not just --force)
if printf '%s' "$command" | grep -Eq '\bgit[[:space:]]+push\b'; then
  deny "git push"
fi

if printf '%s' "$command" | grep -Eq '\bgit[[:space:]]+rebase\b'; then
  deny "git rebase"
fi

if printf '%s' "$command" | grep -Eq '\bgit[[:space:]]+merge\b'; then
  deny "git merge"
fi

if printf '%s' "$command" | grep -Eq '\bgit[[:space:]]+reset[[:space:]]+--hard\b'; then
  deny "git reset --hard"
fi

if printf '%s' "$command" | grep -Eq '\bgit[[:space:]]+clean[[:space:]]+-[a-z]*f'; then
  deny "git clean -f"
fi

if printf '%s' "$command" | grep -Eq '\bgit[[:space:]]+branch[[:space:]]+(-D|--delete[[:space:]]+--force)'; then
  deny "git branch -D"
fi

if printf '%s' "$command" | grep -Eq -- '--no-verify|--no-gpg-sign'; then
  deny "--no-verify/--no-gpg-sign"
fi

exit 0
