#  GoPokedex

GoPokedex is an extended Pokémon Go tracker available at https://gopokedex.com

  - Track your seen, caught, shiny, 100%, 0%, and lucky Pokémon
  - Information about different forms
  - Filter by Pokémon available, what you have, or what you need.
  - Register an account to track, or use an anonymous unique code.

## Tech
GoPokedex uses the following technologies:

  - [PHP 7](https://php.net/) - server side functionality
  - [VueJS](https://vuejs.org/) - user interface framework
  - [Bulma](https://bulma.io/) - css framework
  - [Mailgun](https://mailgun.com) - email delivery service

## Installation

Installing GoPokedex for your own use should be pretty straight forward if you have installed PHP applications before. The basic steps are:

1. Download the code.
2. Run ```composer install``` to install library dependencies.
3. Copy the **config.sample.yaml** file to **config.yaml** and update with your information.
    - You can use **mysql**, **postgresql**, or **sqlite** databases
    - You will need your own **mailgun** credentials
4. Import the 4 **SQL** files in ```src/Migration``` to create the required databases
5. Create a user and update their **role_mask** field to **1** to set up as admin
6. Once logged-in as the **admin** user, navigate to ```/admin/pokemon``` to import the JSON pokedex into the DB.

### Missing Pokemon images

To avoid including copyrighted images in the repository, the ```public/assets/images/pokemon``` and ```public/assets/images/pokemon/thumbs``` folders are empty.
You should include your own images in there named with the Pokémon's Pokedex number with a **.png** extension. (i.e 001.png, 002.png, etc...)

### Adding new Pokémon to the system / Updating Pokémon information

To add new Pokémon or update the information for the Pokémon in the system, you can either update the ```pokemon``` database directly, or update the ```src/Migrations/pokedex.json``` file and re-run ```/admin/pokemon``` as the **admin** user.


## Contributing

All contributions are welcome! Working on the Todo items above or Open Issues is encouraged.
 If you wish to contribute, please create an issue first (if it doesn't exist) so that your feature, problem or question can be discussed.

## License

This project is licensed under the terms of the [GNU GPLv3](https://opensource.org/licenses/gpl-3.0).
