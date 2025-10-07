<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

abstract class SearchableModel extends Model
{
    use Searchable;
}
