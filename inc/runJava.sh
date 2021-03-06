#!/usr/bin/env sh
file=$1

touch $2/output.txt
touch $2/runtime.txt
	

touch $2/temp.txt
touch $2/errorplaceholder.txt
counter=1
#for c in $(cat $2/input.txt)
while read c;
do
	echo $c > $2/temp.txt
	echo $2/output.txt
	timeout 3s java -classpath $2 $file 0< $2/temp.txt 1>> $2/output.txt 2> $2/errorplaceholder.txt
	status=$?
	if [ "$status" = "124" ]
	then
		echo "EXECUTION TIMEOUT,IT TAKES MORE THAN 3S TO EXECUTE IT!\n" >> $2/output.txt
	else
		if [ -s $2/errorplaceholder.txt ]
		then
			cat $2/errorplaceholder.txt >> $2/runtime.txt
			cat $2/errorplaceholder.txt >> $2/output.txt
	
		fi
	fi
	counter=$((counter+1))
done < $2/input.txt
