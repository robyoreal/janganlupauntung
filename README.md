# Jangan Lupa Untung

A comprehensive business management application built with Laravel and Livewire, designed with an iPad-first approach. This application helps manage basic business operations including purchasing, sales, inventory, and financial tracking.

## Features

### Dashboard
- **Month-to-Date Metrics**: View revenue, unpaid debt, and profit for the current month
- **Top Selling Products**: See your best-selling products ranked by quantity sold
- **Real-time Calculations**: Profit automatically calculated as revenue minus cost of goods sold

### Product Management
- Full CRUD operations for products
- Track SKU, cost price, selling price, and stock levels
- Search and pagination functionality
- Stock level indicators (color-coded badges)

### Transaction Management

#### Buying/Purchasing
- Record purchases from suppliers
- Automatically updates product inventory (increases stock)
- Track payment status (paid/unpaid for debt management)
- Updates product cost price when paid

#### Selling
- Record sales to customers
- Automatically updates product inventory (decreases stock)
- Stock validation prevents overselling
- Auto-fills selling price from product data
- Track sales person and customer information

### Entity Management
- **Suppliers**: Manage supplier contacts and information
- **Customers**: Track customer details
- **Sales Force**: Manage sales team members

## Technology Stack

- **Backend**: Laravel 12 with PHP 8.3
- **Frontend**: Livewire 3 for reactive components
- **Styling**: Tailwind CSS (iPad-first responsive design)
- **Database**: SQLite (portable and easy setup)
- **Build Tools**: Vite

## Installation

1. Clone the repository:
```bash
git clone https://github.com/robyoreal/janganlupauntung.git
cd janganlupauntung
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install Node dependencies:
```bash
npm install
```

4. Copy environment file and generate application key:
```bash
cp .env.example .env
php artisan key:generate
```

5. Run database migrations:
```bash
php artisan migrate
```

6. (Optional) Seed the database with sample data:
```bash
php artisan db:seed
```

7. Build frontend assets:
```bash
npm run build
```

8. Start the development server:
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## Deployment

### Docker Deployment

This application can be deployed using Docker and Docker Compose. A complete Docker setup is provided with:

- **Multi-stage Dockerfile** with PHP 8.2-FPM and Node.js for asset compilation
- **docker-compose.yml** for local development with MySQL
- **Production-ready configuration** with Nginx, Supervisor, and queue workers
- **Automatic database migrations** and cache optimization on startup

**Quick start with Docker Compose:**
```bash
docker-compose up -d
```

The application will be available at `http://localhost:8000`

### Railway.app Deployment

Deploy to Railway.app with one click or follow the comprehensive guide:

[![Deploy on Railway](https://railway.app/button.svg)](https://railway.app/new/template)

**For detailed deployment instructions, see [DEPLOYMENT.md](DEPLOYMENT.md)**

The deployment guide includes:
- Step-by-step Railway.app deployment
- Environment variable configuration
- Database setup (MySQL/PostgreSQL)
- Troubleshooting guide
- Production best practices

## Usage

### Getting Started
1. Navigate to the Dashboard to see an overview of your business metrics
2. Add products in the Products section
3. Add suppliers, customers, and sales team members as needed
4. Record purchases in the Buy section
5. Record sales in the Sell section
6. Monitor your business performance on the Dashboard

### Business Logic

**Buying Process:**
- Select product, quantity, and price
- Choose supplier (optional)
- Set payment status (Paid or Unpaid for debt tracking)
- System automatically increases product stock

**Selling Process:**
- Select product from available stock
- Choose customer and sales person (optional)
- System auto-fills selling price
- Validates stock availability
- System automatically decreases product stock

**Dashboard Calculations:**
- **Revenue**: Total from all selling transactions
- **Debt**: Total unpaid purchasing transactions
- **Profit**: Revenue minus cost of goods sold (calculated from product cost prices)

## Screenshots

### Dashboard
![Dashboard](https://github.com/user-attachments/assets/5cd4bb27-0b73-4118-820f-827d97b33fc1)

### Products Management
![Products](https://github.com/user-attachments/assets/9dcbe021-8cd8-48ec-a542-1389120f37b4)

### Purchase/Buy
![Buy](https://github.com/user-attachments/assets/aefb86e3-e940-4bc7-8f82-10199aedc0ad)

### Sale/Sell
![Sell](https://github.com/user-attachments/assets/7638eb7b-5d7b-4dac-8ae7-6f029f306944)

## Database Schema

- **products**: Product information with pricing and stock
- **suppliers**: Supplier contact information
- **customers**: Customer contact information
- **sales**: Sales team member information
- **transactions**: All buy/sell transactions with full tracking

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## License

This project is open-source and available under the MIT License.
