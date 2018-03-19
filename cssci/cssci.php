<?php

set_time_limit(0);
ob_end_clean();
//error_reporting("all~notice");
error_reporting(7);
date_default_timezone_set('PRC'); 
class xxtt_book_exec
{
	function vcurl($url, $post = '', $cookie = '', $cookiejar = '', $referer = '',$stime='80',$localhost='0',$header='')
	{
		$tmpInfo = '';
		$cookiepath = getcwd().'./'.$cookiejar;
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		switch($localhost)
			{
				case "3":
					//echo 'ffffffffffffff';
						curl_setopt($curl, CURLOPT_PROXY, '210.34.4.59:808');   //厦大
						curl_setopt($curl, CURLOPT_PROXYUSERPWD, 'mylibrary:mylibrary');  
					break; 
				default :
					$a='njnu';break;
			}
		curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		if($referer) {
		curl_setopt($curl, CURLOPT_REFERER, $referer);
		} else {
		curl_setopt($curl, CURLOPT_AUTOREFERER, 1);	
		}
		if($post) {
		curl_setopt($curl, CURLOPT_POST, 1); 
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
		}
		if($cookie) {
		curl_setopt($curl, CURLOPT_COOKIE, $cookie);
		}
		if($cookiejar) {
		curl_setopt($curl, CURLOPT_COOKIEJAR, $cookiepath);
		curl_setopt($curl, CURLOPT_COOKIEFILE, $cookiepath);
		//curl_setopt($curl, CURLOPT_COOKIE, $cookie);
		}
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_TIMEOUT, $stime);

