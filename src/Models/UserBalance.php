<?php
/**
 * Created by PhpStorm.
 * User: vadim
 * Date: 22.09.17
 * Time: 15:14
 */

namespace Magnetar\Tariffs\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Magnetar\Mailing\Jobs\MailingJob;

class UserBalance extends Model {

    protected $table = 'magnetar_tariffs_user_balances';

    /**
     * Send notification to user.
     *
     * @return bool
     */
    public function sendNotification() {

        if(config('magnetar.tariffs.notifications.enabled') != true)
            return false;

        $user = User::find($this->user_id);

        if(!$user)
            return false;

        $data['to'] = $user->email;
        $data['subject'] = config('magnetar.tariffs.notifications.subject');
        $data['html'] = '<p>'.$this->info.'</p>';

        dispath(new MailingJob('email', $data));

        return true;

    }

}