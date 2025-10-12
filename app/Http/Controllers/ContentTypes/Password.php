<?php

namespace App\Http\Controllers\ContentTypes;

class Password extends BaseType
{
    /**
     * Handle password fields.
     *
     * @return string
     */
    public function handle()
    {
        $field = $this->row->category."_".$this->row->field;

        return empty($this->request->input($field)) ? null :
            bcrypt($this->request->input($field));
    }
}
