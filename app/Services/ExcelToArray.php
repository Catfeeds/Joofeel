<?php
namespace App\Services;

use App\Exceptions\AppException;
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
            require  '../vendor/PHPExcel/PHPExcel/Reader/Excel5.php';
            $objReader = PHPExcel_IOFactory::createReader('Excel5');

        }elseif(strtolower ( $file_type )=='xlsx')
        {
            require  '../vendor/PHPExcel/PHPExcel/Reader/Excel2007.php';
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

    /**
     * @return array
     * @throws AppException
     * 得到Excel数据
     */
    public function getExcel()
    {
        if (!empty ($_FILES ['file'] ['name'])) {
            $tmp_file = $_FILES ['file'] ['tmp_name'];
            $file_types = explode(".", $_FILES ['file'] ['name']);
            $file_type = $file_types [count($file_types) - 1];
            if (strtolower($file_type) != "xlsx") {
                throw new AppException('不是Excel文件，重新上传');
            }
            $savePath = base_path('public/uploads/');
            $str = date('Ymdhis');
            $file_name = $str . "." . $file_type;
            if (!copy($tmp_file, $savePath . $file_name)) {
                throw new AppException('上传失败');
            }
            $res = $this->read($savePath . $file_name, "UTF-8", $file_type);//传参,判断office2007还是office2003
            //删除本地Excel
            unlink(base_path('public/uploads/' . $file_name));
            return $res;
        }
        throw new AppException('请上传文件');
    }

    /**
     * @param $data
     * @param $name
     * 获取订单的Excel
     */
    public function orderExcel($data,$name)
    {
        $excel = new \PHPExcel(); //引用phpexcel
        iconv('UTF-8', 'gb2312', $name); //针对中文名转码
        $header = ['订单号', '快递单号', '支付价格', '收货人', '收货地址','联系方式','状态']; //表头,名称可自定义
        $excel->setActiveSheetIndex(0);
        $excel->getActiveSheet()->setTitle($name); //设置表名
        $excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(18);
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(14);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(60);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(18);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        $letter = ['A', 'B', 'C', 'D', 'E','F','G'];//列坐标
        for ($i = 0; $i < count($header); $i++) {
            //设置表头值
            $excel->getActiveSheet()->setCellValue("$letter[$i]1", $header[$i]);
            //设置表头字体样式
            $excel->getActiveSheet()->getStyle("$letter[$i]1")->getFont()->setName('宋体');
            //设置表头字体大小
            $excel->getActiveSheet()->getStyle("$letter[$i]1")->getFont()->setSize(14);
            //设置表头字体是否加粗
            $excel->getActiveSheet()->getStyle("$letter[$i]1")->getFont()->setBold(true);
            //设置表头文字水平居中
            $excel->getActiveSheet()->getStyle("$letter[$i]1")->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //设置文字上下居中
            $excel->getActiveSheet()->getStyle($letter[$i])->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            //设置单元格背景色
            $excel->getActiveSheet()->getStyle("$letter[$i]1")->getFill()->getStartColor()->setARGB('FFFFFFFF');
            $excel->getActiveSheet()->getStyle("$letter[$i]1")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
            $excel->getActiveSheet()->getStyle("$letter[$i]1")->getFill()->getStartColor()->setARGB('FF6DBA43');
            //设置字体颜色
            $excel->getActiveSheet()->getStyle("$letter[$i]1")->getFont()->getColor()->setARGB('FFFFFFFF');
        }
        //写入数据
        foreach ($data as $k => $v) {
            //从第二行开始写入数据（第一行为表头）
            $excel->getActiveSheet()->setCellValue('A' . ($k + 2), $v['order_id']);
            $excel->getActiveSheet()->setCellValue('B' . ($k + 2), $v['tracking_id']);
            $excel->getActiveSheet()->setCellValue('C' . ($k + 2), $v['sale_price']);
            $excel->getActiveSheet()->setCellValue('D' . ($k + 2), $v['receipt_name']);
            $excel->getActiveSheet()->setCellValue('E' . ($k + 2), $v['receipt_address']);
            $excel->getActiveSheet()->setCellValue('F' . ($k + 2), $v['receipt_phone']);
            $excel->getActiveSheet()->setCellValue('G' . ($k + 2), $v['sign']);
        }
        $excel->getActiveSheet()->getStyle("A1:G" . (count($data) + 1))
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
        ob_end_clean();
        header('Content-Type: application/vnd.ms-excel charset=utf-8');
        header('Content-Disposition: attachment;filename="' . $name . '.xlsx"');
        header('Cache-Control: max-age=0');
        $res_excel = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $res_excel->save('php://output');
    }

}