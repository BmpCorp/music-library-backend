## Simple Music Library

This repository contains a demo music library application built with the latest versions of PHP and Laravel.
The project is designed for presentation and portfolio purposes, not for production use.

While the application provides basic functionality for managing and browsing a collection of music,
its primary focus is to showcase code quality, design patterns, development approach and set of tools I'm experienced in
rather than complex business logic.

### Frameworks and Libraries used

- **Laravel 12** working on PHP 8.3.4 
- MySQL 8
- nginx
- Meilisearch as search engine
- Redis for cache
- RabbitMQ as message broker
- Docker and Docker Compose for deployment
- **Backpack for Laravel v6** for admin panels
- Spatie Laravel Media Library with Backpack support
- Backpack Permission Manager
- AI services for optional database seeding (Open Router)
- Swagger for API documentation
- Optional Laravel Telescope monitoring (off by default)
- Optional Elasticsearch/Filebeat/Kibana for Laravel logs processing and analysis (separate docker-compose file)

### Business Logic and Functional Overview

The core entities of the project are musical artists and their albums. 
The library of artists and albums is managed by an administrator or a content manager. 
Users can add artists to their "Favorites", mark if they are currently listening to them, and specify the last album they listened to. 
The administrator or manager can also view the lists of users' favorite artists.

The project features a flexible access control system based on Backpack Permission Manager. 
Several permissions grant access to different sections of the admin panel. 
With at least one of these permissions, a user can log into the admin panel using their email and password.

For administrators, a separate menu section provides access to various debugging and monitoring tools.

Artists and albums are indexed using Laravel Scout and Meilisearch.
The search functionality covers titles and genres; it is also possible to search for albums by artist name. 
The search engine supports transliteration and queries typed in the wrong keyboard layout. 
Indexing occurs when the corresponding entities are updated and also on a schedule via the `scout:run` command.

Media files are handled by `spatie/laravel-medialibrary`, which allows for uploading artist logos and album covers. 
A file manager like elFinder is not included in this project (although I have experience working with it).

### Technical Details

The `Artist` model includes a custom `whereFamilyFriendly` scope, which is used for filtering based on the related `Album` model.

The `Artist` model has a `total_song_count` field. Its calculation, being a potentially time-consuming operation,
is offloaded to the `RecalculateArtistSongCount` job. Corresponding events are dispatched upon the creation, update 
and deletion of an album (where the song count is directly specified). The `UpdateArtistStats` event listener
(the only one in this case) delegates the execution to the aforementioned job class. 
For fine-grained control over event-to-listener binding, an `EventServiceProvider` is used, which is not included
by default in recent Laravel versions. I find it more appropriate to place these bindings there instead of in `AppServiceProvider`.

Caching for artist and album search results is implemented in the `ArtistController::index` and `AlbumController::index` methods.

The corresponding directory contains examples of feature tests.

The API follows a RESTful approach, is versioned, and is fully documented according to the OpenAPI 3.0 specification.
The documentation is available via Swagger. 

The admin panel menu, form labels, and error messages are fully localized (supporting both Russian and English).

### CI/CD

A presumptive developer team is expected to use feature- and fix-branches to commit their code. When feature or fix is finished,
a pull request is created to merge these branches to develop branch, squashing commits and deleting branch afterward.
Once merged into develop branch, new feature should be deployed via GitHub Actions to some staging environment.
After testing, develop branch finally merged into master branch and deployed to production environment.

Since there's no actual staging or production servers with this project, no actual deploy is performed, but there's
a Build and Test **GitHub Actions workflow** present, triggering after develop branch is pushed (PR triggers it too).

### Additional Notes

Using LLM to provide mock data is for presentation purposes. In real project, it would be more reliable to use
pre-generated seed data.
Same for ELK stack, that is surely overkill for such a project. 

See DEPLOYMENT.md for deployment instructions.
