<template>
  <div>
    <v-label class="col-sm-3 col-form-label">FILTER BY DATE (FROM-TO)</v-label>
    <v-row class="col-sm-5">
      <v-col cols="6">
        <v-text-field v-model="fromDate" label="From" type="date" outlined :max="toDate"></v-text-field>
      </v-col>
      <v-col cols="6">
        <v-text-field v-model="toDate" label="To" type="date" outlined :min="fromDate"></v-text-field>
      </v-col>
    </v-row>
  </div>
</template>

<script>
export default {
  data() {
    return {
      fromDate: '',
      toDate: ''
    };
  }
};
</script>
