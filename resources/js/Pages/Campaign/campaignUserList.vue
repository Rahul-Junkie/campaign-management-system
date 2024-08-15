<template>
  <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">
    Users of Selected Campaign
    <button
      @click="$emit('hide')"
      class="ml-3 text-red-600 dark:text-red-400 hover:underline"
    >
      <i class="fas fa-close"></i>
    </button>
  </h3>
  <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
    <thead class="bg-gray-50 dark:bg-gray-700">
      <tr>
        <th
          class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
        >
          Name
        </th>
        <th
          class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
        >
          Email
        </th>
        <th
          class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
        >
          Status
        </th>
      </tr>
    </thead>
    <tbody
      class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600"
    >
      <tr v-for="user in selectedCampaignUsers" :key="user.email">
        <td
          class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100"
        >
          {{ user.name }}
        </td>
        <td
          class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
        >
          {{ user.email }}
        </td>
        <td
          class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
        >
          {{ user.email_sent_status }}
        </td>
      </tr>
      <!-- Message if no users -->
      <tr v-if="selectedCampaignUsers.length === 0">
        <td
          colspan="2"
          class="px-6 py-4 text-center text-gray-500 dark:text-gray-400"
        >
          No users found for this campaign.
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
export default {
  props: ["selectedCampaignUsers"],
};
</script>