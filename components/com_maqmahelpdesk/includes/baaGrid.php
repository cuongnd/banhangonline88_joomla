<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package   MaQma_Helpdesk
 * @copyright (C) 2006-2012 Components Lab, Lda.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

/***********************************************************
 * Class:		baaGrid									*
 * Version:	  1.6										*
 * Date:		 December 2003							  *
 * Author:	   Barry Andrew							   *
 * Copyright:	� BA Andrew 2003						   *
 * Licence :	 Free for non-commercial use				*
 *		 http://members.aol.com/barryaandrew/baaLic.html  *
 ************************************************************
 * version 1.1   Added Cross-tab facility				   *
 *----------------------------------------------------------*
 * version 1.2   Add optional legend to setCondition()	  *
 *			   Added showColNumbers()					 *
 *----------------------------------------------------------*
 * version 1.3   Add  setGPCol() to parse for field value   *
 *			   substitutions for checkboxes etc		   *
 * version 1.3a  Modified setGPCol(col, contents)		   *
 *----------------------------------------------------------*
 * version 1.4   Add setSpan()							  *
 *----------------------------------------------------------*
 * version 1.5   Add MS SQL support						 *
 *----------------------------------------------------------*
 * version 1.6   Add setSubTotalFunction()				  *
 ************************************************************

USAGE SAMPLE

$conn = mysql_connect('host' , 'user' , 'password');
mysql_select_db ('databasename', $conn);

include ('baaGrid.php');

$grid = new baaGrid($sql_query_string);

$grid->setTableAttr('border="0" cellspacing="1" width="600" ');
$grid->setDateFormat('D, jS M');
$grid->setWidth(0,'20%');
$grid->setTotal(2,0);
$grid->setTotal(3,2);
$grid->setOnChange(0, 1, 1);
$grid->setCount(1);
$grid->setCount(0);

$grid->display();
 ************* USER DEFINED CONSTANTS ********************************************************/

error_reporting(0);

define ('BAAGRID_LEGEND_TITLE', 'Key');
define ('BAAGRID_NO_RECORDS', '<p><span style="color:#ff0000;">No matching records were found</span></p>');

/********************************************************************************************/

/* DO NOT CHANGE */
define('DB_MYSQL', 0);
define('DB_POSTGRESQL', 1);
define('DB_ODBC', 2);
define('DB_MSSQL', 3);

$CONFIG = new JConfig();
$conn = mysql_connect($CONFIG->host , $CONFIG->user , $CONFIG->password);
mysql_select_db ($CONFIG->db, $conn);

class baaGrid
{
	var $query;
	var $db;
	var $conn;
	var $cursor;
	var $showErrors = false;
	var $tableAttr = 'border="1"';
	var $dateFormat;
	var $heads;
	var $headClass;
	var $totClass;
	var $subTotClass;
	var $colClass;
	var $rowClass;
	var $fieldCount;
	var $classMode;
	var $attrSet = false;
	var $totals;
	var $wantSubTots = false;
	var $subTotals;
	var $counts;
	var $hasTotClass;
	var $hasSubTotClass;
	var $onChangeCol = -1;
	var $onChangeVal = '';
	var $blankOnChange = 0;
	var $align;
	var $widths;
	var $decpl;
	var $links;
	var $conditions;
	var $datefield;
	var $hiddenColumns;
	var $isXTab;
	var $xRow;
	var $xCol;
	var $xTot;
	var $xAggType;
	var $xTotals;
	var $xColHeads;
	var $showColNos = false;
	var $legend;
	var $hasLegend = false;
	var $GPCols;
	var $spans;
	var $showHeadings = true;
	var $subTotFuncs;

	/******************************************
	 *  PUBLIC FUNCTIONS					   *
	 ******************************************/
	function baaGrid($q, $db = DB_MYSQL, $conn = null)
	{
		$this->query = $q;
		$this->db = $db;
		$this->conn = $conn;
		$this->classMode = '';
		$this->dateFormat = 'd M y';
		$this->heads = array();
		$this->headClass = array();
		$this->totClass = array();
		$this->subTotClass = array();
		$this->colClass = array();
		$this->rowClass = array();
		$this->totals = array();
		$this->subTotals = array();
		$this->counts = array();
		$this->align = array();
		$this->decpl = array();
		$this->datefield = array();
		$this->conditions = array();
		$this->hasTotClass = false;
		$this->hasSubTotClass = false;
		$this->links = array();
		$this->widths = array();
		$this->onChange = array();
		$this->hiddenColumns = array();
		$this->xTotals = array();
		$this->xColHeads = array();
		$this->legend = array();
		$this->GPCols = array();
		$this->spans = array();
		$this->subTotFuncs = array();
		return;
	}

