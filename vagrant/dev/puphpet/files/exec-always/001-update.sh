SYSTEM_BASEDIR="/var/www/html"
SYSTEM_UPDATE="upgrade.php"

echo "updating system"

#Go to the basedir to preform commands.
cd $SYSTEM_BASEDIR

php $SYSTEM_UPDATE

echo "FINISHED updating"
