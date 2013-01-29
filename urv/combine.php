<?php
/**
 * Created by JetBrains PhpStorm.
 * User: VorotnikovMV
 * Date: 18.01.13
 * Time: 10:56
 * To change this template use File | Settings | File Templates.
 */
function get_all($db) {
    $out="";
    $query="SELECT Sh_project, Name_Project, ID_Project FROM Projects ORDER BY 1";
    $res=mssql_query($query,$db);
    while ($r=mssql_fetch_row($res)) {
        $out.="<option value='".$r[2]."'>".$r[0]."</option>";
    }
    return $out;}

if (isset($_REQUEST['p_sh_1']) && isset($_REQUEST['p_sh_2']) && isset($_REQUEST['use']) && $_REQUEST['flag'] != "0") {
        switch ($_REQUEST['use']) {
            case "1": $query="UPDATE CalWorksDec SET ID_Project = ".$_REQUEST['p_sh_1']." WHERE (ID_Project = ".$_REQUEST['p_sh_2']."); DELETE FROM Projects WHERE (ID_Project = ".$_REQUEST['p_sh_2'].");";break;
            case "2": $query="UPDATE CalWorksDec SET ID_Project = ".$_REQUEST['p_sh_2']." WHERE (ID_Project = ".$_REQUEST['p_sh_1']."); DELETE FROM Projects WHERE (ID_Project = ".$_REQUEST['p_sh_1'].");";break;
        }
        if (mssql_query($query,$db)==false) {
            $add="<tr><td>".mssql_get_last_message($db)."</td></tr>";
        }
        else $add="<tr><td>Совмещено ".(($_REQUEST['use']=="1")?$_REQUEST['p_sh_2']." и ".$_REQUEST['p_sh_1']." с наименованием последнего.":$_REQUEST['p_sh_1']." и ".$_REQUEST['p_sh_2']." с наименованием последнего.")."</td></tr>";
    }
    else {
    $add="<form action='index.php' method='post' id='fill'>
	    <input type='hidden' value='2' name='p' id='p'/>
	    <input type='hidden' value='0' name='flag' id='flag'/>
	    <tr>
	    <td class='tab_bg_2'>
		        <input type='radio' value='1' name='use'>
	    </td>
		<td class='tab_bg_2'>
				<select name='p_sh_1' style='width:600px;height:20px;'>".get_all($db)."</select>
		</td>
		</tr>
		<tr>
		<td class='tab_bg_2'>
		        <input type='radio' value='2' name='use'>
	    </td>
		<td class='tab_bg_2'>
		        <select name='p_sh_2' style='width:600px;height:20px;'>".get_all($db)."</select>
		</td>
		</tr>
		<tr>
		<td class='tab_bg_2'>
		</td>
		<td class='tab_bg_2'>
		<input type='button' value='Совместить' style='height:24px;' onclick='document.getElementById(\"flag\").value=1;document.fill.submit();'>
		</td>
		</tr>
	</form>";
}
?>