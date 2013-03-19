<?php
/**
 * Created by JetBrains PhpStorm.
 * User: VorotnikovMV
 * Date: 06.03.13
 * Time: 11:30
 * To change this template use File | Settings | File Templates.
 */

function get_by_id($id,$db) {
    $query="SELECT number, user_id, black, organization, comment FROM phones WHERE (number = ".$id.");";
    $res=mssql_query($query,$db);
    $r=mssql_fetch_row($res);
    $tor="<div id=\"wait\" name='wait'>
                <form action='index.php' method='post' id='change' name='change'>
            	<input type='hidden' value='1' name='p' id='p'/>
            	<input type='hidden' value='2' name='flag' id='flag'/>
            	<input type='hidden' value='".$r[0]."' name='id' id='id'/>
                <table style='border:1px solid gray;z-index:1000;margin:10px 0px 10px 10px;width:97%;height:100%;'>
                    <tr>
		                <td class='tab_bg_2'>
				            <input type='text' name='num_new' style='width:90%;height:90%;' value='".$r[0]."' />
		                </td>
		            </tr>
		            <tr>
		                <td class='tab_bg_2'>
		                    <input list='users' type='text' name='user_new' style='width:90%;height:90%;' value='".$r[1]."' />
		                    <datalist id='users'>
		                        %users%
		                    </datalist>
		                </td>
		            </tr>
		            <tr>
		                <td class='tab_bg_2'>
		                    <input type='text' name='organization_new' style='width:90%;height:90%;' value='".$r[3]."' />
		                </td>
		            </tr>
		             <tr>
		                <td class='tab_bg_2'>
		                    <input type='text' name='comment_new' style='width:90%;height:90%;' value='".$r[4]."' />
		                </td>
		            </tr>
		            <tr>
		                <td class='tab_bg_2'>
		                    <input type='checkbox' name='black_new' value='1' ".(($r[2])?"checked":"")." />
		                </td>
		            </tr>
		            <tr>
    		            <td class='tab_bg_2'>
		                    <input type='button' value='Применить' onclick='document.getElementById(\"flag\").value=2;document.change.submit();' style='width:150px;height:24px;'>
	                    </td>
	                </tr>
	            </table>
	            <input style='width:150px;height:90%;' type='button' value='Delete' onclick='document.getElementById(\"flag\").value=3;document.change.submit();'>
	            <input style='width:150px;height:90%;' type='button' value='Close' onclick='document.getElementById(\"wait\").style.visibility=\"hidden\";document.getElementById(\"wait_block\").style.visibility=\"hidden\";'>
	            </form>
        </div>";
    return $tor;}

function change_by_id($id,$num_new,$user_new,$black_new,$organization_new,$comment_new,$db) {
    if (!isset($user_new)) $user_new="";
    $query="UPDATE phones SET number = ".$num_new.(($user_new == "")?"":", user_id = ".$user_new).", black = ".(($black_new)?"1":"0").", organization = '".$organization_new."', comment = '".$comment_new."' WHERE (number = ".$id.")";
    if (!mssql_query($query,$db)) return mssql_get_last_message(); else return "";
}

function add($num_new,$user_new,$black_new,$organization_new,$comment_new,$db) {
    $num_new=htmlspecialchars($num_new);
    $num_new=trim($num_new);
    $num_new=str_ireplace("-","",$num_new);
    $num_new=str_ireplace("(","",$num_new);
    $num_new=str_ireplace(")","",$num_new);
    $num_new=str_ireplace(" ","",$num_new);
    $num_new=trim($num_new);
    if ((substr($num_new,0,1) != "7" || substr($num_new,0,1) != "8") && strlen($num_new) > 7) $num_new="7".$num_new;
    if (!isset($user_new) || $user_new="Пользователь") $user_new="";
    if (!isset($comment_new) || $comment_new="Коментарий") $comment_new="date('d-m-Y')";
    if (!isset($organization_new) || $organization_new="Организация") $organization_new="date('d-m-Y')";
    $r=mssql_fetch_array(mssql_query("SELECT number FROM phones WHERE (number = ".$num_new.")",$db));
    if (isset($r[0])) {
        $query="UPDATE phones SET number = ".$num_new.(($user_new == "")?"":", user_id = ".$user_new).", black = ".(($black_new)?"1":"0").", organization = '".$organization_new."', comment = '".$comment_new."' WHERE (number = ".$num_new.")";
        if (!mssql_query($query,$db)) return $query; else return "";
    }
    else {
        $query="INSERT INTO phones (number, ".(($user_new == "")?"":"user_id, ")." black, organization, comment) VALUES (".
            $num_new.", ".
            (($user_new == "")?"":$user_new.", ").
            (($black_new)?"1":"0").", '".
            $organization_new."', '".
            $comment_new."')";
        if (!mssql_query($query,$db)) return $query; else return "";
    }
}

