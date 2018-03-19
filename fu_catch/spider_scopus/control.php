<?php
class control extends gather_base
{

	function __construct()
	{
		parent::__construct( );
		$this->tasks_id 	= $GLOBALS['tasks_id'];
		$this->spider_scopus_ku = spider_scopus;
		$this->pagesize 	= 20;

		//$this->scopus_canshu($this->tasks_id);die;

	}
	//获取对方网站的session
	function getSession(){

	}

	//获取总页数(循环次数)
	function getTotalPage(){

	}

	//抓取一页内容的方法
	function getListPage(){
		$cookiefile = 'scopus.txt';
		$url 		= 'https://www.scopus.com/search/form.uri?display=basic';
		$content	= $this->vcurl($url,'','',$cookiefile);
		preg_match_all('/id="sid" name="sid" value="(.*)"/siU',$content,$sid);
		$sid	= $sid[1][0];
		preg_match_all('/id="txGid" name="txGid" value="(.*)"/siU',$content,$txGid);
		$txGid	= $txGid[1][0];
		preg_match_all('/id="sl" name="sl" value="(.*)"/siU',$content,$sl);
		$sl		= $sl[1][0];
		$data   = $this->scopus_canshu($this->tasks_id,$sid,$txGid,$sl);
		$url    = $data['url'];
			//echo $url;die;
		$content= $this->vcurl($url,'','',$cookiefile);
		//print_r($content);die;
		preg_match_all('/class="resultsCount">(.*)<\/span>/siU',$content,$resultsCount);
		$resultsCount	= trim(strip_tags($resultsCount[1][0]));
		$resultsCount	= str_replace(',','',$resultsCount);
		//echo $resultsCount;die;
		if($resultsCount > 0){
			if($resultsCount >= 2000){
				$total_page   = 2000;
			}else{
				$total_page   = $resultsCount;
			}
			//把总数目存入task表
			$sql = "UPDATE ".$this->spider_tasks." SET total_count='".$total_page."' WHERE id=".$this->tasks_id;
			$res = mysql_query($sql);
			if($res){
				$url = 'https://www.scopus.com/results/handle.uri';
				$post= $this->post($sid,$data['where'],$sl,$resultsCount,$data['filed1'],$data['filed2']);
				$html= $this->vcurl($url,$post,'',$cookiefile);
				//计算百分比，更新已完成数
				$this->percent($total_page,$this->tasks_id);
				//抓取完成				
				$this->csv($html);
				$this->success($this->tasks_id);

			}else{
				//失败
				$this->fail($this->tasks_id);
			}

		}else{
			//搜索值为空
			$this->null($this->tasks_id);
		}
	}

	//根据一页内容里面的参数导出固定格式的方法
	function getContent(){


	}

	function csv($content){
		$arr = $this->gettotal($this->tasks_id);
		if($arr['complete_count'] < $arr['total_count']){
			$this->getListPage();
		}else{
//			$savepath = 'E:\\www\\'.$this->spider_scopus_ku.'\\upload\\';
//			mkdir($savepath);
//			mkdir($savepath.$this->tasks_id);
			$savepath = $this->savepath($this->spider_scopus_ku,$this->tasks_id);
			file_put_contents($savepath.$this->tasks_id.'\\scopus.csv',$content,FILE_APPEND);
			$path = $this->zip($this->tasks_id,$this->spider_scopus_ku);
			if($path){
				$this->savezip($this->tasks_id,$path);
			}else{
				//失败修改状态
				$this->fail($this->tasks_id);
			}
		}
	}

