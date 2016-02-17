<?php

namespace Routes;

use Silex\Application;
use config\Market;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/*
* Класс отвечает за следующие операции:
* проверка корректности платежа, сохранение информации о платеже,
* ответ на запрос сервера одноклассников.
*/
abstract class PayOkRoute {
    const ERROR_TYPE_UNKNOWN = 1;
    const ERROR_TYPE_SERVISE = 2;
    const ERROR_TYPE_CALLBACK_INVALID_PYMENT = 3;
    const ERROR_TYPE_SYSTEM = 9999;
    const ERROR_TYPE_PARAM_SIGNATURE = 104;

    // в эти переменные следует записать открытый и секретный ключи приложения
    private static $appSecretKey;


    // массив пар код ошибки => описание
    private static $errors = [
        1 => "UNKNOWN: please, try again later. If error repeats, contact application support team.",
        2 => "SERVICE: service temporary unavailible. Please try again later",
        3 => "CALLBACK_INVALID_PAYMENT: invalid payment data. Please try again later. If error repeats, contact application support team. ",
        9999 => "SYSTEM: critical system error. Please contact application support team.",
        104 => "PARAM_SIGNATURE: invalid signature. Please contact application support team."
    ];

    // функция рассчитывает подпись для пришедшего запроса
    // подробнее про алгоритм расчета подписи можно посмотреть в документации (http://apiok.ru/wiki/pages/viewpage.action?pageId=42476522)
    private static function calcSignature($request)
    {
        $tmp = $request;
        unset($tmp["sig"]);
        ksort($tmp);
        $resstr = '';
        foreach($tmp as $key=>$value){
            $resstr = $resstr.$key.'='.$value;
        }
        $resstr = $resstr.self::$appSecretKey;
        return md5($resstr);
    }

    // функция провкерки корректности платежа
    private static function checkPayment($productCode, $price, $app)
    {
        try {
            $item_info = Market::info($productCode, 'ok', 'ru');
            if ($item_info['price'] === (int) $price) {
                return true;
            }
        } catch (\Exception $e) {
            $app['raven']->handleException($e);
        }
        return false;
    }

    // функция возвращает ответ на сервер одноклассников
    // о корректном платеже
    private static function returnPaymentOK()
    {
        $rootElement = 'callbacks_payment_response';
        $dom = self::createXMLWithRoot($rootElement);
        $root = $dom->getElementsByTagName($rootElement)->item(0);
        
        // добавление текста "true" в тег <callbacks_payment_response> 
        $root->appendChild($dom->createTextNode('true')); 
        
        // генерация xml 
        $dom->formatOutput = true;
        $rezString = $dom->saveXML();

        $response = new Response($rezString);
        $response->headers->add(
            [
                'Content-Type' => 'application/xml',
            ]
        );

        return $response;
    }

    // функция возвращает ответ на сервер одноклассников
    // об ошибочном платеже и информацию лб ошибке
    private static function returnPaymentError($errorCode)
    {
        $rootElement = 'ns2:error_response';
        $dom = self::createXMLWithRoot($rootElement);
        $root = $dom->getElementsByTagName($rootElement)->item(0);
        // добавление кода ошибки и описания ошибки
        $el = $dom->createElement('error_code');
        $el->appendChild($dom->createTextNode($errorCode));
        $root->appendChild($el);
        if (array_key_exists($errorCode, self::$errors)){
            $el = $dom->createElement('error_msg');
            $el->appendChild($dom->createTextNode(self::$errors[$errorCode]));
            $root->appendChild($el);
        } 

        // генерация xml 
        $dom->formatOutput = true;
        $rezString = $dom->saveXML();
        
        $response = new Response($rezString);
        $response->headers->add(
            [
                'Content-Type' => 'application/xml',
                'invocation-error' => $errorCode,
            ]
        );

        return $response;
    }

    // Рекомендуется хранить информацию обо всех транзакциях
    private static function saveTransaction($get)
    {
        ;
        $user = \R::findOne('users', 'sys_id = ? AND ext_id = ?', [2, $get['uid']]);
        if(!is_object($user)) {
            throw new \Exception('OK pay user not found');
        }
        $timestamp = time();
        $transaction = \R::dispense('transactions');
        $transaction->orderId = $get['transaction_id'];
        $transaction->createdAt = \DateTime::createFromFormat('Y-m-d H:i:s', $get['transaction_time'])->getTimestamp();
        $transaction->userId = $user->id;
        $transaction->confirmedAt = time();
        $app_order_id = \R::store($transaction);
        // тут может быть код для сохранения информации о транзакции
    }

    // функция создает объект DomDocument и добавляет в него в качестве корневого тега $root
    private static function createXMLWithRoot($root)
    {
        // создание xml документа
        $dom = new \DomDocument('1.0'); 
        // добавление корневого тега
        $root = $dom->appendChild($dom->createElement($root));
        $attr = $dom->createAttribute("xmlns:ns2");
        $attr->value = "http://api.forticom.com/1.0/";
        $root->appendChild($attr);
        return $dom;
    }

    public static function action(Application $app, Request $request)
    {
        self::$appSecretKey = getenv('OK_SECRET');
        self::$appSecretKey = '50FE0921FEC5E944A7BE989B';
        $request_params = $request->query->all();
        try {
            if (
                !array_key_exists("product_code", $request_params)
                or !array_key_exists("amount", $request_params)
                or !array_key_exists("sig", $request_params)
            ) {
                throw new \Exception("Not enought arguments", self::ERROR_TYPE_CALLBACK_INVALID_PYMENT);
            }
            if (!self::checkPayment($request_params["product_code"], $request_params["amount"], $app)){
                throw new \Exception("Invalid product_code or amount", self::ERROR_TYPE_CALLBACK_INVALID_PYMENT);
            }
            if ($request_params["sig"] !== self::calcSignature($request_params)){
                throw new \Exception("Invalid signature", self::ERROR_TYPE_PARAM_SIGNATURE);
            }
            self::saveTransaction($request_params);
            return self::returnPaymentOK();
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            var_dump($e->getLine());
            $app['raven']->handleException($e);
            return self::returnPaymentError($e->getCode());
        }
    }
}
