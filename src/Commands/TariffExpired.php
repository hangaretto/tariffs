<?php

namespace Magnetar\Tariffs\Commands;

use App\Models\Billing\UserTariff;
use Carbon\Carbon;
use Illuminate\Console\Command;
use DB;
use Magnetar\Tariffs\Models\UserObject;

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

        $ar_tariffs = $ar_prices =[];
        foreach($user_objects as $object) {
            $ar_tariffs[$object->object_id][] = $object;
            $ar_prices[$object->object_id] = $object->price;
        }

        foreach ($ar_prices as $tariff_id => $price) {
            // TODO: buy

            $is_buy = true;

            if($is_buy) {
                foreach ($ar_tariffs[$tariff_id] as &$tariff) {

                    if(isset($tariff->expired_at)) {

                        $current = Carbon::parse($tariff->expired_at);
//                        $current = Carbon::now();

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

                foreach ($ar_tariffs[$tariff_id] as &$tariff)
                    $tariff->delete();

            }

            unset($tariff);

        }

    }

}
