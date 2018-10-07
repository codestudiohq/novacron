<template>
    <div>
        <heading class="mb-6">Novacron</heading>


    </div>
</template>

<script>
  export default {
    data() {
      return {
        tasks: Nova.config.tasks,
      }
    },
    mounted() {
      this.getTasks()
    },
    methods: {
      getTasks() {
        Nova.request()
          .get('/novacron/api/tasks')
          .then(response => {
            this.tasks = response.data
          });
      },
    }
  }
</script>

<style>
  /* Scoped Styles */
</style>
