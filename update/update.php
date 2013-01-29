<?php
$d_sel="";
$m_sel="";
$y_sel="";

for ($i=1;$i<32;$i++){
	if ($i != $d) $d_sel.="<option value='".$i."'>".$i."</option>";
		else $d_sel.="<option selected value='".$i."'>".$i."</option>";
}

$month=array( 0 => 0,
	1 => "<option value='01'>Январь</option>",
	2 => "<option value='02'>Февраль</option>",
	3 => "<option value='03'>Март</option>",
	4 => "<option value='04'>Апрель</option>",
	5 => "<option value='05'>Май</option>",
	6 => "<option value='06'>Июнь</option>",
	7 => "<option value='07'>Июль</option>",
	8 => "<option value='08'>Август</option>",
	9 => "<option value='09'>Сентябрь</option>",
	10 => "<option value='10'>Октябрь</option>",
	11 => "<option value='11'>Ноябрь</option>",
	12 => "<option value='12'>Декабрь</option>");

switch ($m) {
	case "1": $m_sel="<option selected value='01'>Январь</option>"; break;
	case "2": $m_sel="<option selected value='02'>Февраль</option>"; break;
	case "3": $m_sel="<option selected value='03'>Март</option>"; break;
	case "4": $m_sel="<option selected value='04'>Апрель</option>"; break;
	case "5": $m_sel="<option selected value='05'>Май</option>"; break;
	case "6": $m_sel="<option selected value='06'>Июнь</option>"; break;
	case "7": $m_sel="<option selected value='07'>Июль</option>"; break;
	case "8": $m_sel="<option selected value='08'>Август</option>"; break;
	case "9": $m_sel="<option selected value='09'>Сентябрь</option>"; break;
	case "10": $m_sel="<option selected value='10'>Октябрь</option>"; break;
	case "11": $m_sel="<option selected value='11'>Ноябрь</option>"; break;
	case "12": $m_sel="<option selected value='12'>Декабрь</option>"; break;
}

for ($i=1;$i<13;$i++){
	if ($i != $m) $m_sel.=$month[$i];
}

$year=array( 0 => "<option value='2010'>2009</option>",
	1 => "<option value='2010'>2010</option>",
	2 => "<option value='2011'>2011</option>",
	3 => "<option value='2012'>2012</option>",
	4 => "<option value='2013'>2013</option>",
	5 => "<option value='2014'>2014</option>");

switch ($y) {
	case "2009": $y_sel="<option selected value='2009'>2009</option>"; break;
	case "2010": $y_sel="<option selected value='2010'>2010</option>"; break;
	case "2011": $y_sel="<option selected value='2011'>2011</option>"; break;
	case "2012": $y_sel="<option selected value='2012'>2012</option>"; break;
	case "2013": $y_sel="<option selected value='2013'>2013</option>"; break;
	case "2014": $y_sel="<option selected value='2014'>2014</option>"; break;
}

for ($i=0;$i<6;$i++){
	if (($i+2009) != $y) $y_sel.=$year[$i];
}

//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
$fio_sel="<option value='%'>*</option>";
$dolz_sel="";
$otdel_sel="";
$in_work_sel="";
$out_work_sel="";
$in_work_time_sel="";
$morning_time_sel="<option selected value='0'>0</option>";
if (isset($_REQUEST['id']) && $_REQUEST['fio'] != "%") $id=$_REQUEST['id'];
	else $id="";
if ($id == "" || $id == "%id%") { 
	$fio_sel="<option selected value='%'>*</option>";
	$id = "";
}
if (isset($_REQUEST['upd'])) $upd=$_REQUEST['upd'];
	else $upd=0;
if (isset($_REQUEST['fio'])) {
	$fio=$_REQUEST['fio'];
}
	else {
	$fio_sel="<option selected value='%'>*</option>";
	$fio="";
}
if (isset($_REQUEST['dolz'])) $dolz=$_REQUEST['dolz'];
	else $dolz="";
if (isset($_REQUEST['otdel'])) $otdel=$_REQUEST['otdel'];
	else $otdel="";
if (isset($_REQUEST['in_work'])) $in_work=$_REQUEST['in_work'];
	else $in_work="";
if (isset($_REQUEST['out_work'])) $out_work=$_REQUEST['out_work'];
	else $out_work="";
if (isset($_REQUEST['in_work_time'])) $in_work_time=$_REQUEST['in_work_time'];
	else $in_work_time="";
