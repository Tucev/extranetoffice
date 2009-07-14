#!/bin/bash

# Script to update project with latest PHPFrame Scaffold source.
# Luis Montero [e-noise.com] 13 Jul 2009

# Init vars
TMP_SRC="/tmp/eo_tmp"
APP_PATH=$PWD

# Export latest PHPFrame Scaffold source from Google Code SVN to tmp location
echo "Exporting latest PHPFrame Scaffold source..."
svn export --quiet https://phpframe.googlecode.com/svn/PHPFrame_Scaffold/trunk/ $TMP_SRC

# Copy source recursively to overwrite old files
echo "Copying latest source to local installation"
cp -r $TMP_SRC/ $APP_PATH/

# Delete temporary source
echo "Deleting temporary source checkout"
rm -rf $TMP_SRC

echo "Bye"
exit
