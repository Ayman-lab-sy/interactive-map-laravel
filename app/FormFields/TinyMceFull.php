<?php
namespace App\FormFields;

use TCG\Voyager\FormFields\AbstractHandler;

class TinyMceFull extends AbstractHandler {
    protected $codename = 'tiny mce full';

    public function createContent($row, $dataType, $dataTypeContent, $options) {
        return view('formfields.tinymce', [
            'row' => $row,
            'options' => $options,
            'dataType' => $dataType,
            'dataTypeContent' => $dataTypeContent
        ]);
     }
}
?>
