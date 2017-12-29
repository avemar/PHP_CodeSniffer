#!/bin/bash
#
# Andrea Aversa, 2017
#
# Args
# -d Diff Only
# -n Diff on not staged files
# -s Diff on staged files
# -c Diff against specific commit hash
# -l Diff against last commit of specific branch
# -t Filetype to be linted
#
# If not -n nor -s:
# $@ File(s) to be checked

myDir=`dirname $0`
gitExtDiff=$myDir/git-external-diff
phpReportType=DiffFull
jsReportType=diff-full
colors=--colors
notStaged=0
staged=0
commitHex=""
jsBuildRegex=\\bclient\/build
jsMinRegex=\.min\.js$

usage() {
    echo "Usage: bbphplint|bbjslint [OPTION]... [FILE]..."
    echo "  -d               show only modified/added lines"
    echo "  -n               perform sniffing on not staged files"
    echo "  -s               perform sniffing on staged files"
    echo "  -c <commit-hash> perform sniffing on resulting files from diff against a specific commit"
    echo "  -l <branch-name> perform sniffing on resulting files from diff against last commit of specific branch"
    printf "\n"
    echo "  -c, -l and -d can be used in conjunction with any other option"
    echo "  -n,-s and -c,-l options are mutually exclusive"

    exit 1
}

# Args
# $1 file type
# $2 file
# $3 commit hex hash
executeDiffSniff() {
    diffLines=`GIT_EXTERNAL_DIFF=$gitExtDiff git diff $3 $2`
    diffLinesOption=""

    if [[ ! -z $diffLines ]]; then
        diffLinesOption="--diff-lines="$diffLines
    fi

    case "$1" in
        php)
            phpcs --standard=Custom --diff-lines=$diffLines --report=$phpReportType $colors $2
            ;;
        js)
            eslint --config=/home/vagrant/src/eslint/custom_rules/.eslintrc.json --format=$jsReportType $diffLinesOption $2
            ;;
        *)
            echo "Invalid file type"
            printf "\n"
            ;;
    esac
}

# Args
# $1 element to find
# $@ elements to be checked
contains() {
    param=$1
    shift
    array=("$@")

    for elem in "${array[@]}"; do
        [[ "$param" = "$elem" ]] && return 0
    done

    return 1
}

while getopts "dnsc:l:t:h" flag; do
    case "$flag" in
        d)
            phpReportType=DiffOnly
            jsReportType=diff-only
            colors=--no-colors
            ;;
        n)
            notStaged=1
            staged=0
            ;;
        s)
            notStaged=0
            staged=1
            ;;
        c)
            commitHex="$OPTARG"
            ;;
        l)
            commitHex=`git log -n 1 --format=%H $OPTARG`
            ;;
        t)
            fileType="$OPTARG"
            ;;
        h | *)
            usage;
            ;;
    esac
done
shift "$((OPTIND-1))"

if [[ $notStaged -eq 1 || $staged -eq 1 ]]; then
    gitDiff=""

    if [[ $staged -eq 1 ]]; then
        gitDiff=--cached
    fi

    fileTypesToBeLinted=()

    case "$fileType" in
        php)
            fileTypesToBeLinted=(php)
            ;;
        js)
            fileTypesToBeLinted=(js jsx)
            ;;
    esac

    for file in $(git diff --name-only $gitDiff $commitHex); do
        if contains ${file##*.} "${fileTypesToBeLinted[@]}"
        then
            if [[ $fileType == "js" && ( $file =~ $jsMinRegex || $file =~ $jsBuildRegex ) ]]
            then
                #Don't execute linters on minified or built js files
                :
            else
                executeDiffSniff $fileType $file $commitHex
            fi
        fi
    done
    exit 0
fi

if [[ $notStaged -eq 0 && $staged -eq 0 && -z $@ ]]; then
    echo "No file passed"
    exit 1
fi

for file in "$@"; do
    executeDiffSniff $fileType $file $commitHex
done

exit 0
