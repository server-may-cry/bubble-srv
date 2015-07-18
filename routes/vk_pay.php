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
$app->post('/vk_pay', function($request, $response) {
    $secret_key = getenv('VK_SECRET');
    if(strlen($secret_key) < 1) {
        throw new Exception('VK_SECRET not set');
    }

    //$input = $_POST;
    $input = $request->getParsedBody();

    // Проверка подписи
    $sig = $input['sig'];
    unset($input['sig']);
    ksort($input);
    $str = '';
    foreach ($input as $k => $v) {
        $str .= $k.'='.$v;
    }

    $vk_response = [];
    if ($sig != md5($str.$secret_key)) {
        $vk_response['error'] = array(
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
                $item_info = Market::info($item, 'vk', 'ru');
                $vk_response['response'] = [
                    'item_id' => 1, 
                    'title' => $item_info['title'],
                    //'photo_url' => $item_info['photo'],
                    'price' => $item_info['price'], //15
                ];
                if(isset($item_info['photo'])) {
                    $vk_response['response']['photo_url'] = $item_info['photo'];
                }
                break;
            case 'order_status_change':
            case 'order_status_change_test':
                // Изменение статуса заказа
                if ($input['status'] == 'chargeable') {
                    $order_id = intval($input['order_id']);
                    $user = R::findOne('users', 'sys_id = ? AND ext_id = ?', ["VK", $input['user_id']]);
                    if(!is_object($user)) {
                        throw new Exception('Vk pay user not found');
                    }
                    Market::buy($user, $input['item'], 'vk');
                    
                    // Код проверки товара, включая его стоимость
                    // fake id
                    $app_order_id = microtime(true) * 10000; // Получающийся у вас идентификатор заказа.

                    $vk_response['response'] = [
                        'order_id' => $order_id,
                        'app_order_id' => $app_order_id,
                    ];
                } else {
                    $vk_response['error'] = [
                        'error_code' => 100,
                        'error_msg' => 'Передано непонятно что вместо chargeable.',
                        'critical' => true
                    ];
                }
                break;
            default:
                $vk_response['error'] = [
                    'error_code' => 100,
                    'error_msg' => 'Передано непонятно что в notification_type.',
                    'critical' => true
                ];
        }
    }

    return render($response, $vk_response);
});
