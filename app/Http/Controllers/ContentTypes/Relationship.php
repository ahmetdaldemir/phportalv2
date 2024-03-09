<?php

namespace App\Http\Controllers\ContentTypes;

class Relationship extends BaseType
{
    /**
     * @return array
     */
    public function handle()
    {
        $field = $this->row->category."_".$this->row->field;

        $content = $this->request->input($field);
        if (is_array($content)) {
            $content = array_filter($content, function ($value) {
                return $value !== null;
            });
        }

        return $content;
    }
}
