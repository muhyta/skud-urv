<?php
$footer="Фирма"; //---copyrite
$db=mssql_connect("server","user","passwords");//---подключение к серверу базы данных СКУД-УРВ
mssql_select_db("data_base",$db);//---база данных СКУД-УРВ
$db2_srv="server";//---сервер базы данных MPhones
$db2_usr="user";//---пользователь сервера базы данных MPhones
$db2_psw="password";//---пароль пользователя сервера базы данных MPhones
$db2="db_MPhones";//---база данных MPhones
$ou=iconv("CP1251","UTF-8","Уволенные");
$dn = "OU=".$ou.",OU=Users,OU=Group,DC=server,DC=ru";
$domain = "domain_name";
$dom_user="domain_user";
$dom_pass="domain_user_password";
$admin_u = array( 0 => "auth_user1",
                1 => "auth_user2",
                2 => "auth_user3");
?>