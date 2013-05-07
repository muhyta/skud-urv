<?php
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

function syncAD($domain,$dn,$dom_user,$dom_pass,$db,$f) {
    $debug=!$f;
    $tor="<table class='tab_cadre_pager'>";
    $filter="(|(sAMAAccountName=*)(cn=*))";
    $justthese = array("sAMAccountName","cn","department","whenCreated","whenChanged", "title", "distinguishedName");
    $ds = ldap_connect($domain) or die("Не могу соединиться с сервером LDAP.");
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
    if ($ds) {
        $ldapbind = ldap_bind($ds, $dom_user."@".$domain, $dom_pass);
        if ($ldapbind) {
            //if ($f != 1) $dn=substr($dn,strpos($dn,"OU=User"));
            $sr=ldap_search($ds, $dn, $filter, $justthese);
            $info = ldap_get_entries($ds, $sr);
            $res=mssql_query("SELECT Workers.F_Worker + ' ' + Workers.N_Worker + ' ' + Workers.P_Worker AS fio,
                                Posts.N_Post,
                                Otdels.Name_Otdel,
                                Workers.Login,
                                Fl_Rel
                            FROM Workers LEFT OUTER JOIN Otdels ON Workers.ID_Otdel = Otdels.ID_Otdel LEFT OUTER JOIN Posts ON Workers.ID_Post = Posts.ID_Post",$db);
            while($r=mssql_fetch_row($res)){
                $status="";
                $s=array(0=>"",1=>"",2=>"",3=>"");
                for ($i = $info["count"]-1;$i>=0; $i--) {
                    if ($r[3] == $info[$i]['samaccountname'][0] || $r[0] == iconv("UTF-8","CP1251",$info[$i]['cn'][0])) {
                        if (!$debug) $status="ok"; else $status="d";
                        if (substr_count(iconv("UTF-8","CP1251",$info[$i]['distinguishedname'][0]),"Уволен") > 0 && !$r[4]) {
                            if (!$debug)
                                if (mssql_query("UPDATE Workers SET Fl_Rel = 1, Exit_DT = (CASE WHEN Exit_DT = NULL THEN { fn NOW() } END) WHERE (Login = '".$info[$i]['samaccountname'][0]."') OR (F_Worker + ' ' + N_Worker + ' ' + P_Worker = '".iconv("UTF-8","CP1251",$info[$i]['cn'][0])."')",$db)) $status="v";
                                else $status="<abbr title='".mssql_get_last_message()."'>-</abbr>";
                            else $status="<abbr title='Под увольнение (уволен в домене)!'>d</abbr>";
                        }
                        if ($r[0] != iconv("UTF-8","CP1251",$info[$i]['cn'][0])) {
                            $s[0]="style='color:red;'в домене: ".iconv("UTF-8","CP1251",$info[$i]['cn'][0]);
                            $id_post=mssql_fetch_row(mssql_query("SELECT ID_Post FROM Posts WHERE (N_Post = '".iconv("UTF-8","CP1251",$info[$i]['title'][0])."')",$db));
                            $id_otd=mssql_fetch_row(mssql_query("SELECT ID_Otdel FROM Otdels WHERE (Name_Otdel = '".iconv("UTF-8","CP1251",$info[$i]['department'][0])."')",$db));
                            if (isset($id_post[0]) && isset($id_otd[0])) {
                                $f_new = substr(iconv("UTF-8","CP1251",$info[$i]['cn'][0]),0,strpos(iconv("UTF-8","CP1251",$info[$i]['cn'][0])," "));
                                $n_new = substr(iconv("UTF-8","CP1251",$info[$i]['cn'][0]),strpos(iconv("UTF-8","CP1251",$info[$i]['cn'][0])," "),strrpos(iconv("UTF-8","CP1251",$info[$i]['cn'][0])," ")-strpos(iconv("UTF-8","CP1251",$info[$i]['cn'][0])," "));
                                $p_new = substr(iconv("UTF-8","CP1251",$info[$i]['cn'][0]),strrpos(iconv("UTF-8","CP1251",$info[$i]['cn'][0])," "));
                                $l_new = iconv("UTF-8","CP1251",$info[$i]['samaccountname'][0]);
                                $post_new = $id_post[0];
                                $otdel_new = $id_otd[0];
                                if (!$debug) add($f_new,$n_new,$p_new,$l_new,$post_new,$otdel_new,$db);
                                else $status="<abbr title='".$f_new.$n_new.$p_new.$l_new.$post_new.$otdel_new."'>a</abbr>";
                            }
                        }
                        if ($r[1] != iconv("UTF-8","CP1251",$info[$i]['title'][0])) {
                            $s[1]="style='color:red;'в домене: ".iconv("UTF-8","CP1251",$info[$i]['title'][0]);
                            if (!$debug)
                                if (mssql_query("UPDATE Workers SET ID_Post = (SELECT TOP 1 ID_Post FROM Posts WHERE (N_Post = '".iconv("UTF-8","CP1251",$info[$i]['title'][0])."')) WHERE (Login = '".$info[$i]['samaccountname'][0]."') OR (F_Worker + ' ' + N_Worker + ' ' + P_Worker = '".iconv("UTF-8","CP1251",$info[$i]['cn'][0])."')",$db)) $status.="v";
                                else $status="<abbr title='".mssql_get_last_message()."'>-</abbr>";
                        }
                        if ($r[2] != iconv("UTF-8","CP1251",$info[$i]['department'][0])) {
                            $s[2]="style='color:red;'в домене: ".iconv("UTF-8","CP1251",$info[$i]['department'][0]);
                            if (!$debug)
                                if (mssql_query("UPDATE Workers SET ID_Otdel = (SELECT TOP 1 ID_Otdel FROM Otdels WHERE (Name_Otdel = '".iconv("UTF-8","CP1251",$info[$i]['department'][0])."')) WHERE (Login = '".$info[$i]['samaccountname'][0]."') OR (F_Worker + ' ' + N_Worker + ' ' + P_Worker = '".iconv("UTF-8","CP1251",$info[$i]['cn'][0])."')",$db)) $status.="v";
                                else $status="<abbr title='".mssql_get_last_message()."'>-</abbr>";
                        }
                        if ($r[3] != $info[$i]['samaccountname'][0]) {
                            $s[3]="style='color:red;'в домене: ".$info[$i]['samaccountname'][0];
                            if (!$debug)
                                if (mssql_query("UPDATE Workers SET Login = '".iconv("UTF-8","CP1251",$info[$i]['samaccountname'][0])."' WHERE (F_Worker + ' ' + N_Worker + ' ' + P_Worker = '".iconv("UTF-8","CP1251",$info[$i]['cn'][0])."')",$db)) $status.="v";
                                else $status.="<abbr title='".mssql_get_last_message()."'>-</abbr>";
                        }
                    }
                }
                if ($status == "") {
                    if (!$debug)
                        if (mssql_query("UPDATE Workers SET Fl_Rel = 1, Exit_DT = (CASE WHEN Exit_DT = NULL THEN { fn NOW() } END) WHERE (Login = '".$r[3]."') OR (F_Worker + ' ' + N_Worker + ' ' + P_Worker = '".$r[0]."')",$db)) $status="<abbr title='Уволили (нет в домене)!'>v</abbr>";
                        else $status="<abbr title='".mssql_get_last_message()."'>-</abbr>";
                    else $status="<abbr title='Под увольнение (нет в домене)!'>d</abbr>";
                }
                $tor.="<tr class='tab_bg_1'><td ".substr($s[3],0,strlen("style='color:red;'"))."><abbr title=\"".substr($s[3],strlen("style='color:red;'"))."\">"
                    .(($r[3] == "")?"+":$r[3])."</abbr></td><td ".substr($s[0],0,strlen("style='color:red;'"))."><abbr title=\"".substr($s[0],strlen("style='color:red;'"))."\">"
                    .(($r[0] == "")?"+":$r[0])."</abbr></td><td ".substr($s[1],0,strlen("style='color:red;'"))."><abbr title=\"".substr($s[1],strlen("style='color:red;'"))."\">"
                    .(($r[1] == "")?"+":$r[1])."</abbr></td><td ".substr($s[2],0,strlen("style='color:red;'"))."><abbr title=\"".substr($s[2],strlen("style='color:red;'"))."\">"
                    .(($r[2] == "")?"+":$r[2])."</abbr></td><td style='text-align:center;'>"
                    .$status."</td></tr>";
            }
        }
        else $tor.="<tr class='tab_bg_1'><td>привязка LDAP не удалась...(".$ds.",".$dn.")</td></tr>";
    }
    $tor.="</table><br>";
    return $tor;}
//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
$body="";$base="";$sh_sel="";$name_sel="";$id_sel="";$err="";$log="";
$add="";
if (isset($_REQUEST['us']) && $_REQUEST['us'] == 1) $log=syncAD($domain,$dn,$dom_user,$dom_pass,$db,1);
elseif (isset($_REQUEST['us']) && $_REQUEST['us'] == 0) $log=syncAD($domain,$dn,$dom_user,$dom_pass,$db,0);
$add="<table class='tab_cadre_pager'>
    <tr>
        <td style='text-align:center;' class='tab_bg_2'>
            <form action='index.php' method='post' name='sync' id='sync' enctype='multipart/form-data'>
            Синхронизировать с доменом<br><span style='font-size:11px;color:#c0272b;'>Для синхронизации пользователей нажмите необходимую кнопку<br>Лог синхронизации будет выведен ниже.</span>
            <br>
            <input type='hidden' name='p' id='p' value='1' />
            <input type='hidden' name='us' id='us' value='0' />
            <input type='button' value='Синхронизировать' onclick='document.getElementById(\"us\").value=1;document.sync.submit();' />
            <input type='submit' value='Проверить' />
            </form>
        </td></tr></table>";

if (isset($_REQUEST['showall']) && htmlspecialchars($_REQUEST['showall'])==1) $showall=1;
    else $showall=0;
$query="SELECT Workers.F_Worker, Workers.N_Worker, Workers.P_Worker, Workers.Login, Posts.N_Post, Otdels.Name_Otdel, Otdels.NB_Otdel, Workers.ID_Worker, Fl_Rel FROM Workers LEFT OUTER JOIN Posts ON Workers.ID_Post = Posts.ID_Post LEFT OUTER JOIN Otdels ON Workers.ID_Otdel = Otdels.ID_Otdel ".(($showall)?"":"WHERE (Fl_Rel <> 1)")." ORDER BY 9,1,2,3";
$res=mssql_query($query,$db);
$base.="<form action='index.php' method='post' name='get' id='get'>
    <input type='hidden' value='1' name='p' id='p'/>
    <input type='hidden' value='0' name='showall' id='showall'/>
    <input type='hidden' name='p_id_del' id='p_id_del' value='1'>";
$i=0;
while ($r=mssql_fetch_row($res)) {
    $i++;
    $base.="<tr class='tab_bg_1' name='".$r[7]."' id='".$r[7]."' onclick='usrTIMEjs(".$r[7].",1,\"".$domain."\");'>". //fill_change(this);
            "<td style='text-align:center;'>".$i."</td>
			<td>".$r[0]."</td>
			<td>".$r[1]."</td>
			<td>".$r[2]."</td>
			<td>".$r[3]."</td>
			<td>".$r[4]."</td>
			<td><abbr title='".$r[5]."'>".$r[6]."</abbr></td>
			<td>".(($r[8])?"<span style='font-size:9px;color:#c0272b;'>Уволен</span>":"<span style='font-size:9px;color:#008844;'>Работает</span>")."</td></tr>";
}
unset($i);
$base.="<tr style='cursor: pointer;' class='tab_bg_1' onclick='p_id_del.value=\"0\";showall.value=\"".(($showall)?"0":"1")."\";get.submit();'>
			<td><span style='font-size:9px;color:#c0272b;'>...</span></td>
			<td><span style='font-size:9px;color:#c0272b;'>Уволенные</span></td>
			<td><span style='font-size:9px;color:#c0272b;'>Уволенные</span></td>
			<td><span style='font-size:9px;color:#c0272b;'>Уволенные</span></td>
			<td><span style='font-size:9px;color:#c0272b;'>Уволенные</span></td>
			<td><span style='font-size:9px;color:#c0272b;'>Уволенные</span></td>
			<td><span style='font-size:9px;color:#c0272b;'>Уволенные</span></td>
			<td><span style='font-size:9px;color:#c0272b;'>Уволенные</span></td></tr>";
$base.="</form></table>";
if (isset($_REQUEST['p_id_del']) && strlen($_REQUEST['p_id_del'])>1) $base.=get_by_id($_REQUEST['p_id_del'],$db,$domain);
$body="<table style='cursor: pointer;' class='tab_cadrehov'>
	<tr class='tab_bg_2'>
	    <th>№</th>
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