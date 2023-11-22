<template>
  <v-container class="text-start">
    <v-menu v-model="menu" :close-on-content-click="false" location="end" @click:outside="closeMenu">
      <template v-slot:activator="{ props }">
        <v-btn color="indigo" v-bind="props">
          {{ buttonLabel }}
        </v-btn>
      </template>

      <v-container cols="12" md="6" lg="6" sm="4">
        <v-list>
          <v-list-item @click="selectItem('Day')">
            <v-list-item-title>Day</v-list-item-title>
          </v-list-item>
          <v-list-item @click="selectItem('Week')">
            <v-list-item-title>Week</v-list-item-title>
          </v-list-item>
          <v-list-item @click="selectItem('Month')">
            <v-list-item-title>Month</v-list-item-title>
          </v-list-item>
          <v-list-item @click="selectItem('Customize')">
            <v-list-item-title>Customize</v-list-item-title>
          </v-list-item>
        </v-list>

        <v-card v-if="selectedItem === 'Customize'" class="card" cols="12" sm="2" md="4" lg="6">
          <v-row>
            <v-col cols="12" sm="4">
              <v-text-field v-model="fromDate" label="From" type="date" outlined :max="toDate"
                @change="updateDateRange"></v-text-field>
            </v-col>
            <v-col cols="12" sm="4">
              <v-text-field v-model="toDate" label="To" type="date" outlined :min="fromDate"
                @change="updateDateRange"></v-text-field>
            </v-col>
          </v-row>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn variant="text" @click="cancelCustomization">
              Cancel
            </v-btn>
            <v-btn color="primary" variant="text" @click="saveCustomization">
              Load
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-container>
    </v-menu>
  </v-container>
</template>

<script>
export default {
  data: () => ({
    menu: false,
    selectedItem: null,
    buttonLabel: 'Filter by Date',
    fromDate: '',
    toDate: ''
  }),
  methods: {
    selectItem(item) {
      if (item !== 'Customize') {
        this.closeMenu();
      }

      this.selectedItem = item;

      if (item === 'Customize') {
        this.buttonLabel = 'Customize';
      } else {
        this.buttonLabel = item;
      }
    },
    saveCustomization() {
      // Perform any necessary actions before closing the menu
      this.closeMenu();
    },
    cancelCustomization() {
      this.closeMenu();
    },
    closeMenu() {
      this.menu = false;
      this.selectedItem = null;
      this.buttonLabel = 'Filter by Date';
    },
    updateDateRange() {
      this.$emit('date-range-change', { fromDate: this.fromDate, toDate: this.toDate });
    }
  }
};
</script>

<style scoped>
.card {
  background-color: #23b78d;
  padding: 10px;
}
</style>