<?php
include ('C:\Users\MarkSmersh\Desktop\the_zakazi_bot\methods.php');

const database = ['localhost', 'root', 'Omar2005lol', 'the_zakazi_bot'];
$db = new mysqli(database[0], database[1], database[2], database[3]);
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
$db->close();

date_default_timezone_set('Europe/Kiev');

// TELEGRAM
$Bot = new Bot('1886521674:AAFfjj8bETiqLrizmD84XwX6d7eZUDUtdxE');
$Update = new Update ($Bot);
$conv_handler = new Converstation (

    $token = $Bot,

    $entry = array (
        'filter' => '/start', 'func' => 'start'
    ),
    
    $filter = array (
        'user_check' => array (
            array ('filter' => '', 'func' => 'userCheck')
        ),
        'main_menu' => array (
            array ('filter' => 'Новый заказ', 'func' => 'newOrder'),
            array ('filter' => 'База данных', 'func' => 'showDatabase'),
            array ('filter' => 'Статус оплаты', 'func' => 'showPaymentStatus'),
            array ('filter' => '/help', 'func' => 'help')
        ),
        // Новый заказ
        'phone_number_handler' => array (
            array ('filter' => '', 'func' => 'newOrder2')
        ),
        'address_handler' => array (
            array ('filter' => '', 'func' => 'newOrder3')
        ),
        'product_handler' => array (
            array ('filter' => '', 'func' => 'newOrder4')
        ),
        'pay_method_handler' => array (
            array ('callback' => 'by_cash', 'func' => 'newOrder5'),
            array ('callback' => 'by_prepay', 'func' => 'newOrder5'),
            array ('callback' => 'have_paid', 'func' => 'newOrder5')
        ),
        'checking_order_handler' => array (
            array ('filter' => 'Подтвердить', 'func' => 'newOrder6'),
            array ('filter' => 'Изменить номер телефона', 'func' => 'changePhoneNumber'),
            array ('filter' => 'Изменить ФИО и адрес', 'func' => 'changeAddress'),
            array ('filter' => 'Изменить товар', 'func' => 'changeProduct'),
            array ('filter' => 'Изменить способ оплаты', 'func' => 'changePayMethod'),
        ),
        'phone_number_handler2' => array (
            array ('filter' => '', 'func' => 'changePhoneNumber2')
        ),
        'address_handler2' => array (
            array ('filter' => '', 'func' => 'changeAddress2')
        ),
        'product_handler2' => array (
            array ('filter' => '', 'func' => 'changeProduct2')
        ),
        //База данных
        'database' => array (
            array ('callback' => 'previous', 'func' => 'showDatabasePrevious'),
            array ('callback' => 'next', 'func' => 'showDatabaseNext'),
            array ('callback' => 'back', 'func' => 'hideDatabase'),
            array ('filter' => '', 'func' => 'showDatabaseOrder')
        ),
        //Статус оплаты
        'check_payment' => array (
            array ('callback' => 'previous', 'func' => 'showPaymentStatusPrevious'),
            array ('callback' => 'next', 'func' => 'showPaymentStatusNext'),
            array ('callback' => 'back', 'func' => 'hidePaymentStatus'),
            array ('filter' => '', 'func' => 'showPaymentStatusOrder')
        ),
        'check_payment_order' => array (
            array ('callback' => 'have_paid', 'func' => 'showPaymentStatus'),
            array ('callback' => 'by_cash', 'func' => 'showPaymentStatus'),
            array ('callback' => 'back', 'func' => 'showPaymentStatus'),
        )
    ),

    $breakout = array (
        'filter' => '/stop', 'func' => 'stop'
    )

);



function start ($bot, $response) {
    $date = date ('H');
    if (in_array($date, range(0, 5))) {
        $data_text = 'Доброй ночи';
    }
    elseif (in_array($date, range(6, 11))) {
        $data_text = 'Доброе утро';
    }
    elseif (in_array($date, range(12, 17))) {
        $data_text = 'Добрый день';
    }
    elseif (in_array($date, range(18, 23))) {
        $data_text = 'Добрый вечер';
    }
    new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                    'text' => $data_text.', '.'['.$response['from']['first_name'].']'.'(tg://user?id='.$response['from']['id'].')'."\nВведите проходное слово:",
                                    'parse_mode' => 'markdown']);
    return 'user_check';
}

