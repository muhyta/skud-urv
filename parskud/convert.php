<?php

$body="<table class='tab_cadrehov'>
	<tr class='tab_bg_2'>
		<th>Дата</th>
		<th>ФИО</th>
		<th>Должность</th>
		<th>Отдел</th>
		<th>Первый<br>вход</th>
		<th>Последний<br>выход</th>
		<th>Находился в здании<br>минут</th>
		<th>Утренняя переработка<br>минут</th>
		<th>Статус<br>выполнения</th>
	</tr>";

function trIt($str) 
{
    $tr = array(
        "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
        "Д"=>"D","Е"=>"E","Ё"=>"Yo","Ж"=>"J","З"=>"Z","И"=>"I",
        "Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
        "О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
        "У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"C","Ч"=>"Ch",
        "Ш"=>"Sh","Щ"=>"Sch","Ъ"=>"","Ы"=>"Y","Ь"=>"",
        "Э"=>"E","Ю"=>"Yu","Я"=>"Ya","а"=>"a","б"=>"b",
        "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ё"=>"yo","ж"=>"j",
        "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
        "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
        "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
        "ц"=>"c","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
        "ы"=>"y","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya"
    );
    return strtr($str,$tr);}

function convert_AD_date ($ad_date) {
	if ($ad_date == 0) {
		return '0000-00-00';
	}
	$secsAfterADEpoch = $ad_date / (10000000);
	$AD2Unix=((1970-1601) * 365 - 3 + round((1970-1601)/4) ) * 86400;
	$unixTimeStamp=intval($secsAfterADEpoch-$AD2Unix);
	$myDate = date("Y-m-d H:i:s", $unixTimeStamp); // formatted date
	return $myDate;
}

