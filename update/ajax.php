<?php
include_once('../config.php');

function get_user($id,$db) {
    $query="SELECT Workers.F_Worker, Workers.N_Worker, Workers.P_Worker, Workers.Login, Workers.ID_Post, Workers.ID_Otdel, Workers.ID_Worker, Workers.Fl_Rel, Workers.I_Worker, WorkersPers.birthday FROM Workers FULL OUTER JOIN WorkersPers ON Workers.ID_Worker = WorkersPers.ID_Worker WHERE (Workers.ID_Worker='".$id."');";
    $res=mssql_query($query,$db);
    $r=mssql_fetch_row($res);
    //       id | w_f_new | w_n_new | w_p_new | w_l_new | w_post_new | w_otdel_new | w_fired | w_i_new | bdate
    $tor=$r[6]."|".iconv('CP1251','UTF-8',$r[0])."|".iconv('CP1251','UTF-8',$r[1])."|".iconv('CP1251','UTF-8',$r[2])."|".$r[3]."|".$r[4]   ."|".$r[5]    ."|".$r[7]."|".iconv('CP1251','UTF-8',$r[8])."|".date('Y-m-d',strtotime($r[9]))."|";//iconv('CP1251','UTF-8',$r[3])."|".iconv('CP1251','UTF-8',$r[4])."|".$r[2];
    return $tor;}

function delete_user($id,$db) {
    $query="DELETE FROM Workers WHERE (ID_Worker=".$id.");";
    if (!mssql_query($query,$db)) return $query."\n".mssql_get_last_message(); else return iconv('CP1251','UTF-8',"Удалено");}

function edit_user($id,$f_new,$n_new,$p_new,$l_new,$post_new,$otdel_new,$fired,$db) {
    if ($id != 0) {
        if (strlen($post_new) <= 3) {
            $query="UPDATE Workers SET F_Worker='".$f_new."', N_Worker='".$n_new."', P_Worker='".$p_new."',I_Worker='".substr($n_new,0,1).".".substr($p_new,0,1).".', Login='".$l_new."', ID_Post='".$post_new."', ID_Otdel='".$otdel_new."', Fl_Rel=".(($fired)?"'1'":"'0'")." WHERE (ID_Worker = ".$id.")";
        }
        else {
            $query="INSERT INTO Posts (N_Post) VALUES ('".$post_new."');UPDATE Workers SET F_Worker='".$f_new."', N_Worker='".$n_new."', P_Worker='".$p_new."',I_Worker='".substr($n_new,0,1).".".substr($p_new,0,1).".', Login='".$l_new."', ID_Post=(SELECT TOP 1 ID_Post FROM Posts WHERE (N_Post = '".$post_new."')), ID_Otdel='".$otdel_new."', Fl_Rel=".(($fired)?"'1'":"'0'")." WHERE (ID_Worker = ".$id.")";
        }
        if (!mssql_query($query,$db)) return $query."\n".mssql_get_last_message(); else return iconv('CP1251','UTF-8',"Изменено");
    }
    else {
        if (strlen($post_new) <= 3) {
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
    if (!mssql_query($query,$db)) return $query."\n".mssql_get_last_message(); else return iconv('CP1251','UTF-8',"Добавлено");}

function get_time($id,$db) {
    $query="SELECT
            tURVData.IN_WORK_DATE,
			Workers.F_Worker,
			Workers.I_Worker,
			tURVData.DAY_START,
			tURVData.DAY_END,
			tURVData.IN_WORK_TIME_MINUTES,
			tURVData.MORNING_TIME_MINUTES
		FROM         tURVData INNER JOIN
                      Workers ON tURVData.ID_Worker = Workers.ID_Worker
		WHERE     (tURVData.id = ".$id.")";
    $res=mssql_query($query,$db);
    $r=mssql_fetch_row($res);
    return "$id|".date('Y-m-d',strtotime($r[0]))."|".iconv('CP1251','UTF-8',$r[1]." ".$r[2])."|".date('H:i',strtotime($r[3]))."|".date('H:i',strtotime($r[4]))."|$r[5]|$r[6]";}

function edit_time($id,$date,$in,$out,$in_time,$m_time,$db) {
    $query="UPDATE tURVData SET DAY_START = CONVERT(DATETIME, '".($date." ".$in.":00")."', 102),  DAY_END = CONVERT(DATETIME, '".($date." ".$out.":00")."', 102), IN_WORK_TIME_MINUTES = ".$in_time.", MORNING_TIME_MINUTES = ".$m_time." WHERE (tURVData.id = ".$id.")";
    if (!mssql_query($query,$db)) return $query."\n".mssql_get_last_message(); else return iconv('CP1251','UTF-8',"Изменено");}

function delete_time($id,$db) {
    $query="DELETE FROM tURVData WHERE (tURVData.id = ".$id.")";
    if (!mssql_query($query,$db)) return $query."\n".mssql_get_last_message(); else return iconv('CP1251','UTF-8',"Удалено");}

foreach ($_REQUEST as $k=>$v) $$k=$v;

if (isset($i)) switch ($i) {
    case 1: //получение данных пользователя
        echo get_user($id,$db);
        break;
    case 2: //изменение данных пользователя
        echo edit_user($id,$f_new,$n_new,$p_new,$l_new,$post_new,$otdel_new,$fired,$db);
        break;
    case 3: //удаление пользователя
        echo delete_user($id,$db);
        break;
    case 5: //получение данных СКУД
        echo get_time($id,$db);
        break;
    case 6: //изменение данных СКУД
        echo edit_time($id,$date,$in,$out,$in_time,$m_time,$db);
        break;
    case 7: //удаление записи СКУД
        echo delete_time($id,$db);
        break;
    default: break;
}

?>