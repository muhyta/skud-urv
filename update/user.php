<?php
function get_by_id($id,$db) {
    //$query="SELECT Workers.F_Worker, Workers.N_Worker, Workers.P_Worker, Workers.Login, Posts.N_Post, Otdels.Name_Otdel, Otdels.NB_Otdel, Workers.ID_Worker FROM Workers INNER JOIN Posts ON Workers.ID_Post = Posts.ID_Post INNER JOIN Otdels ON Workers.ID_Otdel = Otdels.ID_Otdel WHERE (Workers.ID_Worker='".$id."');";
    $query="SELECT Workers.F_Worker, Workers.N_Worker, Workers.P_Worker, Workers.Login, Workers.ID_Post, Workers.ID_Otdel, Otdels.NB_Otdel, Workers.ID_Worker, Workers.Fl_Rel, Workers.I_Worker FROM Workers INNER JOIN Posts ON Workers.ID_Post = Posts.ID_Post INNER JOIN Otdels ON Workers.ID_Otdel = Otdels.ID_Otdel WHERE (Workers.ID_Worker='".$id."');";
    $res=mssql_query($query,$db);
    $r=mssql_fetch_row($res);
    $tor="<div id=\"wait\" name='wait'>
                <form action='index.php' method='post' id='change' name='change'>
            	<input type='hidden' value='1' name='p' id='p'/>
            	<input type='hidden' value='2' name='flag' id='flag'/>
            	<input type='hidden' value='".$r[7]."' name='id_new' id='id_new'/>
                <table style='border:1px solid gray;z-index:1000;margin:10px 0px 10px 10px;width:97%;height:100%;'>
                    <tr>
		                <td class='tab_bg_2'>
				            <input type='text' name='w_f_new' style='width:90%;height:90%;' value='".$r[0]."'>
		                </td>
		            </tr>
		            <tr>
		                <td class='tab_bg_2'>
		                    <input type='text' name='w_n_new' style='width:90%;height:90%;' value='".$r[1]."'>
		                </td>
		            </tr>
		            <tr>
		                <td class='tab_bg_2'>
		                    <input type='text' name='w_p_new' style='width:90%;height:90%;' value='".$r[2]."'>
		                </td>
		            </tr>
		             <tr>
		                <td class='tab_bg_2'>
		                    <input type='text' name='w_l_new' style='width:90%;height:90%;' value='".$r[3]."'>
		                </td>
		            </tr>
		            <tr>
		                <td class='tab_bg_2'>
		                    <input list='posts' type='text' name='w_post_new' style='width:90%;height:90%;' value='".$r[4]."' />
		                    <datalist id='posts'>
		                        %posts%
		                    </datalist>
		                </td>
		            </tr>
		            <tr>
		                <td class='tab_bg_2'>
		                    <input list='otdels' type='text' name='w_otdel_new' style='width:90%;height:90%;' value='".$r[5]."' />
		                    <datalist id='otdels'>
		                        %otdels%
		                    </datalist>
		                </td>
		            </tr>
		             <tr>
		                <td class='tab_bg_2'>
		                    <abbr title='Проставить при увольнении ".$r[0]." ".$r[9]."'><input type='checkbox' name='w_fired' value='1'".(($r[8])?" checked ":" ")."/> <span style='font-size:9px;'>Уволен</span></abbr>
		                </td>
		            </tr>
		            <tr>
    		            <td class='tab_bg_2'>
		                    <input type='button' value='Применить' onclick='document.getElementById(\"flag\").value=2;document.change.submit();' style='width:150px;height:25px;'>
	                    </td>
	                </tr>
	            </table>
	            <input style='width:150px;height:90%;' type='button' value='Delete' onclick='document.getElementById(\"flag\").value=3;document.change.submit();'>
	            <input style='width:150px;height:90%;' type='button' value='Close' onclick='document.getElementById(\"wait\").style.visibility=\"hidden\";document.getElementById(\"wait_block\").style.visibility=\"hidden\";'>
	            </form>
        </div>";
    return $tor;}