function delete_by_id($id,$db) {
    $query="DELETE FROM phones WHERE (number = ".$id.");";
    if (!mssql_query($query,$db)) return mssql_get_last_message(); else return "";
}

function get_users($db) {
    $res=mssql_query("SELECT user_id, name FROM [user] ORDER BY name",$db);
    $users="";
    while ($r=mssql_fetch_row($res)) $users.="<option value='".$r[0]."'>".$r[1]."</option>";
    return $users;}

//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
mssql_close($db);unset($db);$db=mssql_connect($db2_srv,$db2_usr,$db2_psw);mssql_select_db($db2,$db);
$body="";$base="";$sh_sel="";$name_sel="";$id_sel="";$err="";$log="";
if (isset($_REQUEST['black_new'])) $black_new = 1; else $black_new=0;
if ($_REQUEST['flag_add'] == 1) $err=add($_REQUEST['num_new'],$_REQUEST['user_new'],$black_new,$_REQUEST['organization_new'],$_REQUEST['comment_new'],$db);
elseif ($_REQUEST['flag'] == 2) $err=change_by_id($_REQUEST['id'],$_REQUEST['num_new'],$_REQUEST['user_new'],$black_new,$_REQUEST['organization_new'],$_REQUEST['comment_new'],$db);
elseif ($_REQUEST['flag'] == 3) $err=delete_by_id($_REQUEST['id'],$db);

$add="
    <table class='tab_cadre_pager'>
    <tr><form action='index.php' method='post' id='fill'>
	    <input type='hidden' value='1' name='p' id='p'/>
	    <input type='hidden' value='1' name='flag_add' id='flag_add'/>
		<td class='tab_bg_2'>
				<input type='text' name='num_new' style='width:90%;height:90%;' value='Номер' onfocus='if(this.value==\"Номер\"){this.value=\"\";}' onblur='if(this.value==\"\"){this.value=\"Номер\";}'>
		</td>
		<td class='tab_bg_2'>
		        <input list='users' type='text' name='user_new' style='width:90%;height:90%;' value='Пользователь' onfocus='if(this.value==\"Пользователь\"){this.value=\"\";}' />
		        <datalist id='users'>
		        %users%
		        </datalist>
		</td>
		<td class='tab_bg_2'>
		        <input type='checkbox' name='black_new' value='1' checked>
		</td>
		<td class='tab_bg_2'>
		        <input type='text' name='organization_new' style='width:90%;height:90%;' value='Организация' onfocus='if(this.value==\"Организация\"){this.value=\"\";}'>
		</td>
		<td class='tab_bg_2'>
		        <input type='text' name='comment_new' style='width:90%;height:90%;' value='Коментарий' onfocus='if(this.value==\"Коментарий\"){this.value=\"\";}'>
		</td>
		<td class='tab_bg_2'>
		        <input type='submit' value='+' style='width:90%;height:120%;border:1px solid #f2f2f2;' onmouseover='this.style.border=\"1px solid black\";' onmouseout='this.style.border=\"1px solid #f2f2f2\";'>
	    </td>
	</form></tr></table>";
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
    $base.="<tr class='tab_bg_1' name='".$r[0]."' id='".$r[0]."' onclick='document.getElementById(\"num\").value=\"".$r[0]."\";document.get.submit();'>
            <td style='text-align:center;'>".$i."</td>
			<td>".$r[0]."</td>
			<td>".$r[1]."</td>
			<td>".(($r[2])?"<span style='font-size:9px;color:#c0272b;'>Личный</span>":"<span style='font-size:9px;color:#008844;'>Рабочий</span>")."</td>
			<td>".$r[3]."</td>
			<td>".$r[4]."</td></tr>";
}
unset($i);
$base.="<tr style='cursor: pointer;' class='tab_bg_1' onclick='document.getElementById(\"num\").value=\"0\";document.getElementById(\"showall\").value=\"".(($showall)?"0":"1")."\";document.get.submit();'>
			<td><span style='font-size:9px;color:#c0272b;'>...</span></td>
			<td><span style='font-size:9px;color:#008844;'>Рабочие</span></td>
			<td><span style='font-size:9px;color:#008844;'>Рабочие</span></td>
			<td><span style='font-size:9px;color:#008844;'>Рабочие</span></td>
			<td><span style='font-size:9px;color:#008844;'>Рабочие</span></td>
			<td><span style='font-size:9px;color:#008844;'>Рабочие</span></td></tr>";
$base.="</form></table>";
if (isset($_REQUEST['num']) && strlen($_REQUEST['num'])>1) $base.=get_by_id($_REQUEST['num'],$db);
$body="<table style='cursor: pointer;' class='tab_cadrehov'>
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