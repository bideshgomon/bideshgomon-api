<script setup>
/**
 * Table Component
 * Material 3 Design System with Responsive Support
 *
 * Usage:
 * <Table :columns="columns" :data="users" striped hoverable>
 *   <template #cell-name="{ row }">{{ row.name }}</template>
 * </Table>
 */

import { computed } from 'vue'

const props = defineProps({
  columns: {
    type: Array,
    required: true,
    // Format: [{ key: 'name', label: 'Name', sortable: true, align: 'left' }]
  },
  data: {
    type: Array,
    default: () => [],
  },
  striped: {
    type: Boolean,
    default: false,
  },
  hoverable: {
    type: Boolean,
    default: true,
  },
  bordered: {
    type: Boolean,
    default: false,
  },
  loading: {
    type: Boolean,
    default: false,
  },
  emptyText: {
    type: String,
    default: 'No data available',
  },
})

const emit = defineEmits(['sort', 'row-click'])

const handleSort = (column) => {
  if (column.sortable) {
    emit('sort', column.key)
  }
}

const handleRowClick = (row) => {
  emit('row-click', row)
}

const getCellAlignment = (align) => ({
  left: 'text-left',
  center: 'text-center',
  right: 'text-right',
}[align || 'left'])
</script>

<template>
  <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
      <!-- Header -->
      <thead class="bg-gray-50 dark:bg-gray-900">
        <tr>
          <th
            v-for="column in columns"
            :key="column.key"
            :class="[
              'px-6 py-3',
              'text-xs font-medium uppercase tracking-wider',
              'text-gray-500 dark:text-gray-400',
              getCellAlignment(column.align),
              column.sortable && 'cursor-pointer select-none hover:text-gray-700 dark:hover:text-gray-200',
            ]"
            @click="handleSort(column)"
          >
            <div class="flex items-center gap-2" :class="getCellAlignment(column.align)">
              <span>{{ column.label }}</span>
              <!-- Sort indicator placeholder -->
              <span v-if="column.sortable" class="text-gray-400">↕</span>
            </div>
          </th>
        </tr>
      </thead>

      <!-- Body -->
      <tbody
        :class="[
          'bg-white dark:bg-gray-800',
          'divide-y divide-gray-200 dark:divide-gray-700',
        ]"
      >
        <!-- Loading State -->
        <tr v-if="loading">
          <td
            :colspan="columns.length"
            class="px-6 py-12 text-center text-gray-500 dark:text-gray-400"
          >
            <div class="flex items-center justify-center gap-2">
              <svg
                class="animate-spin w-5 h-5"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle
                  class="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  stroke-width="4"
                />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
              </svg>
              <span>Loading...</span>
            </div>
          </td>
        </tr>

        <!-- Empty State -->
        <tr v-else-if="data.length === 0">
          <td
            :colspan="columns.length"
            class="px-6 py-12 text-center text-gray-500 dark:text-gray-400"
          >
            {{ emptyText }}
          </td>
        </tr>

        <!-- Data Rows -->
        <tr
          v-for="(row, rowIndex) in data"
          v-else
          :key="rowIndex"
          :class="[
            striped && rowIndex % 2 === 1 && 'bg-gray-50 dark:bg-gray-900/50',
            hoverable && 'hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors',
            'cursor-pointer',
          ]"
          @click="handleRowClick(row)"
        >
          <td
            v-for="column in columns"
            :key="column.key"
            :class="[
              'px-6 py-4',
              'text-sm text-gray-900 dark:text-white',
              getCellAlignment(column.align),
              column.nowrap && 'whitespace-nowrap',
            ]"
          >
            <!-- Custom cell slot -->
            <slot :name="`cell-${column.key}`" :row="row" :value="row[column.key]">
              {{ row[column.key] }}
            </slot>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
