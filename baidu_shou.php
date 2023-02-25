<?php
/*
Plugin Name: 百度收录检测并自动推送
Version: 1.0
Plugin URL:https://www.fish9.cn/archives/388/
Description: 是一个帮你自动检测并提交页面链接到百度的插件
Author: 吃猫的鱼
Author URL: https://www.fish9.cn
*/

 !defined('EMLOG_ROOT') && exit('access deined!');


/**
 * 获取当前页面链接
 */
function get_urls() {
	$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
	$php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
	$path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
	$relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
	return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
}

/**
 * 检测页面是否收录
 */
function  check_urls($checked_url){
    $url = 'https://api.fish9.cn/api/baidu/'; 
    $data = '?url='.$checked_url;
    $get = $url.$data;
    $result = file_get_contents($get);
    if ($result) {
        if((isset(Json_decode($result,true)["baidu"])?Json_decode($result,true)["baidu"]:"0")==1){
            return true;
        }else{
            return false;
        }
    }
}

/**
 * 获取主域名
 */
function get_main_url(){
    $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
    return  $http_type . $_SERVER['HTTP_HOST'];
}
/**
 * 提交收录到百度
 */
function  submit_urls($url,$token){
    $urls = array(
    $url
    );
    $api = 'http://data.zz.baidu.com/urls?site='.get_main_url().'&token='.$token;
    $ch = curl_init();
    $options =  array(
        CURLOPT_URL => $api,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => implode("\n", $urls),
        CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
    );
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    return $result ;
}



/**
 * 页面主方法
 */
function page(){
    ?>
    <!-- 此处可以修改显示文字样式 -->
&nbsp;&nbsp;&nbsp;&nbsp;<b id="BaiduShoulu" style="color:#cad061;">正在检查是否收录</b>
    <!-- 此处可以修改显示文字样式 -->
    <?php
    if(!check_urls(get_urls())){      //当页面未被收录的时候执行
        $plugin_storage = Storage::getInstance('baidu_shou');
        $result = submit_urls(get_urls(),$plugin_storage->getValue('baidu_token'));
        if(json_decode($result, true)!=null){
            $result = json_decode($result, true);
        }

        if((isset($result['success']) ? $result['success'] : "2")==1){  //推送成功
?>
        <script>
        document.getElementById("BaiduShoulu").style.color="#3dad34";
        document.getElementById("BaiduShoulu").innerHTML="未收录,已推送";
        </script>

<?php
        }else if((isset($result['remain']) ? $result['remain'] : "1")==0){              //推送失败
?>
        <script>
        document.getElementById("BaiduShoulu").style.color="#842927";
        document.getElementById("BaiduShoulu").innerHTML="已达最大推送";
        </script>
<?php
        }else{
?>
        <script>
        document.getElementById("BaiduShoulu").style.color="#842927";
        document.getElementById("BaiduShoulu").innerHTML="推送失败";
        </script>
<?php
        }

    }else{          //页面已经被收录，显示已收录
?>
        <script>
        document.getElementById("BaiduShoulu").style.color="#3dad34";
        document.getElementById("BaiduShoulu").innerHTML="已被百度收录";
        </script>
<?php
    }
}
 addAction('BaiduShoulu', 'page');