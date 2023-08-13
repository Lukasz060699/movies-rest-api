# REST API - zarządzanie filmami 

## Wykorzystane technologie

- Laravel
- PHP
- MySQL
- Postman (testy)

Należy zaimportować bazę danych ```movies.sql```.

## Endpointy

Aplikacja posiada podstawowe uwierzytelnianie. Użytkownicy mogą mieć jedną z dwóch ról: admin, user.

- **Administrator** ma dostęp do wszystkich zasobów oraz ich dodawania/modyfikowania/usuwania
- **Użytkownik** może wyświetlać filmy oraz dodawać i usuwać opinię do danego filmu

W celu przetestowania endpointów z użyciem Postmana należy najpierw uruchomić aplikację serwerową, a następnie wybrać jeden z linków, wkleić go do pola URL, ustawić odpowiednią metodę HTTP i kliknąć Send. 

### Logowanie i wylogowywanie
1. Logowanie: http://localhost:8000/api/login **POST**

    Przykładowe RequestBody:
   ```
   Admin:
   {
    "email": "adam@email.com",
    "password": "password"
   }
   User:
   {
    "email": "john@email.com",
    "password": "password"
   }
   ```
   Zostanie wygenerowany token, który należy umieścić w polu ```Token``` w zakładce ```Authorization```.
   
3. Wylogowywanie: http://localhost:8000/api/logout **POST**

### Gatunki
1. Dodaj gatunek filmu: http://localhost:8000/api/genres **POST**

   Przykładowe RequestBody:
   ```
   {
    "name": "Komedia"
   }
   ```
3. Usuń gatunek o podanym id: http://localhost:8000/api/genres/{id} **DELETE**

### Filmy
1. Dodaj film: http://localhost:8000/api/movies **POST**

   W celu przetestowania dodawania filmu należy przejść w Postmanie do zakładki ```Body```, a następnie wybrać ```form-data``` (ze względu na wgrywanie okładki).
    
3. Aktualizuj film: http://localhost:8000/api/movies/{id} **PUT**
4. Wyświetl dane o wszystkich filmach: http://localhost:8000/api/movies **GET**
5. Wyświetl dane filmu o podanym id: http://localhost:8000/api/movies/{id} **GET**
6. Znajdź film po tytule: http://localhost:8000/api/movies/search?title=nazwa_filmu **GET**
7. Usuń film o podanym id: http://localhost:8000/api/movies/{id} **DELETE**

### Ocena filmów
1. Dodaj ocenę filmu o podanym id: http://localhost:8000/api/movies/{id}/rate **POST**
2. Usuń ocenę danego filmu: http://localhost:8000/api/movies/{id}/rate **DELETE**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
