<?php

namespace Studio\Novacron\Fields;

use Laravel\Nova\Fields\Select;

class Frequency extends Select
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'frequency';

    public $frequencies;

    /**
     * Set the options for the select menu.
     *
     * @param  array  $frequencies
     * @return $this
     */
    public function options($frequencies)
    {
        $options = collect($frequencies)->mapWithKeys(function ($item) {
            return [$item['interval'] => $item['label']];
        })->toArray();

        parent::options($options);

        return $this->withMeta([
            'frequencies' => $frequencies,
        ]);
    }
}
