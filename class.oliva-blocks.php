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

        // Selection of type of block
        $typeLabel = $doc->createElement('label', $this->t('blockType'));
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

        // The code connected to this block
        $codeLabel = $doc->createElement('label', $this->t('blockCode'));
        $form->appendChild($codeLabel);
        
        $codeInput = $doc->createElement('input');
        $codeInput->setAttribute('type', 'text');
        $codeInput->setAttribute('name', 'oliva_block_code');
        $codeInput->setAttribute('class', 'form-control');
        $form->appendChild($codeInput);

        $codeHelp = $doc->createElement('p', $this->t('helpBlockCode'));
        $codeHelp->setAttribute('class', 'small text-muted');
        $form->appendChild($codeHelp);

        // Content of Text field
        $textLabel = $doc->createElement('label', $this->t('blockText'));
        $form->appendChild($textLabel);
        
        $textInput = $doc->createElement('input');
        $textInput->setAttribute('type', 'text');
        $textInput->setAttribute('name', 'oliva_block_text');
        $textInput->setAttribute('class', 'form-control');
        $form->appendChild($textInput);
        
        // URL field
        $urlLabel = $doc->createElement('label', $this->t('blockURL'));
        $form->appendChild($urlLabel);
        
        $urlInput = $doc->createElement('input');
        $urlInput->setAttribute('type', 'text');
        $urlInput->setAttribute('name', 'oliva_block_url');
        $urlInput->setAttribute('class', 'form-control');
        $form->appendChild($urlInput);

        $urlHelp = $doc->createElement('p', $this->t('helpBlockUrl'));
        $urlHelp->setAttribute('class', 'small text-muted');
        $form->appendChild($urlHelp);

        $saveButton = $doc->createElement('button');
        $saveButton->setAttribute('type', 'submit');
        $saveButton->setAttribute('name', 'saveOlivaBlocksSettings');
        $saveButton->setAttribute('class', 'btn btn-primary');
        $saveButton->nodeValue = $this->t('saveButton');

        $form->appendChild($saveButton);

        // Spacing
        $form->appendChild($doc->createElement('br'));
        $blocks = $this->getBlocksArray();

        // Check if we want to see the save button already
        $blockCount = is_array($blocks) ? count($blocks) : 0;
        if ($blockCount > 5) {
            $form->appendChild($saveButton->cloneNode(true));
            $form->appendChild($doc->createElement('br'));
        }

        if (!empty($blocks)) {
            foreach ($blocks as $index => $block) {
                $div = $doc->createElement('div');
                $div->setAttribute('style', 'border:1px solid #ccc;padding:10px;margin-bottom:10px;');
        
                $text = 'Code: ' . ($block['code'] ?? '') . ' | Type: ' . ($block['type'] ?? '');
                if (!empty($block['text'])) {
                    $text .= ' | Text: ' . $block['text'];
                }
                if (!empty($block['url'])) {
                    $text .= ' | URL: ' . $block['url'];
                }
        
                $div->appendChild($doc->createTextNode($text));

                // Include move buttons
                if ($index > 0) {
                    $upBtn = $doc->createElement('button', $this->t('moveUpButton'));
                    $upBtn->setAttribute('type', 'submit');
                    $upBtn->setAttribute('name', 'move_block_up');
                    $upBtn->setAttribute('value', $index);
                    $upBtn->setAttribute('class', 'btn btn-secondary btn-sm');
                    $upBtn->setAttribute('style', 'margin-left:10px;');
                    $div->appendChild($upBtn);
                }
                
                if ($index < count($blocks) - 1) {
                    $downBtn = $doc->createElement('button', $this->t('moveDownButton'));
                    $downBtn->setAttribute('type', 'submit');
                    $downBtn->setAttribute('name', 'move_block_down');
                    $downBtn->setAttribute('value', $index);
                    $downBtn->setAttribute('class', 'btn btn-secondary btn-sm');
                    $downBtn->setAttribute('style', 'margin-left:10px;');
                    $div->appendChild($downBtn);
                }

                // Delete button
                $deleteBtn = $doc->createElement('button', $this->t('deleteButton'));
                $deleteBtn->setAttribute('type', 'submit');
                $deleteBtn->setAttribute('name', 'delete_block');
                $deleteBtn->setAttribute('value', $index);
                $deleteBtn->setAttribute('class', 'btn btn-danger btn-sm');
                $deleteBtn->setAttribute('style', 'margin-left:10px;');
        
                $div->appendChild($deleteBtn);
        
                $form->appendChild($div);
            }
        }

        // Another save button to allow scrolling before saving the content of the fields
        $form->appendChild($doc->createElement('br'));
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

        // Handle the move buttons
        if (isset($_POST['move_block_up'])) {
            $index = (int) $_POST['move_block_up'];
        
            if ($index > 0 && isset($blocks[$index], $blocks[$index - 1])) {
                $temp = $blocks[$index - 1];
                $blocks[$index - 1] = $blocks[$index];
                $blocks[$index] = $temp;
        
                $this->saveBlocksArray($blocks);
            }
        }
        
        if (isset($_POST['move_block_down'])) {
            $index = (int) $_POST['move_block_down'];
        
            if ($index < count($blocks) - 1 && isset($blocks[$index], $blocks[$index + 1])) {
                $temp = $blocks[$index + 1];
                $blocks[$index + 1] = $blocks[$index];
                $blocks[$index] = $temp;
        
                $this->saveBlocksArray($blocks);
            }
        }

        // ADD
        if (isset($_POST['saveOlivaBlocksSettings'])) {
            $type = $_POST['oliva_block_type'] ?? 'text';
            $code = trim($_POST['oliva_block_code'] ?? '');
            $text = $_POST['oliva_block_text'] ?? '';
            $url  = $_POST['oliva_block_url'] ?? '';
            if ($type === 'text' && trim($text) === '') {
                $this->Wcms->alert('danger', $this->t('errorTextNoText'));
                return $args;
            }
            
            if ($type === 'image' && trim($url) === '') {
                $this->Wcms->alert('danger', $this->t('errorImageNoUrl'));
                return $args;
            }
        
            $blocks[] = [
                'code' => $code,
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
        if (!isset($args[0])) {
            return $args;
        }
    
        if (strpos($args[0], '{{OlivaBlocks') === false) {
            return $args;
        }
    
        $args[0] = preg_replace_callback(
            '/\{\{OlivaBlocks(?:\|([^}]+))?\}\}/',
            function ($matches) {
                $code = isset($matches[1]) ? trim($matches[1]) : '';
    
                return $this->renderBlocksHtml($code);
            },
            $args[0]
        );
    
        return $args;
    }

    private function renderBlocksHtml($code = '')
    {
        $blocks = $this->getBlocksArray();
    
        if (empty($blocks)) {
            return '';
        }
    
        $html = '<div class="oliva-blocks">';
    
        foreach ($blocks as $block) {
            $blockCode = trim($block['code'] ?? '');
    
            if ($code !== '' && $blockCode !== $code) {
                continue;
            }
    
            $html .= $this->renderSingleBlock($block);
        }
    
        $html .= '</div>';

        // If nothing to show, don't wast space on a page
        if ($html === '<div class="oliva-blocks"></div>') {
            return '';
        }
    
        return $html;
    }

    private function renderSingleBlock($block)
    {
        $type = $block['type'] ?? '';
        $text = htmlspecialchars($block['text'] ?? '', ENT_QUOTES, 'UTF-8');
        $url  = htmlspecialchars($block['url'] ?? '', ENT_QUOTES, 'UTF-8');
        $url = filter_var($url, FILTER_SANITIZE_URL);
    
        if ($type === 'text') {
            return '
                <section class="oliva-block oliva-block-text">
                    <div class="oliva-block-content">
                        ' . nl2br($text) . '
                    </div>
                </section>
            ';
        }
    
        if ($type === 'image') {
            if (!$url) {
                return '';
            }
    
            return '
                <section class="oliva-block oliva-block-image">
                    <img src="' . $url . '" alt="">
                </section>
            ';
        }
    
        if ($type === 'text_image') {
            return '
                <section class="oliva-block oliva-block-text-image">
                    <div class="oliva-block-text-column">
                        ' . nl2br($text) . '
                    </div>
                    <div class="oliva-block-image-column">
                        ' . ($url ? '<img src="' . $url . '" alt="">' : '') . '
                    </div>
                </section>
            ';
        }
    
        return '';
    }


    function olivaBlocksPluginBasePath()
    {
        return 'plugins/oliva-blocks/';
    }

    public function renderCss($args)
    {
        $css = '<link rel="stylesheet" href="' . $this->olivaBlocksPluginBasePath() . 'css/style.css" type="text/css">';

        if (isset($args[0]) && is_array($args[0])) {
            $args[0][] = $css;
        } else {
            $args[0] = ($args[0] ?? '') . $css;
        }

        return $args;
    }

    public function renderJs($args)
    {
        return $args;
    }
}
