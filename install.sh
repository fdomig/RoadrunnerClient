#!/bin/sh

cd $(dirname $0)

# initialization
if [ "$1" = "--reinstall" ]; then
	rm -rf vendor
fi

mkdir -p web/cache && chmod -R 777 web/cache
mkdir -p log && chmod -R 777 log
mkdir -p vendor && cd vendor


##
# @param destination directory (e.g. "doctrine")
# @param URL of the git remote (e.g. git://github.com/doctrine/doctrine2.git)
# @param revision to point the head (e.g. origin/HEAD)
#
install_git()
{
    INSTALL_DIR=$1
    SOURCE_URL=$2
    REV=$3

    if [ -z $REV ]; then
        REV=origin/HEAD
    fi

    if [ ! -d $INSTALL_DIR ]; then
        git clone $SOURCE_URL $INSTALL_DIR
    fi

    cd $INSTALL_DIR
    git fetch origin
    git reset --hard $REV
    cd ..
}

# Silex
rm -rf silex.phar
wget http://silex-project.org/get/silex.phar

# jQuery
mkdir -p ../web/js && cd ../web/js
rm jquery-1.6.min.js
wget http://code.jquery.com/jquery-1.6.min.js
cd ../../vendor

# DroidCouch
# install_git couchdb-odm https://github.com/doctrine/couchdb-odm.git origin/master
# use fdomigs fork as long as there is no IntegerType in the official repository
install_git couchdb-odm https://github.com/fdomig/couchdb-odm.git origin/IntegerType
cd couchdb-odm && git submodule init && git submodule update && cd ..

# Monolog
install_git Monolog https://github.com/Seldaek/monolog.git origin/master

# Twig
install_git Twig https://github.com/fabpot/Twig.git origin/master

cd ..