	function post($sid,$string,$sl,$resultsCount,$field1,$field2){
		$post = 'alertPopUp=false&sot=b&sid='.$sid.'&sdt=b&s='.urlencode($string).'&sl='.$sl.'&sort=plf-f&stem=t&src=s&rebrandResultsPage=false&searchWithinResultsDefault=t&news=&_selectedOpenAccessClusterCategories=on&_selectedOpenAccessClusterCategories=on&_selectedYearClusterCategories=on&_selectedYearClusterCategories=on&_selectedYearClusterCategories=on&_selectedYearClusterCategories=on&_selectedYearClusterCategories=on&_selectedYearClusterCategories=on&_selectedYearClusterCategories=on&_selectedYearClusterCategories=on&_selectedYearClusterCategories=on&_selectedYearClusterCategories=on&clsYearCount=5&_selectedAuthorClusterCategories=on&_selectedAuthorClusterCategories=on&_selectedAuthorClusterCategories=on&_selectedAuthorClusterCategories=on&_selectedAuthorClusterCategories=on&_selectedAuthorClusterCategories=on&_selectedAuthorClusterCategories=on&_selectedAuthorClusterCategories=on&_selectedAuthorClusterCategories=on&_selectedAuthorClusterCategories=on&clsAuthnameCount=5&scla=%E6%90%9C%E7%B4%A2%E7%BB%93%E6%9E%9C%E6%95%B0&_selectedSubjectClusterCategories=on&_selectedSubjectClusterCategories=on&_selectedSubjectClusterCategories=on&_selectedSubjectClusterCategories=on&_selectedSubjectClusterCategories=on&_selectedSubjectClusterCategories=on&_selectedSubjectClusterCategories=on&_selectedSubjectClusterCategories=on&_selectedSubjectClusterCategories=on&_selectedSubjectClusterCategories=on&clsSubareaCount=5&sclsb=%E6%90%9C%E7%B4%A2%E7%BB%93%E6%9E%9C%E6%95%B0&_selectedDocTypeClusterCategories=on&_selectedDocTypeClusterCategories=on&_selectedDocTypeClusterCategories=on&_selectedDocTypeClusterCategories=on&_selectedDocTypeClusterCategories=on&_selectedDocTypeClusterCategories=on&_selectedDocTypeClusterCategories=on&_selectedDocTypeClusterCategories=on&_selectedDocTypeClusterCategories=on&_selectedDocTypeClusterCategories=on&clsDocTypeCount=5&_selectedSourceClusterCategories=on&_selectedSourceClusterCategories=on&_selectedSourceClusterCategories=on&_selectedSourceClusterCategories=on&_selectedSourceClusterCategories=on&_selectedSourceClusterCategories=on&_selectedSourceClusterCategories=on&_selectedSourceClusterCategories=on&_selectedSourceClusterCategories=on&_selectedSourceClusterCategories=on&clsSrctitleCount=5&scls=%E6%90%9C%E7%B4%A2%E7%BB%93%E6%9E%9C%E6%95%B0&_selectedKeywordClusterCategories=on&_selectedKeywordClusterCategories=on&_selectedKeywordClusterCategories=on&_selectedKeywordClusterCategories=on&_selectedKeywordClusterCategories=on&_selectedKeywordClusterCategories=on&_selectedKeywordClusterCategories=on&_selectedKeywordClusterCategories=on&_selectedKeywordClusterCategories=on&_selectedKeywordClusterCategories=on&clsKeyCount=5&sclk=%E6%90%9C%E7%B4%A2%E7%BB%93%E6%9E%9C%E6%95%B0&clsAffilCount=9&_selectedCountryClusterCategories=on&_selectedCountryClusterCategories=on&_selectedCountryClusterCategories=on&_selectedCountryClusterCategories=on&_selectedCountryClusterCategories=on&_selectedCountryClusterCategories=on&_selectedCountryClusterCategories=on&_selectedCountryClusterCategories=on&_selectedCountryClusterCategories=on&_selectedCountryClusterCategories=on&clsDocCntryCount=5&sclc=%E6%90%9C%E7%B4%A2%E7%BB%93%E6%9E%9C%E6%95%B0&_selectedSourceTypeClusterCategories=on&_selectedSourceTypeClusterCategories=on&_selectedSourceTypeClusterCategories=on&_selectedSourceTypeClusterCategories=on&_selectedSourceTypeClusterCategories=on&_selectedSourceTypeClusterCategories=on&_selectedSourceTypeClusterCategories=on&clsSrctypeCount=6&_selectedLanguageClusterCategories=on&_selectedLanguageClusterCategories=on&_selectedLanguageClusterCategories=on&_selectedLanguageClusterCategories=on&_selectedLanguageClusterCategories=on&_selectedLanguageClusterCategories=on&_selectedLanguageClusterCategories=on&_selectedLanguageClusterCategories=on&_selectedLanguageClusterCategories=on&_selectedLanguageClusterCategories=on&clsLangCount=5&scll=%E6%90%9C%E7%B4%A2%E7%BB%93%E6%9E%9C%E6%95%B0&displayClusteringCountFlag=f&refinedSearchString='.urlencode(urlencode($string)).'&sortOrderFlag=f&groupCheckBox=on&oldSelectAllCheckBox=false&selectAllCheckBox=true&_selectAllCheckBox=on&_selectPageCheckBox=on&exportRadio=on&selectedCitationInformationItemsAll=on&selectedBibliographicalInformationItemsAll=on&selectedAbstractInformationItemsAll=on&selectedFundInformationItemsAll=on&selectedOtherInformationItemsAll=on&selectedCitationInformationItems=_Authors_&selectedCitationInformationItems=_Title_&selectedCitationInformationItems=_Year_&selectedCitationInformationItems=_EID_&selectedCitationInformationItems=_SourceTitle_&selectedCitationInformationItems=_Volume_Issue_ArtNo_PageStart_PageEnd_PageCount_&selectedCitationInformationItems=_CitedBy_&selectedCitationInformationItems=_DocumentType_Source_&selectedCitationInformationItems=_DOI_&selectedCitationInformationItems=_ACCESSTYPE_&selectedBibliographicalInformationItems=_Affiliations_&selectedBibliographicalInformationItems=_ISSN_ISBN_CODEN_&selectedBibliographicalInformationItems=_PubMedID_&selectedBibliographicalInformationItems=_Publisher_&selectedBibliographicalInformationItems=_Editors_&selectedBibliographicalInformationItems=_LanguageOfOriginalDocument_&selectedBibliographicalInformationItems=_CorrespondenceAddress_&selectedBibliographicalInformationItems=_AbbreviatedSourceTitle_&selectedAbstractInformationItems=_Abstract_&selectedAbstractInformationItems=_AuthorKeywords_&selectedAbstractInformationItems=_IndexKeywords_&selectedFundInformationItems=_Number_&selectedFundInformationItems=_Acronym_&selectedFundInformationItems=_Sponsor_&selectedFundInformationItems=_Text_&selectedOtherInformationItems=_Manufacturers_Tradenames_&selectedOtherInformationItems=_ChemicalsCAS_MolecularSequenceNumbers_&selectedOtherInformationItems=_ConferenceName_ConferenceDate_ConferenceLocation_ConferenceCode_Sponsors_&selectedReferenceInformationItems=_References_&exportTypeSelection=on&emailFormat=on&DOC_DISPLAY_LINK_COUNT=&isAbsExpanded=false&selectedEIDs=2-s2.0-85040865500&_selectedEIDs=on&selectedEIDs=2-s2.0-85039945798&_selectedEIDs=on&selectedEIDs=2-s2.0-85040824239&_selectedEIDs=on&selectedEIDs=2-s2.0-85040463252&_selectedEIDs=on&selectedEIDs=2-s2.0-85040536578&_selectedEIDs=on&selectedEIDs=2-s2.0-85040507101&_selectedEIDs=on&selectedEIDs=2-s2.0-85040441144&_selectedEIDs=on&selectedEIDs=2-s2.0-85040774273&_selectedEIDs=on&selectedEIDs=2-s2.0-85040605811&_selectedEIDs=on&selectedEIDs=2-s2.0-85040953133&_selectedEIDs=on&selectedEIDs=2-s2.0-85032840274&_selectedEIDs=on&selectedEIDs=2-s2.0-85040746909&_selectedEIDs=on&selectedEIDs=2-s2.0-85029514508&_selectedEIDs=on&selectedEIDs=2-s2.0-85039703372&_selectedEIDs=on&selectedEIDs=2-s2.0-85031907187&_selectedEIDs=on&selectedEIDs=2-s2.0-85039425353&_selectedEIDs=on&selectedEIDs=2-s2.0-85039695617&_selectedEIDs=on&selectedEIDs=2-s2.0-85039701139&_selectedEIDs=on&selectedEIDs=2-s2.0-85039457835&_selectedEIDs=on&selectedEIDs=2-s2.0-85039787522&_selectedEIDs=on&displayPerPageFlag=f&resultsPerPage=20&documentJumpToPageDefault=t&endPage='.ceil($resultsCount/20).'&currentPage=1&count='.$resultsCount.'&scount=0&pageselecttotal=0&cc=10&offset=1&nextPageOffset=21&prevPageOffset=&partialQuery=&sortField=RelevanceSortButton&resultsTab=&currentSource=s&oldResultsPerPage=20&clustering=&sortClusterField=&oldScls=&oldScla=&oldSclc=&oldSclsb=&ss=plf-f&ws=r-f&ps=r-f&ref=&clickedLink=export&citeCnt=&mciteCt=&img=&tgt=&nlo=&nlr=&nls=&cs=r-f&contextBox=&origin=resultslist&selectDeselectAllAttempt=clicked&oneClickExport=%7B%22Format%22%3A%22CSV%22%2C%22SelectedFields%22%3A%22+Authors++Title++Year++EID++SourceTitle++Volume+Issue+ArtNo+PageStart+PageEnd+PageCount++CitedBy++DocumentType+Source++DOI++ACCESSTYPE++Affiliations++ISSN+ISBN+CODEN++PubMedID++Publisher++Editors++LanguageOfOriginalDocument++CorrespondenceAddress++AbbreviatedSourceTitle++Abstract++AuthorKeywords++IndexKeywords++Number++Acronym++Sponsor++Text++Manufacturers+Tradenames++ChemicalsCAS+MolecularSequenceNumbers++ConferenceName+ConferenceDate+ConferenceLocation+ConferenceCode+Sponsors++References+Link+%22%2C%22View%22%3A%22SpecifyFields%22%7D&zone=exportDropDown&recordid=&relpos=&pageEIDs=2-s2.0-85040865500%212-s2.0-85039945798%212-s2.0-85040824239%212-s2.0-85040463252%212-s2.0-85040536578%212-s2.0-85040507101%212-s2.0-85040441144%212-s2.0-85040774273%212-s2.0-85040605811%212-s2.0-85040953133%212-s2.0-85032840274%212-s2.0-85040746909%212-s2.0-85029514508%212-s2.0-85039703372%212-s2.0-85031907187%212-s2.0-85039425353%212-s2.0-85039695617%212-s2.0-85039701139%212-s2.0-85039457835%212-s2.0-85039787522&allSourceClusterCategories=Quality+Of+Life+Research%23%23%23Lancet%23%23%23SAE+Technical+Papers%23%23%23Nature%23%23%23Plos+One%23%23%23Science%23%23%23Notes+And+Queries%23%23%23Health+And+Quality+Of+Life+Outcomes%23%23%23Journal+Of+Cleaner+Production%23%23%23International+Journal+Of+Life+Cycle+Assessment&allAuthorClusterCategories=1%23%23%237102638119%23%23%2357197738610%23%23%2355574195446%23%23%237005266421%23%23%237004059381%23%23%237005727149%23%23%2316940772000%23%23%2336013369000%23%23%237103084807&allCountryClusterCategories=United+States%23%23%23United+Kingdom%23%23%23Germany%23%23%23Canada%23%23%23China%23%23%23Australia%23%23%23Japan%23%23%23France%23%23%23Italy%23%23%23Netherlands&allYearClusterCategories=2018%23%23%232017%23%23%232016%23%23%232015%23%23%232014%23%23%232013%23%23%232012%23%23%232011%23%23%232010%23%23%232009&allDocTypeClusterCategories=ar%23%23%23cp%23%23%23re%23%23%23ch%23%23%23no%23%23%23le%23%23%23ed%23%23%23sh%23%23%23bk%23%23%23ip&allSubjectClusterCategories=MEDI%23%23%23ENGI%23%23%23SOCI%23%23%23AGRI%23%23%23ARTS%23%23%23BIOC%23%23%23PSYC%23%23%23NURS%23%23%23ENVI%23%23%23MATE&allLanguageClusterCategories=English%23%23%23German%23%23%23French%23%23%23Chinese%23%23%23Spanish%23%23%23Russian%23%23%23Japanese%23%23%23Portuguese%23%23%23Italian%23%23%23Polish&allKeywordClusterCategories=Human%23%23%23Article%23%23%23Humans%23%23%23Female%23%23%23Male%23%23%23Quality+Of+Life%23%23%23Adult%23%23%23Priority+Journal%23%23%23Aged%23%23%23Major+Clinical+Study&allAffiliationClusterCategories=60016849%23%23%2360014232%23%23%2360015481%23%23%2360022148%23%23%2360002746%23%23%2360027550%23%23%2360025778%23%23%2360011520%23%23%2360012311%23%23%2360008088&allSourceTypeClusterCategories=j%23%23%23p%23%23%23b%23%23%23k%23%23%23d%23%23%23r%23%23%23Undefined&st1='.$field1.'&st2='.$field2.'&citedByJson=&extZone=&extOrigin=resultslist&originId=SC&selectedSources=&extSearchType=';
		return $post;
	}

}
?>