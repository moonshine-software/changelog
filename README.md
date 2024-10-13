## MoonShine Changelog

### Requirements

- MoonShine v3.0+

### Support MoonShine versions

| MoonShine   | ChangeLog   |
|-------------|-------------|
| 2.0+        | 1.0+        |
| 3.0+        | 2.0+        |

### Installation

```shell
composer require moonshine/changelog
```

```shell
php artisan migrate
```

### Get started

Add trait HasChangeLog to model

```php
class Post extends Model
{
    use HasChangeLog;
}
```

Add component to Page

```php
protected function bottomLayer(): array
{
    return [
        ...parent::bottomLayer(),

        ChangeLog::make('Changelog', $this->getResource())
    ];
}
```

or in Resource

```php
class PostResource extends ModelResource
{
    // ...
    protected function onLoad(): void
    {
        $this->getFormPage()->pushToLayer(
            Layer::BOTTOM,
            ChangeLog::make('Changelog', $this)
        );
    }
    // ...
}
```

By default, 5 last changes are displayed.
To change this, use the limit() method

```php
ChangeLog::make('Changelog', $this)->limit(10)
```

By default, Resource of BelongsTo is `MoonShine\Laravel\Resources\MoonShineUserResource`
To change this, use the `userResource` parameter

```php
ChangeLog::make('Changelog', $this, userResource: \App\MoonShine\Resources\MoonShineUserResource::class)->limit(10)
```
