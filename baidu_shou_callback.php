<?php
!defined('EMLOG_ROOT') && exit('access deined!');

// 插件激活时调用
function callback_init() {
     $plugin_storage = Storage::getInstance('baidu_shou');
     $plugin_storage->setValue('baidu_token', '','string');
}

// 插件关闭时调用
function callback_rm() {
    // ....
}