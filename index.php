<?php
//error_reporting(E_ALL);ini_set("display_errors", 1);
$start = microtime(true);
$links="";
$ads="";
function getUserList() {
    $dbsql01=mssql_connect("SQL01-GTPTMN","command","jlv8ykxred");
    mssql_select_db("sspd_new",$dbsql01);
    $q="SELECT Workers.Login,
              Workers.F_Worker + ' ' + Workers.N_Worker + ' ' + Workers.P_Worker AS fio,
              Otdels.Name_Otdel,
              Posts.N_Post,
              Workers.TabNum
        FROM Workers INNER JOIN Posts ON Workers.ID_Post = Posts.ID_Post INNER JOIN Otdels ON Workers.ID_Otdel = Otdels.ID_Otdel
        WHERE (Workers.Fl_Rel = 0)
        ORDER BY Workers.F_Worker + ' ' + Workers.N_Worker + ' ' + Workers.P_Worker";
    $rs=mssql_query($q);
    while($r=mssql_fetch_row($rs)){
        $user[$r[0]]=array();
        array_push($user[$r[0]],$r[1]);
        array_push($user[$r[0]],$r[2]);
        array_push($user[$r[0]],$r[3]);
        array_push($user[$r[0]],$r[4]);
    }
    mssql_close($dbsql01);
    return $user;
}

if (isset($_REQUEST['m'])) $m=$_REQUEST['m'];
	else $m=date('m');
if (isset($_REQUEST['y'])) $y=$_REQUEST['y'];
	else $y=date('Y');

$vers="";
$html=file_get_contents("maket.tm_");   //---загрузка макета страницы
include_once('config.php');             //---конфигурация и аутентифицирующие данные
include_once('comm.php');               //---выборка по списку представлений

$html=str_ireplace("%menu%",$menu,$html);
$html=str_ireplace("%leftbar%",$links,$html);
$html=str_ireplace("%rightbar%",$ads,$html);
$html=str_ireplace("%centerbar%",$body,$html);
$html=str_ireplace("%footer%",$footer,$html);
$html=str_ireplace("%header%",$header,$html);
mssql_close($db);                       //---завершение подключения к базе
$vers = round(microtime(true) - $start,2)."s - " . $vers;
$html=str_ireplace("%version%",$vers,$html);
$users=getUserList();
$info=$users[substr($_SERVER['AUTH_USER'],7)][0]."\n".$users[substr($_SERVER['AUTH_USER'],7)][1]."\n".$users[substr($_SERVER['AUTH_USER'],7)][2];
$html=str_ireplace("ОИТ","<abbr title='".$info."'>ОИТ</abbr>",$html);
echo $html;                             //---отображение страницы
?>