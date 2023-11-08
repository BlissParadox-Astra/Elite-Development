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
          <v-data-table :headers="headers" :items="users" :loading="loading" :page="currentPage"
            :items-per-page="itemsPerPage" density="compact" item-value="id" class="elevation-1" hide-default-footer
            @update:options="getUsers" fixed-header height="400">
            <template v-slot:custom-sort="{ header }">
              <span v-if="header.key === 'actions'">Actions</span>
            </template>
            <template v-slot:item="{ item, index }">
              <tr>
                <td>{{ displayedIndex + index }}</td>
                <td>{{ item.user_type.user_type }}</td>
                <td>{{ item.user_credential.username }}</td>
                <td>{{ item.first_name }}</td>
                <td>{{ item.last_name }}</td>
                <td>{{ item.gender }}</td>
                <td>{{ item.address }}</td>
                <td>{{ item.contact_number }}</td>
                <td>
                  <span>
                    <v-icon @click="editUserRow(item)" color="primary">mdi-pencil</v-icon>
                  </span>
                  <span style="margin-left: 15px;">
                    <v-icon @click="showDeleteConfirmation(item)" color="error">mdi-delete</v-icon>
                  </span>
                </td>
              </tr>
            </template>
            <template v-slot:bottom>
              <div class="text-center pt-8 pagination">
                <button class="pagination-button" @click="previousPage" :disabled="currentPage === 1">Previous</button>

                <button v-for="pageNumber in totalPages" :key="pageNumber" @click="gotoPage(pageNumber)"
                  :class="{ active: pageNumber === currentPage }" class="pagination-button">
                  {{ pageNumber }}
                </button>

                <v-btn class="pagination-button" @click="nextPage" :disabled="currentPage === totalPages">Next</v-btn>
              </div>
            </template>
          </v-data-table>
        </v-col>
      </v-row>
      <DeleteConfirmationDialog @confirm-delete="deleteUser" ref="deleteConfirmationDialog" />
      <v-row>
        <v-col cols="12">
          <v-row class="d-flex justify-center">
            <v-col cols="12" sm="10" xl="10" lg="10" md="10" class="form-container">
              <UserForm v-if="showForm" @add-user="addUser" @update-user="updateUser" @cancel="hideUserForm"
                :initialUser="editingUser" :userTypes="userTypes" />
            </v-col>
          </v-row>
        </v-col>
      </v-row>
      <v-snackbar v-model="snackbar" right top :color="snackbarColor">
        {{ snackbarText }}
        <template v-slot:actions>
          <v-btn color="pink" variant="text" @click="snackbar = false">
            Close
          </v-btn>
        </template>
      </v-snackbar>
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
      currentPage: 1,
      id: 1,
      users: [],
      editingUser: null,
      editingUserIndex: -1,
      snackbar: false,
      snackbarColor: '',
      headers: [
        { title: '#', value: 'index' },
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

  computed: {
    displayedIndex() {
      return (this.currentPage - 1) * this.itemsPerPage + 1;
    },
    totalPages() {
      return Math.ceil(this.totalItems / this.itemsPerPage);
    },
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
            page: this.currentPage,
            itemsPerPage: this.itemsPerPage,
          }
        })
        .then((res) => {
          this.users = res.data.users
          this.totalItems = res.data.totalItems;
          this.loading = false;
        })
        .catch((error) => {
          console.error('Error fetching users:', error);
        });
    },

    async fetchUserTypes() {
      try {
        const response = await axios.get('/user-types');
        this.userTypes = response.data;
      } catch (error) {
        console.error(error);
      }
    },

    previousPage() {
      if (this.currentPage > 1) {
        this.currentPage--;
        this.getUsers();
      }
    },

    nextPage() {
      if (this.currentPage < this.totalPages) {
        this.currentPage++;
        this.getUsers();
      }
    },

    gotoPage(pageNumber) {
      this.currentPage = pageNumber;
      this.getUsers();
    },

    async addUser(userData) {
      try {
        const response = await axios.post('/user', userData);
        if (response.status === 200) {
          this.users.push(response.data);
          this.hideUserForm();
          this.snackbarColor = 'success';
          this.showSnackbar(response.data.message, 'success');
          this.getUsers();
        } else {
          this.snackbarColor = 'error';
          this.showSnackbar(response.data.message, 'error');
        }
      } catch (error) {
        console.error('Error adding user:', error);
        this.snackbarColor = 'error';
        this.showSnackbar(error.response.data.message, 'error');
      }
      // this.users.push(userData);
      // this.hideUserForm();
      // this.getUsers();
    },

    editUserRow(user) {
      console.log(user);
      const userType = this.userTypes.find(userType => userType.user_type === user.user_type.user_type);
      const username = user.user_credential ? user.user_credential.username : '';

      if (userType && username) {
        this.editingUser = {
          ...user, user_type: userType ? userType.user_type : null,
          username: username,
          id: user.id,
        };
        const index = this.users.findIndex(p => p.userCode === user.userCode);
        this.editingUserIndex = index;
        this.showForm = true;
      } else {
        this.editingUser = {
          ...user, user_type: '',
          username: '',
          id: user.id
        };
        const index = this.users.findIndex(p => p.userCode === user.userCode);
        this.editingUserIndex = index;
        this.showForm = true;
        console.error(`User Type "${user.UserType.user_type}" or username "${user.username.username}" not found.`);
      }
    },

    async updateUser(userData) {
      try {
        console.log(userData.id);
        const response = await axios.put(`/user/${userData.id}`, userData);
        if (response.status === 200) {
          this.users[this.index] = userData;
          this.hideUserForm();
          this.snackbarColor = 'success';
          this.showSnackbar(response.data.message, 'success');
          this.editingUser = null;
          this.getUsers();
        } else {
          this.snackbarColor = 'error';
          this.showSnackbar(response.data.message, 'error');
        }
      } catch (error) {
        console.error(error);
        if (error.response && error.response.status === 422) {
          const validationErrors = error.response.data.errors;
          const errorMessage = Object.values(validationErrors).flat()[0] || 'An error occurred';
          this.snackbarColor = 'error';
          this.showSnackbar(errorMessage, 'error');
        } else {
          this.snackbarText = error.response.data.error;
          this.snackbarColor = 'error';
          this.showSnackbar(this.snackbarText, 'error');
        }
      }
      // this.users[index] = updatedUser;
      // this.editingUser = null;
      // this.hideUserForm();
      // this.getUsers();
    },

    async deleteUser() {
      if (this.itemToDelete) {
        axios.delete(`/user/${this.itemToDelete.id}`)
          .then((response) => {
            const index = this.users.findIndex(user => user.id === this.itemToDelete.id);
            if (index !== -1) {
              this.users.splice(index, 1);
              this.showSnackbar(response.data.message, 'success');
            } else {
              this.showSnackbar('User not found in the list', 'error');
            }
          })
          .catch(error => {
            console.error('Error deleting item:', error);
            this.showSnackbar(error.response.data.message, 'error');
          })
          .finally(() => {
            this.$refs.deleteConfirmationDialog.closeDialog();
            this.getUsers();
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
      this.getUsers();
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

    showSnackbar(text, color, timeout = 3000) {
      this.snackbarText = text;
      this.snackbarColor = color;
      this.snackbar = true;

      setTimeout(() => {
        this.hideSnackbar();
      }, timeout);
    },

    hideSnackbar() {
      this.snackbar = false;
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

.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
}

.pagination-button {
  padding: 6px 12px;
  margin: 0 4px;
  background-color: #f0f0f0;
  border: 1px solid #ccc;
  border-radius: 4px;
  cursor: pointer;
}

.pagination-button.active {
  background-color: #007bff;
  color: #fff;
  border-color: #007bff;
}
</style>
 