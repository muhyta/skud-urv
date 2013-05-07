<?php
/**
 * Created by JetBrains PhpStorm.
 * User: VorotnikovMV
 * Date: 06.03.13
 * Time: 11:30
 * To change this template use File | Settings | File Templates.
 */

function get_users($db) {
    $res=mssql_query("SELECT user_id, name FROM [user] ORDER BY name",$db);
    $users="";
    while ($r=mssql_fetch_row($res)) $users.="<option value='".$r[0]."'>".$r[1]."</option>";
    return $users;}

mssql_close($db);unset($db);$db=mssql_connect($db2_srv,$db2_usr,$db2_psw);mssql_select_db($db2,$db);
$body="";$base="";$sh_sel="";$name_sel="";$id_sel="";$err="";$log="";
if (isset($_REQUEST['showall']) && htmlspecialchars($_REQUEST['showall'])==1) $showall=1;
else $showall=0;
$query="SELECT phones.number, [user].name, phones.black, phones.organization, phones.comment FROM phones LEFT OUTER JOIN [user] ON phones.user_id = [user].user_id ".(($showall)?"":"WHERE (black = 1)")." ORDER BY phones.black, [user].name, phones.number, phones.organization, phones.comment"; //".(($showall)?"":"WHERE (black = True)")."
$res=mssql_query($query,$db);
$base.="<form action='index.php' method='post' name='get' id='get'>
    <input type='hidden' value='1' name='p' id='p'/>
    <input type='hidden' value='0' name='showall' id='showall'/>
    <input type='hidden' name='num' id='num' value='1'>";
$i=0;
while ($r=mssql_fetch_row($res)) {
    $i++;
    $base.="<tr class='tab_bg_1' name='".$r[0]."' id='".$r[0]."' onclick='telNumjs(".$r[0].",1);'>
            <td style='text-align:center;'>".$i."</td>
			<td>".$r[0]."</td>
			<td>".$r[1]."</td>
			<td>".(($r[2])?"<span style='font-size:9px;color:#c0272b;'>Личный</span>":"<span style='font-size:9px;color:#008844;'>Рабочий</span>")."</td>
			<td>".$r[3]."</td>
			<td>".$r[4]."</td></tr>";
}
unset($i);
$base.="<tr style='cursor: pointer;' class='tab_bg_1' onclick='showall.value=\"".(($showall)?"0":"1")."\";get.submit();'>
			<td><span style='font-size:9px;color:#c0272b;'>...</span></td>
			<td><span style='font-size:9px;color:#008844;'>Рабочие</span></td>
			<td><span style='font-size:9px;color:#008844;'>Рабочие</span></td>
			<td><span style='font-size:9px;color:#008844;'>Рабочие</span></td>
			<td><span style='font-size:9px;color:#008844;'>Рабочие</span></td>
			<td><span style='font-size:9px;color:#008844;'>Рабочие</span></td></tr>";
$base.="</form></table>";
$body="<table style='cursor: pointer;' class='tab_cadrehov' id='main_table' name='main_table'>
	<tr class='tab_bg_2'>
	    <th>№</th>
		<th>Номер</th>
		<th>Ф.И.О.</th>
		<th>Сабж</th>
		<th>Организация</th>
		<th>Коментарий</th>
	</tr>";
$body=$body.$base;
$usr_sel=get_users($db);

unset($base,$query,$res,$sh_sel,$name_sel,$id_sel);
?>