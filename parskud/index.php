<?php
//error_reporting(E_ALL);ini_set("display_errors", 1);//var_dump($_FILES);
$start = microtime(true);
ini_set("max_execution_time", 0);
//----------------------------------------
$insrt=0;//---���� ������
//----------------------------------------
$html=file_get_contents("maket.tm_"); //---�������� ������ ��������
include("../config.php");
$header="�������� ������ �� ����";
$mainmenu="<a href='http://comm.".$domain."/'>�������</a>
    <a style='background-color:rgb(221,255,112);' href='http://comm.".$domain."/update/index.php'>����</a>
    <a href='http://comm.".$domain."/urv/'>���</a>
    <a href='http://comm.".$domain."/tdms/'>����</a>
    <a href='http://comm.".$domain."/tel/'>���������</a>
    <a href='http://comm.".$domain."/graphic/'>���</a>";
$menu="<li id='menu1'><a style='background-color:rgb(221,255,112);' href='/parskud/' class='itemP'>������</a></li>
	<li id='menu2'><a class='itemP' href='/update/index.php?p=2' onmouseover=\"this.style.backgroundColor='rgb(221,255,112)';\" onmouseout=\"this.style.backgroundColor='transparent';\">�����������������</a></li>
	<li id='menu3'><a  href='/update/index.php?p=1' class='itemP' onmouseover=\"this.style.backgroundColor='rgb(221,255,112)';\" onmouseout=\"this.style.backgroundColor='transparent';\">������������</a></li>";

if (substr_count($_SERVER['AUTH_USER'],$admin_u[0]) || substr_count($_SERVER['AUTH_USER'],$admin_u[1]) || substr_count($_SERVER['AUTH_USER'],$admin_u[2])) {
    include_once('convert.php');
}
else $body="<table class='tab_cadre_pager'><tr><td>Access denied!</td></tr></table>";
mssql_close($db);
$html = str_ireplace("%filename%","",$html);
$html=str_ireplace("%mainmenu%",$mainmenu,$html);
$html=str_ireplace("%menu%",$menu,$html);
$html=str_ireplace("%centerbar%",$body,$html);
$html=str_ireplace("%footer%",$footer,$html);
$html=str_ireplace("%domain%","http://www.".$domain,$html);
$html=str_ireplace("%header%",$header,$html);
$vers = round(microtime(true) - $start,5) ."s ". $vers;
$html=str_ireplace("%version%",$vers,$html);
echo $html; //---����������� ��������
?>