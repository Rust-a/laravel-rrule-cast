<?php


use App\Service\RegularTicket\RecurrenceRule;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Recurr\Exception\InvalidRRule;

class RecurrenceRuleCast implements CastsAttributes
{
    /**
     * @throws InvalidRRule
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): RecurrenceRule
    {
        return new RecurrenceRule($value);
    }

    /**
     * @param RecurrenceRule $value
     *
     * @return mixed
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        return $value->toString();
    }
}
