<?php
/**
 * oliva-blocks - Blocks plugin for WonderCMS.
 * Prepared by Steve Alink for Oliva Solutions
 *
 * A block of text, image or a combination of this that can be used multiple times.
 */

class OlivaBlocks
{
    private $Wcms;
    private $translations = [];

    public function __construct($Wcms)
    {
        $this->Wcms = $Wcms;
        $this->loadTranslations();
    }

    private function loadTranslations()
    {
        $adminLang = $this->Wcms->get('config', 'adminLang');

        $map = [
            'en' => 'en_US',
            'nl' => 'nl_NL',
            'es' => 'es_ES',
            'de' => 'de_DE',
            'fr' => 'fr_FR',
            'it' => 'it_IT'
        ];

        $langCode = $map[$adminLang] ?? 'en_US';
        $file = __DIR__ . '/languages/' . $langCode . '.ini';

        if (file_exists($file)) {
            $this->translations = parse_ini_file($file);
        } else {
            $this->translations = parse_ini_file(__DIR__ . '/languages/en_US.ini');
        }
    }

    private function t($key)
    {
        return $this->translations[$key] ?? '[[' . $key . ']]';
    }

    private function getBlocksData()
    {
        $currentJson = $this->Wcms->get('config', 'olivaBlocksData');
        if (!$currentJson) {
            $currentJson = "[]";
        }
        return $currentJson;
    }

    private function getBlocksArray()
    {
        $json = $this->getBlocksData();
        $data = json_decode($json, true);
    
        return is_array($data) ? $data : [];
    }
    
    private function saveBlocksArray($blocks)
    {
        $this->Wcms->set('config', 'olivaBlocksData', json_encode($blocks));
    }

    private function createTextarea($doc, $name, $value, $rows = 6)
    {
        $textarea = $doc->createElement('textarea');
        $textarea->setAttribute('name', $name);
        $textarea->setAttribute('class', 'form-control');
        $textarea->setAttribute('rows', $rows);
        $textarea->nodeValue = $value;

        return $textarea;
    }