function userCheck ($bot, $response) {
    if ($response['text'] === '.783') {
        new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                        'text' => '_Код введён верно!_',
                                        'parse_mode' => 'markdown']);
        $keyboard = json_encode([
            "keyboard" => [
                [
                    [
                        "text" => "Новый заказ",
                    ],
                ],
                [
                    [
                        "text" => "База данных",
                    ],
                ],
                [
                    [
                        "text" => "Статус оплаты",
                    ],
                ]
            ],
            "resize_keyboard" => True,
            "one_time_keyboard" => True
        ]);
        new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                        'text' => 'Добро пожаловать обратно, '.'['.$response['from']['first_name'].']'.'(tg://user?id='.$response['from']['id'].')'.'. Выберите одну из перечисленных функций ниже:',
                                        'parse_mode' => 'markdown',
                                        'reply_markup' => $keyboard]);
        return 'main_menu';
    }
    elseif ($response['text'] !== '.783') {
        new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                        'text' => "_Код введён неверно!_\nПовторите попытку:",
                                        'parse_mode' => 'markdown']);
        return 'user_check';
    }
}



function newOrder($bot, $response) {
    $keyboard = json_encode ([
        "remove_keyboard" => True
    ]);
    new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                        'text' => "Введите номер телефона:\n`пример: +380123456789`",
                                        'parse_mode' => 'markdown',
                                        'reply_markup' => $keyboard]);
    return 'phone_number_handler';
}

function newOrder2($bot, $response) {
    $pattern = "/^\+380\d{9}$/";
    if (preg_match($pattern, $response['text'])) {
        $db = new mysqli(database[0], database[1], database[2], database[3]);
        $sql = "INSERT INTO orders (phone_number, date, time, created_by)
        VALUES ('".$response['text']."', '".date('Y-m-d')."', '".date('H:i')."', '".$response['from']['id']."')";
        $db->query($sql);
        $sql = "SELECT id
        FROM orders
        WHERE created_by='".$response['from']['id']."'
        ORDER BY id DESC";
        $result = $db->query($sql);
        $result = ($result->fetch_assoc()['id']);
        $db->close();

        $filename = 'order_id';
        $json = file_get_contents ($filename.'.json');
        $data = json_decode ($json, true);

        $user_id = $response['from']['id'];

        $data[$user_id] = $result;
        $json = json_encode ($data);
        file_put_contents ($filename.'.json', $json);

        new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                            'text' => "Введите ФИО и адрес:",
                                            'parse_mode' => 'markdown']);
        return 'address_handler';
    }
    elseif (!preg_match($pattern, $response['text'])) {
        new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                            'text' => "_Номер телефон ввёдён некорректно_\nПовторите попытку:\n`пример: +380123456789`",
                                            'parse_mode' => 'markdown']);
        return 'phone_number_handler';
    }
}

function newOrder3($bot, $response) {
    $db = new mysqli(database[0], database[1], database[2], database[3]);
    $filename = 'order_id';
    $json = file_get_contents ($filename.'.json');
    $data = json_decode ($json, true);


    $sql = "UPDATE orders
    SET address = '".$response['text']."'
    WHERE id = '".$data[$response['from']['id']]."'";
    $db->query($sql);
    $db->close();
    new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                    'text' => "Укажите товар:\n`пример: 3.дз.100+2.кф.250/3.дз.100\n(число, точка, две буквы (строчные), точка, число, плюс (в конце строки плюс не нужен))`",
                                    'parse_mode' => 'markdown']);
    return 'product_handler';
}   

function newOrder4($bot, $response) {
    $pattern = '/^\d+\.[а-яё_]{2}\.\d+(\+\d+\.[а-яё_]{2}\.\d+){0,}$/u';
    if (preg_match($pattern, $response['text'])) {
        $db = new mysqli(database[0], database[1], database[2], database[3]);

        $filename = 'order_id';
        $json = file_get_contents ($filename.'.json');
        $data = json_decode ($json, true);

        $sql = "UPDATE orders
        SET product = '".$response['text']."'
        WHERE id = '".$data[$response['from']['id']]."'";
        $db->query($sql);
        $db->close();
        $keyboard = json_encode([
            "inline_keyboard" => [
                [
                    [
                        "text" => "Наличными",
                        "callback_data" => "by_cash"
                    ],
                ],
                [
                    [
                        "text" => "Предоплата",
                        "callback_data" => "by_prepay"
                    ],
                ],
                [
                    [
                        "text" => "Оплачено",
                        "callback_data" => "have_paid"
                    ],
                ]
            ]
        ]);
        new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                        'text' => "Выберите способ оплаты:",
                                        'parse_mode' => 'markdown',
                                        'reply_markup' => $keyboard]);
        return 'pay_method_handler';
    }
    elseif (!preg_match($pattern, $response['text'])) {
        new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                        'text' => "_Некорректный формат товава_\nПовторите попытку:\n`пример: 3.дз.100+2.кф.250/3.дз.100 (число, точка, две буквы(строчные), число, плюс(в конце строки плюс не нужен))`",
                                        'parse_mode' => 'markdown']);
        return 'product_handler';
    }
}

