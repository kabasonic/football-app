# Football API 🚀

## Installation 🛠️

1. Install project dependencies:

    ```bash
    composer install
    ```

2. Set up the `DATABASE_URL` in your `.env.local` file. 🔧
   ```bash
   cp .env .env.local
    ```
3. Create the database:

    ```bash
    bin/console d:d:create
    ```

4. Run migrations to set up the schema:

    ```bash
    bin/console d:m:migrate
    ```

## Docker Setup 🐳

1. Run `cd docker` and copy `.env.dist` to `.env` inside:

    ```bash
    cd docker && cp .env.dist .env
    ```

2. Set up `.env` with your environment-specific configuration. 🔑

3. Build the Docker containers:

    ```bash
    make dc_build
    ```

4. Start the Docker containers:

    ```bash
    make dc_up
    ```

5. Access the app's bash:

    ```bash
    make app_bash
    ```

6. Install dependencies and run migrations inside the Docker container:

    ```bash
    composer i
    ```

    ```bash
    bin/console d:m:migrate
    ```


## For Tests 🧪

1. Create the test database:

    ```bash
    bin/console d:d:create --env=test
    ```

2. Run migrations for the test environment:

    ```bash
    bin/console d:m:migrate --env=test
    ```

3. Load the test fixtures:

    ```bash
    bin/console d:f:load --env=test
    ```

## Running Tests 🏃‍♂️

1. Run the tests:

    ```bash
    ./vendor/bin/phpunit
    ```
## cURL Scripts for API Requests 🚀

## Team

### List
```bash
curl -X GET 'http://127.0.0.1:8000/api/teams' \
--header 'Content-Type: application/json'
```

### Details
```bash
curl -X GET 'http://127.0.0.1:8000/api/teams/{teamId}' \
--header 'Content-Type: application/json'
```

### Create
```bash
curl -X POST 'http://127.0.0.1:8000/api/teams' \
--header 'Content-Type: application/json' \
--data '{
  "name": "Motor",
  "city": "Lublin",
  "yearFounded": 2024,
  "stadiumName": "Arena Lublin"
}'
```

### Update
```bash
curl -X PUT 'http://127.0.0.1:8000/api/teams/{teamId}' \
--header 'Content-Type: application/json' \
--data '{
    "name": "Motor Star",
    "city": "Lublin Centrum",
    "yearFounded": 2025,
    "stadiumName": "Arena Lublin Star"
}'
```

### Delete
```bash
curl -X DELETE 'http://127.0.0.1:8000/api/teams/{teamId}' \
--header 'Content-Type: application/json'
```

### Relocate
```bash
curl -X PUT 'http://127.0.0.1:8000/api/teams/{teamId}/relocate' \
--header 'Content-Type: application/json' \
--data '{
  "city": "Warszawa"
}'
```
## Team player

### List
```bash
curl -X GET 'http://127.0.0.1:8000/api/teams/{teamId}/players' \
--header 'Content-Type: application/json'
```

### Details
```bash
curl -X GET 'http://127.0.0.1:8000/api/teams/{teamId}/players/{playerId}' \
--header 'Content-Type: application/json'
```

### Create
```bash
curl -X POST 'http://127.0.0.1:8000/api/teams/{teamId}/players' \
--header 'Content-Type: application/json' \
--data '{
  "firstName": "John",
  "lastName": "Doe",
  "age": 23,
  "position": "GOALKEEPER"
}'
```

### Update
```bash
curl -X PUT 'http://127.0.0.1:8000/api/teams/{teamId}/players/{playerId}' \
--header 'Content-Type: application/json' \
--data '{
    "firstName": "John",
    "lastName": "Doe",
    "age": 23,
    "position": "DEFENDER"
}'
```

### Delete
```bash
curl -X DELETE 'http://127.0.0.1:8000/api/teams/{teamId}/players/{playerId}' \
--header 'Content-Type: application/json'
```
## Project tree

