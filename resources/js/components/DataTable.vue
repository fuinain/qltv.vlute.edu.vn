<template>
  <table class="table table-hover text-nowrap">
    <thead>
      <tr>
        <th v-for="column in columns" :key="column.key">
          {{ column.label }}
        </th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="(row, rowIndex) in data" :key="row.id || rowIndex">
        <td v-for="column in columns" :key="column.key">
          <slot :name="`column-${column.key}`" :row="row" :row-index="rowIndex">
            <template v-if="column.key === 'index'">
              {{ rowIndex + 1 }}
            </template>
            <template v-else>
              {{ row[column.key] }}
            </template>
          </slot>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
export default {
  name: "DataTable",
  props: {
    columns: {
      type: Array,
      required: true
    },
    data: {
      type: Array,
      required: true
    }
  }
};
</script>
