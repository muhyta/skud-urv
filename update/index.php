<?php
//error_reporting(E_ALL);ini_set("display_errors", 1);
$start = microtime(true);
$html=file_get_contents("maket.tm_"); //---загрузка макета страницы
include_once("../config.php");
$vers="0.1v"; //---version
$menu="<li id='menu1'><a href='/parskud/' class='itemP' onmouseover=\"this.style.backgroundColor='rgb(221,255,112)';\" onmouseout=\"this.style.backgroundColor='transparent';\">Импорт</a></li>
	<li id='menu2'><a href='/update/index.php?p=2'  class='itemP' onmouseover=\"this.style.backgroundColor='rgb(221,255,112)';\" onmouseout=\"this.style.backgroundColor='transparent';\">Администрирование</a></li>
	<li id='menu3'><a href='/update/index.php?p=1' class='itemP' onmouseover=\"this.style.backgroundColor='rgb(221,255,112)';\" onmouseout=\"this.style.backgroundColor='transparent';\">Пользователи</a></li>";
$add="";

if (isset($_REQUEST['m'])) $m=$_REQUEST['m'];
	else $m=date('m');
if (isset($_REQUEST['y'])) $y=$_REQUEST['y'];
	else $y=date('Y');
if (isset($_REQUEST['d'])) $d=$_REQUEST['d'];
	else $d=date('d');

if (substr_count($_SERVER['AUTH_USER'],"VorotnikovMV") || substr_count($_SERVER['AUTH_USER'],"KazantsevaSV") || substr_count($_SERVER['AUTH_USER'],"KozhevnikovAV")) {
    if (isset($_REQUEST['p'])) $p=$_REQUEST['p']; else $p="2";
    switch ($p) {
        case "1":
            $header="Пользователи<br><span style='font-size:11px;color:#c0272b;'>Для добавления пользователя заполните поля формы и нажмите \"+\"<br>Для редактирования пользователя, выберите его из списка.</span>";
            $menu=str_ireplace("<li id='menu3'>","<li id='menu3' style='background-color:rgb(221,255,112);'>",$menu);
            include("user.php"); break;
        case "2":
            $header="Администрирование<br><span style='font-size:11px;color:#c0272b;'>Для изменения времени в СКУД выберите дату, пользователя, время входа/выхода и нажмите \"+\"</span>";
            $menu=str_ireplace("<li id='menu2'>","<li id='menu2' style='background-color:rgb(221,255,112);'>",$menu);
            include_once('update.php'); break;
        default:
            $header="Администрирование<br><span style='font-size:11px;color:#c0272b;'>Для изменения времени в СКУД выберите дату, пользователя, время входа/выхода и нажмите \"+\"</span>";
            include_once('update.php');}
}
else $add = "Access denied!";

$html=str_ireplace("%menu%",$menu,$html);
$html=str_ireplace("%centerbar%",$body,$html);
$html=str_ireplace("%footer%",$footer,$html);
$html=str_ireplace("%header%",$header,$html);
//---1
$html=str_ireplace("%add%",$add,$html);
$html=str_ireplace("%posts%",$post_sel,$html);
$html=str_ireplace("%otdels%",$otd_sel,$html);
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