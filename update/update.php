<?php
function get_by_id($id,$db) {
    $query="SELECT
            tURVData.IN_WORK_DATE,
			Workers.F_Worker,
			Workers.I_Worker,
			Posts.N_Post,
			Otdels.NB_Otdel,
			tURVData.DAY_START,
			tURVData.DAY_END,
			tURVData.IN_WORK_TIME_MINUTES,
			tURVData.MORNING_TIME_MINUTES,
			tURVData.id
		FROM         tURVData INNER JOIN
                      Workers ON tURVData.ID_Worker = Workers.ID_Worker INNER JOIN
                      Otdels ON Workers.ID_Otdel = Otdels.ID_Otdel INNER JOIN
                      Posts ON Workers.ID_Post = Posts.ID_Post
		WHERE     (tURVData.id = ".$id.")";
    $res=mssql_query($query,$db);
    $r=mssql_fetch_row($res);
    $tor="<div id=\"wait\" name='wait'>
                <form action='index.php' method='post' id='change' name='change'>
            	<input type='hidden' value='2' name='p' id='p'/>
            	<input type='hidden' value='2' name='flag' id='flag'/>
            	<input type='hidden' value='".$r[9]."' name='id_new' id='id_new'/>
            	<input type='hidden' value='".date('d',strtotime($r[0]))."' name='d' id='d'/>
            	<input type='hidden' value='".date('m',strtotime($r[0]))."' name='m' id='m'/>
            	<input type='hidden' value='".date('Y',strtotime($r[0]))."' name='y' id='y'/>
                <table style='border:1px solid gray;z-index:1000;margin:10px 0px 10px 10px;width:97%;height:100%;'>
                    <tr class='tab_bg_2'><td>Дата</td>
                        <td class='tab_bg_2'>
				        <input type='date' name='date_new' style='width:90%;height:90%;' value='".date('Y-m-d',strtotime($r[0]))."' />
		            </td></tr>
		            <tr class='tab_bg_2'><td>ФИО</td>
		                <td class='tab_bg_2'>
		                <input type='text' style='width:90%;height:90%;' value='".$r[1]." ".$r[2]."' disabled>
		            </td></tr>
		            <tr class='tab_bg_2'><td>Должность</td>
		                <td class='tab_bg_2'>
		                <input type='text' style='width:90%;height:90%;' value='".$r[3]."' disabled>
		            </td></tr>
		            <tr class='tab_bg_2'><td>Отдел</td>
		                <td class='tab_bg_2'>
		                <input type='text' style='width:90%;height:90%;' value='".$r[4]."' disabled>
		            </td></tr>
		            <tr class='tab_bg_2'><td>Первый вход</td>
		                <td class='tab_bg_2'>
		                <input type='time' name='in_new' style='width:90%;height:90%;' value='".date('H:i',strtotime($r[5]))."' />
		            </td></tr>
		            <tr class='tab_bg_2'><td>Последний выход</td>
		                <td class='tab_bg_2'>
		                <input type='time' name='out_new' style='width:90%;height:90%;' value='".date('H:i',strtotime($r[6]))."' />
		            </td></tr>
		            <tr class='tab_bg_2'><td>Находился в здании</td>
		                <td class='tab_bg_2'>
		                <input type='text' name='in_time_new' style='width:90%;height:90%;' value='".$r[7]."' />
		            </td></tr>
		            <tr class='tab_bg_2'><td>Утренняя переработка</td>
		                <td class='tab_bg_2'>
		                <input type='text' name='b_time_new' style='width:90%;height:90%;' value='".$r[8]."' />
		            </td></tr>
	            </table>
	            <input type='button' value='Применить' onclick='document.getElementById(\"flag\").value=2;document.change.submit();' style='width:150px;height:90%;'></br></br>
	            <input style='width:150px;height:90%;' type='button' value='Удалить' onclick='document.getElementById(\"flag\").value=3;document.change.submit();'>
	            <input style='width:150px;height:90%;' type='button' value='Закрыть' onclick='document.getElementById(\"wait\").style.visibility=\"hidden\";document.getElementById(\"wait_block\").style.visibility=\"hidden\";'>
	            </form>
        </div>";
    return $tor;}

