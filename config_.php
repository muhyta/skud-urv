<?php
$footer="�����"; //---copyrite
$db=mssql_connect("server","user","passwords");//---����������� � ������� ���� ������ ����-���
mssql_select_db("data_base",$db);//---���� ������ ����-���
$db2_srv="server";//---������ ���� ������ MPhones � ���
$db3_srv="server";//---������ ���� ������ TDMS
$db2_usr="user";//---������������ ������� ���� ������ MPhones
$db2_psw="password";//---������ ������������ ������� ���� ������ MPhones
$db2="db_MPhones";//---���� ������ MPhones
$db2_r="db_remarks";//---���� ������ ���
$ou=iconv("CP1251","UTF-8","���������");
$dn = "OU=".$ou.",OU=Users,OU=Group,DC=server,DC=ru";
$domain = "domain_name";
$dom_user="domain_user";
$dom_pass="domain_user_password";
$admin_u = array( 0 => "auth_user1",
                1 => "auth_user2",
                2 => "auth_user3");//---������ ��������������� �������
$id_worker=array(
    "admin3" => 1,
    "admin2" => 2,
    "admin1" => 3);//ID_Workers ����������� ������ ��� � ������� ����-���
?>