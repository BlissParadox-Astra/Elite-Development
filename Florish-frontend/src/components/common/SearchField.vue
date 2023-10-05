<template>
  <v-card color="grey-lighten-3" max-width="400">
    <v-text-field v-model="searchQuery" :loading="loading" density="compact" variant="solo" label="Search Product Here"
      append-inner-icon="mdi-magnify" single-line hide-details @click:append-inner="onClick"
      @keyup.enter="onClick"></v-text-field>
  </v-card>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      searchQuery: '',
      loading: false,
      loaded: false,
    };
  },

  methods: {
    onClick() {
      this.loading = true;

      // Make API request to your backend with the search query as a parameter
      axios.get('/products', { params: { search: this.searchQuery } })
        .then(response => {
          const searchResults = response.data.products ? response.data.products : response.data.users;
          this.$emit('search', searchResults);
          this.loading = false;
          this.loaded = true;
        })
        .catch(error => {
          console.error(error);
          this.loading = false;
        });
    },
    // onClick() {
    //   this.loading = true;

    //   // Make API request to your backend with the search query as a parameter
    //   axios.get('/products', { params: { search: this.searchQuery } })
    //     .then(response => {
    //       this.searchResults = response.data.products;
    //       this.loading = false;
    //       this.loaded = true;
    //     })
    //     .catch(error => {
    //       console.error(error);
    //       this.loading = false;
    //     });
    // },
  },
};
</script>