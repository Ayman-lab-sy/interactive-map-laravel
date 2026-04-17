<?php
namespace App\FormFields;

use TCG\Voyager\FormFields\AbstractHandler;

class IconSelector extends AbstractHandler {
    protected $codename = 'icon';

    public function createContent($row, $dataType, $dataTypeContent, $options) {
        return view('formfields.icon', [
            'row' => $row,
            'options' => $options,
            'dataType' => $dataType,
            'dataTypeContent' => $dataTypeContent
        ]);
     }
}
?>
