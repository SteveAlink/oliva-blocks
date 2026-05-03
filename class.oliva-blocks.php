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
            'nl' => 'nl_NL'
        ];
/*
            'es' => 'es_ES',
            'de' => 'de_DE',
            'fr' => 'fr_FR',
            'it' => 'it_IT'
*/
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
        $currentJson = $this->Wcms->get('config', 'oliva_blocks_data');
        if (!$currentJson) {
            $currentJson = "[]";
        }
        return $currentJson;
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
        $menuItemA->nodeValue = $this->t('olivaBlocks');

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
        $form->appendChild($this->createTextarea($doc, 'oliva_blocks_json', $this->getBlocksData()));

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

        if (isset($_POST['saveOlivaBlocksSettings'])) {
            $json = $_POST['oliva_blocks_json'] ?? '[]';
        
            json_decode($json, true);
        
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->Wcms->set('config', 'oliva_blocks_data', $json);
//                $this->Wcms->save();
            }
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
