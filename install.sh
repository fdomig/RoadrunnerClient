#!/bin/sh

cd $(dirname $0)

# initialization
if [ "$1" = "--reinstall" ]; then
	rm -rf vendor
fi

chmod -R 777 build
mkdir -p web/cache && chmod -R 777 web/cache
mkdir -p log && chmod -R 777 log
mkdir -p vendor && cd vendor

# Silex
rm -rf silex.phar
wget http://silex-project.org/get/silex.phar

# jQuery
cd ../web/js
wget http://jqueryui.com/download/jquery-ui-1.8.13.custom.zip
unzip -u jquery-ui-1.8.13.custom.zip -d jquery
rm -rf jquery-ui-1.8.13.custom.zip jquery/development-bundle jquery/index.html

# jQuery Validation
cd jquery/js
wget http://ajax.aspnetcdn.com/ajax/jquery.validate/1.8.1/jquery.validate.min.js
cd ../../

# highcharts
wget http://www.highcharts.com/downloads/zips/Highcharts-2.1.4.zip
unzip -u Highcharts-2.1.4.zip -d highcharts
rm -rf Highcharts-2.1.4.zip
rm -rf highcharts/examples highcharts/exporting-server highcharts/index.htm

# nyromodal
wget http://nyromodal.googlecode.com/files/nyroModal-1.6.2.zip
unzip -u nyroModal-1.6.2.zip
rm -rf nyroModal-1.6.2.zip


cd ../../vendor

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

# DroidCouch
# install_git couchdb-odm https://github.com/doctrine/couchdb-odm.git origin/master
# use fdomigs fork as long as there is no IntegerType in the official repository
install_git couchdb-odm https://github.com/fdomig/couchdb-odm.git origin/master
cd couchdb-odm && git submodule init && git submodule update && cd ..

# Monolog
install_git Monolog https://github.com/Seldaek/monolog.git origin/master

# Twig
install_git Twig https://github.com/fabpot/Twig.git origin/master

cd ..
