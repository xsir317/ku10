<?php

/**
 * 这玩意负责把我们手写的东西转换成能播放的json
 */
class transformer
{
    public static function do_transform($text)
    {
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
            'TALK'  => '',
            'RESET' => '',
            'MOVE' => '', 
            'CHECKRESULT'  => '',
            'EMOTE' => '',
            'GOTO' => '',
            'BACK' => '',
            'FIRST' => '',
            'CREDIT' => '',
            'NEXT' => '',
            'CLEAR' => '',
            'LOAD' => '',
            'LAST' => ''
        ];
        if(!preg_match('/<\d+:\d+:\d+>\[(\w+)\](?:\{(\w+)\})?(.+)$/i' , $row,$match)){
            return [];
        }
        $action = $match[1];
        if(!isset($allowed_actions[$action])){
            die($row);
        }
        if($action == 'RESET'){
            $pick_len = intval(substr($match[3],3,3)) * 2;
            $tmp = $event;
            $tmp['action'] = 'RESET';
            $return[] = $tmp;
            $tmp = $event;
            $tmp['action'] = 'LOAD';
            $tmp['content'] = self::trans_coordinate(substr($match[3],6,$pick_len));
            if($tmp['content']){
                $return[] = $tmp;
            }
        }else{
            $tmp = $event;
            $tmp['action'] = $action;
            if(isset($match[2])){
                $tmp['user'] = $match[2];
            }
            $tmp['content'] = in_array($action , ['MOVE' , 'LOAD']) ? self::trans_coordinate($match[3]) : $match[3];
            $return[] = $tmp;
        }
        return $return;
    }

    public static function trans_coordinate($str){
        if(!$str){
            return '';
        }
        $return = '';
        foreach(str_split($str,2) as $coord){
            $return .= (dechex(ord($coord[0]) - 64)) . (dechex(ord($coord[1]) - 64));
        }
        return $return;
    }
}
//遍历docs文件夹，处理其中的txt
$trans = [];
$open_folder = opendir("./orc");
while($file = readdir($open_folder))
{
    if(preg_match('/(\S+)\.orc$/',$file,$match))
    {
        echo "$file\n";
        $transformed = transformer::do_transform(iconv('GB18030','UTF-8',file_get_contents('./orc/'.$file)));
        file_put_contents("./json/".$match[1].'.json',
            json_encode($transformed,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
        $trans[] = [
            'show_name' => 'sosei_'.$match[1],
            'data' => $match[1].'.json',
            'source_file_name' => $file,
            'origin' => true
        ];
    }
}

echo json_encode($trans,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
