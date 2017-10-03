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

        return UserBalance::where('user_id', $user_id)->sum('amount');

    }
}