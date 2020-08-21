# The project uses
1. Composer
1. MySQL ver. 14.14
1. Redis ver. 4.0.9
1. PHP ver. 7.2.24

# How to start
1. Install Composer's dependencies;
1. Create DB in MySQL;
1. Create DB structure from dump `sql/Dump.sql`;
1. Create database_config.php file in `src/app/config`. There is also an example;
1. Create redis_config.php in `src/app/config`. There is also an example;
1. There is proxy-list in `src/app/config/proxy.json`. If you want, you can add/change socks5 proxy-addresses. 
There is also an example;
1. For starting execute `php index.php`;
1. The log file will be created at `src/logs/logfile.log`.
