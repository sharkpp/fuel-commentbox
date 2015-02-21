#!/bin/bash

# inital working directory is '/home/vagrant'

TMPL_DIR=/vagrant/development/vagrant

##########################################
# install packages
##########################################

if [ ! -e /etc/yum.repos.d/epel.repo ] ; then
	sudo rpm -Uvh http://dl.fedoraproject.org/pub/epel/6/x86_64/epel-release-6-8.noarch.rpm
fi
if [ ! -e /etc/yum.repos.d/remi.repo ] ; then
	sudo rpm -Uvh http://rpms.famillecollet.com/enterprise/remi-release-6.rpm
fi

if ! sudo service httpd status >/dev/null 2>&1 ; then
	sudo yum -y install httpd
	sudo chkconfig httpd on
fi

if ! sudo service mysqld status >/dev/null 2>&1 ; then
	sudo yum install -y --enablerepo=remi mysql-server
	sudo chkconfig mysqld on
fi

if ! php -v >/dev/null 2>&1 ; then
	sudo yum install -y --enablerepo=remi --enablerepo=remi-php55 \
	         php php-mbstring php-mcrypt php-pdo php-mysqlnd php-opcache
fi

sudo service mysqld restart

sudo cp -f $TMPL_DIR/00_httpd_port_8080.conf /etc/httpd/conf.d/port_8080.conf

mysql -u root < $TMPL_DIR/00_mysql_set_password.sql

##########################################
# setup phpMyAdmin
##########################################

sudo yum install -y --enablerepo=remi --enablerepo=remi-php55 phpMyAdmin

sudo mkdir -p /usr/share/phpMyAdmin/config/
sudo cp $TMPL_DIR/01_phpMyAdmin_config.inc.php /usr/share/phpMyAdmin/config/config.inc.php

##########################################
# setup fuelphp
##########################################

sudo cp -pf "/usr/share/zoneinfo/Asia/Tokyo" "/etc/localtime"

pushd /vagrant/development/

if [ ! -d fuelphp ] ; then
#	rm -f fuelphp
	curl get.fuelphp.com/oil | sudo sh
	oil create fuelphp
	cd fuelphp
	git checkout -- composer.phar
	git checkout 1.8/develop
	cp -f $TMPL_DIR/02_site_composer.json composer.json
	php composer.phar update
fi

popd

##########################################
# setup
##########################################

pushd /vagrant/development/fuelphp

rm -f fuel/packages/commentbox
ln -sf /vagrant fuel/packages/commentbox

mysql -u root --password=root < $TMPL_DIR/02_site_create_database.sql

cp $TMPL_DIR/02_site_app_config.php fuel/app/config/development/config.php

cp $TMPL_DIR/02_site_app_config_routes.php fuel/app/config/development/routes.php

cp $TMPL_DIR/02_site_app_config_auth.php fuel/app/config/development/auth.php

cp $TMPL_DIR/02_site_app_controller_test.php fuel/app/classes/controller/test.php

mkdir -p fuel/app/views/test

cp $TMPL_DIR/02_site_app_views_index.php fuel/app/views/test/index.php

rm -f fuel/app/config/development/migrations.php

php oil r migrate --all

sudo cp -f $TMPL_DIR/02_site_httpd.conf /etc/httpd/conf.d/site.conf

popd

sudo service httpd restart
