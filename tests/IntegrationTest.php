<?php

use config\UserParams;

class IntegrationTest extends TestBootstrap
{

    public function testInitialPage()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertJsonStringEqualsJsonString('{"foo":"bar"}', $client->getResponse()->getContent());
    }

    public function testTestInitialPage()
    {
        $response = $this->post('/', ['asd'=>'zxc']);

        $this->assertArraySubset(['asd'=>'zxc'], $response);
    }

    public function testNewUser()
    {
        $answer = $this->getFirstUser();
        $this->assertArraySubset([],$answer);
        $this->assertGreaterThan(0, $answer['userId'], 'Incorrect user id');

        $answer2 = $this->getFirstUser();
        $this->assertSame( (int)$answer2['userId'], (int)$answer['userId'], 'Duplicate user');
        
        $answer3 = $this->getSecondUser();
        $this->assertNotSame( (int)$answer['userId'], (int)$answer3['userId'], 'Not this user id');
    }

    public function testReduceTries()
    {
        $user = $this->getFirstUser();
        $answ = $this->post('/ReqReduceTries', [
            'userId' => $user['userId'],
        ]);
        $this->assertSame($user['remainingTries']-1, $answ[0], 'Tries not reduced');
    }

    public function testReduceCredits()
    {
        $spentCreditsCount = 10;
        $user = $this->getFirstUser();
        $answ = $this->post('/ReqReduceCredits', [
            'userId' => $user['userId'],
            'amount' => $spentCreditsCount,
            'msgId' => 1,
        ]);
        $this->assertSame($user['credits']-$spentCreditsCount, $answ['credits'], 'Credits not reduced');
    }

    public function testUsersProgress()
    {
        $user = $this->getFirstUser();
        $friend = $this->getSecondUser();
        $answ = $this->post('/ReqUsersProgress', [
            'userId' => $user['userId'],
            'socIds' => [
                $user['userId'],
                $friend['userId'],
            ],
        ]);
        $this->assertSame(2, count($answ['usersProgress']), 'Incorrect answer length');
    }

    public function testSavePlayerProgress()
    {
        $user = $this->getFirstUser();
        $reachedLevel = '5';
        $data = '{
            "authKey":"83db68e3e1524c2e62e6dc67b38bc38c",
            "sysId":"test",
            "msgId":"123",
            "extId":"1234",
            "reachedSubStage":"5",
            "currentStage":"5",
            "reachedStage":"'.$reachedLevel.'",
            "completeSubStage":"5",
            "completeSubStageRecordStat":"3",
            "levelMode":"standart",
            "userId":'.$user['userId'].',
            "appFriends":"0"
        }';

        $answer = $this->post('/ReqSavePlayerProgress', json_decode($data, true) );
        $updatedUser = $this->getFirstUser();
        $this->assertNotSame($updatedUser['reachedStage01'], $user['reachedStage01'], 'Not updated progress');
        $this->assertSame($reachedLevel, $updatedUser['reachedStage01'], 'Set incorrect max level');
    }

    public function testBuyProduct()
    {
        $user = $this->getFirstUser();
        $this->post('/ReqBuyProduct', [
            'userId' => $user['userId'],
            'productId' => 'creditsPack01',
        ]);
        $updatedUser = $this->getFirstUser();
        $this->assertGreaterThan($user['credits'], $updatedUser['credits'], 'Good not recieved');
    }

    public function testGetStaticFiles()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/bubble/assets/Map/Stage01Path.png?ololo');

        $response = $client->getResponse();

        $this->assertSame('/bubble/assets/Map/Stage01Path.png?ololo', $response->getTargetUrl());
        $this->assertSame(307, $response->getStatusCode());
    }

    public function testGetStaticFilesCached()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/bubble/assets/Map/Stage02Path.png');

        $response = $client->getResponse();

        $this->assertSame('/bubble/assets/Map/Stage02Path.png', $response->getTargetUrl());
        $this->assertSame(307, $response->getStatusCode());
    }

    public function testCearStaticFilesCache()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/cache-clear');

        $response = $client->getResponse();

        $files = scandir(ROOT.'web/bubble');
        $this->assertSame(3, count($files));
    }

    public function FIX_ME_testAutoRestoreLifes()
    {
        $user = $this->getFirstUser();
        $this->post('/ReqReduceTries', [
            'userId' => $user['userId'],
        ]);
        // TODO
        $updatedUser = $this->getFirstUser();
        $this->assertSame( (string) UserParams::DEFAULT_REMAINING_TRIES, $updatedUser['remainingTries'], 'Lifes not restored');
    }

    public function testOkPay()
    {
        $this->getOkUser();
        $client = $this->createClient();
        $crawler = $client->request('GET', '/OkPay', [
            'amount' => 29,
            'application_key' => 'CBAHCJIKEBABABABA',
            'call_id' => 1455095504601,
            'method' => 'callbacks.payment',
            'product_code' => 'creditsPack01',
            'sig' => '8b07e57c1845f7adf157f478f23cfff6',
            'transaction_id' => '299363449344',
            'transaction_time' => '2016-02-10 12:11:44',
            'uid' => 556111519987,
        ]);

        $response = $client->getResponse()->getContent();
        $this->assertGreaterThan(1, strpos($response, '>PARAM_SIGNATURE:'));
    }

    private function getFirstUser()
    {
        $data = '{"userId":null,"appFriends":"0","srcExtId":null,"authKey":"83db68e","sysId":"test","extId":"1","msgId":"123","referer":null}';
        $data = json_decode($data, true);
        return $this->post('/ReqEnter', $data);
    }

    private function getSecondUser()
    {
        $data = '{"userId":null,"appFriends":"0","srcExtId":null,"authKey":"83db68e","sysId":"test","extId":"2","msgId":"123","referer":null}';
        $data = json_decode($data, true);
        return $this->post('/ReqEnter', $data);
    }

    private function getOkUser()
    {
        $data = '{"userId":null,"appFriends":"0","srcExtId":null,"authKey":"006a8f6ca8638110f4183ac2a4f18b69","sysId":"OK","extId":"556111519987","msgId":"123","referer":null,"sessionKey":""}';
        $data = json_decode($data, true);
        return $this->post('/ReqEnter', $data);
    }
}
