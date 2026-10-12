# 🍽️ ZAD  
### Food Waste Reduction & Ordering Platform  
**Graduation Project – Initial Prototype (v0.1)**


**ZAD** is a full-stack mobile application developed as a **graduation project**, focused on **reducing food waste** by connecting users with restaurants offering surplus or unsold food items at discounted prices.


The project is designed using real-world production patterns, secure authentication, and a scalable architecture suitable for future expansion into a commercial platform.


---


## 🎯 Project Vision


Food waste is a global problem that impacts the economy, the environment, and society.  
**ZAD** aims to reduce food waste by:


- Allowing restaurants to sell surplus food instead of discarding it
- Giving users access to affordable meals
- Creating a sustainable and eco-friendly food ecosystem


---


## 🚀 Features (v0.1 – Initial Prototype)


### 🔐 Authentication
- User registration
- User login
- Secure JWT-based authentication
- Token refresh mechanism


### 🛍️ Ordering System
- Browse food items per restaurant
- Add items to cart
- Cart validation (single restaurant per order)
- Checkout process
- Order creation with database persistence


### 📦 Orders
- Orders linked to authenticated users
- Order items stored with quantities and prices
- Orders saved with full transactional integrity


---


## Local setup

1. Create an empty database by following [`backend/database/README.md`](backend/database/README.md).
2. Copy `backend/.env.example` to `backend/.env` and set local database credentials and a private JWT secret.
3. Configure the Flutter API endpoint at build time. Run Flutter commands from `zad/` and provide the API's full base URL, ending in `/api/v1/`:

   ```sh
   flutter run --dart-define=API_BASE_URL=http://localhost/grad_project/backend/public/api/v1/
   ```

   Use the address reachable from your emulator or device; `localhost` on a phone points to the phone itself. Production builds should use the deployed HTTPS API URL.

Never commit `.env` files, signing keys, uploaded user content, or real database exports. Only the schema-only SQL file is intended to be shared.

## 🛠️ Tech Stack


### 📱 Mobile Application
- **Flutter**
- **Riverpod** (State Management)
- **GoRouter** (Navigation)
- REST API consumption


### 🖥️ Backend API
- **PHP** (Custom MVC-style architecture)
- **JWT Authentication**
- **PDO**
- **MySQL / MariaDB**


---


## 📁 Project Structure


```text
zad/
│
├── backend/              # PHP REST API
│   ├── app/
│   │   ├── Controllers/
│   │   ├── Repositories/
│   │   ├── Middleware/
│   │   └── Helpers/
│   ├── config/
│   └── public/
│
├── mobile_app/           # Flutter application
│   ├── lib/
│   │   ├── features/
│   │   ├── shared/
│   │   └── core/
│   └── pubspec.yaml
│
└── README.md
