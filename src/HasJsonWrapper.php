<?php

namespace DigitalCreative\JsonWrapper;

use Illuminate\Support\Collection;
use Laravel\Nova\Http\Controllers\ResourceShowController;
use Laravel\Nova\Http\Controllers\ResourceStoreController;
use Laravel\Nova\Http\Controllers\ResourceUpdateController;
use Laravel\Nova\Http\Requests\NovaRequest;

trait HasJsonWrapper
{

    /**
     * Get the panels that are available for the given detail request.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @return \Illuminate\Support\Collection
     */
    public function availablePanelsForDetail($request)
    {
        $panels = parent::availablePanelsForDetail($request);
        $fields = parent::availableFields($request);

        return $panels;

    }

    public function availableFields(NovaRequest $request)
    {

        $controller = $request->route()->controller;

        if ($controller instanceof ResourceStoreController ||
            $controller instanceof ResourceUpdateController) {

            return parent::availableFields($request);

        }

        if ($controller instanceof ResourceShowController) {

            return $this->flattenFields(parent::availableFields($request));

        }

        /**
         * For better compatibility just remove itself from everything else that this field is not really needed
         */
        return collect(parent::availableFields($request))->filter(function ($value) {
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
