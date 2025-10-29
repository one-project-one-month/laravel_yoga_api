# Yoga API Laravel Project - API Endpoints

This document lists **all API endpoints**, **assigned modules**, and **developer responsibilities** for the Yoga API project.

---

## 1️⃣ User & Role Management (Dev 1)

**Models:** `User`, `Role`  
**Responsibilities:** Authentication, user CRUD, role assignment

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST   | /api/register | Register new user |
| POST   | /api/login | User login |
| GET    | /api/users | List all users (admin only) |
| GET    | /api/users/{id} | Show user details |
| PUT    | /api/users/{id} | Update user profile |
| DELETE | /api/users/{id} | Delete user |
| GET    | /api/roles | List roles |

---

## 2️⃣ Lessons & Lesson Types (Dev 2)

**Models:** `Lesson`, `LessonType`, `LessonTrainer`  
**Responsibilities:** CRUD lessons and lesson types, assign lessons to trainers

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET    | /api/lessons | List all lessons |
| GET    | /api/lessons/{id} | Show lesson details |
| POST   | /api/lessons | Create new lesson |
| PUT    | /api/lessons/{id} | Update lesson |
| DELETE | /api/lessons/{id} | Delete lesson |
| GET    | /api/lesson-types | List lesson types |
| POST   | /api/lesson-types | Create lesson type |
| PUT    | /api/lesson-types/{id} | Update lesson type |
| DELETE | /api/lesson-types/{id} | Delete lesson type |
| POST   | /api/lesson-trainer | Assign lesson to trainer |

---

## 3️⃣ Subscriptions & Payments (Dev 3)

**Models:** `Subscription`, `SubscriptionUser`  
**Responsibilities:** Manage subscriptions, assign subscriptions to users, pricing

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET    | /api/subscriptions | List all subscriptions |
| POST   | /api/subscriptions | Create subscription |
| PUT    | /api/subscriptions/{id} | Update subscription |
| DELETE | /api/subscriptions/{id} | Delete subscription |
| POST   | /api/users/{id}/subscriptions | Assign subscription to user |
| GET    | /api/users/{id}/subscriptions | List user's subscriptions |

---

## 4️⃣ Appointments & Trainer Management (Dev 4)

**Models:** `Appointment`, `TrainerDetail`, `Testimonial`  
**Responsibilities:** Manage appointments, trainer profiles, testimonials

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET    | /api/appointments | List all appointments |
| POST   | /api/appointments | Create appointment |
| PUT    | /api/appointments/{id} | Update appointment (approve/complete) |
| DELETE | /api/appointments/{id} | Delete appointment |
| GET    | /api/trainers | List trainers |
| POST   | /api/trainers | Create trainer profile |
| PUT    | /api/trainers/{id} | Update trainer profile |
| GET    | /api/testimonials | List testimonials |
| POST   | /api/testimonials | Create testimonial |

---

## 5️⃣ Payment Method Management

**Models:** `Payment`  
**Responsibilities:** Manage payment information and User payment history

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET    | /api/payments | List all payment |
| POST   | /api/payments | Create payment information |
| PUT    | /api/payments/{id} | Update payment information |
| DELETE | /api/payments/{id} | Delete payment information |

---

## 6️⃣ Notes for Developers

- **Authentication**: Dev 1 implements JWT or Sanctum or Spatie for all protected routes.
- **Role-based access**: Use middleware to protect endpoints by role (`admin`, `trainer`, `student`).
- **Role-based access**: Use Spatie middleware: role:admin|trainer|student
- **Lesson completion**: Track via lesson_user.is_completed
- **Factories & Seeders**: Each developer uses factories for their models and seeds sample data.
- **Always test** in Postman before pushing to any shared branch.


---

## 7️⃣ Branching & Git Commit Guidelines

### Branch Naming
- `feature/users` → User & Role Management  
- `feature/lessons` → Lessons & Lesson Types  
- `feature/subscriptions` → Subscriptions & Payments  
- `feature/appointments` → Appointments & Trainers  

### Git Commit Message Rules
- Use clear, concise, and consistent messages.  
- **Format:** `<type>: <short description>`  
- **Type examples:**  
  - `feat` → new feature  
  - `fix` → bug fix  
  - `chore` → maintenance  
  - `docs` → documentation  
  - `refactor` → code improvement  
- **Examples:**  
  - `feat: add assign-role endpoint`  
  - `fix: correct lesson completion logic`

---

## 8️⃣ Summary Table

| Developer | Module | Models | Main API Endpoints |
|-----------|--------|--------|------------------|
| Dev 1 | User & Role Management | User, Role | `/api/users`, `/api/roles`, `/api/login`, `/api/register` |
| Dev 2 | Lessons & Lesson Types | Lesson, LessonType, LessonTrainer | `/api/lessons`, `/api/lesson-types`, `/api/lesson-trainer` |
| Dev 3 | Subscriptions & Payments | Subscription, SubscriptionUser | `/api/subscriptions`, `/api/users/{id}/subscriptions` |
| Dev 4 | Appointments & Trainer Management | Appointment, TrainerDetail, Testimonial | `/api/appointments`, `/api/trainers`, `/api/testimonials` |
