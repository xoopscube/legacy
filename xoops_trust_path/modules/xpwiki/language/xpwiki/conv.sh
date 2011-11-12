echo "What's a target directory name?"
read dir

echo "What's encoding of \"$dir\"? (ISO-8859-1, EUC-JP etc.)"
read enc


if ([ ! -d $dir ] || [ -z $dir ]) ; then exit 1 ; fi

if [ ! -d ${dir}_utf8 ] ; then mkdir ${dir}_utf8 ; fi
if [ ! -d ${dir}_utf8/plugin ] ; then mkdir ${dir}_utf8/plugin ; fi

cd $dir

for filename in *
do
    case $filename in
    *.php | *.js ) 
        iconv --verbose -c -f $enc -t UTF-8 $filename > ../${dir}_utf8/$filename
        ;;
    esac
done

cd plugin
for filename in *
do
    case $filename in
    *.php ) 
        iconv --verbose -c -f $enc -t UTF-8 $filename > ../../${dir}_utf8/plugin/$filename
        ;;
    esac
done
