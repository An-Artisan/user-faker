<?php
/**
 * Avatar.php
 * 头像数据
 *
 *
 * @copyright Copyright (c) 2018-2019 http://www.gamerpark.com
 * @link    http://www.gamerpark.com
 * @author  StubbornGrass
 * @email   g1090035743@gmail.com
 * @license 游民公园项目组版权所有
 * @version 1.0
 *
 */

namespace StubbornGrass\UserFaker;
require '../vendor/autoload.php';

use GuzzleHttp\Client;
use QL\QueryList;
set_time_limit(0);

class  Avatar
{

    private $client = null;
    private $url = "https://www.woyaogexing.com/";
    private $timeout = 2.0;
    private $avatarCount = 10000;

    public $i = 7359;

    public function __construct($url = null, $timeout = null, $avatarCount = 10000)
    {
        if ($url) $this->url = $url;
        if ($timeout) $this->timeout = $timeout;
        if ($avatarCount) $this->avatarCount = $avatarCount;
        $client = new Client([
//            // Base URI is used with relative requests
            'base_uri' => $this->url,
            // You can set any number of default request options.
            'timeout' => $this->timeout,
        ]);
        $this->client = $client;
    }


    public function handle($link = "touxiang/nan/2019/891760.html")
    {


        //get请求
        $res = $this->client->request('GET', $link);

        $html = (string)$res->getBody();
        $ql = QueryList::html($html);
        $avatars = $ql->rules(array(
            'image' => array('.tx-img>a>img','src')
        ))->query()->getData(function($item){
            return $item['image'];
        })->all();


        $nextPage = $ql->find('div>.listPage>a')->attrs("href")->all();
        if (!count($nextPage)) {
            return false;
        }
        $nextPage = $nextPage[0];

        /**
         * 查看是否抓取足够头像
         */
        foreach ($avatars as $avatar) {
            if ($this->i > $this->avatarCount) return false;
            if ($avatar != '') $this->write($avatar);
        }
        self::handle($nextPage);
    }

    public function write($content)
    {
        $filepath = 'file/avatar.txt';
        if (file_exists($filepath)) {
            // "当前目录中，文件存在"，追加
            $myfile = fopen($filepath, "a") or die("Unable to open file!");
            fwrite($myfile, $content . "\r\n");
            // 记得关闭流
            fclose($myfile);
        } else {
            // "当前目录中，文件不存在",新写入
            $myfile = fopen($filepath, "w") or die("Unable to open file!");
            fwrite($myfile, $content . "\r\n");
            // 记得关闭流
            fclose($myfile);
        }
        $this->i++;
    }
}

$test = new Avatar();
$test->handle();