	function noHeadings()
	{
		$this->showHeadings = false;
	}

	function setHeadings($h)
	{
		if (is_array($h)) {
			$this->heads = $h;
		}
		else {
			$this->heads = explode(',', $h);
		}
		for ($i = 0; $i < count($this->heads); $i++) {
			$this->heads[$i] = nl2br($this->heads[$i]);
		}
		return;
	}

	function setHeaderClass($cl)
	{
		if (is_array($cl)) {
			$this->headClass = $cl;
		}
		else {
			$this->headClass = explode(',', $cl);
		}
		if (!$this->attrSet) $this->tableAttr = '';
		return;
	}

	function setTotalClass($cl)
	{
		if (is_array($cl)) {
			$this->totClass = $cl;
		}
		else {
			$this->totClass = explode(',', $cl);
		}
		$this->hasTotClass = true;
		if (!$this->attrSet) $this->tableAttr = '';
		return;
	}

	function setSubTotalClass($cl)
	{
		if (is_array($cl)) {
			$this->subTotClass = $cl;
		}
		else {
			$this->subTotClass = explode(',', $cl);
		}
		$this->hasSubTotClass = true;
		if (!$this->attrSet) $this->tableAttr = '';
		return;
	}

	function setColClass($cl)
	{
		if (is_array($cl)) {
			$this->colClass = $cl;
		}
		else {
			$this->colClass = explode(',', $cl);
		}
		$this->classMode = 'C';
		if (!$this->attrSet) $this->tableAttr = '';
		return;
	}

	function setRowClass($cl)
	{
		if (is_array($cl)) {
			$this->rowClass = $cl;
		}
		else {
			$this->rowClass = explode(',', $cl);
		}
		$this->classMode = 'R';
		if (!$this->attrSet) $this->tableAttr = '';
		return;
	}

	function setTableAttr($attr)
	{
		$this->tableAttr = $attr;
		$this->attrSet = true;
		return;

	}

	function setDateFormat($format)
	{
		$this->dateFormat = $format;
		return;

	}

	function display()
	{
		if ($this->cursor = $this->_query()) {
			$this->_getFieldNames($farray, $fcount);
			$this->fieldCount = $fcount;
			if (isset($this->headClass) && $this->headClass)
				$this->_padWithLast($this->headClass, $fcount);
			if (isset($this->totClass) && $this->totClass)
				$this->_padWithLast($this->totClass, $fcount);
			if (isset($this->subTotClass) && $this->subTotClass)
				$this->_padWithLast($this->subTotClass, $fcount);
			if ($this->classMode == 'C')
				$this->_padWithLast($this->colClass, $fcount);
			$this->_chkHeads($farray, $fcount);
			echo "\n<table $this->tableAttr >\n";

			if ($this->isXTab) {

				$this->_getXData();
				$this->_printXHeadings();
				$this->_printXData();
				$this->_printXTotals();

			}
			else {

				$this->_printHeadings();
				$this->_printData();
				if (count($this->totals) > 0)
					$this->_printTotals();
				if ($this->hasLegend)
					$this->_printLegend();
			}
			echo "\n</table>\n";
		}
		return;
	}

	function printGrid()
	{
		$this->display();
		return;
	}

	function showErrors($b = true)
	{
		$this->showErrors = $b;
		return;

	}

	function setTotal($colno, $dec = -1)
	{
		$this->totals[$colno] = 0;
		$this->setAlign($colno, 'R');
		if ($dec > -1) $this->setDecPlaces($colno, $dec);
		return;

	}

	function setCount($colno)
	{
		$this->counts[$colno] = 0;
		return;

	}

