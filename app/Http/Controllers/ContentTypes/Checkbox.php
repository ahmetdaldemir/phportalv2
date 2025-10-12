<?php

namespace App\Http\Controllers\ContentTypes;

class Checkbox extends BaseType
{
    /**
     * @return int
     */
    public function handle()
    {
        $field = $this->row->category."_".$this->row->field;

        return (int) ($this->request->input($field) == 'on');
    }
}
