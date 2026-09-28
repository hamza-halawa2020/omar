<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserWhatsAppMessageTemplate extends Model
{
    protected $connection = 'central';

    protected $table = 'user_whatsapp_message_templates';

    protected $fillable = [
        'user_id',
        'key',
        'template',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
