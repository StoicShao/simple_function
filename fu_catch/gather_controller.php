<?php
session_start();
set_time_limit(0);
//ob_end_clean();
error_reporting("all~notice");
include_once('gather_config.php');
include_once('gather_base.php');
//echo '<meta http-equiv="content-type" content="text/html;charset=utf-8">';
global $tasks_id;
global $database_id;
$tasks_id = $_REQUEST['tasks_id'];
if(!$tasks_id){
    $tasks_id = $argv[1];
}
$tasks_id = '224';
$sql     = 'SELECT d.id,d.name FROM `spider_tasks` as t LEFT JOIN `spider_database` as d ON d.id=t.database_id WHERE t.id ='.$tasks_id;
$res     = mysql_query($sql);
if($res){
    $row    = mysql_fetch_assoc($res);
    //任务接收到，修改状态码
    $sqll   = 'UPDATE `spider_tasks` SET `status_code`=6000 WHERE id='.$tasks_id;
    $ress   = mysql_query($sqll);

    if($ress){
        //找到相应库的子库
        $sqlll      = 'SELECT d.map_param FROM `spider_source_type_search` as s RIGHT JOIN `spider_source_type` as d ON s.source_type_id=d.id WHERE s.tasks_id='.$tasks_id;
        $resss      = mysql_query($sqlll);
        if($resss){
            $rowww      = mysql_fetch_assoc($resss);
            if($rowww['map_param']){
                $control    = 'spider_'.$row['name'].'_'.$rowww['map_param'];

            }else{
                $control    = 'spider_'.$row['name'];
            }
            $action		= 'getListPage';
            include_once($control.'\\'.'control.php');
            $obj		= new control();
            $obj->$action();
        }else{
            $this->fail($tasks_id);
        }
    }else{
        $this->fail($tasks_id);
    }
}

?>