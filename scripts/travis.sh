#!/bin/bash
# Example workflow file

function pull_request() {
    echo "Pull Request, Skipping Deploy"
}

function package() {
    echo "Prepping a build"

    # This will result in an archive which will be available in `/tmp`
    mkdir /tmp/build
    cp -r * /tmp/build
    cd /tmp/build
    rm -rf .git
    cd -
    cd /tmp
    # Set the name of the package in your travis env files
    tar -czf /tmp/${TRAVIS_PACKAGE}.tgz /tmp/build
    cd -
}

function submit() {
    echo "Submitting"
    echo "EDIT YOUR TRAVIS SCRIPTS TO ENABLE THIS"

    # Use these variables for your script
    S_PORT=$1
    S_USER=$2
    S_HOST=$3

    # Write your deployment script here
}

function deploy_dev() {
    echo "Deploying Dev Branch to Staging"
    echo "EDIT YOUR TRAVIS FILE TO ENABLE THIS"

    package
    # Set these in travis
    submit ${DEPLOY_PORT} ${DEPLOY_USER} ${DEPLOY_HOST}
}

function deploy_prod() {
    echo "Deploying Master Branch to Production"
    echo "EDIT YOUR TRAVIS FILE TO ENABLE THIS"

    package
    # Set these in travis
    submit ${LIVE_DEPLOY_PORT} ${LIVE_DEPLOY_USER} ${LIVE_DEPLOY_HOST}
}

# Set these environment variables in your Travis config
if [ "$TRAVIS_BRANCH" == "$DEV_BRANCH" ]; then
  deploy_dev
elif [ "$TRAVIS_BRANCH" == "$PROD_BRANCH" ]; then
  deploy_prod
else
  echo "Not a deployment branch"
fi