function newOrder5($bot, $response, $edit = false) {
    if ($edit === false) {
        if ($response['data'] === 'by_cash') {
            $response['text'] = '0';
        }
        elseif ($response['data'] === 'by_prepay'){
            $response['text'] = 'false';
        }
        elseif ($response['data'] === 'have_paid'){
            $response['text'] = 'true';
        }
        $db = new mysqli(database[0], database[1], database[2], database[3]);

        $filename = 'order_id';
        $json = file_get_contents ($filename.'.json');
        $data = json_decode ($json, true);

        $sql = "UPDATE orders
        SET payment = '".$response['text']."'
        WHERE id = '".$data[$response['from']['id']]."'";
        $db->query($sql);
        $sql = "SELECT *
        FROM orders
        WHERE id='".$data[$response['from']['id']]."'";
        $full_order = ($db->query($sql));
        $full_order = ($full_order->fetch_assoc());
    }

    elseif ($edit === true) {
        $db = new mysqli(database[0], database[1], database[2], database[3]);

        $filename = 'order_id';
        $json = file_get_contents ($filename.'.json');
        $data = json_decode ($json, true);

        $sql = "SELECT *
        FROM orders
        WHERE id='".$data[$response['from']['id']]."'";
        $full_order = ($db->query($sql));
        $full_order = ($full_order->fetch_assoc());

    }
    $db->close();

    $is_empty = (empty($full_order));
    
    if ($is_empty === false) {
        if ($full_order['payment'] === '0') {
            $full_order['payment'] = 'Наличными';
        }
        elseif ($full_order['payment'] === 'false'){
            $full_order['payment'] = 'Предоплата';
        }
        elseif ($full_order['payment'] === 'true'){
            $full_order['payment'] = 'Оплачено';
        }
        $keyboard = json_encode([
            "keyboard" => [
                [
                    [
                        "text" => "Подтвердить",
                    ],
                ],
                [
                    [
                        "text" => "Изменить номер телефона",
                    ],
                ],
                [
                    [
                        "text" => "Изменить ФИО и адрес",
                    ],
                ],
                [
                    [
                        "text" => "Изменить товар",
                    ],
                ],
                [
                    [
                        "text" => "Изменить способ оплаты",
                    ],
                ],
            ],
            "resize_keyboard" => True,
            "one_time_keyboard" => True
        ]);
        new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                            'text' => "*Вот как выглядит заказ:*\n\nНомер телефона: ".$full_order['phone_number']."\nФИО и адрес: ".$full_order['address']."\nТовар: ".$full_order['product']."\nСпособ оплаты: ".$full_order['payment']."\nДата и время: ".$full_order['date']." ".$full_order['time']."\nСоздатель: [".'Пользователь'."]"."(tg://user?id=".$full_order['created_by'].")",
                                            'parse_mode' => 'markdown',
                                            'reply_markup' => $keyboard]);
        return 'checking_order_handler';
    }
    elseif ($is_empty === true) {
        new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                        'text' => "*Заказ с таким номером не существует*\nПовторите попытку:",
                                        'parse_mode' => 'markdown']);
        return 'database';
    }
}

function newOrder6 ($bot, $response) {
    $return = hideDatabase ($bot, $response, true);
    return $return;
}



function changePhoneNumber($bot, $response) {
    newOrder ($bot, $response);
    return 'phone_number_handler2';
}

