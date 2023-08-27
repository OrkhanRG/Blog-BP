<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailTheme extends Model
{
    protected $guarded = [];

    public const THEME_TYPE = [
        'Şablon Tipini Seçin',
        'Özüm Email Şablon Hazırlamaq İstəyirəm',
        'Şifrə Sıfırlama Maili',
    ];

    public const PROCESS = [
        'Əməliyyat Seçin',
        'Email Doğrulama',
        'Şifrə Sıfırlama',
        'Şifrə Sıfırlama Əməliyyatı Bitdikdən Sonra Göndəriləcək Email',
    ];

    public function getThemeTypeAttribute($value):string
    {
        return self::THEME_TYPE[$value];
    }

    public function getProcessAttribute($value):string
    {
        return self::PROCESS[$value];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
