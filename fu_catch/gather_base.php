<?php
class HZip
{
  /**
   * Add files and sub-directories in a folder to zip file.
   * @param string $folder
   * @param ZipArchive $zipFile
   * @param int $exclusiveLength Number of text to be exclusived from the file path.
   */
  private static function folderToZip($folder, &$zipFile, $exclusiveLength) {
    $handle = opendir($folder);
    while (false !== $f = readdir($handle)) {
      if ($f != '.' && $f != '..') {
        $filePath = "$folder/$f";
        // Remove prefix from file path before add to zip.
        $localPath = substr($filePath, $exclusiveLength);
        if (is_file($filePath)) {
          $zipFile->addFile($filePath, $localPath);
        } elseif (is_dir($filePath)) {
          // Add sub-directory.
          $zipFile->addEmptyDir($localPath);
          self::folderToZip($filePath, $zipFile, $exclusiveLength);
        }
      }
    }
    closedir($handle);
  }

  /**
   * Zip a folder (include itself).
   * Usage:
   *   HZip::zipDir('/path/to/sourceDir', '/path/to/out.zip');
   *
   * @param string $sourcePath Path of directory to be zip.
   * @param string $outZipPath Path of output zip file.
   */
  public static function zipDir($sourcePath, $outZipPath)
  {
    $pathInfo = pathInfo($sourcePath);
    $parentPath = $pathInfo['dirname'];
    $dirName = $pathInfo['basename'];

    $z = new ZipArchive();
    $z->open($outZipPath, ZIPARCHIVE::CREATE);
    $z->addEmptyDir($dirName);
    self::folderToZip($sourcePath, $z, strlen("$parentPath/"));
    $z->close();
  }
} 
class gather_base
{
    static $timeout_infos;
	static $timeout_refer;
	var	   $fgf			= '##@@##';//分隔符号
	var    $step		= 10000;//抓取步长
	var	   $sessStrings1	= 'jcrsst2.txt';//jcr session文件
	var	   $sessStrings2	= 'jcrsst3.txt';//Highly Hot session文件
	var	   $sessStrings3	= 'jcrsst4.txt';//topaper session文件
	var	   $sessStrings4	= 'jcrsst5.txt';//coun,jour,front session文件

	var	   $sessStrings5	= 'jcrsst6.txt';//toppaper排名session 文件

	function __construct()
	{
		$this->spider_tasks = 'spider_tasks';
		//$this->spider_wanfang_qikan = 'spider_wanfang_qikan';
		$this->spider_field_search  = 'spider_field_search';
		$this->spider_field_alias	= 'spider_field_alias';

		$this->spider_type_search	= 'spider_source_type_search';
		$this->spider_source_type	= 'spider_source_type';

		$this->spider_subject_search= 'spider_subject_search';
		$this->spider_subject	= 'spider_subject';
		$this->fail2			= '3000';
		$this->success2			= '9000';
		$this->null2			= '9001';

	}

	/**************抓取网页方法***************/
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

//		if($header!='')
//		{
//			 curl_setopt($curl, CURLOPT_HEADER, 0);
//		}
//		else
//		{
//			 curl_setopt($curl, CURLOPT_HEADER, 1);
//		}
		curl_setopt($curl, CURLOPT_HEADER, 0);
	   
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect:'));
		$tmpInfo = curl_exec($curl);
		$errno = curl_errno($curl);
		curl_close($curl);
		