function changePhoneNumber2($bot, $response) {
    $pattern = "/^\+380\d{9}$/";
    if (preg_match($pattern, $response['text'])) {
        $db = new mysqli(database[0], database[1], database[2], database[3]);

        $filename = 'order_id';
        $json = file_get_contents ($filename.'.json');
        $data = json_decode ($json, true);

        $sql = "UPDATE orders
        SET phone_number = '".$response['text']."'
        WHERE id = '".$data[$response['from']['id']]."'"; //строчка выше
        $db->query($sql);
        $sql = "SELECT *
        FROM orders
        WHERE id='".$data[$response['from']['id']]."'";
        $full_order = ($db->query($sql));
        $full_order = ($full_order->fetch_assoc());
        if ($full_order['payment'] === '0') {
            $full_order['payment'] = 'Наличными';
        }
        elseif ($full_order['payment'] === 'false'){
            $full_order['payment'] = 'Предоплата';
        }
        elseif ($full_order['payment'] === 'true'){
            $full_order['payment'] = 'Оплачено';
        }
        $db->close();
        $keyboard = json_encode([
            "keyboard" => [
                [
                    [
                        "text" => "Подтвердить",
                    ],
                ],
                [
                    [
                        "text" => "Изменить номер телефона",
                    ],
                ],
                [
                    [
                        "text" => "Изменить ФИО и адрес",
                    ],
                ],
                [
                    [
                        "text" => "Изменить товар",
                    ],
                ],
                [
                    [
                        "text" => "Изменить способ оплаты",
                    ],
                ],
            ],
            "resize_keyboard" => True,
            "one_time_keyboard" => True
        ]);
        new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                            'text' => "*Вот как выглядит заказ:*\n\nНомер телефона: ".$full_order['phone_number']."\nАдрес доставки: ".$full_order['address']."\nТовар: ".$full_order['product']."\nСпособ оплаты: ".$full_order['payment']."\nДата и время: ".$full_order['date']." ".$full_order['time']."\nСоздатель: [".$response['from']['first_name']."]"."(tg://user?id=".$response['from']['id'].")",
                                            'parse_mode' => 'markdown',
                                            'reply_markup' => $keyboard]);
        return 'checking_order_handler';
    }
    elseif (!preg_match($pattern, $response['text'])) {
        new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                            'text' => "_Номер телефон ввёдён некорректно_\nПовторите попытку:\n`пример: +380123456789`",
                                            'parse_mode' => 'markdown']);
        return 'phone_number_handler2';
    }
}

function changeAddress($bot, $response) {
    $keyboard = json_encode ([
        "remove_keyboard" => True
    ]);
    new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                            'text' => "Введите ФИО и адрес:",
                                            'parse_mode' => 'markdown',
                                            'reply_markup' => $keyboard]);
    return 'address_handler2';
}

function changeAddress2($bot, $response) {
    $db = new mysqli(database[0], database[1], database[2], database[3]);

    $filename = 'order_id';
    $json = file_get_contents ($filename.'.json');
    $data = json_decode ($json, true);

    $sql = "UPDATE orders
    SET address = '".$response['text']."'
    WHERE id = '".$data[$response['from']['id']]."'";
    $db->query($sql);
    $sql = "SELECT *
    FROM orders
    WHERE id='".$data[$response['from']['id']]."'";
    $full_order = ($db->query($sql));
    $full_order = ($full_order->fetch_assoc());
    if ($full_order['payment'] === '0') {
        $full_order['payment'] = 'Наличными';
    }
    elseif ($full_order['payment'] === 'false'){
        $full_order['payment'] = 'Предоплата';
    }
    elseif ($full_order['payment'] === 'true'){
        $full_order['payment'] = 'Оплачено';
    }
    $db->close();
    $keyboard = json_encode([
        "keyboard" => [
            [
                [
                    "text" => "Подтвердить",
                ],
            ],
            [
                [
                    "text" => "Изменить номер телефона",
                ],
            ],
            [
                [
                    "text" => "Изменить ФИО и адрес",
                ],
            ],
            [
                [
                    "text" => "Изменить товар",
                ],
            ],
            [
                [
                    "text" => "Изменить способ оплаты",
                ],
            ],
        ],
        "resize_keyboard" => True,
        "one_time_keyboard" => True
    ]);
    new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                        'text' => "*Вот как выглядит заказ:*\n\nНомер телефона: ".$full_order['phone_number']."\nАдрес доставки: ".$full_order['address']."\nТовар: ".$full_order['product']."\nСпособ оплаты: ".$full_order['payment']."\nДата и время: ".$full_order['date']." ".$full_order['time']."\nСоздатель: [".$response['from']['first_name']."]"."(tg://user?id=".$response['from']['id'].")",
                                        'parse_mode' => 'markdown',
                                        'reply_markup' => $keyboard]);
    return 'checking_order_handler';
}

