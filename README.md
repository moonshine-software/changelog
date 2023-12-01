## MoonShine Changelog

### Requirements

- MoonShine v2.0+

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
    protected function onBoot(): void
    {
        $this->getPages()
                ->formPage()
                ->pushToLayer(
                    Layer::BOTTOM,
                    ChangeLog::make('Changelog', $this)
                );
    }
    // ...
}
```

