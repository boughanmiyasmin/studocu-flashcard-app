# Flashcard App with Laravel Sail and Docker

This project is an interactive CLI program for Flashcard practice, developed using Laravel and Docker. The application allows users to create, list, and practice flashcards through a command-line interface. Additionally, it provides statistics and reset functionalities.

## Requirements

- Docker
- composer
- php8.3

## Getting Started

### Clone the Repository

```sh
git clone git@github.com:boughanmiyasmin/studocu-flashcard-app.git
cd studocu-flashcard-app
```
### Setup Environment Variables

Copy the example environment file and modify it as needed:

```sh
cp .env.example .env
```

Install all laravel dependencies using composer
```sh
composer install
```

### Build and Start the Containers
Using Laravel Sail, build and start the Docker containers:
but first, to streamline the process of running Sail commands, you can create a Bash alias. 
This will save you from having to type vendor/bin/sail every time you need to execute a Sail command.

Linux or Mac
```sh
alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'
```
Windows
add alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'

```sh
C:\Users\<username>\AppData\Local\GitHub\PortableGit_\etc\profile.d\aliases.sh
```
now run sail
```sh
sail up -d
```

### Clear laravel configuration cache

```sh
sail artisan config:cache
```

### Run Migrations
To set up the database, run the migrations:

```sh
sail artisan migrate
```

### Run the Flashcard CLI Program
You can now start the interactive flashcard CLI program:

```sh
sail artisan flashcard:interactive
```

## Project Structure and Justification
### Dependencies

- Laravel Sail: Provides a simple way to run the Laravel application using Docker.
- PHP 8.3: Latest version of PHP for improved performance and features.
- MySQL: Used as the SQL database to persist flashcards and practice progress.

### Database Structure
- flashcards table: Stores the flashcard questions and answers.
- practices table: Tracks the practice status for each flashcard, including whether the answer was correct or incorrect.

### Additional information
- Create a Flashcard: Prompts the user to enter a question and its answer, then stores it in the database.
- List All Flashcards: Displays all created flashcards.
- Practice: Allows the user to practice flashcards, showing progress and storing practice results.
- Stats: Displays statistics including total questions, percentage of answered questions, and percentage of correctly answered questions.
- Reset: Erases all practice progress.
- Exit: Exits the interactive program.

## Testing
The project includes test cases to ensure the correct functionality of all features. Run the tests with:

```sh
sail artisan test
```
