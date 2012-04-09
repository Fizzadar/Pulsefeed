#update daemons
* * * * * root php /home/pulsefeed/www/cron.php update source
* * * * * root php /home/pulsefeed/www/cron.php update facebook
* * * * * root php /home/pulsefeed/www/cron.php update twitter

#popularity daemon
* * * * * root php /home/pulsefeed/www/cron.php popularity

#every 30min => popcalc
*/30 * * * * root php /home/pulsefeed/www/cron.php popcalc

#every 2 hour => cleanup
0 */2 * * * root php /home/pulsefeed/www/cron.php cleanup