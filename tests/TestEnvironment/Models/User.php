<?php

namespace Korridor\LaravelHasManySync\Tests\TestEnvironment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
    ];

    /**
     * @return HasMany|Task
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
