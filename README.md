# Plex Now Playing Display
Scraps the Plex sessions page to display the current playing movie or TV show background art and movie or show data on a screen.

This script scrapes http://IP_ADDRESS_OF_PLEX_SERVER>:32400/status/sessions for clients and displays the art and metadata information of the currently playing movie or TV show. If nothing is currently playing it will display a clock and background image (current code points to assets/standby.jpg, file not included).

## My Setup
Plex Media Server is running on a dedicated server.
Plex Now Playing Display is running on separate Raspberry Pi connected to a 3.5" pi tft screen. On boot up the Pi launches Chromium in kiosk mode and loads the Plex Now Playing Display URL.

## Prerequisites
 - A functioning Plex Server
 - Web Server – I am running apache
 - PHP – I am running version  5.6.30
 - Your X-Plex-Token. https://support.plex.tv/hc/en-us/articles/204059436-Finding-your-account-token-X-Plex-Token

## Features 
- TV Shows display show title, season and episode number and episode title over show background art.
- Movies display the movie title, year and description over the background art for the movie.
- Custom Image. If current status isn't for you, you can have the display show whatever.
- Web Frontend for configuration (ALPHA) 

## Installation
- Copy all the files into the root of your web server.
- Fix permission on cache folder (chmod 777 cache)
- Fix permission on config.php file. (chmod 777 config.php)
- Open the URL to your server in a browser and configure. http://SERVER_IP_ADDRESS/admin.php

## Upgrading
- Delete all files in the cache directory.
- Check permissions on cache and config.php.
