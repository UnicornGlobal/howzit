#!/bin/bash

# The below variables need to match what comes in from travis in order to run
SUBMITTED_BRANCH=$1
TRUE_BRANCH=dev
PACKAGE_NAME=/tmp/howzit-package.tgz
PACKAGE_FOLDER=/tmp/tmp/build/

if [ "$1" != "$TRUE_BRANCH" ]; then
    echo "Bad submission, aborting"
    exit 1
fi

echo 'Deploying Howzit'
cd /tmp
tar xzf $PACKAGE_NAME
cd /srv/
sudo mv $PACKAGE_FOLDER ./new-howzit
sudo rm -rf howzit_old
sudo mv howzit howzit_old
sudo mv new-howzit howzit
cd /srv/howzit

# owners and permissions
sudo chown -R ducky:www-data /srv/howzit/

cd /srv/howzit

# sensitive files
rm -rf scripts
rm -rf node_modules
rm -rf config
rm -rf test
rm -rf src
rm -rf build
rm -rf .git
rm -f .gitignore
rm -f .gitattributes
rm -f .babelrc
rm -f .editorconfig
rm -f .travis.yml
rm -f .eslintignore
rm -f .eslintrc.js
rm -f .postcssrc.js
rm -f README.md
rm -f package.json
rm -f npm-debug.log
rm -f index.html
