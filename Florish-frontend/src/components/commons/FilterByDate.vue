<template>
  <div>
    <v-label class="col-sm-3 col-form-label">FILTER BY DATE (FROM-TO)</v-label>
    <v-row class="col-sm-5">
      <v-col cols="6">
        <v-text-field v-model="fromDate" label="From" type="date" outlined :max="toDate"
          @change="updateDateRange"></v-text-field>
      </v-col>
      <v-col cols="6">
        <v-text-field v-model="toDate" label="To" type="date" outlined :min="fromDate"
          @change="updateDateRange"></v-text-field>
      </v-col>
    </v-row>
  </div>
</template>

<script>
export default {
  name: "FilterByDate",
  data() {
    return {
      fromDate: '',
      toDate: ''
    };
  },
  methods: {
    updateDateRange() {
      this.$emit('date-range-change', { fromDate: this.fromDate, toDate: this.toDate });
    }
  }
};
</script>
