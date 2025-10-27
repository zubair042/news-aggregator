
## News Aggregator API

The News Aggregator API is a Laravel 12-based application designed to provide a comprehensive solution for managing user authentication, articles, user preferences, and data aggregation. This application adopts a clean architecture with separate service and repository layers for streamlined business logic and database interaction management.

---

## Features

- **User Authentication**:
  - Registration a new user.
  - Login and logout functionality.
  - Update password workflow
- **Article Management**:
  - Fetch articles with pagination.
  - Search articles by keyword, date, category, and source.
  - View Article detail.
- **User Preferences**: 
  - Manage preferences for news sources, categories, and authors.
  - Access a personalized news feed based on preferences.
- **Data Aggregation**:
  - Daily automated news article fetching using CRON jobs from various sources.
- **API Documentaion**:
  - Daily automated news article fetching using CRON jobs from various sources.

---
 
## Libraries and Tools Used

- **Laravel Sanctum** — For API authentication  
- **Scramble OpenAPI** — For generating OpenAPI documentation
  
---

## Project Environment Versions

The application is developed and tested with the following versions:

| Component | Version |
|------------|----------|
| PHP        | 8.2      |
| Composer   | 2.8.4    |
| Laravel    | 12       |

---

## Architecture

The application follows an **n-layer architecture**:

- **Service Layer** — Handles business logic  
- **Repository Layer** — Manages database interactions  

This separation ensures a **clean and maintainable codebase**.

---

## Installation

### **Pre-requisites**
Ensure the following are installed on your system:
- XAMPP / WAMP / Laragon  
- PHP 8.2 or higher  

### **Setup Instructions**

1. **Clone the repository**
   ```bash
   git clone git@github.com:zubair042/news-aggregator.git
   
2. **Navigate to the project directory**
   ```bash
   cd news-aggregator

3. **Copy the environment configuration file**
   ```bash
   cp .env.example .env

4. **Update the .env file**
   - Set up your database credentials
   - Add API keys for news sources as required

5. **Install dependencies**
   ```bash
   composer install

6. **Run database migrations**
   ```bash
   php artisan migrate

7. **Start the local development server**
   ```bash
   php artisan serve

---

## Accessing the Application

### **API Documentation**
Once the server is running, open your browser and navigate to:
    ```bash
http://localhost:8000/docs/api

---

## Feature Testing

To run the test cases, execute the following command in the terminal:
```bash
php artisan test


