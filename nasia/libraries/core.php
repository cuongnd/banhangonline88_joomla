<?php

/**
 * Created by PhpStorm.
 * User: cuong
 * Date: 9/21/2017
 * Time: 3:54 PM
 */
require_once PATH_ROOT . DS . "libraries/phpExcelReader/Excel/reader.php";
require_once PATH_ROOT . DS . "libraries/HighchartsPHP-master/src/HighchartOption.php";
require_once PATH_ROOT . DS . "libraries/HighchartsPHP-master/src/HighchartJsExpr.php";
require_once PATH_ROOT . DS . "libraries/HighchartsPHP-master/src/HighchartOptionRenderer.php";
require_once PATH_ROOT . DS . "libraries/HighchartsPHP-master/src/Highchart.php";
use Ghunti\HighchartsPHP\Highchart;
use Ghunti\HighchartsPHP\HighchartJsExpr;

class core
{
    public static $core;

    public static function getInstance()
    {
        if (!static::$core) {
            static::$core = new core();
        }
        return static::$core;
    }
    static $list=array();
    private static function loatbotinhieuloi($list)
    {
        static::$list=$list;
        $option=new stdClass();
        $option->sogia=10;
        $list_do_xang=array();
        $list_stander=array();
        $max=0;
        for($i=0;$i<count($list);$i++){
            if($i>0){
                $prev=$list[$i-1];
                $pre_time=$prev[0];
                $pre_item_muc_xang=$prev[1];
                $current=$list[$i];
                $current_time=$current[0];
                $current_item_muc_xang=$current[1];

            }


        }
        $list=array_values($list);
        return $list;
    }
    public function readFileExcel()
    {
        $path = PATH_ROOT . DS . "stories/log data fuel sensor.xls";
        // ExcelFile($filename, $encoding);

        $data = new Spreadsheet_Excel_Reader();

// Set output Encoding.
        $data->setOutputEncoding('CP1251');

        $data->read($path);
        array_pop($data->sheets);

        //This will create a highchart chart with the jquery js engine
        $chart = new Highchart();
        $chart->chart = array(
            'renderTo' => 'container',
            'type' => 'line',
            'width' => "1000",
            'zoomType'=>'x'
        );

        $chart->xAxis = array(
            'type'=>"datetime"
        );


        foreach ($data->sheets as $key => $sheet) {
            $cells = $sheet['cells'];
            array_shift($cells);
            $list = [];
            for ($i=0;$i<count($cells);$i++) {
                $cell=$cells[$i];
                $record = $cell[1];
                $record = explode(',', $record);
                $dateTime = $cell[2];
                $dateTime = strtotime($dateTime); // Unix timestamp
                $element6=(int)$record[6];
                $item=array(
                    $dateTime,$element6
                );
                //$date = $cell[2];
                $list[]=$item;
                //$list[]=(int)$record[5];
                //$list[]=(int)$record[6];
                //$list[]=(int)$record[5];
                //$list[]=(int)$record[6];
                if($i>1000){
                    //break;
                }
            }
            usort($list, array($this, "cmp"));
            $chart->tooltip=array(
                headerFormat=>'<span style="font-size:10px">The point value at {point.x} is {point.y}</span><table>'
            );
            $chart->series[] = array(
                'name' => 'c-' . $key,
                'data' => $list
            );

        }

        return $chart;

    }
    public function getData()
    {
        $path = PATH_ROOT . DS . "stories/log data fuel sensor.xls";
        // ExcelFile($filename, $encoding);

        $data = new Spreadsheet_Excel_Reader();

// Set output Encoding.
        $data->setOutputEncoding('CP1251');

        $data->read($path);
        array_pop($data->sheets);
        $return_list=new stdClass();

        foreach ($data->sheets as $key => $sheet) {
            $cells = $sheet['cells'];
            array_shift($cells);
            $list3 = [];
            $list5 = [];
            $list6 = [];
            for ($i=0;$i<count($cells);$i++) {
                $cell=$cells[$i];
                $record = $cell[1];
                $record = explode(',', $record);
                $dateTime = $cell[2];
                $dateTime = strtotime($dateTime); // Unix timestamp
                $element3=(int)$record[3];
                $element5=(int)$record[5];
                $element6=(int)$record[6];
                $item3=array(
                    $dateTime,$element3
                );
                $item5=array(
                    $dateTime,$element5
                );
                $item6=array(
                    $dateTime,$element6
                );
                //$date = $cell[2];
                $list3[]=$item3;
                $list5[]=$item5;
                $list6[]=$item6;
                //$list[]=(int)$record[5];
                //$list[]=(int)$record[6];
                //$list[]=(int)$record[5];
                //$list[]=(int)$record[6];
                if($i>1000){
                    //break;
                }
            }
            usort($list3, array($this, "cmp"));
            usort($list5, array($this, "cmp"));
            usort($list6, array($this, "cmp"));
            $key_return=$key;
            $return_list->{$key_return}[3]=$list3;
            $return_list->{$key_return}[5]=$list5;
            $return_list->{$key_return}[6]=$list6;

        }
        return $return_list;

    }
    public function getDataChuanHoaByKey($current_key)
    {
        $path = PATH_ROOT . DS . "stories/log data fuel sensor.xls";
        // ExcelFile($filename, $encoding);

        $data = new Spreadsheet_Excel_Reader();

// Set output Encoding.
        $data->setOutputEncoding('CP1251');

        $data->read($path);
        array_pop($data->sheets);
        $car_data=new stdClass();

        foreach ($data->sheets as $key => $sheet) {
            if($key==$current_key){
                $cells = $sheet['cells'];
                array_shift($cells);
                $list = [];
                for ($i=0;$i<count($cells);$i++) {
                    $cell=$cells[$i];
                    $record = $cell[1];
                    $record = explode(',', $record);
                    $dateTime = $cell[2];
                    $dateTime = strtotime($dateTime); // Unix timestamp
                    $element6=(int)$record[6];
                    $item=array(
                        $dateTime,$element6
                    );
                    //$date = $cell[2];
                    $list[]=$item;
                    //$list[]=(int)$record[5];
                    //$list[]=(int)$record[6];
                    //$list[]=(int)$record[5];
                    //$list[]=(int)$record[6];
                    if($i>1000){
                        //break;
                    }
                }
                usort($list, array($this, "cmp"));
                $list=$this->loatbotinhieuloi($list);
                $car_data->{$key}=$list;
                return  $car_data;
            }


        }
        return null;

    }
    function cmp($a, $b)
    {
        return strcmp($a[0], $b[0]);
    }
    public function loaiboxungnhieu()
    {
        $path = PATH_ROOT . DS . "stories/log data fuel sensor.xls";
        // ExcelFile($filename, $encoding);

        $data = new Spreadsheet_Excel_Reader();

// Set output Encoding.
        $data->setOutputEncoding('CP1251');

        $data->read($path);
        array_pop($data->sheets);

        //This will create a highchart chart with the jquery js engine
        $chart = new Highchart();
        $chart->zoomType="x";
        $chart->chart = array(
            'renderTo' => 'loaiboxungnhieu',
            'type' => 'line',
            'width' => "1000",
            'zoomType'=>'x'
        );

        $chart->xAxis = array(
            'type'=>"datetime",
            'title'=>array(
                'text'=>'Date'
            )
        );


        $chart->yAxis = array(
            'title' => array(
                'text' => 'Snow depth (m)'
            ),
            'min'=>0
        );
        $chart->tooltip =array(
            'headerFormat'=>'<b>{series.name}</b><br>',
            'pointFormat'=>'{point.x:%e. %b}: {point.y:.2f} m'
        );
        $chart->plotOptions =array(
            'spline'=>array(
                'marker'=>array(
                    "enabled"=>true
                )
            )
        );
        foreach ($data->sheets as $key => $sheet) {
            $cells = $sheet['cells'];
            array_shift($cells);
            $list = [];
            foreach ($cells as $cell) {
                $record = $cell[1];
                $record = explode(',', $record);
                $date = $cell[2];
                $list[]=(int)$record[6];
                //$list[]=(int)$record[5];
                //$list[]=(int)$record[6];
                //$list[]=(int)$record[5];
                //$list[]=(int)$record[6];

            }
            $list=static::loatbotinhieuloi($list);
            $chart->series[] = array(
                'name' => 'c-' . $key,
                'data' => $list
            );
            break;

        }

        return $chart;

    }
}