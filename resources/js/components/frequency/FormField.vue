<template>
  <span>
    <default-field :field="field">
      <template slot="field">
        <select
            :id="field.attribute"
            v-model="value"
            class="w-full form-control form-select"
            :class="errorClasses"
        >
          <option value="" selected disabled>
            {{__('Choose an option')}}
          </option>

          <option
              v-for="option in field.options"
              :value="option.value"
              :selected="option.value == value"
          >
            {{ option.label }}
          </option>
        </select>
        <p v-if="hasError" class="my-2 text-danger">
          {{ firstError }}
        </p>
      </template>
    </default-field>
      <span v-if="hasParameters">
        <div class="flex border-b border-40" v-for="(parameter, index) in this.selected.parameters">
          <div class="w-1/5 px-8 py-6">
            <label class="inline-block text-80 h-9 pt-2">
              {{parameter.label}}
            </label>
          </div>
          <div class="w-1/5 px-8 py-6">
            <input
                class="w-full form-control form-select"
                :value="parameter.value"
                v-bind:name="parameter.name"
                v-bind:type="parameter.type"
                v-bind:min="parameter.min"
                v-bind:max="parameter.max"
                required
            ></input>
          </div>
        </div>
      </span>
  </span>
</template>

<script>
  import { FormField, HandlesValidationErrors } from 'laravel-nova'
  export default {
    mixins: [HandlesValidationErrors, FormField],
    data: function() {
      return {
        selected: false,
        parameters: false
      }
    },
    computed: {
      hasParameters() {
        this.selected = this.getSelected();

        if (!this.selected) {
          return false;
        }

        this.parameters = this.selected.parameters ? this.selected.parameters.map((parameter, index) => {
          let fieldParameter = this.field.parameters.filter((_fieldParameter) => {
            return _fieldParameter.name == parameter.name;
          });
          parameter.value = fieldParameter.length ? fieldParameter[0].value : null;
          return parameter;
        }) : false;


        return this.parameters;
      }
    },
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
        formData.append("label", this.selected.label);
        formData.append(`frequencies[0][interval]`, this.value);
        this.parameters.forEach((parameter, index) => {
          formData.append(`frequencies[0][parameters][${index}][name]`, parameter.name);
          formData.append(`frequencies[0][parameters][${index}][value]`, parameter.value);
        });
      },

      getSelected() {
        return this.field.frequencies.filter((frequency) => {
          return frequency.interval == this.value;
        })[0];
      }
    }
  }
</script>

<style scoped>

</style>