if (isset($_REQUEST['morning_time'])) $morning_time=$_REQUEST['morning_time'];
	else $morning_time="";

if ($upd == 0 && $fio != "%") 
	$query = "SELECT	tURVData.IN_WORK_DATE, 
			Workers.F_Worker, 
			Workers.I_Worker, 
			Posts.N_Post, 
			Otdels.Name_Otdel, 
			tURVData.DAY_START, 
			tURVData.DAY_END, 
			tURVData.IN_WORK_TIME_MINUTES, 
			tURVData.MORNING_TIME_MINUTES,
			Workers.N_worker,
			Workers.P_Worker,
			Workers.ID_Worker
	FROM         tURVData INNER JOIN
                      Workers ON tURVData.ID_Worker = Workers.ID_Worker INNER JOIN
                      Otdels ON Workers.ID_Otdel = Otdels.ID_Otdel INNER JOIN
                      Posts ON Workers.ID_Post = Posts.ID_Post
	WHERE     (tURVData.IN_WORK_DATE = CONVERT(DATETIME, '".$y."-".$m."-".$d." 00:00:00', 102))
		AND (tURVData.ID_Worker = ".$fio.")
	ORDER BY Workers.F_Worker, Workers.I_Worker";

elseif ($upd == 1 && $id != "") {
	$in_work=date("Y-m-d H:i:s",strtotime($_REQUEST['in_work']));
	$out_work=date("Y-m-d H:i:s",strtotime($_REQUEST['out_work']));
	$in_work_time=((strtotime($out_work) - strtotime($in_work)) / 60) - 60;
	$morning_time="0";

	$query = "

		UPDATE    tURVData
		SET     DAY_START = CONVERT(DATETIME, '".$in_work."', 102), 
			DAY_END = CONVERT(DATETIME, '".$out_work."', 102), 
			IN_WORK_TIME_MINUTES = ".$in_work_time.", 
			MORNING_TIME_MINUTES = ".$morning_time."
		FROM         tURVData INNER JOIN
                      Workers ON tURVData.ID_Worker = Workers.ID_Worker INNER JOIN
                      Otdels ON Workers.ID_Otdel = Otdels.ID_Otdel INNER JOIN
                      Posts ON Workers.ID_Post = Posts.ID_Post
		WHERE     (tURVData.IN_WORK_DATE = CONVERT(DATETIME, '".$y."-".$m."-".$d." 00:00:00', 102)) 
			AND (tURVData.ID_Worker = ".$id.");
		SELECT	tURVData.IN_WORK_DATE, 
			Workers.F_Worker, 
			Workers.I_Worker, 
			Posts.N_Post, 
			Otdels.Name_Otdel, 
			tURVData.DAY_START, 
			tURVData.DAY_END, 
			tURVData.IN_WORK_TIME_MINUTES, 
			tURVData.MORNING_TIME_MINUTES,
			Workers.N_worker,
			Workers.P_Worker,
			Workers.ID_Worker
		FROM         tURVData INNER JOIN
                      Workers ON tURVData.ID_Worker = Workers.ID_Worker INNER JOIN
                      Otdels ON Workers.ID_Otdel = Otdels.ID_Otdel INNER JOIN
                      Posts ON Workers.ID_Post = Posts.ID_Post
		WHERE     (tURVData.IN_WORK_DATE = CONVERT(DATETIME, '".$y."-".$m."-".$d." 00:00:00', 102))
			AND (tURVData.ID_Worker = ".$id.")
		ORDER BY Workers.F_Worker, Workers.I_Worker";
}
else 	$query = "SELECT	tURVData.IN_WORK_DATE, 
			Workers.F_Worker, 
			Workers.I_Worker, 
			Posts.N_Post, 
			Otdels.Name_Otdel, 
			tURVData.DAY_START, 
			tURVData.DAY_END, 
			tURVData.IN_WORK_TIME_MINUTES, 
			tURVData.MORNING_TIME_MINUTES,
			Workers.N_worker,
			Workers.P_Worker,
			Workers.ID_Worker
	FROM         tURVData INNER JOIN
                      Workers ON tURVData.ID_Worker = Workers.ID_Worker INNER JOIN
                      Otdels ON Workers.ID_Otdel = Otdels.ID_Otdel INNER JOIN
                      Posts ON Workers.ID_Post = Posts.ID_Post
	WHERE     (tURVData.IN_WORK_DATE = CONVERT(DATETIME, '".$y."-".$m."-".$d." 00:00:00', 102))
	ORDER BY Workers.F_Worker, Workers.I_Worker";