function changeProduct ($bot, $response) {
    new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                    'text' => "Укажите товар:\n`пример: 3.дз.100+2.кф.250/3.дз.100\n(число, точка, две буквы (строчные), точка, число, плюс (в конце строки плюс не нужен))`",
                                    'parse_mode' => 'markdown']);
    return 'product_handler2';
}

function changeProduct2 ($bot, $response) {
    $pattern = '/^\d+\.[а-яё_]{2}\.\d+(\+\d+\.[а-яё_]{2}\.\d+){0,}$/u';
    if (preg_match($pattern, $response['text'])) {
        $db = new mysqli(database[0], database[1], database[2], database[3]);

        $filename = 'order_id';
        $json = file_get_contents ($filename.'.json');
        $data = json_decode ($json, true);

        $sql = "UPDATE orders
        SET product = '".$response['text']."'
        WHERE id = '".$data[$response['from']['id']]."'";
        $db->query($sql);
        $sql = "SELECT *
        FROM orders
        WHERE id='".$data[$response['from']['id']]."'";
        $full_order = ($db->query($sql));
        $full_order = ($full_order->fetch_assoc());
        if ($full_order['payment'] === '0') {
            $full_order['payment'] = 'Наличными';
        }
        elseif ($full_order['payment'] === 'false'){
            $full_order['payment'] = 'Предоплата';
        }
        elseif ($full_order['payment'] === 'true'){
            $full_order['payment'] = 'Оплачено';
        }
        $db->close();
        $keyboard = json_encode([
            "keyboard" => [
                [
                    [
                        "text" => "Подтвердить",
                    ],
                ],
                [
                    [
                        "text" => "Изменить номер телефона",
                    ],
                ],
                [
                    [
                        "text" => "Изменить ФИО и адрес",
                    ],
                ],
                [
                    [
                        "text" => "Изменить товар",
                    ],
                ],
                [
                    [
                        "text" => "Изменить способ оплаты",
                    ],
                ],
            ],
            "resize_keyboard" => True,
            "one_time_keyboard" => True
        ]);
        new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                            'text' => "*Вот как выглядит заказ:*\n\nНомер телефона: ".$full_order['phone_number']."\nАдрес доставки: ".$full_order['address']."\nТовар: ".$full_order['product']."\nСпособ оплаты: ".$full_order['payment']."\nДата и время: ".$full_order['date']." ".$full_order['time']."\nСоздатель: [".$response['from']['first_name']."]"."(tg://user?id=".$response['from']['id'].")",
                                            'parse_mode' => 'markdown',
                                            'reply_markup' => $keyboard]);
        return 'checking_order_handler';
    }
    elseif (!preg_match($pattern, $response['text'])) {
        new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                        'text' => "_Некорректный формат товава_\nПовторите попытку:\n`пример: 3.дз.100+2.кф.250/3.дз.100 (число, точка, две буквы(строчные), число, плюс(в конце строки плюс не нужен))`",
                                        'parse_mode' => 'markdown']);
        return 'product_handler2';
    }
}

function changePayMethod ($bot, $response) {
    $keyboard = json_encode([
        "inline_keyboard" => [
            [
                [
                    "text" => "Наличными",
                    "callback_data" => "by_cash"
                ],
            ],
            [
                [
                    "text" => "Предоплата",
                    "callback_data" => "by_prepay"
                ],
            ],
            [
                [
                    "text" => "Оплачено",
                    "callback_data" => "have_paid"
                ],
            ]
        ]
    ]);
    new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                    'text' => "Выберите способ оплаты:",
                                    'parse_mode' => 'markdown',
                                    'reply_markup' => $keyboard]);
    return 'pay_method_handler';
}



