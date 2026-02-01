<?php

namespace MarceliTo\Aicms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Change extends Model
{
    protected $table = 'aicms_changes';

    protected $fillable = [
        'file_path',
        'content_before',
        'content_after',
        'summary',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }
}
