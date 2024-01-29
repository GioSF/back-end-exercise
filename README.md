The test is not idempotent - it requires to migrate and seed the database before execution:

php artisan migrate:fresh; php artisan db:seed

On this implementation I choose not to implement models/tables related to achievements or badges nor add new attributes to the User. In this sense, the only source of truth for counting the accomplishments are the registers on the comment and lesson_user tables.