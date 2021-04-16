# <p align="center">SON - Lumen User API</p>

<p align="center">
<img src="https://img.shields.io/badge/php-8.0-green" alt="PHP"/>
</p>

## 💬 About

This project was developed following School of Net [Lumen Intermediário](https://www.schoolofnet.com/curso/php/lumen/lumen-intermediario/3945) course.

## :computer: Technologies

-   [Docker](https://www.docker.com/)

## :scroll: Requirements

-   [Docker](https://www.docker.com/)

## :cd: Installation

```sh
./bin/runenv.sh
```

```sh
docker-compose exec app-php-fpm touch database/database.sqlite
```

```sh
docker-compose exec app-php-fpm php artisan migrate --seed
```

## :runner: Running

**Up app**

```sh
./bin/runenv.sh
```

## :white_check_mark: Tests

After up the container:

```sh
docker exec -t app-php-fpm ./vendor/bin/phpunit
```

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License

[MIT](https://choosealicense.com/licenses/mit/)

## About Me

<p align="center">
    <a style="font-weight: bold" href="https://www.linkedin.com/in/filipe1309/">
    <img style="border-radius:50%" width="100px; "src="https://avatars.githubusercontent.com/u/2081014?s=60&v=4"/>
    </a>
</p>

---

<p align="center">
Done with ♥ by <a style="font-weight: bold" href="https://www.linkedin.com/in/filipe1309/">Filipe Leuch Bonfim</a> 🖖

</p>
