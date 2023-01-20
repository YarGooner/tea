-------------------
РАЗВОРАЧИВАНИЕ ПРОЕКТА
-------------------
1.	Слить содержимое в корень сайта
    - Вариант 1: скачать архив, загрузить файлы на хостинг
    - Вариант 2: напрямую копировать содержимое мастер-ветки репозитория на хостинг 

2.  Прописать webroot на htdocs

3.	Скачать зависимости:

    >$ php composer update

4. Проинициировать приложение:

    >$ php init
    
	Выбрать необходимое окружение:
	- 0 Песочница PEPPERS
	- 1 DEV-Сервер Клиента
	- 2 PROD-Сервер Клиента
	
	Важно! При применении этой команды могут затереться измененные руками файлы на сервере. Файлы для перезаписи берутся отсюда: \environments\

5.	Прописать настройки БД:
	common\config\main-local.php
		components => db

6. 	Применить миграции для создания структуры БД:
	>$ php yii migrate

7.	Создать админа 
    >$ php yii user-admin/create

8. Прописать ключи от соц.сетей:
    common\modules\auth\Keys.php

9. Для отправки почты нужно внести настройки соединения в разделе "Настройки почты" в Админ.панели

-------------------
После указанных выше действий панель администратора будет доступна по адресу <site>/admin, а точка входа для api - <site>/api/v1/<необходимый_раздел>




=============================================================================
Сводный документ
https://docs.google.com/document/d/1B4hUFv2Ocgokj8TeOJiLHMUbpuWPxg3rlscVOVamTJ8/edit?usp=sharing

Настройки шаблона
https://docs.google.com/document/d/1T8tlEjj7-cFaCm-j4Pf9wi2_v0288quXb_U9IGEK0XE/edit#heading=h.9zs6uejuk65n

Развёртывание Yii2 проекта
https://docs.google.com/document/d/1FBwsMFdWx4CgZvxk7xk2d_1KPf5xE3YytTKBOrvTsK4/edit#heading=h.9nw9wcdllpgj

Тестирование с помощью API Tester
https://docs.google.com/document/d/10cRIOboeV2gj4Pw4MaZaMKh2GeHpYFAmpQlblxh4Uuc/edit#heading=h.9zs6uejuk65n
