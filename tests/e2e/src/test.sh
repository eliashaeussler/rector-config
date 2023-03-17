#!/usr/bin/env bash
# shellcheck disable=SC2155
set -e

readonly root_path="$(cd -- "$(dirname "$0")/.." >/dev/null 2>&1; pwd -P)"
readonly suite_name="$1"
readonly test_file="src/test-files/${suite_name}.php"

# Check suite name
if [ -z "$suite_name" ]; then
    >&2 echo -e "🚨 No suite name provided."
    exit 1
fi

# Check test file
if [ ! -f "${root_path}/${test_file}" ]; then
    >&2 echo -e "🚨 Test file \"${test_file}\" does not exist."
    exit 1
fi

# Run Rector
set +e
composer -d "$root_path" migration -- --dry-run --clear-cache "$test_file" >/dev/null 2>&1
readonly exit_code=$?
set -e

# Check output
if [ $exit_code -eq 0 ]; then
    >&2 echo -e "🚨 Failed."
    exit 1
fi

echo "✅ Rector config is successfully tested with suite \"${suite_name}\"."
