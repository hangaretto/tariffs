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
    public static function create($user_id, $action, $amount, $info = []) {

        $amount = NumericHelper::toPositive($amount);

        if(array_key_exists($action, UserBalanceReference::INFO_TEMPLATES) == false)
            throw new \Exception('not.found.transaction_template');

        $reference = UserBalanceReference::INFO_TEMPLATES[$action];

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
        $user_balance->amount = $action  == '-' ? NumericHelper::toNegative($amount) : $amount;
        $user_balance->info = $template;
        $user_balance->user_id = $user_id;
        $user_balance->save();

        return $user_balance->id;

    }

    /**
     * Return current balance.
     *
     * @param int $user_id
     * @return float
     */
    public static function currentBalance($user_id) {

        return floatval(UserBalance::where('user_id', $user_id)->sum('amount'));

    }

    /**
     * Return last transaction code.
     *
     * @param int $user_id
     * @return string
     */
    public static function getLastCode($user_id) {

        return null;
        return '2000001627031';

    }
    
    /**
     * Buy balance, and return current sum.
     *
     * @param int $user_id
     * @param float $amount
     * @return float
     */
    public static function buyBalance($user_id, $amount) {

        $tx_code = UserBalanceService::getLastCode($user_id);

        if($tx_code != null) {

            $mws = new \Magnetar\Tariffs\Services\Yandex\MWS(new \Magnetar\Tariffs\Services\Yandex\Settings());
            $mws->repeatCardPayment($tx_code, $amount);

        }

        return UserBalanceService::currentBalance($user_id);

    }
}