function update_by_id($id,$in_new,$out_new,$in_time_new,$b_time_new,$db) {
    $query="UPDATE tURVData SET DAY_START = CONVERT(DATETIME, '".$in_new."', 102),  DAY_END = CONVERT(DATETIME, '".$out_new."', 102), IN_WORK_TIME_MINUTES = ".$in_time_new.", MORNING_TIME_MINUTES = ".$b_time_new." WHERE (tURVData.id = ".$id.")";
    mssql_query($query,$db);
}
foreach ($_REQUEST as $k => $v) $$k=$v;
$d_sel="";$m_sel="";$y_sel="";
for ($i=1;$i<32;$i++){
	if ($i != $d) $d_sel.="<option value='".$i."'>".$i."</option>";
    else $d_sel.="<option selected value='".$i."'>".$i."</option>";}
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
	12 => "<option value='12'>Декабрь</option>",
    21 => "<option selected value='01'>Январь</option>",
    22 => "<option selected value='02'>Февраль</option>",
    23 => "<option selected value='03'>Март</option>",
    24 => "<option selected value='04'>Апрель</option>",
    25 => "<option selected value='05'>Май</option>",
    26 => "<option selected value='06'>Июнь</option>",
    27 => "<option selected value='07'>Июль</option>",
    28 => "<option selected value='08'>Август</option>",
    29 => "<option selected value='09'>Сентябрь</option>",
    30 => "<option selected value='10'>Октябрь</option>",
    31 => "<option selected value='11'>Ноябрь</option>",
    32 => "<option selected value='12'>Декабрь</option>",);
for ($i=1;$i<13;$i++){
	if ($i != $m) $m_sel.=$month[$i];
    else $m_sel.=$month[$i+20];}
$year=array( 0 => "<option value='2010'>2010</option>",
	1 => "<option value='2011'>2011</option>",
	2 => "<option value='2012'>2012</option>",
	3 => "<option value='2013'>2013</option>",
	4 => "<option value='2014'>2014</option>",
	5 => "<option value='2015'>2015</option>",
    20 => "<option selected value='2010'>2010</option>",
    21 => "<option selected value='2011'>2011</option>",
    22 => "<option selected value='2012'>2012</option>",
    23 => "<option selected value='2013'>2013</option>",
    24 => "<option selected value='2014'>2014</option>",
    25 => "<option selected value='2015'>2015</option>");
for ($i=0;$i<6;$i++){
	if (($i+2010) != $y) $y_sel.=$year[$i];
    else $y_sel.=$year[$i+20];}

