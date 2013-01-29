<?php
/**
 * Created by JetBrains PhpStorm.
 * User: VorotnikovMV
 * Date: 18.01.13
 * Time: 10:56
 * To change this template use File | Settings | File Templates.
 */
function get_by_id($id,$db) {
    $query="SELECT Sh_project, Name_Project, ID_Project FROM Projects WHERE (ID_Project='".$id."');";
    $res=mssql_query($query,$db);
    $r=mssql_fetch_row($res);
    $tor="
            <div id=\"wait\" name='wait'>
                <form action='index.php' method='post' id='change' name='change'>
            	<input type='hidden' value='1' name='p' id='p'/>
            	<input type='hidden' value='2' name='flag' id='flag'/>
            	<input type='hidden' value='".$r[2]."' name='id_new' id='id_new'/>
                <table style='border:1px solid gray;z-index:1000;margin:10px 0px 10px 10px;width:97%;height:100%;'>
                    <tr>
		            <td class='tab_bg_2' style='vertical-align:top;'>
				        <input type='text' name='p_sh_new' style='width:95%;' value='".$r[0]."'>
		            </td>
		            <tr>
		            <td class='tab_bg_2'>
		                <textarea name='p_name_new' style='width:95%;height:190px;'>".$r[1]."</textarea>
		            </td>
		            </tr>
    		        <td class='tab_bg_2'>
		                <input type='submit' value='Применить' style='width:150px;height:25px;'>
	                </td>
	                </tr>
	            </table>
	            <input style='width:150px;height:90%;' type='button' value='Delete' onclick='document.getElementById(\"flag\").value=3;document.change.submit();'>
	            <input style='width:150px;height:90%;' type='button' value='Close' onclick='document.getElementById(\"wait\").style.visibility=\"hidden\";document.getElementById(\"wait_block\").style.visibility=\"hidden\";'>
	            </form>
        </div>";
    return $tor;}

function change_by_id($id,$sh,$name,$db) {
    $query="UPDATE Projects SET Sh_project='".$sh."', Name_Project='".$name."' WHERE (ID_Project='".$id."');";
    mssql_query($query,$db);}

function add($sh,$name,$db) {
    $query="INSERT INTO Projects (Sh_project, Name_Project) VALUES ('".$sh."','".$name."');";
    mssql_query($query,$db);
    $query="SELECT ID_Project FROM Projects WHERE (Sh_project='".$sh."') AND (Name_Project='".$name."');";
    $r=mssql_fetch_row(mssql_query($query,$db));
    return $r[0];}

function delete_by_id($id,$db) {
    $query="DELETE FROM Projects WHERE (ID_Project=".$id.");";
    mssql_query($query,$db);}

$body="";$base="";$sh_sel="";$name_sel="";$id_sel="";
if ($_REQUEST['flag_add'] == 1) add($_REQUEST['p_sh_new'],$_REQUEST['p_name_new'],$db);
elseif ($_REQUEST['flag'] == 2) change_by_id($_REQUEST['id_new'],$_REQUEST['p_sh_new'],$_REQUEST['p_name_new'],$db);
elseif ($_REQUEST['flag'] == 3) delete_by_id($_REQUEST['id_new'],$db);
$add="<tr><form action='index.php' method='post' id='fill'>
	    <input type='hidden' value='1' name='p' id='p'/>
	    <input type='hidden' value='1' name='flag_add' id='flag_add'/>
		<td class='tab_bg_2'>
				<input type='text' name='p_sh_new' style='width:90%;height:90%;' value='Шифр' onfocus='if(this.value==\"Шифр\"){this.value=\"\";}' onblur='if(this.value==\"\"){this.value=\"Шифр\";}'>
		</td>
		<td class='tab_bg_2'>
		        <input type='text' name='p_name_new' style='width:90%;height:90%;' value='Название' onfocus='if(this.value==\"Название\"){this.value=\"\";}' onblur='if(this.value==\"\"){this.value=\"Название\";}'>
		</td>
		<td class='tab_bg_2'>
		        <input type='submit' value='+' style='width:90%;height:120%;'>
	    </td>
	</form></tr>";
$query="SELECT TOP 100 Sh_project, Name_Project, ID_Project FROM Projects ORDER BY 3 DESC";
$res=mssql_query($query,$db);
$base.="<form action='index.php' method='post' name='get' id='get'><input type='hidden' value='1' name='p' id='p'/><input type='hidden' name='p_id_del' id='p_id_del' value='1'>";
while ($r=mssql_fetch_row($res)) {
    $base.="<tr class='tab_bg_1'>
			<td onclick='document.getElementById(\"p_id_del\").value=\"".$r[2]."\";document.get.submit();'>".((strlen($r[0])>32)?"<abbr title='".$r[0]."'>".substr($r[0],0,32)."...</abbr>":$r[0])."</td>
			<td onclick='document.getElementById(\"p_id_del\").value=\"".$r[2]."\";document.get.submit();'>".((strlen($r[1])>140)?"<abbr title='".$r[1]."'>".substr($r[1],0,140)."...</abbr>":$r[1])."</td>
			<td onclick='document.getElementById(\"p_id_del\").value=\"".$r[2]."\";document.get.submit();'>".$r[2]."</td></tr>";
    //$sh_sel.="<option>".((strlen($r[0])>32)?substr($r[0],0,32)."...":$r[0])."</option>";
    //$name_sel.="<option>".((strlen($r[1])>150)?substr($r[1],0,150)."...":$r[1])."</option>";
    //$id_sel.="<option>".$r[2]."</option>";
}
$base.="</form></table>";
if (isset($_REQUEST['p_id_del']) && strlen($_REQUEST['p_id_del'])>1) $base.=get_by_id($_REQUEST['p_id_del'],$db);
$body="<table class='tab_cadrehov'>
	<tr class='tab_bg_2'>
		<th>Шифр проекта</th>
		<th>Описание</th>
		<th>id</th>
	</tr>";
$body=$body.$base;
unset($base,$query,$res,$sh_sel,$name_sel,$id_sel);
?>