	function setAlign($colno, $aval)
	{
		switch ($aval) {
			case 'c':
			case 'C':
				$this->align[$colno] = 'center';
				break;
			case 'l':
			case 'L':
				$this->align[$colno] = 'left';
				break;
			case 'r':
			case 'R':
				$this->align[$colno] = 'right';
				break;
			default:
				$this->align[$colno] = 'left';
				break;
		}
		return;

	}

	function setDecPlaces($colno, $dec, $align = 'R')
	{
		$this->decpl[$colno] = $dec;
		$this->setAlign($colno, $align);
		return;

	}

	function setCondition($colno, $test, $value, $bgcolor, $fgcolor, $legend = '')
	{
		$count = count($this->conditions[$colno]);
		$this->conditions[$colno][$count]['test'] = $test;
		$this->conditions[$colno][$count]['value'] = $value;
		$this->conditions[$colno][$count]['bgcolor'] = $bgcolor;
		$this->conditions[$colno][$count]['fgcolor'] = $fgcolor;
		if ($legend != '') {
			$l = count($this->legend);
			$this->legend[$l]['text'] = $legend;
			$this->legend[$l]['bg'] = $bgcolor;
			$this->legend[$l]['fg'] = $fgcolor;
			$this->hasLegend = true;
		}
		return;
	}

	function setWidth($colno, $w)
	{
		$this->widths[$colno] = $w;
		return;
	}

	function setOnChange($colno, $subTots = false, $blank = 0)
	{
		$this->onChangeCol = $colno;
		$this->onChangeVal = '';
		$this->wantSubTots = $subTots;
		$this->blankOnChange = $blank;
		return;
	}

	function setLink($colno, $url)
	{
		$this->links[$colno] = $url;
		return;
	}

	function setGPCol($colno, $content = '')
	{
		$this->GPCols[$colno] = $content;
		return;
	}

	function hideColumn($colno)
	{
		$this->hiddenColumns[$colno] = 1;
		return;
	}

	function setXTab($rows, $cols, $totCol, $aggType = 'T')
	{
		$this->isXTab = 1;
		$this->xRow = $rows;
		$this->xCol = $cols;
		$this->xTot = $totCol;
		switch ($aggType) {
			case 'A':
			case 'a':
				$this->xAggType = 'A';
				break;
			case 'C':
			case 'c':
				$this->xAggType = 'C';
				break;
			default:
				$this->xAggType = 'T';
				break;
		}
		return;
	}

	function showColNumbers($show = true)
	{
		$this->showColNos = $show;
		return;

	}

	function setSpan($colno, $newrow, $cspan = 1, $rspan = 1)
	{
		$this->spans[$colno]['row'] = $colno == 0 ? 0 : $newrow;
		$this->spans[$colno]['cspan'] = $cspan;
		$this->spans[$colno]['rspan'] = $rspan;
		return;
	}

	function setSubTotalFunction($colno, $fstr)
	{
		if ($fstr != '') {
			if (strpos($fstr, '#') !== false)
				$this->subTotFuncs[$colno] = $fstr;
		}
		return;
	}

	function debug()
	{
		echo '<pre>';
		print_r($this);
		echo '</pre>';
		return;

	}

	/*****************************************************************************
	 *  PRIVATE FUNCTIONS														 *
	 *****************************************************************************/

	/******************************************
	 *  DATABASE DEPENDENT FUNCTIONS		   *
	 *  the next 3 function are db dependent.  *
	 *  You can add a case for your db and add *
	 *  appropriate code					   *
	 ******************************************/

	function _query()
	{
		$res = @mysql_query($this->query);
		if (!$res) {
			$this->_handleError(mysql_error());
			return false;
		}
		return $res;
	}

	function _fetch_row($rc)
	{
		return mysql_fetch_row($this->cursor);
	}

	function _getFieldNames(&$fa, &$fn)
	{
		$fn = mysql_num_fields($this->cursor);
		for ($i = 0; $i < $fn; $i++) {
			$fa[] = mysql_field_name($this->cursor, $i);
			if (strpos(strtolower(mysql_field_type($this->cursor, $i)), 'date') !== false)
				$this->datefield[$i] = 1;
		}
		return;
	}

	/*** END DB DEPENDENT  *******************************************************************/

