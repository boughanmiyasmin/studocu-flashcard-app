<?php

namespace App;

class Constants
{
    const EMAIL_PROMPT = 'Enter your email';

    const EMAIL = 'email';

    const WELCOME_MESSAGE = 'Welcome to the Flashcard CLI!';
    const MENU_OPTIONS = [
        '1. Create a flashcard',
        '2. List all flashcards',
        '3. Practice',
        '4. Stats',
        '5. Reset',
        '6. Exit'
    ];

    const CHOOSE_OPTION_PROMPT = 'Choose an option';
    const GOODBYE_MESSAGE = 'Goodbye!';
    const INVALID_OPTION_MESSAGE = 'Invalid option. Please try again.';
    const QUESTION_PROMPT = 'Enter the flashcard question';
    const QUESTION = 'question';
    const ANSWER_PROMPT = 'Enter the answer';
    const ANSWER = 'answer';
    const FLASHCARD_CREATED_FAIL = 'Something Went wrong while creating the flashcard, please try again later!';
    const FLASHCARD_CREATED_SUCCESS = 'Flashcard created successfully!';
    const LIST_HEADER = ['ID', 'Question', 'Answer'];
    const PRACTICE_LIST_HEADER = ['ID', 'Question', 'Status'];

    const NOT_ANSWERED = 'Not answered';
    const CORRECT = 'Correct';
    const INCORRECT = 'Incorrect';

    const ENTER_FLASHCARD_ID_PROMPT = 'Enter the ID of the flashcard you want to practice or type exit to go back to the main menu';
    const EXIT = 'exit';
    const ENDING_PRACTICE = 'Ending practice...';
    const ALREADY_CORRECT_MESSAGE = 'You have already answered this question correctly.';

}
