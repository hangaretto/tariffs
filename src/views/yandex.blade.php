<html>
<head>
    <meta charset="utf-8">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <title>Тестовая платежная форма</title>
</head>
<body>
<form action="https://demomoney.yandex.ru/eshop.xml" method="post">
    <!-- Обязательные поля -->
    <input name="shopId" value="160091" type="hidden"/>
    <input name="scid" value="558710" type="hidden"/>
    <input name="customerNumber" value="{{ $id }}" type="hidden"/>
    <input name="sum" value="100">
    <input name="module" value="magnetar_billing" type="hidden">
    <input name="paymentType" value="AC" type="hidden">
    <input type="submit" value="Пополнить"/>
</form>
</body>
</html>