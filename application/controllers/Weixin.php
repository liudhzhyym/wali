<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: henbf
 * Date: 2017/5/7
 * Time: 13:06
 */
class Weixin extends CI_Controller{
    function __construct()
    {
        parent::__construct();
        $this->load->library('CI_Wechat');

    }

    protected function getList($key) {
        //$key = '白夜追凶';
        $seach = file_get_contents('http://so.360kan.com/index.php?kw=' . $key);
        $szz = '#js-playicon" title="(.*?)"\s*data#';
        $szz1 = '#a href="(.*?)" class="g-playicon js-playicon"#';
        $szz2 = '#<img src="(.*?)" alt="(.*?)" \/>[\s\S]+?</a>\n</div>#';
        $szz3 = '#(<b>(.*?)</b><span>(.*?)</span></li></ul>)?<ul class="index-(.*?)-ul g-clear">(\n\s*)?<li>(\n\s*)?<b>类型：</b>(\n\s*)?<span>(.*?)</span>#';
        $szz4 = '#<span class="playtype">(.*?)</span>#';
        $szz5 = '#href="(.*?)" class="btn#';
        preg_match_all($szz, $seach, $sarr);
        preg_match_all($szz1, $seach, $sarr1);
        preg_match_all($szz2, $seach, $sarr2);
        preg_match_all($szz3, $seach, $sarr3);
        preg_match_all($szz4, $seach, $sarr4);
        preg_match_all($szz5, $seach, $sarr5);
        $one = $sarr[1];
        $two = $sarr2[1];
        $three = $sarr3[3];
        $si = $sarr1[1];
        $wu = $sarr4[1];
        $liu = $sarr5[1]; 
        $info = array(
            $one,
            $two,
            $three,
            $si,
            $wu,
            $liu,
        );
        //print_r($info);

        $listUrl = $si[0];

        $tvinfo = file_get_contents($listUrl);
        $tvzz = '#<div class="num-tab-main g-clear\s*js-tab"\s*(style="display:none;")?>[\s\S]+?<a data-num="(.*?)" data-daochu="to=(.*?)" href="(.*?)">[\s\S]+?</div>#';
        $tvzz1 = '#<a data-num="(.*?)" data-daochu="to=(.*?)" href="(.*?)">#';
        $bflist = '#<a data-daochu(.*?) href="(.*?)" class="js-site-btn btn btn-play"></a>#';
        $jianjie = '#<p class="item-desc js-open-wrap">(.*?)</p>#';
        $biaoti = '#<h1>(.*?)</h1>#';
        $pan = '#<h2 class="title g-clear">(.*?)</h2>#';
        $pan1 = '#<h2 class="g-clear">(.*?)</h2>#';
        $zytimu = "#<ul class=\"list w-newfigure-list g-clear js-year-page\" style=\"display:block;\">\r\n                (.*?)\r\n            </ul>#";
        preg_match_all($jianjie, $tvinfo, $jjarr);
        preg_match_all($tvzz, $tvinfo, $tvarr);
        preg_match_all($pan, $tvinfo, $ptvarr);
        preg_match_all($pan1, $tvinfo, $ptvarr1);
        preg_match_all($bflist, $tvinfo, $tvlist);
        preg_match_all($biaoti, $tvinfo, $btarr);
        preg_match_all($zytimu, $tvinfo, $zybtarr);
        //print_r($tvarr);
        // print_r($tvlist);
        // $mvsrc = $tvlist[2][0];
        // $jian = $jjarr[1][0];
        // $timu = $btarr[1][0];
        // $panduan = $ptvarr[1][0];
        // $panduan1 = $ptvarr1[1][0];
        // $zybiaoti = $zybtarr[1][0];
        // $mvsrc1 = str_replace('http://cps.youku.com/redirect.html?id=0000028f&url=', '', "$mvsrc");
        $zcf = implode('', $tvarr[0]);
        preg_match_all($tvzz1, $zcf, $tvarr1);
        $jishu = $tvarr1[1];
        $b = $tvarr1[3];
        // $much = 1;

        // $info = array(
        //     $mvsrc,
        //     $jian,
        //     $timu,
        //     $panduan,
        //     $panduan1,
        //     $zybiaoti,
        //     $mvsrc1,
        //     $jishu,
        //     $b,
        // );

        $base = 'http://jx.vgoodapi.com/jx.php?url=';
        $infoList = array();
        foreach($jishu as $index => $value) {
            $name = $value;
            $url = $b[$index];
            $url = $base . $url;
            $txt = "<a href='{$url}'>{$key} {$name}</a>";
            $infoList[] = $txt;
        }
        $infoList = array_slice($infoList, 0 , 5);
        $html = implode("  ",$infoList);
        return $html;
    }