include_once('PHPExcel.php');
set_include_path(get_include_path() . PATH_SEPARATOR . '.');
include 'PHPExcel/IOFactory.php';
if (isset($_FILES['f']) and $_FILES['f']['error'] == 0) {
if (isset($_REQUEST['p'])) { //---подбробно
$inputFileName=$_FILES['f']['tmp_name'];
if (isset($_REQUEST['i'])) $insrt=$_REQUEST['i'];
$inputFileType = 'Excel5';
class MyReadFilter implements PHPExcel_Reader_IReadFilter
{
	public function readCell($column, $row, $worksheetName = '') {
		if ($row > 4) {
			if (in_array($column,range('A','Q'))) {
				return true;}}
		return false;}}
$filterSubset = new MyReadFilter();
pathinfo($inputFileName,PATHINFO_BASENAME);
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
$html = str_ireplace("%filename%","Loading ".$inputFileName,$html);
$objReader->setReadFilter($filterSubset);
$objPHPExcel = $objReader->load($inputFileName);
$sd = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
$day_start = array();
$day_end = array();
$in_work_time_minutes = array();
$morning_time_minutes = array();
$in_work_date = array();
$lta = array();
$flag = array();
$otdel = array();
$post = array();

for ($i=5; $i <= sizeof($sd); $i++) {
	$firma=iconv('UTF-8','CP1251',$sd[$i]['E']);	// firma
	$fio=iconv('UTF-8','CP1251',$sd[$i]['A']);	// fio
	$otdel[$fio]=iconv('UTF-8','CP1251',$sd[$i]['D']);	// otdel
	if ( ( substr_count($firma,iconv('UTF-8','CP1251',$footer)) > 0 || substr_count($firma,"Временный пропуск") > 0 ) && substr_count($otdel[$fio],"Рабоч") == 0 ) {
		$post[$fio]=iconv('UTF-8','CP1251',$sd[$i]['B']);	// post
		$date_time=iconv('UTF-8','CP1251',$sd[$i]['F']);	// date_time
		$action=iconv('UTF-8','CP1251',$sd[$i]['I']);	// in_out action where
		if ( !isset($in_work_date[$fio]) ) $in_work_date[$fio] = date('d.m.Y',strtotime($date_time))." 00:00:00";//date('d.m.Y',strtotime(substr($date_time,6,2)."-".substr($date_time,0,2)."-".substr($date_time,3,2)))." 00:00:00";
		if ( (strtotime($date_time) - strtotime($in_work_date[$fio])) >= 46800 && (strtotime($date_time) - strtotime($in_work_date[$fio])) <= 50400 && !isset($flag[$fio]) ) {
			$day_end[$fio] = date('d.m.Y',strtotime($date_time)). " 13:00:00";//date('d.m.Y',strtotime(substr($date_time,6,2)."-".substr($date_time,0,2)."-".substr($date_time,3,2)))." 13:00:00";
			$in_work_time_minutes[$fio] = $in_work_time_minutes[$fio] + ((strtotime($day_end[$fio]) - strtotime($lta[$fio]))/60);
			$lta[$fio] = date('d.m.Y',strtotime($date_time)). " 14:00:00";//date('d.m.Y',strtotime(substr($date_time,6,2)."-".substr($date_time,0,2)."-".substr($date_time,3,2)))." 14:00:00";
			$flag[$fio]=1;
		}
		if ( substr_count($action,"Вход") > 0 && (substr_count($action,"Турникет") > 0 || substr_count($action,"Парковка") > 0)) {
			if ( !isset($day_start[$fio]) ) {
				$day_start[$fio] = date("d.m.Y H:i:s",strtotime($date_time));
				$in_work_time_minutes[$fio] = 0;
				$lta[$fio] = $day_start[$fio];
				//32400s = 9h:00m
				if ( (strtotime($day_start[$fio]) - strtotime($in_work_date[$fio])) > 32400 ) {
					$morning_time_minutes[$fio] = 0;
				}
				else $morning_time_minutes[$fio] = (32400 - (strtotime($day_start[$fio]) - strtotime($in_work_date[$fio]))) / 60;
			}
			if ( (substr_count($action,"Турникет") > 0 || substr_count($action,"Парковка") > 0) && (strtotime($date_time) - strtotime($in_work_date[$fio])) >= (strtotime($lta[$fio]) - strtotime($in_work_date[$fio])) ) $lta[$fio] = $date_time;
		} 
		if ( substr_count($action,"Выход") > 0 && (substr_count($action,"Турникет") > 0 || substr_count($action,"Парковка") > 0)) {
			if ( (strtotime($date_time) - strtotime($lta[$fio])) > 0 ) $in_work_time_minutes[$fio] = $in_work_time_minutes[$fio] + (strtotime($date_time) - strtotime($lta[$fio]))/60;
			$day_end[$fio] = date("d.m.Y H:i:s",strtotime($date_time));
		}
	}
}
unset($fio);
unset($firma,$date_time,$action);
foreach ($in_work_date as $fio => $in_work) {
		$result = "default";
		if ( !isset($flag[$fio]) && (strtotime($day_start[$fio]) - strtotime($in_work_date[$fio])) < 46800 && (strtotime($day_end[$fio]) - strtotime($in_work_date[$fio])) > 46800) $in_work_time_minutes[$fio] = $in_work_time_minutes[$fio] - 60;
		if ( $in_work_time_minutes[$fio] > 1440 ) {
			$in_work_time_minutes[$fio] = 495;
			$morning_time_minutes[$fio] = 0;
		}
		$id_w=mssql_fetch_row(mssql_query("SELECT	Workers.ID_Worker FROM	Workers WHERE	(Workers.F_Worker + ' ' + Workers.N_Worker + ' ' + Workers.P_Worker LIKE '%".$fio."%')"));
		if (!isset($id_w[0])) {
			$id_o=mssql_fetch_row(mssql_query("SELECT     ID_Otdel FROM         Otdels WHERE     (Name_Otdel = '".$otdel[$fio]."')"));
			if (!isset($id_o[0])) {
				$id_o=mssql_fetch_row(mssql_query("SELECT     ID_Otdel FROM         Otdels WHERE     (Name_Otdel like '%".substr($otdel[$fio],0,strpos($otdel[$fio]," ")-1)."%') AND (Name_Otdel like '%".substr($otdel[$fio],strrpos($otdel[$fio]," "))."%')")); }
			if (isset($id_o[0])) {
				$id_p=mssql_fetch_row(mssql_query("SELECT     ID_Post FROM         Posts WHERE     (N_Post = '".$post[$fio]."')"));
				if (isset($id_p[0])) {
					$query="INSERT INTO Workers (F_Worker, N_Worker, P_Worker, ID_Post, ID_Otdel, Login) VALUES     ('".substr($fio,0,strpos($fio," "))."', '".substr($fio,strpos($fio," ")+1,strrpos($fio," ")-strpos($fio," ")-1)."', '".substr($fio,strrpos($fio," ")+1)."', ".$id_o[0].", ".$id_p[0].", '".trIt( substr($fio,0,strpos($fio," ")) ).trIt( substr($fio,strpos($fio," ")+1,1) ).trIt( substr($fio,strrpos($fio," ")+1,1) )."')";
					if ($insrt == 1) {
						if (mssql_query($query)) $result="added";
						else $result = mssql_get_last_message();
					}
					$body = $body . "<tr class='tab_bg_1'><td>"
							.date('d.m.Y',strtotime($in_work))."</td><td>"
							.$fio."</td><td>"
							.$post[$fio]."</td><td>"
							.$otdel[$fio]."</td><td>Новый</td><td>Новый</td><td>Новый</td><td>"
							.trIt( substr($fio,0,strpos($fio," ")) ).trIt( substr($fio,strpos($fio," ")+1,1) ).trIt( substr($fio,strrpos($fio," ")+1,1) )."</td><td><abbr title=\"".$query."\">"
							.$result."</abbr></td></tr>";
				}
				else $result = "<span style='color:red;'>no post</span>";
			}
			else $result = "<span style='color:red;'>no otdel</span>";
		}
		$query="SELECT	tURVData.ID_Worker
			FROM	tURVData INNER JOIN
				Workers ON tURVData.ID_Worker = Workers.ID_Worker
			WHERE	(tURVData.IN_WORK_DATE = CONVERT(DATETIME, '".date('Y-m-d H:i:s',strtotime($in_work))."', 102)) 
				AND (tURVData.DAY_START <> 0) 
				AND (tURVData.DAY_END <> 0) 
				AND (Workers.F_Worker + ' ' + Workers.N_Worker + ' ' + Workers.P_Worker LIKE '%".$fio."%')";
		$r=mssql_fetch_row(mssql_query($query));
		$result=$query;
		if (isset($r[0])) {
			$r=mssql_fetch_row(mssql_query("SELECT	Workers.ID_Worker FROM	Workers WHERE	(Workers.F_Worker + ' ' + Workers.N_Worker + ' ' + Workers.P_Worker LIKE '%".$fio."%')"));
			if (isset($r[0])) {
				$query="UPDATE	tURVData
					SET	IN_WORK_TIME_MINUTES = ".$in_work_time_minutes[$fio].", 
						MORNING_TIME_MINUTES = ".$morning_time_minutes[$fio].", 
						IN_WORK_DATE = CONVERT(DATETIME, '".date('Y-m-d H:i:s',strtotime($in_work))."', 102), 
						DAY_START = CONVERT(DATETIME, '".date('Y-m-d H:i:s',strtotime($day_start[$fio]))."', 102), 
						DAY_END = CONVERT(DATETIME, '".date('Y-m-d H:i:s',strtotime($day_end[$fio]))."', 102)
					FROM	tURVData INNER JOIN
						Workers ON tURVData.ID_Worker = Workers.ID_Worker
					WHERE	(tURVData.ID_Worker = ".$r[0].") 
						AND (tURVData.IN_WORK_DATE = CONVERT(DATETIME, '".date('Y-m-d H:i:s',strtotime($in_work))."', 102))";
				if ($insrt == 1) { 
					if (mssql_query($query)) $result="updated";
					else $result = mssql_get_last_message();
				}
				else $result="<abbr title=\"".$result.";\n".$query."\">debug</abbr>";
			}
			else $result="<abbr title=\"".$result.";\n".$query."\"><span style='color:red;'>no user</span></abbr>";
		}
		else {
//			$result="debug"; 
			$r=mssql_fetch_row(mssql_query("SELECT	Workers.ID_Worker FROM	Workers WHERE	(Workers.F_Worker + ' ' + Workers.N_Worker + ' ' + Workers.P_Worker LIKE '%".$fio."%')"));
			if (isset($r[0])) {
				$query="INSERT INTO tURVData 
						(ID_Worker, IN_WORK_TIME_MINUTES, MORNING_TIME_MINUTES, IN_WORK_DATE, DAY_START, DAY_END) 
					VALUES 	(".$r[0].", ".$in_work_time_minutes[$fio].", ".$morning_time_minutes[$fio].", CONVERT(DATETIME, '".date('Y-m-d H:i:s',strtotime($in_work))."', 102), CONVERT(DATETIME, '".date('Y-m-d H:i:s',strtotime($day_start[$fio]))."', 102), CONVERT(DATETIME, '".date('Y-m-d H:i:s',strtotime($day_end[$fio]))."', 102))";
				if ($insrt == 1) { 
					if (mssql_query($query)) $result="inserted";
					else $result = mssql_get_last_message();
				}
				else $result = "<abbr title=\"".$result.";\n".$query."\">debug</abbr>";
			}
			else $result="<abbr title=\"".$result.";\n".$query."\"><span style='color:red;'>no user</span></abbr>";
		}
		$body = $body . "<tr class='tab_bg_1'><td>"
			.date('d.m.Y',strtotime($in_work))."</td><td>"
			.$fio."</td><td>"
			.$post[$fio]."</td><td>"
			.$otdel[$fio]."</td><td>"
			.date('H:i',strtotime($day_start[$fio]))."</td><td>"
			.date('H:i',strtotime($day_end[$fio]))."</td><td>"
			.(($in_work_time_minutes[$fio] <= 10 || $in_work_time_minutes[$fio] > 700)?"<span style='color:red;'>".$in_work_time_minutes[$fio]."</span>":$in_work_time_minutes[$fio])."</td><td>"
			.$morning_time_minutes[$fio]."</td><td><abbr title=\"".$query."\">"
			.$result."</abbr></td></tr>";
		}
	}
	else {//---обычно
$inputFileName=$_FILES['f']['tmp_name']; 
if (isset($_REQUEST['i'])) $insrt=$_REQUEST['i'];
$inputFileType = 'Excel5';
class MyReadFilter implements PHPExcel_Reader_IReadFilter
{
	public function readCell($column, $row, $worksheetName = '') {
		if ($row > 4) {
			if (in_array($column,range('A','Q'))) {
				return true;}}
		return false;}}
$filterSubset = new MyReadFilter();
pathinfo($inputFileName,PATHINFO_BASENAME);
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
$html = str_ireplace("%filename%","Loading ".$inputFileName,$html);
$objReader->setReadFilter($filterSubset);
$objPHPExcel = $objReader->load($inputFileName);
$sd = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
for ($i=5; $i <= sizeof($sd); $i++) {
	$sd[$i]['D']=iconv('UTF-8','CP1251',$sd[$i]['D']);
	$sd[$i]['E']=iconv('UTF-8','CP1251',$sd[$i]['E']);
	if (( substr_count($sd[$i]['E'],iconv('UTF-8','CP1251',$footer)) > 0 || substr_count($sd[$i]['E'],"Временный пропуск") > 0 ) && substr_count($sd[$i]['D'],"Рабоч") == 0) { //($sd[$i]['E'] == iconv('UTF-8','CP1251',$footer) or $sd[$i]['E'] == "Временный пропуск") and $sd[$i]['D'] != "Рабочие") {
		$sd[$i]['A']=iconv('UTF-8','CP1251',$sd[$i]['A']);
		$sd[$i]['B']=iconv('UTF-8','CP1251',$sd[$i]['B']);
		$sd[$i]['F']=iconv('UTF-8','CP1251',$sd[$i]['F']); 
		$sd[$i]['G']=iconv('UTF-8','CP1251',$sd[$i]['G']); $sd[$i]['G']=date('d.m.Y',strtotime(substr($sd[$i]['F'],6,2)."-".substr($sd[$i]['F'],0,2)."-".substr($sd[$i]['F'],3,2)))." ".$sd[$i]['G']. ":00";
		$sd[$i]['J']=iconv('UTF-8','CP1251',$sd[$i]['J']); $sd[$i]['J']=date('d.m.Y',strtotime(substr($sd[$i]['F'],6,2)."-".substr($sd[$i]['F'],0,2)."-".substr($sd[$i]['F'],3,2)))." ".$sd[$i]['J']. ":00";
		$sd[$i]['K']=iconv('UTF-8','CP1251',$sd[$i]['K']); //$sd[$i]['K']=date('d.m.Y',strtotime(substr($sd[$i]['F'],6,2)."-".substr($sd[$i]['F'],0,2)."-".substr($sd[$i]['F'],3,2)))." ".$sd[$i]['K']. ":00";
		$sd[$i]['M']=iconv('UTF-8','CP1251',$sd[$i]['M']); $sd[$i]['M']=date('d.m.Y',strtotime(substr($sd[$i]['F'],6,2)."-".substr($sd[$i]['F'],0,2)."-".substr($sd[$i]['F'],3,2)))." ".$sd[$i]['M']. ":00";
		$sd[$i]['N']=iconv('UTF-8','CP1251',$sd[$i]['N']); $sd[$i]['N']=date('d.m.Y',strtotime(substr($sd[$i]['F'],6,2)."-".substr($sd[$i]['F'],0,2)."-".substr($sd[$i]['F'],3,2)))." ".$sd[$i]['N']. ":00";
		$sd[$i]['O']=iconv('UTF-8','CP1251',$sd[$i]['O']); $sd[$i]['O']=date('d.m.Y',strtotime(substr($sd[$i]['F'],6,2)."-".substr($sd[$i]['F'],0,2)."-".substr($sd[$i]['F'],3,2)))." ".$sd[$i]['O']. ":00";
		$sd[$i]['P']=iconv('UTF-8','CP1251',$sd[$i]['P']);
		$sd[$i]['Q']=iconv('UTF-8','CP1251',$sd[$i]['Q']);
		$in_work_date = date('d.m.Y',strtotime(substr($sd[$i]['F'],6,2)."-".substr($sd[$i]['F'],0,2)."-".substr($sd[$i]['F'],3,2)))." 00:00:00";
		$day_start = date('d.m.Y',strtotime($in_work_date))." ".$sd[$i]['P']. ":00";
		$day_end = date('d.m.Y',strtotime($in_work_date))." ".$sd[$i]['Q']. ":00";
		if ((((substr_count($sd[$i]['P'],"Нет") == 0) and (substr_count($sd[$i]['Q'],"Нет") == 0)) or ((substr_count($sd[$i]['P'],"Нет") != 0)  and (substr_count($sd[$i]['Q'],"Нет") == 0)) or ((substr_count($sd[$i]['P'],"Нет") == 0)  and (substr_count($sd[$i]['Q'],"Нет") != 0)))
		and (((substr_count($sd[$i]['P'],"-") == 0)   and (substr_count($sd[$i]['Q'],"-") == 0))   or ((substr_count($sd[$i]['P'],"-") != 0)    and (substr_count($sd[$i]['Q'],"-") == 0))   or ((substr_count($sd[$i]['P'],"-") == 0)    and (substr_count($sd[$i]['Q'],"-") != 0)))) {
			$query = "SELECT Workers.ID_Worker FROM Workers 
					WHERE (Workers.F_Worker LIKE '%".substr($sd[$i]['A'],0,strpos($sd[$i]['A']," "))."%') 
						AND (Workers.N_Worker LIKE '%".substr($sd[$i]['A'],strpos($sd[$i]['A']," ")+1,strrpos($sd[$i]['A']," ")-strpos($sd[$i]['A']," ")-1)."%') 
						AND (Workers.P_Worker LIKE '%".substr($sd[$i]['A'],strrpos($sd[$i]['A']," ")+1)."%');";
			$r=mssql_fetch_row(mssql_query($query));
			if (!$r[0]) {
				$query = "SELECT ID_Post FROM Posts WHERE (N_Post = '".substr($sd[$i]['B'],0)."')";
				$r_dol=mssql_fetch_row(mssql_query($query));
				if ($r_dol[0]!= NULL) {
					$query = "SELECT ID_Otdel FROM Otdels WHERE (Name_Otdel LIKE '".substr($sd[$i]['D'],0)."%')";
					$r_otdel=mssql_fetch_row(mssql_query($query));
					if ($r_otdel[0] != NULL) {
						$query = "INSERT INTO Workers (F_Worker, N_Worker, P_Worker, I_Worker, ID_Post, ID_Otdel, Login) VALUES ('"
							.substr($sd[$i]['A'],0,strpos($sd[$i]['A']," "))."','"
							.substr($sd[$i]['A'],strpos($sd[$i]['A']," ")+1,strrpos($sd[$i]['A']," ")-strpos($sd[$i]['A']," ")-1)."','"
							.substr($sd[$i]['A'],strrpos($sd[$i]['A']," ")+1)."','"
							.substr($sd[$i]['A'],strpos($sd[$i]['A']," ")+1,1).".".substr($sd[$i]['A'],strrpos($sd[$i]['A']," ")+1,1).".','"
							.$r_dol[0]."','"
							.$r_otdel[0]."','"
							.trIt(substr($sd[$i]['A'],0,strpos($sd[$i]['A']," "))).trIt(substr($sd[$i]['A'],strpos($sd[$i]['A']," ")+1,1)).trIt(substr($sd[$i]['A'],strrpos($sd[$i]['A']," ")+1,1))."');";
						$body.= "<tr class='tab_bg_1'><td>"
								.date("d.m.Y",strtotime($in_work_date))."</td><td>"
								.$sd[$i]['A']."</td><td>"
								.$sd[$i]['B']."</td><td>"
								.$sd[$i]['D']."</td><td>Новый</td><td>Новый</td><td>Новый</td><td><span style='color:red;'>Новый</span></td>";
						if ($insrt == 1) {
							if (mssql_query($query)) $body.="<td>added</td></tr>"; else $body.="<td><b><abbr title=\"".mssql_get_last_message()."\"><span style='color:red;'>error!</span></abbr></b></td></tr>";
						} else {$body.="<td><abbr title=\"".$query."\">debug</abbr></td></tr>";} //--- Если пользователь отдел и должность существуют в базах, добавляем нового пользователя
					} //--- Проверяем отдел пользователя на наличие в базе отделов
				} //--- Проверяем должность пользователя на наличие в базе должностей
			} //--- Проверяем, есть ли в пользователь в базе пользователей
			if (substr_count($sd[$i]['P'],"Нет") or substr_count($sd[$i]['P'],"-")) {
				$day_start=date('d.m.Y',strtotime(substr($sd[$i]['F'],6,2)."-".substr($sd[$i]['F'],0,2)."-".substr($sd[$i]['F'],3,2)))." 09:00:00";
				$sd[$i]['P']=date('m.d.Y',strtotime(substr($sd[$i]['F'],6,2)."-".substr($sd[$i]['F'],0,2)."-".substr($sd[$i]['F'],3,2)))." 09:00:00";
				} else $sd[$i]['P']=date('m.d.Y',strtotime(substr($sd[$i]['F'],6,2)."-".substr($sd[$i]['F'],0,2)."-".substr($sd[$i]['F'],3,2)))." ".$sd[$i]['P']. ":00";
			if (substr_count($sd[$i]['Q'],"Нет") or substr_count($sd[$i]['Q'],"-")) {
				$day_end = date('d.m.Y',strtotime(substr($sd[$i]['F'],6,2)."-".substr($sd[$i]['F'],0,2)."-".substr($sd[$i]['F'],3,2)))." 18:15:00";
				$sd[$i]['Q']=date('m.d.Y',strtotime(substr($sd[$i]['F'],6,2)."-".substr($sd[$i]['F'],0,2)."-".substr($sd[$i]['F'],3,2)))." 18:15:00";
				} else $sd[$i]['Q']=date('m.d.Y',strtotime(substr($sd[$i]['F'],6,2)."-".substr($sd[$i]['F'],0,2)."-".substr($sd[$i]['F'],3,2)))." ".$sd[$i]['Q']. ":00";
			$in_work_time_minutes = (strtotime($sd[$i]['G']) - strtotime($in_work_date)) / 60; //- (strtotime($sd[$i]['N']) - strtotime($in_work_date)) - (strtotime($sd[$i]['O']) - strtotime($in_work_date))) / 60;
			$real_in_work = ((strtotime($day_end) - strtotime($in_work_date)) - (strtotime($day_start) - strtotime($in_work_date))) / 60;
			$morning_time_minutes = ((strtotime($sd[$i]['M']) - strtotime($in_work_date))/60);
			if (($in_work_time_minutes > $real_in_work) or ($in_work_time_minutes < 0)) $in_work_time_minutes = $real_in_work;
			if ($in_work_time_minutes < 0) $in_work_time_minutes = 0;
			if ($morning_time_minutes >= $in_work_time_minutes) $morning_time_minutes = 0;
			if (!$r[0]) {
				$query = "SELECT Workers.ID_Worker FROM Workers 
						WHERE (Workers.F_Worker LIKE '%".substr($sd[$i]['A'],0,strpos($sd[$i]['A']," "))."%') 
							AND (Workers.N_Worker LIKE '%".substr($sd[$i]['A'],strpos($sd[$i]['A']," ")+1,strrpos($sd[$i]['A']," ")-strpos($sd[$i]['A']," ")-1)."%') 
							AND (Workers.P_Worker LIKE '%".substr($sd[$i]['A'],strrpos($sd[$i]['A']," ")+1)."%');";
				$r=mssql_fetch_row(mssql_query($query));
			} //--- Проверяем, был ли добавлен пользователь в базу как новый
			if ($r[0]) {
				$query = "SELECT ID_Worker FROM tURVData WHERE (ID_worker = ".$r[0].") AND (IN_WORK_DATE = CONVERT(DATETIME,'".date('m.d.Y H:i:s',strtotime($in_work_date))."'))";
				if ($r != mssql_fetch_row(mssql_query($query))) {
					$query = "INSERT INTO tURVData (ID_Worker, IN_WORK_DATE, DAY_START, DAY_END, IN_WORK_TIME_MINUTES, MORNING_TIME_MINUTES)
							VALUES     ('".$r[0]."','".date('m.d.Y H:i:s',strtotime($in_work_date))."','".$sd[$i]['P']."','".$sd[$i]['Q']."','".$in_work_time_minutes."','".$morning_time_minutes."')";
					$body.= "<tr class='tab_bg_1'><td>"
							.date("d.m.Y",strtotime($in_work_date))."</td><td>"
							.$sd[$i]['A']."</td><td>"
							.$sd[$i]['B']."</td><td>"
							.$sd[$i]['D']."</td><td>"
							.date("H:i",strtotime($day_start))."</td><td>"
							.date("H:i",strtotime($day_end))."</td><td>"
							.(($in_work_time_minutes <= 10 || $in_work_time_minutes > 700)?"<span style='color:red;'>".$in_work_time_minutes."</span>":$in_work_time_minutes)."</td><td>"
							.$morning_time_minutes."</td>";
					if ($insrt == 1) {
						if (mssql_query($query)) $body.="<td>inserted</td></tr>"; else $body.="<td><b><abbr title=\"".mssql_get_last_message()."\"><span style='color:red;'>error!</span></abbr></b></td></tr>";
					} else {$body.="<td><abbr title=\"".$query."\">debug</abbr></td></tr>";}
				} //--- Если нет записи - пишем строку в базу
				elseif ($r == mssql_fetch_row(mssql_query($query))) {
					$query = "UPDATE tURVData SET 
								DAY_START = '".$sd[$i]['P']."', 
								DAY_END = '".$sd[$i]['Q']."', 
								IN_WORK_TIME_MINUTES = '".$in_work_time_minutes."', 
								MORNING_TIME_MINUTES = '".$morning_time_minutes."' 
							WHERE (ID_Worker = '".$r[0]."') 
								AND (IN_WORK_DATE = '".date('m.d.Y H:i:s',strtotime($in_work_date))."')";
					$body.= "<tr class='tab_bg_1'><td>"
							.date("d.m.Y",strtotime($in_work_date))."</td><td>"
							.$sd[$i]['A']."</td><td>"
							.$sd[$i]['B']."</td><td>"
							.$sd[$i]['D']."</td><td>"
							.date("H:i",strtotime($day_start))."</td><td>"
							.date("H:i",strtotime($day_end))."</td><td>"
							.(($in_work_time_minutes <= 10 || $in_work_time_minutes > 700)?"<span style='color:red;'>".$in_work_time_minutes."</span>":$in_work_time_minutes)."</td><td>"
							.$morning_time_minutes."</td>";
					if ($insrt == 1) {
						if (mssql_query($query)) $body.="<td>updated</td></tr>"; else $body.="<td><b><abbr title=\"".mssql_get_last_message()."\"><span style='color:red;'>error!</span></abbr></b></td></tr>";
					} else {$body.="<td><abbr title=\"".$query."\">debug</abbr></td></tr>";}
				} //--- Если есть запись - обновляем данные
			} //--- Проверяем, есть ли записи по пользователю в базе от этой даты
				else $body.= "<tr class='tab_bg_1'><td> </td><td>".$sd[$i]['A']."</td><td>".$sd[$i]['B']."</td><td>".$sd[$i]['D']."</td><td> </td><td> </td><td> </td><td> </td><td><b>не в базе</b></td></tr>";
		} //--- Проверка на наличие данных во "Вход" или "Выход"
	} //--- Проверка на принадлежность к организации
//	else $body.= "<tr class='tab_bg_1'><td> </td><td>".$sd[$i]['A']."</td><td>не из Тюменьгипротрубопровод <br>или не Временный пропуск</td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td></tr>";
} //--- Прохождение всех занисей в файле
	} 
}
elseif (isset($_FILES['f']) and $_FILES['f']['error'] > 0) {
	$vers = "Ошибка загрузки файла №<a href='http://php.net/manual/ru/features.file-upload.errors.php'>" . $_FILES['f']['error'] . "</a>";
	$header = "Ошибка загрузки файла №<a href='http://php.net/manual/ru/features.file-upload.errors.php'>" . $_FILES['f']['error'] . "</a>";
}
elseif (isset($_REQUEST['us'])) {
	$filter="(|(sAMAAccountName=*)(cn=*))";
	$justthese = array("sAMAccountName","cn","department","whenCreated","whenChanged", "title");
	$ds = ldap_connect($domain) or die("Не могу соединиться с сервером LDAP.");
	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3); 
	ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
	if ($ds) {
		$ldapbind = ldap_bind($ds, $dom_user."@".$domain, $dom_pass);
 		if ($ldapbind) {
			$sr=ldap_search($ds, $dn, $filter, $justthese);
			$info = ldap_get_entries($ds, $sr);
			for ($i = $info["count"]-1;$i>=0; $i--) {
 					$query="UPDATE Workers SET Fl_Rel = 1, Exit_DT = (CASE WHEN Exit_DT = NULL THEN { fn NOW() } END) WHERE (Login = '".$info[$i]['samaccountname'][0]."')";
					if (mssql_query($query)) $status="v"; else $status="-";
					$body = $body . "<tr class='tab_bg_1'><td>"
						.$info[$i]['samaccountname'][0]."</td><td>"
						.iconv("UTF-8","CP1251",$info[$i]['cn'][0])."</td><td>"
						.iconv("UTF-8","CP1251",$info[$i]['title'][0])."</td><td>"
						.iconv("UTF-8","CP1251",$info[$i]['department'][0])."</td><td></td><td></td><td></td><td></td><td style='text-align:center;'>"
						.$status."</td></tr>";
			}
		} else {
			$html=str_ireplace("%header%","привязка LDAP не удалась...",$html);
		}
	}
}
$body.="</table>";
?>