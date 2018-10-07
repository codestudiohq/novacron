<?php

namespace Studio\Novacron;

use function is_null;
use Laravel\Nova\Panel;
use Studio\Totem\Totem;
use Laravel\Nova\Resource;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Status;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use OwenMelbz\RadioField\RadioButton;
use Studio\Novacron\Actions\EnableTask;
use Studio\Novacron\Actions\DisableTask;
use Studio\Novacron\Actions\ExecuteTask;
use Studio\Novacron\Rules\CronExpression;
use Symfony\Component\Console\Command\Command;

class Task extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Studio\Totem\Task::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'description';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'description', 'command',
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
            Text::make('Description')
                ->help('Provide a descriptive name for your task')
                ->sortable()
                ->rules(['required']),

            Select::make('Command')
                ->help('Select an artisan command to schedule')
                ->hideFromIndex()
                ->options(Totem::getCommands()->map(function (Command $command) {
                    return $command->getName().' - '.$command->getDescription();
                })->toArray())
                ->displayUsingLabels()
                ->rules(['required']),

            Text::make('Parameters')
                ->help('Command parameters required to run the selected command')
                ->hideFromIndex(),

            RadioButton::make('Type', 'type')
                ->options([
                    'cron' => ['Cron Expression' => 'Use a cron expression'],
                    'frequency' => ['Frequencies' => 'Use predefined frequencies. You can add/edit frequencies from details view'],
                ])
                ->stack()
                ->hideFromIndex()
                ->toggle([
                    'frequency' => ['expression'],
                ])
                ->default(! is_null($this->expression) ? 'cron' : 'frequency'),

            Text::make('Cron Expression', 'expression')
                ->help('Provide a descriptive name for your task')
                ->hideFromIndex()
                ->rules(['nullable', 'required_if:type,cron', new CronExpression]),

            Select::make('Timezone', 'tz')
                ->help('Select a timezone for your task. App timezone is selected by default')
                ->rules(['required'])
                ->hideFromIndex()
                ->options(array_combine(timezone_identifiers_list(), timezone_identifiers_list()))
                ->withMeta(['value' => date_default_timezone_get()])
                ->displayUsingLabels(),

            new Panel('Information', $this->informationFields()),
            new Panel('Notification Settings', $this->notificationsFields()),
            new Panel('Server Settings', $this->configurationFields()),
            new Panel('Old Results Cleanup', $this->cleanupFields()),

            HasMany::make('Frequencies')
                ->canSee(function () {
                    return $this->type == 'frequency';
                }),
            HasMany::make('Results'),
        ];
    }

    public function informationFields()
    {
        return [
            Status::make('Status', function () {
                return Str::title($this->status);
            })
                ->loadingWhen(['Running'])
                ->failedWhen(['Failed']),

            Boolean::make('Is Active', 'is_active')
                ->hideWhenCreating()
                ->hideWhenUpdating(),

            Text::make('Average Run Time', function () {
                return number_format($this->averageRuntime / 1000, 2).' seconds';
            })
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->hideFromDetail()
                ->sortable(),

            Text::make('Last Run', function () {
                if ($last = $this->lastResult) {
                    return $last->ran_at->toDateTimeString();
                }

                return 'N/A';
            }),

            Text::make('Next Run', function () {
                return $this->upcoming;
            }),
        ];
    }

    public function notificationsFields()
    {
        return [
            Text::make('Email Notification (Optional)', 'notification_email_address')
                ->help('Add an email address to receive notifications when this task gets executed. Leave empty if you do not wish to receive email notifications')
                ->rules(['nullable', 'email'])
                ->hideFromIndex(),

            Text::make('SMS Notification (Optional)', 'notification_phone_number')
                ->help('Add a phone number to receive SMS notifications. Leave empty if you do not wish to receive sms notifications')
                ->rules(['nullable', 'digits_between:11,13'])
                ->hideFromIndex(),

            Text::make('Slack Notification (Optional)', 'notification_slack_webhook')
                ->help('Add a slack web hook url to recieve slack notifications. Phone numbers should include country code and are digits only. Leave empty if you do not wish to receive slack notifications')
                ->rules(['nullable', 'url'])
                ->hideFromIndex(),
        ];
    }

    public function configurationFields()
    {
        return [
            Boolean::make("Don't Overlap", 'dont_overlap')
                ->help('Decide whether multiple instances of same task should overlap each other or not.')
                ->hideFromIndex(),

            Boolean::make('Run in maintenance mode', 'run_in_maintenance')
                ->help('Decide whether the task should be executed while the app is in maintenance mode.')
                ->hideFromIndex(),

            Boolean::make('Run in single server', 'run_on_one_server')
                ->help('Decide whether the task should be executed on a single server.')
                ->hideFromIndex(),

        ];
    }

    public function cleanupFields()
    {
        return [
            RadioButton::make('Auto cleanup type', 'auto_cleanup_type')
                ->help('Determine if an over-abundance of results will be removed after a set limit or age.')
                ->options([
                    'days' => ['Days' => 'Truncate results after the number of days defined in auto cleanup threshold. 0 means disabled.'],
                    'results' => ['Results' => 'Truncate results after the number of results defined in auto cleanup threshold. 0 means disabled'],
                ])
                ->default('days')
                ->stack()
                ->marginBetween()
                ->hideFromIndex(),

            Number::make('Auto cleanup threshold', 'auto_cleanup_num')
                ->withMeta(['value' => $this->auto_cleanup_num.'' ?? '0'])
                ->help('Cleanup threshold. Set non-zero value to enable.')
                ->hideFromIndex(),
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
        return [
            new EnableTask,
            new DisableTask,
            new ExecuteTask,
        ];
    }
}
