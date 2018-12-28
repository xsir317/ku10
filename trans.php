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
        foreach ($split as $row)
        {
            $row = trim($row);
            if($row == '')
            {
                continue;
            }
            $tmp = self::trans_row($row);
            if(!$tmp)
            {
                continue;
            }
            //处理一下时间吧
            $tmp['time_int'] = $time;
            $time += ($tmp['action'] == 'TALK' ? 4 : 2);
            $data[] = $tmp;
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
        $return  = [
            'action' => '',
            'user' => 'GM',
            'content' => '',
        ];
        if($row{0} == '#')
        {
            return false;
        }
        if(substr($row,0,4) == 'Load')
        {
            $return['action'] = 'LOAD';
            $return['content'] = trim(substr($row,4));
        }
        elseif($row == 'back')
        {
            $return['action'] = 'BACK';
        }
        elseif(strlen($row) == 2)
        {
            $return['action'] = 'MOVE';
            $return['content'] = $row;
        }
        else
        {
            $return['action'] = 'TALK';
            $return['content'] = $row;
        }
        return $return;
    }
}

$trans_json = transformer::do_transform(file_get_contents('./intro.txt'));

echo json_encode($trans_json,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);