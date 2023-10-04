<template>
  <v-table>
    <thead>
      <tr>
        <th class="text-center">#</th>
        <th v-for="column in columns" :key="column.key" class="text-center">
          {{ column.label }}
        </th>
        <th v-if="showEditIcon" class="text-center"></th>
        <th v-if="showDeleteIcon" class="text-center"></th>
        <th v-if="showFetchIcon" class="text-center"></th>
        <th v-if="showAddToCartIcon" class="text-center"></th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="(item, index) in items" :key="item.productCode">
        <td class="text-center">{{ index + 1 }}</td>
        <td class="text-center" v-for="column in columns" :key="column.key">
          <template v-if="column.key === 'quantity' && isStockEntryPage">
            <span @click="openEditQuantityDialog(index)">{{ item[column.key] }}</span>
            {{ console.log("Quantity in CustomTable component:", item[column.key]) }}
          </template>
          <template v-else>
            <span v-if="column.key === 'user_type'">
              {{ item[column.key].user_type }}
            </span>
            <span v-else-if="column.key === 'brand'">
              {{ item[column.key].brand_name }}
            </span>
            <span v-else-if="column.key === 'category'">
              {{ item[column.key].category_name }}
            </span>
            <span v-else-if="column.render" v-html="column.render(item)"></span>
            <span v-else>{{ item[column.key] }}</span>
          </template>
        </td>
        <td v-if="showEditIcon" class="edit-icon-cell text-center">
          <v-icon @click="editData(item)">mdi-pencil</v-icon>
        </td>
        <td v-if="showDeleteIcon" class="text-center">
          <v-icon @click="deleteData(item)">mdi-delete</v-icon>
        </td>
        <td v-if="showFetchIcon">
          <v-icon @click="fetchData(item)">mdi-arrow-right</v-icon>
        </td>
        <td v-if="showAddToCartIcon">
          <v-icon @click="addToCartProduct(item)">mdi-cart-plus</v-icon>
        </td>
      </tr>
    </tbody>
  </v-table>
</template>

<script>
export default {
  props: ['columns', 'items', 'showEditIcon', 'showDeleteIcon', 'showFetchIcon', 'showAddToCartIcon', 'isStockEntryPage', 'add-to-cart-method'],
  data() {
    return {
      search: '',
      editingIndex: -1,
      editedQuantity: 0,
    };
  },
  computed: {
    tableHeaders() {
      const headers = [
        {
          text: '#', value: 'index'
        },
        ...this.columns.map(column => ({ text: column.label, value: column.key })),
      ];
      if (this.showEditIcon) {
        headers.push({ text: 'Edit', value: 'editIcon' });
      }
      if (this.showDeleteIcon) {
        headers.push({ text: 'Delete', value: 'deleteIcon' });
      }
      if (this.showFetchIcon) {
        headers.push({ text: 'Fetch', value: 'FetchIcon' });
      }
      if (this.showAddToCartIcon) {
        headers.push({ text: 'addToCart', value: 'AddToCartIcon' });
      }
      return headers;
    },
  },
  methods: {
    editData(data) {
      this.$emit('edit-data', data);
    },
    deleteData(data) {
      this.$emit('delete-data', data);
    },
    fetchData(data) {
      this.$emit('fetch-data', data);
    },
    addToCartProduct(product) {
      this.$emit('add-to-cart-product', product);
    },
    openEditQuantityDialog(index) {
      this.$emit('edit-quantity', index);
    },
  },
};
</script>
