# TmpNote.de

A free and open-source service for end-to-end encrypted notes and code snippets.

[Visit TmpNote.de.](https://tmpnote.de)

## Apache Configuration

    <VirtualHost *:80>
        ServerName tmpnote.de
        DocumentRoot "/var/www/tmpnote.de"
        ErrorDocument 404 /404.php
    </VirtualHost>
    <Directory /var/www/tmpnote.de/src>
        Require all denied
    </Directory>

## SQL Statement to Create Table

    CREATE TABLE `tmpnotes` (
        `id` varchar(11) NOT NULL,
        `type` int(1) NOT NULL,
        `formatting` varchar(20) NOT NULL,
        `expires` int(11) NOT NULL,
        `encrypted` text NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
    ALTER TABLE `tmpnotes` ADD PRIMARY KEY (`id`);

within a databased called `other`.

## Crontab Configuration

    * * * * * php /var/www/tmpnote.de/src/minutely.php
