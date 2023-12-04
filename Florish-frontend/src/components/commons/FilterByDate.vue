<template>
  <div>
    <v-row class="col-sm-6">
      <v-col cols="12" lg="6" md="5" sm="5">
        <v-select v-model="selectedFiltering" :items="choices" label="FILTER BY: " outlined
          @change="updateFilterType"></v-select>
      </v-col>
      <v-col cols="12" lg="6" md="6" sm="6" v-if="selectedFiltering === 'Customize'">
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
      <v-col cols="12" lg="6" md="6" sm="6"
        v-else-if="selectedFiltering === 'Day' || selectedFiltering === 'Week' || selectedFiltering === 'Month' || selectedFiltering === 'Year'">
        <v-row class="col-sm-5">
          <v-col cols="6">
            <v-text-field v-model="selectedDate" label="Select Date" type="date" outlined
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
    const asiaManilaTimezone = 'Asia/Manila';
    const currentDate = new Date();
    currentDate.setHours(0, 0, 0, 0);
    const currentDateInManila = currentDate.toLocaleDateString('en-CA', { timeZone: asiaManilaTimezone });
    return {
      selectedFiltering: '',
      selectedDate: currentDateInManila,
      fromDate: '',
      toDate: '',
      choices: ['Day', 'Week', 'Month', 'Year', 'Customize']
    };
  },
  watch: {
    selectedFiltering(newFilterType) {
      this.$emit('filter-type-change', newFilterType);
      if (newFilterType === 'Customize') {
        this.fromDate = '';
        this.toDate = '';
      }
    }
  },
  methods: {
    updateFilterType() {
      this.$emit('filter-type-change', this.selectedFiltering);
    },
    updateDateRange() {
      if (['Day', 'Week', 'Month', 'Year'].includes(this.selectedFiltering)) {
        this.$emit('date-range-change', { selectedDate: this.selectedDate });
      } else {
        this.$emit('date-range-change', { fromDate: this.fromDate, toDate: this.toDate });
      }
    }
  }
};
</script>
