<template>
  <v-dialog v-model="dialog" max-width="400">
    <v-card>
      <v-card-title class="headline">Confirm Deletion</v-card-title>
      <v-card-text>
        Are you sure you want to delete this record?
      </v-card-text>
      <v-card-actions>
        <v-btn color="error" @click="deleteItem">Yes</v-btn>
        <v-btn @click="closeDialog">No</v-btn>
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
