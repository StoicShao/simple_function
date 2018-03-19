<?php
class control extends gather_base
{

	function __construct()
	{
		parent::__construct( );
		$this->tasks_id 	   = $GLOBALS['tasks_id'];
		$this->spider_cssci_ku = spider_cssci;
		$this->pagesize 	   = 20;
		//$path = $this->zip($this->tasks_id,$this->spider_cssci_ku);die;
		//$this->page		= $_REQUEST['page'];
		//$this->zip($this->tasks_id,$this->spider_cssci_ku);die;

	}
	//获取对方网站的session
	function getSession()
	{
		$tmp_url  = 'http://cssci.nju.edu.cn/control/controllers.php';
		$post	  = 'control=user_control&action=check_user_online&rand=0.15508920689082006';
		$tmp_c	  = $this->vcurl2($tmp_url,$post);
		preg_match_all('/Set-Cookie:(.*)path=/siU',$tmp_c,$arr);

		$is_session = trim(strip_tags($arr[1][0]));

		if(!empty($is_session)){
			return $is_session;
		}else{
			$this->fail();
		}
	}

	//获取总页数(循环次数)
	function getTotalPage(){

	}

	//抓取一页内容的方法
	function getListPage(){
		$url	    = $this->cssci_canshu($this->tasks_id,$this->pagesize,1);
		$content	= $this->vcurl($url,'', $this->getSession(),'','','','','fdsf');
		if(preg_match('/{"contents":null,"totalnum":null,"pagenum":null,"pagenow":null,"state":"wrong_year"}/siU',$content)){
			//搜索内容为空
			$this->null();
		}else{
			//去除bom头
			$string    = chr(239).chr(187).chr(191);
			$content   = ltrim($content,$string);
			$content   = json_decode($content,true);

			$total_count= $content['totalnum'];
			$total_page = $content['pagenum'];
			//判断内容是否有值
			if($total_count > 0){
				//把总数目存入task表
				$sql = "UPDATE ".$this->spider_tasks." SET total_count='".$total_count."' WHERE id=".$this->tasks_id;
				$res = mysql_query($sql);
				if($res){
					//判断是否任务中断重启
					$arr = $this->gettotal($this->tasks_id);
					if($arr['complete_count'] && ($arr['complete_count'] < $arr['total_count'])){
						$value = $arr['complete_count'];
						$value = ceil($value/$this->pagesize);
					}else{
						$value = 1;
					}
					//获取总页数进行循环，每页显示记录数在url中
					for($i=$value;$i<=$total_page;$i++){
						if($i == 1){
							$content = $content;
						}else{
							sleep(2);
							$url        = $this->cssci_canshu($this->tasks_id,$this->pagesize,$i);
							$content    = $this->vcurl($url,'', $this->getSession(),'','','','','fdsf');
							$content    = ltrim($content,$string);
							$content    = json_decode($content,true);
						}
						//列表页
						$articles 		= $content['contents'];
						$articles_num	= count($articles);
						//echo $articles_num;die;
						for($k=0;$k<$articles_num;$k++){
							$sno = $articles[$k]['sno'];
							//$sno = $articles[7]['sno'];
							sleep(1);
							//抓取每一个详情页
							$url	= 'http://cssci.nju.edu.cn/control/controllers.php?control=search&action=source_id&id='.$sno.'&rand=0.9888618150235109';
							$html	= $this->vcurl($url,'','','','','','','dfdsafsd');
							if(preg_match('/{"contents":null,"totalnum":null,"pagenum":null,"pagenow":null,"state":"wrong_year"}/siU',$html)){
								$html= $this->vcurl($url,'',$this->getSession(),'','','','','dfdsafsd');
							}
							$html = ltrim($html,$string);
							$html = json_decode($html,true);
							$data = $this->getjson($html,$sno);
							$data['tasks_id']   = $this->tasks_id;
							$data['catch_page']	= $i;
							//被引用频次
							$use_url  = $this->cssci_use($data['first_author'],$data['title']);
							$use_html = $this->vcurl($use_url,'',$this->getSession(),'','','','','dfdsafsd');
							$use_html = ltrim($use_html,$string);
							$use_html = json_decode($use_html,true);
							$data['use_num'] = $use_html['totalnum'];

							$sql_is = 'SELECT count(1) as row_num FROM '.$this->spider_cssci_ku.' WHERE `tasks_id`='.$this->tasks_id.' AND `sno`="'.$sno.'"';
							$res_is = mysql_query($sql_is);
							if($res_is){
								$row_num = mysql_fetch_assoc($res_is);
								if($row_num['row_num'] <= 0){
									$sqll = sprintf('INSERT INTO '.$this->spider_cssci_ku.' (`%s`) VALUES ("%s")',implode('`,`',array_keys($data)),implode('","',array_values($data)));
									$ress = mysql_query($sqll);
									if(!$ress){
										$this->getListPage();
									}
								}
							}
						}
						if($ress){
							//计算百分比，更新已完成数
							if(($i*$this->pagesize) >= $total_count){
								$up_num = $total_count;
							}else{
								$up_num = $i*$this->pagesize;
							}
							$this->percent($up_num,$this->tasks_id);
						}else{
							//任务失败
							$this->fail($this->tasks_id);
						}
						//抓取完成
						if($i == $total_page){
							
							$this->txt();
							$this->success($this->tasks_id);
						}

					}
				}else{
					//任务失败
					$this->fail($this->tasks_id);
				}

			}else{
				//任务失败
				$this->null($this->tasks_id);
			}

		}


	}

