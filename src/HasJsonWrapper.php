<?php

namespace DigitalCreative\JsonWrapper;

use Illuminate\Support\Collection;
use Laravel\Nova\Http\Controllers\CreationFieldController;
use Laravel\Nova\Http\Controllers\ResourceShowController;
use Laravel\Nova\Http\Controllers\ResourceStoreController;
use Laravel\Nova\Http\Controllers\ResourceUpdateController;
use Laravel\Nova\Http\Controllers\UpdateFieldController;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;

trait HasJsonWrapper
{

    /**
     * Get the panels that are available for the given detail request.
     *
     * @param NovaRequest $request
     * @param Resource $resource
     *
     * @return Collection
     */
    public function availablePanelsForDetail(NovaRequest $request, Resource $resource)
    {
        $panels = parent::availablePanelsForDetail($request, $resource);
        $fields = parent::availableFields($request);

        return $panels;

    }

    public function availableFields(NovaRequest $request)
    {

        $controller = $request->route()->controller;

        if ($controller instanceof ResourceStoreController ||
            $controller instanceof ResourceUpdateController ||
            $controller instanceof CreationFieldController ||
            $controller instanceof UpdateFieldController) {

            return parent::availableFields($request);

        }

        if ($controller instanceof ResourceShowController) {

            return $this->flattenFields(parent::availableFields($request));

        }

        /**
         * For better compatibility just remove itself from everything else that this field is not really needed
         */
        return parent::availableFields($request)->filter(function ($value) {
            return !($value instanceof JsonWrapper);
        });

    }

    private function flattenFields(Collection $fields)
    {

        if ($fields->whereInstanceOf(JsonWrapper::class)->isEmpty()) {

            return $fields;

        }

        return $fields->flatMap(function ($field) {

            if ($field instanceof JsonWrapper) {

                foreach ($field->fields as $child) {

                    $child->panel = $field->panel;
                    $child->attribute = "$field->attribute->$child->attribute";

                }

                return $this->flattenFields($field->fields, $field);

            }

            return [ $field ];

        });

    }

}
