<?php
/**
 * Created by PhpStorm.
 * User: edjgf
 * Date: 2018/3/22
 * Time: 9:16
 */

/**
 * $filename = 'goods';
 * $name = '商品';
 * $title = array('ID','商品分类','商品名称','商品卖价','商品佣金','属性');
 * $data = array(array('ID','商品分类','商品名称','商品卖价','商品佣金','属性'),array('ID','商品分类','商品名称','商品卖价','商品佣金','属性'));
 * $is_cell 是否合并单元格，如果需要合并需要填写表格整个列数
 * $height 行高
 * $width 列宽度 单个数字则是所有列都为统一宽度 可以为数组 array(10,20,30,40) 则表示4列的宽度
 **/
//导出excel数据
function Eexcel($filename, $name, $data, $is_cell = 0, $height = 20, $width = 30, $align = 'left')
{
    vendor("PHPExcel.PHPExcel");
    error_reporting(E_ALL);
//        date_default_timezone_set('Europe/London');

    $objPHPExcel = new \PHPExcel();

    /*以下是一些设置 ，什么作者  标题啊之类的*/

    $objPHPExcel->getProperties()->setCreator($name)
        ->setLastModifiedBy($name)
        ->setTitle("数据EXCEL导出")
        ->setSubject("数据EXCEL导出")
        ->setDescription("备份数据")
        ->setKeywords("excel")
        ->setCategory("result file");
    /*以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改*/
    $az = array(
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
        'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ',
    );
    $num = 1;
    $PEASI = $objPHPExcel->setActiveSheetIndex(0);


    foreach ($data as $ks => $v) {
        $is_img_true = 0;
        foreach ($v as $k => $vs) {
            $azkey = $az[$k] . $num;
            $ext = strrchr($vs, '.');

            $imgextarr = array('.jpg', '.png', '.gif');
            if ($num == 1) {
                $PEASI->getRowDimension($num)->setRowHeight(20);
            } else {
                if (in_array($ext, $imgextarr)) {
                    $is_img_true++;
                }
            }
            $PEASI->getStyle($azkey)->getAlignment()->setWrapText(TRUE);
            if ($is_img_true > 0) {
                $PEASI->getRowDimension($num)->setRowHeight(80);
            } else {
                $PEASI->getRowDimension($num)->setRowHeight($height);
            }
            switch ($align) {
                case 'left':
                    $palign = \PHPExcel_Style_Alignment::HORIZONTAL_LEFT;
                    break;
                case 'right':
                    $palign = \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT;
                    break;
                case 'center':
                    $palign = \PHPExcel_Style_Alignment::VERTICAL_CENTER;
                    break;

            }
            //左靠齐
            $PEASI->getStyle($azkey)->getAlignment()->setHorizontal($palign);
            //上下居中
            $PEASI->getStyle($azkey)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            if ($is_cell > 0) {
                if ($vs == 'ende') {
                    //如果值为空
                    if ($vs[$k - 1]) {
                        $cellsstart = $az[$k - 1] . $num;
                    } else {
                        $cellsstart = $az[$k] . $num;
                    }
                    $cellsend = $az[$is_cell - 1] . $num;
                    $cells = $cellsstart . ':' . $cellsend;
                    //$PEASI->setCellValue($azkey, $vs);
                    $PEASI->mergeCells($cells);
                    continue;
                }
            }

            if (is_array($width)) {
                $PEASI->getColumnDimension($az[$k])->setWidth($width[$k]);
            } else {
                $PEASI->getColumnDimension($az[$k])->setWidth($width);
            }
            if (in_array($ext, $imgextarr)) {
                $objDrawing[$k] = new \PHPExcel_Worksheet_Drawing();
                $objDrawing[$k]->setPath('.' . $vs);
                $objDrawing[$k]->setWidth(80);//照片宽度
                $objDrawing[$k]->setHeight(80);//照片高度
                $objDrawing[$k]->setCoordinates($azkey);
                $objDrawing[$k]->setOffsetX(12);
                $objDrawing[$k]->setOffsetY(12);
                $objDrawing[$k]->setWorksheet($objPHPExcel->getActiveSheet());
            } else {
                $PEASI->setCellValue($azkey, $vs);
            }
        }
        $num++;
    }

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
    header('Cache-Control: max-age=0');
    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
}