<template>
  <v-card color="grey-lighten-3" max-width="430">
    <v-text-field v-model="searchQuery" :loading="loading" density="compact" variant="solo" :label="searchLabel"
      append-inner-icon="mdi-magnify" single-line hide-details @input="onInput"></v-text-field>
  </v-card>
</template>

<script>
export default {
  props: {
    searchLabel: {
      type: String,
      default: 'Search Product Here',
    },
  },
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