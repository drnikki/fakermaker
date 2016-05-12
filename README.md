# Cool!
FakerMaker is a tool that provides field-level control of dummy data generated for Drupal sites.  The dummy data is provided by [Faker][https://github.com/fzaninotto/Faker].


# todo
- have the configuration entity respond to the initial field-creation event
and steal the machine name then so that for every field that's created,
a corresponding config entry is created
- respond to deletion events also.
- turn composer methods into tokens

- add field-type settings form for patterns for fields without generation patterns.
- add drush command
 -- 