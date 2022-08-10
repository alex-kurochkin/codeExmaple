<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user api\models\user\User */

$homeUrl = Yii::$app->mailUrlManager->createAbsoluteUrl('');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="telephone=no" name="format-detection">
    <title></title>
    <!--[if (mso 16)]>
    <style type="text/css">
        a {
            text-decoration: none;
        }
    </style>
    <![endif]-->
    <!--[if gte mso 9]>
    <style>sup {
        font-size: 100% !important;
    }</style><![endif]-->
    <!--[if gte mso 9]>
    <xml>
        <o:OfficeDocumentSettings>
            <o:AllowPNG></o:AllowPNG>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->
    <style type="text/css">
        #outlook a {
            padding: 0;
        }

        .ExternalClass {
            width: 100%;
        }

        .ExternalClass,
        .ExternalClass p,
        .ExternalClass span,
        .ExternalClass font,
        .ExternalClass td,
        .ExternalClass div {
            line-height: 100%;
        }

        .es-button {
            mso-style-priority: 100 !important;
            text-decoration: none !important;
        }

        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        .es-desk-hidden {
            display: none;
            float: left;
            overflow: hidden;
            width: 0;
            max-height: 0;
            line-height: 0;
            mso-hide: all;
        }

        @media only screen and (max-width: 480px) {
            .small-left {
                text-align: left !important;
            }
        }

        @media only screen and (max-width: 600px) {
            p, ul li, ol li, .es-menu td a {
                font-size: 16px !important
            }

            .es-header-body p, .es-header-body ul li, .es-header-body ol li, .es-header-body a {
                font-size: 14px !important
            }

            .es-footer-body p, .es-footer-body ul li, .es-footer-body ol li, .es-footer-body a {
                font-size: 14px !important
            }

            .es-infoblock p, .es-infoblock ul li, .es-infoblock ol li, .es-infoblock a {
                font-size: 12px !important
            }

            *[class="gmail-fix"] {
                display: none !important
            }

            .es-m-txt-c, .es-m-txt-c h1, .es-m-txt-c h2, .es-m-txt-c h3 {
                text-align: center !important
            }

            .es-m-txt-r, .es-m-txt-r h1, .es-m-txt-r h2, .es-m-txt-r h3 {
                text-align: right !important
            }

            .es-m-txt-l, .es-m-txt-l h1, .es-m-txt-l h2, .es-m-txt-l h3 {
                text-align: left !important
            }

            .es-m-txt-r img, .es-m-txt-c img, .es-m-txt-l img {
                display: inline !important
            }

            .es-button-border {
                display: block !important
            }

            .es-btn-fw {
                border-width: 10px 0px !important;
                text-align: center !important
            }

            .es-adaptive table, .es-btn-fw, .es-btn-fw-brdr, .es-left, .es-right {
                width: 100% !important
            }

            .es-content table, .es-header table, .es-footer table, .es-content, .es-footer, .es-header {
                width: 100% !important;
                max-width: 600px !important
            }

            .es-adapt-td {
                display: block !important;
                width: 100% !important
            }

            .adapt-img {
                width: 100% !important;
                height: auto !important
            }

            .es-m-p0 {
                padding: 0 !important
            }

            .es-m-p0r {
                padding-right: 0 !important
            }

            .es-m-p0l {
                padding-left: 0 !important
            }

            .es-m-p0t {
                padding-top: 0 !important
            }

            .es-m-p0b {
                padding-bottom: 0 !important
            }

            .es-m-p20b {
                padding-bottom: 20px !important
            }

            .es-mobile-hidden, .es-hidden {
                display: none !important
            }

            tr.es-desk-hidden, td.es-desk-hidden, table.es-desk-hidden {
                width: auto !important;
                overflow: visible !important;
                float: none !important;
                max-height: inherit !important;
                line-height: inherit !important
            }

            tr.es-desk-hidden {
                display: table-row !important
            }

            table.es-desk-hidden {
                display: table !important
            }

            td.es-desk-menu-hidden {
                display: table-cell !important
            }

            .es-menu td {
                width: 1% !important
            }

            table.es-table-not-adapt, .esd-block-html table {
                width: auto !important
            }

            table.es-social {
                display: inline-block !important
            }

            table.es-social td {
                display: inline-block !important
            }

            .es-m-p5 {
                padding: 5px !important
            }

            .es-m-p5t {
                padding-top: 5px !important
            }

            .es-m-p5b {
                padding-bottom: 5px !important
            }

            .es-m-p5r {
                padding-right: 5px !important
            }

            .es-m-p5l {
                padding-left: 5px !important
            }

            .es-m-p10 {
                padding: 10px !important
            }

            .es-m-p10t {
                padding-top: 10px !important
            }

            .es-m-p10b {
                padding-bottom: 10px !important
            }

            .es-m-p10r {
                padding-right: 10px !important
            }

            .es-m-p10l {
                padding-left: 10px !important
            }

            .es-m-p15 {
                padding: 15px !important
            }

            .es-m-p15t {
                padding-top: 15px !important
            }

            .es-m-p15b {
                padding-bottom: 15px !important
            }

            .es-m-p15r {
                padding-right: 15px !important
            }

            .es-m-p15l {
                padding-left: 15px !important
            }

            .es-m-p20 {
                padding: 20px !important
            }

            .es-m-p20t {
                padding-top: 20px !important
            }

            .es-m-p20r {
                padding-right: 20px !important
            }

            .es-m-p20l {
                padding-left: 20px !important
            }

            .es-m-p25 {
                padding: 25px !important
            }

            .es-m-p25t {
                padding-top: 25px !important
            }

            .es-m-p25b {
                padding-bottom: 25px !important
            }

            .es-m-p25r {
                padding-right: 25px !important
            }

            .es-m-p25l {
                padding-left: 25px !important
            }

            .es-m-p30 {
                padding: 30px !important
            }

            .es-m-p30t {
                padding-top: 30px !important
            }

            .es-m-p30b {
                padding-bottom: 30px !important
            }

            .es-m-p30r {
                padding-right: 30px !important
            }

            .es-m-p30l {
                padding-left: 30px !important
            }

            .es-m-p35 {
                padding: 35px !important
            }

            .es-m-p35t {
                padding-top: 35px !important
            }

            .es-m-p35b {
                padding-bottom: 35px !important
            }

            .es-m-p35r {
                padding-right: 35px !important
            }

            .es-m-p35l {
                padding-left: 35px !important
            }

            .es-m-p40 {
                padding: 40px !important
            }

            .es-m-p40t {
                padding-top: 40px !important
            }

            .es-m-p40b {
                padding-bottom: 40px !important
            }

            .es-m-p40r {
                padding-right: 40px !important
            }

            .es-m-p40l {
                padding-left: 40px !important
            }

            a.es-button, button.es-button {
                display: block !important;
                border-left-width: 0px !important;
                border-right-width: 0px !important
            }
        }

        @media only screen and (max-device-width: 480px) {
            .text-mobile-full {
                max-width: 100% !important;
            }

            .flag {
                text-align: center !important;
            }

            .margin-left {
                margin: 0 20px !important;
            }
        }
    </style>
