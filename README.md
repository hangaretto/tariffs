============
composer пакет с системой тарифов
============

<p>Добавить в confing/app.php в блок providers - Magnetar\Tariffs\TariffsServiceProvider::class,</p>
<p>php artisan migrate --path=/vendor/magnetar/tariffs/src/migrations</p>
<p>Добавить задачу в крон billing:expired (от 10 минут, до 1 дня)</p>