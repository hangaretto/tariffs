<?php

namespace Magnetar\Tariffs\Presenters;

use Validator;

trait ValidatePresenter {

    private $errors;

    public function validate($data, $rules = null)
    {
        if($rules == null)
            $v = Validator::make($data, $this->rules);
        else
            $v = Validator::make($data, $rules);

        if ($v->fails())
        {
            $this->errors = $v->errors();
            return false;
        }

        return true;
    }

    public function errors()
    {
        return $this->errors;
    }

}