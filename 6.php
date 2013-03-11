<?php
$start = microtime(true);
if ($m != "0") {//---по месяцам
	$body="<table class='tab_cadrehov'>
		<tr class='tab_bg_2'>
			<th>
				Отдел<br>
				<form action='index.php' method='post' id='filt'>
					<input type='hidden' value='6' name='p' id='p'/>
					<select name='m' onchange=\"start();document.getElementById('filt').submit();\">
						".$m_sel."
					</select>
					<select name='y' onchange=\"start();document.getElementById('filt').submit();\">
						".$y_sel."
					</select>
				</form>
			</th>
			<th>Суммарно за месяц,<br>минут</th>
			<th>Суммарно за месяц,<br>часов</th>
			<th>Работников в отделе</th>
			<th>Количество отработанных<br>дней в месяце</th>
			<th>Средний<br>трудодень</th>
		</tr>";
	$r1 = array();
	$r2 = array();
	$query="	SELECT	Otdels.ID_Otdel, COUNT(tURVData.IN_WORK_DATE) AS [Сумма дней]
			FROM	Workers INNER JOIN Otdels ON Workers.ID_Otdel = Otdels.ID_Otdel INNER JOIN tURVData ON Workers.ID_Worker = tURVData.ID_Worker
	 		WHERE	(tURVData.IN_WORK_TIME_MINUTES > 0) 
				AND (tURVData.IN_WORK_DATE >= CONVERT(DATETIME, '".$y."-".$m."-01 00:00:00', 102)) 
				AND (tURVData.IN_WORK_DATE < CONVERT(DATETIME, '".(($m>11)?($y+1):$y)."-".(($m>11)?($m-11):($m+1))."-01 00:00:00', 102)) 
				AND ({ fn DAYOFWEEK(tURVData.IN_WORK_DATE) } IN (1, 7))
				AND (Otdels.ID_Otdel <> 28)
			GROUP BY Otdels.ID_Otdel
			ORDER BY Otdels.ID_Otdel";
	$res=mssql_query($query);
	while ($r=mssql_fetch_row($res)) {
		array_push($r1,$r[0]);
		array_push($r2,$r[1]);	
	}
	$r3 = array_combine($r1,$r2);
	$query = "	SELECT	Otdels.NB_Otdel + ' - ' + Otdels.Name_Otdel AS Otdel, 
				COUNT(DISTINCT Workers.ID_Worker) AS [Количество работников], 
				Otdels.ID_Otdel AS Отдел, 
				SUM(tURVData.IN_WORK_TIME_MINUTES) AS [Сумма часов], 
				COUNT(tURVData.IN_WORK_DATE) AS [Сумма дней]
			FROM	Workers INNER JOIN Otdels ON Workers.ID_Otdel = Otdels.ID_Otdel INNER JOIN tURVData ON Workers.ID_Worker = tURVData.ID_Worker
			WHERE	(tURVData.IN_WORK_TIME_MINUTES > 0) 
				AND (tURVData.IN_WORK_DATE >= CONVERT(DATETIME, '".$y."-".$m."-01 00:00:00', 102)) 
				AND (tURVData.IN_WORK_DATE < CONVERT(DATETIME, '".(($m>11)?($y+1):$y)."-".(($m>11)?($m-11):($m+1))."-01 00:00:00', 102)) 
				AND (Otdels.ID_Otdel NOT IN (20, 22, 28, 31, 43, 108))
			GROUP BY Otdels.ID_Otdel,Otdels.NB_Otdel + ' - ' + Otdels.Name_Otdel";
	$res=mssql_query($query);
	while($row=mssql_fetch_row($res)) {
		$sum_time=round($row[3] / 60,2);
		$sum_days=$row[4]-$r3[$row[2]];
		$result_skud=round($sum_time/$sum_days,2);
		$body.= "<tr class='tab_bg_1'>
				<td>".$row[0]."</td>
				<td>".$row[3]."</td>
				<td>".$sum_time."</td>
				<td><abbr title='...'>".$row[1]."</abbr></td>
				<td>".$sum_days."</td>
				<td>".$result_skud."</td> 
			</tr>"; 
	}
		//---------- <td style='background-color: rgb(".$colr[0].",".$colr[1].",".$colr[2].");color: white;'>".$r."</td>
		//---------- $colr= array (0 => 255-($r*25), 1 => $r*15, 2 => $r*25);
	$body.="</table>";
} elseif ($m == "0") {//---за год
	$body="<table class='tab_cadrehov'>

		<tr class='tab_bg_2'>
			<th>
				Отдел<br>
				<form action='index.php' method='post' id='filt'>
					<input type='hidden' value='6' name='p' id='p'/>
					<select name='m' onchange=\"start();document.getElementById('filt').submit();\">
						".$m_sel."
					</select>
					<select name='y' onchange=\"start();document.getElementById('filt').submit();\">
						".$y_sel."
					</select>
				</form>
			</th>
			<th>Январь</th>
			<th>Февраль</th>
			<th>Март</th>
			<th>Апрель</th>
			<th>Май</th>
			<th>Июнь</th>
			<th>Июль</th>
			<th>Август</th>
			<th>Сентябрь</th>
			<th>Октябрь</th>
			<th>Ноябрь</th>
			<th>Декабрь</th>
			<th>Среднее <br>арифметическое</th>
		</tr>";
	$r1 = array();
	$r2 = array();
	$result_skud=array();
	$result_skud_tbl=array();
	$name_otdel_tbl=array();
	$name_otdel=array();
	for ($i=1;$i<13;$i++) {
		$query="SELECT	Otdels.ID_Otdel,
					COUNT(tURVData.IN_WORK_DATE) AS [Сумма дней]
				FROM	Workers INNER JOIN Otdels ON Workers.ID_Otdel = Otdels.ID_Otdel INNER JOIN tURVData ON Workers.ID_Worker = tURVData.ID_Worker
		 		WHERE	(tURVData.IN_WORK_TIME_MINUTES > 0) 
					AND (tURVData.IN_WORK_DATE >= CONVERT(DATETIME, '".$y."-".$i."-01 00:00:00', 102)) 
					AND (tURVData.IN_WORK_DATE < CONVERT(DATETIME, '".(($i>11)?($y+1):$y)."-".(($i>11)?($i-11):($i+1))."-01 00:00:00', 102)) 
					AND ({ fn DAYOFWEEK(tURVData.IN_WORK_DATE) } IN (1, 7))
					AND (Otdels.ID_Otdel <> 28)
				GROUP BY Otdels.ID_Otdel
				ORDER BY Otdels.ID_Otdel";
		$res=mssql_query($query);
		while ($r=mssql_fetch_row($res)) {
			array_push($r1,$r[0]);
			array_push($r2,$r[1]);	
		}
		$r3 = array_combine($r1,$r2);
		array_splice($r1,0);
		array_splice($r2,0);
		$query="SELECT	Otdels.Name_Otdel, 
				SUM(tURVData.IN_WORK_TIME_MINUTES) AS Минут, 
				COUNT(tURVData.IN_WORK_DATE) AS [Отработано дней], 
				Otdels.ID_Otdel
			FROM	Otdels INNER JOIN
				Workers ON Otdels.ID_Otdel = Workers.ID_Otdel INNER JOIN
				tURVData ON Workers.ID_Worker = tURVData.ID_Worker
			WHERE	(tURVData.IN_WORK_DATE >= CONVERT(DATETIME, '".$y."-".$i."-01 00:00:00', 102)) 
				AND (tURVData.IN_WORK_DATE < CONVERT(DATETIME,'".(($i>11)?($y+1):$y)."-".(($i>11)?($i-11):($i+1))."-01 00:00:00', 102)) 
				AND (tURVData.IN_WORK_TIME_MINUTES > 0)
				AND (Otdels.ID_Otdel NOT IN (20, 22, 28, 31, 43, 108))
			GROUP BY Otdels.Name_Otdel,Otdels.ID_Otdel
			ORDER BY Otdels.ID_Otdel";
		$res=mssql_query($query);
		while($row=mssql_fetch_row($res)) {
			array_push($name_otdel,$row[0]);
			$sum_time=round($row[1] / 60,2);
			$sum_days=$row[2]-$r3[$row[3]];
			array_push($result_skud,round($sum_time/$sum_days,2));
		}
		$result_skud_tbl[$i]=array_combine($name_otdel,$result_skud);
		array_splice($name_otdel,0);
		array_splice($result_skud,0);
		array_splice($r3,0);
	}

	foreach ($result_skud_tbl as $key1 => $value1) {
		foreach ($value1 as $key => $value) {
			$sr_ar=0;
			$body.= "<tr class='tab_bg_1'>";
			$body.= "<td>".$key."</td>";
			for ($j=1;$j<13;$j++) {
				$sr_ar=$sr_ar+$result_skud_tbl[$j][$key];
				$body.= "<td>".$result_skud_tbl[$j][$key]."</td>";
			}
			$body.= "<td>".round($sr_ar/12,2)."</td></tr>";
		}
	break;
	}
	$body.="</table>";
}
?>