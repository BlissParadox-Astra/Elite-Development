<template>
<v-menu>
        <template v-slot:activator="{ props }">
          <v-list-item lines="two" v-bind="props">
            <v-avatar>
              <v-img src="../../assets/assets/profileIcon.png" alt="John"></v-img>
            </v-avatar>
            <span>Jane Smith</span>
          </v-list-item>
        </template>
        <v-card class="mx-auto" width="300">
          <v-img class="align-end text-white" height="auto" src="../../assets/assets/profileIcon.png" cover>
          </v-img>
          <v-card-actions class="d-flex justify-space-around">
            <v-btn>Profile</v-btn>
            <v-btn>Logout</v-btn>
          </v-card-actions>
        </v-card>
      </v-menu>
</template>
<script>
export default {
name: 'UserProfile',

  data: () => ({
    drawer: null,
    user: {
      initials: 'JD',
    },
  }),
}

</script>
<style scooped>
.align-center {
  display: flex;
  align-items: center;
}
</style>