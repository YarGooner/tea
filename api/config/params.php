<?php
return [
    'adminEmail' => 'admin@example.com',
	
	// >>> ADMIN/INFO >>>
	
	// 'upload-max-size' => 1024*1024*10, // Максимальный вес файла для загрузки = 10Mb
	
	// <<< ADMIN/INFO <<<


    // >>> API SETTINGS >>>

    // Метод восстановления пароля. Должен быть активен один из списка ниже. по умолчанию - token
    'passwordRestoreType' => 'token', // 1. Отправляем
//    'passwordRestoreType' => 'generate', // 2. Отправляем сгенерированный пароль пользователю

    'signup' => [ // Регистрация
        'enabled_clients' => [
            'email-password' => true,
        ],
        'require' => [
            'rules_accepted' => true, // Необходимо согласиться с правилами
        ],
        'unique' => [
            'email' => true, // Почта должна быть уникальной
        ],
    ],

    // Названия темплейтов писем из папки common/mail. Если записи нет или она пустая письма по событию не отправляются
    'email_on' => [
        'signup' => 'email-confirm',
        'email_changed' => 'email-confirm',
        'send_password' => 'passwordSend',
        'send_password_restore_token' => 'passwordResetToken',
    ],
    
    // <<< API SETTINGS <<<

];
