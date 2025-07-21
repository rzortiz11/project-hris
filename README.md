## HRIS

HRIS is an API built using Laravel 11

#BACKEND 
  - LARAVEL 11 FRAMEWORK - PHP PHP 8.2
  - ADMIN PANEL - FILAMENT 3.0

#DATABASE :
  -  MySQL

LOCAL PROJECT SETUP : 

1. Download WSL2 
LINK : https://learn.microsoft.com/en-us/windows/wsl/install-manual

2. Download LINUX OS (I USED UBUNTU IN MICROSOFT STORE)

3. RUN the UBUNTU

4. Update Package Lists:
	 - Run the following command to update the package lists:
		 sudo apt update

5. Download and Install PHP : 
	 - Choose the PHP version you want to install. In this case, I'll use PHP 8.2 as an example. You can replace it with the version you prefer:
		 sudo apt install php8.2-cli
	 - Verify PHP Installation:
	   php --version

5.1. Install Docker Compose in WSL:
   - sudo apt install docker-compose

6. Download and Install Composer:

	- Run the following commands in your WSL terminal to download and install Composer:
			php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
			php composer-setup.php
			php -r "unlink('composer-setup.php');"

	- Move Composer to a Global Location:
			sudo mv composer.phar /usr/local/bin/composer
	- Verify Installation:
		  composer --version


HOW TO RUN THE PROJECT

Clone the Repo inside the wsl - (FOR project-hris)
- git clone https://github.com/rortiz11/project-hris.git
- composer install 

open the project folder
 - code .
 - update .env file configuration (FOR Project-hris)
	
    - add additional .env for pusher
	PUSHER_APP_ID=
	PUSHER_APP_KEY=
	PUSHER_APP_SECRET=
	PUSHER_HOST=
	PUSHER_PORT=
	PUSHER_SCHEME=
	PUSHER_APP_CLUSTER=
	
open docker desktop

✅ 1. Enable WSL Integration in Docker Desktop
Open Docker Desktop on your Windows machine.
Go to Settings → Resources → WSL Integration.
Enable integration for your Ubuntu-20.04 distribution.
Click Apply & Restart.

run the project
 - source .bashrc
 - sail up

- sail artisan key:generate
- sail artisan storage:link
COMMAND FOR database notification to works 
 - sail artisan queue:work

Read crontab_setup.txt to make the scheduler works

Login URL : http://localhost/app/login