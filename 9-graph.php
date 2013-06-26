<?php
/**
 * Created by JetBrains PhpStorm.
 * User: VorotnikovMV
 * Date: 18.02.13
 * Time: 18:36
 * To change this template use File | Settings | File Templates.
 */
//$users=getUserList();
//$dep = $users[substr($_SERVER['AUTH_USER'],strpos($_SERVER['AUTH_USER'],"\\")+1)][1];
//var_dump($dep);
error_reporting(E_NONE);ini_set("display_errors", 0);
ini_set("max_execution_time", 0);
include_once('config.php');
$min=100;
$max=0;
if (isset($_REQUEST['m'])) $m=$_REQUEST['m'];
else $m=date('m');
if (isset($_REQUEST['y'])) $y=$_REQUEST['y'];
else $y=date('Y')-1;
if ($m > 0 ) {
$r1 = array();
$r2 = array();
$query="	SELECT	Otdels.ID_Otdel, COUNT(tURVData.IN_WORK_DATE) AS [Сумма дней]
			FROM	Workers INNER JOIN Otdels ON Workers.ID_Otdel = Otdels.ID_Otdel INNER JOIN tURVData ON Workers.ID_Worker = tURVData.ID_Worker
	 		WHERE	(tURVData.IN_WORK_TIME_MINUTES > 0)
				AND (tURVData.IN_WORK_DATE >= CONVERT(DATETIME, '".$y."-".$m."-01 00:00:00', 102))
				AND (tURVData.IN_WORK_DATE < CONVERT(DATETIME, '".(($m>11)?($y+1):$y)."-".(($m>11)?($m-11):($m+1))."-01 00:00:00', 102))
				AND ({ fn DAYOFWEEK(tURVData.IN_WORK_DATE) } IN (1, 7))
				AND (Otdels.ID_Otdel NOT IN (20, 21, 28, 31, 43, 108))
			GROUP BY Otdels.ID_Otdel
			ORDER BY Otdels.ID_Otdel";
$res=mssql_query($query);
while ($r=mssql_fetch_row($res)) {
    array_push($r1,$r[0]);
    array_push($r2,$r[1]);
}
$r3 = array_combine($r1,$r2);
//------------------------------------------------------------------------#################################
$m--;
if ($m < 1) {$m=12;$y--;}
$data = array();
$otd = array();
include_once( 'ext/php-ofc-library/open-flash-chart.php' );
$bar = new bar_fade( 55,'#FAAC58');
$bar->key( date('M',strtotime($y."-".$m."-01 00:00:00")), 10 );
//------------------------------------------------------------------------#################################
$query = "	SELECT	Otdels.NB_Otdel AS Otdel,
				COUNT(DISTINCT Workers.ID_Worker) AS [Количество работников],
				Otdels.ID_Otdel AS Отдел,
				SUM(tURVData.IN_WORK_TIME_MINUTES) AS [Сумма часов],
				COUNT(tURVData.IN_WORK_DATE) AS [Сумма дней]
			FROM	Workers INNER JOIN Otdels ON Workers.ID_Otdel = Otdels.ID_Otdel INNER JOIN tURVData ON Workers.ID_Worker = tURVData.ID_Worker
			WHERE	(tURVData.IN_WORK_TIME_MINUTES > 0)
				AND (tURVData.IN_WORK_DATE >= CONVERT(DATETIME, '".$y."-".$m."-01 00:00:00', 102))
				AND (tURVData.IN_WORK_DATE < CONVERT(DATETIME, '".(($m>11)?($y+1):$y)."-".(($m>11)?($m-11):($m+1))."-01 00:00:00', 102))
				AND (Otdels.ID_Otdel NOT IN (20, 21, 28, 31, 43, 108))
			GROUP BY Otdels.ID_Otdel,Otdels.NB_Otdel";
$res=mssql_query($query);
while($row=mssql_fetch_row($res)) {
    array_push($otd,iconv('CP1251','UTF-8',$row[0]));
    $sum_time=round($row[3] / 60,2);
    $sum_days=$row[4]-$r3[$row[2]];
    $result_skud=round($sum_time/$sum_days,2);
    $max = ($result_skud > $max) ? $result_skud : $max;
    $min = ($result_skud < $min) ? $result_skud : $min;
    $bar->data[] = $result_skud;
}
//------------------------------------------------------------------------#################################
$query="	SELECT	Otdels.ID_Otdel, COUNT(tURVData.IN_WORK_DATE) AS [Сумма дней]
			FROM	Workers INNER JOIN Otdels ON Workers.ID_Otdel = Otdels.ID_Otdel INNER JOIN tURVData ON Workers.ID_Worker = tURVData.ID_Worker
	 		WHERE	(tURVData.IN_WORK_TIME_MINUTES > 0)
				AND (tURVData.IN_WORK_DATE >= CONVERT(DATETIME, '".$y."-".$m."-01 00:00:00', 102))
				AND (tURVData.IN_WORK_DATE < CONVERT(DATETIME, '".(($m>11)?($y+1):$y)."-".(($m>11)?($m-11):($m+1))."-01 00:00:00', 102))
				AND ({ fn DAYOFWEEK(tURVData.IN_WORK_DATE) } IN (1, 7))
				AND (Otdels.ID_Otdel NOT IN (20, 21, 28, 31, 43, 108))
			GROUP BY Otdels.ID_Otdel
			ORDER BY Otdels.ID_Otdel";
$res=mssql_query($query);
while ($r=mssql_fetch_row($res)) {
    array_push($r1,$r[0]);
    array_push($r2,$r[1]);
}
$r3 = array_combine($r1,$r2);
//------------------------------------------------------------------------#################################
unset($data,$otd);
$m++;
if ($m > 12) {$m=1;$y++;}
$data = array();
$otd = array();
//include_once( 'ext/php-ofc-library/open-flash-chart.php' );
$bar2 = new bar_fade( 55,'#717DD1');
$bar2->key( date('M',strtotime($y."-".$m."-01 00:00:00")), 10 );
//------------------------------------------------------------------------#################################
$query = "	SELECT	Otdels.NB_Otdel AS Otdel,
				COUNT(DISTINCT Workers.ID_Worker) AS [Количество работников],
				Otdels.ID_Otdel AS Отдел,
				SUM(tURVData.IN_WORK_TIME_MINUTES) AS [Сумма часов],
				COUNT(tURVData.IN_WORK_DATE) AS [Сумма дней]
			FROM	Workers INNER JOIN Otdels ON Workers.ID_Otdel = Otdels.ID_Otdel INNER JOIN tURVData ON Workers.ID_Worker = tURVData.ID_Worker
			WHERE	(tURVData.IN_WORK_TIME_MINUTES > 0)
				AND (tURVData.IN_WORK_DATE >= CONVERT(DATETIME, '".$y."-".$m."-01 00:00:00', 102))
				AND (tURVData.IN_WORK_DATE < CONVERT(DATETIME, '".(($m>11)?($y+1):$y)."-".(($m>11)?($m-11):($m+1))."-01 00:00:00', 102))
				AND (Otdels.ID_Otdel NOT IN (20, 21, 28, 31, 43, 108))
			GROUP BY Otdels.ID_Otdel,Otdels.NB_Otdel";
$res=mssql_query($query);
while($row=mssql_fetch_row($res)) {
    array_push($otd,iconv('CP1251','UTF-8',$row[0]));
    $sum_time=round($row[3] / 60,2);
    $sum_days=$row[4]-$r3[$row[2]];
    $result_skud=round($sum_time/$sum_days,2);
    $max = ($result_skud > $max) ? $result_skud : $max;
    $min = ($result_skud < $min) ? $result_skud : $min;
    $bar2->data[] = $result_skud;
}
//------------------------------------------------------------------------#################################

    $g = new graph();
    $g->data_sets[] = $bar;
    $g->data_sets[] = $bar2;
}
else {
//------------------------------------------------------------------------#################################
    $bar = array();
    $c = array(
        1 => "#ffcc00",
        2 => "#eecc11",
        3 => "#ddcc22",
        4 => "#cccc33",
        5 => "#bbcc44",
        6 => "#aacc55",
        7 => "#99cc66",
        8 => "#88cc77",
        9 => "#77cc88",
        10 => "#66cc99",
        11 => "#55ccAA",
        12 => "#44ccBB",
    );
    for ($i=1;$i<13;$i++) {
        $query="	SELECT	Otdels.ID_Otdel, COUNT(tURVData.IN_WORK_DATE) AS [Сумма дней]
			FROM	Workers INNER JOIN Otdels ON Workers.ID_Otdel = Otdels.ID_Otdel INNER JOIN tURVData ON Workers.ID_Worker = tURVData.ID_Worker
	 		WHERE	(tURVData.IN_WORK_TIME_MINUTES > 0)
				AND (tURVData.IN_WORK_DATE >= CONVERT(DATETIME, '".$y."-".$i."-01 00:00:00', 102))
				AND (tURVData.IN_WORK_DATE < CONVERT(DATETIME, '".(($m>11)?($y+1):$y)."-".(($i>11)?($i-11):($i+1))."-01 00:00:00', 102))
				AND ({ fn DAYOFWEEK(tURVData.IN_WORK_DATE) } IN (1, 7))
				AND (Otdels.ID_Otdel NOT IN (20, 21, 28, 31, 43, 108))
			GROUP BY Otdels.ID_Otdel
			ORDER BY Otdels.ID_Otdel";
        $res=mssql_query($query);
        while ($r=mssql_fetch_row($res)) {
            array_push($r1,$r[0]);
            array_push($r2,$r[1]);
        }
        $r3 = array_combine($r1,$r2);
//------------------------------------------------------------------------#################################
        unset($data,$otd);
        $data = array();
        $otd = array();
        include_once( 'ext/php-ofc-library/open-flash-chart.php' );
        $bar[$i] = new bar_outline( 50, $c[$i], '#8010A0' );
        $bar[$i]->key( date('M',strtotime($y."-".$i."-01 00:00:00")), 10 );
//------------------------------------------------------------------------#################################
        $query = "	SELECT	Otdels.NB_Otdel AS Otdel,
				COUNT(DISTINCT Workers.ID_Worker) AS [Количество работников],
				Otdels.ID_Otdel AS Отдел,
				SUM(tURVData.IN_WORK_TIME_MINUTES) AS [Сумма часов],
				COUNT(tURVData.IN_WORK_DATE) AS [Сумма дней]
			FROM	Workers INNER JOIN Otdels ON Workers.ID_Otdel = Otdels.ID_Otdel INNER JOIN tURVData ON Workers.ID_Worker = tURVData.ID_Worker
			WHERE	(tURVData.IN_WORK_TIME_MINUTES > 0)
				AND (tURVData.IN_WORK_DATE >= CONVERT(DATETIME, '".$y."-".$i."-01 00:00:00', 102))
				AND (tURVData.IN_WORK_DATE < CONVERT(DATETIME, '".(($i>11)?($y+1):$y)."-".(($i>11)?($i-11):($i+1))."-01 00:00:00', 102))
				AND (Otdels.ID_Otdel NOT IN (20, 21, 28, 31, 43, 108))
			GROUP BY Otdels.ID_Otdel,Otdels.NB_Otdel";
        $res=mssql_query($query);
        while($row=mssql_fetch_row($res)) {

            array_push($otd,($y > 2012) ? iconv('CP1252','CP1251',$row[0]) : iconv('CP1251','UTF-8',$row[0]));

            $sum_time=round($row[3] / 60,2);
            $sum_days=$row[4]-$r3[$row[2]];
            $result_skud=round($sum_time/$sum_days,2);
            $max = ($result_skud > $max) ? $result_skud : $max;
            $min = ($result_skud < $min) ? $result_skud : $min;
            $bar[$i]->data[] = $result_skud-8;
        }
        var_dump ($otd);
    }
//------------------------------------------------------------------------#################################

    $max=2;
    $min=-1;
    $g = new graph();
    $g->data_sets = $bar;
}
$g->bg_colour = '#FFFFFF';
$g->title('', '{font-size: 16px;}' );
$g->set_tool_tip('#key#: #x_label#<br>#val#');
$g->set_x_labels( $otd );
$g->set_y_max($max+1);
$g->set_y_min($min-1);
$g->y_label_steps(20);
echo $g->render();



?>