$body="<table class='tab_cadrehov'>
	<form action='index.php' method='post' id='filt'>
	<tr class='tab_bg_2'>
		<th>
				<select name='d' onchange=\"document.getElementById('filt').submit();\">
					".$d_sel."
				</select>
				<select name='m' onchange=\"document.getElementById('filt').submit();\">
					".$m_sel."
				</select>
				<select name='y' onchange=\"document.getElementById('filt').submit();\">
					".$y_sel."
				</select>

		</th>
		<th>
				<select name='fio' onchange=\"document.getElementById('filt').submit();\">
					%fio_sel%
				</select>

		</th>
		<th>
				<select disabled='disabled' name='dolz'>
					%dolz_sel%
				</select>
		</th>
		<th>
				<select disabled='disabled' name='otdel'>
					%otdel_sel%
				</select>
		</th>
		<th>
				<select name='in_work'>
					%in_work_sel%
				</select>
		</th>
		<th>
				<select name='out_work'>
					%out_work_sel%
				</select>
		</th>
		<th>
				<select disabled='disabled' name='in_work_time'>
					%in_work_time_sel%
				</select>
		</th>
		<th>
				<select disabled='disabled' name='morning_time'>
					%morning_time_sel%
				</select> 
				<input type='submit' value='±' />
				<input type='hidden' name='id' value='%id%' />
				<input type='hidden' name='p' value='2' />
				<input type='checkbox' name='upd' value='1' />
		</th>
	</tr>
	</form>	
	<tr class='tab_bg_2'>
		<th><a href='#'>Дата</a></th>
		<th><a href='#'>ФИО</a></th>
		<th><a href='#'>Должность</a></th>
		<th><a href='#'>Отдел</a></th>
		<th><a href='#'>Первый вход</a></th>
		<th><a href='#'>Последний выход</a></th>
		<th><a href='#'>Находился в здании<br>минут</a></th>
		<th><a href='#'>Утренняя переработка<br>минут</a></th>
	</tr>
	</form>";

$in_work_sel="	<option value='".$d."-".$m."-".$y." 7:00:00'>07:00</option>
		<option value='".$d."-".$m."-".$y." 7:10:00'>07:10</option>
		<option value='".$d."-".$m."-".$y." 7:20:00'>07:20</option>
		<option value='".$d."-".$m."-".$y." 7:30:00'>07:30</option>
		<option value='".$d."-".$m."-".$y." 7:40:00'>07:40</option>
		<option value='".$d."-".$m."-".$y." 7:50:00'>07:50</option>
		<option value='".$d."-".$m."-".$y." 8:00:00'>08:00</option>
		<option value='".$d."-".$m."-".$y." 8:10:00'>08:10</option>
		<option value='".$d."-".$m."-".$y." 8:20:00'>08:20</option>
		<option value='".$d."-".$m."-".$y." 8:30:00'>08:30</option>
		<option value='".$d."-".$m."-".$y." 8:40:00'>08:40</option>
		<option value='".$d."-".$m."-".$y." 8:50:00'>08:50</option>
		<option value='".$d."-".$m."-".$y." 9:00:00'>09:00</option>
		<option value='".$d."-".$m."-".$y." 9:10:00'>09:10</option>
		<option value='".$d."-".$m."-".$y." 9:20:00'>09:20</option>
		<option value='".$d."-".$m."-".$y." 9:30:00'>09:30</option>
		<option value='".$d."-".$m."-".$y." 9:40:00'>09:40</option>
		<option value='".$d."-".$m."-".$y." 9:50:00'>09:50</option>
		<option value='".$d."-".$m."-".$y." 10:00:00'>10:00</option>
		<option value='".$d."-".$m."-".$y." 10:10:00'>10:10</option>
		<option value='".$d."-".$m."-".$y." 10:20:00'>10:20</option>
		<option value='".$d."-".$m."-".$y." 10:30:00'>10:30</option>
		<option value='".$d."-".$m."-".$y." 10:40:00'>10:40</option>
		<option value='".$d."-".$m."-".$y." 10:50:00'>10:50</option>
		<option value='".$d."-".$m."-".$y." 11:00:00'>11:00</option>";

