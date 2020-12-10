<?php
namespace App\FormFields;

use TCG\Voyager\FormFields\AbstractHandler;

class OptionsFormField extends AbstractHandler
{
    protected $codename = 'options';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('vendor.voyager.formfields.options', [
            'row' => $row,
            'options' => $options,
            'dataType' => $dataType,
            'dataTypeContent' => $dataTypeContent
        ]);
    }
}
