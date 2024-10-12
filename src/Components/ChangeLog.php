<?php

declare(strict_types=1);

namespace MoonShine\ChangeLog\Components;

use Closure;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Support\Enums\Color;
use MoonShine\UI\Components\ActionButton;
use MoonShine\ChangeLog\Models\MoonshineChangeLog;
use MoonShine\UI\Components\Badge;
use MoonShine\UI\Components\MoonShineComponent;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Preview;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Laravel\Resources\MoonShineUserResource;
use MoonShine\Core\Traits\HasResource;
use MoonShine\UI\Traits\WithLabel;
use MoonShine\Laravel\TypeCasts\ModelCaster;

/**
 * @method static static make(Closure|string $label, ModelResource $resource, ?string $userResource = null)
 */
final class ChangeLog extends MoonShineComponent
{
    use HasResource;
    use WithLabel;

    protected string $view = 'moonshine-changelog::components.change-log';

    protected $except = [
        'getItem',
        'getResource',
    ];

    protected int $limit = 5;

    public function __construct(
        Closure|string $label,
        ModelResource $resource,
        private ?string $userResource = null,
    ) {
        parent::__construct();

        $this->setResource($resource);
        $this->setLabel($label);

        $this->userResource ??= MoonShineUserResource::class;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function getItem(): Model
    {
        return $this->getResource()->getItemOrInstance();
    }

    protected function getTable(): TableBuilder
    {
        $logs = $this->getItem()->changeLogs()->take($this->getLimit())->get();

        return TableBuilder::make([
            ID::make(),
            BelongsTo::make(
                __('moonshine-changelog::ui.user'),
                'moonshineUser',
                resource: $this->userResource
            )->badge('purple'),
            Preview::make(
                __('moonshine-changelog::ui.changes'),
                'states_before_states_before',
                static function (MoonshineChangeLog $data) {
                    $before = collect($data->states_before)
                        ->map(fn (mixed $value) => is_string($value) ? $value : json_encode($value));

                    $after = collect($data->states_after)
                        ->map(fn (mixed $value) => is_string($value) ? $value : json_encode($value))
                        ->diffAssoc($before)
                        ->except([$data->getCreatedAtColumn(), $data->getUpdatedAtColumn()]);

                    return TableBuilder::make()
                        ->simple()
                        ->items(array_filter([$after->toArray()]))
                        ->fields([
                            Preview::make(
                                __('moonshine-changelog::ui.before'),
                                'states_before',
                                static function ($data) use ($before) {
                                    return collect($data)->map(function ($value, $key) use ($before) {
                                        $badge = Badge::make($key, Color::YELLOW);
                                        $value = str((string) $before->get($key, $value))
                                            ->stripTags();

                                        return $badge . $value->limit();
                                    })->implode('<br /><hr class="divider" />');
                                }
                            ),
                            Preview::make(
                                __('moonshine-changelog::ui.after'),
                                'states_after',
                                static function ($data) {
                                    return collect($data)->map(function ($value, $key) {
                                        $badge = Badge::make($key, Color::GREEN);
                                        return $badge . str((string) $value)->stripTags()->limit();
                                    })->implode('<br /><hr class="divider" />');
                                }
                            ),
                        ])->render();
                }
            ),
            Date::make(
                __('moonshine-changelog::ui.date'),
                'created_at'
            )->format('d.m.Y H:i'),
        ], $logs)
            ->cast(new ModelCaster(MoonshineChangeLog::class))
            ->buttons([
                ActionButton::make(
                    'Restore',
                    fn (MoonshineChangeLog $data) => route('moonshine.changelog', [
                        'resourceUri' => $this->getResource()->getUriKey(),
                        'changeLog' => $data->getKey(),
                        'resourceItem' => $this->getItem()->getKey(),
                    ])
                )
                    ->withConfirm(
                        __('moonshine::ui.confirm'),
                        __('moonshine::ui.confirm_message'),
                        __('moonshine::ui.confirm'),
                    ),
                /*
                ->inModal(
                __('moonshine::ui.confirm'),
                content: fn(ActionButton $actionButton) => form(
                    route('moonshine.changelog', [
                        'resourceUri' => $this->getResource()->uriKey(),
                        'changeLog' => $actionButton->getItem()->getKey(),
                        'resourceItem' => $this->getItem()->getKey(),
                    ]),
                )->submit('Restore')->render()
                )
                */

            ])
            ->withNotFound();
    }

    protected function viewData(): array
    {
        return [
            'label' => $this->getLabel(),
            'resource' => $this->getResource(),
            'table' => $this->getItem()?->exists
                ? $this->getTable()
                : '',
            'limit' => $this->getLimit(),
        ];
    }
}
