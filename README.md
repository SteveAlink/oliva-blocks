# Oliva Blocks

By Steve Alink for Oliva Solutions.

Oliva Blocks gives the ability to add structured content blocks to a page, in WonderCMS, without touching HTML.

## Overview
This plugin helps you to organize blocks of text that will be use multiple times in various pages. Next to text  
this plugin is also able to support images that will be placed on various pages.

## Preview of the settings for this plugin:
WIP

## Download the plugin via
```text
https://raw.githubusercontent.com/SteveAlink/oliva-blocks/main/wcms-modules.json
```

## Versions
v0.3.0 04-05-2026 Rendering the blocks on the frontend  
v0.2.0 04-05-2026 Removal of JSON field. Replace by various type related fields  
v0.1.2 03-05-2026 Check if value passed missing, causing blank screen   
v0.1.1 03-05-2026 Something wrong with one of the programs causing blank screen  
v0.1.0 03-05-2026 Initial version

This first version is deliberately small:

- Backend tab: Oliva Blocks
- Option to have base of a block that later can be used on the front end

Version 0.1.1 Contains bug solution

- Previous version caused a blank screen to be shown in the frontend due to incorrect usage of statement Echo.

Version 0.1.2 Contains bug solution

- In previous version a new bug was entered, and didn't pass values where it should be. Blank screen shown on frontend.

Version 0.2.0 Other fields in Settings

- The tab Oliva Blocks now contain an option to select the type of content
- Where there used to be a field to enter, following a strict syntax, this has now been replaced

Version 0.3.0 Rendering blocks on frontend

- Whilst this is still a pre-version, all the available blocks will be rendered
