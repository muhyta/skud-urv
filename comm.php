<?php
$m_sel="";$y_sel="";

$month=array(0 => "<option value='0'>�� ���</option>",
    1 => "<option value='01'>������</option>",
    2 => "<option value='02'>�������</option>",
    3 => "<option value='03'>����</option>",
    4 => "<option value='04'>������</option>",
    5 => "<option value='05'>���</option>",
    6 => "<option value='06'>����</option>",
    7 => "<option value='07'>����</option>",
    8 => "<option value='08'>������</option>",
    9 => "<option value='09'>��������</option>",
    10 => "<option value='10'>�������</option>",
    11 => "<option value='11'>������</option>",
    12 => "<option value='12'>�������</option>",
    20 => "<option selected value='0'>�� ���</option>",
    21 => "<option selected value='01'>������</option>",
    22 => "<option selected value='02'>�������</option>",
    23 => "<option selected value='03'>����</option>",
    24 => "<option selected value='04'>������</option>",
    25 => "<option selected value='05'>���</option>",
    26 => "<option selected value='06'>����</option>",
    27 => "<option selected value='07'>����</option>",
    28 => "<option selected value='08'>������</option>",
    29 => "<option selected value='09'>��������</option>",
    30 => "<option selected value='10'>�������</option>",
    31 => "<option selected value='11'>������</option>",
    32 => "<option selected value='12'>�������</option>");

for ($i=0;$i<13;$i++){
    if ($i != $m) $m_sel.=$month[$i];
    else $m_sel.=$month[$i+20];}

$year=array( 0 => "<option value='2010'>2010</option>",
    1 => "<option value='2011'>2011</option>",
    2 => "<option value='2012'>2012</option>",
    3 => "<option value='2013'>2013</option>",
    4 => "<option value='2014'>2014</option>",
    5 => "<option value='2015'>2015</option>",
    20 => "<option selected value='2010'>2010</option>",
    21 => "<option selected value='2011'>2011</option>",
    22 => "<option selected value='2012'>2012</option>",
    23 => "<option selected value='2013'>2013</option>",
    24 => "<option selected value='2014'>2014</option>",
    25 => "<option selected value='2015'>2015</option>");

for ($i=0;$i<6;$i++){
    if (($i+2010) != $y) $y_sel.=$year[$i];
    else $y_sel.=$year[$i+20];}

$menu="<li id='menu1'><a href='#' class='itemP' onclick=\"start();document.getElementById('p').value=1;document.getElementById('filt').submit();\" onmouseover=\"this.style.backgroundColor='rgb(221,255,112)';\" onmouseout=\"this.style.backgroundColor='transparent';\">��� > ����</a></li>
	<li id='menu2'><a href='#' class='itemP' onclick=\"start();document.getElementById('p').value=2;document.getElementById('filt').submit();\" onmouseover=\"this.style.backgroundColor='rgb(221,255,112)';\" onmouseout=\"this.style.backgroundColor='transparent';\">��� > 0 � ���� = 0</a></li>
	<li id='menu3'><a href='#' class='itemP' onclick=\"start();document.getElementById('p').value=3;document.getElementById('filt').submit();\" onmouseover=\"this.style.backgroundColor='rgb(221,255,112)';\" onmouseout=\"this.style.backgroundColor='transparent';\">��� = 0 � ���� > 0</a></li>
	<li id='menu4'><a href='#' class='itemP' onclick=\"start();document.getElementById('p').value=4;document.getElementById('filt').submit();\" onmouseover=\"this.style.backgroundColor='rgb(221,255,112)';\" onmouseout=\"this.style.backgroundColor='transparent';\">���� > ���</a></li>
	<li id='menu5'><a href='index.php?p=6' class='itemP' onclick=\"start();document.getElementById('p').value=6;document.getElementById('filt').submit();\" onmouseover=\"this.style.backgroundColor='rgb(221,255,112)';\" onmouseout=\"this.style.backgroundColor='transparent';\">������� ���������(����)</a></li>
	<li id='menu6'><a href='index.php?p=7' class='itemP' onclick=\"start();document.getElementById('p').value=7;document.getElementById('filt').submit();\" onmouseover=\"this.style.backgroundColor='rgb(221,255,112)';\" onmouseout=\"this.style.backgroundColor='transparent';\">������� ���������(���)</a></li>
	<li id='menu7'><a href='index.php?p=8' class='itemP' onclick=\"start();document.getElementById('p').value=8;document.getElementById('filt').submit();\" onmouseover=\"this.style.backgroundColor='rgb(221,255,112)';\" onmouseout=\"this.style.backgroundColor='transparent';\">����� (���)</a></li>";

	if (isset($_REQUEST['p'])) $p=$_REQUEST['p']; else $p="6";
		switch ($p) {
		case "1": $dbv="vReportATotal"; 
			//if ($m < 10) $m=10;
			if ($y < 2012) $y=2012;
			$header=$m.".".$y.": ����� ��� > ���� <br>(�� ������ ��������� ������ �������, ��� ����������� � �������� �����)";
			$menu=str_ireplace("<li id='menu1'>","<li id='menu1' style='background-color:rgb(221,255,112);'>",$menu);
			include("1.php"); break;
		case "2": $dbv="vReportBTotal"; 
			//if ($m < 10) $m=10;
			if ($y < 2012) $y=2012;	
			$header=$m.".".$y.": ����� ��� > 0; ���� = 0 <br>(� �������� ����� ����� �����������, � � ����� �� �������)";
			$menu=str_ireplace("<li id='menu2'>","<li id='menu2' style='background-color:rgb(221,255,112);'>",$menu);
			include("2.php"); break; 
		case "3": $dbv="vReportCTotal"; 
			//if ($m < 10) $m=10;
			if ($y < 2012) $y=2012;
			$header=$m.".".$y.": ����� ��� = 0; ���� > 0 <br>(� �������� ����� ����� �� �����������, �� � ����� �������)";
			$menu=str_ireplace("<li id='menu3'>","<li id='menu3' style='background-color:rgb(221,255,112);'>",$menu);
			include("3.php"); break; 
		case "4": $dbv="vReportDTotal"; 
			//if ($m < 10) $m=10;
			if ($y < 2012) $y=2012;
			$header=$m.".".$y.": ����� ���� > ��� <br>(�� ������ ��������� ������ �������, ��� ����������� � �������� �����)";
			$menu=str_ireplace("<li id='menu4'>","<li id='menu4' style='background-color:rgb(221,255,112);'>",$menu);
			include("4.php"); break;
		case "6": $header=$m.".".$y.": ������� ��������� <br>(�� ����)";
			$menu=str_ireplace("<li id='menu5'>","<li id='menu5' style='background-color:rgb(221,255,112);'>",$menu);
			include("6.php"); break;
		case "7": $header=$m.".".$y.": ������� ��������� <br>(�� ���)";
			$menu=str_ireplace("<li id='menu6'>","<li id='menu6' style='background-color:rgb(221,255,112);'>",$menu);
			include("7.php"); break;
        case "8": $header=$m.".".$y.": ����� �������� - ������ <br>(�� ���)";
            $menu=str_ireplace("<li id='menu7'>","<li id='menu7' style='background-color:rgb(221,255,112);'>",$menu);
            include("8.php"); break;
		default: $header=$m.".".$y.": ������� ��������� <br>(�� ����)";
			include("6.php");}
?>