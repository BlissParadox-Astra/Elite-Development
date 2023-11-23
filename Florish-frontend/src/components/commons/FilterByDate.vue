<template>
  <div>
    <v-row class="col-sm-5">
      <v-col cols="12" lg="5" md="5" sm="5">
        <v-select v-model="selectedDay" :items="days" label="FILTER BY DATE (FROM-TO)" outlined></v-select>
      </v-col>
      <v-col cols="12" lg="6" md="6" sm="6" v-if="selectedDay === 'Customize'">
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

      </v-col>
    </v-row>
  </div>
</template>

<script>
export default {
  name: "FilterByDate",
  data() {
    return {
      selectedDay: '',
      fromDate: '',
      toDate: '',
      days: ['day', 'week', 'month','Customize']
    };
  },
  watch: {
    selectedDay(newDay) {
      if (newDay === 'Customize') {
        this.fromDate = '';
        this.toDate = '';
      }
    },
    updateDateRange() {
      this.$emit('date-range-change', { fromDate: this.fromDate, toDate: this.toDate });
    }
  }
};
</script>