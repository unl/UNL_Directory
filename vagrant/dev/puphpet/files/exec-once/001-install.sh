SYSTEM_BASEDIR="/var/www/html"

echo "installing directory"

#Go to the basedir to preform commands.
cd $SYSTEM_BASEDIR

git submodule init
git submodule update

#copy .htaccess
if [ ! -f ${SYSTEM_BASEDIR}/www/.htaccess ]; then
    echo "Creating .htaccess"
    cp ${SYSTEM_BASEDIR}/www/sample.htaccess ${SYSTEM_BASEDIR}/www/.htaccess
fi

#copy config
if [ ! -f ${SYSTEM_BASEDIR}/www/config.inc.php ]; then
    echo "Creating config.inc.php"
    cp ${SYSTEM_BASEDIR}/www/config-sample.inc.php ${SYSTEM_BASEDIR}/www/config.inc.php
fi

#make a link to the phpmyadmin
ln -s /var/www/html/phpmyadmin/ /var/www/html/www/phpmyadmin

echo "FINISHED installing"