    public function alterAdmin(array $args): array
    {

        $doc = new DOMDocument();
        @$doc->loadHTML(mb_convert_encoding($args[0], 'HTML-ENTITIES', 'UTF-8'));

        $currentPage = $doc->getElementById('currentPage');

        if (!$currentPage) {
            return $args;
        }

        $menuList = $currentPage
            ->parentNode
            ->parentNode
            ->childNodes
            ->item(1);

        $menuItem = $doc->createElement('li');
        $menuItem->setAttribute('class', 'nav-item');

        $menuItemA = $doc->createElement('a');
        $menuItemA->setAttribute('href', '#olivaBlocksSettings');
        $menuItemA->setAttribute('aria-controls', 'olivaBlocksSettings');
        $menuItemA->setAttribute('role', 'tab');
        $menuItemA->setAttribute('data-toggle', 'tab');
        $menuItemA->setAttribute('class', 'nav-link');
        $menuItemA->nodeValue = $this->t('OlivaBlocks');

        $menuItem->appendChild($menuItemA);
        $menuList->appendChild($menuItem);

        $wrapper = $doc->createElement('div');
        $wrapper->setAttribute('role', 'tabpanel');
        $wrapper->setAttribute('class', 'tab-pane');
        $wrapper->setAttribute('id', 'olivaBlocksSettings');

        $form = $doc->createElement('form');
        $form->setAttribute('method', 'post');
        $form->setAttribute('action', '');

        $title = $doc->createElement('h2', $this->t('headingBlocksSettings'));
        $form->appendChild($title);

        // Prepare a field to hold the json code
//        $form->appendChild($this->createTextarea($doc, 'oliva_blocks_json', $this->getBlocksData(), 12));
        // Block type
        $typeLabel = $doc->createElement('label', 'Block type');
        $form->appendChild($typeLabel);
        
        $typeSelect = $doc->createElement('select');
        $typeSelect->setAttribute('name', 'oliva_block_type');
        $typeSelect->setAttribute('class', 'form-control');
        
        foreach (['text', 'image', 'text_image'] as $type) {
            $option = $doc->createElement('option', $type);
            $option->setAttribute('value', $type);
            $typeSelect->appendChild($option);
        }
        
        $form->appendChild($typeSelect);
        
        // Text field
        $textLabel = $doc->createElement('label', 'Text');
        $form->appendChild($textLabel);
        
        $textInput = $doc->createElement('input');
        $textInput->setAttribute('type', 'text');
        $textInput->setAttribute('name', 'oliva_block_text');
        $textInput->setAttribute('class', 'form-control');
        $form->appendChild($textInput);
        
        // URL field
        $urlLabel = $doc->createElement('label', 'Image URL');
        $form->appendChild($urlLabel);
        
        $urlInput = $doc->createElement('input');
        $urlInput->setAttribute('type', 'text');
        $urlInput->setAttribute('name', 'oliva_block_url');
        $urlInput->setAttribute('class', 'form-control');
        $form->appendChild($urlInput);
        
        // spacing
        $form->appendChild($doc->createElement('br'));
        $blocks = $this->getBlocksArray();
        
        if (!empty($blocks)) {
            foreach ($blocks as $index => $block) {
                $div = $doc->createElement('div');
                $div->setAttribute('style', 'border:1px solid #ccc;padding:10px;margin-bottom:10px;');
        
                $text = 'Type: ' . ($block['type'] ?? '');
                if (!empty($block['text'])) {
                    $text .= ' | Text: ' . $block['text'];
                }
                if (!empty($block['url'])) {
                    $text .= ' | URL: ' . $block['url'];
                }
        
                $div->appendChild($doc->createTextNode($text));
        
                // delete button
                $deleteBtn = $doc->createElement('button', 'Delete');
                $deleteBtn->setAttribute('type', 'submit');
                $deleteBtn->setAttribute('name', 'delete_block');
                $deleteBtn->setAttribute('value', $index);
                $deleteBtn->setAttribute('class', 'btn btn-danger btn-sm');
                $deleteBtn->setAttribute('style', 'margin-left:10px;');
        
                $div->appendChild($deleteBtn);
        
                $form->appendChild($div);
            }
        }
        $saveButton = $doc->createElement('button');
        $saveButton->setAttribute('type', 'submit');
        $saveButton->setAttribute('name', 'saveOlivaBlocksSettings');
        $saveButton->setAttribute('class', 'btn btn-primary');
        $saveButton->nodeValue = $this->t('saveButton');

        $form->appendChild($saveButton);

        $wrapper->appendChild($form);
        $currentPage->parentNode->appendChild($wrapper);

        $args[0] = preg_replace(
            '~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i',
            '',
            $doc->saveHTML()
        );

        return $args;
    }

    public function handleSettings(array $args): array
    {
        if (!$this->Wcms->loggedIn) {
            return $args;
        }

        $blocks = $this->getBlocksArray();
        
        // DELETE
        if (isset($_POST['delete_block'])) {
            $index = (int)$_POST['delete_block'];
            unset($blocks[$index]);
            $blocks = array_values($blocks);
            $this->saveBlocksArray($blocks);
        }
        
        // ADD
        if (isset($_POST['saveOlivaBlocksSettings'])) {
        
            $type = $_POST['oliva_block_type'] ?? 'text';
            $text = $_POST['oliva_block_text'] ?? '';
            $url  = $_POST['oliva_block_url'] ?? '';
        
            $blocks[] = [
                'type' => $type,
                'text' => $text,
                'url'  => $url
            ];
        
            $this->saveBlocksArray($blocks);
        }

        return $this->alterAdmin($args);
    }

    public function renderBlocks($args)
    {
        return $args;
    }

    public function renderCss($args)
    {
        return $args;
    }

    public function renderJs($args)
    {
        return $args;
    }
}
