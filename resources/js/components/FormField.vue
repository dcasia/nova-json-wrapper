<template>
    <div>
        <component v-for="(childField, index) in field.fields" :key="index"
                :is="'form-' + childField.component"
                :resource-name="resourceName"
                :resource-id="resourceId"
                :field="childField"
                :errors="errors"
                :related-resource-name="relatedResourceName"
                :related-resource-id="relatedResourceId"
                :via-resource="viaResource"
                :via-resource-id="viaResourceId"
                :via-relationship="viaRelationship"
                :show-help-text="childField.helpText != null"
        />
    </div>
</template>

<script>

    import { FormField, HandlesValidationErrors } from 'laravel-nova'

    export default {
        mixins: [ FormField, HandlesValidationErrors ],

        props: [
            'field',
            'resourceId',
            'viaResource',
            'resourceName',
            'viaResourceId',
            'viaRelationship',
            'relatedResourceId',
            'relatedResourceName'
        ],

        methods: {
            /*
             * Set the initial, internal value for the field.
             */
            setInitialValue() {
                this.value = this.field.value || ''
            },

            /**
             * Fill the given FormData object with the field's internal value.
             */
            fill(formData) {

                for (const field of this.field.fields) {

                    field.fill(formData)

                }

            },

            /**
             * Update the field's internal value.
             */
            handleChange(value) {
                this.value = value
            }
        }
    }

</script>
