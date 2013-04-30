<?php
include_once('../config.php');

function get_by_id($id,$db) {
    $query="SELECT number, user_id, black, organization, comment FROM phones WHERE (number = ".$id.");";
    $res=mssql_query($query,$db);
    $r=mssql_fetch_row($res);
    //id,num,user,org,comm,black
    $tor=$r[0]."|".$r[0]."|".$r[1]."|".$r[3]."|".$r[4]."|".$r[2];
    return $tor;}

function get_users($db) {
    $res=mssql_query("SELECT user_id, name FROM [user] ORDER BY name",$db);
    $users="";
    while ($r=mssql_fetch_row($res)) $users.="<option value='".$r[0]."'>".$r[1]."</option>";
    return $users;}

function change_by_id($id,$num_new,$user_new,$black_new,$organization_new,$comment_new,$db) {
    if (!isset($user_new)) $user_new="";
    $query="UPDATE phones SET number = ".$num_new.(($user_new == "")?"":", user_id = ".$user_new).", black = ".(($black_new)?"1":"0").", organization = '".$organization_new."', comment = '".$comment_new."' WHERE (number = ".$id.")";
    if (!mssql_query($query,$db)) return mssql_get_last_message(); else return 0;}

function delete_by_id($id,$db) {
    $query="DELETE FROM phones WHERE (number = ".$id.");";
    if (!mssql_query($query,$db)) return mssql_get_last_message(); else return 0;}

function add($num_new,$user_new,$black_new,$organization_new,$comment_new,$db) {
    $num_new=htmlspecialchars($num_new);
    $num_new=trim($num_new);
    $num_new=str_ireplace("-","",$num_new);
    $num_new=str_ireplace("(","",$num_new);
    $num_new=str_ireplace(")","",$num_new);
    $num_new=str_ireplace(" ","",$num_new);
    $num_new=trim($num_new);
    if ((substr($num_new,0,1) != "7" || substr($num_new,0,1) != "8") && strlen($num_new) > 7) $num_new="7".$num_new;
    if (!isset($user_new) || $user_new=="Пользователь") $user_new="";
    if (!isset($comment_new) || $comment_new=="Коментарий") $comment_new=date('d-m-Y');
    if (!isset($organization_new) || $organization_new=="Организация") $organization_new=date('d-m-Y');
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
        if (!mssql_query($query,$db)) return $query; else return "Удалено";}}

mssql_close($db);unset($db);$db=mssql_connect($db2_srv,$db2_usr,$db2_psw);mssql_select_db($db2,$db);

foreach ($_REQUEST as $k=>$v) $$k=$v;

if (isset($i)) switch ($i) {
    case 1: //получение данных номера
        echo get_by_id($id,$db);
        break;
    case 2: //изменение данных номера
        echo change_by_id($id,$num_new,$user_new,$black_new,$organization_new,$comment_new,$db);
        break;
    case 3: //удаление номера
        echo delete_by_id($id,$db);
        break;
    case 4: //добавление номера
        echo add($num_new,$user_new,$black_new,$organization_new,$comment_new,$db);
        break;
    default: break;
}
?>