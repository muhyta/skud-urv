<?php
ini_set("max_execution_time", 0);
if ($m != 0) { //---�� �������
	$body="<table class='tab_cadrehov'>
		<tr class='tab_bg_2'>
			<th>�����</th>
			<th>�������� �� ����� �� ���,<br>�����</th>
			<th>���������� � ������</th>
			<th>���������� �������<br>���� � ������</th>
			<th>���<br>��� ������</th>
			<th>������� ��������� <br>���</th>
		</tr>
		<tr>
			<th>
				<form action='index.php' method='post' id='filt'>
					<input type='hidden' value='7' name='p' id='p'/>
					<select name='m' onchange=\"start();document.getElementById('filt').submit();\">
						".$m_sel."
					</select>
					<select name='y' onchange=\"start();document.getElementById('filt').submit();\">
						".$y_sel."
					</select>
				</form>
			</th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>";
	$query="SELECT     cw_date_n AS ����, 
			dep_full_name AS [������������ ������], 
			total_sum_time_in_min AS [����� ����������, ���], 
			days_in_month AS [���������� ������� ���� � ������], 
			num_workers AS [���������� ������� � ������], 
			total_count_234 AS [��� ��� ������], 
			total_sum_time_in_min / (days_in_month * num_workers - total_count_234) AS Expr1
	FROM         vURVForAVGTotal
	WHERE     (cw_date_n >= CONVERT(DATETIME, '".$y."-".$m."-01 00:00:00', 102))
		AND (cw_date_n < CONVERT(DATETIME, '".(($m>11)?($y+1):$y)."-".(($m>11)?($m-11):($m+1))."-01 00:00:00', 102))
		AND  (NOT (dep_full_name LIKE '%�����%'))
		AND  (NOT (dep_full_name LIKE '%���������%'))
	ORDER BY total_sum_time_in_min / (days_in_month * num_workers - total_count_234) DESC";
	$res=mssql_query($query);
	while($row=mssql_fetch_row($res)) {
		$body.= "<tr class='tab_bg_1'>
			<td>".$row[1]."</td>
			<td>".round($row[2],2)."</td>
			<td><abbr title='...'>".$row[4]."</abbr></td>
			<td>".$row[3]."</td>
			<td>".$row[5]."</td>
			<td>".round($row[6] / 60, 2)."</td>
			</tr>"; 
	}
	$body.="</table>";
} elseif ($m == 0) { //---�� ���
	$body="<table class='tab_cadrehov'>
		<tr class='tab_bg_2'>
			<th>�����</th>
			<th>������</th>
			<th>�������</th>
			<th>����</th>
			<th>������</th>
			<th>���</th>
			<th>����</th>
			<th>����</th>
			<th>������</th>
			<th>��������</th>
			<th>�������</th>
			<th>������</th>
			<th>�������</th>
		</tr>
		<tr>
			<th>
				<form action='index.php' method='post' id='filt'>
					<input type='hidden' value='6' name='p' id='p'/>
					<select name='m' onchange=\"document.getElementById('filt').submit();\">
						".$m_sel."
					</select>
					<select name='y' onchange=\"document.getElementById('filt').submit();\">
						".$y_sel."
					</select>
				</form>
			</th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>";
	$r1 = array();
	$r2 = array();
	$r3 = array();
	for ($i=1;$i<date('m')+1;$i++) {
		$query="SELECT	dep_full_name AS [������������ ������], 
				total_sum_time_in_min / (days_in_month * num_workers - total_count_234) AS Expr1
			FROM	vURVForAVGTotal
			WHERE	(cw_date_n >= CONVERT(DATETIME, '".$y."-".$i."-01 00:00:00', 102))
				AND (cw_date_n < CONVERT(DATETIME, '".(($i>11)?($y+1):$y)."-".(($i>11)?($i-11):($i+1))."-01 00:00:00', 102))
				AND  (NOT (dep_full_name LIKE '%�����%'))
				AND  (NOT (dep_full_name LIKE '%���������%'))
			ORDER BY dep_full_name ASC";
		$res=mssql_query($query);
		while ($r=mssql_fetch_row($res)) {
			array_push($r1,$r[0]);
			array_push($r2,$r[1]);	
		}
		$r3[$i] = array_combine($r1,$r2);
		array_splice($r1,0);
		array_splice($r2,0);
	}

	foreach ($r3 as $key1 => $value1) {
		foreach ($value1 as $key => $value) {
			$body.= "<tr class='tab_bg_1'>";
			$body.= "<td>".$key."</td>";
			for ($j=1;$j<date('m')+1;$j++) {
				$body.= "<td>".round($r3[$j][$key]/60,2)."</td>";
			}
			$body.= "</tr>";
		}
	break;
	}
	$body.="</table>";
}
?>