<?php
//---фильтры инициализация и получение
$n_fam = array();
$n_otd = array();
array_push($n_fam,"Все");
array_push($n_otd,"Все");
if (isset($_REQUEST['f_name']) and $_REQUEST['f_name'] !="Все") $n_fam_f=htmlspecialchars($_REQUEST['f_name']);
	else $n_fam_f="%";
if (isset($_REQUEST['o_name']) and $_REQUEST['o_name'] !="Все") $n_otd_f=htmlspecialchars($_REQUEST['o_name']);
	else $n_otd_f="%";
//---конец фильтры инициализация и получение
$body="<table class='tab_cadrehov'>
	<tr class='tab_bg_2'>
		<th>Дата</th>
		<th>Отдел</th>
		<th>ФИО</th>
		<th>Время в СКУД</th>
	</tr>
	<tr>
	<form action='index.php' method='post' id='filt'>
		<th>
				<input type='hidden' value='3' name='p'  id='p'/>
				<select name='m' onchange=\"start();document.getElementById('filt').submit();\">
					".$m_sel."
				</select>
				<select name='y' onchange=\"start();document.getElementById('filt').submit();\">
					".$y_sel."
				</select>
		</th>
		<th>%отдел%</th>
		<th>%фио%</th>
		<th></th>
	</form>		
	</tr>";
$query="SELECT	TOP 100 PERCENT tURVData.IN_WORK_DATE, 
		Otdels.NB_Otdel + ' - ' + Otdels.Name_Otdel AS Otdel, 
		Workers.F_Worker + ' ' + Workers.N_Worker + ' ' + Workers.P_Worker AS FIO, 
		tURVData.IN_WORK_TIME_MINUTES, 
		CASE WHEN (	SELECT 	TOP 1 SUBSTRING(FTDay, DAY(tURVData.IN_WORK_DATE), 1) 
				FROM 	CalWorks
				WHERE 	(CalWorks.ID_Worker = tURVData.ID_Worker) 
					AND (CalWorks.NYear = YEAR(tURVData.IN_WORK_DATE)) 
					AND (CalWorks.NMonth = MONTH(tURVData.IN_WORK_DATE))) IN (2, 3, 4) THEN 1 
		ELSE 0 END AS Vacation, 
		tURVData.IN_WORK_DATE AS Expr1,
		tURVData.ID_Worker
	FROM	vURVSmall RIGHT OUTER JOIN
		tURVData INNER JOIN
		Workers INNER JOIN
		Otdels ON Workers.ID_Otdel = Otdels.ID_Otdel ON tURVData.ID_Worker = Workers.ID_Worker ON 
		vURVSmall.cw_date = tURVData.IN_WORK_DATE AND vURVSmall.ID_Worker = tURVData.ID_Worker
	WHERE	(vURVSmall.time_sum_in_minutes IS NULL 
		OR vURVSmall.time_sum_in_minutes = 0) 
		AND (tURVData.IN_WORK_DATE >= CONVERT(DATETIME, '".$y."-".$m."-01 00:00:00', 102)) 
		AND (tURVData.IN_WORK_TIME_MINUTES > 0) 
		AND (Otdels.ID_Otdel IN (2, 5, 6, 7, 27, 29, 31, 32, 36, 37, 40, 42)) 
		AND (tURVData.IN_WORK_DATE < CONVERT(DATETIME, '".(($m>11)?($y+1):$y)."-".(($m>11)?($m-11):($m+1))."-01 00:00:00', 102))
		AND ({ fn DAYOFWEEK(IN_WORK_DATE) } IN (2, 3, 4, 5, 6)) ";
if (isset($n_otd_f) and $n_otd_f != "%") $query = $query .  "AND  (Otdels.NB_Otdel + ' - ' + Otdels.Name_Otdel like '".$n_otd_f."') ";
if (isset($n_fam_f) and $n_fam_f != "%" and $n_fam_f != 0) $query.="AND (Workers.ID_Worker = ".$n_fam_f.") ";
$query.="ORDER BY Otdels.NB_Otdel + ' - ' + Otdels.Name_Otdel, Workers.F_Worker + ' ' + Workers.N_Worker + ' ' + Workers.P_Worker, tURVData.IN_WORK_DATE";

$res=mssql_query($query);
while($row=mssql_fetch_row($res)) 
if ($row[4] == 0 and $row[3] > 60) {
	$body.= "<tr class='tab_bg_1'>
		<td>".date('d.m.Y',strtotime($row[0]))."</td>
		<td>".$row[1]."</td>
		<td>".$row[2]."</td>
		<td>".$row[3]."</td>
		</tr>";
	if (!in_array($row[2],$n_fam)) $n_fam[$row[6]]=$row[2];
	if (!in_array($row[1],$n_otd)) array_push($n_otd,$row[1]);
}
$body.="</table>";

asort($n_fam);
asort($n_otd);

$filter="<select name='f_name' onchange=\"document.getElementById('filt').submit();\">";
foreach ($n_fam as $key=>$value) 
	if ($n_fam_f != "%" and $key == $n_fam_f) $filter.="<option value=".$key." selected>".$value."</option>";
	elseif ($n_fam_f == "%" and $value == "Все") $filter.="<option value='%' selected>".$value."</option>";
	else $filter.="<option value=".$key.">".$value."</option>";
$filter.="</select>";
$body=str_ireplace("%фио%",$filter,$body);
$filter="<select name='o_name' onchange=\"document.getElementById('filt').submit();\">";
foreach ($n_otd as $key=>$value) 
	if ($n_otd_f != "%" and $value == $n_otd_f) $filter.="<option selected>".$value."</option>";
	elseif ($n_otd_f == "%" and $value == "Все") $filter.="<option selected>".$value."</option>";
	else $filter.="<option>".$value."</option>";
$filter.="</select>";
$body=str_ireplace("%отдел%",$filter,$body);
?>