```angular2html
src
├── Application
│   ├── Command
│   │   ├── AddPlayerToTeamCommand.php
│   │   ├── CreateTeamCommand.php
│   │   ├── DeleteTeamCommand.php
│   │   ├── RelocateTeamCommand.php
│   │   ├── RemovePlayerFromTeamCommand.php
│   │   ├── UpdatePlayerInTeamCommand.php
│   │   └── UpdateTeamCommand.php
│   ├── Dto
│   │   ├── PlayerDto.php
│   │   ├── Request
│   │   │   ├── Player
│   │   │   │   └── PlayerPayloadRequest.php
│   │   │   └── Team
│   │   │       ├── RelocateTeamRequest.php
│   │   │       └── TeamPayloadRequest.php
│   │   ├── TeamDto.php
│   │   └── TeamPlayerDto.php
│   ├── Handler
│   │   ├── AddPlayerToTeamHandler.php
│   │   ├── CreateTeamHandler.php
│   │   ├── DeleteTeamHandler.php
│   │   ├── GetTeamPlayerQueryHandler.php
│   │   ├── GetTeamPlayersQueryHandler.php
│   │   ├── GetTeamQueryHandler.php
│   │   ├── GetTeamsQueryHandler.php
│   │   ├── RelocateTeamHandler.php
│   │   ├── RemovePlayerFromTeamHandler.php
│   │   ├── UpdatePlayerInTeamHandler.php
│   │   └── UpdateTeamHandler.php
│   ├── Query
│   │   ├── GetTeamPlayerQuery.php
│   │   ├── GetTeamPlayersQuery.php
│   │   ├── GetTeamQuery.php
│   │   └── GetTeamsQuery.php
│   ├── Service
│   │   └── NotificationService.php
│   └── Validator
│       ├── Constraints
│       │   └── CurrentYearConstraintValidator.php
│       └── CurrentYearConstraint.php
├── Domain
│   ├── Event
│   │   └── TeamRelocatedEvent.php
│   ├── Exception
│   │   ├── InvalidLocationChangeException.php
│   │   ├── InvalidPlayerAgeException.php
│   │   ├── InvalidPlayerPositionException.php
│   │   ├── InvalidTeamYearFoundedException.php
│   │   ├── InvalidUlidException.php
│   │   ├── PlayerNotFoundException.php
│   │   ├── TeamNotFoundException.php
│   │   └── TeamPlayerLimitExceededException.php
│   ├── Models
│   │   └── Team
│   │       ├── Entity
│   │       │   ├── Player.php
│   │       │   └── Team.php
│   │       └── ValueObject
│   │           ├── PlayerId.php
│   │           └── TeamId.php
│   └── Repository
│       └── TeamRepositoryInterface.php
├── Infrastructure
│   ├── Controller
│   │   ├── AbstractController.php
│   │   ├── Player
│   │   │   └── PlayerController.php
│   │   └── Team
│   │       └── TeamController.php
│   ├── EventListener
│   │   └── TeamRelocatedListener.php
│   ├── EventSubscriber
│   │   └── ExceptionSubscriber.php
│   ├── Persistence
│   │   └── Doctrine
│   │       ├── Mapping
│   │       │   ├── Player.orm.xml
│   │       │   └── Team.orm.xml
│   │       └── Migrations
│   │           └── Version20250226001849.php
│   └── Repository
│       └── TeamRepository.php
└── Shared
    ├── Application
    │   ├── Command
    │   │   ├── CommandBusInterface.php
    │   │   ├── CommandHandlerInterface.php
    │   │   └── CommandInterface.php
    │   └── Query
    │       ├── QueryBusInterface.php
    │       ├── QueryHandlerInterface.php
    │       └── QueryInterface.php
    ├── Domain
    │   ├── Aggregate
    │   │   └── AggregateRoot.php
    │   ├── Event
    │   │   └── DomainEventInterface.php
    │   ├── Services
    │   │   └── UlidService.php
    │   └── ValueObject
    │       ├── AggregateRootId.php
    │       └── UserValueObject.php
    └── Infrastructure
        ├── Bus
        │   ├── CommandBus.php
        │   └── QueryBus.php
        └── Kernel.php
```
