# be-review-film

A web application for managing reviews and information about films, including functionalities for user authentication, film details, and reviews.

## Features and Functionality

- **User Authentication**: Registration, login, logout, and email verification using OTP.
- **Film Management**: Create, read, update, and delete film entries, including titles, summaries, and posters.
- **Genre Management**: Manage film genres.
- **Cast Management**: Manage film cast, including names and bios.
- **Reviews**: Users can submit reviews and ratings for films.
- **Role Management**: Admins can manage user roles and permissions.

## Technology Stack

- **Backend**: Laravel (PHP Framework)
- **Database**: MySQL (or SQLite)
- **API**: RESTful API for frontend interaction
- **Authentication**: JWT (JSON Web Tokens)
- **Cloud Storage**: Cloudinary for image uploads
- **Email**: Sending OTP and notifications via mail

## Prerequisites

- PHP >= 8.0
- Composer
- Node.js (for front-end asset management)
- Database server (MySQL or SQLite)

## Installation Instructions

1. **Clone the repository**:
   ```bash
   git clone https://github.com/bimapopo345/be-review-film.git
   cd be-review-film
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Install Node.js dependencies** (if applicable):
   ```bash
   npm install
   ```

4. **Set up the environment file**:
   Copy `.env.example` to `.env` and configure your database and mail settings.
   ```bash
   cp .env.example .env
   ```

5. **Generate application key**:
   ```bash
   php artisan key:generate
   ```

6. **Run migrations**:
   ```bash
   php artisan migrate
   ```

7. **Seed the database** (optional):
   ```bash
   php artisan db:seed
   ```

8. **Serve the application**:
   ```bash
   php artisan serve
   ```

## Usage Guide

- **Authentication**:
  - Register a new user: `POST /api/v1/auth/register`
  - Login: `POST /api/v1/auth/login`
  - Logout: `POST /api/v1/auth/logout`
  - Generate OTP for email verification: `POST /api/v1/auth/generate-otp-code`
  - Verify email with OTP: `POST /api/v1/auth/verification-email`

- **Film Management**:
  - Get all films: `GET /api/v1/movie`
  - Get a specific film: `GET /api/v1/movie/{id}`
  - Create a new film: `POST /api/v1/movie`
  - Update a film: `PUT /api/v1/movie/{id}`
  - Delete a film: `DELETE /api/v1/movie/{id}`

- **Genre Management**:
  - Get all genres: `GET /api/v1/genre`
  - Create a new genre: `POST /api/v1/genre`
  - Update a genre: `PUT /api/v1/genre/{id}`
  - Delete a genre: `DELETE /api/v1/genre/{id}`

- **Cast Management**:
  - Get all casts: `GET /api/v1/cast`
  - Create a new cast: `POST /api/v1/cast`
  - Update a cast: `PUT /api/v1/cast/{id}`
  - Delete a cast: `DELETE /api/v1/cast/{id}`

- **Review Management**:
  - Submit a review: `POST /api/v1/review`

## API Documentation

API endpoints are organized under the `/api/v1` prefix. Make sure to include authentication tokens where required.

### Example Request for User Registration

```json
POST /api/v1/auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

### Example Response

```json
{
  "message": "User berhasil di-register",
  "user": {
    "id": "uuid",
    "name": "John Doe",
    "email": "john@example.com",
    "email_verified_at": null,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

## Contributing Guidelines

1. Fork the repository.
2. Create a new branch for your feature or bugfix:
   ```bash
   git checkout -b feature/my-feature
   ```
3. Make your changes and commit them:
   ```bash
   git commit -m "Add some feature"
   ```
4. Push to your branch:
   ```bash
   git push origin feature/my-feature
   ```
5. Create a pull request.

## License Information

No specific license information is provided. Please check the repository for any updates regarding licensing.

## Contact/Support Information

For support or inquiries, please open an issue in the GitHub repository:
[be-review-film Issues](https://github.com/bimapopo345/be-review-film/issues)
