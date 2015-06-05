<?php

/*
{
    "app_id":"4890xxx",
    "item":"creditsPacxxx",
    "lang":"ru_RU",
    "notification_type":"get_item_test",
    "order_id":"829xxx",
    "receiver_id":"5523xxx",
    "user_id":"5523xxx",
    "sig":"bd59934272e8xxxx"
}

{
    "app_id":"4890948",
    "date":"1433503962",
    "item":"creditsPack01",
    "item_id":"1",
    "item_photo_url":"http:\\/\\/example.com\\/img.jpg",
    "item_price":"15",
    "item_title":"Extra help pack",
    "notification_type":"order_status_change_test",
    "order_id":"830232",
    "receiver_id":"5523718",
    "status":"chargeable",
    "user_id":"5523718",
    "sig":"bd59934272e8xxxx"
}
*/
$app->post('/vk_pay', function() use ($app) {
    $secret_key = VK_SECRET; // Защищенный ключ приложения 
    if(strlen($secret_key) < 1) {
        error_log('VK_SECRET not set');
    }

    $input = $_POST;

    // Проверка подписи 
    $sig = $input['sig']; 
    unset($input['sig']); 
    ksort($input); 
    $str = ''; 
    foreach ($input as $k => $v) { 
        $str .= $k.'='.$v; 
    } 

    if ($sig != md5($str.$secret_key)) { 
        $response['error'] = array( 
        'error_code' => 10, 
        'error_msg' => 'Несовпадение вычисленной и переданной подписи запроса.', 
        'critical' => true 
        ); 
    } else { 
        // Подпись правильная 
        switch ($input['notification_type']) { 
            case 'get_item': 
            case 'get_item_test': 
                // Получение информации о товаре в тестовом режиме 
                $item = $input['item']; 
                if ($item == 'helpPack01' or $item = 'creditsPack01') { 
                    $response['response'] = array( 
                    'item_id' => 1, 
                    'title' => 'Extra help pack', 
                    'photo_url' => 'http://example.com/img.jpg',
                    'price' => 15
                    ); 
                } else {
                    error_log('unknown item '.$item);
                }
                break; 
            case 'order_status_change': 
            case 'order_status_change_test':
                // Изменение статуса заказа 
                if ($input['status'] == 'chargeable') { 
                    $order_id = intval($input['order_id']); 
                    switch($input['item_price']) {
                        case "15":
                            //"+420 монет и +10 жизней за 15 голосов"
                            $user = R::findOne('user', 'sys_id = "VK" AND ext_id = ?', [$input['user_id']]);
                            $user->credits += 420;
                            $user->remainingTries += 10;
                            R::store($user);
                        break;
                    }
                    
                    // Код проверки товара, включая его стоимость 
                    // fake id
                    $app_order_id = microtime(true) * 10000; // Получающийся у вас идентификатор заказа. 

                    $response['response'] = array( 
                        'order_id' => $order_id, 
                        'app_order_id' => $app_order_id, 
                    ); 
                } else { 
                    $response['error'] = array( 
                        'error_code' => 100, 
                        'error_msg' => 'Передано непонятно что вместо chargeable.', 
                        'critical' => true 
                    ); 
                } 
                break;
            default:
                $response = ['wtf'=>$input];
        } 
    } 

    render($response);
});
