# Plex Now Playing Display
This fork has been modified to work with the Pseudo Channel script (more information here https://medium.com/@Fake.TV/faketv-now-with-pseudo-channels-7c0ed2f32872)

This script scrapes http://<IP_ADDRESS_OF_PLEX_SERVER>:<ACCESS_PORT_OF_PLEX_SERVER>/status/sessions for clients and displays information about the currently playing show on the selected client. For movies, it will display the name, year and tagline. For TV shows, it displays the name, season number, episode number and episode title. 

There are two display modes available. Full Landscape and Half Landscape. In my setup, I have two adafruit 3.5" TFT screens which are mounted in repurposed VCR shells, in the former LED windows. One of them allows for the full screen to be shown, and the other allows for only half. With that in mind, full landscape displays data on the full screen and the half landscape option restricts the data to only display in approximately the top half of the screen, leaving the bottom blank.

Note: Because Plex classifies both movies and custom video libraries as "movies" and this fork is meant to work with Pseudo Channel, which injects commercials between content, any movie or custom videos under 30 minutes are classified as "commercials" and will display the name of the library on the standard clock display instead of switching to the movie poster/background mode.

## My Setup
Plex Media Server is running on a dedicated Rasperry Pi 3
Plex Now Playing Display is running 2 instances on separate Raspberry Pi 2 connected to an adafruit 3.5" TFT screen. This same Pi also runs Pseudo Channel. On boot up the Pi launches Chromium in kiosk mode and loads the Plex Now Playing Display URL. Another Pi, a Zero W, is configured with another screen and connects to the second instance on the Pi2.

## Prerequisites
 - A functioning Plex Server
 - Web Server – I am running Apache, but nginx or whatever else you prefer will work just fine.
 - PHP – I am running version  5.6.30
 - Other elements to read xml strings. For exact dependencies, see the install_dependencies.sh file.
 - Your X-Plex-Token. https://support.plex.tv/hc/en-us/articles/204059436-Finding-your-account-token-X-Plex-Token

## Features 
- Two View Modes
- Shows Media title text and other information
- Displays Pseudo Channel status information and commercial library names
- Web Frontend for configuration

## Installation
- If you don't have apache installed already, do that first (sudo apt-get install apache2)
- Copy all the files into the root of your web server, or desired subfolder if running multiple instances (one copy per instance).
- Change permissions on certain files.
-- Edit permissions of html folder (sudo chmod -R 777 /var/www/html)
-- Edit permissions of main channel folders (sudo chmod -R 777 <Home Directory Goes here>/channels(_NAME if necessary)
- Add www-data as a sudo user, so they can run certain controls
-- Add the line "www-data ALL=(ALL) NOPASSWD: ALL" to the file /etc/sudoers (This can be done with an editing program like nano and sudo permissions)
- Run the dependencies file to ensure everything is up to date (sudo /var/www/html/install_dependencies.sh)
- Test that the output works
-- Open the URL to your server in a browser and configure. http://SERVER_IP_ADDRESS/adminConfig.php or http://SERVER_IP_ADDRESS/path/adminConfig.php

## Upgrading
- Check permissions on cache and config.php.