function showDatabase ($bot, $response, $index = 0, $edit = false) {
    $db = new mysqli(database[0], database[1], database[2], database[3]);
    $value = 10;
    $offset = $value + 1;
    $sql = "SELECT id, phone_number, address, product
    FROM orders
    ORDER BY id DESC
    LIMIT ".$index.", ".$offset."";
    $orders = ($db->query($sql));
    $orders = ($orders->fetch_all());
    $db->close();
    for ($i = 0, $text = null; $i != count($orders); $i++) {
        if ($i > 0) {
            $text .= "\n\n";
        }
        $text .= '*'.$orders[$i][0].'*. '.$orders[$i][1].', '.$orders[$i][3].",\n".$orders[$i][2].'';
    }
    $keyboard = [
        "inline_keyboard" => [
            [

            ],
            [
                [
                    "text" => "Назад",
                    "callback_data" => "back"
                ],
            ]
        ]
    ];
    $keyboard_hide = json_encode ([
        "remove_keyboard" => True
    ]);
    if ($index != 0) {
        $keyboard['inline_keyboard'][0][0]['text'] = "\xE2\x97\x80";
        $keyboard['inline_keyboard'][0][0]['callback_data'] = 'previous';
    }
    if (array_key_exists($value, $orders)) {
        if (array_key_exists('0', $keyboard['inline_keyboard'][0])) {
            $i = 1;
        }
        elseif (!array_key_exists('0', $keyboard['inline_keyboard'][0])) {
            $i = 0;
        }
        $keyboard['inline_keyboard'][0][$i]['text'] = "\xE2\x96\xB6";
        $keyboard['inline_keyboard'][0][$i]['callback_data'] = 'next';
    }
    if ($edit === false) {
        new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                        'text' => '*СПИСОК ЗАКАЗОВ:*',
                                        'parse_mode' => 'markdown',
                                        'reply_markup' => $keyboard_hide]);
        new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                        'text' => $text,
                                        'parse_mode' => 'markdown',
                                        'reply_markup' => json_encode($keyboard)]);
    }
    elseif ($edit === true) {
        new Answer($bot, 'editMessageText', ['chat_id' => $response['from']['id'],
                                        'text' => $text,
                                        'parse_mode' => 'markdown',
                                        'reply_markup' => json_encode($keyboard),
                                        'message_id' => $response['message']['message_id']]);
    }
    return 'database';
}

function showDatabaseNext ($bot, $response) {
    $filename = 'list';
    $index = 10;
    $json = file_get_contents ($filename.'.json');
    $data = json_decode ($json, true);

    $user_id = $response['from']['id'];

    if (!array_key_exists ($user_id, $data)) {
        $data[$user_id] = '0';
        $json = json_encode ($data);
        file_put_contents ($filename . '.json', $json);
    }

    $user_index = $data[$user_id] + $index;

    $return = showDatabase ($bot, $response, $user_index, true);
    
    $data[$user_id] = $user_index;
    $json = json_encode ($data);
    file_put_contents ($filename . '.json', $json);
    return $return;
}

function showDatabasePrevious ($bot, $response) { //if $index > $user_index => $user_index = 0
    $filename = 'list';
    $index = 10;
    $json = file_get_contents ($filename.'.json');
    $data = json_decode ($json, true);

    $user_id = $response['from']['id'];

    if (!array_key_exists ($user_id, $data)) {
        $data[$user_id] = 0;
        $json = json_encode ($data);
        file_put_contents ($filename . '.json', $json);
    }

    if ($index > $data[$user_id] ) {
        $user_index = 0;
    }
    else {
        $user_index = $data[$user_id] - $index;
    }

    $return = showDatabase ($bot, $response, $user_index, true);

    $data[$user_id] = $user_index;
    $json = json_encode ($data);
    file_put_contents ($filename . '.json', $json);
    return $return;
}

function hideDatabase ($bot, $response, $edit = false) {

    if ($edit === false) {
        $filename = 'list';
        $index = 10;
        $json = file_get_contents ($filename.'.json');
        $data = json_decode ($json, true);
    
        $user_id = $response['from']['id'];
    
        if (!array_key_exists ($user_id, $data)) {
            $data[$user_id] = 0;
            $json = json_encode ($data);
            file_put_contents ($filename . '.json', $json);
        }
    
        $data[$user_id] = 0;
        $json = json_encode ($data);
        file_put_contents ($filename . '.json', $json);
    }

    $keyboard = json_encode([
        "keyboard" => [
            [
                [
                    "text" => "Новый заказ",
                ],
            ],
            [
                [
                    "text" => "База данных",
                ],
            ],
            [
                [
                    "text" => "Статус оплаты",
                ],
            ]
        ],
        "resize_keyboard" => True,
        "one_time_keyboard" => True
    ]);
    new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                    'text' => '_Вы возвращены обратно в меню_',
                                    'parse_mode' => 'markdown',
                                    'reply_markup' => $keyboard]);
    return 'main_menu';

}

