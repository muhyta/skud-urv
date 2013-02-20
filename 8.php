<?php
/**
 * Created by JetBrains PhpStorm.
 * User: VorotnikovMV
 * Date: 18.02.13
 * Time: 18:36
 * To change this template use File | Settings | File Templates.
 */
$users=getUserList();
$dep = $users[substr($_SERVER['AUTH_USER'],strpos($_SERVER['AUTH_USER'],"\\")+1)][1];
//var_dump($dep);
ini_set("max_execution_time", 0);
if ($m != 0) { //---по месяцам
    $d1 = (isset($_REQUEST['d1'])?$_REQUEST['d1']:1);$d1_sel="";
    $d2 = (isset($_REQUEST['d2'])?$_REQUEST['d2']:31);$d2_sel="";
    if ($d1 > $d2) $d2 = 31;
    for ($i=1;$i<=31;$i++) {
        if ($d1==$i) $d1_sel.="<option selected>".$i."</option>"; else $d1_sel.="<option>".$i."</option>";
        if ($d2==$i) $d2_sel.="<option selected>".$i."</option>"; else $d2_sel.="<option>".$i."</option>";
    }
    $body="<table class='tab_cadrehov'>
		<tr class='tab_bg_2'>
		    <th>Фамилия И.О.</th>
			<th>Проект</th>
			<th>Время</th>
			<th>Дни</th>
		</tr>
		<tr>
			<th>
				<form action='index.php' method='post' id='filt'>
					<input type='hidden' value='8' name='p' id='p'/>
					с
					<select name='d1'>
						".$d1_sel."
					</select>
					до
					<select name='d2' onchange=\"start();document.getElementById('filt').submit();\">
						".$d2_sel."
					</select>
					<select name='m' onchange=\"start();document.getElementById('filt').submit();\">
						".$m_sel."
					</select>
					<select name='y' onchange=\"start();document.getElementById('filt').submit();\">
						".$y_sel."
					</select>
				</form>
			</th>
			<th></th>
			<th></th>
			<th></th>
		</tr>";
    $query="SELECT Workers.F_Worker + ' ' + Workers.I_Worker AS name, Projects.Sh_project, SUM(CalWorksDec.TimeW) AS Expr3
            FROM Workers INNER JOIN CalWorks ON Workers.ID_Worker = CalWorks.ID_Worker INNER JOIN CalWorksDec ON CalWorks.ID_Rec = CalWorksDec.ID_Rec INNER JOIN Projects ON CalWorksDec.ID_Project = Projects.ID_Project INNER JOIN CalWorksVarWorks ON CalWorksDec.ID_NW = CalWorksVarWorks.ID_NW INNER JOIN CalWorksTW ON CalWorksDec.ID_TW = CalWorksTW.ID_TW INNER JOIN Otdels ON Workers.ID_Otdel = Otdels.ID_Otdel
            WHERE (CalWorks.NYear = ".$y.") AND (CalWorks.NMonth = ".$m.") AND (Otdels.Name_Otdel = '".$dep."') AND (CalWorksDec.NTD <= ".((isset($d2)&&strlen($d2)>0)?$d2:"31").") AND (CalWorksDec.NTD >= ".((isset($d1)&&strlen($d1)>0)?$d1:"1").")
            GROUP BY Workers.F_Worker + ' ' + Workers.I_Worker, Projects.Sh_project
            ORDER BY Workers.F_Worker + ' ' + Workers.I_Worker";
    $res=mssql_query($query,$db);
    while($r=mssql_fetch_row($res)) {
        $query1="SELECT CalWorksDec.NTD AS Expr1
            FROM Workers INNER JOIN CalWorks ON Workers.ID_Worker = CalWorks.ID_Worker INNER JOIN CalWorksDec ON CalWorks.ID_Rec = CalWorksDec.ID_Rec INNER JOIN Projects ON CalWorksDec.ID_Project = Projects.ID_Project INNER JOIN CalWorksVarWorks ON CalWorksDec.ID_NW = CalWorksVarWorks.ID_NW INNER JOIN CalWorksTW ON CalWorksDec.ID_TW = CalWorksTW.ID_TW
            WHERE (CalWorks.NYear = ".$y.")
               AND (CalWorks.NMonth = ".$m.")
               AND (CalWorksDec.NTD <= ".((isset($d2)&&strlen($d2)>0)?$d2:"31").")
               AND (CalWorksDec.NTD >= ".((isset($d1)&&strlen($d1)>0)?$d1:"1").")
               AND (Projects.Sh_project = '".$r[1]."')
               AND (Workers.F_Worker + ' ' + Workers.I_Worker = '".$r[0]."') ORDER BY CalWorksDec.NTD";
        $res2=mssql_query($query1,$db);
        $body.= "<tr class='tab_bg_1'>
            <td>".$r[0]."</td>
			<td>".$r[1]."</td>
			<td>".round($r[2]*60,2)." минут (".round($r[2],2)." часов)</td>
			<td>";
        while ($r2=mssql_fetch_row($res2)) $body.=$r2[0].", ";
        $body = substr($body,0,strlen($body)-2);
		$body.="</td></tr>";
    }
    $body.="<tr><th><abbr title=\"".$query."\">debug</abbr></th><th>debug</th><th>debug</th><th>debug</th></tr>";
    $body.="</table>";
} elseif ($m == 0) { //---за год
    $body="<table class='tab_cadrehov'>
		<tr class='tab_bg_2'>
			<th>Фамилия И.О.</th>
			<th>Проект</th>
			<th>Время</th>
		</tr>
		<tr>
			<th>
				<form action='index.php' method='post' id='filt'>
					<input type='hidden' value='8' name='p' id='p'/>
					<select name='m' onchange=\"document.getElementById('filt').submit();\">
						".$m_sel."
					</select>
					<select name='y' onchange=\"document.getElementById('filt').submit();\">
						".$y_sel."
					</select>
				</form>
			</th>
			<th></th>
			<th></th>
		</tr>";
    $query="SELECT Workers.F_Worker + ' ' + Workers.I_Worker AS name, Projects.Sh_project, SUM(CalWorksDec.TimeW) AS Expr3
            FROM Workers INNER JOIN CalWorks ON Workers.ID_Worker = CalWorks.ID_Worker INNER JOIN CalWorksDec ON CalWorks.ID_Rec = CalWorksDec.ID_Rec INNER JOIN Projects ON CalWorksDec.ID_Project = Projects.ID_Project INNER JOIN CalWorksVarWorks ON CalWorksDec.ID_NW = CalWorksVarWorks.ID_NW INNER JOIN CalWorksTW ON CalWorksDec.ID_TW = CalWorksTW.ID_TW INNER JOIN Otdels ON Workers.ID_Otdel = Otdels.ID_Otdel
            WHERE (Otdels.Name_Otdel = '".$dep."') AND (CalWorks.NYear = ".$y.")
            GROUP BY Workers.F_Worker + ' ' + Workers.I_Worker, Projects.Sh_project
            ORDER BY Workers.F_Worker + ' ' + Workers.I_Worker";
    $res=mssql_query($query,$db);
    while($r=mssql_fetch_row($res)) {
        $body.= "<tr class='tab_bg_1'>
        <td>".$r[0]."</td>
	    <td>".$r[1]."</td>
		<td>".round($r[2]*60,2)." минут (".round($r[2],2)." часов)</td>
		</tr>";
    }
    $body.="</table>";
}
?>