//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//$fio_sel="<option value='%'>*</option>";$dolz_sel="";$otdel_sel="";$in_work_sel="";$out_work_sel="";$in_work_time_sel="";$morning_time_sel="<option selected value='0'>0</option>";
/*
if (isset($_REQUEST['id']) && $_REQUEST['fio'] != "%") $id=$_REQUEST['id']; else $id="";
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
*/
if ($upd == 0 && $fio != "%"){
	$query = "SELECT	tURVData.IN_WORK_DATE, 
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
	FROM         tURVData INNER JOIN
                      Workers ON tURVData.ID_Worker = Workers.ID_Worker INNER JOIN
                      Otdels ON Workers.ID_Otdel = Otdels.ID_Otdel INNER JOIN
                      Posts ON Workers.ID_Post = Posts.ID_Post
	WHERE     (tURVData.IN_WORK_DATE = CONVERT(DATETIME, '".$y."-".$m."-".$d." 00:00:00', 102))
	ORDER BY Workers.F_Worker, Workers.I_Worker";
}
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
			Otdels.NB_Otdel,
			tURVData.DAY_START, 
			tURVData.DAY_END, 
			tURVData.IN_WORK_TIME_MINUTES, 
			tURVData.MORNING_TIME_MINUTES,
			Workers.N_worker,
			Workers.P_Worker,
			Workers.ID_Worker,
			tURVData.id
		FROM         tURVData INNER JOIN
                      Workers ON tURVData.ID_Worker = Workers.ID_Worker INNER JOIN
                      Otdels ON Workers.ID_Otdel = Otdels.ID_Otdel INNER JOIN
                      Posts ON Workers.ID_Post = Posts.ID_Post
		WHERE     (tURVData.IN_WORK_DATE = CONVERT(DATETIME, '".$y."-".$m."-".$d." 00:00:00', 102))
			AND (tURVData.ID_Worker = ".$id.")
		ORDER BY Workers.F_Worker, Workers.I_Worker";
}
else {
    $query = "SELECT	tURVData.IN_WORK_DATE,
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
	FROM         tURVData INNER JOIN
                      Workers ON tURVData.ID_Worker = Workers.ID_Worker INNER JOIN
                      Otdels ON Workers.ID_Otdel = Otdels.ID_Otdel INNER JOIN
                      Posts ON Workers.ID_Post = Posts.ID_Post
	WHERE     (tURVData.IN_WORK_DATE = CONVERT(DATETIME, '".$y."-".$m."-".$d." 00:00:00', 102))
	ORDER BY Workers.F_Worker, Workers.I_Worker";
}

$body="<table class='tab_cadrehov'>
	<form action='index.php' method='post' id='filt'>
	<tr class='tab_bg_2'>
		<th>
			<select name='d' onchange=\"document.getElementById('filt').submit();\">".$d_sel."</select>
			<select name='m' onchange=\"document.getElementById('filt').submit();\">".$m_sel."</select>
			<select name='y' onchange=\"document.getElementById('filt').submit();\">".$y_sel."</select>
        </th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th>
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

if (isset($_REQUEST['l_id_del']) && strlen($_REQUEST['l_id_del'])>1) $base=get_by_id($_REQUEST['l_id_del'],$db);
elseif ($_REQUEST['flag'] == 2) update_by_id($_REQUEST['id_new'],date("Y-m-d H:i:s",strtotime($_REQUEST['in_new'])),date("Y-m-d H:i:s",strtotime($_REQUEST['out_new'])),$_REQUEST['in_time_new'],$_REQUEST['b_time_new'],$db);
//var_dump($query);
$res=mssql_query($query);
$body.="<form action='index.php' method='post' name='get' id='get'>
            <input type='hidden' value='2' name='p' id='p'/>
            <input type='hidden' name='l_id_del' id='l_id_del' value='1'>
            <input type='hidden' value='".$d."' name='d' id='d'/>
            <input type='hidden' value='".$m."' name='m' id='m'/>
            <input type='hidden' value='".$y."' name='y' id='y'/>";
while($row=mssql_fetch_row($res)) {
	$body.= "<tr style='cursor:pointer;' class='tab_bg_1' onclick='document.getElementById(\"l_id_del\").value=\"".$row[12]."\";document.get.submit();'>
		<td>".date('d.m.Y',strtotime($row[0]))."</td>
		<td>".$row[1]." ".$row[2]."</td>
		<td>".$row[3]."</td>
		<td>".$row[4]."</td>
		<td><abbr title='".date('d.m.Y H:i:s',strtotime($row[5]))."'>".date('H:i:s',strtotime($row[5]))."</abbr></td>
		<td><abbr title='".date('d.m.Y H:i:s',strtotime($row[6]))."'>".date('H:i:s',strtotime($row[6]))."</abbr></td>
		<td>".$row[7]."</td>
		<td>".$row[8]."</td>";
}
$body.="</form></table>";
$body=$body.$base;
?>