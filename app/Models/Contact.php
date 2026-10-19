<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'first_name',
        'last_name',
        'gender',
        'email',
        'tel',
        'address',
        'building',
        'detail',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function category(): BelongsTo{
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany{
        return $this->belongsToMany(Tag::class);
    }

    public function getGenderLabelAttribute(): string
    {
        return match ((int)$this->gender) {
            1 => '男性',
            2 => '女性',
            3 => 'その他',
        };
    }

    public function scopeFilter($query, array $filters)
    {
        if (!empty($filters['keyword'])) {
            $keyword = $filters['keyword'];

            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', "%{$keyword}%")
                ->orWhere('last_name', 'like', "%{$keyword}%")
                ->orWhereRaw(
                    "CONCAT(first_name, last_name) LIKE ?",
                    ["%{$keyword}%"]
                )
                ->orWhereRaw(
                    "CONCAT(last_name, first_name) LIKE ?",
                    ["%{$keyword}%"]
                )
                ->orWhere('email', 'like', "%{$keyword}%");
            });
        }
        if (!empty($filters['gender']) && $filters['gender'] !== '0') {
            $query->where('gender', $filters['gender']);
        }
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        if (!empty($filters['date'])) {
            $query->whereDate('created_at', $filters['date']);
        }
        return $query;
    }
}
