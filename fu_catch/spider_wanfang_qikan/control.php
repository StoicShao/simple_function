<?php
class control extends gather_base
{

	function __construct(){
		parent::__construct( );
		$this->database_id  = $GLOBALS['database_id'];
		$this->tasks_id 	= $GLOBALS['tasks_id'];
		$this->spider_tasks = 'spider_tasks';
		$this->spider_wanfang_ku    = 'spider_wanfang_qikan';
		$this->spider_field_search  = 'spider_field_search';
		$this->spider_field_alias	= 'spider_field_alias';
		$this->ku_type		= 'perio-perio_artical';
		$this->pagesize		= '20';
		//$path = $this->zip($this->tasks_id,$this->spider_wanfang_ku);
		//echo $path;die;

	}
	//获取对方网站的session
	function getSession(){
		
	}

	//抓取内容的方法
	function getListPage(){
		//抓取第一页内容
		$url    = $this->wf_canshu($this->tasks_id,$this->ku_type,'0',$this->pagesize);
		$html	= $this->vcurl($url,'','','');
		$html	= json_decode($html,true);
		$total_count = $html['totalRow'];
		$total_page  = $html['pageTotal'];
		//$total_page  = 5;
		//判断内容是否有值
		if($total_count > 0){
			//把总数目存入task表
			$sql = "UPDATE ".$this->spider_tasks." SET total_count='".$total_count."' WHERE id=".$this->tasks_id;
			$res = mysql_query($sql);
			if($res){

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
						$content = $html;
					}else{
						sleep(2);
						$url    = $this->wf_canshu($this->tasks_id,$this->ku_type,$i,$this->pagesize);
						$content	= $this->vcurl($url,'','','');
						$content	= json_decode($content,true);
					}
					$articles 		= $content['pageRow'];
					$articles_num	= count($articles);
					for($k=0;$k<$articles_num;$k++){
						$data = '';
						$data['tasks_id']= $this->tasks_id;
						//标题
						$data['title']	= $this->chulistring($articles[$k]['title']);
						//英文标题
						$data['entitle']= $this->chulistring($articles[$k]['trans_title']);
						//作者
						if(count($articles[$k]['authors_name']) >=2){
							$author 	= '';
							for($j=0;$j<count($articles[$k]['authors_name']);$j++){
								$author .=  $this->chulistring3($articles[$k]['authors_name'][$j]).'@@@';
							}
						}else{
							$author		 = $this->chulistring($articles[$k]['authors_name']);
						}
						$data['author']	 = $author;
						//第一作者
						//$data['first_authors']= $this->chulistring($articles[$ii]['first_authors']);
						//作者英文名
						if(count($articles[$k]['trans_authors']) >=2){
							$enauthor	 = '';
							for($jj=0;$jj<count($articles[$k]['trans_authors']);$jj++){
								$enauthor.=  $this->chulistring3($articles[$k]['trans_authors'][$jj]).'@@@';
							}
						}else{
							$enauthor    = $this->chulistring($articles[$k]['trans_authors']);
						}
						$data['enauthor']= $enauthor;
						//作者机构
						if(count($articles[$k]['authors_unit']) >= 2){
							$jg			 = '';
							for($jjj=0;$jjj<count($articles[$k]['authors_unit']);$jjj++){
								$jg 	.=  $this->chulistring3($articles[$k]['authors_unit'][$jjj]).'@@@';
							}
						}else{
							$jg			 = $this->chulistring($articles[$k]['authors_unit']);
						}
						$data['jg']	 	 = $jg;
						//期刊名
						$data['kan_name']= $this->chulistring3($articles[$k]['perio_title']);
						//期刊名英文
						$data['enkan_name']= $this->chulistring3($articles[$k]['perio_title_en']);
						//doi
						$data['doi']	 = $this->chulistring3($articles[$k]['doi']);
						//年，卷期
						$data['year']	 = $this->chulistring3($articles[$k]['publish_year']);
						$data['qi']	 	 = $this->chulistring3($articles[$k]['issue_num']);
						//摘要
						$data['abstract']= $this->chulistring($articles[$k]['summary']);
						//基金
						$data['fund']	 = str_replace('%','@@@',$this->chulistring3($articles[$k]['fund_info']));

						//关键词
						if(count($articles[$k]['keywords']) >= 2){
							$keywords 	 = '';
							for($jjjj=0;$jjjj<count($articles[$k]['keywords']);$jjjj++){
								$keywords.=  $this->chulistring3($articles[$k]['keywords'][$jjjj]).'@@@';
							}
						}else{
							$keywords	  = $this->chulistring3($articles[$k]['keywords']);
						}
						$data['keywords'] = $keywords;

						//分类号
						if(count($articles[$k]['subject_classcode_level']) >= 2){
							$class_code 	 = '';
							for($kk=0;$kk<count($articles[$k]['subject_classcode_level']);$kk++){
								$class_code.=  $this->chulistring3($articles[$k]['subject_classcode_level'][$kk]).'@@@';
							}
						}else{
							$class_code	  = $this->chulistring3($articles[$k]['subject_classcode_level']);
						}
						$data['class_code']= $class_code;

						//出版日期
						if($articles[$k]['abst_webdate']['time']){
							$data['published_time']= date('Y-m-d H:i:s',$this->chulistring3($articles[$k]['abst_webdate']['time'])/1000);
						}else{
							$data['published_time']= '';
						}

						//页码
						$data['page_code']= $this->chulistring3($articles[$k]['page_range']);
						//页数
						$data['page_num'] = $this->chulistring3($articles[$k]['page_cnt']);
						//下载次数

						//被引频次

						//文章唯一号
						$data['articles_id']= $this->chulistring3($articles[$k]['article_id']);
						sleep(2);
						//参考文献数
						$refdoc_cnt   		  = $this->chulistring3($articles[$k]['refdoc_cnt']);
						$data['reference_num']= $refdoc_cnt;

						//获取参考文献
						if($refdoc_cnt >0){
							$refdoc_cnt		= ceil($refdoc_cnt/10);
							$articles_string    = '';
							for($iii=1;$iii<=$refdoc_cnt;$iii++){
								$article_url	= 'http://www.wanfangdata.com.cn/graphical/turnpage.do?type=reference&id='.$articles[$k]['article_id'].'&number='.$iii;
								$article_html	= json_decode($this->vcurl($article_url),true);
								$every_article  = '';
								for($iiii=0;$iiii<count($article_html[0]);$iiii++){
									if(!$article_html[0][$iiii]['Type']){
										$article_html[0][$iiii]['Type'] = 'J';
									}
									$where = '['.(($iii-1)*10+1+$iiii).']';
									if($article_html[0][$iiii]['Author']){
										$where .= $article_html[0][$iiii]['Author'].'@@';
									}else{
										$where .= '佚名@@';
									}
									if($article_html[0][$iiii]['Title']){
										$where .= $article_html[0][$iiii]['Title'].'['.$article_html[0][$iiii]['Type'].']'.'@@';
									}else{
										$where .= $article_html[0][$iiii]['Publisher'].'['.$article_html[0][$iiii]['Type'].']'.'@@';
									}
									if($article_html[0][$iiii]['Periodical']){
										$where .= $article_html[0][$iiii]['Periodical'].','.$article_html[0][$iiii]['Year'].',('.$article_html[0][$iiii]['Issue'].'):'.$article_html[0][$iiii]['Page'].'@@'.'doi:'.$article_html[0][$iiii]['DOI'];
									}else{
										$where .= $article_html[0][$iiii]['Publisher'].':'.$article_html[0][$iiii]['Year'];
									}

									$every_article.= $where.'@@@@';
								}
								$articles_string  .= $every_article;

							}
							//参考文献
							$data['reference']	  = $this->chulistring3(str_replace('%',',',$articles_string));

						}else{
							$data['reference']    = '';
						}

						$data['catch_page']		  = $i;
						$sql_is = 'SELECT count(1) as row_num FROM '.$this->spider_wanfang_ku.' WHERE `tasks_id`='.$this->tasks_id.' AND `articles_id`="'.$data['articles_id'].'"';
						$res_is = mysql_query($sql_is);
						if($res_is){
							$row_num = mysql_fetch_assoc($res_is);
							if($row_num['row_num'] <= 0){
								//print_r($data);die;
								$sqll = sprintf('INSERT INTO '.$this->spider_wanfang_ku.' (`%s`) VALUES ("%s")',implode('`,`',array_keys($data)),implode('","',array_values($data)));
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
						//失败
						$this->fail($this->tasks_id);
					}
					//抓取完成
					if($i == $total_page){						
						$this->txt();
						$this->success($this->tasks_id);
					}
				}
			}else{
				//失败
				$this->fail($this->tasks_id);
			}

		}else{
			//搜索值为空
			$this->null($this->tasks_id);
		}

	}