$out_work_sel="	<option value='".$d."-".$m."-".$y." 15:00:00'>15:00</option>
		<option value='".$d."-".$m."-".$y." 15:10:00'>15:10</option>
		<option value='".$d."-".$m."-".$y." 15:20:00'>15:20</option>
		<option value='".$d."-".$m."-".$y." 15:30:00'>15:30</option>
		<option value='".$d."-".$m."-".$y." 15:40:00'>15:40</option>
		<option value='".$d."-".$m."-".$y." 15:50:00'>15:50</option>
		<option value='".$d."-".$m."-".$y." 16:00:00'>16:00</option>
		<option value='".$d."-".$m."-".$y." 16:10:00'>16:10</option>
		<option value='".$d."-".$m."-".$y." 16:20:00'>16:20</option>
		<option value='".$d."-".$m."-".$y." 16:30:00'>16:30</option>
		<option value='".$d."-".$m."-".$y." 16:40:00'>16:40</option>
		<option value='".$d."-".$m."-".$y." 16:50:00'>16:50</option>
		<option value='".$d."-".$m."-".$y." 17:00:00'>17:00</option>
		<option value='".$d."-".$m."-".$y." 17:15:00'>17:15</option>
		<option value='".$d."-".$m."-".$y." 17:20:00'>17:20</option>
		<option value='".$d."-".$m."-".$y." 17:30:00'>17:30</option>
		<option value='".$d."-".$m."-".$y." 17:40:00'>17:40</option>
		<option value='".$d."-".$m."-".$y." 17:50:00'>17:50</option>
		<option value='".$d."-".$m."-".$y." 18:00:00'>18:00</option>
		<option value='".$d."-".$m."-".$y." 18:10:00'>18:10</option>
		<option value='".$d."-".$m."-".$y." 18:15:00'>18:15</option>
		<option value='".$d."-".$m."-".$y." 18:20:00'>18:20</option>
		<option value='".$d."-".$m."-".$y." 18:30:00'>18:30</option>
		<option value='".$d."-".$m."-".$y." 18:40:00'>18:40</option>
		<option value='".$d."-".$m."-".$y." 18:50:00'>18:50</option>
		<option value='".$d."-".$m."-".$y." 19:00:00'>19:00</option>
		<option value='".$d."-".$m."-".$y." 19:10:00'>19:10</option>
		<option value='".$d."-".$m."-".$y." 19:20:00'>19:20</option>
		<option value='".$d."-".$m."-".$y." 19:30:00'>19:30</option>
		<option value='".$d."-".$m."-".$y." 19:40:00'>19:40</option>
		<option value='".$d."-".$m."-".$y." 19:50:00'>19:50</option>
		<option value='".$d."-".$m."-".$y." 20:00:00'>20:00</option>";

$res=mssql_query($query);
while($row=mssql_fetch_row($res)) {
	$fio_tmp = $row[1] . " " . $row[9] . " " . $row[10];
	if ($fio != $row[11]) $fio_sel.="<option value='".$row[11]."'>".$fio_tmp."</option>";
		else {
			$fio_sel.="<option selected value='".$row[11]."'>".$fio_tmp."</option>";
			$body = str_ireplace("%id%",$row[11],$body);
			$dolz_sel.="<option selected>".$row[3]."</option>";
			$otdel_sel.="<option selected>".$row[4]."</option>";
			$in_work_sel.="<option selected value='".$row[5]."'>".date('H:i',strtotime($row[5]))."</option>";
			$out_work_sel.="<option selected value='".$row[6]."'>".date('H:i',strtotime($row[6]))."</option>";
		}
	if ($dolz != $row[3]) $dolz_sel.="<option>".$row[3]."</option>";
		else $dolz_sel.="<option selected>".$row[3]."</option>";
	if ($otdel != $row[4]) $otdel_sel.="<option>".$row[4]."</option>";
		else $otdel_sel.="<option selected>".$row[4]."</option>";
	
	$body.= "<tr class='tab_bg_1'>
		<td>".date('d.m.Y',strtotime($row[0]))."</td>
		<td>".$row[1]." ".$row[2]."</td>
		<td>".$row[3]."</td>
		<td>".$row[4]."</td>
		<td>".date('d.m.Y H:i:s',strtotime($row[5]))."</td>
		<td>".date('d.m.Y H:i:s',strtotime($row[6]))."</td>
		<td>".$row[7]."</td>
		<td>".$row[8]."</td>";
}
$body.="</table>";
?>