	//根据一页内容里面的参数导出固定格式的方法
	function getContent(){


	}

	function getjson($content,$sno){
		$data		  =	 '';
		//sno号
		$data['sno']  =  $sno;
		//标题
		$data['title'] =  trim(addslashes($content[contents][0][lypm]));
		//标题拼音
		$data['title_py'] =  trim(addslashes($content[contents][0][lypmp]));
		//标题英文
		$data['entitle'] =  trim(addslashes($content[contents][0][blpm]));
		//作者
		$data['author'] =   addslashes(str_replace('aaa','/',trim($content[contents][0][authors],'aaa')));
		//机构
		$data['jg'] =  str_replace('aaa','@@@',trim(addslashes($content[contents][0][authors_jg])));
		//作者邮编地址
		$data['author_address'] =  str_replace('aaa','@@@',trim(addslashes($content[contents][0][authors_address])));
		//文献号
		$data['wx_num'] =  trim(addslashes($content[contents][0][wzlx]));
		//文献
		if($data['wx_num'] == 1){
			$data['wx'] = '论文';
		}elseif($data['wx_num'] == 2){
			$data['wx'] = '综述';
		}elseif($data['wx_num'] == 3){
			$data['wx'] = '评论';
		}elseif($data['wx_num'] == 4){
			$data['wx'] = '传记资料';
		}elseif($data['wx_num'] == 5){
			$data['wx'] = '报告';
		}elseif($data['wx_num'] == 9){
			$data['wx'] = '其他';
		}
		//期刊号
		$data['qkno']  =  trim(addslashes($content[contents][0][qkno]));
		//中图分类号
		$data['zt_num'] =  trim(addslashes($content[contents][0][xkdm1]));
		//$data['xkdm2'] =  trim(addslashes($content[contents][0][xkdm2]));
		//页数
		$data['ym']    =  trim(addslashes($content[contents][0][ym]));
		//$data['ywsl']  =  trim(addslashes($content[contents][0][ywsl]));
		//关键词
		$data['byc']   =  str_replace('aaa','@@@',trim(addslashes($content[contents][0][byc])));
		//$data['dcbj']  =  trim(addslashes($content[contents][0][dcbj]));
		//基金
		$data['fund']  =  trim(addslashes($content[contents][0][xmlb]));
		//$data['jjlb']  =  trim(addslashes($content[contents][0][jjlb]));
		//$data['lrymc'] =  trim(addslashes($content[contents][0][lrymc]));
		//$data['skdm']  =  trim(addslashes($content[contents][0][skdm]));

		//$data['yjdm']  =  trim(addslashes($content[contents][0][yjdm]));
		//学科号
		$data['xk_num'] =  trim(addslashes($content[contents][0][xkfl1]));
		//学科
		$res2 = mysql_query("SELECT `bz` FROM zd_xkfl1 WHERE title=".$data['xk_num']."");
		if($res2){
			$result2    =  mysql_fetch_assoc($res2);
			$data['xk'] = trim($result2['bz']);
		}

		//$data['ycflag']=  trim(addslashes($content[contents][0][ycflag]));

		//$data['xkfl2'] =  trim(addslashes($content[contents][0][xkfl2]));
		$data['qkdm']  =  trim(addslashes($content[contents][0][qkdm]));
		//年
		$data['year']  =  trim(addslashes($content[contents][0][nian]));
		//卷
		$data['juan']  =  trim(addslashes($content[contents][0][juan]));
		//期
		$data['qi']    =  trim(addslashes($content[contents][0][qi]));
		//期刊
		$data['qkmc']  =  trim(addslashes($content[contents][0][qkmc]));
		//$data['wzlx_z']=  trim(addslashes($content[contents][0][wzlx_z]));
		//第一作者
		$data['first_author'] =  trim(addslashes($content[author][0][zzmc]));
		//第一机构
		//$data['first_jg']     =  trim(addslashes($content[author][0][txdz]));
		$data['first_jg']     =  trim(addslashes($content[author][0]['jgmc'].$content[author][0]['bmmc']));

		$arr	 = $content['author'];
		$num	 = count($arr);
		//作者机构信息
		$message = '';
		for($ii=0;$ii<$num;$ii++){
			if($arr[$ii][bmmc]){
				$people[$ii] = '['.trim(addslashes($arr[$ii][zzmc])).']'.trim(addslashes($arr[$ii][jgmc])).'.'.trim(addslashes($arr[$ii][bmmc]));
			}else{
				$people[$ii] = '['.trim(addslashes($arr[$ii][zzmc])).']'.trim(addslashes($arr[$ii][jgmc]));
			}
			$message	    .=  $people[$ii]."/";
		}
		$data['message']     =  addslashes(rtrim($message,'/'));
		//参考文献
		$reference 	   = '';
		$arr1		   = $content['catation'];
		$reference_num = count($arr1);
		for($ii=0;$ii<$reference_num;$ii++){
			$reference .= '@@@'.($ii+1).'.'.$arr1[$ii]['ywzz'].'.'.$arr1[$ii]['ywpm'];
			if($arr1[$ii]['ywcbs']){
				$reference .= ':'.$arr1[$ii]['ywcbs'].','.$arr1[$ii]['ywnd'];
			}
			if($arr1[$ii]['ywym']){
				$reference .= ':'.$arr1[$ii]['ywym'];
			}
		}
		$data['reference']  =  addslashes(ltrim($reference,'@@@'));
		return $data;

	}



