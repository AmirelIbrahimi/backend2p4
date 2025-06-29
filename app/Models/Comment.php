<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'game_id',
        'content',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
