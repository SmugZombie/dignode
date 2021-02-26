#!/bin/bash
# Pull the version from the package.json
version=0.0.1
tag=smugz/dignode
name=dignode

# Check if running
running=$(docker ps -a | grep "$name" | awk {'print $1'})

echo $running

if [[ ! -z "$running" ]]
then
        echo "Running Instance Found.. Stopping"
        docker stop $running
        echo "Deleting Image"
        docker rm $running

fi

# Build the image
docker build -t $tag:$version .

# Run the image
myname=$name"_1"
docker run -td --restart unless-stopped -p 8111:80 --dns 8.8.8.8 --name $myname $tag:$version
myname=$name"_2"
docker run -td --restart unless-stopped -p 8112:80 --dns 8.8.4.4 --name $myname $tag:$version
myname=$name"_3"
docker run -td --restart unless-stopped -p 8113:80 --dns 1.1.1.1 --name $myname $tag:$version
myname=$name"_4"
docker run -td --restart unless-stopped -p 8114:80 --dns 1.0.0.1 --name $myname $tag:$version
myname=$name"_5"
docker run -td --restart unless-stopped -p 8115:80 --dns 9.9.9.9 --name $myname $tag:$version
myname=$name"_6"
docker run -td --restart unless-stopped -p 8116:80 --dns 149.112.112.112 --name $myname $tag:$version
myname=$name"_7"
docker run -td --restart unless-stopped -p 8117:80 --dns 208.67.222.222 --name $myname $tag:$version
myname=$name"_8"
docker run -td --restart unless-stopped -p 8118:80 --dns 208.67.220.220 --name $myname $tag:$version
