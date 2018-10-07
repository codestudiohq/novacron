<?php

namespace Studio\Novacron;

use Studio\Totem\Totem;
use Laravel\Nova\Resource;
use Illuminate\Http\Request;
use Studio\Novacron\Fields\Hidden;
use Studio\Novacron\Fields\Frequency as Frequencies;

class Frequency extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Studio\Totem\Frequency::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'interval';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [

    ];

    /**
     * Hide resource from Nova's standard menu.
     *
     * @var bool
     */
    public static $displayInNavigation = false;

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            Hidden::make('Label'),
            Frequencies::make('Frequency', 'interval')
                ->withMeta(['parameters' => $this->parameters])
                ->options(Totem::frequencies()),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
