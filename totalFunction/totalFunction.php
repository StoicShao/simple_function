<?php
/*
 * 全国最大中文IT社区
 * http://www.mengfan.club:9000/index/article/id/190.html
 * 慕课网
 * 开源中国社区
 * */
    /*
     * php脚本执行时出现问题，可以通过try，catch去捕获异常
     * */
//
//echo '<meta http-equiv="content-type" content="text/html;charset=utf-8">';echo '<meta http-equiv="content-type" content="text/html;charset=utf-8">';
class service extends Common
{

    function syg(){
//        106.14.159.58

    }
	function vcurl($url, $post = '', $cookie = '', $cookiejar = '', $referer = '',$stime='40',$localhost='0',$header='0')
    {
        $tmpInfo = '';
        $cookiepath = getcwd().'./'.$cookiejar;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        
        //curl_setopt($curl, CURLOPT_PROXY, '');   //厦大
        //curl_setopt($curl, CURLOPT_PROXYUSERPWD, '');
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

        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);//改为0可以打印出域名        

        curl_setopt($curl, CURLOPT_TIMEOUT, $stime);

        curl_setopt($curl, CURLOPT_HEADER, 0);//$header
        //$header改为0可以去除掉头部的http参数，1可以获取响应的参数值

        curl_setopt($curl, CURLOPT_NOBODY, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect:'));
        $tmpInfo = curl_exec($curl);
        $errno = curl_errno($curl);
        curl_close($curl);
        return $tmpInfo;

    }


    /**
	 * 无限分类
	 * @param $items	       数组	    Y
	 * @param $pid	           父级分类 Y
	 */
	public function genTree($items,$pid ='fid_classification_num'){
		$map  = array();
		$tree = array();
		foreach($items as &$it){
			$map[$it['classification_num']] = &$it;
		}
		//数据的ID名生成新的引用索引树
		foreach($items as &$it){
			$parent = &$map[$it[$pid]];
			if($parent){
				$parent['son'][] = &$it;
			}else{
				$tree[] = &$it;
			}
		}
		return $tree;
	}

	/**
	 * 正选反选
	 * @param #all	       按钮控制	          Y
	 * @param .list	       循环下所有子类	  Y
	 */
	public function select(){
//		$('#all').click(function(){
//			if(this.checked){
//				$(".list  :checkbox").prop("checked",true);
//			}else{
//				$(".list  :checkbox").prop("checked",false);
//			}
//		});
//
//		//获取所选的值
//		$('#getValue').click(function(){
//			var  valArr  = new Array;//大写
//			$(".list input[type='checkbox']:checked").each(function(i){
//				valArr[i]   = $(this).val();
//			});
//			var  vals = valArr.join(',');
//		});
	}

	/**
	 * 分页
	 * $where   判断条件
	 * @param  $_GET['page']	       前台需要显示的第几页          Y
	 * @param  $min	                   显示的第几页的那部分数据 	 Y
	 * @param  $count                  共有多少条记录                Y
	 * @param  $page_count             一页显示十条能分成多少页      Y
	 *  /^[0-9]{1,}$/                  正整数的正则表达式
	 */
	public function page(){
		$page   = intval($_GET['page'])?$_GET['page']:1;
		$min    = ($page-1)*10;
 
		$count  = M('User')->count();
		$page_count = ceil($count/10);
		$this->assign('page_count',$page_count);

		$user   =  M('User')->limit($min,10)->select();
		$this->assign('user',$user);
		$this->assign('page',$page);
		//跳转 $_GET['page']，$page/$page_count

	}

	
    //剥除标签    
    function chulistring(){
        // 数据抓取 可以通过模拟表单提交，仿制后台控制文件去接收值
        //原文链接或者参数有时候需要转义 urlencode----urldecode

        // preg_match('/欢迎您/i',$cnt);
        //如果要取到最后面，使用$符号截止

        //取出数组中规定的值      array_slice

        // 删除数组中的第一个元素 array_shift

        //去除空数组              array_filter($a)

        //去除指定key值得元素组   unset($a[1])

        //两个数组组合成一个数组  array_combine

        //notice : 存入数据库时需要转义  addslashes

        $string	= trim(str_replace('&nbsp;','',strip_tags($string)));
        return $string;
    }
    //global
    function action(){
        //php的引用（就是在变量或者函数、对象等前面加上&符号）
        $a = 1;    
        $b = 2;    
        function test_global(){    
            global $a,$b;    
            $a=&$b;//a的值改变,b的值也跟着改变    
            $a=3;    
        }
        test_global();    
        echo $a;    
        echo $b; 
        //值是1和3

        $a = "ABC";
        $b = &$a;
        echo $a;//这里输出:ABC
        echo $b;//这里输出:ABC
        $b = "EFG";
        echo $a;//这里$a的值变为EFG 所以输出EFG
        echo $b;//这里输出EFG

    }



