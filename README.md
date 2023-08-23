# Banking System API

This repository contains a Laravel-based API for a banking system that supports deposit and withdrawal operations for Individual and Business users.

## Table of Contents

-   [Introduction](#introduction)
-   [Prerequisites](#prerequisites)
-   [Setup](#setup)
-   [API Endpoints](#api-endpoints)
    -   [Create User](#create-user)
    -   [Login](#login)
    -   [View All Transactions and Balances](#view-all-transactions-and-balances)
    -   [View Deposits](#view-deposits)
    -   [Deposit](#deposit)
    -   [View Withdrawals](#view-withdrawals)
    -   [Withdrawal](#withdrawal)
-   [Usage](#usage)

## Introduction

This API simulates a banking system with support for various types of transactions. It includes methods for user creation, login, deposit, and withdrawal. The system also applies fees and conditions as specified in the project requirements.

## Prerequisites

-   XAMPP or a similar local server environment.
-   Composer installed on your system.
-   Postman or a similar API testing tool.

## Setup

1.  Clone this repository to your local environment:

        ```shell
        git clone https://github.com/asif-cse-kuet/Mediusware_Laravel_Project.git
        ```

2.  Navigate to the project directory:

    ```shell
    cd Mediusware_Laravel_Project
    ```

3.  Install the required dependencies using Composer:

    ```shell
    composer install
    ```

4.  Create a MySQL database for the project and update the `.env` file with your database credentials:

    ```shell
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database_name
    DB_USERNAME=your_database_username
    DB_PASSWORD=your_database_password
    ```

5.  Generate the application key:

    ```shell
    php artisan key:generate
    ```

6.  Run database migrations to create the required tables:

    ```shell
    php artisan migrate
    ```

7.  Start the development server:

    ```shell
    php artisan serve
    ```

## API Endpoints

### Create User

-   **Method**: POST
-   **Endpoint**: `/api/users`
-   **Request Body**:
-   `name` (required): Name of the user.
-   `account_type` (optional): Account type (Individual or Business). By default it will be Individual.
-   `email` (required, unique): User's email.
-   `password` (Optional): User's password. By default it will be a default password.
-   `balance` (Optional): Initial balance. By default it will be 0.
-   **Response**: JSON containing user details and a success message.

### Login

-   **Method**: POST
-   **Endpoint**: `/api/login`
-   **Request Body**:
-   `email` (required): User's email.
-   `password` (required): User's password.
-   **Response**: JSON containing authentication token.

### View All Transactions and Balances

-   **Method**: GET
-   **Endpoint**: `/api/`
-   **Response**: JSON containing all transactions and current user balances.

### View Deposits

-   **Method**: GET
-   **Endpoint**: `/api/deposit`
-   **Response**: JSON containing all deposit transactions.

### Deposit

-   **Method**: POST
-   **Endpoint**: `/api/deposit`
-   **Request Body**:
-   `user_id` (required): User's ID.
-   `amount` (required): Amount to deposit.
-   **Response**: JSON containing success message.

### View Withdrawals

-   **Method**: GET
-   **Endpoint**: `/api/withdrawal`
-   **Response**: JSON containing all withdrawal transactions.

### Withdrawal

-   **Method**: POST
-   **Endpoint**: `/api/withdrawal`
-   **Request Body**:
-   `user_id` (required): User's ID.
-   `amount` (required): Amount to withdraw.
-   **Response**: JSON containing withdrawal details and updated balance.

## Usage

1. After setting up the project, use Postman or a similar tool to send API requests.
2. Create a user using the `/api/users` endpoint.
3. Login using the `/api/login` endpoint to obtain an authentication token.
4. Use the provided endpoints to perform various transactions and view account details.

---

Please make sure to replace placeholders such as `your_database_name`, `your_database_username`, and `your_database_password` with your actual database information.

This `README.md` file provides a step-by-step guide to set up and use the project, along with information about the available API endpoints and their inputs and outputs. It's crucial to ensure that all the prerequisites are met and each step is followed accurately for the project to work as expected.

Feel free to update or modify this `README.md` file to suit your needs and project specifics.

---
