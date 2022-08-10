<?php

declare(strict_types=1);

namespace api\tests\api;

use api\tests\ApiTester;

/**
 * run: php vendor/bin/codecept run api/tests/
 *
 * Class UserInfoCest
 * @package api\tests\api
 */
class UserInfoCest
{
    /**
     * @param ApiTester $I
     */
    public function testUserInfo(ApiTester $I): void
    {
        $I->amBearerAuthenticated('3rssnfjneafkjacrkjfd8gydf7df786fdg78fd67g');
        $I->sendGet('/user/info');
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseContains(
            '{"id":4,"username":"user2","email":"user2@test.com","status":"active","phone":"79001111114","balance":10000,"phoneVerified":false,"forceEnableProxyBuy":false,"testCount":0,"language":"en","isAdmin":false,"isSu":false,"createdAt":1620801850,"updatedAt":1620801850,"use2fa":false,"proxiesCount":null,"proxiesCountAll":null,"spentMoney":0,"proxies":[]}'
        );
    }
}