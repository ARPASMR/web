#!/bin/bash

################################################################################
# Create the redist tar file for ARPA anagrafica web
# Date:   2018-08-16
# Author: Luca Paganotti - <luca dot paganotti at gmail dot com>
################################################################################

olddir=`pwd`
now=`date +"%Y%m%dT%H%M%S"`
workdir="/home/buck/dev/web/arpa/anagrafica/anagrafica"
version=1
versionfile="$workdir/version"
TAR="/bin/tar"
TAROPTS="--exclude=./*.tar.gz -zcvf"
tarname="anagrafica_redist"
tarext="tar.gz"

cd $workdir

# Check versionfile exists
# If versionfile does not exist, create it
# and write to it version else read it
if [ -f $versionfile ]; then
	while IFS=' ' read v
	do
		version=$v
	done < "$versionfile"
else
	touch $versionfile
	echo $version > $versionfile
fi

tarfile="$workdir/$tarname.v$version.$now.$tarext"

echo "Creating $tarfile ..."

$TAR $TAROPTS $tarfile ./*

echo "Done."

version=$((version + 1))
echo $version > $versionfile

cd $olddir