function change_by_id($id,$f_new,$n_new,$p_new,$l_new,$post_new,$otdel_new,$fired,$db) {
    $query="UPDATE Workers SET F_Worker='".$f_new."', N_Worker='".$n_new."', P_Worker='".$p_new."',I_Worker='".substr($n_new,0,1).".".substr($p_new,0,1).".', Login='".$l_new."', ID_Post='".$post_new."', ID_Otdel='".$otdel_new."', Fl_Rel=".(($fired)?"'1'":"'0'")." WHERE (ID_Worker = ".$id.")";
    mssql_query($query,$db);
    }

function add($f_new,$n_new,$p_new,$l_new,$post_new,$otdel_new,$db) {
    $query="INSERT INTO Workers (F_Worker, N_Worker, P_Worker, Login, ID_Post, ID_Otdel, I_Worker) VALUES ('".
        $f_new."', '".
        $n_new."', '".
        $p_new."', '".
        $l_new."', ".
        $post_new.", ".
        $otdel_new.", '".
        substr($n_new,0,1).".".substr($p_new,0,1).".')";
    mssql_query($query,$db);
    }

function delete_by_id($id,$db) {
    $query="DELETE FROM Workers WHERE (ID_Worker=".$id.");";
    mssql_query($query,$db);
    }

function get_posts($db) {
    $query="SELECT N_Post, ID_Post FROM Posts ORDER BY 1;";
    $res=mssql_query($query,$db);

    $posts="";
    while ($r=mssql_fetch_row($res)) $posts.="<option value='".$r[1]."'>".$r[0]."</option>";
    return $posts;}

function get_otdels($db) {
    $query="SELECT Name_Otdel, ID_Otdel FROM Otdels ORDER BY 1;";
    $res=mssql_query($query,$db);

    $otdels="";
    while ($r=mssql_fetch_row($res)) $otdels.="<option value='".$r[1]."'>".$r[0]."</option>";
    return $otdels;}

//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
$body="";$base="";$sh_sel="";$name_sel="";$id_sel="";$err="";
if ($_REQUEST['flag_add'] == 1) add($_REQUEST['w_f_new'],$_REQUEST['w_n_new'],$_REQUEST['w_p_new'],$_REQUEST['w_l_new'],$_REQUEST['w_post_new'],$_REQUEST['w_otdel_new'],$db);
elseif ($_REQUEST['flag'] == 2) change_by_id($_REQUEST['id_new'],$_REQUEST['w_f_new'],$_REQUEST['w_n_new'],$_REQUEST['w_p_new'],$_REQUEST['w_l_new'],$_REQUEST['w_post_new'],$_REQUEST['w_otdel_new'],$_REQUEST['w_fired'],$db);
elseif ($_REQUEST['flag'] == 3) delete_by_id($_REQUEST['id_new'],$db);
$add="<tr><form action='index.php' method='post' id='fill'>
	    <input type='hidden' value='1' name='p' id='p'/>
	    <input type='hidden' value='1' name='flag_add' id='flag_add'/>
		<td class='tab_bg_2'>
				<input type='text' name='w_f_new' style='width:90%;height:90%;' value='Фамилия' onfocus='if(this.value==\"Фамилия\"){this.value=\"\";}' onblur='if(this.value==\"\"){this.value=\"Фамилия\";}'>
		</td>
		<td class='tab_bg_2'>
		        <input type='text' name='w_n_new' style='width:90%;height:90%;' value='Имя' onfocus='if(this.value==\"Имя\"){this.value=\"\";}' onblur='if(this.value==\"\"){this.value=\"Имя\";}'>
		</td>
		<td class='tab_bg_2'>
		        <input type='text' name='w_p_new' style='width:90%;height:90%;' value='Отчество' onfocus='if(this.value==\"Отчество\"){this.value=\"\";}' onblur='if(this.value==\"\"){this.value=\"Отчество\";}'>
		</td>
		<td class='tab_bg_2'>
		        <input type='text' name='w_l_new' style='width:90%;height:90%;' value='Логин' onfocus='if(this.value==\"Логин\"){this.value=\"\";}' onblur='if(this.value==\"\"){this.value=\"Логин\";}'>
		</td>
		<td class='tab_bg_2'>
		        <input list='posts' type='text' name='w_post_new' style='width:90%;height:90%;' value='Должность' onfocus='if(this.value==\"Должность\"){this.value=\"\";}' onblur='if(this.value==\"\"){this.value=\"Должность\";}' />
		        <datalist id='posts'>
		        %posts%
		        </datalist>
		</td>
		<td class='tab_bg_2'>
		        <input list='otdels' type='text' name='w_otdel_new' style='width:90%;height:90%;' value='Отдел' onfocus='if(this.value==\"Отдел\"){this.value=\"\";}' onblur='if(this.value==\"\"){this.value=\"Отдел\";}' />
		        <datalist id='otdels'>
		        %otdels%
		        </datalist>
		</td>
		<td class='tab_bg_2'>
		        <input type='submit' value='+' style='width:90%;height:120%;'>
	    </td>
	</form></tr>";
