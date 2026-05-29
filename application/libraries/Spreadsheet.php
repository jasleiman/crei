<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH."/third_party/phpspreadsheet/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

private $sp;
private $wt;


class Spreadsheet extends Spreadsheet {
  $this->sp = new Spreadsheet();
  $sheet = $spreadsheet->getActiveSheet();
  $sheet->setCellValue('A1', 'Hello World !');

  $writer = new Xlsx($spreadsheet);
  $writer->save('hello world.xlsx');
}
