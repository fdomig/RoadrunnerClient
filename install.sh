#!/bin/sh

cd $(dirname $0)

# initialization
if [ "$1" = "--reinstall" ]; then
	rm -rf vendor
fi

mkdir -p web/cache && chmod -R 777 web/cache
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
# XXX: URL currently not available, load from fixup
# cd vendor && wget http://github.com/fabpot/silex/blob/master/silex.phar && cd ..
wget http://seric.at/ffs/upload/silex.phar

# DroidCouch
install_git couchdb-odm https://github.com/doctrine/couchdb-odm.git origin/master
cd couchdb-odm && git submodule init && git submodule update && cd ..


cd ..