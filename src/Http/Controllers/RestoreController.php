<?php

declare(strict_types=1);

namespace MoonShine\ChangeLog\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use MoonShine\ChangeLog\Models\MoonshineChangeLog;
use MoonShine\Http\Controllers\MoonShineController;
use MoonShine\MoonShineRequest;
use MoonShine\Resources\ModelResource;
use Symfony\Component\HttpFoundation\Response;

class RestoreController extends MoonShineController
{
    public function __invoke(
        string $resourceUri,
        MoonshineChangeLog $changeLog,
        MoonShineRequest $request
    ): Response {
        /* @var ModelResource $resource */
        $resource = $request->getResource();
        $item = $resource->getItem();

        if(!is_null($item)) {
            $item->fill($changeLog->states_before);
            $item->save();

            $this->toast('Restored');
        }

        return back();
    }
}
