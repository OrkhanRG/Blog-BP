<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleComment extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function scopeApproveStatus($query)
    {
        return $query->where('approve_status', 0);
    }

    public function scopeStatus($query, $status)
    {
        if (!is_null($status))
        {
            return $query->where('status', $status);
        }
    }

    public function scopeUser($query, $userID)
    {
        if (!is_null($userID)) {
            return $query->where('user_id', $userID);
        }
    }

    public function scopeCreatedDate($query, $created_date)
    {
        if (!is_null($created_date)) {
            return $query->where('created_at', '>=', $created_date)
                ->where('created_at', '<=', now());
        }
    }

    public function scopeSearchText($query, $serach_text)
    {
        if (!is_null($serach_text)) {
            return $query->where('name', '%' . $serach_text . '%')
                ->orWhere('name', 'LIKE', '%' . $serach_text . '%')
                ->orWhere('comment', 'LIKE', '%' . $serach_text . '%')
                ->orWhere('email', 'LIKE', '%' . $serach_text . '%');
        }
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function article(): HasOne
    {
        return $this->hasOne(Article::class, 'id', 'article_id');
    }

    public function parent(): HasOne
    {
        return $this->hasOne(ArticleComment::class, 'id', 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ArticleComment::class, 'parent_id', 'id');
    }

    public function commentLikes(): HasMany
    {
        return $this->hasMany(UserLikeComment::class, 'comment_id', 'id');
    }
}