    //利用mysql_insert_id和全局变量进行分表插入
    function InsertTable(){
        error_reporting(7);
        set_time_limit(0);
        global $provide;
        $num=1;
        $provide = 'provide_'.$num;
        ini_set('memory_limit', '1024M');//调整内存

        $sql = "CREATE TABLE `".$provide."` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `品种` text,
          `1` text,
          `2` text,
          `3` text,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
        mysql_query($sql);
        $res = mysql_query("SELECT * FROM provide");
        if($res){
            while($row = mysql_fetch_assoc($res)){
                $result[] = $row;
            }
        }
        foreach ($result as $key => $value) {
            $url    = 'http://nc.mofcom.gov.cn';
            $html   = $this->vcurl($url);
            preg_match_all('/v_PageCount =(.*);/siU',$html,$arr);                      
            foreach($arr as $k => $v){
                $a     =  array();                   
                $sql_i = sprintf('INSERT INTO `%s` (`%s`) VALUES ("%s")',$provide,implode('`,`',array_keys($a)),implode('","',array_values($a)));
                $res_i = mysql_query($sql_i);
                if(mysql_insert_id() >= 1000000){
                    $num++;
                    $provide = 'provide_'.$num;                 
                    $sql = "CREATE TABLE `".$provide."` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `品种`  text,
                          `1` text,
                          `2` text,
                          `3` text,
                          PRIMARY KEY (`id`)
                        ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                        mysql_query($sql);
                }
                if(!$res_i){
                    echo $i."<br>";                    
                }     

            }            
        }
    } 

