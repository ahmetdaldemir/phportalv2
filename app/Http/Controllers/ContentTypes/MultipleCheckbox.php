<?php

namespace App\Http\Controllers\ContentTypes;

class MultipleCheckbox extends BaseType
{
    /**
     * @return mixed
     */
    public function handle()
    {        $field = $this->row->category."_".$this->row->field;

        $content = $this->request->input($field, []);
        if (true === empty($content)) {
            return json_encode([]);
        }

        return json_encode($content);
    }
}
