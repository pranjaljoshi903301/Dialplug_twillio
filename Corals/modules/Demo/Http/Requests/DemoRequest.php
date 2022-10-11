<?php

namespace Corals\Modules\Demo\Http\Requests;

use Corals\Foundation\Http\Requests\BaseRequest;
use Corals\Modules\Demo\Models\Demo;

class DemoRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->setModel(Demo::class);

        return $this->isAuthorized();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->setModel(Demo::class);
        $rules = parent::rules();

        if ($this->isUpdate() || $this->isStore()) {
            $rules = array_merge($rules, []);
        }

        if ($this->isStore()) {
            $rules = array_merge($rules, []);
        }

        if ($this->isUpdate()) {
            $demo = $this->route('demo');
            $rules = array_merge($rules, []);
        }

        return $rules;
    }
}
