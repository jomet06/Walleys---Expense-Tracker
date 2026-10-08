<?php

return [
    'required' => 'Enter :attribute to continue.',
    'email' => 'Enter a valid email address, for example name@example.com.',
    'confirmed' => 'The confirmation does not match. Enter the same password in both fields.',
    'current_password' => 'That password does not match your account. Try again.',
    'unique' => 'An account already uses this email. Sign in or use a different email.',
    'integer' => 'Enter a whole number for :attribute.',
    'date_format' => 'Choose a valid date for :attribute.',
    'exists' => 'Choose an available :attribute.',
    'in' => 'Choose one of the available options for :attribute.',
    'image' => 'Choose an image file for your receipt.',
    'mimes' => 'Choose a JPG, PNG, or WEBP receipt image.',
    'min' => [
        'string' => 'Use at least :min characters for :attribute.',
        'numeric' => 'Enter :attribute of at least :min.',
    ],
    'max' => [
        'string' => 'Keep :attribute within :max characters.',
        'numeric' => 'Enter :attribute no larger than :max.',
        'file' => 'Choose a file no larger than :max KB.',
    ],
    'attributes' => [
        'name' => 'your name', 'title' => 'a transaction title', 'category_id' => 'category',
        'type' => 'category type', 'icon' => 'an icon', 'amount' => 'an amount',
        'transaction_date' => 'the transaction date', 'description' => 'the description',
        'password' => 'a password', 'current_password' => 'your current password',
        'start_date' => 'the start date', 'end_date' => 'the end date', 'receipt_image' => 'a receipt image',
        'email' => 'an email address',
    ],
    'custom' => [
        'name' => ['required' => 'Enter a name to continue.'],
        'amount' => ['required' => 'Enter the transaction amount.', 'min' => 'Enter an amount greater than zero.'],
        'password' => ['required' => 'Enter your password to continue.'],
    ],
];
