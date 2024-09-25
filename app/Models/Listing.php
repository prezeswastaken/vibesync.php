<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Auth;
use Str;

class Listing extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'is_sale_offer' => 'boolean',
        ];
    }

    public function title(): Attribute
    {
        return Attribute::get(fn ($title) => Str::apa($title));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(Link::class)->chaperone();
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function price(): HasOne
    {
        return $this->hasOne(Price::class);
    }

    public function usersWhoLiked(): MorphToMany
    {
        return $this->morphToMany(User::class, 'likeable');
    }

    public function usersWhoDisliked(): MorphToMany
    {
        return $this->morphToMany(User::class, 'dislikeable');
    }

    public function doesCurrentUserLike(): bool
    {
        return $this->usersWhoLiked->contains(Auth::user());
    }

    public function doesCurrentUserDislike(): bool
    {
        return $this->usersWhoDisliked->contains(Auth::user());
    }

    public function scopePublished($query): Builder
    {
        return $query->where('is_published', true);
    }
}
