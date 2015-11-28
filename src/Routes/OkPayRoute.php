<?php

namespace Routes;

use Silex\Application;
use config\Market;

/*
* Класс отвечает за следующие операции:
* проверка корректности платежа, сохранение информации о платеже,
* ответ на запрос сервера одноклассников.
*/
abstract class OkPayRoute {
    const ERROR_TYPE_UNKNOWN = 1;
    const ERROR_TYPE_SERVISE = 2;
    const ERROR_TYPE_CALLBACK_INVALID_PYMENT = 3;
    const ERROR_TYPE_SYSTEM = 9999;
    const ERROR_TYPE_PARAM_SIGNATURE = 104;

    // в эти переменные следует записать открытый и секретный ключи приложения
    private static $appSecretKey;

    // массив пар код продукта => цена
    private static $catalog = array(
        "777" => 1
    );
    // массив пар код ошибки => описание
    private static $errors = array(
            1 => "UNKNOWN: please, try again later. If error repeats, contact application support team.",
            2 => "SERVICE: service temporary unavailible. Please try again later",
            3 => "CALLBACK_INVALID_PAYMENT: invalid payment data. Please try again later. If error repeats, contact application support team. ",
            9999 => "SYSTEM: critical system error. Please contact application support team.",
            104 => "PARAM_SIGNATURE: invalid signature. Please contact application support team."
    );

    // функция рассчитывает подпись для пришедшего запроса
    // подробнее про алгоритм расчета подписи можно посмотреть в документации (http://apiok.ru/wiki/pages/viewpage.action?pageId=42476522)
    private static function calcSignature($request)
    {
        $tmp = $request;
        unset($tmp["sig"]);
        ksort($tmp);
        $resstr = "";
        foreach($tmp as $key=>$value){
            $resstr = $resstr.$key."=".$value;
        }
        $resstr = $resstr.self::$appSecretKey;
        return md5($resstr);
    }

    // функция провкерки корректности платежа
    private static function checkPayment($productCode, $price)
    {
        if (
            array_key_exists($productCode, self::$catalog)
            and (self::$catalog[$productCode] == $price)
        ) {
            return true; 
        } else {
            return false;
        }
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
        
        // установка заголовка
        header('Content-Type: application/xml');
        // вывод xml
        print $rezString;
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
        
        // добавление необходимых заголовков
        header('Content-Type: application/xml');
        // ВАЖНО: если не добавить этот заголовок, система может некорректно обработать ответ
        header('invocation-error:'.$errorCode);
        // вывод xml
        print $rezString;
    }

    // Рекомендуется хранить информацию обо всех транзакциях
    private static function saveTransaction(/* any params you need*/)
    {
        $timestamp = time();
        $transaction = \R::dispense('transactions');
        $transaction->orderId = $order_id;
        $transaction->createdAt = $timestamp;
        $transaction->userId = $user->id;
        $transaction->confirmedAt = $timestamp;
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

    public static function get(Application $app)
    {
        error_log(json_encode($_GET));
        die();
        static::$appSecretKey = getenv('OK_SECRET');
        if (
            array_key_exists("product_code", $_GET)
            and array_key_exists("amount", $_GET)
            and array_key_exists("sig", $_GET)
        ) {
            if (self::checkPayment($_GET["product_code"], $_GET["amount"])){
                if ($_GET["sig"] == self::calcSignature($_GET)){
                    self::saveTransaction();
                    self::returnPaymentOK();
                } else {
                    // здесь можно что-нибудь сделать, если подпись неверная
                    self::returnPaymentError(self::ERROR_TYPE_PARAM_SIGNATURE);
                }
            } else {
                // здесь можно что-нибудь сделать, если информация о покупке некорректна
                self::returnPaymentError(self::ERROR_TYPE_CALLBACK_INVALID_PYMENT);
            }
        } else {
            // здесь можно что-нибудь сделать, если информация о покупке или подпись отсутствуют в запросе
            self::returnPaymentError(self::ERROR_TYPE_CALLBACK_INVALID_PYMENT);
        }
        die();
    }
}

8d849ca4163e04ca4c5eeba9cfa633ac

3b6ba6f21da579d9e006190dbf0e8579
78c887aa5a053bfedb23f1ab15e1ef8e
8d849ca4163e04ca4c5eeba9cfa633ac

3b6ba6f21da579d9e006190dbf0e8579
78c887aa5a053bfedb23f1ab15e1ef8e
8d849ca4163e04ca4c5eeba9cfa633ac
1233634304_1448712726785

239278689941
91f459cd9fd4d42e79322cad5a5847029f354d6d4ea45f6c19092431.47
ef2d97b4fbf70b35079c3146
EF2D97B4FBF70B35079C3146

8d849ca4163e04ca4c5eeba9cfa633ac


web_server=ok.ru
first_start=0
logged_user_id=239278689941
sig=3b6ba6f21da579d9e006190dbf0e8579
new_sig=1
apiconnection=1233634304_1448712726785
authorized=1
session_key=91f459cd9fd4d42e79322cad5a5847029f354d6d4ea45f6c19092431.47
clientLog=0
session_secret_key=78c887aa5a053bfedb23f1ab15e1ef8e
auth_sig=8d849ca4163e04ca4c5eeba9cfa633ac
api_server=https%3A%2F%2Fapi.ok.ru%2F
ip_geo_location=RU%2C48%2CMoscow
application_key=CBAFMIIKEBABABABA