		if($errno==28) { self::$timeout_infos='2';}
		elseif($errno==7){self::$timeout_infos='3';}
		elseif($errno==52){self::$timeout_infos='4';}
		return $tmpInfo;
	}

	function vcurl2($url, $post = '', $cookie = '', $cookiejar = '', $referer = '',$stime='50',$localhost='0')
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
		curl_setopt($curl, CURLOPT_HEADER, 1);
		curl_setopt($curl, CURLOPT_NOBODY, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect:'));
		$tmpInfo = curl_exec($curl);
		$errno = curl_errno($curl);
		curl_close($curl);
		
		if($errno==28) { self::$timeout_infos='2';}
		elseif($errno==7){self::$timeout_infos='3';}
		elseif($errno==52){self::$timeout_infos='4';}
		return $tmpInfo;
	}

	/********************去除html标签***********************/
	function delHtmltag($string)
	{
		$string	= trim(strip_tags($string));
		return $string;
	}

	/***************获取cnki文章唯一号*****************/
	function getCnkifname($durl)
	{
		$arr			= '';
		$fname			= '';

		if(preg_match_all('/filename=(.*)&/siU',$durl,$arr))
		{
			$fname		= $arr[1][0];			
		}
		else
		{
			if(preg_match_all('/filename=(.*)$/siU',$durl,$arr))
			{
				$fname	= $arr[1][0];
			}
			else
			{
				echo "获取文章唯一号失败!";exit;
			}
		}
		return $fname;
	}

	/***************获取cnki期刊唯一号***************/
	function getCnkikancode($durl)
	{
		$arr	= array();
		preg_match_all('/BaseID=(.*)&/siU',$durl,$arr);
		return $this->chulistring2($arr[1][0]);
	}

	function chulistring2($ddg)
	{
		$ddg=trim(strip_tags($ddg));
		return $ddg;
	}

	function chulistring($ddg)
	{
		$ddg=trim(strip_tags($ddg));
		$ddg=str_replace('&nbsp;','',$ddg);
		$ddg=addslashes($ddg);
		return $ddg;
	}

	function chulistring3($ddg)
	{
		$ddg=trim(strip_tags($ddg));
		$ddg=addslashes($ddg);
		return $ddg;
	}

	//转义函数
	function stringSlash($ddg)
	{
		$ddg	= addslashes($ddg);
		return $ddg;
	}

	//创建通用目次抓取表
	function creatMcTable($tbname)
	{
		mysql_query("CREATE TABLE IF NOT EXISTS `".$tbname."` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`content` longtext NOT NULL,
		`url` text NOT NULL,
		PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	}

	//获取lastid
	function getLastId($tbname)
	{
		$sql	= "select id from ".$tbname." order by id DESC";
		$result	= mysql_query($sql);
		$rel	= mysql_fetch_assoc($result);
		return $rel['id'];
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

	//获取当前时间
	function getTimeString()
	{
		$timestring	= date('YmdHis');
		return $timestring;
	}

	//获取webofscience session
	function getWebOfSession($seNum)
	{
		switch ($seNum)
		{
			case 1:$this->getJcrSession();break;
			case 2:$this->getHighHotSession();break;
			case 3:$this->getTopaperSession();break;
			case 4:$this->getCounSession();break;
			case 5:$this->getPaiMingSession();break;
		}
	}

	//获取country session
	function getCounSession()
	{
		$tmp_c		= $this->vcurl('http://isiknowledge.com/?DestApp=UA','','','','','40');
		preg_match_all("/title=\"Essential Science Indicators\".*\('(.*)'/siU",$tmp_c,$arr);
		$tmp_url	= 'http://apps.webofknowledge.com/'.$arr[1][0];
		$this->vcurl($tmp_url,'','',$this->sessStrings4);
	}

	//获取排名 session
	function getPaiMingSession()
	{
		$tmp_c		= $this->vcurl('http://isiknowledge.com/?DestApp=UA','','','','','40');
		preg_match_all("/title=\"Essential Science Indicators\".*\('(.*)'/siU",$tmp_c,$arr);
		$tmp_url	= 'http://apps.webofknowledge.com/'.$arr[1][0];
	    $this->vcurl($tmp_url,'','',$this->sessStrings5);//exit;
	}

	//获取topapersession
	function getTopaperSession()
	{
		$tmp_c		= $this->vcurl('http://isiknowledge.com/?DestApp=UA','','','','','40');
		preg_match_all("/title=\"Essential Science Indicators\".*\('(.*)'/siU",$tmp_c,$arr);
		$tmp_url	= 'http://apps.webofknowledge.com/'.$arr[1][0];
		$this->vcurl($tmp_url,'','',$this->sessStrings3);
	}

	//获取highly and hot session
	function getHighHotSession()
	{
		$tmp_c		= $this->vcurl('http://isiknowledge.com/?DestApp=UA','','','','','40');
		preg_match_all("/title=\"Essential Science Indicators\".*\('(.*)'/siU",$tmp_c,$arr);
		$tmp_url	= 'http://apps.webofknowledge.com/'.$arr[1][0];
		$this->vcurl($tmp_url,'','',$this->sessStrings2);
	}

	//获取jcr的session
	function getJcrSession()
	{
		//echo $this->sessStrings1;exit;
		$tmp_c		= $this->vcurl('http://isiknowledge.com/?DestApp=UA','','','','','40');
		preg_match_all("/title=\"Journal Citation Reports\".*\('(.*)'/siU",$tmp_c,$arr);
		$tmp_url	= 'http://apps.webofknowledge.com/'.$arr[1][0];
		$this->vcurl($tmp_url,'','',$this->sessStrings1);
	}

	

	//自己获取内容更新自己
	function getContentUpdateSelf($tbname='',$url='',$field='',$sestring='')
	{
		$sql		= "select * from ".$tbname." where `".$field."`='' and `".$url."`!=''";
		//echo $sql;exit;
		$result		= mysql_query($sql);
		while($row	= mysql_fetch_assoc($result))
		{
			$tmp_cnt	= $this->stringSlash($this->vcurl(trim($row[$url]),'','jcrsid=4Er3tLCSLh4gSyFkDX8; CUSTOMER="Nanjing University"; E_GROUP_NAME="Nanjing University"; SID="4Er3tLCSLh4gSyFkDX8"',$sestring));
			//echo $tmp_cnt;exit;
			$sql		= "update ".$tbname." set ".$field."='".$tmp_cnt."' where id='".$row['id']."'";
			mysql_query($sql);
			echo ">>>>>>>>>>>>>>".$row['id']."<br>";flush();//exit;
		}
	}

	//保留一个标签
	function stripWithOutTag($string='',$cnt='',$link='')
	{
		$cnt	= preg_replace("/".$string."/siU",$link,$cnt);
		$cnt	= $this->chulistring($cnt);
		$cnt	= preg_replace("/".$link."++/siU",$link,$cnt);
		return $cnt;
	}

	//获取搜索的结果数目
	function getSelectNum($result)
	{
		$num	= mysql_num_rows($result);
		return $num;
	}

	//获取头部cookivalue
	function getCookieValue($string)
	{
		preg_match_all('/Set-Cookie:(.*)path=/siU',$string,$arr);
		$cookieValue	= implode('',$arr[1]);
		return	$cookieValue;
	}
	
	//表结构相同的表数据对拷
	function DataTableToTable($tbname1='',$tbname2='')
	{
		$lastid	= $this->getLastId($tbname1);
		$step	= $this->step;

		for($z	= 0;$z<$lastid;$z+=$step)
		{
			$sql2	= "select * from ".$tbname1." where id between ".($z+1)." and ".($z+$step);
			$result	= mysql_query($sql2);

			while($row=mysql_fetch_row($result))
			{
				$string='';
				for($i=1;$i<count($row);$i++)
				{
					$string.=addslashes($row[$i])."','";
				}
				$string="'','".preg_replace("/(,')$/siU",'',$string);

				//根据这条记录的年份选择插入年份表
				$sql="insert into ".$tbname2." values (".$string.")";
				mysql_query($sql);	
			}	
			echo $sql2."<br>";flush();	
		}
	}

	//判断字符串中是否含有中文
	function isChinese($str)
	{
		if(preg_match("/[\x7f-\xff]/", $str))
		{ 
			return true;
		} 
		else 
		{
			return false;
		}
	}

	//获取京东封面地址
	function getFmFromJd($isbn	= '')
	{
		$imgUrl		 = '';
		$tmp_url	 = 'http://search.jd.com/bookadvsearch?keyword=&author=&publisher=&isbn='.$isbn.'&discount=&ep1=&ep2=&ap1=&ap2=&pty1=&ptm1=&pty2=&ptm2=&enc=utf-8';
		$cnt		 = $this->vcurl($tmp_url,'','','','','40');
		preg_match_all('/<img data-img=.*lazyload="(.*)"/siU',$cnt,$acc);
		$imgUrl		 = $this->chulistring2($acc[1][0]);
		return $imgUrl;
	}

	//获取亚马逊封面
	function getFmFromYmx($isbn	= '')
	{
		$imgUrl		= '';
		$tmp_url	= 'http://www.amazon.cn/%E5%8F%98%E8%BD%A8-%E8%B6%85%E8%B6%8A%E6%89%80%E6%9C%89%E4%BA%BA%E6%9C%9F%E6%9C%9B%E7%9A%845%E4%B8%AA%E8%A6%81%E8%AF%80-%E7%BD%97%E4%BC%AF%E7%89%B9%E2%80%A2%E5%BA%93%E7%8F%80/dp/B001393AV8/ref=sr_1_1?s=books&ie=UTF8&qid=1423892421&sr=1-1&keywords=9787801965677';
		$tmp_cnt	= $this->vcurl($tmp_url);
		preg_match_all('/id="imgBlkFront" src="(.*)"/siU',$tmp_cnt,$arr);
		$imgUrl		= $this->chulistring2($arr[1][0]);
		return $imgUrl;
	}

	//中文分号转化为英文分号
	function getfh($string)
	{
		$string	= str_replace('；',';',$string);
		return $string;
	}

	//获取上一步插入的id号
	function getInsertId()
	{
		$num	= mysql_insert_id();
		return $num;
	}

	function Del_space($str){
		$str=preg_replace('/	/siU','',trim($str));
		for($i=0;$i<strlen($str);$i++){
			if($i==0){
				$b=substr($str,$i,1);
			}else{
				if($str[$i]==" "){
					if($str[$i-1]!=" ")
						$b.=substr($str,$i,1);
				}else
					$b.=substr($str,$i,1);
			}
		}
		return $b;	
	}
	function con_replace($str){
		$patterns[0] = '/	/'; 
		$patterns[1] = '/\n/';
		$patterns[2] = '/\r/';
		$patterns[3] = '/&nbsp;/';
		$replacements[0] = ' '; 
		$replacements[1] = ' ';
		$replacements[2] = ' ';
		$replacements[3] = ' ';
		$arr=preg_replace($patterns, $replacements,$str); 
		return $arr;
	}

	function get_head($sUrl)
	{
		$oCurl = curl_init();
		curl_setopt($oCurl, CURLOPT_URL, $sUrl);
		curl_setopt($oCurl, CURLOPT_HTTPHEADER,$header);
		// 返回 response_header, 该选项非常重要,如果不为 true, 只会获得响应的正文
		curl_setopt($oCurl, CURLOPT_HEADER, true);
		// 是否不需要响应的正文,为了节省带宽及时间,在只需要响应头的情况下可以不要正文
		curl_setopt($oCurl, CURLOPT_NOBODY, true);
		// 使用上面定义的 ua
		curl_setopt($oCurl, CURLOPT_USERAGENT,$user_agent);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		// 不用 POST 方式请求, 意思就是通过 GET 请求
		curl_setopt($oCurl, CURLOPT_POST, false);

		$sContent = curl_exec($oCurl);
		// 获得响应结果里的：头大小
		$headerSize = curl_getinfo($oCurl, CURLINFO_HEADER_SIZE);
		// 根据头大小去获取头信息内容
		$header = substr($sContent, 0, $headerSize);

		curl_close($oCurl);

		return $header;
	}
	//成功
	function success($tasks_id){
		$sql_s = "UPDATE ".$this->spider_tasks." SET status_code=".$this->success2." WHERE id=".$tasks_id;
		$res_s =  mysql_query($sql_s);
		if(!$res_s){
			$this->fail($tasks_id);
		}

	}
	//失败
	function fail($tasks_id){
		$sql_f = "UPDATE ".$this->spider_tasks." SET status_code=".$this->fail2." WHERE id=".$tasks_id;
		mysql_query($sql_f);

	}
	//没有值
	function null($tasks_id){
		$sql_n = "UPDATE ".$this->spider_tasks." SET status_code=".$this->null2." WHERE id=".$tasks_id;
		$res_n = mysql_query($sql_n);
		if(!$res_n){
			$this->fail($tasks_id);
		}

	}

//	function percent($i,$percent,$tasks_id){
//		$sql = "UPDATE ".$this->spider_tasks." SET complete_count='".($i+1)."',percent='".$percent."' WHERE id=".$tasks_id;
//		mysql_query($sql);
//	}
	function percent($i,$tasks_id){
		$sql = "UPDATE ".$this->spider_tasks." SET complete_count='".$i."' WHERE id=".$tasks_id;
		$res = mysql_query($sql);
		if(!$res){
			$this->fail($tasks_id);
		}
	}
	//查询总数
	function gettotal($tasks_id){
		$sql_t 	 = 'SELECT total_count,complete_count FROM '.$this->spider_tasks.' WHERE id ='.$tasks_id;
		$res_t   = mysql_query($sql_t);
		if($res_t){
			$row_t = mysql_fetch_assoc($res_t);
			return $row_t;
		}
	}

	//万方拼接参数
	function wf_canshu($tasks_id,$ku_type,$pagenum=0,$pagesize){
		$sql = 'SELECT * FROM '.$this->spider_field_search.' as s LEFT JOIN '.$this->spider_field_alias.' as a ON s.field_alias_id=a.id WHERE s.tasks_id='.$tasks_id;
		$res = mysql_query($sql);
		if($res){
			while($row   = mysql_fetch_assoc($res)){
				$result[]= $row;
			}
			//print_r($result);die;
			$where  = '';
			$where1 = '';
			for($kkk=0;$kkk<count($result);$kkk++){

				if($result[$kkk]['is_show'] == 1){
					if($kkk == 0){
						if($result[$kkk]['matched_pattern']== 1){
							$where .= $result[$kkk]['name'].':("'.$result[$kkk]['search_content'].'")';

						}else{
							$where .= $result[$kkk]['name'].':('.$result[$kkk]['search_content'].')';
						}
					}else{
						//与
						if($result[$kkk]['logic_id'] == 1){
							if($result[$kkk]['matched_pattern']== 1){
								$where .= '*'.$result[$kkk]['name'].':('.$result[$kkk]['search_content'].')';
							}else{
								$where .= '*'.$result[$kkk]['name'].':(∷'.$result[$kkk]['search_content'].'∷)';
							}
							//或
						}elseif($result[$kkk]['logic_id'] == 2){
							if($result[$kkk]['matched_pattern']== 1){
								$where .= '+'.$result[$kkk]['name'].':('.$result[$kkk]['search_content'].')';
							}else{
								$where .= '+'.$result[$kkk]['name'].':(∷'.$result[$kkk]['search_content'].'∷)';
							}
							//非
						}else{
							if($result[$kkk]['matched_pattern']== 1){
								$where .= '^'.$result[$kkk]['name'].':('.$result[$kkk]['search_content'].')';
							}else{
								$where .= '^'.$result[$kkk]['name'].':(∷'.$result[$kkk]['search_content'].'∷)';
							}
						}
					}
				}else{
					if($result[$kkk]['name']== '开始年'){
						$where1  .= '&'.$result[$kkk]['short'].'='.$result[$kkk]['search_content'];
					}elseif($result[$kkk]['name']== '结束年'){
						$where1  .= '&'.$result[$kkk]['short'].'='.$result[$kkk]['search_content'];
					}else{
						$where1  .= '&startDate=1900&endDate=2018';
					}


				}

			}
			return 'http://www.wanfangdata.com.cn/searchResult/getCoreSearch.do?d=0.7501605535783562&paramStrs='.urlencode($where).$where1.'&updateDate=&classType='.$ku_type.'&pageNum='.$pagenum.'&pageSize='.$pagesize.'&sortFiled=';

		}
	}

	function cssci_canshu($tasks_id,$pagesize,$pagenum){
		$sql = 'SELECT * FROM '.$this->spider_field_search.' as s LEFT JOIN '.$this->spider_field_alias.' as a ON s.field_alias_id=a.id WHERE s.tasks_id='.$tasks_id;
		$res = mysql_query($sql);
		if($res){
			while($row   = mysql_fetch_assoc($res)){
				$result[]= $row;
			}
			//print_r($result);die;
			$where = '';
			$where1 = '';
			for($kkk=0;$kkk<count($result);$kkk++){
				//是否是填入的内容
				if($result[$kkk]['is_show'] == 1){
					if($kkk == 0){
						//精确
						if($result[$kkk]['matched_pattern']== 1){
							$where .= urlencode($result[$kkk]['search_content']).'%2B%2B%2B'.($result[$kkk]['map_param']+1).'';
						}else{
							//模糊
							$where .= urlencode($result[$kkk]['search_content']).'%2B%2B%2B'.$result[$kkk]['map_param'];
						}
					}else{
						//与
						if($result[$kkk]['logic_id'] == 1){
							//精确
							if($result[$kkk]['matched_pattern']== 1){
								$where .= '%2B%2B%2BAND%7C%7C%7C'.urlencode($result[$kkk]['search_content']).'%2B%2B%2B'.($result[$kkk]['map_param']+1);
								//模糊
							}else{
								$where .= '%2B%2B%2BAND%7C%7C%7C'.urlencode($result[$kkk]['search_content']).'%2B%2B%2B'.$result[$kkk]['map_param'];
							}
							//或
						}elseif($result[$kkk]['logic_id'] == 2){
							//精确
							if($result[$kkk]['matched_pattern']== 1){
								$where .= '%2B%2B%2BOR%7C%7C%7C'.urlencode($result[$kkk]['search_content']).'%2B%2B%2B'.($result[$kkk]['map_param']+1);
								//模糊
							}else{
								$where .= '%2B%2B%2BOR%7C%7C%7C'.urlencode($result[$kkk]['search_content']).'%2B%2B%2B'.$result[$kkk]['map_param'];
							}
						}
					}

				}else{
					//其他的搜索框
					//如果有搜索年份
					if($result[$kkk]['name'] == '开始年'){
						$where1 .= '&start_year='.$result[$kkk]['search_content'];
					}elseif($result[$kkk]['name'] == '结束年'){
						$where1 .= '&end_year='.$result[$kkk]['search_content'];
					}elseif($result[$kkk]['name'] == '年'){
						$where1 .= '&nian='.$result[$kkk]['search_content'];
					}elseif($result[$kkk]['name'] == '卷'){
						$where1 .= '&juan='.$result[$kkk]['search_content'];
					}elseif($result[$kkk]['name'] == '期'){
						$where1 .= '&qi='.$result[$kkk]['search_content'];
					}else{
						$where1 .= '&end_year=1998&end_year=2018&nian=&juan=&qi=';
					}

				}
			}
			//文献类型
			$sql2 = 'SELECT t.map_param FROM '.$this->spider_type_search.' as s LEFT JOIN '.$this->spider_source_type.' as t ON s.source_type_id=t.id WHERE s.tasks_id='.$tasks_id;
			$res2 = mysql_query($sql2);
			if($res2){
				$row2   = mysql_fetch_assoc($res2);
				if($row2['map_param']){
					$where1 .= '&wzlx='.$row2['map_param'];
				}
			}else{
				$where1 .= '&wzlx=';
			}

			//学科类别
			$sql3 = 'SELECT t.map_param FROM '.$this->spider_subject_search.' as s LEFT JOIN '.$this->spider_subject.' as t ON s.subject_id=t.id WHERE s.tasks_id='.$tasks_id;
			$res3 = mysql_query($sql3);
			if($res3){
				$row3   = mysql_fetch_assoc($res3);
				if($row3['map_param']){
					$where1 .= '&xkfl1='.$row3['map_param'];
				}
			}else{
				$where1 .= '&xkfl1=';
			}

			return 'http://cssci.nju.edu.cn/control/controllers.php?control=search_base&action=search_lysy&title='.urlencode($where).$where1.'&qkname=&jj=&xw1=&xw2=&pagesize='.$pagesize.'&pagenow='.$pagenum.'&order_type=nian&order_px=DESC&search_tag=0&session_key=942&rand=0.9070847153889212';
		}
	}
	//被引频次
	function cssci_use($author,$title){
//		$author = '大卫·科兹';
//		$title  = '坚持社会主义:苏联的教训和中国的经验';
		if(preg_match_all('/(.*)·(.*)$/siU',$author,$arr)){
			$author = $arr[2][0].','.$arr[1][0];
		}
		$author = urlencode(urlencode($author));
		$title  = urlencode(urlencode($title));
		$url    = 'http://cssci.nju.edu.cn/control/controllers.php?control=search_base&action=search_ywsy&author='.$author.'&author_type1=0&author_type2=1&title='.$title.'&title_type1=0&ywqk=&ywqk_type1=1&ywxj=&ywnd=&ywnd1=1998%252C1999%252C2000%252C2001%252C2002%252C2003%252C2004%252C2005%252C2006%252C2007%252C2008%252C2009%252C2010%252C2011%252C2012%252C2013%252C2014%252C2015%252C2016%252C2017&ywnd2=&ywlx=&search_model=AND&order_type=num&order_px=DESC&pagesize=20&search_tag=0&session_key=483&rand=0.7826750463022596&pagenow=1';
		return  $url;
	}

	function scopus_canshu($tasks_id,$sid,$txGid,$sl){
		//echo 123;die;
		$sql = 'SELECT * FROM ' .$this->spider_field_search. ' as s LEFT JOIN '.$this->spider_field_alias.' as a ON s.field_alias_id=a.id WHERE s.tasks_id='.$tasks_id;
		//echo $sql;die;
		$res = mysql_query($sql);
		if($res){
			while($row = mysql_fetch_assoc($res)) {
				$result[] = $row;
			}
			$where  = '';
			$where1 = '';
			$where2 = '';
			$field_num = 0;
			for($kkk = 0;$kkk<count($result);$kkk++){
				//是否是填入的内容
				if($result[$kkk]['is_show'] == 1){
					$field_num = $field_num+1;
					if($kkk == 0){
						$filed1 = $result[0]['search_content'];
						$where .= 'searchterm'.($kkk + 1).'='.$result[$kkk]['search_content'].'&'.'field'.($kkk + 1).'='.$result[$kkk]['map_param'];
						$where2.= '('.$result[$kkk]['map_param'].'('.$result[$kkk]['search_content'].')';
					}else{
						if($kkk == 1){
							$filed2 = $result[1]['search_content'];
							//与
							if($result[$kkk]['logic_id'] == 1){
								$where .= '&connector=AND&'.'searchterm'.($kkk+1).'='.$result[$kkk]['search_content'].'&'.'field'.($kkk+1).'='.$result[$kkk]['map_param'];
								$where2.= ' AND '.$result[$kkk]['map_param'].'('.$result[$kkk]['search_content'].')';
							//或
							}elseif($result[$kkk]['logic_id'] == 2){
								$where .= '&connector=OR&'.'searchterm'.($kkk+1).'='.$result[$kkk]['search_content'].'&'.'field'.($kkk+1).'='.$result[$kkk]['map_param'];
								$where2.= ' OR '.$result[$kkk]['map_param'].'('.$result[$kkk]['search_content'].')';
							//非
							}elseif($result[$kkk]['logic_id'] == 3){
								$where .= '&connector=AND+NOT&'.'searchterm'.($kkk+1).'='.$result[$kkk]['search_content'].'&'.'field'. ($kkk+1).'='.$result[$kkk]['map_param'];
								$where2.= ' AND NOT '.$result[$kkk]['map_param'].'('.$result[$kkk]['search_content'].')';
							}

						}else{
							//与
							if($result[$kkk]['logic_id'] == 1){
								$where .= '&connectors=AND&'.'searchterms='.$result[$kkk]['search_content'].'&'.'fields='.$result[$kkk]['map_param'];
								$where2.= ' AND '.$result[$kkk]['map_param'].'('.$result[$kkk]['search_content'].')';
								//或
							}elseif($result[$kkk]['logic_id'] == 2){
								$where .= '&connectors=OR&'.'searchterms='.$result[$kkk]['search_content'].'&'.'fields='.$result[$kkk]['map_param'];
								$where2.= ' OR '.$result[$kkk]['map_param'].'('.$result[$kkk]['search_content'].')';
								//非
							}elseif($result[$kkk]['logic_id'] == 3){
								$where .= '&connectors=AND+NOT&'.'searchterms='.$result[$kkk]['search_content'].'&'.'fields='.$result[$kkk]['map_param'];
								$where2.= ' AND NOT '.$result[$kkk]['map_param'].'('.$result[$kkk]['search_content'].')';
							}
						}
					}
				}else{
					//其他的搜索框
					//如果有搜索年份
					if($result[$kkk]['name'] == '开始年'){
						$where1 .= 'dateType=Publication_Date_Type&yearFrom='.$result[$kkk]['search_content'];
					}elseif($result[$kkk]['name'] == '结束年'){
						$where1 .= '&yearTo='.$result[$kkk]['search_content'];
					}
					if($result[$kkk]['name'] == '最近几日更新'){
						$where1 .= 'dateType=Load_Date_Type&yearFrom=Before+1960&yearTo=Present&loadDate='.$result[$kkk]['search_content'];
					}else{
						$where1 .= '&loadDate=7';
					}

				}

			}
			$where2 = str_replace('_','-',$where2.')');
			$data			= '';
			$data['filed1'] = $filed1;
			$data['filed2'] = $filed2;
			$data['where']  = $where2;
			$data['url']    = 'https://www.scopus.com/results/results.uri?numberOfFields='.($field_num-1).'&src=s&clickedLink=&edit=&editSaveSearch=&origin=searchbasic&authorTab=&affiliationTab=&advancedTab=&scint=1&menu=search&tablin=&'.$where.'&'.$where1.'&documenttype=All&accessTypes=All&resetFormLink=&st1='.$filed1.'&st2='.$filed2.'&sot=b&sdt=b&sl='.$sl.'&s='.urlencode($where2).'&sid='.$sid.'&searchId='.$sid.'&txGid='.$txGid.'&sort=plf-f&originationType=b&rr=';
			return $data;
		}
	}
	function road(){
		return 'E:/phpStudy/WWW/subject_catch/upload/spider_data/'.date('Y-m-d').'/';
	}
	//压缩成zip
	function zip($tasks_id,$ku){
		$path = $this->road();
		$this->Directory($path);
		$path = $path.md5($tasks_id.'_'.date('Ymd')).'.zip';
		if(file_exists($path)){
			unlink($path);
		}
		  HZip::zipDir(dirname(__FILE__).'/'.$ku.'/upload/'.$tasks_id, $path); 
		//$zip=new ZipArchive();

		/*if($zip->open($path,ZipArchive::OVERWRITE)=== TRUE){
			//echo dirname(__FILE__);
			$this->addFileToZip(dirname(__FILE__).'/'.$ku.'/upload/'.$tasks_id,$zip); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
			$zip->close(); //关闭处理的zip文件

		}*/
		return date('Y-m-d').'/'.md5($tasks_id.'_'.date('Ymd')).'.zip';
	}

	function addFileToZip($path,$zip){

		$handler=opendir($path); //打开当前文件夹由$path指定。
		while(($filename=readdir($handler))!==false){
			if($filename != "." && $filename != ".."){//文件夹文件名字为'.'和‘..'，不要对他们进行操作
				if(is_dir($path."/".$filename)){// 如果读取的某个对象是文件夹，则递归
					$this->addFileToZip($path."/".$filename, $zip);
				}else{ //将文件加入zip对象
					$zip->addFile($path."/".$filename);
				}
			}
		}
		@closedir($path);
	}

	//存储压缩文件位置
	function savezip($tasks_id,$path){
		$sql = "UPDATE ".$this->spider_tasks." SET file_path='".addslashes($path)."' WHERE id=".$tasks_id;
		$res = mysql_query($sql);
		if(!$res){
			$this->fail($tasks_id);
		}
	}

	function savepath($ku,$tasks_id){
		$savepath	= '../auto_spider_py3/php/fu_catch/'.$ku.'/upload/';
		$this->Directory($savepath.$tasks_id);
		return $savepath;
	}

	function Directory($dir){

		if(is_dir($dir) || @mkdir($dir,0777)){

			//echo $dir."创建成功<br>";

		}else{

			$this->Directory(dirname($dir));
			if(@mkdir($dir,0777)){

				//echo $dir."创建成功<br>";

			}

		}
	}
}

?>