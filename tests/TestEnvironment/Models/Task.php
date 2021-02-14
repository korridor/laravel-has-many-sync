<?php

namespace Korridor\LaravelHasManySync\Tests\TestEnvironment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'content',
        'user_id',
    ];

    /**
     * @return BelongsTo|User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
