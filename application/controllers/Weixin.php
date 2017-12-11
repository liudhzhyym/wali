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
        // $this->load->model('Wxreply_model');
        // $this->load->model('Wxguestbook_model');

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
    }
}