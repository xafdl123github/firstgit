<?php

namespace Home\Controller;
use Think\Page;
use Think\Verify;
class IndexController extends BaseController {
    //获取用户基本信息
    public function getUserInfo2(){
        //得到 access_token 与 openid
        $AppID = 'wx8c865e5d43176a2b';
        $AppSecret = '3039728f3acb02e00a42435794f9d3f8';
        if(!isset($_GET['code'])){
            //触发微信返回code码
            $baseUrl = urlencode($this->get_url());
            $url = $this->__CreateOauthUrlForCode($baseUrl); // 获取 code地址
            Header("Location: $url"); // 跳转到微信授权页面 需要用户确认登录的页面  //又跳转回当前方法执行下面的else  zhai
            exit();
        }else{
            $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$AppID.'&secret='.$AppSecret.'&code='.$_GET['code'].'&grant_type=authorization_code';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_URL, $url);
            $json = curl_exec($ch);
            curl_close($ch);
            $arr=json_decode($json,1);
        }
        return $arr;

    }
    /**
     * 获取当前的url 地址
     * @return type
     */
    private function get_url() {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
        return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
    }
    /**
     * （第一步：获取code,拼接链接  zhai）
     *
     * 构造获取code的url连接
     * @param string $redirectUrl 微信服务器回跳的url，需要url编码
     *
     * @return 返回构造好的url
     */
    private function __CreateOauthUrlForCode($redirectUrl)
    {
        $urlObj["appid"] = 'wx8c865e5d43176a2b';
        $urlObj["redirect_uri"] = "$redirectUrl";
        $urlObj["response_type"] = "code";
        $urlObj["scope"] = "snsapi_userinfo";
        $urlObj["state"] = "STATE"."#wechat_redirect";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
    }
    /**
     *
     * 拼接签名字符串
     * @param array $urlObj
     *
     * @return 返回已经拼接好的字符串
     */
    private function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v)
        {
            if($k != "sign"){
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }
    /*
     * 个人--首页
     */
    public function person_index(){

//        echo '33';exit;
      //  print_r($_SESSION);exit;
        $cat_list = M('goods_cat')->select();
          
        if($_SESSION['car_user']['car_openid']){
            $user_openid =  $_SESSION['car_user']['car_openid'];
        }else{
            $arr2 = $this->getUserInfo2();
            $user_openid = $arr2['openid'];
            $_SESSION['car_user']['car_openid'] = $user_openid;
        }
//        echo $user_openid;exit;
        $is_member = M('user')->where("openid = '$user_openid'")->find();
        if($is_member){
           // echo '1111';exit;
            $user_store = M('user')->alias('u')->where("u.openid = '$user_openid'")->join('tp_store as s on u.user_from = s.store_id')->field('u.user_id,u.user_from,u.openid,u.nickname,u.head_pic,u.sex,u.openid,u.province,u.city,u.gz_time,s.store_id,s.store_name,s.store_logo,s.contact_name,s.contact_phone,s.store_address')->find();
        }else{
           // echo '222222';exit;
            $user_store = M('store')->where(array('is_default'=>1))->find();
        }
       // print_r($user_store);exit;
        $_SESSION['car_user']['car_store_id'] = $user_store['store_id'];
    	 foreach($cat_list as $k => $v){
    	 $cat_list[$k]['goods_list'] = M('goods')->order('sort asc')->where('cat_id ='.$v['cat_id'])->select();
         }

        $pic_url = M('pic')->where('id = 2')->find();
        $this->assign('pic_url',$pic_url);
        $this->assign('user_store',$user_store);
        $this->assign('cat_list',$cat_list);
        $this->display();
    }

}
