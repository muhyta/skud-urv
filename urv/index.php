<?php
//error_reporting(E_ALL);ini_set("display_errors", 1);
$start = microtime(true);
$vers="";
$links="";
$ads="";
$html=file_get_contents("maket.tm_"); //---�������� ������ ��������
include_once("../config.php");
$mainmenu="<a href='http://comm.".$domain."/'>�������</a>
    <a href='http://comm.".$domain."/update/index.php'>����</a>
    <a style='background-color:rgb(221,255,112);' href='http://comm.".$domain."/urv/'>���</a>
    <a href='http://comm.".$domain."/tdms/'>����</a>
    <a href='http://comm.".$domain."/tel/'>���������</a>
    <a href='http://comm.".$domain."/graphic/'>���</a>";
$menu="<li id='menu1'><a href='#' class='itemP' onclick=\"document.getElementById('p').value=1;document.getElementById('fill').submit();\" onmouseover=\"this.style.backgroundColor='rgb(221,255,112)';\" onmouseout=\"this.style.backgroundColor='transparent';\">�������������</a></li>
	<li id='menu2'><a href='#' class='itemP' onclick=\"document.getElementById('p').value=2;document.getElementById('fill').submit();\" onmouseover=\"this.style.backgroundColor='rgb(221,255,112)';\" onmouseout=\"this.style.backgroundColor='transparent';\">����������</a></li>";

if (isset($_REQUEST['p'])) $p=$_REQUEST['p']; else $p="1";
switch ($p) {
    case "1":
        $header="�������������<br><span style='font-size:11px;color:#c0272b;'>��� ���������� ������� ���� � ������������ ������� � ������� \"+\".<br>��� �������������� �������, �������� ����������� ������� �� ������.</span>";
        $menu=str_ireplace("<li id='menu1'>","<li id='menu1' style='background-color:rgb(221,255,112);'>",$menu);
        include("add.php"); break;
    case "2":
        $header="�����������<br><span style='font-size:11px;color:#c0272b;'>��� ���������� ���� ������ � ���� - �������� ����������� �����.<br>����� ����� �������� ����, ������������ �������� ����� ��������, � ������� \"����������\".</span>";
        $menu=str_ireplace("<li id='menu2'>","<li id='menu2' style='background-color:rgb(221,255,112);'>",$menu);
        include("combine.php"); break;
    default: $header="�������������<br><span style='font-size:11px;color:#c0272b;'>��� ���������� ������� ���� � ������������ ������� � ������� \"+\".<br>��� �������������� �������, �������� ����������� ������� �� ������.</span>";
        $menu=str_ireplace("<li id='menu2'>","<li id='menu2' style='background-color:rgb(221,255,112);'>",$menu);
        include("add.php");}

$html=str_ireplace("%menu%",$menu,$html);
$html=str_ireplace("%mainmenu%",$mainmenu,$html);
$html=str_ireplace("%leftbar%",$links,$html);
$html=str_ireplace("%rightbar%",$ads,$html);
$html=str_ireplace("%centerbar%",$body,$html);
$html=str_ireplace("%footer%",$footer,$html);
$html=str_ireplace("%header%",$header,$html);
$html=str_ireplace("%add%",$add,$html);
$html=str_ireplace("%domain%","http://www.".$domain,$html);

mssql_close($db);
$vers = round(microtime(true) - $start,2)."s - " . $vers;
$html=str_ireplace("%version%",$vers,$html);
echo $html; //---����������� ��������
unset($html);
?>