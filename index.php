<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
$start = microtime(true);
if (isset($_REQUEST['m'])) $m=$_REQUEST['m'];
	else $m=date('m');
if (isset($_REQUEST['y'])) $y=$_REQUEST['y'];
	else $y=date('Y');

$vers="";
$html=file_get_contents("maket.tm_");   //---�������� ������ ��������
include_once('config.php');             //---������������ � ����������������� ������
include_once('comm.php');               //---������� �� ������ �������������

$html=str_ireplace("%menu%",$menu,$html);
$html=str_ireplace("%leftbar%",$links,$html);
$html=str_ireplace("%rightbar%",$ads,$html);
$html=str_ireplace("%centerbar%",$body,$html);
$html=str_ireplace("%footer%",$footer,$html);
$html=str_ireplace("%header%",$header,$html);
mssql_close($db);                       //---���������� ����������� � ����
$vers = round(microtime(true) - $start,2)."s - " . $vers;
$html=str_ireplace("%version%",$vers,$html);
echo $html;                             //---����������� ��������
?>