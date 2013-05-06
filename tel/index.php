<?php
//error_reporting(E_ALL);ini_set("display_errors", 1);
$start = microtime(true);
$html=file_get_contents("maket.tm_"); //---загрузка макета страницы
include_once("../config.php");
$vers="1v"; //---version
$menu="<li id='menu1'><a href='/tel/?p=1' class='itemP' onmouseover=\"this.style.backgroundColor='rgb(221,255,112)';\" onmouseout=\"this.style.backgroundColor='transparent';\">Список</a></li>
	<li id='menu2'><a href='/tel/?p=2'  class='itemP' onmouseover=\"this.style.backgroundColor='rgb(221,255,112)';\" onmouseout=\"this.style.backgroundColor='transparent';\">Ничего</a></li>";
$add="";

if (substr_count($_SERVER['AUTH_USER'],$admin_u[0]) || substr_count($_SERVER['AUTH_USER'],$admin_u[1]) || substr_count($_SERVER['AUTH_USER'],$admin_u[2])) {
    if (isset($_REQUEST['p'])) $p=$_REQUEST['p']; else $p="1";
    switch ($p) {
        default:
        case "1":
            $header="Список телефонов<br><span style='font-size:11px;color:#c0272b;'>Для добавления телефона заполните поле формы и нажмите Добавить.<br>Если телефон является личным, проставьте галочку.<br>Для редактирования телефона, выберите его из списка.</span>";
            $menu=str_ireplace("<li id='menu1'>","<li id='menu1' style='background-color:rgb(221,255,112);'>",$menu);
            include("list.php"); break;
        case "2":
            $header="Ничегошеньки<br><span style='font-size:11px;color:#c0272b;'>Для изменения ничевошеньки - ничего не нужно</span>";
            $menu=str_ireplace("<li id='menu2'>","<li id='menu2' style='background-color:rgb(221,255,112);'>",$menu);
            include_once('nope.php'); break;
        }}
else $add = "Access denied!";

$html=str_ireplace("%menu%",$menu,$html);
$html=str_ireplace("%centerbar%",$body,$html);
$html=str_ireplace("%footer%",$footer,$html);
$html=str_ireplace("%domain%","http://www.".$domain,$html);
$html=str_ireplace("%header%",$header,$html);
$html=str_ireplace("%err%",$err,$html);
//---1
$html=str_ireplace("%add%",$add,$html);
$html=str_ireplace("%log%",$log,$html);
$html=str_ireplace("%users%",$usr_sel,$html);
//---2
$html=str_ireplace("%fio_sel%",$fio_sel,$html);
$html=str_ireplace("%dolz_sel%",$dolz_sel,$html);
$html=str_ireplace("%otdel_sel%",$otdel_sel,$html);
$html=str_ireplace("%in_work_sel%",$in_work_sel,$html);
$html=str_ireplace("%out_work_sel%",$out_work_sel,$html);
$html=str_ireplace("%in_work_time_sel%",$in_work_time_sel,$html);
$html=str_ireplace("%morning_time_sel%",$morning_time_sel,$html);

mssql_close($db); //---завершение подключения к базе
$vers = microtime(true) - $start;
$html=str_ireplace("%version%",$vers."s",$html);

echo $html; //---отображение страницы
unset($html);
?>