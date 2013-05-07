<?php

foreach ($_REQUEST as $k => $v) $$k=$v;

$query = "SELECT
            tURVData.IN_WORK_DATE,
			Workers.F_Worker, 
			Workers.I_Worker, 
			Posts.N_Post, 
			Otdels.NB_Otdel,
			tURVData.DAY_START, 
			tURVData.DAY_END, 
			tURVData.IN_WORK_TIME_MINUTES, 
			tURVData.MORNING_TIME_MINUTES,
			Workers.N_worker,
			Workers.P_Worker,
			Workers.ID_Worker,
			tURVData.id
	FROM    tURVData INNER JOIN Workers ON tURVData.ID_Worker = Workers.ID_Worker INNER JOIN Otdels ON Workers.ID_Otdel = Otdels.ID_Otdel INNER JOIN Posts ON Workers.ID_Post = Posts.ID_Post
	WHERE   (tURVData.IN_WORK_DATE = CONVERT(DATETIME, '".$Ymd." 00:00:00', 102))
	ORDER BY Workers.F_Worker, Workers.I_Worker";

$header.="</td></tr><tr><td class='tab_bg_2'><form action='index.php' method='post' id='filt' name='filt'>
			<input type='date' id='Ymd' name='Ymd' style='height:14px;' value='".(isset($Ymd)?$Ymd:date('Y-m-d'))."' onchange='filt.submit();' />
			<input type='hidden' name='id' value='%id%' />
			<input type='hidden' name='p' value='2' />
			</form>";

$body="<table class='tab_cadrehov'>
	<tr class='tab_bg_2'>
		<th><a href='#'>Дата</a><br></th>
		<th><a href='#'>ФИО</a></th>
		<th><a href='#'>Должность</a></th>
		<th><a href='#'>Отдел</a></th>
		<th><a href='#'>Первый вход</a></th>
		<th><a href='#'>Последний выход</a></th>
		<th><a href='#'>Находился в здании<br>минут</a></th>
		<th><a href='#'>Утренняя переработка<br>минут</a></th>
	</tr>";

$res=mssql_query($query);
while($r=mssql_fetch_row($res)) {
	$body.= "<tr style='cursor:pointer;' class='tab_bg_1'>
		<td onclick='usrTIMEjs(".$r[12].",5);'>".date('d.m.Y',strtotime($r[0]))."</td>
		<td><img src='../pics/aide.png' onclick='usrTIMEjs(".$r[11].",1,\"".$domain."\");wait_user.style.visibility=\"visible\";'> ".$r[1]." ".$r[2]."</td>
		<td onclick='usrTIMEjs(".$r[12].",5);'>".$r[3]."</td>
		<td onclick='usrTIMEjs(".$r[12].",5);'>".$r[4]."</td>
		<td onclick='usrTIMEjs(".$r[12].",5);'><abbr title='".date('d.m.Y H:i:s',strtotime($r[5]))."'>".date('H:i:s',strtotime($r[5]))."</abbr></td>
		<td onclick='usrTIMEjs(".$r[12].",5);'><abbr title='".date('d.m.Y H:i:s',strtotime($r[6]))."'>".date('H:i:s',strtotime($r[6]))."</abbr></td>
		<td onclick='usrTIMEjs(".$r[12].",5);'>".$r[7]."</td>
		<td onclick='usrTIMEjs(".$r[12].",5);'>".$r[8]."</td>";
}
$body.="</form></table>";
$body=$body.$base;
?>