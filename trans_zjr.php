<?php

/**
 * 这玩意负责把我们手写的东西转换成能播放的json
 */
class transformer
{
    public static function do_transform($text)
    {
        $row_regex = '/^<\d+:\d+:\d+>/';
        $split = explode("\n",$text);
        $data = [];
        $data[] = [
            'action' => 'RESET',
            'user' => 'GM',
            'content' => '',
            'time_int' => 0
        ];
        $time = 2;
        foreach ($split as $num => $row)
        {
            if(!preg_match($row_regex , $row)){
                continue;
            }
            $curr_row = $row;
            $ptr_list = $num + 1;
            while(isset($split[$ptr_list]) && !preg_match($row_regex,$split[$ptr_list])){
                $curr_row .= "<br/>\n".$split[$ptr_list];
                $ptr_list ++;
            }
            //echo $num,"\n";
            foreach (self::trans_row($row) as $tmp){
                //处理一下时间吧
                $tmp['time_int'] = $time;
                $time += ($tmp['action'] == 'TALK' ? 5 : ($tmp['action'] == 'BACK' ? 1 : 3));
                $data[] = $tmp;
            }
        }
        return $data;
    }

    /**
     * 负责处理一行。
     * @param string $row
     * @return array
     */
    public static function trans_row($row)
    {
        $row = trim($row);
        if(empty($row)){
            return [];
        }
        $return = [];
        $event  = [
            'action' => '',
            'user' => '',
            'content' => '',
        ];
        $allowed_actions = [
            '100'  => 'TALK',
            '300' => 'RESET',
            '202' => 'MOVE',
            '316'  => 'CHECKRESULT',
            '203' => 'BACK',
            '306' => 'CREDIT',
            '207' => 'FIRST',
            'EMOTE' => '',
            //'' => 'GOTO',
            'NEXT' => '',
            'CLEAR' => '',
            '201' => 'LOAD',
            'LAST' => '',
            '205' => '',
            '204' => '',
            '206' => '',
        ];
        if(!preg_match('/<\d+:\d+:\d+>\[(\d+)\](\w+)(?:\{(\w+)\})?(.+)$/mi' , $row,$match)){
            echo $row;exit;
            return [];
        }
        if(!isset($allowed_actions[$match[1]])){
            var_dump($match);
            die($row);
        }
        $action = $allowed_actions[$match[1]];
        if(empty($action)){
            return [];
        }
        if($action == 'RESET'){
            $tmp = $event;
            $tmp['action'] = 'RESET';
            $return[] = $tmp;
        }else{
            $tmp = $event;
            $tmp['action'] = $action;
            if(isset($match[3])){
                $tmp['user'] = $match[3];
            }
            switch($action){
                case 'MOVE':
                    $tmp['content'] = self::trans_coordinate(substr($match[2],3));
                    break;
                case 'LOAD':
                    $tmp['content'] = self::trans_coordinate(substr($match[4],3));
                    break;
                default:
                    $tmp['content'] = $match[4];
            }
            $return[] = $tmp;
        }
        return $return;
    }

    public static function trans_coordinate($str){
        if(!$str){
            return '';
        }
        $return = '';
        foreach(str_split($str,3) as $coord){
            $return .= (dechex(ord($coord[1]) - 64)) . (dechex(ord($coord[2]) - 64));
        }
        return $return;
    }
}
//遍历docs文件夹，处理其中的txt
$trans = [];
$open_folder = opendir("./zjr");
while($file = readdir($open_folder))
{
    if(preg_match('/(\S+)\.zjr$/',$file,$match))
    {
        echo "$file\n";
        $transformed = transformer::do_transform(mb_convert_encoding(file_get_contents('./zjr/'.$file),'UTF-8','GB18030'));
        echo json_encode($transformed,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);exit;
        file_put_contents("./json/zj-".$match[1].'.json',
            json_encode($transformed,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
        $trans[] = [
            'show_name' => '浙江-'.$match[1],
            'data' => 'zj-'.$match[1].'.json',
            'source_file_name' => $file,
            'origin' => true
        ];
    }
}

echo json_encode($trans,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
