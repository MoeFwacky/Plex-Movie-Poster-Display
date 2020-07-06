#!/bin/bash

#Shell script will ensure that all the correct dependencies are installed.
# This includes the following packages
# - php
# - apache2
# - libapache2-mod-php
#
# Then apache2 is reset to be careful
sudo apt-get update
sudo apt-get install php apache2 libapache2-mod-php php-xml php-sqlite3
sudo service apache2 restart
