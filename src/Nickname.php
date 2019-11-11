<?php
/**
 * Nickname.php
 * 昵称数据
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

class  Nickname
{

    private $client = null;
    private $url = "http://mingzi.jb51.net/";
    private $timeout = 2.0;
    private $nicknameCount = 10000;
    public $i = 0;

    public function __construct($url = null, $timeout = null, $nicknameCount = 10000)
    {
        if ($url) $this->url = $url;
        if ($timeout) $this->timeout = $timeout;
        if ($nicknameCount) $this->nicknameCount = $nicknameCount;
        $client = new Client([
//            // Base URI is used with relative requests
            'base_uri' => $this->url,
            // You can set any number of default request options.
            'timeout' => $this->timeout,
        ]);
        $this->client = $client;
    }


    public function handle($link = "haoting/26761.html")
    {


        //get请求
        $res = $this->client->request('GET', $link);

        $html = (string)$res->getBody();
        $ql = QueryList::html($html);
        $nicknames = $ql->find('div>.article>p')->texts();
        $nextPage = $ql->find('div>.pagelast>a')->attrs("href");
        if (!count($nextPage)) {
            echo "暂无数据";
            return false;
        }
        $nextPage = $nextPage[0];


        foreach ($nicknames as $nickname) {
            if ($this->i > $this->nicknameCount) return false;
            if ($nickname != '') $this->write($nickname);
        }

        self::handle($nextPage);
    }

    public function write($content)
    {
        $filepath = '../nickname/nickname.txt';
        if (file_exists($filepath)) {
            //"当前目录中，文件存在"，追加
            $myfile = fopen($filepath, "a") or die("Unable to open file!");
            fwrite($myfile, $content . "\r\n");
            //记得关闭流
            fclose($myfile);
        } else {
            //"当前目录中，文件不存在",新写入
            $myfile = fopen($filepath, "w") or die("Unable to open file!");
            fwrite($myfile, $content . "\r\n");
            //记得关闭流
            fclose($myfile);
        }
        $this->i++;
    }
}

$test = new Nickname();
$test->handle();

