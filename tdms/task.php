<?php
function get_jobs($otdel, $month, $db) {
    $q="SELECT vTASKS_SITE.obj_guid,
                vTASKS_SITE.status_descr,
                vTASKS_SITE.status_name,
                vDEPARTMENT.dep_code AS dep_from,
                vDEPARTMENT_1.dep_code AS dep_to,
                vTASKS_SITE.obj_name,
                vTASKS_SITE.ATTR_TASK_NUMBER,
                vTASKS_SITE.ATTR_TASK_DATE_GIVEN,
                vTASKS_SITE.ATTR_TASK_DATE_START
        FROM vTASKS_SITE INNER JOIN vDEPARTMENT ON vTASKS_SITE.ATTR_TASK_DEP_CUSTOMER = vDEPARTMENT.dep_id INNER JOIN vDEPARTMENT AS vDEPARTMENT_1 ON vTASKS_SITE.ATTR_TASK_DEP_PERFORMER = vDEPARTMENT_1.dep_id
        WHERE (vDEPARTMENT_1.dep_id = '".$otdel."')
        AND (vTASKS_SITE.ATTR_TASK_DATE_START >= CONVERT(DATETIME, '".date('Y')."-".$month."-01 00:00:00', 102))
        AND (vTASKS_SITE.ATTR_TASK_DATE_START < CONVERT(DATETIME, '".date('Y')."-".($month+1)."-01 00:00:00', 102))
        ORDER BY 2,9";
    $res=mssql_query($q,$db);
    $jobs="<table class='tab_cadre_pager'>
        <tr>
            <td style='text-align:center;' class='tab_bg_2'>
                <span style='font-size:18px;color:black;font-weight:bold;'>ТДМС - Задания</span>
            </td>
        </tr>
    </table>
    <table style='cursor: pointer;' class='tab_cadrehov'>
	<tr class='tab_bg_2'>
	    <th style='width:10px;'></th>
		<th>Отдел заказчик</th>
		<th>Описание</th>
		<th style='width:70px;'>Дата</th>
	</tr>";
    while ($r=mssql_fetch_row($res)) {
        $jobs.="<tr class='tab_bg_1' onclick='document.location.href=\"tdms://".$r[0]."\"'>";
        $jobs.="<td><abbr title='".$r[1]."'><img src='/pics/".$r[2].".png'></abbr></td>";
        $jobs.="<td>".$r[3]."</td>";
        $jobs.="<td>".$r[5]."</td>";
        $jobs.="<td>".date('d.m.Y',strtotime($r[8]))."</td>";
        $jobs.="</tr>";
    }
    return $jobs."</table>";}

function get_gipjobs($otdel, $month, $db) {
    $q="SELECT obj_guid,
            Выдано,
            Наименование,
            Срок
        FROM vOBJECT_GIP_TASK
        WHERE (dep_id = ".$otdel.")
            AND (Срок >= CONVERT(DATETIME, '".date('Y')."-".$month."-01 00:00:00', 102))
            AND (Срок < CONVERT(DATETIME, '".date('Y')."-".($month+1)."-01 00:00:00', 102))
        ORDER BY 2,4";
    $res=mssql_query($q,$db);
    $jobs="<table class='tab_cadre_pager'>
        <tr>
            <td style='text-align:center;' class='tab_bg_2'>
                <span style='font-size:18px;color:black;font-weight:bold;'>ТДМС - Задания ГИПа</span>
            </td>
        </tr>
    </table>
    <table style='cursor: pointer;' class='tab_cadrehov'>
	<tr class='tab_bg_2'>
	    <th style='width:10px;'></th>
		<th>Описание</th>
		<th style='width:70px;'>Дата</th>
	</tr>";
    while ($r=mssql_fetch_row($res)) {
        $jobs.="<tr class='tab_bg_1' onclick='document.location.href=\"tdms://".$r[0]."\"'>";
        $jobs.="<td><img src='/pics/".($r[1] == 1 ? "STATUS_TASK_ACCEPTED" : "STATUS_TASK_NOT_ACCEPTED").".png'></td>";
        $jobs.="<td>".$r[2]."</td>";
        $jobs.="<td>".date('d.m.Y',strtotime($r[3]))."</td>";
        $jobs.="</tr>";
    }
    return $jobs."</table>";}

