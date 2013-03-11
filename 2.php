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
		<th>Время в УРВ</th>
	</tr>
	<tr>
	<form action='index.php' method='post' id='filt'>
		<th>
				<input type='hidden' value='2' name='p'  id='p'/>
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

$query = "SELECT     	tURVData.IN_WORK_DATE, 
			Workers.ID_Worker, 
			Workers.F_Worker, 
			Workers.I_Worker, 
			Otdels.NB_Otdel + ' - ' + Otdels.Name_Otdel AS Otdel, 
                      	CASE WHEN ({ fn DAYOFWEEK(IN_WORK_DATE) } IN (2, 3, 4, 5, 6)) AND (SUM(IN_WORK_TIME_MINUTES) > 0) THEN (SUM(IN_WORK_TIME_MINUTES) - SUM(MORNING_TIME_MINUTES)) ELSE SUM(IN_WORK_TIME_MINUTES) END AS Time1, 
			SUM(vURV.time_sum_in_minutes) AS Time2
FROM         tURVData INNER JOIN
                      Workers ON tURVData.ID_Worker = Workers.ID_Worker INNER JOIN
                      Otdels ON Workers.ID_Otdel = Otdels.ID_Otdel INNER JOIN
                      vURV ON Workers.ID_Worker = vURV.ID_Worker AND tURVData.IN_WORK_DATE = vURV.cw_date
WHERE     (tURVData.IN_WORK_DATE < CONVERT(DATETIME, '".(($m>11)?($y+1):$y)."-".(($m>11)?($m-11):($m+1))."-01 00:00:00', 102)) 
	AND (tURVData.IN_WORK_DATE >= CONVERT(DATETIME, '".$y."-".$m."-01 00:00:00', 102))
	AND (Otdels.ID_Otdel NOT IN (20, 28, 31, 43, 108))
	AND (Workers.Fl_Rel = 0)
	AND (IN_WORK_TIME_MINUTES = 0)	";
if (isset($n_otd_f) and $n_otd_f != "%") $query = $query .  "AND  (Otdels.NB_Otdel + ' - ' + Otdels.Name_Otdel like '".$n_otd_f."') ";
if (isset($n_fam_f) and $n_fam_f != "%" and $n_fam_f != 0) $query.="AND (Workers.ID_Worker = ".$n_fam_f.") ";
$query.=" GROUP BY tURVData.IN_WORK_DATE, Workers.ID_Worker, Workers.F_Worker, Workers.I_Worker, Otdels.NB_Otdel + ' - ' + Otdels.Name_Otdel ORDER BY Otdels.NB_Otdel + ' - ' + Otdels.Name_Otdel, Workers.F_Worker, tURVData.IN_WORK_DATE ";
//$query.=" ORDER BY Otdels.NB_Otdel + ' - ' + Otdels.Name_Otdel, Workers.F_Worker, tURVData.IN_WORK_DATE";

$res=mssql_query($query);
while($row=mssql_fetch_row($res)) 
if (($row[6] > 0) and ($row[5] == 0) ) {
	$query="SELECT	SUM(CalWorksDec.TimeW) AS e1
		FROM	CalWorks INNER JOIN
			CalWorksDec ON CalWorks.ID_Rec = CalWorksDec.ID_Rec INNER JOIN
			CalWorksVarWorks ON CalWorksDec.ID_NW = CalWorksVarWorks.ID_NW
		WHERE	(CAST(CAST(CalWorks.NYear AS VARCHAR(4)) + '-' + CAST(CalWorks.NMonth AS VARCHAR(2)) + '-' + CAST(CalWorksDec.NTD AS VARCHAR(2)) AS DATETIME) = CONVERT(DATETIME, '".$row[0]."', 102)) 
			AND (SUBSTRING(CalWorksVarWorks.NameWorks, 1, 1) IN (5, 6, 7) 
				OR SUBSTRING(CalWorks.FTDay, CalWorksDec.NTD, 1) IN (2, 3, 4)) 
			AND (CalWorks.ID_Worker = ".$row[1].")
			GROUP BY CalWorks.ID_Worker";
	$r=mssql_fetch_row(mssql_query($query));
	if ($row[6] - $r[0]*60 == $row[6]) {
		$body.= "<tr class='tab_bg_1'>
			<td>".date('d.m.Y',strtotime($row[0]))."</td>
			<td>".$row[4]."</td>
			<td>".$row[2]." ".$row[3]."</td>
			<td>".round($row[6],1)."</td>
			</tr>";
		$fullname=$row[2]." ".$row[3];
		if (!in_array($fullname,$n_fam)) $n_fam[$row[1]]=$fullname;
		if (!in_array($row[4],$n_otd)) array_push($n_otd,$row[4]);
	}
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