<h3>Система тарифов</h3>

<p>Добавить в confing/app.php в блок providers - Magnetar\Tariffs\TariffsServiceProvider::class,</p>
<p>php artisan vendor:publish: миграции и конфиги</p>
<p>Добавить задачу в крон billing:expired (от 10 минут, до 1 дня)</p>