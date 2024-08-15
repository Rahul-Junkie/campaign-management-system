<template>
  <div
    class="flex-1 max-w-md bg-white dark:bg-gray-800 shadow-md rounded-lg p-6"
  >
    <h3 class="text-3xl font-bold mb-4 text-gray-900 dark:text-gray-100">
      Campaigns
    </h3>
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
      <thead class="bg-gray-50 dark:bg-gray-700">
        <tr>
          <th
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
          >
            Campaign Name
          </th>
          <th
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
          >
            Status
          </th>
          <th
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
          >
            View Users
          </th>
        </tr>
      </thead>
      <tbody
        class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600"
      >
        <tr v-for="campaign in campaigns" :key="campaign.id">
          <td
            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100"
          >
            {{ campaign.name }}
          </td>
          <td
            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100"
          >
            {{ campaign.status }}
          </td>
          <td
            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
          >
            <button
              @click="$emit('show_users',campaign.id);"
              class="text-indigo-600 dark:text-indigo-400 hover:underline"
            >
              <i class="fas fa-eye"></i>
            </button>
          </td>
        </tr>
        <!-- Message if no campaigns -->
        <tr v-if="campaigns.length === 0">
          <td
            colspan="3"
            class="px-6 py-4 text-center text-gray-500 dark:text-gray-400"
          >
            No campaigns found.
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  props: ["campaigns"],
};
</script>