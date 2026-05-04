# Oliva Blocks

By Steve Alink for Oliva Solutions.

Oliva Blocks gives the ability to add structured content blocks to a page, in WonderCMS, without touching HTML.

## Overview
This plugin helps you to organize blocks of text that will be use multiple times in various pages. Next to text  
this plugin is also able to support images that will be placed on various pages.  
Use ```{{OlivaBlocks}}``` on any page to render the blocks. This will show all blocks on the page.  
Use ```{{OlivaBlocks|Queens}}``` to show only the blocks of information that have as block code 'Queens'.  
Be aware that the block code is case sensitive, so Queens is a different code from queens.  
You can leave the block code blank.  
Reordering of a block is possible using the indicated buttons.  

## Note
If the placeholder ```{{OlivaBlocks|...}}``` is used, the block information will be inserted in the page. So it replaces the placeholder.

## Preview of the settings for this plugin:
<img width="2003" height="1651" alt="WcmsOlivaBlocksPreviewSettings063" src="https://github.com/user-attachments/assets/97c4f159-ff94-42a6-8120-b9be46a60398" />

## Download the plugin via
```text
https://raw.githubusercontent.com/SteveAlink/oliva-blocks/main/wcms-modules.json
```

## Versions
v0.6.3 04-05-2026 Forgotten to include translation on Image URL  
v0.6.2 04-05-2026 Inluded more translations  
v0.6.1 04-05-2026 Missing ) in the code caused a fatal error  
v0.6.0 04-05-2026 Included more language for backend: DE, FR, IT, ES. Reordering of information  
v0.5.0 04-05-2026 Allow selection of blocks using a code  
v0.4.0 04-05-2026 Rendering on frontend can now use ```{{OlivaBlocks}}``` in any page. So moved out of footer  
v0.3.0 04-05-2026 Rendering the blocks on the frontend on footer  
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

Version 0.4.0 Rendering in any page on frontend

- In previous versions, the rendering was done to the footer of a page, now it can be put on any page

Version 0.5.0 Includes a code to connect to blocks of information

- Per block type one is now able to enter a code
- Using ```{{OlivaBlocks|code}}``` on a page will only show the blocks that have the include code as Block code.
- Translations have been extended

Version 0.6.0 Translations and validations

- Four languages are now included for the backend  
- Validation build in to check if correct fields are filled
- New Up and Down button to move blocks in correct sequence

Version 0.6.1 Missing parenthesis caused a HTTP error 500 to show

Version 0.6.2 Included more translations

Version 0.6.3 Included one more translation
