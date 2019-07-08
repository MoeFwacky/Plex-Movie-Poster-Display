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
 - PHP – I am running version  7
 - Other elements to read xml strings. For exact dependencies, see the install_dependencies.sh file.
 - Your X-Plex-Token. https://support.plex.tv/hc/en-us/articles/204059436-Finding-your-account-token-X-Plex-Token

## Features 
- Two View Modes
- Shows Media title text and other information
- Displays Pseudo Channel status information and commercial library names
- Web Frontend for configuration
- Channel Icons

## Installation
- Install apache2
```bash
% sudo apt install apache2
```

- Change dirs to the new web folder / remove the default index.html / clone this repo there (or a subdir if desired)
```bash
% cd /var/www/html
% sudo rm index.html
% sudo git clone https://github.com/FakeTV/Web-Interface-for-Pseudo-Channel.git .
```

- Since we're in the `permissions` branch, checkout that branch in your new clone:
```bash
% sudo git checkout permissions
```

- Make `./install_dependencies.sh` file executable / install the dependencies
```bash
% sudo chmod +x ./install_dependencies.sh
% sudo ./install_dependencies.sh
```

- Navigate your browser to your Pi's IP
```bash
http://SERVER_IP_ADDRESS/adminConfig.php 
#or 
http://SERVER_IP_ADDRESS/path/adminConfig.php
```

- Add www-data as a sudo user, so they can run certain controls
```bash
% sudo vim /etc/sudoers 

# or:

% sudo nano /etc/sudoers 
```
...add this line to the end of the file:
```bash
www-data ALL=(ALL) NOPASSWD: ALL
```

- Finally, change the permissions for the `html` dir (you can probably get away with only doing this to just `psConfig.php`):
```bash
% sudo chmod -R 777 /var/www/html
```

## Channel Icons

![FakeTV Web Interface](https://i.imgur.com/nhg16Pd.png "FakeTV Web Interface")

- Just add a icon/logo img file to your `pseudo-channel_##` dir named, `favicon.png` (possible filetypes: `jpg,png,gif,ico,svg`). The file must be named, `favicon` and there must only be one of these files in the directory. If you update/change the image you must delete the `./logos` directory that is created in the web directory (i.e. `/var/www/html/logos`). You can do this manually or you can navigate to `settings` in the web interface, scroll to the bottom and click `Purge Logo Image Cache`. 

