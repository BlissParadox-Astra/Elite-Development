<template>
  <v-main>
    <v-container class="section2">
      <v-row justify="end">
        <v-col cols="12" sm="3">
          <v-btn color="success" block @click="showUserForm">Add User</v-btn>
        </v-col>
      </v-row>
      <v-row justify="center">
        <v-col cols="12">
          <v-data-table-server v-model:items-per-page="itemsPerPage" :page="page" :headers="headers"
            :items-length="totalItems" :items="users" :loading="loading" item-value="id" class="elevation-1"
            @update:options="getUsers">
            <template v-slot:custom-sort="{ header }">
              <span v-if="header.key === 'actions'">Actions</span>
            </template>
            <template v-slot:item="{ item }">
              <tr>
                <td>{{ item.id }}</td>
                <td>{{ item.user_type.user_type }}</td>
                <td>{{ item.user_credential.username }}</td>
                <td>{{ item.first_name }}</td>
                <td>{{ item.last_name }}</td>
                <td>{{ item.gender }}</td>
                <td>{{ item.address }}</td>
                <td>{{ item.contact_number }}</td>
                <td>
                  <span>
                    <v-icon @click="editBrandRow(item)" color="primary">mdi-pencil</v-icon>
                  </span>
                  <span style="margin-left: 15px;">
                    <v-icon @click="showDeleteConfirmation(item)" color="error">mdi-delete</v-icon>
                  </span>
                </td>
              </tr>
            </template>
          </v-data-table-server>
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
import UserForm from '../../forms/UserForm.vue';
import DeleteConfirmationDialog from '../../commons/DeleteConfirmationDialog.vue';
import axios from 'axios';


export default {
  name: 'UsersDetails',
  components: {
    UserForm,
    DeleteConfirmationDialog,
  },

  data() {
    return {
      loading: true,
      showForm: false,
      itemsPerPage: 10,
      totalItems: 0,
      page: 1,
      id: 1,
      users: [],
      editingUser: null,
      editingUserIndex: -1,
      headers: [
        { title: '#', key: 'id' },
        { title: 'User Type', key: 'user_type.user_type' },
        { title: 'User Name', key: 'user_credential.username' },
        { title: 'First Name', key: 'first_name' },
        { title: 'Last Name', key: 'last_name' },
        { title: 'Gender', key: 'gender' },
        { title: 'Address', key: 'address' },
        { title: 'Contact Number', key: 'contact_number' },
        { title: 'Actions', key: 'actions', sortable: false }
      ],
      userTypes: [],
    };
  },

  async mounted() {
    await this.getUsers();
    await this.fetchUserTypes();
  },

  methods: {
    getUsers() {
      this.loading = true;
      axios
        .get('/users', {
          params: {
            page: this.page,
            itemsPerPage: this.itemsPerPage,
          }
        })
        .then((res) => {
          this.users = res.data.users
          this.totalItems = res.data.totalItems;
          this.loading = false;
        })
        .catch((error) => {
          console.error('Error fetching categories:', error);
        });
  },

  addUser(userData) {
    if (!userData.userType || !userData.userName || !userData.firstName ||
      !userData.lastName || !userData.gender || !userData.age || !userData.address) {
      return;
    }
    this.users.push(userData);
    this.hideUserForm();
  },

  editUserRow(user) {
    const userType = this.userTypes.find(userType => userType.user_type === user.user_type.user_type);
    const username = user.user_credential ? user.user_credential.username : '';

    this.editingUser = { ...user, user_type: userType ? userType.user_type : null, username: username };
    const index = this.users.findIndex(p => p.userCode === user.userCode);
    this.editingUserIndex = index;
    this.showForm = true;
  },

  updateUser(index, updatedUser) {
    this.users[index] = updatedUser;
    this.editingUser = null;
    this.hideUserForm();
  },

  deleteUser() {
    if (this.itemToDelete) {
      axios.delete(`/user/${this.itemToDelete.id}`)
        .then(() => {
          const index = this.users.findIndex(user => user.id === this.itemToDelete.id);
          if (index !== -1) {
            this.users.splice(index, 1);
          }
          this.$refs.deleteConfirmationDialog.closeDialog();
        })
        .catch(error => {
          console.error('Error deleting item:', error);
        });
    }
  },

  showDeleteConfirmation(item) {
    this.itemToDelete = item;
    this.$refs.deleteConfirmationDialog.showConfirmationDialog();
  },

  hideUserForm() {
    this.showForm = false;
    this.editingUser = null;
    this.editingUserIndex = -1;
  },

  showUserForm() {
    this.showForm = true;
  },

  deleteUserRow(user) {
    const index = this.users.findIndex(p => p.userCode === user.userCode);
    if (index !== -1) {
      this.users.splice(index, 1);
    }
  },

  renderUserType(user) {
    return user.user_type ? user.user_type.user_type : 'Unknown';
  },

  renderUserName(user) {
    return user.user_credential ? user.user_credential.username : 'Unknown';
  },

  async fetchUserTypes() {
    try {
      const response = await axios.get('/user-types');
      this.userTypes = response.data;
    } catch (error) {
      console.error(error);
    }
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
  overflow-y: auto;
}

.custom-table {
  height: 445px;
}
</style>
 