	function _getXData()
	{
		$rowcount = 0;
		$rowClassCount = $this->classMode == 'R' ? count($this->rowClass) : 1;
		foreach ($this->totals as $k => $v) $this->totals[$k] = 0;
		foreach ($this->subTotals as $k => $v) $this->subTotals[$k] = 0;
		while ($row = $this->_fetch_row($rowcount)) {
			$this->xTotals[$row[$this->xRow]][$row[$this->xCol]]['sum'] += $row[$this->xTot];
			$this->xTotals[$row[$this->xRow]][$row[$this->xCol]]['count'] += 1;
			$this->xColHeads[$row[$this->xCol]] += 1;
			$rowcount++;
		}
		return;
	}

	function _printXHeadings()
	{
		$numCols = count($this->xColHeads);
		ksort($this->xColHeads);
		echo "<tr>\n<th class='{$this->headClass[0]}' rowspan='2'>{$this->heads[$this->xRow]}</th>
					  <th colspan='$numCols'  class='{$this->headClass[2]}'>{$this->heads[$this->xCol]}</th>
					  <th class='{$this->headClass[0]}' rowspan='2'>Totals</th></tr>\n";
		echo "<tr class='{$this->headClass[0]}'>\n";
		foreach ($this->xColHeads as $title => $v) {
			if (isset($this->datefield[$this->xCol]))
				$title = date($this->dateFormat, strtotime($title));
			else
				$title = stripSlashes($title);
			echo "<th class='{$this->headClass[1]}' >$title</th>\n";
		}
		return;
	}

	function _printXData()
	{
		foreach ($this->xTotals as $rowhead => $rowdata) {
			$rowtotal = array();
			echo "<tr>\n";
			if (isset($this->datefield[$this->xRow]))
				$rowhead = date($this->dateFormat, strtotime($rowhead));
			else
				$rowhead = stripSlashes($rowhead);

			echo "<td class='{$this->colClass[0]}'>$rowhead</td>\n";
			foreach ($this->xColHeads as $colhead => $v) {
				switch ($this->xAggType) {
					case 'T':
						$cell = $rowdata[$colhead]['sum'];
						break;
					case 'C':
						$cell = $rowdata[$colhead]['count'];
						break;
					case 'A':
						$cell = $rowdata[$colhead]['count'] == 0 ? 0 : $rowdata[$colhead]['sum'] / $rowdata[$colhead]['count'];
						break;
				}
				$rowtotal['sum'] += $rowdata[$colhead]['sum'];
				$rowtotal['count'] += $rowdata[$colhead]['count'];
				$this->totals[$colhead]['sum'] += $rowdata[$colhead]['sum'];
				$this->totals[$colhead]['count'] += $rowdata[$colhead]['count'];
				if (isset($this->decpl[$this->xTot])) {
					$cell = $cell == 0 ? '&nbsp;' : number_format($cell, $this->decpl[$this->xTot]);
				}
				echo "<td class='{$this->colClass[1]}' style='text-align:right'>$cell</td>\n";
			}

			switch ($this->xAggType) {
				case 'T':
					$cell = $rowtotal['sum'];
					break;
				case 'C':
					$cell = $rowtotal['count'];
					break;
				case 'A':
					$cell = $rowtotal['count'] == 0 ? 0 : $rowtotal['sum'] / $rowtotal['count'];
					break;
			}
			if (isset($this->decpl[$this->xTot])) {
				$cell = number_format($cell, $this->decpl[$this->xTot]);
			}
			echo "<td class='{$this->colClass[2]}' style='text-align:right'>$cell</td>\n";
			echo "</tr>\n";
		}
		return;
	}

