<?php

$m_sel="";
$y_sel="";
$month=array(0 => "<option value='0'>За год</option>",
	1 => "<option value='01'>Январь</option>",
	2 => "<option value='02'>Февраль</option>",
	3 => "<option value='03'>Март</option>",
	4 => "<option value='04'>Апрель</option>",
	5 => "<option value='05'>Май</option>",
	6 => "<option value='06'>Июнь</option>",
	7 => "<option value='07'>Июль</option>",
	8 => "<option value='08'>Август</option>",
	9 => "<option value='09'>Сентябрь</option>",
	10 => "<option value='10'>Октябрь</option>",
	11 => "<option value='11'>Ноябрь</option>",
	12 => "<option value='12'>Декабрь</option>");


switch ($m) {
	case "1": $m_sel="<option selected value='01'>Январь</option>"; break;
	case "2": $m_sel="<option selected value='02'>Февраль</option>"; break;
	case "3": $m_sel="<option selected value='03'>Март</option>"; break;
	case "4": $m_sel="<option selected value='04'>Апрель</option>"; break;
	case "5": $m_sel="<option selected value='05'>Май</option>"; break;
	case "6": $m_sel="<option selected value='06'>Июнь</option>"; break;
	case "7": $m_sel="<option selected value='07'>Июль</option>"; break;
	case "8": $m_sel="<option selected value='08'>Август</option>"; break;
	case "9": $m_sel="<option selected value='09'>Сентябрь</option>"; break;
	case "10": $m_sel="<option selected value='10'>Октябрь</option>"; break;
	case "11": $m_sel="<option selected value='11'>Ноябрь</option>"; break;
	case "12": $m_sel="<option selected value='12'>Декабрь</option>"; break;
	case "0": $m_sel="<option selected value='0'>За год</option>"; break;
}

for ($i=0;$i<13;$i++){
	if ($i != $m) $m_sel.=$month[$i];
}

$year=array(0 => "<option value='2009'>2009</option>",
	1 => "<option value='2010'>2010</option>",
	2 => "<option value='2011'>2011</option>",
	3 => "<option value='2012'>2012</option>",
	4 => "<option value='2013'>2013</option>",
	5 => "<option value='2014'>2014</option>");

switch ($y) {
	case "2009": $y_sel="<option selected value='2009'>2009</option>"; break;
	case "2010": $y_sel="<option selected value='2010'>2010</option>"; break;
	case "2011": $y_sel="<option selected value='2011'>2011</option>"; break;
	case "2012": $y_sel="<option selected value='2012'>2012</option>"; break;
	case "2013": $y_sel="<option selected value='2013'>2013</option>"; break;
	case "2014": $y_sel="<option selected value='2014'>2014</option>"; break;
}

for ($i=3;$i<6;$i++){
	if (($i+2009) != $y) $y_sel.=$year[$i];
}

$menu="<li id='menu1'><a href='#' class='itemP' onclick=\"start();document.getElementById('p').value=1;document.getElementById('filt').submit();\" onmouseover=\"this.style.backgroundColor='rgb(221,255,112)';\" onmouseout=\"this.style.backgroundColor='transparent';\">УРВ > СКУД</a></li>
	<li id='menu2'><a href='#' class='itemP' onclick=\"start();document.getElementById('p').value=2;document.getElementById('filt').submit();\" onmouseover=\"this.style.backgroundColor='rgb(221,255,112)';\" onmouseout=\"this.style.backgroundColor='transparent';\">УРВ > 0 и СКУД = 0</a></li>
	<li id='menu3'><a href='#' class='itemP' onclick=\"start();document.getElementById('p').value=3;document.getElementById('filt').submit();\" onmouseover=\"this.style.backgroundColor='rgb(221,255,112)';\" onmouseout=\"this.style.backgroundColor='transparent';\">УРВ = 0 и СКУД > 0</a></li>
	<li id='menu4'><a href='#' class='itemP' onclick=\"start();document.getElementById('p').value=4;document.getElementById('filt').submit();\" onmouseover=\"this.style.backgroundColor='rgb(221,255,112)';\" onmouseout=\"this.style.backgroundColor='transparent';\">СКУД > УРВ</a></li>
	<li id='menu5'><a href='index.php?p=6' class='itemP' onclick=\"start();document.getElementById('p').value=6;document.getElementById('filt').submit();\" onmouseover=\"this.style.backgroundColor='rgb(221,255,112)';\" onmouseout=\"this.style.backgroundColor='transparent';\">Средний трудодень(СКУД)</a></li>
	<li id='menu6'><a href='index.php?p=7' class='itemP' onclick=\"start();document.getElementById('p').value=7;document.getElementById('filt').submit();\" onmouseover=\"this.style.backgroundColor='rgb(221,255,112)';\" onmouseout=\"this.style.backgroundColor='transparent';\">Средний трудодень(УРВ)</a></li>";

	if (isset($_REQUEST['p'])) $p=$_REQUEST['p']; else $p="6";
		switch ($p) {
		case "1": $dbv="vReportATotal"; 
			//if ($m < 10) $m=10;
			if ($y < 2012) $y=2012;
			$header=$m.".".$y.": Отчет УРВ > СКУД <br>(на работе проведено меньше времени, чем проставлено в карточке учета)";
			$menu=str_ireplace("<li id='menu1'>","<li id='menu1' style='background-color:rgb(221,255,112);'>",$menu);
			include("1.php"); break;
		case "2": $dbv="vReportBTotal"; 
			//if ($m < 10) $m=10;
			if ($y < 2012) $y=2012;	
			$header=$m.".".$y.": Отчет УРВ > 0; СКУД = 0 <br>(в карточке учета время проставлено, а в СКУДе не отмечен)";
			$menu=str_ireplace("<li id='menu2'>","<li id='menu2' style='background-color:rgb(221,255,112);'>",$menu);
			include("2.php"); break; 
		case "3": $dbv="vReportCTotal"; 
			//if ($m < 10) $m=10;
			if ($y < 2012) $y=2012;
			$header=$m.".".$y.": Отчет УРВ = 0; СКУД > 0 <br>(в карточке учета время не проставлено, но в СКУДе отмечен)";
			$menu=str_ireplace("<li id='menu3'>","<li id='menu3' style='background-color:rgb(221,255,112);'>",$menu);
			include("3.php"); break; 
		case "4": $dbv="vReportDTotal"; 
			//if ($m < 10) $m=10;
			if ($y < 2012) $y=2012;
			$header=$m.".".$y.": Отчет СКУД > УРВ <br>(на работе проведено больше времени, чем проставлено в карточке учета)";
			$menu=str_ireplace("<li id='menu4'>","<li id='menu4' style='background-color:rgb(221,255,112);'>",$menu);
			include("4.php"); break;
		case "6": $header=$m.".".$y.": Средний трудодень <br>(по СКУД)";
			$menu=str_ireplace("<li id='menu5'>","<li id='menu5' style='background-color:rgb(221,255,112);'>",$menu);
			include("6.php"); break;
		case "7": $header=$m.".".$y.": Средний трудодень <br>(по УРВ)";
			$menu=str_ireplace("<li id='menu6'>","<li id='menu6' style='background-color:rgb(221,255,112);'>",$menu);
			include("7.php"); break;
		default: $header=$m.".".$y.": Средний трудодень <br>(по СКУД)";
			include("6.php");}
?>