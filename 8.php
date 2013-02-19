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
    $body="<table class='tab_cadrehov'>
		<tr class='tab_bg_2'>
			<th>Проект</th>
			<th>Фамилия И.О.</th>
			<th>Время</th>
		</tr>
		<tr>
			<th>
				<form action='index.php' method='post' id='filt'>
					<input type='hidden' value='8' name='p' id='p'/>
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
		</tr>";
    $query="SELECT Workers.F_Worker + ' ' + Workers.I_Worker AS name, Projects.Sh_project, SUM(CalWorksDec.TimeW) AS Expr3
            FROM Workers INNER JOIN CalWorks ON Workers.ID_Worker = CalWorks.ID_Worker INNER JOIN CalWorksDec ON CalWorks.ID_Rec = CalWorksDec.ID_Rec INNER JOIN Projects ON CalWorksDec.ID_Project = Projects.ID_Project INNER JOIN CalWorksVarWorks ON CalWorksDec.ID_NW = CalWorksVarWorks.ID_NW INNER JOIN CalWorksTW ON CalWorksDec.ID_TW = CalWorksTW.ID_TW INNER JOIN Otdels ON Workers.ID_Otdel = Otdels.ID_Otdel
            WHERE (CalWorks.NYear = ".$y.") AND (CalWorks.NMonth = ".$m.") AND (Otdels.Name_Otdel = '".$dep."')
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
} elseif ($m == 0) { //---за год
    $body="<table class='tab_cadrehov'>
		<tr class='tab_bg_2'>
			<th>Проект</th>
			<th>Фамилия И.О.</th>
			<th>Время, мин</th>
		</tr>
		<tr>
			<th>
				<form action='index.php' method='post' id='filt'>
					<input type='hidden' value='6' name='p' id='p'/>
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