# Cool!
FakerMaker is a tool that provides field-level control of dummy data generated for Drupal sites.  The dummy data is provided by [Faker][https://github.com/fzaninotto/Faker].



# todo
## Tokens
- turn Faker methods into tokens
- implement the 'browse available tokens' front-end thing on the settings page
- add token validate to the saving

## Content Type Specific Settings UI
- break content type settings form down by content type
- add validation to match the pattern to the fieldtype (looooosely, this is a developer tool for now)
(should this be a derivitive of the node edit form? seems probably simpler)

## Generic Settings UI
- add field-type settings form for patterns for fields without generation patterns.

## Field concerns
- need special handling for reused  fields / prepend with content type name is probably simplest.
- need to validate the patterns/dummy text used on the settings page
