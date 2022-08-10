<?php

declare(strict_types=1);

namespace api\tests\api;

use api\tests\ApiTester;

class UserLoginCest
{
    /**
     * @param ApiTester $I
     */
    public function testUserLogin(ApiTester $I): void
    {
        $I->sendPut(
            '/user/login',
            [
                'email' => 'user2@test.com',
                'password' => '11111111'
            ]
        );
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(
            [
                'accessToken' => 'string:regex(|[\w_-]{42}|)'
            ]
        );
    }

    /**
     * @param ApiTester $I
     */
    public function testUserLogin2fa(ApiTester $I): void
    {
        $response = $I->sendPut(
            '/user/login',
            [
                'email' => 'user1@test.com',
                'password' => '11111111'
            ]
        );
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseContains('wait2fa');

        // Strange but it grubs old DB state but not new records
//        $code2fa = $I->grabFromDatabase('AccessToken', 'code2fa', ['userId' => 3, 'status' => 1]);
//
//        $I->sendPut('/user/login', ['code2fa' => $code2fa]);
//        $I->seeResponseCodeIsSuccessful();
//        $I->seeResponseIsJson();
//        $I->seeResponseMatchesJsonType(
//            [
//                'accessToken' => 'string:regex(|[\w_-]{42}|)'
//            ]
//        );
    }
}