function showDatabaseOrder ($bot, $response) {
    settype ($response['text'], 'int');
    if ($response['text'] === 0) {
        new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                        'text' => "*Введено НЕчисловое значение*\nПовторите попытку:",
                                        'parse_mode' => 'markdown']);
        return 'database';
    }
    else {
        $filename = 'order_id';
        $json = file_get_contents ($filename.'.json');
        $data = json_decode ($json, true);
    
        $data[$response['from']['id']] = $response['text'];
    
        $json = json_encode ($data);
        file_put_contents ($filename.'.json', $json);
    
        $return = newOrder5($bot, $response, true);
        return $return;
    }
}



function showPaymentStatus ($bot, $response, $index = 0, $edit = false) {
    $db = new mysqli(database[0], database[1], database[2], database[3]);

    if (array_key_exists('data', $response)) {
        $filename = 'order_id';
        $json = file_get_contents ($filename.'.json');
        $data = json_decode ($json, true);

        switch ($response['data']) {
            case "have_paid":
                $sql = "UPDATE orders
                SET payment = 'true'
                WHERE id = ".$data[$response['from']['id']]."";
                $db->query($sql);
                new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                                'text' => '_Способ оплаты успешно изменён_',
                                                'parse_mode' => 'markdown']);
                break;
            case "by_cash":
                $sql = "UPDATE orders
                SET payment = '0'
                WHERE id = ".$data[$response['from']['id']]."";
                $db->query($sql);
                new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                                'text' => '_Способ оплаты успешно изменён_',
                                                'parse_mode' => 'markdown']);
                break;
            case "back":
                new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                                'text' => '_Вы возвращены к списку заказов_',
                                                'parse_mode' => 'markdown']);
                break;
        }
    }

    $value = 5;
    $offset = $value + 1;
    $sql = "SELECT id, phone_number, address, product, payment
    FROM orders
    WHERE payment = 'false'
    ORDER BY id DESC
    LIMIT ".$index.", ".$offset."";
    $orders = ($db->query($sql));
    $orders = ($orders->fetch_all());
    $db->close();

    for ($i = 0, $text = null; $i != count($orders); $i++) {
        if ($i > 0) {
            $text .= "\n\n";
        }
        $text .= '*'.$orders[$i][0].'*. '.$orders[$i][1].', '.$orders[$i][3].",\n".$orders[$i][2]."\nПредоплата (неоплачено)";
    }
    $keyboard = [
        "inline_keyboard" => [
            [

            ],
            [
                [
                    "text" => "Назад",
                    "callback_data" => "back"
                ],
            ]
        ]
    ];
    $keyboard_hide = json_encode ([
        "remove_keyboard" => True
    ]);
    if ($index != 0) {
        $keyboard['inline_keyboard'][0][0]['text'] = "\xE2\x97\x80";
        $keyboard['inline_keyboard'][0][0]['callback_data'] = 'previous';
    }
    if (array_key_exists($value, $orders)) {
        if (array_key_exists('0', $keyboard['inline_keyboard'][0])) {
            $i = 1;
        }
        elseif (!array_key_exists('0', $keyboard['inline_keyboard'][0])) {
            $i = 0;
        }
        $keyboard['inline_keyboard'][0][$i]['text'] = "\xE2\x96\xB6";
        $keyboard['inline_keyboard'][0][$i]['callback_data'] = 'next';
    }
    if ($edit === false) {
        new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                        'text' => '*СТАТУС ЗАКАЗОВ:*',
                                        'parse_mode' => 'markdown',
                                        'reply_markup' => $keyboard_hide]);
        new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                        'text' => $text,
                                        'parse_mode' => 'markdown',
                                        'reply_markup' => json_encode($keyboard)]);
    }
    elseif ($edit === true) {
        new Answer($bot, 'editMessageText', ['chat_id' => $response['from']['id'],
                                        'text' => $text,
                                        'parse_mode' => 'markdown',
                                        'reply_markup' => json_encode($keyboard),
                                        'message_id' => $response['message']['message_id']]);
    }
    return 'check_payment';
}