    /**
     * @param $str
     * @return string
     */
    function  unicodeToUtf8($str){
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
    /**
     * 获取文件编码
     * @param $string
     * @return string
     */
    function getEncoding($string){
        $encoding = mb_detect_encoding($string, array('UTF-8', 'GBK', 'GB2312', 'LATIN1', 'ASCII', 'BIG5', 'iso-8859-1'));
        return strtolower($encoding);
    }
    /**
     * 将任意格式字符串转换为 UTF8 格式
     * @param $string
     * @return string
     */
   function strToUtf8($string){
        $code_type = mb_detect_encoding($string, array("ASCII",'UTF-8',"GB2312","GBK",'BIG5', 'LATIN1', 'iso-8859-1'));
        return iconv($code_type, 'UTF-8', $string);
    }

   function foreach_class($arr='',$pid=0,$lev=0){
        $area = array(
        array('id'=>1,'area'=>'北京','pid'=>0),
        array('id'=>2,'area'=>'广西','pid'=>0),
        array('id'=>3,'area'=>'广东','pid'=>0),
        array('id'=>4,'area'=>'福建','pid'=>0),
        array('id'=>11,'area'=>'朝阳区','pid'=>1),
        array('id'=>12,'area'=>'海淀区','pid'=>1 ),
        array('id'=>21,'area'=>'南宁市','pid'=>2),
        array('id'=>45,'area'=>'福州市','pid'=>4),
        array('id'=>113,'area'=>'亚运村','pid'=>11),
        array('id'=>115,'area'=>'奥运村','pid'=>11),
        array('id'=>234,'area'=>'武鸣县','pid'=>21)
    );
    $list = $this->t($area);
    print_r($list);

   }

   function t($area,$pid=0,$lev=0){
        //递归
        static $list = array();
        foreach($area as $value) {
            if($value['pid'] == $pid){
                //$value[]     = $value;
                $list[]      = $value;
                $this->t($area,$value['id'],$lev+1);
            }
        }
        return $list;
   }

   function info(){
        //依赖注入、容器、反射或控制反转  是框架内的一种编程思想
        //WebSocket  双向通信的网络协议，服务器不再被动的接收数据，当有新的数据产生时会主动推送给客户端
        //事件流 客户端接收新的信息，在服务器用一个插件即时刷新数据库，页面客户端事件流接收通过scoket通讯传过来的新信息
   }

   function Suretime(){
//        js定时器
//        var time=60;
//        var timer=setInterval(function(){
//            time--;
//            $('#').text(time+'秒');
//            if(time<=0){
//                $('#').text('获取验证码');
//            }
//        },1000);
    }

    function mysql(){
//        来源：http://blog.csdn.net/xrt95050/article/details/5556411
//        mysql默认顺序查询 关系型数据库
//        主键：是唯一索引，但每张表只能有一个主键，不能为空，例如id
//        索引：用于mysql的快速查询
//        普通索引：normal 用于优化查询  ALTER TABLE '表名' ADD INDEX '命名的索引名'('字段名');
//        唯一索引：unique 优化查询，插入记录时进行排重  ALTER TABLE '表名' ADD UNIQUE '命名的索引名'('字段名');
//        全文索引：full text
//        单列索引和多列索引：多条件查询时，使用多列索引查询比单列索引效率高
//        ALTER TABLE '表名' ADD INDEX '命名的索引名'('字段名','字段名','字段名');
//        索引的方法：一般使用Btree方法，Hash是在特定条件下的精确查询，有很多限制性

//        两种索引方法
//        btree ,hash
//        hash方法的检索速度比btree要快，但是不适用于范围条件查询，也不适用于排序查询，通配符LIKE操作符会不起作用
//        B-Tree索引可以被用在像=,>,>=,<,<=和BETWEEN这些比较操作符上。而且还可以用于LIKE操作符

//
//        外键：需要先将两张表里有关联作用的字段设置为索引，然后把其中一张表里的索引字段设置为外键，会受另一个表里的索引字段的值变化影响
//        ALTER TABLE '字表' ADD CONSTRAINT '命名的外键名' FOREIGN KEY ('字表的索引字段') REFERENCES parts('主表的索引字段') ON UPDATE CASCADE;
//        在字表里添加一条记录，这个记录的外键字段值在主表里不存在，就不会生成这条记录
//        ON UPDATE CASCADE 加入这个主表更新时，字表里这个字段也会更新 RESTRICT(禁止主表变更)、SET NULL(子表相应字段设置为空)

        //同时操作两台服务器的数据库
        //每对不同服务器上的数据库进行操作，需要在后面加上相应的指定($this->conn.)
        //$this->conn1 = mysql_connect(localhost,'root','root');
        //mysql_query("set names utf8",$this->conn1);

        //$this->conn2 = mysql_connect(localhost2,'root2','root2');
        //mysql_query("set names utf8",$this->conn2);
        //通过id分组合并
        //select id,group_concat(louhao) from tablename group by id
    }

    //mysql cmd操作
    function mysqlcmd(){
        /*
         * 先切换到bin目录下面
         * mysql -u root -proot
         * show databases;          展示库
         * use 库名；                使用哪张库
         * show tables;             展示这库下面有几张表
         *
         * 具体操作
         * repair table 表名；
         * describe 表名;          显示表的结构
         * create database 库名;   建库
         * drop database 库名;     删库
         * 建表时，先use 库名;
         * create table 表名(字段列表);
         * drop table 表名;        删库
         * */

    }

    /*
     * mysql事务，锁*/
    function mysql_use(){
        //结合try{}catch(Exception $e){}去捕捉自行的异常
        //之前先开启事务，利用try和catch去进行事务利用
        //乐观锁：，先给数据表加version字段，先查询出那条记录，获取出version字段,如果要对那条记录进行操作(更新),则先判断此刻version的值是否与刚刚查询出来时的version的值相等，如果相等，则说明这段期间，没有其他程序对其进行操作，则可以执行更新，将version字段的值加1；如果更新时发现此刻的version值与刚刚获取出来的version的值不相等，则说明这段期间已经有其他程序对其进行操作了，则不进行更新操作。

        //悲观锁:一般行锁都是悲观锁，将某条记录查询加锁并进行操作，其他进程在排队等待上个进程的执行，容易产生堵塞，只有能上个进程执行成功释放锁，才可以执行下个进程
    }
    function bingfa(){
        //并发，本身apache有队列
        /*
         * redis队列，mysql自身的事务和锁表，访问数据库时加一道锁文件的门槛
         *
         * 先对奖品给识别号,然后在中奖纪录表中建立索引（唯一索引）,对于中奖用户的所中奖品插入表中，通过索引和数量来查询限制是否超过规定，如果能插入表中再返回结果给用户
         *
         * mysql模拟队列，建立新表来存储用户的请求，然后对那张表再进行集中判断处理
         *
         * */
    }

    function web(){
        //ping 查看域名的ip地址
        //内网之间不同电脑可以访问对面数据库（速度最优）
        //服务器之间可以在phpmyadmin里面添加用户来相互访问数据库
        //服务器不能访问内网本地电脑的数据
    }

    function crossWeb(){
        //跨域
        //数据请求需遵循同源策略，不同域(协议，域名，端口)不能交互
        //js的jsonp会去动态生成一个script标签，利用标签里的src不受同源策略约束来跨域获取数据。
        //var script = document.createElement("script");
        //script.src = "http://www.baidu.com?q=&callback=handleResponse";
        //document.body.insertBefore(script, document.body.firstChild);
        //$.ajax({})的jsonp是jquery把跨域请求封装好的，可以直接调用
    }

    function linux_suoyin(){
        //setup install pip install 安装文件
        //关闭索引
        //cd /usr/local/coreseek && bin/searchd -c etc/conf_505.conf --stop
        //建索引
        //cd /usr/local/coreseek && bin/indexer -c etc/conf_505.conf --all
        //启动索引
        //cd /usr/local/coreseek && bin/searchd -c etc/conf_505.conf

       // windows和linux查看端口被占用情况
        //netstat -aon|findstr “80” 找出所有端口，并查看相应端口的pid，例80端口的pid是123，
        //tasklist|findstr “123”,根据pid找出占用80端口的程序
        //taskkill /t /f /im 进程名.exe或者taskkill /t /f /PID 123 结束这个进程

        //netstat -an 查看所有端口占用情况  或者 netstat -apn|grep 80 直接找到占用80端口的进程
        //lsof -i :80 查看具体占用80端口的进程，找出pid 123
        // ps -aux | grep 123 通过pid查看详细信息
        //kill -9 pid 结束这个进程

        //执行脚本命令时，也可以用php的system,exec,passthru
        //cd 打开目录
        //mkdir 创建自己的文件夹，一般是在home下面
        //ls 遍历查看是否生效，然后cd打开新建的文件
        //chmod 将shell脚本文件放进去，然后编辑777权限 chomd 777 jiaoben.sh
        // ./ jiaoben.sh 执行脚本

        //crontab -e 打开编辑crontab linux定时任务
        //0 02 * * * /home/syg/reboot_sphnix.sh 每天凌晨两点运行shelljiaoben

    }

    function windowsShell(){
        //添加环境变量
        //php可以直接运行
        //redis先开启redis-server,在新窗口连接redis-cli.exe，运行操作

    }

    function redis(){
        //缓存
        //来源：
        //redis环境配置，php_redis扩展安装
//        $redis = new \Redis();         //redis对象
//        $redis->connect("127.0.0.1","6379"); //连接redis服务器
//        $redis->set("test","Hello World");   //set字符串值
        //echo $redis->get("test");die;
    }

    function yuming(){
        //域名绑定
        //打开apache里面的conf里的vhosts.conf,点击编辑器自带的换行，不能自行换行apache会报错，域名配置，重启服务中的Apache

    }

    //大数据量操作时，以一万为界便利循环操作
    function xunhuan(){
        ob_end_clean();//                                                                                                                                                                       放到最顶上，flush刷新php缓存，直接输出，结合使用，不需要等待程序全部执行完才输出
        //ob_flush,windows下面可以不用添加，linux下面必须在flush前面添加
        //exit;
        $seTB   = "article_ww";
        $lastid = $this->getLastId($seTB);
        $step   = 10000;
        for($z = 0;$z<=$lastid;$z+=$step){
            $sql2 = "select * from ".$seTB." where id between ".($z+1)." and ".($z+$step);
            $result = mysql_query($sql2);
            $savepath = 'G:/xstt/qk_en/'.($z+1).'-'.($z+$step);
            while($row = mysql_fetch_assoc($result)){
                $strstring = '';
                foreach($row as $key1=>$value1){
                    $strstring .= "<{".$key1."}>:".$value1."\r\n";
                }
                $strstring .="##@@##";
                $strstring .="\r\n";
                $strstring .="\r\n";
                //echo $strstring;exit;
                file_put_contents($savepath.'.txt',$strstring,FILE_APPEND);//exit;
            }
            mysql_free_result($result);
            echo $sql2."<br>";flush();//exit;
        }
    }

    //环境部署 php,python
    function huanjing(){
        /*
         * 安装phpstudy，模式改为系统模式(自动开启)，设置里面关掉允许目录列表(防止查看文件目录)
         * 删除数据库里的test文件，修改phpmyadmin的文件名，修改数据库密码
         * 删除指定www目录下面的多余文件
         * 在apache里conf下面vhosts.conf文件可以绑定域名
         *
         * python安装2.7
         * 先点击几大exe
         * 安装python命令行环境变量，scripts
         * python 三个安装 python   setup.py  install
         * c:/python27/Scripts
         * 需要特殊安装的文件pip install APScheduler
         *
         * */
    }

    function git(){
        /*
         * 第一次建仓并推
         *  git init //把这个目录变成Git可以管理的仓库
        　　git add README.md //文件添加到仓库
        　　git add . //不但可以跟单一文件，还可以跟通配符，更可以跟目录。一个点就把当前目录下所有未追踪的文件全部add了
        　　git commit -m "first commit" //把文件提交到仓库
        　　git remote add origin 仓库地址 //关联远程仓库
        　　git push -u origin master //把本地库的所有内容推送到远程库上

         * 更新上传
           git status
           git add . //不但可以跟单一文件，还可以跟通配符，更可以跟目录。一个点就把当前目录下所有未追踪的文件全部add了
        　 git commit -m "first commit" //把文件提交到仓库
           git push -u origin master //把本地库的所有内容推送到远程库上

          * 本地、远程仓库合并（需要手动消除冲突，可以借助phpstorm）
            git fetch origin master:temp  从远程的origin仓库的master分支下载到本地，并新建一个temp分支
            git diff temp                   查看temp分支与本地原有分支的不同
            git merge temp          将temp分支和本地分支合并。B的本地代码已经和远程仓库处于同一个版本了，接下来去代码中消除冲突，并提交新版本到远程代码库
            git branch -d temp      删除temp分支
        * */
//        配置phpstorm的Git账户
//        查看生成的公钥
//        cat ~/.ssh/id_rsa.pub
//        自己Git邮箱匹配生成秘钥，并利用秘钥到后台添加SSH-Keys，这条命令的目的是为了让本地机器ssh登录远程机器上的GitHub账户无需输入密码
//        ssh-keygen -t rsa -b 4096 -C "81173844@house365.com"
    }

    //邮箱发送
    function sendMail(){

        require_once "./email.class.php";
        //下面开始设置一些信息
        $smtpserver    = "smtp.163.com";//SMTP服务器
        $smtpserverport= 25;//SMTP服务器端口 465/587 ssl
        $smtpusermail  = "@163.com";//SMTP服务器的用户邮箱
        $smtpemailto   = "@qq.com";//发送给谁(可以填写任何邮箱地址)
        $smtpuser      = "";//SMTP服务器的用户帐号(即SMTP服务器的用户邮箱@前面的信息)
        $smtppass      = "";//SMTP服务器的授权码
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

    //数据到出 txt
    function dc_txt()
    {
        $savepath	= 'E:\\www\\dc_txt\\';
        dirname(__FILE__);
//        file_exists:文件是否存在
//        is_dir:目录是否存在
        mkdir($savepath.'dc_txt');
        $seTB	= "schooldatachouqu.dc_table";
        $lastid	= $this->getLastId($seTB);
        $step	= $this->step;
        $step	= 10000;
        $strstring	= '';
        for($z	= 0;$z<=$lastid;$z+=$step)
        {
            $sql2	= "select * from ".$seTB." where id between ".($z+1)." and ".($z+$step);
            $result	= mysql_query($sql2);
            while($row	= mysql_fetch_assoc($result))
            {
                $strstring   = '';
                $strstring	.='title-标题:'.$row['title']."\r\n";//
                $strstring	.='entitle-外文标题:'.$row['title_en']."\r\n";//
                $strstring	.="###@@@";
                $strstring	.="\r\n";
                $strstring	.="\r\n";
                file_put_contents($savepath.'dc_txt'.'\\'.($z+1).'-'.($z+$step).'.txt',$strstring,FILE_APPEND);
            }
            echo $sql2."<br>";flush();
        }
    }


    //定时器
    function cmd(){
//        windows：php死循环，sleep时间 （不可取）。
//        .bat 文件
//        linux ：crontab，shell脚本
//
//        模拟线程分发
//        cmd中起cmd 1111
    }

    function session(){
        //多台服务器调用一个公共服务器上面的session
//        1.存数据库
//        2.缓存
//        3.把session存入cookie里面 当访问服务器A时，登录成功之后将产生的session信息存放在cookie中；当访问请求分配到服务器B时，服务器B先判断服务器有没有这个session，如果没有，在去看看客户端的cookie里面有没有这个session，如果cookie里面有，就把cookie里面的sessoin同步到web服务器B，这样就可以实现session的同步了。
//        4.使用一台作为用户的登录服务器，当用户登录成功之后，会将session写到当前服务器上，我们通过脚本或者守护进程将session同步到其他服务器上，这时当用户跳转到其他服务器，session一致，也就不用再次登录。

    }
    function jumpWall(){
//       翻墙软件，通过购买海外代理服务器翻墙
//        软件名：Shadowsocks.exe
//        域名：104.225.158.28
//        端口：8990
//        密码：
//        代理端口：1080
    }


    //nginx配置
    function nginx(){
       //vhosts.conf
        server {
              #监听端口
              listen    80;
              #域名配置  一个ip地址只能用一个占用一个端口号，多个域名地址可以共用一个端口
              server_name   rent.365.cn;
              #存放项目文件访问目录
              set $root_path 'F:\phpStudy\PHPTutorial\WWW\New_Rent\frontend\web';
              root $root_path; # 该项要修改为你准备存放相关网页的路径
              #入口文件
              index index.php;
              #路由解析
               location / {
                            # Redirect everything that isn't a real file to index.php
                            try_files $uri $uri/ /index.php?$args;
               }
               #php和nginx解析配置  fastcgi
              location ~ \.php$ {
                  fastcgi_pass   127.0.0.1:9000;
                  fastcgi_index   index.php;
                  include fastcgi.conf;
              }
              #proxy the php scripts to php-fpm
              #是否开启错误日志
              #error_log /usr/local/etc/nginx/logs/rent_error.log;
              #设置静态资源缓存过期时间
              location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$ {
                        expires      30d;
              }
        }
    }
    function linux_action(){
        //find ./ -name "*.conf*"   找出当前文件夹下面含有conf的文件
        //find ../ -name "*.conf*"  找出上级文件夹下面含有conf的文件
        //grep -r "*.conf"          找出内容含有conf的文件
        //i         代表insert ，可以修改内容
        //u         需先esc退出编辑模式，撤回修改内容
        //:q        退出文件
        // 加上反斜杠 / 可以正则匹配内容
    }


    function js_study(){
        // 模拟js表单提交 $('#crmBrokerListForm').attr('action','http://esfadmin.house365.com/module/broker_manage/crm_broker.php?page=1&test=2').submit()
//        查找p元素下，所有class选择器为selected的元素
//        $("p").siblings(".selected")
//        查找div下所有的class选择器为selected的子元素，设为蓝色
//        $("div").children(".selected").css("color", "blue")
//        选择第二个p元素，index 值从 0 开始
//        $("p:eq(1)") 等同于 $("p").eq(1)
//        点击获取相应元素的index值
//        $("li").click(function(){
//            alert($(this).index());
//        });
//        查找b元素的上级元素，parent是单层上一级，parents是所有的上级元素
//        $("b").parent()
//        $("b").parents()
//        添加一个或多个 class 属性类，添加多个类用空格分隔类名，不会移除已存在的 class 属性
//        $(selector).addClass(class1 class2 class3)
//        jq去除空白字符
//        $.trim()
//        js打印
//        console.log()
//        IS_OWNER_CHECK
//        alter table real_house_appeal add (internal_num varchar(50) default 0 comment '内部编号',appeal_qiqaun varchar(50) default 0 comment '丘权号',owner_phone char(11) default 0 comment '业主手机号',property_rights_num varchar(50) default 0 comment '产权证号',property_rights_name varchar(50) default 0 comment '产权人姓名',property_rights_person_id varchar(50) default 0 comment '产权人身份证号')


    }

}