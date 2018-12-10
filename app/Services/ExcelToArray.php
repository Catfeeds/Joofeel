<?php
namespace App\Services;

use App\Exceptions\AppException;
use PHPExcel_IOFactory;
use PHPExcel_Cell;

/**
 * 读取Excel数据
 */


class ExcelToArray
{
    public function __construct()
    {
        //这些文件需要下载phpexcel，然后放在vendor文件里面。具体参考上一篇数据导出。
        require '../vendor/PHPExcel/PHPExcel.php';
        require '../vendor/PHPExcel/PHPExcel/Writer/IWriter.php';
        require '../vendor/PHPExcel/PHPExcel/Writer/Abstract.php';
        require '../vendor/PHPExcel/PHPExcel/Writer/Excel5.php';
        require '../vendor/PHPExcel/PHPExcel/Writer/Excel2007.php';
        require '../vendor/PHPExcel/PHPExcel/IOFactory.php';
    }

    public function read($filename, $encode, $file_type)
    {
        if (strtolower($file_type) == 'xls')//判断excel表类型为2003还是2007
        {
            require '../vendor/PHPExcel/PHPExcel/Reader/Excel5.php';
            $objReader = PHPExcel_IOFactory::createReader('Excel5');

        } elseif (strtolower($file_type) == 'xlsx') {
            require '../vendor/PHPExcel/PHPExcel/Reader/Excel2007.php';
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
                $excelData[$row][] = (string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
            }
        }
        return $excelData;
    }

    /**
     * @return array
     * @throws AppException
     * 得到Excel数据
     */
    public function get()
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
     * @param $name
     * @param $data
     * @param $header
     * @param string $info
     * @param string $info_header
     */
    private function out($name,$data,$header,$info = '',$info_header = '')
    {
        $excel = new \PHPExcel();
        iconv('UTF-8', 'gb2312', $name); //针对中文名转码
        $excel->setActiveSheetIndex(0);
        $excel->getActiveSheet()->setTitle($name); //设置表名
        $excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(18);

        $letter = [];
        //写入数据
        foreach ($data as $k => $v) {
            $coordinate= 'A';
            for($i=0;$i<count($header);$i++)
            {
                $excel->getActiveSheet()->setCellValue($coordinate . ($k + 2), $v[$i]);
                $coordinate =  chr(ord($coordinate) +1);
            }
        }
        if($info)
        {
            foreach ($data as $k => $v)
            {
                $info_coordinate = chr(ord('A') + count($header));
                for($i=0;$i<count($info[$k]);$i++)
                {
                    for ($j=0;$j<count($info[$k][$i]);$j++)
                    {
                        $excel->getActiveSheet()->setCellValue($info_coordinate . ($k + 2), $info[$k][$i][$j]);
                        $info_coordinate = chr(ord($info_coordinate) +1);
                    }
                }
            }
        }
        $head_coordinate = 'A';
        for($i=0;$i<count($header);$i++)
        {
            $letter[$i] = $head_coordinate;
            $excel->getActiveSheet()->getColumnDimension($head_coordinate)->setWidth(25);
            $head_coordinate =  chr(ord($head_coordinate) +1);
        }


        for ($i = 0; $i < count($header); $i++) {
            $excel->getActiveSheet()->setCellValue("$letter[$i]1", $header[$i]);
            $excel->getActiveSheet()->getStyle("$letter[$i]1")->getFont()->setName('宋体');
            $excel->getActiveSheet()->getStyle("$letter[$i]1")->getFont()->setSize(14);
            $excel->getActiveSheet()->getStyle("$letter[$i]1")->getFont()->setBold(true);
            $excel->getActiveSheet()->getStyle("$letter[$i]1")->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $excel->getActiveSheet()->getStyle($letter[$i])->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle("$letter[$i]1")->getFill()->getStartColor()->setARGB('FFFFFFFF');
            $excel->getActiveSheet()->getStyle("$letter[$i]1")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
            $excel->getActiveSheet()->getStyle("$letter[$i]1")->getFill()->getStartColor()->setARGB('FFC901');
            $excel->getActiveSheet()->getStyle("$letter[$i]1")->getFont()->getColor()->setARGB('FFFFFFFF');
        }
        $excel->getActiveSheet()->getStyle("A1:I" . (count($data) + 1))
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

    /**
     * @param $data
     * @param $name
     * @param $info
     * 获取订单
     */
    public function order($data, $name,$info)
    {
        $array = [];
        $header = ['订单号', '快递单号','快递公司', '支付价格', '收货人', '收货地址','联系方式','状态','下单时间'];
        foreach ($data as $key => $item)
        {
            $array[$key][0] = $item['order_id'];
            $array[$key][1] = $item['tracking_id'];
            $array[$key][2] = $item['tracking_company'];
            $array[$key][3] = $item['sale_price'];
            $array[$key][4] = $item['receipt_name'];
            $array[$key][5] = $item['receipt_address'];
            $array[$key][6] = $item['receipt_phone'];
            $array[$key][7] = $item['isSign'];
            $array[$key][8] = $item['created_at'];
        }
        $info_header = ['商品名','数量'];
        return $this->out($name,$array,$header,$info,$info_header);
    }
}