# Campaign Management System

## Overview

This application is designed to manage email campaigns by importing users from a CSV file, processing them in batches, and sending out emails using a customizable HTML template.

## Installation

### Prerequisites

- PHP 8.0+
- Composer
- Laravel ^10.10
- MySQL 

### Setup

1. **Clone the repository:**

   ```bash
   git clone https://github.com/rahul-junkie/campaign-management-system.git
   cd campaign-management-system
   composer install
   npm install
   cp .env.example .env
   php artisan migrate


**To run the test case:**
    ```bash
    ./vendor/bin/pest --filter CampaignCreateTest


## Requirements

1. **Campaign Creation**:
   - Create a campaign by uploading a CSV file containing user data.
   - Validate the CSV data and import users into the system.
   - Track the progress of the campaign.

2. **Email Template**:
   - Use a fixed HTML email template with placeholders for dynamic content.
   - Dynamically replace `{{username}}` with the actual user's name and email.

3. **Queue Processing**:
   - Utilize Laravel's queue system to process campaigns in chunks.
   - Ensure the system is scalable and can handle large datasets efficiently.

4. **Notifications**:
   - Notify the campaign owner by email once the campaign is successfully processed.

5. **Deployment & Documentation**:
   - Code is version-controlled using GitHub.
   - A video demonstrating the application’s functionality is available.

6. **Testing**:
   - Test cases are written using PEST to ensure the application works as expected.
