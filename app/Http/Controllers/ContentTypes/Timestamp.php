<?php

namespace App\Http\Controllers\ContentTypes;

use Carbon\Carbon;

class Timestamp extends BaseType
{
    public function handle()
    {
        if (!in_array($this->request->method(), ['PUT', 'POST'])) {
            return;
        }
        $field = $this->row->category."_".$this->row->field;

        $content = $this->request->input($field);

        if (empty($content)) {
            return;
        }

        return Carbon::parse($content);
    }
}
