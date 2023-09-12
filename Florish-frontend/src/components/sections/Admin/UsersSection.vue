<template>
  <v-main>
    <v-container class="mt-5 section2">
      <v-row justify="end">
        <v-col cols="12" sm="3">
          <v-btn color="success" block @click="showUserForm">Add User</v-btn>
        </v-col>
      </v-row>


      <v-row justify="center">
        <v-col cols="12">
          <CustomTable :columns="tableColumns" :items="users" :showEditIcon="true" :showDeleteIcon="true"
            class="custom-table"  @edit-data="editUserRow" @delete-data="showDeleteConfirmation"/>
        </v-col>
      </v-row>
      <DeleteConfirmationDialog @confirm-delete="deleteUser" ref="deleteConfirmationDialog" />
      <v-row>
        <v-col cols="12">
          <v-row class="d-flex justify-center">
            <v-col cols="12" sm="10" xl="10" lg="10" md="10" class="form-container">
              <UserForm v-if="showForm" @add="addUser" @update="updateUser(editingUserIndex, $event)"
                @cancel="hideUserForm" :initialUser="editingUser" :userTypes="userTypes" />
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-container>
  </v-main>
</template>
 
<script>
import CustomTable from '../../common/CustomTable.vue';
import UserForm from '../../common/UserForm.vue';
import DeleteConfirmationDialog from '../../common/DeleteConfirmationDialog.vue';
import axios from 'axios';


export default {
  name: 'UsersDetails',
  components: {
    CustomTable,
    UserForm,
    DeleteConfirmationDialog,
  },


  data() {
    return {
      showForm: false,
      users: [],
      editingUser: null,
      editingUserIndex: -1,
      tableColumns: [
        { key: 'user_type', label: 'User Type', render: this.renderUserType },
        { key: 'username', label: 'User Name', render: this.renderUserName },
        { key: 'first_name', label: 'First Name' },
        { key: 'last_name', label: 'Last Name' },
        { key: 'gender', label: 'Gender' },
        { key: 'age', label: 'Age' },
        { key: 'address', label: 'Address' },
        { key: 'contact_number', label: 'Contact Number' },
      ],


      userTypes: [],
    };
  },
  mounted() {
    this.getUsers();
    this.fetchUserTypes();
  },
  methods: {
    getUsers() {
      axios.get('/users').then(res => {
        this.users = res.data.users
        // console.log(this.users)
      });
    },

    showDeleteConfirmation(item) {
      this.itemToDelete = item;
      // Access the dialog component and call its method to show the dialog
      this.$refs.deleteConfirmationDialog.showConfirmationDialog();
    },

    deleteUser() {
      if (this.itemToDelete) {
        // Send a DELETE request to your backend API to delete the item
        axios.delete(`/user/${this.itemToDelete.id}`)
          .then(() => {
            // After successful deletion, remove the item from the users array
            const index = this.users.findIndex(user => user.id === this.itemToDelete.id);
            if (index !== -1) {
              this.users.splice(index, 1);
            }

            // Close the confirmation dialog
            this.$refs.deleteConfirmationDialog.closeDialog();
          })
          .catch(error => {
            console.error('Error deleting item:', error);
            // Handle errors here, such as displaying an error message to the user
          });
      }
    },

    async fetchUserTypes() {
      try {
        const response = await axios.get('/user-types'); // Replace with your actual API endpoint
        this.userTypes = response.data; // Store user types in the variable
      } catch (error) {
        console.error(error);
      }
    },


    showUserForm() {
      this.showForm = true;
    },


    hideUserForm() {
      this.showForm = false;
      this.editingUser = null;
      this.editingUserIndex = -1;
    },


    addUser(userData) {
      if (!userData.userType || !userData.userName || !userData.firstName ||
        !userData.lastName || !userData.gender || !userData.age || !userData.address) {
        return;
      }
      this.users.push(userData);
      this.hideUserForm();
    },


    updateUser(index, updatedUser) {
      this.users[index] = updatedUser;
      this.editingUser = null;
      this.hideUserForm();
    },


    editUserRow(user) {
      // Find the corresponding user type for the edited user
      const userType = this.userTypes.find(userType => userType.user_type === user.user_type.user_type);
      const username = user.user_credential ? user.user_credential.username : ''; // Get the username


      this.editingUser = { ...user, user_type: userType ? userType.user_type : null, username: username };
      const index = this.users.findIndex(p => p.userCode === user.userCode);
      this.editingUserIndex = index;
      this.showForm = true;
    },


    deleteUserRow(user) {
      const index = this.users.findIndex(p => p.userCode === user.userCode);
      if (index !== -1) {
        this.users.splice(index, 1);
      }
    },


    // Custom render function for 'User Type' column
    renderUserType(user) {
      return user.user_type ? user.user_type.user_type : 'Unknown';
    },


    // Custom render function for 'User Name' column
    renderUserName(user) {
      return user.user_credential ? user.user_credential.username : 'Unknown';
    },


  },
};
</script>
 
<style scoped>
.form-container {
  position: absolute;
  top: 0;
  left: 1;
  right: 1;
  z-index: 999;
  max-height: 100%;
  /* Adjust the maximum height as needed */
  overflow-y: auto;
}

.custom-table {
  height: 445px;
}
</style>
 