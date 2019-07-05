echo This script will be download entire link in file RESOURCES.txt
echo Type yes to continue downloading.
read answer
if [[ ! $answer == "yes" ]];then
    exit
fi
file="RESOURCES.txt"
lines=`cat $file`
for line in $lines; do
    wget -x "$line"
done
mkdir -p vendor
lines=`ls cdnjs.cloudflare.com/ajax/libs`
for line in $lines; do
    cp -rf cdnjs.cloudflare.com/ajax/libs/$line -t vendor
done
rm -rf cdnjs.cloudflare.com
clear
find vendor
