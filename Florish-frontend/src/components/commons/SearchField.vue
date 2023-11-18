<template>
  <v-card color="grey-lighten-3" max-width="400">
    <v-text-field v-model="searchQuery" :loading="loading" density="compact" variant="solo" label="Search Product Here"
      append-inner-icon="mdi-magnify" single-line hide-details @input="onInput"></v-text-field>
  </v-card>
</template>

<script>
export default {
  data: () => ({
    loaded: false,
    loading: false,
    searchQuery: '',
  }),

  methods: {
    onInput() {
      this.$emit('search', this.searchQuery);
    },
  },
}
</script>