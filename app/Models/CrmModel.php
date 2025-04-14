<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CrmModel extends Model
{
    public function getTable(): ?string
    {
        if (!isset($this->table)) {
            $classBase = class_basename($this);
            $snake = Str::snake(Str::pluralStudly($classBase));
            return config('crm.table_prefix') . $snake;
        }

        return $this->table;
    }
}