    public function test() {
        $key = '白夜追凶';
        $html = $this->getList($key);
        echo $html;
        //print_r($infoList);
    }

    /**
     *微信入口方法，对微信端进入的数据进行响应
     */
    public function index(){
        $this->ci_wechat->valid();
        $type = $this->ci_wechat->getRev()->getRevType();
        $content = $this->ci_wechat->getRevContent();
        $openid = $this->ci_wechat->getRevFrom();
        $time = $this->ci_wechat->getRevCtime();
        $enent = $this->ci_wechat->getRevEvent();


        /*
         * 对微信端的时间类型进行响应
         * */
        switch($type){
            /*
             * 如果用户给我们发送文字消息，我们的响应内容如下
             * */
            case Wechat::MSGTYPE_TEXT;

                $html = $this->getList($content);
                $this->ci_wechat->text($html)->reply();
                break;

            /*
             * 如果用户发送图片信息，就将
             * 图片信息保存到服务器上面
             * */
            case Wechat::MSGTYPE_IMAGE:
                // $picinfo= $this->ci_wechat->getRevPic();
                // $out = file_get_contents($picinfo['picurl']);
                // $imageName = $time.rand(1,9);
                // $image="./upload/"."$imageName".".jpg";
                // file_put_contents($image,$out);
                // $guestbookinfo=[
                //     'g_openid' => $openid,
                //     'g_pictureid' => $picinfo['mediaid'],
                //     'g_pictureurl' => "/upload/"."$imageName".".jpg",
                //     'g_type' => 'images',
                //     'g_time' => $time
                // ];
                // if($this->Wxguestbook_model->addGuestbook($guestbookinfo)){
                //     $this->ci_wechat->text('好吧，实话告诉你，其实我是在套路你的图片来了')->reply();break;
                // }else{
                //     $this->ci_wechat->text('系统异常，请联系管理员')->reply();
                // }
                break;
            case Wechat::MSGTYPE_VOICE:
                $this->ci_wechat->text('很抱歉，公众号不接收语音类消息')->reply();
                break;
            case Wechat::MSGTYPE_VIDEO:
                $this->ci_wechat->text('很抱歉，公众号不接收视频类消息')->reply();
                break;
            case Wechat::MSGTYPE_EVENT:
//                 switch ($enent['event']){
//                     case Wechat::EVENT_SUBSCRIBE:
//                         $this->load->model('Wxconcern_model');
//                         $this->config->load('wechat');
//                         $replay = $this->config->item('sub');
//                         $data = [
//                             'C_openid' => $openid,
//                             'C_state' => 1,
//                             'C_time' => $time
//                         ];
//                         if($this->Wxconcern_model->action($data)){
//                             $this->ci_wechat->text($replay)->reply();
//                         }else{
//                             $this->ci_wechat->text('系统异常，请联系管理员')->reply();
//                         }
//                         break;
//                     case Wechat::EVENT_UNSUBSCRIBE:
//                         $this->load->model('Wxconcern_model');
//                         $data = [
//                             'C_openid' => $openid,
//                             'C_state' => 2,
//                             'C_time' => $time
//                         ];
//                         $this->Wxconcern_model->action($data);
//                         break;

//                     /*
//                      * 获取二维码的参数进行数据响应
//                      * */
//                     case Wechat::EVENT_SCAN:
//                         $sceneid = $this->ci_wechat->getRevSceneId();
//                         $this->ci_wechat->text($sceneid)->reply();
//                         break;
//                     /*
//                      * 菜单点击时间的响应
//                      * */
//                     case Wechat::EVENT_MENU_CLICK;
//                         $key = $this->ci_wechat->getRevEvent()['key'];
//                         switch ($key){
//                             case  "df":
// //                                $this->ci_wechat->image("guhBtkUjjXEoGFEFWxXHBGFZAfW2Xmi0Sq6QrxipjFs")->reply();break;
//                                 $this->ci_wechat->text('关闭中，开放时间待定')->reply();break;
//                             case  "caidan":
//                                 $this->ci_wechat->text('电费停止查询')->reply();break;
//                             default:
//                                 break;
//                         }
//                         break;

//                     default:
//                         break;
//                 }
                break;
            default:
                break;
        }
    }
}