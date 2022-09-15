## Zadanie rekrutacyjne

Szczegóły aplikacji:

- Testy funkcjonalne wykonane są za pomocą PestPHP
- Aplikacja pozwala na wybór wersji API, czyli podział routes na wersje - v1/v2 itd.
- ID poszczególnych danych są ukryte, zamiast tego używałem w publicznej części aplikacji identyfikatora UUID
- 'Implicit binding' wykonałem za pomocą tego samego UUID
- Każdy model zawiera trait który zapisuje UUID przy stworzeniu egzemplarzu modelu
- Korzystałem z biblioteki timacdonald/json-api dla zgodności formatu aplikacji JSON ze standardem JSON API
- Korzystałem z biblioteki spatie/laravel-query-builder dla implementacji funkcji sortowania/filtracji zdodnie ze standardem JSON API (chociaż można to zaimplementować ręcznie)
- Aplikcja używa klas - DataTransferObject, Actions, FormRequest (w celu walidacji)
- Testy mają prawie 100% pokrycie.
