<?php

namespace DigitalCloud\PageBuilderField\Http\Controllers;

use DigitalCloud\PageBuilderField\Asset;
use DigitalCloud\PageBuilderField\PageBuilderField;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Http\Requests\NovaRequest;

class Controller extends BaseController
{
    public function index()
    {
        return response()->json(Asset::query()->latest()->get()->pluck('url'));
    }

    /**
     * Store an attachment for a Trix field.
     *
     * @param NovaRequest $request
     * @return Response
     */
    public function store(NovaRequest $request)
    {
        /** @var PageBuilderField $field */
        $field = $request->newResource()
            ->availableFields($request)
            ->findFieldByAttribute($request->field, function () {
                abort(404);
            });

        if (is_null($file = $request->file('file')))
            return response()->json([]);

        $storedFile = $file->store($field->storagePath, $field->disk);

        $asset = Asset::create([
            'file' => $storedFile,
            'disk' => $field->disk,
            'url' => Storage::disk($field->disk)->url($storedFile),
        ]);

        return response()->json([
            'data' => [
                $asset->url
            ]
        ]);
    }
}
