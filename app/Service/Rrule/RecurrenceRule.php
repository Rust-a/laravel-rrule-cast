<?php


use App\Casts\RecurrenceRuleCast;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Recurr\Exception\InvalidRRule;
use Recurr\Exception\InvalidWeekday;
use Recurr\Recurrence;
use Recurr\Rule;
use Recurr\Transformer\ArrayTransformer;
use Recurr\Transformer\ArrayTransformerConfig;
use Recurr\Transformer\Constraint\AfterConstraint;

class RecurrenceRule implements Castable
{
    private const string RECUR_START = 'DTSTART:';
    private const string RECUR_RULE = 'RRULE:';

    private Rule $rule;

    /**
     * @throws InvalidRRule
     */
    public function __construct(string $rruleString)
    {
        $rules = explode("\n", $rruleString);
        $dateStart = now();
        foreach ($rules as $str) {
            if (str_starts_with($str, self::RECUR_START)) {
                $startDateString = substr($str, \Str::length(self::RECUR_START));
                $dateStart = new Carbon($startDateString);
            } elseif (str_starts_with($str, self::RECUR_RULE)) {
                $rrule = $str;
            }
        }
        $this->rule = new Rule($rrule, $dateStart->toDate());
    }

    public function toString(): string
    {
        $startTime = $this->rule->getStartDate();

        return self::RECUR_START.$startTime->format('Ymd\THis+').$startTime->getOffset().'Z'
            ."\n"
            .self::RECUR_RULE.$this->rule->getString();
    }

    #[\Override]
    public static function castUsing(array $arguments): string
    {
        return RecurrenceRuleCast::class;
    }

    public function getRule(): Rule
    {
        return $this->rule;
    }

    /**
     * @throws InvalidWeekday
     */
    public function getNextRecurrence(): ?Recurrence
    {
        $config = new ArrayTransformerConfig();
        $config->setVirtualLimit(1);
        $config->enableLastDayOfMonthFix();
        $transformer = new ArrayTransformer($config);

        return $transformer->transform(
            $this->rule,
            new AfterConstraint(\App\Service\RegularTicket\now()->toDate(), false),
            false,
        )->first();
    }
}