	function _printXTotals()
	{
		$rowtotal = array();
		echo "<tr>\n";
		echo "<th class='{$this->headClass[0]}'>Totals</th>\n";
		foreach ($this->xColHeads as $colhead => $v) {
			switch ($this->xAggType) {
				case 'T':
					$cell = $this->totals[$colhead]['sum'];
					break;
				case 'C':
					$cell = $this->totals[$colhead]['count'];
					break;
				case 'A':
					$cell = $this->totals[$colhead]['count'] == 0 ? 0 : $this->totals[$colhead]['sum'] / $this->totals[$colhead]['count'];
					break;
			}
			$rowtotal['sum'] += $this->totals[$colhead]['sum'];
			$rowtotal['count'] += $this->totals[$colhead]['count'];
			if (isset($this->decpl[$this->xTot])) {
				$cell = number_format($cell, $this->decpl[$this->xTot]);
			}
			echo "<th class='{$this->headClass[1]}' style='text-align:right'>$cell</th>\n";
		}
		switch ($this->xAggType) {
			case 'T':
				$cell = $rowtotal['sum'];
				break;
			case 'C':
				$cell = $rowtotal['count'];
				break;
			case 'A':
				$cell = $rowtotal['count'] == 0 ? 0 : $rowtotal['sum'] / $rowtotal['count'];
				break;
		}
		if (isset($this->decpl[$this->xTot])) {
			$cell = number_format($cell, $this->decpl[$this->xTot]);
		}
		echo "<th class='{$this->headClass[0]}' style='text-align:right'>$cell</th>\n";

		echo "</tr>\n";
		return;
	}

	function _printHeadings()
	{
		if (!$this->showHeadings) return;
		echo "\n<tr>\n";
		for ($c = 0; $c < $this->fieldCount; $c++) {
			if (isset($this->hiddenColumns[$c])) continue;

			$stxt = '';
			if (isset($this->spans[$c])) {
				if ($this->spans[$c]['row'] != 0) {
					echo "</tr>\n";
					echo "<tr>\n";
				}
				if ($this->spans[$c]['cspan'] > 1) {
					$stxt .= " colspan=\"" . $this->spans[$c]['cspan'] . "\" ";
				}
				if ($this->spans[$c]['rspan'] > 1) {
					$stxt .= " rowspan=\"" . $this->spans[$c]['rspan'] . "\" ";
				}
			}

			echo "<th";
			if (isset($this->headClass[$c])) echo " class=\"{$this->headClass[$c]}\"";
			echo "$stxt>";
			if ($this->showColNos) echo "$c<br>";
			echo "{$this->heads[$c]}</th>\n";
		}
		echo "\n</tr>\n";

		return;

	}

