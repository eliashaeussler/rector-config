#!/usr/bin/env bash
# shellcheck disable=SC2155
set -e

readonly test_path="$(cd -- "$(dirname "$0")/.." >/dev/null 2>&1; pwd -P)"
readonly root_path="$(cd -- "${test_path}/../.." >/dev/null 2>&1; pwd -P)"
readonly rector_bin="${root_path}/vendor/bin/rector"
readonly rector_config="${test_path}/rector.php"
readonly suite_name="$1"
readonly expected_rule="$2"
readonly test_file="src/test-files/${suite_name}.php"

# Check suite name
if [ -z "$suite_name" ]; then
    >&2 echo -e "🚨 No suite name provided."
    exit 1
fi

# Check expected rule
if [ -z "$expected_rule" ]; then
    >&2 echo -e "🚨 No expected rule provided."
    exit 1
fi

# Check test file
if [ ! -f "${test_path}/${test_file}" ]; then
    >&2 echo -e "🚨 Test file \"${test_file}\" does not exist."
    exit 1
fi

# Run Rector
if output=$("$rector_bin" process -c "$rector_config" --dry-run --clear-cache --no-progress-bar "${test_path}/${test_file}"); then
    >&2 echo -e "🚨 Test failed, Rector did not propose changes."
    exit 1
fi

# Check output
if [[ $output != *"$expected_rule"* ]]; then
    echo "${output[*]}"
    echo

    >&2 echo -e "🚨 Test failed, expected rule did not match."
    exit 1
fi

echo "✅ Rector config is successfully tested with suite \"${suite_name}\"."
