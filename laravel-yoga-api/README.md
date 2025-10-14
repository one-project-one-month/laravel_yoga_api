# Laravel Yoga API

## Overview
The Laravel Yoga API is a RESTful API designed to manage yoga lessons, lesson types, and trainers. It provides endpoints for creating, updating, retrieving, and deleting lessons and lesson types, as well as assigning lessons to trainers.

## Features
- **Lesson Management**: Create, read, update, and delete lessons.
- **Lesson Type Management**: Create, read, update, and delete lesson types.
- **Trainer Assignment**: Assign lessons to trainers.

## Installation
1. Clone the repository:
   ```
   git clone <repository-url>
   ```
2. Navigate to the project directory:
   ```
   cd laravel-yoga-api
   ```
3. Install dependencies:
   ```
   composer install
   ```
4. Copy the example environment file:
   ```
   cp .env.example .env
   ```
5. Generate the application key:
   ```
   php artisan key:generate
   ```
6. Run migrations to set up the database:
   ```
   php artisan migrate
   ```

## API Endpoints
### Lessons
- `GET /api/lessons`: List all lessons
- `GET /api/lessons/{id}`: Show lesson details
- `POST /api/lessons`: Create new lesson
- `PUT /api/lessons/{id}`: Update lesson
- `DELETE /api/lessons/{id}`: Delete lesson

### Lesson Types
- `GET /api/lesson-types`: List lesson types
- `POST /api/lesson-types`: Create lesson type
- `PUT /api/lesson-types/{id}`: Update lesson type
- `DELETE /api/lesson-types/{id}`: Delete lesson type

### Lesson Trainer
- `POST /api/lesson-trainer`: Assign lesson to trainer

## Testing
To run the feature tests, use the following command:
```
php artisan test
```

## License
This project is licensed under the MIT License. See the LICENSE file for more details.