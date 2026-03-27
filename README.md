# 🛒 Foie Gras E-commerce Platform (Backend & Admin)

A robust, API-driven e-commerce solution built with **Laravel 10**, featuring an advanced administration panel, complex product logic, and seamless payment integrations.

## Technical Stack
* **Backend:** Laravel 10 (PHP 8.2+)
* **Admin Panel:** Filament V3 (TALL Stack)
* **API:** RESTful API for React Frontend
* **Database:** MySQL with complex variant relationships
* **Payments:** Paymob Integration & Cash on Delivery (COD)
* **Authentication:** Laravel Sanctum & Role-Based Access Control (RBAC)

## Key Features
- **Smart API Endpoints:** Unified data fetching for User Profile, Wishlist, and Cart to reduce network requests.
- **Dynamic Product Variants:** Support for products with multiple weights and corresponding price changes.
- **Advanced Dashboard:** Real-time analytics, order tracking, and landing page management via Filament.
- **Multi-Role Authorization:** Dedicated permissions for Admins and Editors using Laravel Policies.
- **Data Reporting:** Exportable sales and inventory reports in Excel format with background processing notifications.

## 🛠 Installation & Setup
1.  **Clone the project:**
    ```bash
    git clone [https://github.com/EslamMeky/your-repo-name.git](https://github.com/EslamMeky/your-repo-name.git)
    ```
2.  **Install Dependencies:**
    ```bash
    composer install
    npm install && npm run build
    ```
3.  **Environment Setup:**
    - Copy `.env.example` to `.env`.
    - Configure Database and Paymob API keys.
4.  **Database Migration:**
    ```bash
    php artisan migrate --seed
    ```
5.  **Run Server:**
    ```bash
    php artisan serve
    ```

##  Dashboard Preview
> **Note:** Since the admin panel is hosted on a secure subdomain, you can view the screenshots of the analytics and management modules in the `/screenshots` folder.

---
Developed with ❤️ by [Eslam Mekky](https://github.com/EslamMeky)