function showPaymentStatusPrevious ($bot, $response) {
    $filename = 'list';
    $index = 5;
    $json = file_get_contents ($filename.'.json');
    $data = json_decode ($json, true);

    $user_id = $response['from']['id'];

    if (!array_key_exists ($user_id, $data)) {
        $data[$user_id] = 0;
        $json = json_encode ($data);
        file_put_contents ($filename . '.json', $json);
    }

    if ($index > $data[$user_id] ) {
        $user_index = 0;
    }
    else {
        $user_index = $data[$user_id] - $index;
    }

    $return = showPaymentStatus ($bot, $response, $user_index, true);

    $data[$user_id] = $user_index;
    $json = json_encode ($data);
    file_put_contents ($filename . '.json', $json);
    return $return;
}

function showPaymentStatusNext ($bot, $response) {
    $filename = 'list';
    $index = 5;
    $json = file_get_contents ($filename.'.json');
    $data = json_decode ($json, true);

    $user_id = $response['from']['id'];

    if (!array_key_exists ($user_id, $data)) {
        $data[$user_id] = '0';
        $json = json_encode ($data);
        file_put_contents ($filename . '.json', $json);
    }

    $user_index = $data[$user_id] + $index;

    $return = showPaymentStatus ($bot, $response, $user_index, true);
    
    $data[$user_id] = $user_index;
    $json = json_encode ($data);
    file_put_contents ($filename . '.json', $json);
    return $return;
}

function hidePaymentStatus ($bot, $response) {
    $return = hideDatabase ($bot, $response, true);
    return $return;
}

function showPaymentStatusOrder ($bot, $response) {
    settype ($response['text'], 'int');
    if ($response['text'] === 0) {
        new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                        'text' => "*Введено НЕчисловое значение*\nПовторите попытку:",
                                        'parse_mode' => 'markdown']);
        return 'check_payment';
    }
    else {
        $db = new mysqli(database[0], database[1], database[2], database[3]);
        $sql = "SELECT *
        FROM orders
        WHERE id = ".$response['text']."
        LIMIT 1";
        $order = ($db->query($sql));
        $order = ($order->fetch_assoc());

        $is_empty = (empty($order));

        if ($is_empty === false) {
            $data[$response['from']['id']] = $response['text'];
            $json = json_encode ($data);
            $filename = 'order_id';
            file_put_contents ($filename.'.json', $json);

            $keyboard = json_encode([
                "inline_keyboard" => [
                    [
                        [
                            "text" => "Оплачено \xE2\x9C\x85",
                            "callback_data" => "have_paid"
                        ],
                    ],
                    [
                        [
                            "text" => "Наличными",
                            "callback_data" => "by_cash"
                        ],
                    ],
                    [
                        [
                            "text" => "Назад",
                            "callback_data" => "back"
                        ],
                    ],
                ]
            ]);

            switch ($order['payment']) {
                case "0":
                    $order['payment'] = 'Наличными';
                    break;
                case "false":
                    $order['payment'] = 'Предоплата (неоплачено)';
                    break;
                case "true":
                    $order['payment'] = 'Оплачено';
                    break;
            }

            new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                        'text' => "*Данные о заказе:*\n\nНомер телефона: ".$order['phone_number']."\nФИО и адрес: ".$order['address']."\nТовар: ".$order['product']."\nСпособ оплаты: ".$order['payment']."\nДата и время: ".$order['date']." ".$order['time']."\nСоздатель: [".'Пользователь'."]"."(tg://user?id=".$order['created_by'].")",
                                        'parse_mode' => 'markdown',
                                        'reply_markup' => $keyboard]);
            return 'check_payment_order';
        }
        elseif ($is_empty === true) {
            new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                        'text' => "*Заказ с таким номером не существует*\nПовторите попытку:",
                                        'parse_mode' => 'markdown']);
            return 'check_payment';
        }
    }
}



function stop ($bot, $response) {
    $keyboard = json_encode ([
        "remove_keyboard" => True
    ]);
    new Answer($bot, 'sendMessage', ['chat_id' => $response['from']['id'],
                                    'text' => 'Пока, прощай',
                                    'reply_to_message_id' => $response['message_id'],
                                    'reply_markup' => $keyboard]);
}



while (TRUE) {
    $Update->getUpdate($Update->lastupdate);
    if (!empty ($Update->response)) {  

        $conv_handler->getFilter($Update->response);
    }
}
?>