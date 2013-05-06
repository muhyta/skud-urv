<?php
include_once('../config.php');

function get_by_id($id,$db) {
    $query="SELECT Workers.F_Worker, Workers.N_Worker, Workers.P_Worker, Workers.Login, Workers.ID_Post, Workers.ID_Otdel, Workers.ID_Worker, Workers.Fl_Rel, Workers.I_Worker FROM Workers WHERE (Workers.ID_Worker='".$id."');";
    $res=mssql_query($query,$db);
    $r=mssql_fetch_row($res);
    //       id | w_f_new | w_n_new | w_p_new | w_l_new | w_post_new | w_otdel_new | w_fired | w_i_new
    $tor=$r[6]."|".iconv('CP1251','UTF-8',$r[0])."|".iconv('CP1251','UTF-8',$r[1])."|".iconv('CP1251','UTF-8',$r[2])."|".$r[3]."|".$r[4]   ."|".$r[5]    ."|".$r[7]."|".iconv('CP1251','UTF-8',$r[8]);//iconv('CP1251','UTF-8',$r[3])."|".iconv('CP1251','UTF-8',$r[4])."|".$r[2];
    return $tor;}

function delete_by_id($id,$db) {
    $query="DELETE FROM Workers WHERE (ID_Worker=".$id.");";
    if (!mssql_query($query,$db)) return $query."\n".mssql_get_last_message(); else return iconv('CP1251','UTF-8',"Удалено");}

function add($id,$f_new,$n_new,$p_new,$l_new,$post_new,$otdel_new,$fired,$db) {
    if ($id != 0) {
        $query="UPDATE Workers SET F_Worker='".$f_new."', N_Worker='".$n_new."', P_Worker='".$p_new."',I_Worker='".substr($n_new,0,1).".".substr($p_new,0,1).".', Login='".$l_new."', ID_Post='".$post_new."', ID_Otdel='".$otdel_new."', Fl_Rel=".(($fired)?"'1'":"'0'")." WHERE (ID_Worker = ".$id.")";
        if (!mssql_query($query,$db)) return $query."\n".mssql_get_last_message(); else return iconv('CP1251','UTF-8',"Изменено");
    }
    else {
        if (sizeof(mssql_fetch_array(mssql_query("SELECT ID_Post FROM Posts WHERE (ID_Post = '".$post_new."')"))) > 0) {
            $query="INSERT INTO Workers (F_Worker, N_Worker, P_Worker, Login, ID_Post, ID_Otdel, I_Worker) VALUES ('".
                $f_new."', '".
                $n_new."', '".
                $p_new."', '".
                $l_new."', ".
                $post_new.", ".
                $otdel_new.", '".
                substr($n_new,0,1).".".substr($p_new,0,1).".')";}
        else {
            $query = "
                INSERT INTO Posts (N_Post) VALUES ('".$post_new."');
                INSERT INTO Workers (F_Worker, N_Worker, P_Worker, Login, ID_Post, ID_Otdel, I_Worker) SELECT '".
                    $f_new."' as f, '".
                    $n_new."' as n, '".
                    $p_new."' as p, '".
                    $l_new."' as l, ID_Post, ".
                    $otdel_new." as o, '".
                    substr($n_new,0,1).".".substr($p_new,0,1).".' as izz
                    FROM Posts WHERE (N_Post = '".$post_new."')";}}
    if (!mssql_query($query,$db)) return $query."\n".mssql_get_last_message(); else return iconv('CP1251','UTF-8',"Добавлено");
}

foreach ($_REQUEST as $k=>$v) $$k=$v;

/*
$id=7161;
$f_new="%C2%EE%F0%EE%F2%ED%E8%EA%EE%E2";
$n_new="%CC%E8%F5%E0%E8%EB";$p_new="%C2%EB%E0%E4%E8%EC%E8%F0%EE%E2%E8%F7";
$l_new="VorotnikovMV";
$post_new="20";
$otdel_new="19";
$fired="0";
$i_new="%CC.%C2.";
$i=2;
*/

if (isset($i)) switch ($i) {
    case 1: //получение данных номера
        echo get_by_id($id,$db);
        break;
    case 2: //изменение данных номера
        echo add($id,$f_new,$n_new,$p_new,$l_new,$post_new,$otdel_new,$fired,$db);
        break;
    case 3: //удаление номера
        echo delete_by_id($id,$db);
        break;
    default: break;
}


?>