<?php

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

        $data = '{"userId":null,"appFriends":"0","srcExtId":null,"authKey":"83db68e","sysId":"test","extId":"1","msgId":"123","referer":null}';
        $data = json_decode($data, true);
        $answer2 = $this->post('/ReqEnter', $data);
        $this->assertSame( (int)$answer2['userId'], $answer['userId'], 'Duplicate user');
        $this->assertNotSame( $answer['userId'], $answer2['userId'], 'Not this user id');
    }

    public function testContentUpload()
    {
        $this->post('/upload');
        $dirElements = scandir(ROOT.'web/bubble');
        $this->assertGreaterThan(1, count($dirElements), 'Empty archive');
    }

    public function testReduseTries()
    {
        $user = $this->getFirstUser();
        $answ = $this->post('/ReqReduceTries', [
            'userId' => $user['userId'],
        ]);
        $this->assertSame($user['remainingTries']-1, $answ[0], 'Tries not redused');
    }

    public function testReduseCredits()
    {
        $spentCreditsCount = 10;
        $user = $this->getFirstUser();
        $answ = $this->post('/ReqReduceCredits', [
            'userId' => $user['userId'],
            'amount' => $spentCreditsCount,
        ]);
        $this->assertSame($user['credits']-$spentCreditsCount, $answ['credits'], 'Credits not redused');
    }

    public function testBuyProduct()
    {
        $user = $this->getFirstUser();
        $answ = $this->post('/ReqReduceCredits', [
            'userId' => $user['userId'],
            'amount' => 10,
        ]);
        $this->assertSame($user['credits']-10, $answ['credits'], 'Credits not redused');
    }

    public function testUsersProgress()
    {
        $user = $this->getFirstUser();
        $answ = $this->post('/ReqUsersProgress', [
            'userId' => $user['userId'],
            'socIds' => [ $user['userId'] ],
        ]);
        $this->assertSame(1, count($answ['usersProgress']), 'Incorrect answer length');
    }

    public function testSavePlayerProgress()
    {
        $user = $this->getFirstUser();
        $reachedLevel = 20;
        $data = '{
            "isTest":true,
            "authKey":"83db68e3e1524c2e62e6dc67b38bc38c",
            "sysId":"test",
            "msgId":"123",
            "extId":"1234",
            "reachedSubStage":"10",
            "currentStage":"20",
            "reachedStage":"'.$reachedLevel.'",
            "completeSubStage":"40",
            "completeSubStageRecordStat":"40",
            "levelMode":"standart",
            "userId":'.$user['userId'].',
            "appFriends":"0"
        }';

        $answer = $this->post('/ReqSavePlayerProgress', $data);
        $updatedUser = $this->getFirstUser();
        $this->assertNotSame($updatedUser['reachedStage01'], $user['reachedStage01'], 'Not updated progress');
        $this->assertSame($reachedLevel, $updatedUser['reachedStage01'], 'Set incorrect max level');
    }

    private function getFirstUser()
    {
        $data = '{"userId":null,"appFriends":"0","srcExtId":null,"authKey":"83db68e","sysId":"test","extId":"1","msgId":"123","referer":null}';
        $data = json_decode($data, true);
        return $this->post('/ReqEnter', $data);
    }

}
