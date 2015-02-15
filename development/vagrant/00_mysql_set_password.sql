use mysql;
update user set password=PASSWORD("root") where User='root';
flush privileges;
