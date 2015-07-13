<?php
class IntegrationTest extends stdClass {

    public function setUp()
    {
    
    }

    public function tearDown()
    {
        
    }

    public function testNewUser()
    {
        $data = '{"isTest":true,"userId":null,"appFriends":"0","srcExtId":null,"authKey":"83db68e","sysId":"test","extId":"1234","msgId":"123","referer":null}';
        
        $answer = curl('ReqEnter', $data);
        if(is_object($answer)){
            $userID = $answer->userId; // next tests
            $answer2 = curl('ReqEnter', $data);
            if ($answer2->userId == $answer->userId) {
                echo '. ';
            } else {
                echo '"ReqEnter" duplicate user'.PHP_EOL;
            }
            $data2 = '{"isTest":true,"userId":null,"appFriends":"0","srcExtId":null,"authKey":"83db68e","sysId":"testooo","extId":"1234","msgId":"123","referer":null}';
            $answer3 = curl('ReqEnter', $data2);
            if($answer3->userId == $answer2->userId) {
                echo '"ReqEnter" wrong user'.PHP_EOL;
            } else
                echo '. ';
        } else {
            echo '"ReqEnter" registration fail'.PHP_EOL;
            echo $host.PHP_EOL;
            die('Cannot continue test. There is no UserID'.PHP_EOL);
        }
    }
/*
    public function testReduseCredits()
    {
        $reschedLevel = 40;
        $data = '{
            "authKey":"83db68e3e1524c2e62e6dc67b38bc38c",
            "sysId":"test",
            "extId":"1234",
            "amount":"2",
            "msgId":"123",
            "userId":'.$userID.',
            "appFriends":"0"
        }';

        $answer0 = curl('ReqEnter', $data);
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
