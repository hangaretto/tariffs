<?php
/**
 * Created by PhpStorm.
 * User: vadim
 * Date: 23.08.17
 * Time: 9:12
 */

namespace Magnetar\Tariffs\Services;

use Magnetar\Tariffs\Models\UserBalance;
use Magnetar\Tariffs\NumericHelper;
use Magnetar\Tariffs\References\UserBalanceReference;
use Magnetar\Tariffs\Services\Yandex\MWS;
use Magnetar\Tariffs\Services\Yandex\Settings;

class UserBalanceService
{
    /**
     * Insert user transaction
     *
     * @param integer $user_id
     * @param string $action
     * @param float $amount
     * @param array $info
     * @return int id
     * @throws
     */
    public static function create($user_id, $action, $amount, $info = [])
    {
        $amount = NumericHelper::toPositive($amount);

//        if(array_key_exists($action, UserBalanceReference::INFO_TEMPLATES) == false)
//            throw new \Exception('not.found.transaction_template');

        if(config('magnetar.tariffs.billing.templates.'.$action) == null)
            throw new \Exception('not.found.transaction_template');

        $reference = config('magnetar.tariffs.billing.templates.'.$action);

        if($reference['enabled'] != true)
            throw new \Exception('transaction_template.disabled');

        $template = $reference['template'];
        foreach ($info as $key => $item)
            $template = str_replace(':'.$key, $item, $template);

        $action = $reference['action'];
        if($action == '-') {
            $sum = self::currentBalance($user_id);
            if($sum - $amount < 0)
                throw new \Exception('need.money');
        }

        $user_balance = new UserBalance();
        $user_balance->amount = $action == '-' ? NumericHelper::toNegative($amount) : $amount;
        $user_balance->info = $template;
        $user_balance->user_id = $user_id;
        $user_balance->save();

        if($reference['notification'] == true)
            $user_balance->sendNotification();

        return $user_balance->id;
    }

    /**
     * Return current balance.
     *
     * @param int $user_id
     * @return float
     */
    public static function currentBalance($user_id)
    {
        return floatval(UserBalance::where('user_id', $user_id)->sum('amount'));
    }

    /**
     * Buy balance, and return current sum.
     *
     * @param int $user_id
     * @param float $amount
     * @return float
     */
    public static function buyBalance($user_id, $amount)
    {
//        $tx_code = UserDataServices::getData($user_id, 'last_code'); // todo::
//
//        if($tx_code != null) {
//            $mws = new MWS(new Settings());
//            $mws->repeatCardPayment($tx_code, $amount, $user_id);
//        }

        return UserBalanceService::currentBalance($user_id);
    }
}