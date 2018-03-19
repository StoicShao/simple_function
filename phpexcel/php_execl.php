<?php
/**
 *
 * 从数据库中将数据导出为excel 表格
 * 数据库字段注释为excel 表格的列标题
 *
 * 手动设置导出的excel 每张表格数据的条数
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/23
 * Time: 14:44
 */
//echo 123;die;
//require_once '../core/init.php';

//require_once '../phpexcel/PHPExcel.php';
//require_once '../phpexcel/PHPExcel/IOFactory.php';
//require_once '../phpexcel/PHPExcel/Reader/Excel5.php';
//require_once '../phpexcel/PHPExcel/Reader/Excel2007.php';
include("./phpexcel/PHPExcel.php");
include("./phpexcel/PHPExcel/IOFactory.php");
include("./phpexcel/PHPExcel/Reader/Excel5.php");
include("./phpexcel/PHPExcel/Reader/Excel2007.php");
error_reporting(0);
header("Content-Type: text/html; charset=utf-8");                       //设置页面输出字符编码格式
$dbname	= 'cssci_new_chouqu';                                                      //数据库名
//$step = 1000;                                                          //设置一张表的数据条数

$datapath = 'D:\\phpstudy\\WWW\\cssci_new_exec\\school_chouqu_auto_execl\\';   //数据保存路径
$this->delFile($datapath,'');

$link = mysql_connect("localhost", "root", "root")      or die("Could not connect");
mysql_select_db($dbname) or die("Could not select database");
mysql_query("set names utf8;");

//库中总共有多少表需要导成execl
$table_num = mysql_query("SELECT COUNT(*) TABLES, table_schema FROM information_schema.TABLES  WHERE table_schema = '".$dbname."' ");
$table_num = mysql_fetch_assoc($table_num);
$school_chouqu_num = trim($table_num[TABLES]);

for($ii=0;$ii<$school_chouqu_num;$ii++){
	$tbname	= "cssci_new_chouqu.cssci_chouqu_".$ii;
	                                 
	mkdir($datapath.'cssci_chouqu_'.$ii);
	$lastid	= $this->getLastId($tbname);
	
	$step	= 1000;
	$sql_c = sprintf('SELECT COUNT(*) FROM %s', $tbname);
	$res_c = mysql_query($sql_c);
	$tmp_num = mysql_fetch_array($res_c);
	$num = intval($tmp_num[0]);
	if($num > 0){	
		//print_r($num);die;
		$piece = ceil($num/$step);                                              //表总张数
		for($i = 1; $i<= $piece; $i ++)
		{
			$start	= ($i-1)*$step;                                           //注意：limit 是从0 开始计数, $step为步长
			$sql = sprintf('SELECT * FROM %s LIMIT %s , %s', $tbname, $start, $step);
			$res = mysql_query($sql);
			$data = array();
			while($row = mysql_fetch_assoc($res))                                //数据库中获取
			{
				$data[] = $row;
			}
			$headerRow = array();
			$rescolumns = mysql_query("SHOW FULL COLUMNS FROM ".$tbname) ;       //获取表字段的注释信息
			while($row_info = mysql_fetch_assoc($rescolumns))
			{
				$headerRow[] = $row_info;
			}
			$columns_xls = count($headerRow);                                    // 数据表字段数 == excel 表中的列数
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
				->setLastModifiedBy("Maarten Balliauw")
				->setTitle("Office 2005 XLSX Test Document")
				->setSubject("Office 2005 XLSX Test Document")
				->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
				->setKeywords("office 2005 openxml php")
				->setCategory("Test result file");
			//print_r($headerRow);die;
			//echo $columns_xls;die;
		/*	for($ac=0; $ac<=$columns_xls; $ac++)                                //将数据库表中每一个字段的注释作为excel表的每一列的标题
			{
				$j=chr(65+$ac);                                                 //assic 码转字母
				$objPHPExcel->getActiveSheet()->setCellValue($j.'1' ,$headerRow[$ac]['Comment']);
			}
			for($ac=0; $ac<=$columns_xls; $ac++){                                //将数据库中的每一列的值插入数据表
				$j=chr(65+$ac);
				foreach($data as $key=> $value) {
					$key=$key + 2;
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($j.$key, $value[$headerRow[$ac]['Field']]);
				}
			}*/
			$content = $data;
			$maxColumn = count($content[0]);
			$maxRow    = count($content);
			for ($iii = 0; $iii < $maxColumn; $iii++) {
				for ($j = 0; $j < $maxRow; $j++) {
					$pCoordinate = $this->stringFromColumnIndex($iii) . '' . ($j + 1);
					$pValue      = $content[$j][$iii];
					$objPHPExcel->getActiveSheet()->setCellValue($pCoordinate, $pValue);
				}
			}

			for($iii=0; $iii<=$maxColumn; $iii++){                                //将数据库中的每一列的值插入数据表
				for ($j = 0; $j < $maxRow; $j++) {
					$pCoordinate = $this->stringFromColumnIndex($iii) . '' . ($j + 1);
					$pValue      = $content[$j][$headerRow[$iii]['Field']];
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($pCoordinate, $pValue);
				}
			}
			// 设置活动单指数到第一个表,所以Excel打开这是第一个表
			$objPHPExcel->setActiveSheetIndex(0);
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			
			$objWriter->save($datapath.'cssci_chouqu_'.$ii.'\\'.($start+1).'--'.$step*$i.'.xls');flush();            //文件生成在 $datapath 目录下
			echo '==========第'.$i.'张表=======</br>';flush();
		}
		echo '==========一共'.$piece.'张表导出完毕!!!=======</br>';flush();
	}else{
		$objWriter->save($datapath.'cssci_chouqu_'.$ii.'\\nothing.xls');flush();
		echo '==========此张表中没有数据!!!=======</br>';flush();
	}

}





