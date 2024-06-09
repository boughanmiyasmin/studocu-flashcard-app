<?php

namespace App\Models;

use App\Constants;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Flashcard extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function practices(): HasMany
    {
        return $this->hasMany(Practice::class);
    }

    public function getPractice(): Practice|null
    {
        return $this->getRelation('practices')->first();
    }

    public function determinePracticeStatus(): self
    {
        $practice = $this->getPractice();
        $this->status = $practice instanceof Practice ? $practice->status : Constants::NOT_ANSWERED;

        return $this;
    }

    public function getPracticeProperties(): array
    {
        return [
            'ID' => $this->id,
            'Question' => $this->question,
            'Status' => $this->status,
        ];
    }
}
