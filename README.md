# TaskManager MVP (ES + CQRS)

System zarządzania zadaniami zbudowany w architekturze **Event Sourcing (ES)** oraz **CQRS**, demonstrujący nowoczesne podejście do budowania skalowalnych aplikacji w Symfony 7.

## 🚀 Szybki start

### Wymagania
- Docker & Docker Compose
- Make (rekomendowane do wygodnej obsługi projektu)

### Instalacja i Makefile
Projekt zawiera plik `Makefile`, który automatyzuje powtarzalne czynności. Aby przygotować i uruchomić środowisko, wykonaj sekwencję:

1. make build
2. make up
3. make composer_install

**Dostępne kluczowe komendy Make:**
- make build – buduje obrazy Docker.
- make up / make down – zarządzanie cyklem życia kontenerów.
- make shell – wejście do terminala kontenera PHP.
- make composer_install – instalacja zależności PHP.
- make cache_clear – czyszczenie pamięci podręcznej i logów.
- make graphQL_dump_schema – generowanie aktualnego schematu GraphQL.
- make generate_secret_keys – generowanie kluczy dla mechanizmu secrets.

## 🔐 Autentykacja i API

System wykorzystuje **JWT (JSON Web Token)**. Logowanie odbywa się bezhasłowo na podstawie adresu e-mail zintegrowanego z tożsamościami JSONPlaceholder.

### 1. Logowanie (Pobranie tokena)
Wyślij żądanie POST:
URL: http://localhost:8080/api/login
Body (JSON): {"email": "Julianne.OConner@kory.org"}

### 2. Autoryzacja GraphQL
Nagłówek: Authorization: Bearer {token}
- Endpoint: http://localhost:8080/graphql
- Explorer: http://localhost:8080/graphiql

## 🏗️ Architektura i Decyzje Projektowe

- **Pragmatyczny Event Sourcing:** Własny DbalEventStore zamiast ciężkich frameworków. Pełna kontrola nad strumieniem zdarzeń i minimalny narzut.
- **CQRS z Symfony Messenger:** Rozdzielenie szyny komend (Write) od szyny zdarzeń i zapytań (Read).
- **Brak Refleksji:** Mapowanie zdarzeń oparte na jawnych kontraktach, co zapewnia wysoką wydajność i bezpieczeństwo typów.
- **State-at-Time:** Możliwość odtworzenia stanu Agregatu w dowolnym punkcie historii (Audit Log / History Query).
- **Read Model (Projekcja):** Synchronizowana synchronicznie tabela SQL zoptymalizowana pod szybkie wyszukiwanie.

## ✅ Zrealizowane funkcjonalności

### Agregat User (Integracja i Auth)
- Integracja z zewnętrznym API JSONPlaceholder (Strategy Pattern).
- Autentykacja JWT (Lexik/Lcobucci) i UserContext.
- Query "me" i filtrowanie użytkowników.

### Agregat Task (Logika Biznesowa)
- Full Event Sourcing (Tworzenie, Zmiana statusu, Edycja).
- Macierz przejść statusów (Domain Logic).
- Historia zmian (Task History) pokazująca ewolucję obiektu.
