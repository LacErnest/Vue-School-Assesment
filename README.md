# Vue School Assessment API

This project is a Laravel-based API for the Vue School Assessment, featuring user management and data synchronization capabilities.

## Prerequisites

- PHP 8.1+
- Composer
- Docker and Docker Compose
- Git

## Setup and Installation

1. Clone the repository:
   ```
   git clone https://github.com/LacErnest/vue-school-assessment-api.git
   cd vue-school-assessment-api
   ```

2. Copy the `.env.example` file to `.env`:
   ```
   cp .env.example .env
   ```

3. Update the `.docker-compose.env` file with your database credentials.

4. Build and start the Docker containers:
   ```
   docker-compose up -d
   ```

5. Generate application key:
   ```
   docker-compose exec app php artisan key:generate
   ```

6. Run migrations and seed the database:
   ```
   docker-compose exec app php artisan db:refresh--and--seed [--count=20000] [--force]
   ```

## API Routes

The API routes are defined in `routes/api.php` and are prefixed with `/api`. Here are the available endpoints:

- GET `/api/users` - Get all users
- GET `/api/users/{id}` - Get a specific user
- POST `/api/users` - Create a new user
- PUT `/api/users/{id}` - Update a user
- DELETE `/api/users/{id}` - Delete a user

To access these routes, ensure your server is running and use the appropriate HTTP method and URL.

## Usage

To interact with the API, you can use tools like cURL, Postman, or any HTTP client library. Here's an example using cURL:

```bash
# Get all users
curl http://localhost:8000/api/users

# Create a new user
curl -X POST http://localhost:8000/api/users \
     -H "Content-Type: application/json" \
     -d '{"name":"John Doe","email":"john@example.com","password":"password123","timezone":"UTC"}'
```

## Development Commands

Here are some useful commands for development:

```bash
# Run database migrations
docker-compose exec app php artisan migrate

# Seed the database
docker-compose exec app php artisan db:seed

# Run the database refresh and seed command
docker-compose exec app php artisan db:refresh-and-seed --count=4000 --force

# Update user information
docker-compose exec app php artisan users:update 1000

# Sync users with external API (simulation)
docker-compose exec app php artisan users:sync
```

## Testing

To run the test suite:

```bash
docker-compose exec app php artisan test
```

## QA Scenarios

The following QA scenarios are designed to test the functionality and performance of the API:

### User Management

1. **User Creation**
   - Run `php artisan db:refresh-and-seed --count=100 --force`
   - Verify that 100 users are created in the database
   - Check that each user has a valid name, email, and timezone

2. **User Update**
   - Run `php artisan users:update 50`
   - Verify that 50 random users have their name or timezone updated
   - Check that the `needs_sync` flag is set to true for these updated users

3. **Get Single User**
   - Get the ID of an existing user
   - Send a GET request to `/api/users/{id}`
   - Verify that the response includes the correct user details

4. **Create New User**
   - Send a POST request to `/api/users` with valid user data
   - Verify that the user is created in the database
   - Check that the response includes the newly created user's details

5. **Update User**
   - Get the ID of an existing user
   - Send a PUT request to `/api/users/{id}` with updated user data
   - Verify that the user's details are updated in the database
   - Check that the `needs_sync` flag is set to true for this user

6. **Delete User**
   - Get the ID of an existing user
   - Send a DELETE request to `/api/users/{id}`
   - Verify that the user is removed from the database

### Data Synchronization

7. **User Sync**
   - Run `php artisan users:sync`
   - Verify that users with `needs_sync` set to true are processed
   - Check that the sync respects the batch size limit (1000 users per batch)
   - Verify that the sync respects the API rate limits (50 requests per hour for batch endpoints, 3,600 individual requests per hour)
   - Check that processed users have their `needs_sync` flag set to false

### Performance Testing

8. **Large Dataset Handling**
   - Run `php artisan db:refresh-and-seed --count=100000 --force`
   - Run `php artisan users:update 50000`
   - Run `php artisan users:sync`
   - Monitor system performance and ensure operations complete within acceptable time limits

### Error Handling

9. **Invalid Input Handling**
   - Send requests with invalid data to various endpoints
   - Verify that appropriate error messages are returned
   - Check that no invalid data is persisted to the database

10. **Rate Limiting**
    - Send a large number of requests in quick succession
    - Verify that rate limiting is enforced and appropriate error messages are returned

### Concurrency

13. **Simultaneous Operations**
    - Simulate multiple users performing operations concurrently
    - Verify that data integrity is maintained and no race conditions occur

To run these QA scenarios:

1. Ensure your development environment is set up correctly
2. Run the specific commands mentioned in each scenario
3. Use tools like Postman or cURL to send API requests
4. Check the database state after each operation to verify the results
5. Monitor logs and system performance during tests

## Troubleshooting

If you encounter any issues with database connections, ensure that the MySQL container is running and that the credentials in your `.env` and `.docker-compose.env` files are correct.
