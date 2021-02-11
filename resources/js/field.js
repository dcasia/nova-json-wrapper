import FormField from './components/FormField.vue'

Nova.booting((Vue, router, store) => {
    Vue.component('form-json-wrapper', FormField)
})