		if($header!='')
		{
			 curl_setopt($curl, CURLOPT_HEADER, 0);
		}
		else
		{
			 curl_setopt($curl, CURLOPT_HEADER, 1);
		}
	   
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect:'));
		$tmpInfo = curl_exec($curl);
		$errno = curl_errno($curl);
		curl_close($curl);
		
		
		return $tmpInfo;
	}

	function chulistring($string)
	{
		$string	= trim(strip_tags($string));
		return addslashes($string);
	}

	function getLastId($tbname)
	{
		$sql	= "select id from ".$tbname." order by id DESC";
		$result	= mysql_query($sql);
		$rel	= mysql_fetch_assoc($result);
		return $rel['id'];
	}

	function __construct()
	{
		mysql_connect('localhost','root','root');
		mysql_select_db('cssci_new');
		mysql_query("set names utf8");
	}

	function getSession()
	{
		$tmp_url	= 'http://cssci.nju.edu.cn/control/controllers.php';
		$tmp_param	= 'control=user_control&action=check_user_online&rand=0.15508920689082006';
		$icnt	= $this->vcurl($tmp_url,$tmp_param);
		preg_match_all('/Set-Cookie:(.*)path=/siU',$icnt,$arr);
		return trim($arr[1][0]);
	}
	
	//抓取
	function step_1()
	{
		//441
		echo '<meta http-equiv="content-type" content="text/html;charset=utf-8">';

		$pagesize	= 50;

		$school	= trim($_REQUEST['school']);
		preg_match_all('/(.*)\|/siU',$school,$search_school);
		$result = $search_school[1];

		$start = trim($_REQUEST['start_year']);
		$end   = trim($_REQUEST['end_year']);
		
		if($_REQUEST['type'] == 1){
			$bu_zhua = mysql_query("SELECT * FROM school_zhuaqu_bu_zhua ORDER BY id DESC");
			if($bu_zhua){
				$school_bu_zhua = mysql_fetch_assoc($bu_zhua);
				$start = trim($school_bu_zhua['start']);
				$end   = trim($school_bu_zhua['end']);
				$school_bu_zhua	= trim($school_bu_zhua['school_name']);
				preg_match_all('/(.*)\|/siU',$school_bu_zhua,$search_school);
				$result = $search_school[1];
				
			}
		}
		
		for($jj=$start;$jj<=$end;$jj++){
			
			//if($start == $end){
			//	$start_year	= $jj;
			//	$end_year	= $jj;
			//}else{
			//	$start_year	= $jj;
			//	$end_year	= $jj+1;
			//}
			$start_year	= $jj;
			$end_year	= $jj;
			$cookiValue	= $this->getSession();
			
			foreach($result as $key=>$value){		
				$school_name= $value;
				$tmp_url	= 'http://cssci.nju.edu.cn/control/controllers.php?control=search_base&action=search_lysy&title='.$school_name.'%252B%252B%252B10%252B%252B%252BAND%257C%257C%257C&xkfl1=&wzlx=&qkname=&jj=&start_year='.$start_year.'&end_year='.$end_year.'&nian=&juan=&qi=&xw1=&xw2=&pagesize='.$pagesize.'&pagenow=1&order_type=nian&order_px=DESC&search_tag=0&session_key=494&rand=0.668935300106181';
				$content	= $this->vcurl($tmp_url,'',$cookiValue,'','','','','fdsf');
				if(preg_match('/{"contents":null,"totalnum":null,"pagenum":null,"pagenow":null,"state":"wrong_year"}/siU',$content))
				{
					$cookiValue	= $this->getSession();	
					$content	= $this->vcurl($tmp_url,'',$cookiValue,'','','','','fdsf');
					
				}
				
				preg_match_all('/"totalfound":"(.*)"/siU',$content,$arr);
				$tnum	= trim($arr[1][0]);
				$tnum	= str_replace(',','',$tnum);
				$xun	= ceil($tnum/$pagesize);
				$add_time= date('Y-m-d H:i:s',time());
				//$sqluu	= "update school_zhuaqu set ,type='".$tnum."', where id ='".$row['id']."'";
				$sqluu	= "INSERT INTO school_zhuaqu (`school_name`,`type`,`start_year`,`end_year`,`add_time`) VALUES ('".$value."','".$tnum."','".$start_year."','".$end_year."','".$add_time."')";
				mysql_query($sqluu);//exit;
				
				for($i=1;$i<=$xun;$i++)
				{
					sleep(2);
					if($i==1)
					{
						$content	= $content;
					}
					else
					{
						$tmp_url	= 'http://cssci.nju.edu.cn/control/controllers.php?control=search_base&action=search_lysy&title='.$school_name.'%252B%252B%252B10%252B%252B%252BAND%257C%257C%257C&xkfl1=&wzlx=&qkname=&jj=&start_year='.$start_year.'&end_year='.$end_year.'&nian=&juan=&qi=&xw1=&xw2=&pagesize='.$pagesize.'&pagenow='.$i.'&order_type=nian&order_px=DESC&search_tag=0&session_key=494&rand=0.668935300106181';
						$content	= $this->vcurl($tmp_url,'',$cookiValue,'','','','','fdsf');
						if(preg_match('/{"contents":null,"totalnum":null,"pagenum":null,"pagenow":null,"state":"wrong_year"}/siU',$content))
						{
							$cookiValue	= $this->getSession();	
							$content	= $this->vcurl($tmp_url,'',$cookiValue,'','','','','fdsf');
							
						}
					}
					preg_match_all('/"id":".*","sno":".*",/siU',$content,$arr10);
					for($j=0;$j<count($arr10[0]);$j++)
					{

						$inArr	= array();
						preg_match_all('/"id":"(.*)"/siU',$arr10[0][$j],$arr11);
						$inArr['cid']	= $this->chulistring($arr11[1][0]);
						preg_match_all('/"sno":"(.*)"/siU',$arr10[0][$j],$arr11);
						$inArr['sno']	= $this->chulistring($arr11[1][0]);
						sleep(1);
						$durl	= 'http://cssci.nju.edu.cn/control/controllers.php?control=search&action=source_id&id='.$inArr['sno'].'&rand=0.9888618150235109';
						$content	= $this->vcurl($durl,'','','','','','','dfdsafsd');
						if(preg_match('/{"contents":null,"totalnum":null,"pagenum":null,"pagenow":null,"state":"wrong_year"}/siU',$content))
						{
							$cookiValue	= $this->getSession();	
							$content	= $this->vcurl($durl,'','','','','','','dfdsafsd');
						}
						$inArr['content']	= addslashes($content);
						$inArr['schoolname']= $value;
						$inArr['add_time']= date('Y-m-d H:i:s',time());

						$sqlii = sprintf('INSERT INTO `%s` (`%s`) VALUES ("%s")',"school_zhuaqu_auto",implode('`,`',array_keys($inArr)),implode('","',array_values($inArr)));
						mysql_query($sqlii);

						echo ">>>>".$value.">>>>".$i.">>>>".($j+1)."<br>";flush();
					}
				}	
				
			}
		}
		
		echo ">>>>>抓取完成"."<br>";flush();	
		//exit;
	}
	
	//解析
	function step_2(){
		//1153983
		$res = mysql_query('SELECT * FROM school_zhuaqu_num ORDER BY id DESC');
		if($res){
			$num	   = mysql_fetch_assoc($res);
			if(!$num[school_num]){
				$num[school_num] = 0;
			}
			$start_num = $num[school_num]+1;
			$seTB	= "`cssci_new`.`school_zhuaqu_auto`";
			$lastid	= $this->getLastId($seTB);
			$step	= 10000;
			//echo 123;die;
			for($z=0;$z<=$lastid;$z+=$step){
				$sql1	= "SELECT * FROM ".$seTB." WHERE id between ".($z+$start_num)." and ".($z+$step);
				$res1	= mysql_query($sql1);
				while($value	= mysql_fetch_assoc($res1)){
					$data		  =	 array();
					$data['sno']  =  $value[sno];
					$data['search_school']  =  $value[schoolname];
					$content	= str_replace('\u','%u',$value[content]);
					$content	= $this->unicodeToUtf8($content);
					//print_r($content);die;
					preg_match_all('/"lypm":"(.*)"/siU',$content,$title);
					$data['title']  =  trim(addslashes($title[1][0]));

					preg_match_all('/"lypmp":"(.*)"/siU',$content,$lypmp);
					$data['lypmp']  =  trim(addslashes($lypmp[1][0]));

					preg_match_all('/"blpm":"(.*)"/siU',$content,$blpm);
					$data['blpm']   =  trim(addslashes($blpm[1][0]));

					preg_match_all('/"authors":"(.*)"/siU',$content,$authors);
					$data['authors']=  trim(addslashes($authors[1][0]));

					preg_match_all('/"authors_jg":"(.*)"/siU',$content,$authors_jg);
					$data['authors_jg']  =  trim(addslashes($authors_jg[1][0]));

					preg_match_all('/"authors_address":"(.*)"/siU',$content,$authors_address);
					$data['authors_address']  =  trim(addslashes($authors_address[1][0]));

					preg_match_all('/"wzlx":"(.*)"/siU',$content,$wzlx);
					$data['wzlx']  =  trim(addslashes($wzlx[1][0]));

					if($data['wzlx'] == 1){
						$data['wzlx_wenxian'] = '论文';
					}elseif($data['wzlx'] == 2){
						$data['wzlx_wenxian'] = '综述';
					}elseif($data['wzlx'] == 3){
						$data['wzlx_wenxian'] = '评论';
					}elseif($data['wzlx'] == 4){
						$data['wzlx_wenxian'] = '传记资料';
					}elseif($data['wzlx'] == 5){
						$data['wzlx_wenxian'] = '报告';
					}elseif($data['wzlx'] == 9){
						$data['wzlx_wenxian'] = '其他';
					}

					preg_match_all('/"qkno":"(.*)"/siU',$content,$qkno);
					$data['qkno']  =  trim(addslashes($qkno[1][0]));

					preg_match_all('/"xkdm1":"(.*)"/siU',$content,$xkdm1);
					$data['xkdm1']  =  trim(addslashes($xkdm1[1][0]));

					preg_match_all('/"xkdm2":"(.*)"/siU',$content,$xkdm2);
					$data['xkdm2']  =  trim(addslashes($xkdm2[1][0]));

					preg_match_all('/"ym":"(.*)"/siU',$content,$ym);
					$data['ym']  =  trim(addslashes($ym[1][0]));

					preg_match_all('/"ywsl":"(.*)"/siU',$content,$ywsl);
					$data['ywsl']  =  trim(addslashes($ywsl[1][0]));

					preg_match_all('/"byc":"(.*)"/siU',$content,$byc);
					$data['byc']  =  trim(addslashes($byc[1][0]));

					preg_match_all('/"dcbj":"(.*)"/siU',$content,$dcbj);
					$data['dcbj']  =  trim(addslashes($dcbj[1][0]));

					preg_match_all('/"xmlb":"(.*)"/siU',$content,$xmlb);
					$data['xmlb']  =  trim(addslashes($xmlb[1][0]));

					preg_match_all('/"jjlb":"(.*)"/siU',$content,$jjlb);
					$data['jjlb']  =  trim(addslashes($jjlb[1][0]));

					preg_match_all('/"lrymc":"(.*)"/siU',$content,$lrymc);
					$data['lrymc']  =  trim(addslashes($lrymc[1][0]));

					preg_match_all('/"skdm":"(.*)"/siU',$content,$skdm);
					$data['skdm']  =  trim(addslashes($skdm[1][0]));

					preg_match_all('/"yjdm":"(.*)"/siU',$content,$yjdm);
					$data['yjdm']  =  trim(addslashes($yjdm[1][0]));

					preg_match_all('/"xkfl1":"(.*)"/siU',$content,$xkfl1);
					$data['xkfl1']  =  trim(addslashes($xkfl1[1][0]));
					$res2 = mysql_query("SELECT `bz` FROM zd_xkfl1 WHERE title=".$data['xkfl1']."");
					if($res2){
						$result2  =  mysql_fetch_assoc($res2);
						$data['xkfl1_xueke'] = trim($result2['bz']);
					}

					preg_match_all('/"ycflag":"(.*)"/siU',$content,$ycflag);
					$data['ycflag']  =  trim(addslashes($ycflag[1][0]));

					preg_match_all('/"xkfl2":"(.*)"/siU',$content,$xkfl2);
					$data['xkfl2']  =  trim(addslashes($xkfl2[1][0]));

					preg_match_all('/"qkdm":"(.*)"/siU',$content,$qkdm);
					$data['qkdm']  =  trim(addslashes($qkdm[1][0]));

					preg_match_all('/"nian":"(.*)"/siU',$content,$nian);
					$data['nian']  =  trim(addslashes($nian[1][0]));

					preg_match_all('/"juan":"(.*)"/siU',$content,$juan);
					$data['juan']  =  trim(addslashes($juan[1][0]));

					preg_match_all('/"qi":"(.*)"/siU',$content,$qi);
					$data['qi']  =  trim(addslashes($qi[1][0]));

					preg_match_all('/"qkmc":"(.*)"/siU',$content,$qkmc);
					$data['qkmc']  =  trim(addslashes($qkmc[1][0]));
					
					preg_match_all('/"wzlx_z":"(.*)"/siU',$content,$wzlx_z);
					$data['wzlx_z']  =  trim(addslashes($wzlx_z[1][0]));

					preg_match_all('/aaa(.*)aaa/siU',$data[authors],$first_author);
					$data[first_author]		= addslashes($first_author[1][0]);

					preg_match_all('/aaa(.*)aaa/siU',$data[authors_jg],$first_jg);
					$data[first_jg]			= addslashes($first_jg[1][0]);
					
					$data[authors]	= str_replace('aaa','@@@',$data[authors]);
					$data[authors1] =  trim($data[authors],'@@@');
					$data[authors1] =  addslashes(str_replace('@@@','/',$data[authors1]));

					$data[authors_jg]		= str_replace('aaa','@@@',$data[authors_jg]);
					$data[authors_address]	= str_replace('aaa','@@@',$data[authors_address]);
					$data[byc]				= str_replace('aaa','@@@',$data[byc]);

					preg_match_all('/"author":.*]/siU',$content,$message);
					preg_match_all('/"jgmc":"(.*)",/siU',$content,$jgmc);
					preg_match_all('/"bmmc":"(.*)",/siU',$content,$bmmc);
					preg_match_all('/"zzmc":"(.*)",/siU',$content,$zzmc);
					$message = '';
					$message1= '';
					for($i=0;$i<count($jgmc[1]);$i++){
						$people[$i]  = trim(addslashes($jgmc[1][$i]))."-".trim(addslashes($bmmc[1][$i]))."-".trim(addslashes($zzmc[1][$i]));
						$message	.=  $people[$i].";";
						
						if($bmmc[1][$i]){
							$people1[$i] = '['.trim(addslashes($zzmc[1][$i])).']'.trim(addslashes($jgmc[1][$i])).'.'.trim(addslashes($bmmc[1][$i]));
						}else{
							$people1[$i] = '['.trim(addslashes($zzmc[1][$i])).']'.trim(addslashes($jgmc[1][$i]));
						}
						
						$message1	.=  $people1[$i]."/";

					}
					$data['message']  =  $message;
					$data['message1'] =  addslashes(rtrim($message1,'/'));

					//print_r($data);die;
					$sqlii = sprintf('INSERT INTO `%s` (`%s`) VALUES ("%s")',"select_school",implode('`,`',array_keys($data)),implode('","',array_values($data)));
					mysql_query($sqlii);
					echo ">>>>".$value[id].">>>>".$value[sno]."<br>";flush();
				
				
				}
			}

		}
		$add_time= date('Y-m-d H:i:s',time());
		$num	 = mysql_fetch_assoc(mysql_query("SELECT MAX(id) as num from school_zhuaqu_auto"));
		$sql_num = "INSERT INTO school_zhuaqu_num (`school_num`,`add_time`) VALUES ('".$num[num]."','".$add_time."')";
		mysql_query($sql_num);
		echo ">>>>>解析完成"."<br>";flush();
	
	}
	
	//抽取
	function step_3(){
		mysql_query("DROP DATABASE `cssci_new_chouqu`");
		mysql_query("CREATE DATABASE IF NOT EXISTS `cssci_new_chouqu`");
		$school_chouqu	= trim($_REQUEST['school_chouqu']);
		preg_match_all('/(.*)\|/siU',$school_chouqu,$search_school);
		$school_chouqu = $search_school[1];

		$start = trim($_REQUEST['start_year_chouqu']);
		$end   = trim($_REQUEST['end_year_chouqu']);

		$seTB	= "`cssci_new`.`select_school`";
		$lastid	= $this->getLastId($seTB);
		$step	= 10000;

		$seTB1	= "`cssci_new`.`lysy`";
		$lastid1= $this->getLastId($seTB1);

		foreach($school_chouqu as $key=>$value){
				$sqll = "CREATE TABLE IF NOT EXISTS `cssci_new_chouqu`.`cssci_chouqu_".$key."` (
				  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
				  `sno` char(16) NOT NULL,
				  `title` varchar(500) NOT NULL,
				  `lypmp` char(255) NOT NULL COMMENT '篇名的拼音',
				  `blpm` varchar(500) NOT NULL,
				  `authors` text NOT NULL COMMENT '合并的作者',
				  `authors1` text NOT NULL COMMENT '作者',
				  `first_author` varchar(200) NOT NULL,
				  `authors_jg` text NOT NULL COMMENT '合并的作者机构',
				  `first_jg` varchar(200) NOT NULL,
				  `authors_address` text NOT NULL COMMENT '合并的作者地区',
				  `wzlx` int(2) NOT NULL COMMENT '文章类型',
				  `wzlx_wenxian` varchar(200) NOT NULL,
				  `qkno` char(13) NOT NULL,
				  `xkdm1` char(15) NOT NULL,
				  `xkdm2` char(15) NOT NULL,
				  `ym` char(40) NOT NULL,
				  `ywsl` int(3) NOT NULL,
				  `byc` char(255) NOT NULL,
				  `dcbj` char(1) NOT NULL,
				  `xmlb` varchar(500) NOT NULL,
				  `jjlb` char(40) NOT NULL,
				  `lrymc` char(8) NOT NULL,
				  `skdm` char(3) NOT NULL,
				  `yjdm` char(6) NOT NULL,
				  `xkfl1` char(6) NOT NULL,
				  `xkfl1_xueke` varchar(200) NOT NULL,
				  `ycflag` int(1) NOT NULL,
				  `xkfl2` char(6) NOT NULL,
				  `qkdm` char(6) NOT NULL,
				  `nian` int(4) NOT NULL,
				  `juan` char(6) NOT NULL,
				  `qi` char(7) NOT NULL,
				  `qkmc` char(100) NOT NULL,
				  `wzlx_z` int(1) NOT NULL COMMENT '总的类型',
				  `message` text NOT NULL,
				  `message1` text NOT NULL,
				  `search_school` varchar(200) NOT NULL COMMENT '搜索的学校',
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `sno` (`sno`,`search_school`),
				  KEY `qkdm` (`qkdm`),
				  KEY `nian` (`nian`),
				  KEY `search_school` (`search_school`)
				) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
			mysql_query($sqll);
			$res = mysql_query("SELECT * FROM ".$seTB." WHERE `search_school`='".$value."'");
			if(mysql_num_rows($res) > 0){
				for($z=0;$z<=$lastid;$z+=$step){
					$sql = "SELECT * FROM ".$seTB." WHERE `nian` >='".$start."' AND `nian`<='".$end."' AND `search_school`='".$value."' AND id between ".($z+1)." and ".($z+$step);				
					$result	= mysql_query($sql);
					while($row	= mysql_fetch_assoc($result)){
						unset($row[id]);		
						$sql_i = sprintf('INSERT INTO `cssci_new_chouqu`.`cssci_chouqu_'.$key.'` (`%s`) VALUES ("%s")',implode('`,`',array_keys($row)),implode('","',array_values($row)));
						$res_i = mysql_query($sql_i);
						//echo $sql_i."<br>";flush();	
						echo '成功的学校<<<<<'.$value."<br>";flush();
					}
				}
		   }else{
				//没有就去lysy表里去搜
				$res1	= mysql_query("SELECT * FROM ".$seTB1." WHERE `authors_jg` LIKE '%".$value."%'");

				if(mysql_num_rows($res1) >0){
					for($j=0;$j<=$lastid1;$j+=$step){
						$sql1		= "SELECT * FROM ".$seTB1." WHERE `authors_jg` LIKE '%".$value."%' AND `nian` >='".$start."' AND `nian`<='".$end."' AND id between ".($j+1)." and ".($j+$step);
						$res11		= mysql_query($sql1);
						while($row1	= mysql_fetch_assoc($res11)){
							unset($row1[id]);
							$data = array();
							foreach($row1 as $k=>$v){
								$data[$k] = addslashes($v);
							}						

							$data[search_school] = $value;
							//print_r($data);die;
							$sql_i = sprintf('INSERT INTO `select_school` (`%s`) VALUES ("%s")',implode('`,`',array_keys($data)),implode('","',array_values($data)));
							$res_i = mysql_query($sql_i);
							if($res_i){
								echo '这几个学校需要重新抽取<<<<<'.$value."<br>";flush();
							}
						}
					}
				}else{
					//库中没有此学校的数据，把学校插入另外一个表					
					$school	 .= $value.'|';		
					$school	 = addslashes($school);					
				}
		   }			
			
		}
		if($school){
			$add_time= date('Y-m-d H:i:s',time());
			$sql_num = "INSERT INTO school_zhuaqu_bu_zhua (`school_name`,`start`,`end`,`add_time`) VALUES ('".$school."','".$start."','".$end."','".$add_time."')";
			mysql_query($sql_num);
			echo "这几个学校库中没有，需要抓取>>>>>".$school.">>>>>开始时间".$start.">>>>>结束时间".$end."<br>";flush();
		}
		
	}

	 function step_4()
	 {
		echo '<meta http-equiv="content-type" content="text/html;charset=utf-8">';
		//抽取学校的数量
		$savepath	= 'D:\\phpstudy\\WWW\\cssci_new_exec\\school_chouqu_auto_txt\\';
		$this->delFile($savepath,'');
		$table_num = mysql_query("SELECT COUNT(*) TABLES, table_schema FROM information_schema.TABLES  WHERE table_schema = 'cssci_new_chouqu' ");
		$table_num = mysql_fetch_assoc($table_num);
		$school_chouqu_num = trim($table_num[TABLES]);
		if(!$school_chouqu_num){
			$school_chouqu_num	= trim($_REQUEST['school_chouqu_num']);
		}		
		for($i=0;$i<$school_chouqu_num;$i++){
			//$savepath	= 'D:\\phpstudy\\WWW\\cssci_new_exec\\school_chouqu_auto\\';
			mkdir($savepath.'cssci_chouqu_'.$i);
			$seTB	= "cssci_new_chouqu.cssci_chouqu_".$i;
			$lastid	= $this->getLastId($seTB);
			$step	= $this->step;	
			$step	= 10000;
			//判断此学校表是否为空			
			if(mysql_num_rows(mysql_query("select id from ".$seTB)) > 0){
				$strstring	= '';
				for($z	= 0;$z<=$lastid;$z+=$step)
				{
					$sql2	= "select * from ".$seTB." where id between ".($z+1)." and ".($z+$step);
					$result	= mysql_query($sql2);
					while($row	= mysql_fetch_assoc($result))
					{						
						$strstring = '';
						$strstring	.='sno-文章唯一号:'.$row['sno']."\r\n";//
						$strstring	.='search_school-搜索的学校:'.$row['search_school']."\r\n";//
						$strstring	.='title-篇名:'.$row['title']."\r\n";//
						$strstring	.='lypmp-篇名拼音:'.$row['lypmp']."\r\n";//
						$strstring	.='blpm-英文篇名:'.$row['blpm']."\r\n";//
						$strstring	.='authors-作者:'.str_replace('aaa','@@@',$row['authors1'])."\r\n";//
						$strstring	.='first_author-第一作者:'.str_replace('aaa','@@@',$row['first_author'])."\r\n";//
						$strstring	.='authors_jg-作者机构:'.str_replace('aaa','@@@',$row['authors_jg'])."\r\n";//
						$strstring	.='first_jg-第一机构:'.str_replace('aaa','@@@',$row['first_jg'])."\r\n";//
						$strstring	.='authors_address-作者邮编:'.str_replace('aaa','@@@',$row['authors_address'])."\r\n";//
						$strstring	.='wzlx-文献类型号:'.$row['wzlx']."\r\n";//
						$strstring	.='wzlx_wenxian-文献类型:'.$row['wzlx_wenxian']."\r\n";//
						$strstring	.='qkno-期刊号:'.$row['qkno']."\r\n";//
						$strstring	.='xkdm1-中图类号:'.$row['xkdm1']."\r\n";//
						$strstring	.='xkdm2:'.$row['xkdm2']."\r\n";//
						$strstring	.='ym-页数:'.$row['ym']."\r\n";//
						$strstring	.='ywsl:'.$row['ywsl']."\r\n";//
						$strstring	.='byc-关键词:'.str_replace('aaa','@@@',$row['byc'])."\r\n";//
						$strstring	.='dcbj:'.$row['dcbj']."\r\n";
						$strstring	.='xmlb-基金项目:'.$row['xmlb']."\r\n";//
						$strstring	.='jjlb:'.$row['jjlb']."\r\n";//
						$strstring	.='lrymc:'.$row['lrymc']."\r\n";//
						$strstring	.='skdm:'.$row['skdm']."\r\n";//
						$strstring	.='yjdm:'.$row['yjdm']."\r\n";
						$strstring	.='xkfl1-学科类型号:'.$row['xkfl1']."\r\n";//
						$strstring	.='xkfl1_xueke-学科类型:'.$row['xkfl1_xueke']."\r\n";//
						$strstring	.='ycflag:'.$row['ycflag']."\r\n";//
						$strstring	.='xkfl2:'.$row['xkfl2']."\r\n";
						$strstring	.='qkdm:'.$row['qkdm']."\r\n";
						$strstring	.='nian-年:'.$row['nian']."\r\n";
						$strstring	.='juan-卷:'.$row['juan']."\r\n";
						$strstring	.='qi-期:'.$row['qi']."\r\n";
						$strstring	.='qkmc-来源期刊:'.$row['qkmc']."\r\n";
						$strstring	.='wzlx_z-期刊类型号:'.$row['wzlx_z']."\r\n";
						$strstring	.='message-作者信息:'.$row['message1']."\r\n";
						$strstring	.="###@@@";
						$strstring	.="\r\n";
						$strstring	.="\r\n";
						file_put_contents($savepath.'cssci_chouqu_'.$i.'\\'.($z+1).'-'.($z+$step).'.txt',$strstring,FILE_APPEND);
					}	
					echo $sql2."<br>";flush();
				}
			}else{
				$strstring = '';
				file_put_contents($savepath.'cssci_chouqu_'.$i.'\\nothing.txt',$strstring,FILE_APPEND);
				//$fopen = fopen($savepath.'cssci_chouqu_'.$i.'\\no.txt',"W");
				//fclose($fopen);
				echo ">>>>>>>>>>>此表中没有数据>>>>>><br>";flush();
			}
		}
		
	 }

	 function step_5(){
		$path = 'school_chouqu_auto_txt_zip.zip';
		if(file_exists($path)){
			unlink($path);
		}
		$zip=new ZipArchive();

		if($zip->open($path, ZipArchive::OVERWRITE)=== TRUE){
		  $this->addFileToZip('../school_chouqu_auto_txt', $zip); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
		  $zip->close(); //关闭处理的zip文件

		  $filename = $path;
			header ( "Cache-Control: max-age=0" );
			header ( "Content-Description: File Transfer" );
			header ( 'Content-disposition: attachment; filename=' . basename ( $filename ) ); // 文件名
			header ( "Content-Type: application/zip" ); // zip格式的
			header ( "Content-Transfer-Encoding: binary" ); // 告诉浏览器，这是二进制文件
			header ( 'Content-Length: ' . filesize ( $filename ) ); // 告诉浏览器，文件大小
			@readfile ( $filename );//输出文件;
		}		
		
	 }

	 function step_6(){
		include('./phpexcel/php_execl.php');
		//require_once("./phpexcel/php_execl.php");

	 }

	 function step_7(){
		$path = 'school_chouqu_auto_execl_zip.zip';
		if(file_exists($path)){
			unlink($path);
		}
		$zip=new ZipArchive();

		if($zip->open($path, ZipArchive::OVERWRITE)=== TRUE){
		  $this->addFileToZip('../school_chouqu_auto_execl', $zip); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
		  $zip->close(); //关闭处理的zip文件

		  $filename = $path;
			header ( "Cache-Control: max-age=0" );
			header ( "Content-Description: File Transfer" );
			header ( 'Content-disposition: attachment; filename=' . basename ( $filename ) ); // 文件名
			header ( "Content-Type: application/zip" ); // zip格式的
			header ( "Content-Transfer-Encoding: binary" ); // 告诉浏览器，这是二进制文件
			header ( 'Content-Length: ' . filesize ( $filename ) ); // 告诉浏览器，文件大小
			@readfile ( $filename );//输出文件;
		}		
		
	 }

	function stringFromColumnIndex($pColumnIndex = 0) {
		static $_indexCache = array();
		if (!isset($_indexCache[$pColumnIndex])) {
			// Determine column string
			if ($pColumnIndex < 26) {
				$_indexCache[$pColumnIndex] = chr(65 + $pColumnIndex);
			} elseif ($pColumnIndex < 702) {
				$_indexCache[$pColumnIndex] = chr(64 + ($pColumnIndex / 26)) .
					chr(65 + $pColumnIndex % 26);
			} else {
				$_indexCache[$pColumnIndex] = chr(64 + (($pColumnIndex-26)/676)) .
						chr(65 + ((($pColumnIndex-26)%676)/26)) .
						chr(65 + $pColumnIndex % 26);
			}
		}
		return $_indexCache[$pColumnIndex];
	}

	 function addFileToZip($path,$zip){
		 $handler=opendir($path); //打开当前文件夹由$path指定。
		  while(($filename=readdir($handler))!==false){
			if($filename != "." && $filename != ".."){//文件夹文件名字为'.'和‘..'，不要对他们进行操作
			  if(is_dir($path."/".$filename)){// 如果读取的某个对象是文件夹，则递归
				$this->addFileToZip($path."/".$filename, $zip);
			  }else{ //将文件加入zip对象
				//if(is_file($path."/".$filename) == false){
					//file_put_contents($path."/".$filename.'\\此学校没有数据.txt','',FILE_APPEND);
				//	fopen($path."/".$filename.'/此学校没有数据.txt', "w");
				//}
				$zip->addFile($path."/".$filename);
			  }
			}
		  }
		  @closedir($path);
		 //下面是输出下载;
		
	}
 
	 function delFile($dir,$file_type='') { 
	  if(is_dir($dir)){
		$files = scandir($dir);
	 //打开目录 //列出目录中的所有文件并去掉 . 和 .. 
		foreach($files as $filename){
		  if($filename!='.' && $filename!='..'){
			if(!is_dir($dir.'/'.$filename)){
			  if(empty($file_type)){
				unlink($dir.'/'.$filename);
			  }else{
				if(is_array($file_type)){
				  //正则匹配指定文件
				  if(preg_match($file_type[0],$filename)){
					unlink($dir.'/'.$filename);
				  }
				}else{
				  //指定包含某些字符串的文件
				  if(false!=stristr($filename,$file_type)){
					unlink($dir.'/'.$filename);
				  }
				}
			  }
			}else{ 
			  $this->delFile($dir.'/'.$filename);
			  rmdir($dir.'/'.$filename);
			} 
		  }
		}
	  }else{
		if(file_exists($dir)) unlink($dir);
	  } 
	}

	 function del_directory_file($directory){
		if (is_dir($directory) == false){
			exit("The Directory Is Not Exist!");
		}
		$handle = opendir($directory);
		while (($file = readdir($handle)) !== false){
			if ($file != "." && $file != ".." && is_file("$directory/$file")){
				unlink("$directory/$file");
			}
		}
		closedir($handle);
	}

	//邮箱发送
    function sendMail(){
        require_once "./email.class.php";
        //下面开始设置一些信息
        $smtpserver    = "smtp.163.com";//SMTP服务器
        $smtpserverport= 25;//SMTP服务器端口 465/587 ssl
        $smtpusermail  = "@163.com";//SMTP服务器的用户邮箱
        $smtpemailto   = "";//发送给谁(可以填写任何邮箱地址)
        $smtpuser      = "";//SMTP服务器的用户帐号(即SMTP服务器的用户邮箱@前面的信息)
        $smtppass      = "";//SMTP服务器的用户密码,授权码
        $mailtitle     = '111';//邮件主题
        $mailcontent   = "<h1>您成功发送了一条电子邮件</h1>";//邮件内容
        $mailtype      = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件

        $smtp          = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
        $smtp->debug   = true;//是否显示发送的调试信息
        $state         = $smtp->sendmail($smtpemailto,$smtpusermail,$mailtitle,$mailcontent,$mailtype);

        if($state==""){
            echo "发送失败！";exit();
        }
        echo "发送成功！";die;

    }


	function unicodeToUtf8($str) {
		$str = rawurldecode($str);
		$str=preg_replace('/&#x000d;&#x000a;/siU','',$str);
		preg_match_all("/(?:%u.{4})|&#x.{4};|&#\d+;|.+/U",$str,$r);//以&#、&#x、&#u的unicode
		$ar = $r[0];
		//print_r($ar);
		foreach($ar as $k=>$v) {
			if(substr($v,0,2) == "%u")
				$ar[$k] = iconv("UCS-2","UTF-8",pack("H4",substr($v,-4)));
			elseif(substr($v,0,3) == "&#x")
				$ar[$k] = iconv("UCS-2","UTF-8",pack("H4",substr($v,3,-1)));
			elseif(substr($v,0,2) == "&#") {
				//echo substr($v,2,-1)."\n";
				$ar[$k] = iconv("UCS-2","UTF-8",pack("n",substr($v,2,-1)));
			}
		}
		return join("",$ar);//字符解码转换  修改  Author【cxx】
	}

	
	function run()
	{
		
		//$this->step_1();//自动抓取，补抓
		//$this->step_2();//解析，从school_zhuaqu_num表里最后一个记录开始解析

		//$this->step_3();//数据抽取，select_school表里没有从lysy表里解析，都没有存入school_zhuaqu_bu_zhua

		//$this->step_4();//数据导出txt
		//$this->step_5();//下载导出txt
	}
}
$action = $_REQUEST['action'];
$obj=new xxtt_book_exec();
$obj->$action();


?>