	function _printData()
	{
		$rowcount = 0;
		$rowClassCount = $this->classMode == 'R' ? count($this->rowClass) : 1;
		foreach ($this->totals as $k => $v) $this->totals[$k] = 0;
		foreach ($this->subTotals as $k => $v) $this->subTotals[$k] = 0;
		while ($row = $this->_fetch_row($rowcount)) {

			if ($this->onChangeCol != -1) {
				if (($row[$this->onChangeCol] != $this->onChangeVal) && ($this->onChangeVal != '')) {
					if ($this->wantSubTots) {
						$this->_printSubTotals();
					}
					if ($this->blankOnChange > 0) {
						echo "<tr><td>&nbsp;</td></tr>\n";
					}
					if ($this->blankOnChange == 2) {
						$this->_printHeadings();
					}
				}
			}
			if ($this->classMode == 'R') {
				echo '<tr class="' . $this->rowClass[$rowcount % $rowClassCount] . '">';
			}
			else echo "<tr>\n";

			for ($i = 0; $i < $this->fieldCount; $i++) {
				if (isset($this->hiddenColumns[$i])) continue;
				$atxt = array();
				$stxt = '';
				if (isset($this->align[$i])) {
					$atxt[] = "text-align: {$this->align[$i]}";
				}
				if (isset($this->conditions[$i])) {
					if (($t = $this->_conditionTrue($row, $i)) != -1) {
						if ($this->conditions[$i][$t]['bgcolor'] != '')
							$atxt[] = "background:{$this->conditions[$i][$t]['bgcolor']}";
						if ($this->conditions[$i][$t]['fgcolor'] != '')
							$atxt[] = "color:{$this->conditions[$i][$t]['fgcolor']}";
					}
				}
				if (isset($this->widths[$i])) {
					$atxt[] = strpos($this->widths[$i], '%') ? "width : {$this->widths[$i]}" : "width : {$this->widths[$i]}px";
				}
				if (count($atxt) > 0) {
					$stxt = "style = \"" . join('; ', $atxt) . "\" ";
				}

				if (isset($this->spans[$i])) {
					if ($this->spans[$i]['row'] != 0) {
						echo "</tr>\n";
						if ($this->classMode == 'R') {
							echo '<tr class="' . $this->rowClass[$rowcount % $rowClassCount] . '">';
						}
						else echo "<tr>\n";
					}
					if ($this->spans[$i]['cspan'] > 1) {
						$stxt .= " colspan=\"" . $this->spans[$i]['cspan'] . "\" ";
					}
					if ($this->spans[$i]['rspan'] > 1) {
						$stxt .= " rowspan=\"" . $this->spans[$i]['rspan'] . "\" ";
					}
				}


				if ($this->classMode == 'C') {
					echo '<td class="' . $this->colClass[$i] . '" ' . $stxt . '>';
				}
				else echo "<td $stxt>";
				$rowInc = 1;
				if (isset($row[$i])) {
					if (isset($this->datefield[$i])) {
						$cell = date($this->dateFormat, strtotime($row[$i]));
					}
					elseif (isset($this->decpl[$i]) && is_numeric($row[$i]))
						$cell = number_format($row[$i], $this->decpl[$i]);
					else
						$cell = stripslashes($row[$i]);

					if ($this->onChangeCol == $i) {
						$rowInc = 0;
						if ($row[$i] != $this->onChangeVal) {
							echo $cell;
							$rowInc = 1;
						}
						else echo "&nbsp;";
						$this->onChangeVal = $row[$i];
					}
					elseif (isset($this->links[$i])) {
						echo  $this->_addHref($cell, $i, $row);
					}
					elseif (isset($this->GPCols[$i])) {
						echo  $this->_parseFields($cell, $row, $i);
					}
					else echo $cell;

				} else echo '&nbsp;';

				echo "</td>\n";
				if (isset($this->counts[$i])) {
					$this->subTotals[$i] += $rowInc;
					$this->totals[$i] += $rowInc;
				}
				elseif (isset($this->totals[$i])) {
					$this->subTotals[$i] += $row[$i];
					$this->totals[$i] += $row[$i];
				}
			}
			echo "</tr>\n";
			$rowcount++;
		}
		if ($rowcount == 0) {
			//echo "</table><p>" . BAAGRID_NO_RECORDS . "</p>";
			echo "<p>" . BAAGRID_NO_RECORDS . "</p>";
			return;
		}
		if ($this->wantSubTots) {
			$this->_printSubTotals();
		}
		if ($this->blankOnChange) {
			echo "<tr><td>&nbsp;</td></tr>\n";
		}
		return;
	}

	function _printSubTotals()
	{
		$theClass = ($this->hasSubTotClass) ? $this->subTotClass :
			($this->hasTotClass ? $this->totClass : $this->headClass);
		echo "\n<tr>\n";
		for ($c = 0; $c < $this->fieldCount; $c++) {
			if (isset($this->hiddenColumns[$c])) continue;
			$atxt = '';
			if ($this->align[$c]) {
				$atxt = " style=\"text-align: {$this->align[$c]}\" ";
			}

			if (isset($this->spans[$c])) {
				if ($this->spans[$c]['row'] != 0) {
					echo "</tr>\n";
					echo "<tr>\n";
				}
				if ($this->spans[$c]['cspan'] > 1) {
					$atxt .= " colspan=\"" . $this->spans[$c]['cspan'] . "\" ";
				}
				if ($this->spans[$c]['rspan'] > 1) {
					$atxt .= " rowspan=\"" . $this->spans[$c]['rspan'] . "\" ";
				}
			}

			echo "<th";
			if ($theClass[$c]) echo " class=\"{$theClass[$c]}\"";
			echo "$atxt >";
			if ($c == $this->onChangeCol) unset($this->subTotals[$c]);
			if (isset($this->subTotals[$c])) {
				$subTotalValue = $this->subTotals[$c];
				if (isset($this->subTotFuncs[$c])) {
					$theFunc = "return(" . str_replace("#", "$subTotalValue", $this->subTotFuncs[$c]) . ");";
					$subTotalValue = eval($theFunc);
					echo $subTotalValue;
				}
				else {
					if (isset($this->decpl[$c]))
						echo number_format($subTotalValue, $this->decpl[$c]);
					else
						echo $subTotalValue;
				}
			}
			else
				echo '&nbsp;';
			echo "</th>\n";
		}
		echo "\n</tr>\n";
		foreach ($this->subTotals as $k => $v) $this->subTotals[$k] = 0;
		return;
	}

