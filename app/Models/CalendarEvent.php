<?php

namespace Models;

use App\Models\Client\ClientCompany;
use App\Models\Ticket\Category;
use App\Models\Ticket\Status;
use App\Models\Ticket\Template;
use App\Service\RegularTicket\RecurrenceRule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Recurr\Exception\InvalidWeekday;

class RegularTicket extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'repeat_rule',
    ];

    protected $casts = [
        'repeat_rule' => RecurrenceRule::class,
    ];

    /**
     * @throws InvalidWeekday
     */
    public function setNextStart(): void
    {
        $nextRecurrence = $this->repeat_rule->getNextRecurrence();

        $this->next_start = $nextRecurrence?->getStart();
        $this->save();
    }
}
