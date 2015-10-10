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
        $data = '{"userId":null,"appFriends":"0","srcExtId":null,"authKey":"83db68e","sysId":"test","extId":"1","msgId":"123","referer":null}';
        $data = json_decode($data, true);

        $answer = $this->post('/ReqEnter', $data);
        $this->assertArraySubset([],$answer);
        $userID = $answer['userId']; // next tests

        $answer2 = $this->post('/ReqEnter', $data);

        $this->assertSame( (int)$answer2['userId'], $answer['userId'], 'Duplicate user');

        $data2 = '{"userId":null,"appFriends":"0","srcExtId":null,"authKey":"83db68e","sysId":"test","extId":"2","msgId":"123","referer":null}';
        $data2 = json_decode($data2, true);
        $answer3 = $this->post('/ReqEnter', $data2);
        $this->assertNotSame( $answer3['userId'], $answer2['userId'], 'Not this user id');
    }

    public function testContentUpload()
    {
        $this->post('/upload');
        $dirElements = scandir(ROOT.'web/bubble');
        $this->assertGreaterThan(1, count($dirElements), 'Empty archive?');
    }
/*
    public function testReduseCredits()
    {
        $reachedLevel = 40;
        $data = [
            "authKey" => "83db68e3e1524c2e62e6dc67b38bc38c",
            "sysId" => "test",
            "extId" => "1234",
            "amount" => "2",
            "msgId" => "123",
            "userId" => $userID,
            "appFriends" =>"0",
        ];

        $answer0 = $this->post('/ReqEnter', $data);
        $answer = curl('ReqReduceCredits', $data);
        if($answer !== NULL){
            $answer2 = curl('ReqEnter', $data);
            if($answer2 !== null) {
                if($answer2->credits >= $answer0->credits)
                    echo '"ReqEnter" credits not dicreased ('.$answer0->credits.')'.PHP_EOL;
                else
                    echo '. ';
            } else {
                'reqenter after reduce credits problem';
            }
        }
    }
/*
    public function testSavePlayerProgress()
    {
        $reschedLevel = 20;
        $data = '{
            "isTest":true,
            "authKey":"83db68e3e1524c2e62e6dc67b38bc38c",
            "sysId":"test",
            "msgId":"123",
            "extId":"1234",
            "reachedSubStage":"10",
            "currentStage":"20",
            "reachedStage":"'.$reschedLevel.'",
            "completeSubStage":"40",
            "completeSubStageRecordStat":"40",
            "levelMode":"standart",
            "userId":'.$userID.',
            "appFriends":"0"
        }';

        $answer = curl('ReqSavePlayerProgress', $data);
        if($answer !== NULL){
            $answer2 = curl('ReqEnter', $data);
            if($answer2 !== NULL){
                if($answer2->reachedStage01 != $reschedLevel)
                    echo '"ReqSavePlayerProgress" progres not updated'.PHP_EOL;
            } else {
                echo ' regEnter after save progress problem';
            }
        }
    }
*/
}
