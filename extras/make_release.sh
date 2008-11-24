#-----------------
# make_release.sh
# Version: $Id: make_release.sh,v 1.1 2008/07/07 13:57:13 minahito Exp $
#-----------------
# This shell script removes unneeded directories and files from file tree that you checked out,
# and makes the file tree to release.
#

if [ -e ../html/xoops.css ]; then
	find .. -type d | grep '/CVS' | xargs rm -rf
	find .. -type d | grep '/.xml' | xargs rm -rf
	find .. -type d | grep '/.doxy' | xargs rm -rf
	mv ../html/modules/system ../extras/system
	rm ../.project
fi 
