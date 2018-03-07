<?php

namespace Magnetar\Tariffs\Commands;

use App\Models\Billing\UserTariff;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Magnetar\Tariffs\Services\UserObjectService;

class ObjectNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification, if enough balance.';

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
        UserObjectService::sendNotifications();
    }
}
