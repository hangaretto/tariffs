<?php

namespace Magnetar\Tariffs\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Magnetar\Tariffs\Models\UserCard;
use Magnetar\Tariffs\ResponseHelper;

class CardCrudController extends Controller
{

    /**
     * Return modules list.
     *
     * @param Request $request
     * @return ResponseHelper
     */
    public function index(Request $request) {

        $out['cards'] = UserCard::get();

        return ResponseHelper::response_success("successful", $out);

    }

    /**
     * Return module by id.
     *
     * @param Request $request
     * @param int $id
     * @return ResponseHelper
     */
    public function show(Request $request, $id) {

        $out['card'] = UserCard::find($id);

        if(!$out['card'])
            return ResponseHelper::response_error("not.found", 404);

        return ResponseHelper::response_success("successful", $out);

    }

    /**
     * Create/update module.
     *
     * @param Request $request
     * @param int $id
     * @return ResponseHelper
     */
    public function process(Request $request, $id) {

        if($id == null)
            $card = new UserCard();
        else {

            $card = UserCard::find($id);

            if(!$card)
                return ResponseHelper::response_error("not.found", 404);

        }

        if ($card->validate($request->all())) {

            $card->user_id = $request->input('user_id');
            $card->active = $request->input('active');

            $card->save();

        } else
            return ResponseHelper::response_error($card->errors(), 400);

    }

    /**
     * Delete module.
     *
     * @param int $id
     * @return ResponseHelper
     */
    public function destroy($id) {

        $card = UserCard::find($id);

        if(!$card)
            return ResponseHelper::response_error("not.found", 404);

        $card->delete();

        return ResponseHelper::response_success("successful");

    }

}