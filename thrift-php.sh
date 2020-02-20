#!/bin/bash
set -e

APP_HOME="$(cd "$(dirname "${0}")" && pwd)"

out="${APP_HOME}/target/php"
rm -rf "${out}"
mkdir -p "${out}"
thrift --gen php -out "${out}" "${APP_HOME}"/src/main/thrift/sentinel.thrift
tree -a -F "${out}"