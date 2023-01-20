<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,
	
	
	// >>> ADMIN/INFO >>>
	
	'required-php-version' => '7.1',
	// 'upload-max-size' => 1024*1024*10, // Максимальный вес файла для загрузки = 10Mb
	
	// <<< ADMIN/INFO <<<
	
	// DICTIONARIES
    // !!! WARNING! AFTER CHANGING THE VALUES BELOW CHECK UP THE DATA BASE !!!
    'dictionary' => [

        // YES-NO
        'yes-no' => [
            0 => ['name' => 'no', 'description' => 'Нет', 'color' => 'red' ],
            1 => ['name' => 'yes', 'description' => 'Да', 'color' => 'green' ],
        ],

        // ACTIVE
        'active' => [
            0 => ['name' => 'inactive', 'description' => 'Неактивен', 'color' => 'gray' ],
            1 => ['name' => 'active', 'description' => 'Активен', 'color' => 'green' ],
        ],

        // MODERATION STATUS
        /*'moderation-status' => [
            0 => ['name' => 'new', 'description' => 'Новое', 'color' => 'black' ],
            10 => ['name' => 'approved', 'description' => 'Одобрено', 'color' => 'green' ],
            20 => ['name' => 'rejected', 'description' => 'Отклонено', 'color' => 'red' ],
        ],*/


        // ------- APP ---------

        // IMAGE TYPES
        'uploaded-image-type' => [
            1 => ['name' => 'check', 'description' => 'Чек', 'color' => 'black' ],
            2 => ['name' => 'photo', 'description' => 'Фото', 'color' => 'black' ],
        ],

        // CHEATING TYPES
        'cheating-type' => [
            1 => ['name' => 'game-start', 'description' => 'Старт игры' ],
            2 => ['name' => 'game-save', 'description' => 'Сохранение игры' ],
        ],

        'news-status' => [
            \common\models\News::STATUS_PUBLISHED_NO => ['name' => 'no', 'description' => 'Скрыто', 'color' => 'red' ],
            \common\models\News::STATUS_PUBLISHED_YES => ['name' => 'yes', 'description' => 'Опубликовано', 'color' => 'green' ],
        ],

        'moderation-status' => [
            \common\models\Feedback::MODERATION_NEW => ['name' => 'new', 'description' => 'Новое', 'color' => 'black'],
            \common\models\Feedback::MODERATION_ACCEPTED=> ['name' => 'acc', 'description' => 'Принято', 'color' => 'green'],
            \common\models\Feedback::MODERATION_REJECTED => ['name' => 'rej', 'description' => 'Отклонено', 'color' => 'red'],
        ]
    ],
	
];
