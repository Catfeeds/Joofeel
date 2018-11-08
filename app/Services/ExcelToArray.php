<?php
namespace App\Services;

use PHPExcel_IOFactory;
use PHPExcel_Cell;
use PHPExcel;
/**
 * 读取Excel数据
 */

class ExcelToArray
{
    public function __construct() {
        //这些文件需要下载phpexcel，然后放在vendor文件里面。具体参考上一篇数据导出。
        require  '../vendor/PHPExcel/PHPExcel.php';
        require  '../vendor/PHPExcel/PHPExcel/Writer/IWriter.php';
        require  '../vendor/PHPExcel/PHPExcel/Writer/Abstract.php';
        require  '../vendor/PHPExcel/PHPExcel/Writer/Excel5.php';
        require  '../vendor/PHPExcel/PHPExcel/Writer/Excel2007.php';
        require  '../vendor/PHPExcel/PHPExcel/IOFactory.php';
    }

    public function read($filename,$encode,$file_type){
        if(strtolower ( $file_type )=='xls')//判断excel表类型为2003还是2007
        {
            require  '../extend/PHPExcel/PHPExcel/Reader/Excel5.php';
            $objReader = PHPExcel_IOFactory::createReader('Excel5');
            $objReader = PHPExcel_IOFactory::createReader('Excel5');

        }elseif(strtolower ( $file_type )=='xlsx')
        {
            require  '../extend/PHPExcel/PHPExcel/Reader/Excel2007.php';
            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        }
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($filename);
        $objWorksheet = $objPHPExcel->getActiveSheet();
        $highestRow = $objWorksheet->getHighestRow();
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
        $excelData = array();
        for ($row = 1; $row <= $highestRow; $row++) {
            for ($col = 0; $col < $highestColumnIndex; $col++) {
                $excelData[$row][] =(string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
            }
        }
        return $excelData;
    }
}