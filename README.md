# News Aggregator API 📰

A RESTful API built with Laravel that aggregates news articles from multiple sources, allowing users to browse, search, and personalize their news feed.

## ⚙️ Specifications

- **Laravel Version**: 12
- **PHP Version**: 8.4+
- **Database**: MySQL 8.0
- **Authentication**: Laravel Sanctum

## 🛠️ Setup Instructions

Follow these steps to get the project running locally:

### Prerequisites
- Git
- Docker and Docker Compose

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/jazeel-zainudeen/news-aggregator-api.git
   ```

2. **Navigate to project directory**
   ```bash
   cd news-aggregator-api
   ```

3. **Install dependencies**
   ```bash
   docker run --rm \
       -u "$(id -u):$(id -g)" \
       -v "$(pwd):/var/www/html" \
       -w /var/www/html \
       laravelsail/php84-composer:latest \
       composer install --ignore-platform-reqs
   ```

4. **Copy environment file**
   ```bash
   cp .env.example .env
   ```

5. *(Optional)* **Update API keys**
   
   If you plan to fetch news from external sources, update the following variables in the `.env` file with your API keys:
   ```
   NEWS_API_KEY=your_news_api_key
   THE_GUARDIAN_API_KEY=your_guardian_api_key
   THE_NEW_YORK_TIMES_API_KEY=your_nyt_api_key
   ```

6. **Build Docker containers**
   ```bash
   ./vendor/bin/sail build --no-cache
   ```

7. **Start Docker containers**
   ```bash
   ./vendor/bin/sail up -d
   ```

8. **Run migration and seeder**
   ```bash
   ./vendor/bin/sail artisan migrate --seed
   ```

Your application is now running! 🎉

## 📋 API Documentation

API documentation is available at:
```
http://localhost/api/documentation
```

You can explore all available endpoints, request parameters, and response formats through the interactive documentation interface.

## 📌 Additional Notes

### Mail Configuration
By default, mail configuration is set to use Mailpit.
Access the mail interface at:
```
http://localhost:8025/
```

### Useful Sail Commands

- **Stop containers**
  ```bash
  ./vendor/bin/sail down
  ```

- **Run artisan commands**
  ```bash
  ./vendor/bin/sail artisan <command>
  ```

- **Run tests**
  ```bash
  ./vendor/bin/sail test
  ```

- **Run tests with coverage report**
  ```bash
  ./vendor/bin/sail artisan test --coverage
  ```

### Populating News Articles
To populate news articles, choose any of the following methods:

1. **Run the seeder**
   ```bash
   ./vendor/bin/sail artisan db:seed --class=NewsArticleSeeder
   ```
2. **Add API keys in the .env file and run the scheduled command**
   ```bash
   ./vendor/bin/sail artisan news:fetch
   ```

### Troubleshooting

If you encounter permission issues, ensure your user has appropriate permissions for Docker operations.

---

Built with 💻 using Laravel + Docker