if (isset($_REQUEST['showall']) && htmlspecialchars($_REQUEST['showall'])==1) $showall=1;
    else $showall=0;
$query="SELECT Workers.F_Worker, Workers.N_Worker, Workers.P_Worker, Workers.Login, Posts.N_Post, Otdels.Name_Otdel, Otdels.NB_Otdel, Workers.ID_Worker, Fl_Rel FROM Workers INNER JOIN Posts ON Workers.ID_Post = Posts.ID_Post INNER JOIN Otdels ON Workers.ID_Otdel = Otdels.ID_Otdel ".(($showall)?"":"WHERE (Fl_Rel <> 1)")." ORDER BY 9,1,2,3";
$res=mssql_query($query,$db);
$base.="<form action='index.php' method='post' name='get' id='get'>
    <input type='hidden' value='1' name='p' id='p'/>
    <input type='hidden' value='0' name='showall' id='showall'/>
    <input type='hidden' name='p_id_del' id='p_id_del' value='1'>";
while ($r=mssql_fetch_row($res)) {
    $base.="<tr class='tab_bg_1' name='".$r[7]."' id='".$r[7]."' onclick='document.getElementById(\"p_id_del\").value=\"".$r[7]."\";document.get.submit();'>
			<td>".$r[0]."</td>
			<td>".$r[1]."</td>
			<td>".$r[2]."</td>
			<td>".$r[3]."</td>
			<td>".$r[4]."</td>
			<td><abbr title='".$r[5]."'>".$r[6]."</abbr></td>
			<td>".(($r[8])?"<span style='font-size:9px;color:#c0272b;'>Уволен</span>":"<span style='font-size:9px;color:#008844;'>Работает</span>")."</td></tr>";
}
$base.="<tr style='cursor: pointer;' class='tab_bg_1' onclick='document.getElementById(\"p_id_del\").value=\"0\";document.getElementById(\"showall\").value=\"".(($showall)?"0":"1")."\";document.get.submit();'>
			<td><span style='font-size:9px;color:#c0272b;'>Уволенные</span></td>
			<td><span style='font-size:9px;color:#c0272b;'>Уволенные</span></td>
			<td><span style='font-size:9px;color:#c0272b;'>Уволенные</span></td>
			<td><span style='font-size:9px;color:#c0272b;'>Уволенные</span></td>
			<td><span style='font-size:9px;color:#c0272b;'>Уволенные</span></td>
			<td><span style='font-size:9px;color:#c0272b;'>Уволенные</span></td>
			<td><span style='font-size:9px;color:#c0272b;'>Уволенные</span></td></tr>";
$base.="</form></table>";
if (isset($_REQUEST['p_id_del']) && strlen($_REQUEST['p_id_del'])>1) $base.=get_by_id($_REQUEST['p_id_del'],$db);
$body="<table style='cursor: pointer;' class='tab_cadrehov'>
	<tr class='tab_bg_2'>
		<th>Фамилия</th>
		<th>Имя</th>
		<th>Отчество</th>
		<th>Логин</th>
		<th>Должность</th>
		<th>Отдел</th>
		<th></th>
	</tr>";
$body=$body.$base;
$post_sel=get_posts($db);
$otd_sel=get_otdels($db);
unset($base,$query,$res,$sh_sel,$name_sel,$id_sel);
?>