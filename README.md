# QuizGo

QuizGo is a PHP-based online quiz builder designed for users who want to create, take, and review quizzes with ease. The platform includes user authentication, dashboard-based quiz management, and AI-assisted question generation powered by Google Gemini.

## Overview

QuizGo provides a lightweight and practical way to build quizzes for learning, assessments, and engagement. The application supports:

- User registration and login
- Quiz creation workflow
- AI-generated quiz questions
- Quiz scoring and results
- Quiz history tracking
- Leaderboard support
- PDF export for quiz data
- Responsive front-end using Bootstrap

## Features

- Secure authentication flow using PHP sessions and MySQL
- Dashboard for creating and accessing quizzes
- AI-powered content generation via Google Gemini API
- Quiz-taking experience with answer submission and result calculation
- History of previously created or completed quizzes
- Leaderboard page for ranking and comparison
- Professional UI with Bootstrap styling
- Easy local setup for development testing

## Tech Stack

- PHP
- MySQL
- Bootstrap 5
- Google Gemini API
- HTML / CSS / JavaScript

## Architecture

The app is organized into a simple MVC-like structure using PHP pages and reusable includes:

```text
QuizGo/
├── config/
│   ├── config.php
│   ├── database.php
│   ├── api_helper.php
│   └── ...
├── includes/
│   ├── auth.php
│   ├── generate_quiz.php
│   ├── navbar.php
│   └── ...
├── quiz/
│   ├── create.php
│   ├── take.php
│   ├── submit.php
│   ├── history.php
│   ├── result.php
│   ├── leaderboard.php
│   ├── export_pdf.php
│   └── ...
├── dashboard.php
├── index.php
├── login.php
├── register.php
├── profile.php
├── logout.php
├── README.md
├── test_api.php
├── test_connection.php
├── test_database.php
├── test_quiz.php
└── ...
```

## Requirements

Before running the project locally, ensure you have:

- PHP 8.x or later
- MySQL or MariaDB
- Apache or Nginx web server (or PHP built-in server)
- A Google Gemini API key

## Configuration

1. Update the database credentials in `config/database.php`:

```php
$host = 'localhost';
$dbname = 'ai_quiz_builder';
$username = 'root';
$password = '';
```

2. Update your Gemini API settings in `config/config.php`:

```php
define("GEMINI_API_KEY", "YOUR_API_KEY");
define("GEMINI_API_URL", "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent");
```

3. Make sure the required MySQL database and tables are available.

## Getting Started

Clone the repository:

```bash
git clone https://github.com/Mohitranjan07/QuizGo.git
cd QuizGo
```

Start a local PHP server:

```bash
php -S localhost:8000
```

Then open:

```text
http://localhost:8000
```

## Usage

1. Register a new account or log in.
2. Open the dashboard.
3. Create a new quiz.
4. Generate questions using the Gemini-powered flow.
5. Take the quiz and review the results.
6. View quiz history and leaderboard information.

## Project Flow

The typical application flow looks like this:

- Landing page (`index.php`)
- Registration / login (`register.php`, `login.php`)
- Dashboard (`dashboard.php`)
- Quiz generation (`quiz/create.php`)
- Quiz execution (`quiz/take.php`)
- Submission handling (`quiz/submit.php`)
- Result review (`quiz/result.php`)

## Security Notes

This project is intended for learning and local development. For production deployment, consider:

- Using environment variables for sensitive values
- Storing credentials outside source control
- Validating and sanitizing all user input
- Protecting API keys and database credentials
- Enabling secure server configuration and HTTPS

## Roadmap

Planned improvements may include:

- Better role-based access control
- Improved quiz templates and categories
- Enhanced AI prompt tuning
- Export in more formats
- Cleaner admin dashboard
- Better test coverage and validation

## License

This project does not currently include a license file. If you plan to distribute it publicly, it is recommended to add an appropriate open-source license.

## Contributing

Contributions are welcome. If you want to improve the project:

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Submit a pull request

## Contact

For questions, issues, or feature requests, please use the GitHub Issues section of this repository.
