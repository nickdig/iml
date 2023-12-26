#!/bin/bash

# Update and install docker
sudo apt-get update
sudo apt-get install -y docker.io


# give permissions for the compilation and execution of java code 
sudo chmod 666 /var/run/docker.sock
sudo chmod -R 755 /var/www  