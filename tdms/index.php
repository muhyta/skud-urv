<?php
/**
 * Created by JetBrains PhpStorm.
 * User: VorotnikovMV
 * Date: 13.06.13
 * Time: 9:23
 * To change this template use File | Settings | File Templates.
 */
//error_reporting(E_ALL);ini_set("display_errors", 1);
$start = microtime(true);
$html=file_get_contents("../maket.tm_"); //---�������� ������ ��������
include_once("../config.php");
$title="����";
$mainmenu="<a href='http://comm.".$domain."/'>�������</a>
    <a href='http://comm.".$domain."/update/index.php'>����</a>
    <a href='http://comm.".$domain."/urv/'>���</a>
    <a style='background-color:rgb(221,255,112);' href='http://comm.".$domain."/tdms/'>����</a>
    <a href='http://comm.".$domain."/tel/'>���������</a>
    <a href='http://comm.".$domain."/graphic/'>���</a>";
$menu="<li id='menu1'><a href='/tdms/?p=1' class='itemP' onmouseover=\"this.style.backgroundColor='rgb(221,255,112)';\" onmouseout=\"this.style.backgroundColor='transparent';\">�������</a></li>";
if (isset($_REQUEST['p'])) $p=$_REQUEST['p']; else $p="1";
switch ($p) {
    case "1":
        $header="�������<br><span style='font-size:11px;color:#c0272b;'>� ������ ����� ������������ ��� ������� ��� ������ ������.</span>";
        $menu=str_ireplace("<li id='menu1'>","<li id='menu1' style='background-color:rgb(221,255,112);'>",$menu);
        include("task.php"); break;
    default:
        $header="�������<br><span style='font-size:11px;color:#c0272b;'>� ������ ����� ������������ ��� ������� ��� ������ ������.</span>";
        $menu=str_ireplace("<li id='menu1'>","<li id='menu1' style='background-color:rgb(221,255,112);'>",$menu);
        include("task.php");}

$html=str_ireplace("%mainmenu%",$mainmenu,$html);
$html=str_ireplace("%menu%",$menu,$html);
$html=str_ireplace("%title%",$title,$html);
$html=str_ireplace("%centerbar%",$body,$html);
$html=str_ireplace("%footer%",$footer,$html);
$html=str_ireplace("%domain%","http://www.".$domain,$html);
$html=str_ireplace("%header%",$header,$html);
$html=str_ireplace("%err%",$err,$html);
//---1
$html=str_ireplace("%add%",$add,$html);
$html=str_ireplace("%log%",$log,$html);
$html=str_ireplace("%posts%",$post_sel,$html);
$html=str_ireplace("%otdels%",$otd_sel,$html);
$html=str_ireplace("%wait_user%",$wait_user,$html);
//---2
$html=str_ireplace("%fio_sel%",$fio_sel,$html);
$html=str_ireplace("%dolz_sel%",$dolz_sel,$html);
$html=str_ireplace("%otdel_sel%",$otdel_sel,$html);
$html=str_ireplace("%in_work_sel%",$in_work_sel,$html);
$html=str_ireplace("%out_work_sel%",$out_work_sel,$html);
$html=str_ireplace("%in_work_time_sel%",$in_work_time_sel,$html);
$html=str_ireplace("%morning_time_sel%",$morning_time_sel,$html);

mssql_close($db);
$vers = microtime(true) - $start;
$html=str_ireplace("%version%",round($vers,2)."s",$html);

echo $html; //---����������� ��������
unset($html);