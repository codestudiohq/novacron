<template>
  <field-wrapper>
    <input
        type="hidden"
        :id="field.attribute"
        v-model="value"
    ></input>
  </field-wrapper>
</template>

<script>
  import { FormField, HandlesValidationErrors } from 'laravel-nova'
  export default {
    mixins: [HandlesValidationErrors, FormField],

    methods: {
      /**
       * Provide a function that fills a passed FormData object with the
       * field's internal value attribute. Here we are forcing there to be a
       * value sent to the server instead of the default behavior of
       * `this.value || ''` to avoid loose-comparison issues if the keys
       * are truthy or falsey
       */
      fill(formData) {
        formData.append(this.field.attribute, this.value);
      },
    }
  }
</script>

<style scoped>

</style>