# Dailylogs v1.0

## Requirements

php5<br />
mysql<br />
libssh2-php <br />

## Setup

Commands :<br />

git clone https://github.com/Denamok/dailylogs.git<br />
chmod 775 dailylogs (user www-data must have write access)<br />
cd dailylogs<br />
chmod 777 old<br />

On MySQL :<br />

create database dailylogs;<br />
CREATE USER 'poney'@'localhost' IDENTIFIED BY 'neypo';<br />
GRANT ALL ON mysuit.* TO 'poney'@'localhost';<br />

Commands :<br />
cp config.php.template config.php (replace with your own settings)<br />
mysql -u poney -p -D dailylogs < setup/setup.sql<br />

## Automatic log upload

Cron the following command :

php5 dailylogs/parse_logs.php


