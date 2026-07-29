<?php

// Translation of lang/es/legal.php (the source locale). Keep the sections in the
// same order and describe the same behaviour — this is not a generic template.
return [
    'updated' => 'Last updated: 28 July 2026',

    'operator' => [
        'h' => 'Who runs this instance',
        'p' => 'This instance is operated by :operator.',
        'contact' => 'For anything about your data you can write to :contact.',
    ],

    'privacy' => [
        'title' => 'Privacy',
        'intro' => 'This instance of Nexo Agenda is open source and self-hosted. We collect the minimum a booking needs, and nothing else. There are no tracking cookies, no third-party analytics and nothing is sent to advertising networks.',
        'sections' => [
            [
                'h' => 'Two different relationships',
                'p' => 'Two kinds of people meet here: the business, which opens an account to take bookings, and the person who books an appointment on that business\'s page without creating any account. Whoever runs this instance processes both sets of data so the service works. The business, in turn, is the one answerable to its own client: it decides what happens with those details after the booking. If you booked an appointment and want your data corrected or deleted, you can ask the business or whoever runs this instance — both can do it.',
            ],
            [
                'h' => 'What we store about the business account',
                'p' => 'Name, email and an encrypted (hashed) version of the password. For the business we also store what it publishes on its page: name, category, city, address, WhatsApp number, description, colour, logo and timezone, along with its services and the names of its team members. If you sign in with Nexo ID, we also store the identifier that service gives us to recognise you.',
            ],
            [
                'h' => 'What we store about the person booking',
                'p' => 'The name you type when booking, an email or a phone number so we can confirm the appointment and reach you, and the note you leave if you want to. That is what makes up the booking: service, professional, day and time. We do not create an account and never ask you for a password. Those details are visible to the business you booked with — it is the one that needs to know who is coming — and appear in its calendar, its client list and the CSV files it downloads.',
            ],
            [
                'h' => 'The link to manage your appointment',
                'p' => 'When you book, you get a link of your own to view, cancel or reschedule the appointment without an account. That link is the key: the database only stores its fingerprint (a hash), never the link itself, so even someone with access to the database could not rebuild it. Anyone holding the link can manage that appointment, so it is best not to share it.',
            ],
            [
                'h' => 'Waiting list and reminders',
                'p' => 'If you join the waiting list for a day, we store your name and email to let you know if a slot frees up. The appointment reminder (24 hours before) uses the same contact detail you left when booking. None of this is used to send you advertising.',
            ],
            [
                'h' => 'Reviews',
                'p' => 'If your appointment was marked as attended, you can leave a rating and a comment. They are published on the business page next to the name you booked with, and the business can hide them.',
            ],
            [
                'h' => 'Cookieless metrics',
                'p' => 'We count how many distinct people viewed a business page using a fingerprint computed with the current date and then discarded: we do not store your IP or your browser, and today\'s fingerprint cannot be matched against tomorrow\'s. We do not know who you are and cannot follow you across sites.',
            ],
            [
                'h' => 'Ecosystem counter',
                'p' => 'If this instance turns it on, the business dashboard pages send an anonymous visit (which tool and which path, no personal data) to the Nexo hub. It is never sent from a business\'s public page, and it honours the browser\'s "Do Not Track" signal.',
            ],
            [
                'h' => 'Cookies',
                'p' => 'Only the ones the site needs to work: the session cookie (to keep a signed-in business identified) and the ones remembering the language and the light or dark theme you chose. None of them is for advertising or tracking.',
            ],
            [
                'h' => 'Email',
                'p' => 'Confirmations, reminders, cancellation or reschedule notices and account emails are sent through an external email provider, which necessarily processes the destination address and the message content in order to deliver it.',
            ],
            [
                'h' => 'A professional\'s calendar subscription',
                'p' => 'Each professional can subscribe their agenda to an external calendar through a secret link. That calendar includes the name of the person who booked and the service, so the link should not be shared; the business can regenerate it at any time and the previous one stops working.',
            ],
            [
                'h' => 'Contact form',
                'p' => 'If you write to us through the contact form we store the message, the type of enquiry, the page you wrote from and your email if you leave one, so we can reply.',
            ],
            [
                'h' => 'How long we keep it',
                'p' => 'Appointments, reviews and statistics are kept for as long as the business keeps its account: they are its history. Nothing is deleted automatically by age. When an account is deleted, its business, services, team, appointments, waiting list, reviews and metrics are deleted along with it.',
            ],
            [
                'h' => 'Your rights',
                'p' => 'You can ask for access to your data, its correction or its deletion. If you have an account, write to whoever runs this instance (the contact is below and on the help page): today an account is deleted on request, there is no button for it. If you booked an appointment, you can cancel it from your link and ask the business to remove your details from its list.',
            ],
            [
                'h' => 'Other instances',
                'p' => 'Nexo Agenda can be installed on any server. Each installation is independent and responsible for its own data: this policy covers this instance only.',
            ],
        ],
    ],

    'terms' => [
        'title' => 'Terms of use',
        'intro' => 'By using this instance of Nexo Agenda you accept what follows. It is a free service, offered as is.',
        'sections' => [
            [
                'h' => 'What the service is',
                'p' => 'A tool for a business to publish its booking page, set up its services, team and opening hours, and take online appointments with email confirmation and reminder. We do not process payments, we take no deposits or commissions, and we are not part of the agreement between the business and its client.',
            ],
            [
                'h' => 'The business account',
                'p' => 'You need an account to publish a page and take bookings. You are responsible for what happens with your account and for keeping your password safe. Booking an appointment, on the other hand, requires no account.',
            ],
            [
                'h' => 'The business answers for its clients\' data',
                'p' => 'The details of whoever books are collected so the business can provide the service, and the business is the one answerable to that person: it must use them only to handle the appointment, never pass them on or use them for advertising without permission, honour access or deletion requests and comply with whatever regulation applies to it. The same goes for anything it exports to CSV or subscribes to its calendar: once downloaded, that file is under its control.',
            ],
            [
                'h' => 'The appointment is between the business and its client',
                'p' => 'Delivering the service, its price, punctuality, the cancellation policy and any complaint are matters between the business and the person who booked. Nexo Agenda only provides the calendar.',
            ],
            [
                'h' => 'Misuse',
                'p' => 'You may not publish fake or misleading businesses, impersonate others, upload illegal content, book appointments in other people\'s names or use the tool to collect data for purposes unrelated to the booking. Anyone can report a business through the contact form, and whoever runs this instance can take it down: its page stops being available and no further appointments can be booked.',
            ],
            [
                'h' => 'Availability',
                'p' => 'The service is offered with no availability guarantee. We do what is reasonable to keep it online, but there can be outages: a business should not rely on this calendar alone to work.',
            ],
            [
                'h' => 'Limitation of liability',
                'p' => 'Whoever runs this instance is not liable for damages arising from use of the service, including appointments that are not honoured, notices that do not arrive or data loss.',
            ],
            [
                'h' => 'Free software',
                'p' => 'Nexo Agenda is distributed under the MIT licence: you can read the code, modify it and host your own instance. The software is provided without warranty, as that licence states.',
            ],
            [
                'h' => 'Changes',
                'p' => 'These terms may change. The date above shows the last update.',
            ],
        ],
    ],
];
