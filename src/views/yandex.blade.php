<form action="{{ config('magnetar.tariffs.services.yandex.testmode') == true ? "https://demomoney.yandex.ru/eshop.xml" : "https://money.yandex.ru/eshop.xml" }}" method="post">
    <input name="shopId" value="{{ config('magnetar.tariffs.services.yandex.shopId') }}" type="hidden"/>
    <input name="scid" value="{{ config('magnetar.tariffs.services.yandex.scid') }}" type="hidden"/>
    <input name="customerNumber" value="{{ $user->id }}" type="hidden"/>
    <input name="sum" value="{{ $amount }}" type="hidden">
    <input name="module" value="magnetar_billing" type="hidden">
    <input name="paymentType" value="AC" type="hidden">
    <input type="submit" value="Пополнить"/>
    <input name="ym_merchant_receipt"
       value='{"customerContact": "{{ $user->email }}","taxSystem": 1, "items":[{"quantity": 1, "price": {"amount": {{ $amount }}}, "tax": 1, "text": {{ json_encode(config('magnetar.tariffs.services.yandex.receipt_text'), JSON_UNESCAPED_UNICODE) }}}]}'
       type="hidden"/>
</form>