	function txt(){
		$arr = $this->gettotal($this->tasks_id);
		if($arr['complete_count'] < $arr['total_count']){
			$this->getListPage($arr['complete_count']);
		}else{
//			$savepath	= 'E:\\www\\'.$this->spider_cssci_ku.'\\upload\\';
//			mkdir($savepath);
//			mkdir($savepath.$this->tasks_id);
			$savepath = $this->savepath($this->spider_cssci_ku,$this->tasks_id);
			$lastid	= $this->getLastId($this->spider_cssci_ku);
			$step	= 10000;
			//$strstring	= '';
			for($z	= 0;$z<=$lastid;$z+=$step)
			{
				$sql2	= "select * from ".$this->spider_cssci_ku." where id between ".($z+1)." and ".($z+$step);
				$result	= mysql_query($sql2);
				while($row	= mysql_fetch_assoc($result))
				{
					$strstring  = '';
					$strstring	.='文章唯一号:'.$row['sno']."\r\n";//
					$strstring	.='标题:'.$row['title']."\r\n";//
					$strstring	.='标题拼音:'.$row['title_py']."\r\n";//
					$strstring	.='英文标题:'.$row['entitle']."\r\n";//
					$strstring	.='作者:'.$row['author']."\r\n";//
					$strstring	.='第一作者:'.$row['first_author']."\r\n";//
					$strstring	.='作者机构:'.$row['jg']."\r\n";//
					$strstring	.='第一机构:'.$row['first_jg']."\r\n";//
					$strstring	.='作者地址邮编:'.$row['author_address']."\r\n";//
					$strstring	.='文献号:'.$row['wx_num']."\r\n";//
					$strstring	.='文献类型:'.$row['wx']."\r\n";//
					$strstring	.='期刊号:'.$row['qkno']."\r\n";//
					$strstring	.='中图类号:'.$row['zt_num']."\r\n";//
					//$strstring	.='xkdm2:'.$row['xkdm2']."\r\n";//
					$strstring	.='页数:'.$row['ym']."\r\n";//
					//$strstring	.='ywsl:'.$row['ywsl']."\r\n";//
					$strstring	.='关键词:'.$row['byc']."\r\n";//
					//$strstring	.='dcbj:'.$row['dcbj']."\r\n";
					$strstring	.='基金项目:'.$row['fund']."\r\n";//
					//$strstring	.='jjlb:'.$row['jjlb']."\r\n";//
					//$strstring	.='lrymc:'.$row['lrymc']."\r\n";//
					//$strstring	.='skdm:'.$row['skdm']."\r\n";//
					//$strstring	.='yjdm:'.$row['yjdm']."\r\n";
					$strstring	.='学科号:'.$row['xk_num']."\r\n";//
					$strstring	.='学科类型:'.$row['xk']."\r\n";//
					//$strstring	.='ycflag:'.$row['ycflag']."\r\n";//
					//$strstring	.='xkfl2:'.$row['xkfl2']."\r\n";
					$strstring	.='qkdm:'.$row['qkdm']."\r\n";
					$strstring	.='年:'.$row['year']."\r\n";
					$strstring	.='卷:'.$row['juan']."\r\n";
					$strstring	.='期:'.$row['qi']."\r\n";
					$strstring	.='来源期刊:'.$row['qkmc']."\r\n";
					//$strstring	.='wzlx_z-期刊类型号:'.$row['wzlx_z']."\r\n";
					$strstring	.='作者信息:'.$row['message']."\r\n";
					$strstring	.='被引频次:'.$row['use_num']."\r\n";
					$strstring	.='参考文献:'.$row['reference']."\r\n";
					$strstring	.="###@@@";
					$strstring	.="\r\n";
					$strstring	.="\r\n";
					file_put_contents($savepath.$this->tasks_id.'\\'.($z+1).'-'.($z+$step).'.txt',$strstring,FILE_APPEND);
				}
			}
			$path = $this->zip($this->tasks_id,$this->spider_cssci_ku);
			if($path){
				$this->savezip($this->tasks_id,$path);
			}else{
				//失败修改状态
				$this->fail($this->tasks_id);
			}
		}
	}

}
?>