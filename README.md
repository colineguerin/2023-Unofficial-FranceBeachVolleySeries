This is an unofficial remake of the Beach-Volley System website. It is meant to improve user experience for French beach-volleyball players.

As a visitor, they will be able to:

- Access the homepage with the login button
- Read the articles
- Contact the admin through a contact form, in case the connection doesn't work or to ask for information

As a player, they will be able to:

- Login with their permit number and the auto-generated password given by the admin, once they have paid their membership
- Create one or several teams and reactivate teams from previous seasons
- Enter tournaments and follow their results and annual performance
- See all tournaments (past and future), the teams registered and their results
- Check the national score board, filtered by male and female

As an admin, they will be able to:

- Create a new player account with the information given by the club after their registration
- Update and delete the player's account (in case of incorrect behavior for example)
- Create, update, delete and duplicate a tournament
- Create, update and delete teams
- Create, update and delete articles for the homepage
- Add new clubs with their information
- Enter the results of the latest tournaments and update the national score board consequently

The project was created in PHP 8.1 with the Symfony framework v6. It includes the EasyAdmin bundle for the admin dashboard as well as other useful symfony bundles. It uses Bootstrap for the style.

To check it out, follow these easy steps:

1. Clone this repo in your favorite editor
2. Run composer install (you may also install the symfony CLI if you don't have it yet)
3. Set up the database configuration in the .env file (ideally creating a .env.local file)
4. Run database migrations: php bin/console doctrine:migrations:migrate
5. Start the symfony development server (for example, symfony server:start)
6. Open your web browser and navigate to http://localhost:8000 to view the project.

If you want to see the website as a player and/or the back-office as an admin, run symfony console doctrine:fixtures:load. It will create a user with the admin role (you can check their password in the fixtures). From there, you will be able to create other users. 
NB : You will need the PHP extension php-intl to manage date and time values as an admin.

Please note that this website is a work in progress as a personal project, everything is not functional yet but, hopefully it will be soon and additional features might come!