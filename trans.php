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
        $title = '';
        $data[] = [
            'action' => 'RESET',
            'user' => 'GM',
            'content' => '',
            'time_int' => 0
        ];
        $time = 2;
        foreach ($split as $num => $row)
        {
            $row = trim($row);
            if($num == 0)
            {
                if($row{0} == '#')
                {
                    $title = substr($row,1);
                }
            }
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
            $time += ($tmp['action'] == 'TALK' ? 5 : ($tmp['action'] == 'BACK' ? 1 : 3));
            $data[] = $tmp;
        }
        return [
            'data' => $data,
            'title' => $title,
        ];
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
        elseif(preg_match('/^[0-9a-f]{2}$/',$row))
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

//遍历docs文件夹，处理其中的txt
$trans = [];
$open_folder = opendir("./docs");
while($file = readdir($open_folder))
{
    if(preg_match('/(\d+)\.txt$/',$file,$match))
    {
        $transformed = transformer::do_transform(file_get_contents('./docs/'.$file));
        file_put_contents("./json/".$match[1].'.json',
            json_encode($transformed['data'],JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
        $trans[] = [
            'show_name' => $transformed['title'],
            'data' => $match[1].'.json',
            'source_file_name' => $file,
            'origin' => true
        ];
    }
}

echo json_encode($trans,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
