<?php
namespace App\Traits;

trait FillsComponentFromModel
{
    public function fillFromModel($model, $ignore = [])
    {
        foreach ($model->toArray() as $key => $val) {
            if (in_array($key, $ignore)) continue;
            if (property_exists($this, $key)) {
                $this->$key = $val;
            }
        }
    }
}
