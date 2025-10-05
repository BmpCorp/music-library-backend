<?php

namespace App\Attributes;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaLibraryCollectionAttribute extends Attribute
{
    public $withObjectCaching = false;

    public function __construct($model, $collection = 'image', $conversion = '', $single = false)
    {
        parent::__construct(
            get: function () use ($model, $single, $collection, $conversion) {
                if ($single) {
                    return $model->getFirstMediaUrl($collection, $conversion);
                }

                return $model->getMedia($collection)->map(fn (Media $media) => $media->getUrl($conversion));
            },
            set: function ($value) use ($model, $collection, $single, $conversion) {
                if ($single) {
                    if ($value instanceof UploadedFile) {
                        $model->addMediaFromDisk(
                            Storage::disk(config('media-library.disk_name'))->put('/tmp', $value),
                            config('media-library.disk_name')
                        )->toMediaCollection($collection);
                    } elseif (filter_var($value, FILTER_VALIDATE_URL)) {
                        $model->addMediaFromUrl($value)->toMediaCollection($collection);
                    } elseif (!$value) {
                        $model->getMedia($collection)->each(function (Media $media) {
                            $media->delete();
                        });
                    }
                } else {
                    $filesToClear = request()->get('clear_' . $collection, []);
                    $model->getMedia($collection, function (Media $media) use ($filesToClear, $conversion) {
                        return in_array($media->getUrl(), $filesToClear) || in_array($media->getUrl($conversion), $filesToClear);
                    })->each(function (Media $media) {
                        $media->delete();
                    });
                    if (is_array($value)) {
                        foreach ($value as $file) {
                            if (filter_var($file, FILTER_VALIDATE_URL)) {
                                $model->addMediaFromUrl($file)->toMediaCollection($collection);
                            } elseif ($file) {
                                $model->addMediaFromDisk(
                                    Storage::disk(config('media-library.disk_name'))->put('/tmp', $file),
                                    config('media-library.disk_name')
                                )->toMediaCollection($collection);
                            }
                        }
                    } elseif ($value === null) {
                        $model->getMedia($collection)->each(function (Media $media) {
                            $media->delete();
                        });
                    }
                }

                return [];
            },
        );
    }
}
