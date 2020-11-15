# Basic Symfony project

Ready to use basic project already configured to work with:
- multilanguage
- a database with Doctrine
- webpack Encore for the frontend
- maker bundle to have an easier life developing
- authentication with login form (go to /login)
- easy admin ready to manage the administrative side (go to /admin)
- 2 commands to manage administrators
- Open Graph tags already into the base template

## Installation
Run: 

    composer install
    npm install
    npm run build
    
Configure the database into the .env.local of the project and then: 

    php bin/console doctrine:migrations:migrate    
    php bin/console app:admin-add
    
## Configurations

- The project has the default language set on "it". Change it in the services.yaml file
- The project has a twig extension to manage languages on the templates
- The project has an admin dashboards but no entities configured
- The project is not meant to have normal users to login, because it lacks of "lost password" and "change password" functionality
- The project lacks of a basic css framework (TBD)
