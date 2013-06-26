<?php
include_once('../config.php');

function get_proj($id,$db) {
    $query="SELECT Sh_project, Name_Project, ID_Project FROM Projects WHERE (ID_Project='".$id."');";
    $res=mssql_query($query,$db);
    $r=mssql_fetch_row($res);
    //       id | sh | name
    return $r[2]."|".iconv('CP1251','UTF-8',$r[0])."|".iconv('CP1251','UTF-8',$r[1]);}

function delete_proj($id,$db) {
    $query="DELETE FROM Projects WHERE (ID_Project=".$id.");";
    if (!mssql_query($query,$db)) return $query."\n".mssql_get_last_message(); else return iconv('CP1251','UTF-8',"Удалено");}

function edit_proj($id,$sh,$name,$db) {
    if (strlen($sh) > 0 && strlen($sh) > 0 && $sh != "Шифр" && $name != "Наименование") {
        if ($id != 0) {
            $query="UPDATE Projects SET Sh_project='".$sh."', Name_Project='".$name."' WHERE (ID_Project='".$id."');";
            $ret = "Измменено";
        }
        else {
            $query="INSERT INTO Projects (Sh_project, Name_Project) VALUES ('".$sh."','".$name."');";
            $ret = "Добавлено";
        }
        if (!mssql_query($query,$db)) return $query."\n".mssql_get_last_message(); else return iconv('CP1251','UTF-8',$ret);
    }
    else {
        return iconv('CP1251','UTF-8',"Заполните все поля формы");
    }
}

function edit_proj_tdms($id,$sh,$name,$db) {
    if (strlen($sh) > 0 && strlen($sh) > 0 && $sh != "Шифр" && $name != "Наименование") {
        if ($id != 0) {
            $id = mssql_fetch_row(mssql_query("SELECT ID_Project FROM Projects WHERE (Sh_project='".$sh."') OR (Name_Project='".$name."')",$db));
            if (isset($id[0]) && $id[0] > 0) {
                $query="UPDATE Projects SET Sh_project='".$sh."', Name_Project='".$name."' WHERE (ID_Project='".$id[0]."');";
                $ret = "Измменено";
            }
            else {$query="INSERT INTO Projects (Sh_project, Name_Project) VALUES ('".$sh."','".$name."');";$ret="Объект добавлен";}
        }
        else {
            $query="INSERT INTO Projects (Sh_project, Name_Project) VALUES ('".$sh."','".$name."');";
            $ret = "Добавлено";
        }
        if ($query != "" && !mssql_query($query,$db)) return $query."\n".mssql_get_last_message(); else return iconv('CP1251','UTF-8',$ret);
    }
    else {
        return iconv('CP1251','UTF-8',"Шифр и наименование некоректно переданы");
    }
}

foreach ($_REQUEST as $k=>$v) $$k=htmlspecialchars($v);

if (isset($i)) switch ($i) {
    case 1: //получение данных проекта
        echo get_proj($id,$db);
        break;
    case 2: //изменение данных проекта
        echo edit_proj($id,$sh,$name,$db);
        break;
    case 3: //удаление проекта
        echo delete_proj($id,$db);
        break;
    case 4: //изменение данных проекта (TDMS)
        echo edit_proj($id,iconv('UTF-8','CP1251',$sh),iconv('UTF-8','CP1251',$name),$db);
        break;
    case 5: //изменение данных проекта (TDMS)
        echo edit_proj_tdms($id,iconv('UTF-8','CP1251',$sh),iconv('UTF-8','CP1251',$name),$db);
        break;
    default: break;
}

?>