</head>
<body style="padding: 0; margin: 0;background-color:#F6F6F6;">
<div style="font-size: 0; line-height:0;height: 0;">使用这些账号和密码来方向全球移动代理服务器！</div>
<div class="es-wrapper-color" style="background-color:#F6F6F6">
    <table cellspacing="0" cellpadding="0" border="0" align="center"
           style="width:100%; max-width:600px; border:0; margin: 0 auto; background: #fff;">
        <tr>
            <td valign="center"
                style="padding-left: 27px; height: 100px; background-color: #0072FF; background-image: url('https://i.imgur.com/5JSlPLA.jpg'); background-repeat: repeat-y;">
                <a href="<?= $homeUrl ?>" target="_blank">
                    <img src="https://i.imgur.com/feQJ1mm.png" style="width: auto; height: 60px;"/>
                </a>
            </td>
        </tr>
        <tr>
            <td style="padding: 30px 22px; background: #fff;">
                <div style="margin-bottom: 10px; font-family: Arial, Helvetica, sans-serif; font-size: 24px; color: #133662; font-weight: 500; line-height: 1.5;">
                    你好！
                </div>
                <div style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #133662; font-weight: 500; line-height: 1.5; margin-bottom: 20px">
                    感谢你注册！为了登录使用这些数据：
                </div>
                <div style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #133662; font-weight: 500; line-height: 1.5; margin-bottom: 40px">
                    账号名： <?= Html::encode($user->username) ?><br/>
                    密码： <?= $user->generatedPassword ?>
                </div>
                <div class="es-button-border"
                     style="border-style:solid;border-color:#00D45E;background:#00D45E;border-width:0;display:inline-block;border-radius:5px;width:auto;margin-bottom: 30px">
                    <a href="<?= $homeUrl ?>" class="es-button" target="_blank"
                       style="mso-style-priority:100 !important;text-decoration:none;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:16px;color:#FFFFFF;border-style:solid;border-color:#00D45E;border-width: 16px 30px;display:inline-block;background:#00D45E;border-radius:5px;font-style:normal;line-height:17px;width:auto;text-align:center;font-weight: 600;">
                        用户中心
                    </a>
                </div>
                <div style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #133662; font-weight: 500; line-height: 1.5;">
                    祝你顺利使用mp！
                </div>
            </td>
        </tr>
        <tr>
            <td style="background: #133662; height: 70px; padding: 0 20px;" valign="center">
                <table cellspacing="0" cellpadding="0" border="0" align="center" valign="center"
                       style="width:100%; border:0;">
                    <tr>
                        <td style="padding-right: 20px; width: 30px;">
                            <img src="https://i.imgur.com/dP0lY8A.png" alt=""/>
                        </td>
                        <td>
                            <span style="font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #fff; font-weight: 500; line-height: 1.5;">
                                微信：754259170
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
</body>
