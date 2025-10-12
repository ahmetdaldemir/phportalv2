<?php

namespace App\Http\Controllers\ContentTypes;

class Text extends BaseType
{
    /**
     * @return null|string
     */
    public function handle()
    {
        $field = $this->row->category."_".$this->row->field;
        $value = $this->request->input($field);
        if (isset($this->options->null)) {
            return $value == $this->options->null ? null : $value;
        }

        return $value;
    }
}
