<template>
  <Head title="Campaigns" />
  <AuthenticatedLayout>
    <template #header>
      <h2
        class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
      >
        Campaigns
      </h2>
    </template>
    <div
      class="flex flex-wrap p-6 gap-4 bg-gray-100 dark:bg-gray-900 min-h-screen"
    >
      <!-- Form Section -->
      <div
        class="flex-1 max-w-lg bg-white dark:bg-gray-800 shadow-md rounded-lg p-6"
      >
        <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">
          Create New Campaign
        </h3>
        <!-- Success Message -->
        <div
          v-if="$page.props.flash.success"
          class="p-4 mb-4 text-sm text-green-700 bg-green-100 border border-green-300 rounded"
        >
          {{ $page.props.flash.success }}
        </div>

        <!-- Failed Message -->
        <div
          v-if="$page.props.flash.failed"
          class="p-4 mb-4 text-sm text-red-700 bg-red-100 border border-red-300 rounded"
        >
          {{ $page.props.flash.failed }}
        </div>

        <!-- Error Message -->
        <div
          v-if="$page.props.flash.error"
          class="p-4 mb-4 text-sm text-yellow-700 bg-yellow-100 border border-yellow-300 rounded"
        >
          {{ $page.props.flash.error }}
        </div>
        <form @submit.prevent="submitCampaign" class="space-y-4">
          <!-- Error Message -->

          <div>
            <label
              for="campaignName"
              class="block text-sm font-medium text-gray-700 dark:text-gray-300"
              >Campaign Name</label
            >
            <input
              id="campaignName"
              v-model="campaignName"
              type="text"
              placeholder="Campaign Name"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:placeholder-gray-400"
            />
          </div>
          <div>
            <label
              for="csvFile"
              class="block text-sm font-medium text-gray-700 dark:text-gray-300"
              >Upload CSV</label
            >
            <input
              id="csvFile"
              type="file"
              @change="handleFileUpload"
              required
              class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:border file:border-gray-300 file:rounded-md file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 dark:file:bg-gray-700 dark:file:text-gray-300 dark:file:border-gray-600 dark:file:hover:bg-gray-600"
            />
          </div>
          <div>
            <label
              for="csvFile"
              class="block text-sm font-medium text-gray-700 dark:text-gray-300"
              >Email Body</label
            >
            <textarea
              v-model="email_body"
              type="textarea"
              @change="handleFileUpload"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:placeholder-gray-400"
            />
          </div>
          <div>
            <button
              type="submit"
              class="w-full inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-600"
            >
              Create Campaign
            </button>
          </div>
        </form>
      </div>
      <!-- Campaigns List Section -->
      <campaign-list :campaigns="campaigns" @show_users="getUsers" />
      <!-- Users List Section -->
      <div
        v-if="selectedCampaignUsers.length > 0"
        class="flex-1 max-w-md bg-white dark:bg-gray-800 shadow-md rounded-lg p-6"
      >
        <campaign-user-list
          :selectedCampaignUsers="selectedCampaignUsers"
          @hide="selectedCampaignUsers = []"
        />
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import campaignList from "@/Pages/Campaign/campaignList.vue";
import campaignUserList from "@/Pages/Campaign/campaignUserList.vue";
import { Head } from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({
  campaigns: Array,
  errors: Object, // Capture errors from the server
});

const campaignName = ref("");
const email_body = ref(
  "<p>Dear {{username}},</p><br><p>Thank you for being part of our campaign.</p><br><p>Best regards,</p><br><p>The Campaign Team</p>"
);
const csvFile = ref(null);
const campaigns = ref(props.campaigns); // Initialize with campaigns from props
const selectedCampaignUsers = ref([]); // Array to store users of the selected campaign
const errorMessage = ref(props.errors || ""); // Initialize error message from props

const submitCampaign = () => {
  const form = useForm({
    name: campaignName.value,
    email_body: email_body.value,
    csv: csvFile.value,
  });

  form.post("/campaigns", {
    forceFormData: true,
    onSuccess: (response) => {
      // Update campaigns list with the response or fetch new data
      campaigns.value = response.props.campaigns;
      errorMessage.value = ""; // Clear error message on success
    },
    onError: (errors) => {
      // Check if errors have a specific key and set errorMessage
      if (errors && errors.csv) {
        errorMessage.value = errors.csv;
      } else {
        errorMessage.value = "An error occurred. Please try again.";
      }
    },
  });
};

const handleFileUpload = (event) => {
  csvFile.value = event.target.files[0];
};

// Method to fetch and display users for the selected campaign
const getUsers = async (campaignId) => {
  try {
    // Make an API request to get the users for the specified campaign
    const response = await axios.get(`/api/campaigns/${campaignId}/users`);

    // Update the selectedCampaignUsers with the fetched data
    selectedCampaignUsers.value = response.data;
  } catch (error) {
    console.error("Error fetching users:", error);
    // Optionally handle the error, e.g., display a message
  }
};
</script>