	function txt(){
		$arr = $this->gettotal($this->tasks_id);
		if($arr['complete_count'] < $arr['total_count']){
			$this->getListPage($arr['complete_count']);
		}else{
			$savepath = $this->savepath($this->spider_wanfang_ku,$this->tasks_id);
			$lastid	= $this->getLastId($this->spider_wanfang_ku);
			$step	= 10000;
			$strstring	= '';
			for($z	= 0;$z<=$lastid;$z+=$step)
			{
				$sql2	= "select * from ".$this->spider_wanfang_ku." where id between ".($z+1)." and ".($z+$step);
				$result	= mysql_query($sql2);
				while($row	= mysql_fetch_assoc($result))
				{
					$strstring   = '';
					$strstring	.='title-题名:'.$row['title']."\r\n";//
					$strstring	.='entitle-外文题名:'.$row['entitle']."\r\n";//
					$strstring	.='author-作者:'.$row['author']."\r\n";//
					$strstring	.='enauthor-作者英文:'.$row['enauthor']."\r\n";//
					$strstring	.='jg-机构:'.$row['jg']."\r\n";//
					$strstring	.='kan_name-刊名:'.$row['kan_name']."\r\n";//
					$strstring	.='enkan_name-刊名英文:'.$row['enkan_name']."\r\n";//
					$strstring	.='doi-doi:'.$row['doi']."\r\n";//
					$strstring	.='year-年份:'.$row['year']."\r\n";//
					$strstring	.='qi-期:'.$row['qi']."\r\n";//
					$strstring	.='abstract-摘要:'.$row['abstract']."\r\n";//
					$strstring	.='enabstract-摘要英文:'.$row['enabstract']."\r\n";//
					$strstring	.='fund-基金:'.$row['fund']."\r\n";//
					$strstring	.='keywords-关键词:'.$row['keywords']."\r\n";//
					$strstring	.='en_keywords-关键词英文:'.$row['en_keywords']."\r\n";//
					$strstring	.='class_code-分类号:'.$row['class_code']."\r\n";//
					$strstring	.='published_time-出版时间:'.$row['published_time']."\r\n";//
					$strstring	.='page_code-页码:'.$row['page_code']."\r\n";//
					$strstring	.='page_num-页数:'.$row['page_num']."\r\n";//
					$strstring	.='amount-下载次数:'.$row['dow_num']."\r\n";//
					$strstring	.='cited-被引频次:'.$row['use_num']."\r\n";//
					$strstring	.='reference_num-文献数目:'.$row['reference_num']."\r\n";//
					$strstring	.='reference-文献:'.$row['reference']."\r\n";//
					$strstring	.="###@@@";
					$strstring	.="\r\n";
					$strstring	.="\r\n";
					file_put_contents($savepath.$this->tasks_id.'\\'.($z+1).'-'.($z+$step).'.txt',$strstring,FILE_APPEND);
				}
			}
			$path = $this->zip($this->tasks_id,$this->spider_wanfang_ku);
			if($path){
				$this->savezip($this->tasks_id,$path);
			}else{
				//失败修改状态
				$this->fail($this->tasks_id);
			}
		}
	}


	//根据一页内容里面的参数导出固定格式的方法
	function getContent(){


	}
}
?>