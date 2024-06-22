<?php
require '../vendor/autoload.php';
require '../config/conexion.php'; 

$objPHPExcel = new PHPExcel();


$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16)->setBold(true);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Lista de Ventas');


$fechaHoraLima = new DateTime('now', new DateTimeZone('America/Lima'));
$fechaHoraTexto = $fechaHoraLima->format('Y-m-d H:i:s');


$objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
$objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->setCellValue('A2', 'Fecha y Hora de Generación: ' . $fechaHoraTexto);


$objPHPExcel->getActiveSheet()->getStyle('A3:G3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A3:G3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9D9D9');
$objPHPExcel->getActiveSheet()->getStyle('A3:G3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A3', 'Fecha')
    ->setCellValue('B3', 'Cliente')
    ->setCellValue('C3', 'Usuario')
    ->setCellValue('D3', 'Documento')
    ->setCellValue('E3', 'Número')
    ->setCellValue('F3', 'Total Venta')
    ->setCellValue('G3', 'Estado');

$query = "SELECT DATE(v.fecha_hora) as fecha, p.nombre as cliente, u.nombre as usuario, v.tipo_comprobante, v.num_comprobante, v.total_venta, v.estado FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN usuario u ON v.idusuario=u.idusuario ORDER BY v.idventa DESC";
$resultado = ejecutarConsulta($query);

if ($resultado) {

    $fila = 4;
    while ($filaDatos = mysqli_fetch_assoc($resultado)) {
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $fila, $filaDatos['fecha'])
            ->setCellValue('B' . $fila, $filaDatos['cliente'])
            ->setCellValue('C' . $fila, $filaDatos['usuario'])
            ->setCellValue('D' . $fila, $filaDatos['tipo_comprobante'])
            ->setCellValue('E' . $fila, $filaDatos['num_comprobante'])
            ->setCellValue('F' . $fila, $filaDatos['total_venta'])
            ->setCellValue('G' . $fila, $filaDatos['estado']);

        $fila++;
    }

    $objPHPExcel->getActiveSheet()->getStyle('A3:G' . ($fila - 1))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    
    $objPHPExcel->getActiveSheet()->getStyle('F5:F' . ($fila - 1))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="ventas.xlsx"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

    $objWriter->save('php://output');
    exit;
} else {
    echo "Error en la consulta: " . mysqli_error($conexion);
}
?>
