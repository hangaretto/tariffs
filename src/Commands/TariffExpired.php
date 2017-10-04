<?php

namespace Magnetar\Tariffs\Commands;

use App\Models\Billing\UserTariff;
use Carbon\Carbon;
use Illuminate\Console\Command;
use DB;
use Magnetar\Tariffs\Models\Object;
use Magnetar\Tariffs\Models\UserObject;
use Magnetar\Tariffs\References\UserBalanceReference;
use Magnetar\Tariffs\Services\UserBalanceService;

class TariffExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove/buy expired tariffs';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $user_objects = UserObject::where(function ($query) {
                $query->whereNotNull('object_id')
                    ->orWhereNotNull('module_id');
            })
            ->where('expired_at', '<', DB::raw('NOW()'))
            ->get();

        $ar_objects = $ar_prices = $ar_objects_ids = [];
        foreach($user_objects as $object) {

            $ar_objects[$object->user_id][$object->object_id][] = $object;
            $ar_prices[$object->user_id][$object->object_id] = $object->price;

            if(!in_array($object->object_id, $ar_objects_ids))
                $ar_objects_ids[] = $object->object_id;

        }

        if(count($ar_objects_ids) > 0)
            $objects = Object::whereIn('id', $ar_objects_ids)->get()->keyBy('id');

        foreach ($ar_prices as $user_id => $user_prices) {

            $necessary_sum = array_sum($user_prices);
            $user_balance = UserBalanceService::currentBalance($user_id);

            if($necessary_sum > $user_balance)
                $user_balance = UserBalanceService::buyBalance($user_id, $necessary_sum - $user_balance);

            foreach ($user_prices as $object_id => $price) {

                if($necessary_sum <= $user_balance) {

                    UserBalanceService::create($user_id, UserBalanceReference::BUY, $price, ['name' => $objects[$object_id]->name]);

                    foreach ($ar_objects[$user_id][$object_id] as &$tariff) {

                        if(isset($tariff->expired_at)) {

//                            $current = Carbon::parse($tariff->expired_at);
                            $current = Carbon::now();

                            switch ($tariff->period_type) {
                                case 'day':
                                    $tariff->expired_at = $current->addDays($tariff->period);
                                    break;
                                case 'week':
                                    $tariff->expired_at = $current->addWeeks($tariff->period);
                                    break;
                                case 'month':
                                    $tariff->expired_at = $current->addMonths($tariff->period);
                                    break;
                                case 'year':
                                    $tariff->expired_at = $current->addYears($tariff->period);
                                    break;
                            }

                            $tariff->save();

                        }

                    }

                } else {

                    foreach ($ar_objects[$user_id][$object_id] as &$tariff)
                        $tariff->delete();

                }

                unset($tariff);

            }

        }

    }

}