function get_tzjobs($otdel, $month, $db) {
    $q="SELECT obj_guid,
            Экспертиза,
            Участие,
            Наименование,
            Срок
        FROM vOBJECT_TZ_TASK
        WHERE (dep_id = ".$otdel.")
            AND (Срок >= CONVERT(DATETIME, '".date('Y')."-".$month."-01 00:00:00', 102))
            AND (Срок < CONVERT(DATETIME, '".date('Y')."-".($month+1)."-01 00:00:00', 102))
        ORDER BY 2,3,5";
    $res=mssql_query($q,$db);
    $jobs="<table class='tab_cadre_pager'>
        <tr>
            <td style='text-align:center;' class='tab_bg_2'>
                <span style='font-size:18px;color:black;font-weight:bold;'>ТДМС - Задания ТЗ</span>
            </td>
        </tr>
    </table>
    <table style='cursor: pointer;' class='tab_cadrehov'>
	<tr class='tab_bg_2'>
	    <th style='width:10px;'></th>
	    <th style='width:10px;'></th>
		<th>Описание</th>
		<th style='width:70px;'>Дата</th>
	</tr>";
    while ($r=mssql_fetch_row($res)) {
        $jobs.="<tr class='tab_bg_1' onclick='document.location.href=\"tdms://".$r[0]."\"'>";
        $jobs.="<td><abbr title='Экспертиза проведена: ".($r[1] == 1 ? "Да" : "Нет")."'><img src='/pics/".($r[1] == 1 ? "STATUS_TASK_ACCEPTED" : "STATUS_TASK_NOT_ACCEPTED").".png'></abbr></td>";
        $jobs.="<td><abbr title='Участие ".($r[2] == 1 ? "принято" : "отклонено")."'><img src='/pics/".($r[2] == 1 ? "STATUS_TASK_ACCEPTED" : "STATUS_TASK_NOT_ACCEPTED").".png'></abbr></td>";
        $jobs.="<td>".$r[3]."</td>";
        $jobs.="<td>".date('d.m.Y',strtotime($r[4]))."</td>";
        $jobs.="</tr>";}
    $q="SELECT obj_guid,
            Экспертиза,
            Участие,
            Наименование,
            Срок
        FROM vOBJECT_TZ_IZM_TASK
        WHERE (dep_id = ".$otdel.")
            AND (Срок >= CONVERT(DATETIME, '".date('Y')."-".$month."-01 00:00:00', 102))
            AND (Срок < CONVERT(DATETIME, '".date('Y')."-".($month+1)."-01 00:00:00', 102))
        ORDER BY 2,3,5";
    $res=mssql_query($q,$db);
    while ($r=mssql_fetch_row($res)) {
        $jobs.="<tr class='tab_bg_1' onclick='document.location.href=\"tdms://".$r[0]."\"'>";
        $jobs.="<td><abbr title='Экспертиза проведена: ".($r[1] == 1 ? "Да" : "Нет")."'><img src='/pics/".($r[1] == 1 ? "STATUS_TASK_ACCEPTED" : "STATUS_TASK_NOT_ACCEPTED").".png'></abbr></td>";
        $jobs.="<td><abbr title='Участие ".($r[2] == 1 ? "принято" : "отклонено")."'><img src='/pics/".($r[2] == 1 ? "STATUS_TASK_ACCEPTED" : "STATUS_TASK_NOT_ACCEPTED").".png'></abbr></td>";
        $jobs.="<td>".$r[3]."</td>";
        $jobs.="<td>".date('d.m.Y',strtotime($r[4]))."</td>";
        $jobs.="</tr>";}
    return $jobs."</table>";}

