<?php

function saveBoFile(){

    return config('customurl.save_bo_file');
}

function assessment_way_url(){
    return config('customurl.assessment_gateway_url');
}

function assessment_url(){
    return config('customurl.assessment_url');
}




function spCode(){
    return config('customurl.sp_code');
}

function subSpCode(){
    return config('customurl.sub_sp_code');
}

function spSysId(){
    return config('customurl.sp_sys_id');
}

function billRequestUrl(){
    return config('customurl.bill_request_url');
}

function paymentNotificationUrl(){
    return config('customurl.payment_notification_url');
}

function reconRequestUrl(){
    return config('custom_url.recon_request_url');
}

function keyStore(){
    return config('customurl.key_store');
}
function pubKey(){
    return config('customurl.pub_key');
}

function privKey(){
    return config('customurl.pri_key');
}
