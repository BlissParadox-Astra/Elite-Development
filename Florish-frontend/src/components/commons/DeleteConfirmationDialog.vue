<template>
  <v-dialog v-model="dialog" max-width="400">
    <v-card>
      <v-card-title class="headline text-center bg-teal pa-3">Confirm Deletion</v-card-title>
      <v-card-text>
        Are you sure you want to delete this record?
      </v-card-text>
      <v-card-actions class="d-flex justify-center">
        <v-btn color="#23b78d" @click="deleteItem">Yes</v-btn>
        <v-btn @click="closeDialog" color="#068863">No</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  data() {
    return {
      dialog: false,
    };
  },
  methods: {
    showConfirmationDialog() {
      this.dialog = true;
    },
    closeDialog() {
      this.dialog = false;
    },
    deleteItem() {
      this.$emit('confirm-delete');
      this.dialog = false;
    },
  },
};
</script>