function get_remarks($login, $month, $db) {
    $q="SELECT tExpertDecisions.id_block_ed,
            tRemark.remark_text,
            tObjects.code,
            tExpertDecisions.decision_date,
            tExpertDecisions.id_ed_kind
        FROM tExpertDecisions INNER JOIN tRemark ON tExpertDecisions.id = tRemark.id_expert_decisions INNER JOIN tDepartment ON tRemark.id_department_originator_total = tDepartment.id INNER JOIN tObjects ON tExpertDecisions.id_objects = tObjects.id INNER JOIN tEmployee ON tDepartment.id = tEmployee.id_department
        WHERE (NOT (tExpertDecisions.id_block_ed = 2))
            AND (tEmployee.login = '".$login."')
            AND (tExpertDecisions.date_answer_finish >= CONVERT(DATETIME, '".date('Y')."-".$month."-01 00:00:00', 102))
            AND (tExpertDecisions.date_answer_finish < CONVERT(DATETIME, '".date('Y')."-".($month+1)."-01 00:00:00', 102))";
    $res=mssql_query($q,$db);
    $jobs="<table class='tab_cadre_pager'>
        <tr>
            <td style='text-align:center;' class='tab_bg_2'>
                <span style='font-size:18px;color:black;font-weight:bold;'>СУЗ</span>
            </td>
        </tr>
    </table>
    <table style='cursor: pointer;' class='tab_cadrehov'>
	<tr class='tab_bg_2'>
	    <th style='width:10px;'></th>
		<th>Код проекта</th>
		<th>Текст замечания</th>
		<th style='width:70px;'>Дата</th>
	</tr>";
    while ($r=mssql_fetch_row($res)) {
        $jobs.="<tr class='tab_bg_1'>";
        $jobs.="<td>".($r[4] == 1 ? "out" : "in")."</td>";
        $jobs.="<td>".$r[2]."</td>";
        $jobs.="<td>".$r[1]."</td>";
        $jobs.="<td>".date('d.m.Y',strtotime($r[3]))."</td>";
        $jobs.="</tr>";
    }
    return $jobs."</table>";}

function get_users($db) {
    $q="SELECT TUser.F_LOGIN, vDEPARTMENT.dep_id FROM TUser INNER JOIN vDEPARTMENT ON TUser.F_DEPARTMENTID = vDEPARTMENT.dep_id";
    $rs=mssql_query($q,$db);
    $user=array();
    while($r=mssql_fetch_row($rs)) $user[$r[0]]=$r[1];
    return $user;}

function get_otdels($db) {
    $query="SELECT dep_id, dep_name, dep_code FROM vDEPARTMENT ORDER BY 2;";
    $res=mssql_query($query,$db);
    $otdels=array();
    while ($r=mssql_fetch_row($res)) $otdels[$r[0]]=$r[1]." - ".$r[2];
    return $otdels;}
//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
$mth=array(
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
foreach ($_REQUEST as $k => $v) $$k=htmlspecialchars($v);
if (!isset($month)) $month=date('m');
$db=mssql_connect($db2_srv,$db2_usr,$db2_psw);
mssql_select_db($db2_r,$db);
$remarks=get_remarks(substr($_SERVER['AUTH_USER'],7),$month,$db);
mssql_close($db);
$db=mssql_connect($db3_srv,$db2_usr,$db2_psw);
$body="";$base="";$sh_sel="";$name_sel="";$id_sel="";$err="";$log="";
$otdels=get_otdels($db);
$users=get_users($db);
if (!isset($otdel)) $otdel=$users[$_SERVER['AUTH_USER']];

$add="<form action='/tdms/index.php' method='post' id='dep' name='dep'><table class='tab_cadre_pager'><tr><td style='text-align:center;' class='tab_bg_2'><input type='hidden' id='p' name='p' value='1' />";

if (substr_count($_SERVER['AUTH_USER'],$admin_u[0]) || substr_count($_SERVER['AUTH_USER'],$admin_u[1]) || substr_count($_SERVER['AUTH_USER'],$admin_u[2])) {
    $o_sel="";
    foreach ($otdels as $id => $name) $o_sel .= "<option".(($id==$otdel) ? " selected" : "")." value='".$id."'>".$name."</option>";
    $add.="<select name='otdel' id='otdel' onchange='dep.submit();'>".$o_sel."</select></td><td>";
}
$m_sel="";
foreach ($mth as $m => $opt) $m_sel .= ($m==$month) ? substr($opt,0,8)." selected ".substr($opt,8) : $opt;
$add.="<select name='month' id='month' onchange='dep.submit();'>".$m_sel."</select></td></tr></table></form>";

$body=get_jobs($otdel,$month,$db)."<br>".get_gipjobs($otdel,$month,$db)."<br>".get_tzjobs($otdel, $month, $db)."<br>".$remarks;
unset($base,$query,$res,$sh_sel,$name_sel,$id_sel);
?>