	function _printTotals()
	{
		$theClass = ($this->hasTotClass) ? $this->totClass : $this->headClass;
		echo "\n<tr>\n";
		for ($c = 0; $c < $this->fieldCount; $c++) {
			if (isset($this->hiddenColumns[$c])) continue;
			$atxt = '';
			if ($this->align[$c]) {
				$atxt = " style=\"text-align: {$this->align[$c]}\" ";
			}

			if (isset($this->spans[$c])) {
				if ($this->spans[$c]['row'] != 0) {
					echo "</tr>\n";
					echo "<tr>\n";
				}
				if ($this->spans[$c]['cspan'] > 1) {
					$atxt .= " colspan=\"" . $this->spans[$c]['cspan'] . "\" ";
				}
				if ($this->spans[$c]['rspan'] > 1) {
					$atxt .= " rowspan=\"" . $this->spans[$c]['rspan'] . "\" ";
				}
			}

			echo "<th";
			if ($theClass[$c]) echo " class=\"{$theClass[$c]}\"";
			echo "$atxt >";
			if ($this->totals[$c]) {
				if (isset($this->decpl[$c]))
					echo number_format($this->totals[$c], $this->decpl[$c]);
				else
					echo $this->totals[$c];
			}
			else
				echo '&nbsp;';
			echo "</th>\n";
		}
		echo "\n</tr>\n";
		return;
	}

	function _printLegend()
	{
		echo "<TABLE $this->tableAttr >\n";
		echo "<TR><td colspan=\"3\">" . BAAGRID_LEGEND_TITLE . "</td></TR>\n";
		foreach ($this->legend as $explan) {
			printf("<TR>\n<TD width=\"40\" STYLE=\"font-size:10pt; background: %s; color: %s\">Abc123</td><TD width=\"10\">&nbsp;</td><TD>%s</td>\n</TR>\n", $explan['bg'], $explan['fg'], $explan['text']);
		}
		echo "</table>\n";
		return;
	}

	function _handleError($e)
	{
		if ($this->showErrors)
			echo "<p>$e</p>";
		return;
	}

	function _padWithLast(&$a, $n)
	{
		$val = $a[count($a) - 1];
		$a = array_pad($a, $n, $val);
		return;
	}

	function _chkHeads($farray, $fcount)
	{
		if ($fcount > count($this->heads)) {
			for ($i = count($this->heads); $i < $fcount; $i++) {
				$this->heads[$i] = $farray[$i];
			}
		}
		return;
	}

	function _conditionTrue($row, $i)
	{
		$field = $row[$i];
		foreach ($this->conditions[$i] as $key => $testdata) {
			$test = $testdata['test'];
			$val = $testdata['value'];
			if (!$field || !$test || !$val)
				$res = -1;
			else {
				switch ($test) {
					case '#' :
						for ($f = count($row) - 1; $f >= 0; $f--) {
							$val = str_replace("#$f", $row[$f], $val);
						}
						$str = "return ($val);";
						break;
					case '?' :
						$str = "return(strpos('$field', '$val') !== false); ";
						break;
					case '!' :
						$str = "return(strpos('$field', '$val') === false); ";
						break;
					default :
						if (is_numeric($field))
							$str = "return($field $test $val); ";
						else
							$str = "return('$field' $test '$val'); ";
				}
				if (eval($str)) {
					return $key;
				}
			}
		}
		return -1;
	}

	function _addHref($cell, $col, $row)
	{

		$href = $this->links[$col];
		for ($f = count($row) - 1; $f >= 0; $f--) {
			$href = str_replace("#$f", $row[$f], $href);
		}

		return "<a href=\"$href\" >$cell</a>";
	}

	function _parseFields($cell, $row, $col)
	{
		$cell = $this->GPCols[$col] == '' ? $cell : $this->GPCols[$col];
		for ($f = count($row) - 1; $f >= 0; $f--) {
			$cell = str_replace("#$f", $row[$f], $cell);
		}
		return $cell;
	}
} // end class baaGrid definition

?>