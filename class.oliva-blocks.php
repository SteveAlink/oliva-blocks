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
    private $settingKey = 'oliva_blocks_data';

    public function __construct($Wcms)
    {
        $this->Wcms = $Wcms;
    }

    /* =========================
       SETTINGS (ADMIN)
    ========================= */

    public function handleSettings()
    {
        if (!$this->Wcms->loggedIn) {
            return;
        }

        if (isset($_POST['saveOlivaBlocks'])) {
            $this->Wcms->set('config', $this->settingKey, $_POST['oliva_blocks_json']);
        }

        $current = $this->Wcms->get('config', $this->settingKey) ?? '[]';

        echo '
        <h2>Oliva Blocks</h2>
        <p>Define your blocks (JSON format)</p>

        <form method="post">
            <textarea name="oliva_blocks_json" style="width:100%;height:300px;">' . htmlspecialchars($current) . '</textarea>
            <br><br>
            <button type="submit" name="saveOlivaBlocks">Save</button>
        </form>
        ';
    }

    /* =========================
       FRONTEND RENDERING
    ========================= */

    public function renderBlocks()
    {
        $json = $this->Wcms->get('config', $this->settingKey);

        if (!$json) {
            return;
        }

        $blocks = json_decode($json, true);

        if (!is_array($blocks)) {
            return;
        }

        echo '<div class="oliva-blocks">';

        foreach ($blocks as $block) {
            echo $this->renderBlock($block);
        }

        echo '</div>';
    }

    private function renderBlock($block)
    {
        if (!isset($block['type'])) {
            return '';
        }

        switch ($block['type']) {

            case 'text':
                return '
                <div class="ob-block ob-text">
                    <p>' . htmlspecialchars($block['text'] ?? '') . '</p>
                </div>';

            case 'image':
                return '
                <div class="ob-block ob-image">
                    <img src="' . htmlspecialchars($block['url'] ?? '') . '" alt="">
                </div>';

            case 'text_image':
                return '
                <div class="ob-block ob-text-image">
                    <div class="ob-text">' . htmlspecialchars($block['text'] ?? '') . '</div>
                    <div class="ob-image"><img src="' . htmlspecialchars($block['url'] ?? '') . '" alt=""></div>
                </div>';

            default:
                return '';
        }
    }

    /* =========================
       CSS
    ========================= */

    public function renderCss()
    {
        echo '<link rel="stylesheet" href="' . BASE_URL . '/plugins/oliva-blocks/css/style.css">';
    }
}
