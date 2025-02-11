<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Definir las columnas que se van a utilizar para la tabla de facturas
$aColumns = [
    'numero_factura',  // Número de factura
    'fecha_emision',   // Fecha de emisión
    'fecha_vencimiento', // Fecha de vencimiento
    'total',  // Total de la factura
    'estado',  // Estado de la factura
];

$sIndexColumn = 'id';  // Índice para la tabla (probablemente el ID de la factura)
$sTable       = db_prefix() . 'facturacion';  // Tabla de facturación

// No es necesario el JOIN si solo vamos a mostrar las columnas de la tabla 'facturacion'
$join = [];  // Sin uniones

// Obtener los datos de la tabla 'facturacion' utilizando DataTables
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, [], ['id']);

// var_dump($result);exit;

$output  = $result['output'];  // El array de salida para el DataTable
$rResult = $result['rResult'];  // Los resultados de la consulta

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];  // Obtener los datos de cada columna
        
        if ($aColumns[$i] == 'numero_factura') {
            $_data = '<a href="' . admin_url('facturacion/factura/' . $aRow['id']) . '" class="tw-font-medium">' . e($_data) . '</a>';
            $_data .= '<div class="row-options">';
            $_data .= '<a href="' . admin_url('facturacion/factura/' . $aRow['id']) . '">' . _l('view') . '</a>';

            if (staff_can('delete', 'facturacion')) {
                $_data .= ' | <a href="' . admin_url('facturacion/eliminar/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
            }
            $_data .= '</div>';
        } elseif ($aColumns[$i] == 'fecha_emision' || $aColumns[$i] == 'fecha_vencimiento') {
            $_data = e(_d($_data));  // Formatear las fechas
        } elseif ($aColumns[$i] == 'estado') {
            $_data = e($_data);  // Mostrar el estado de la factura
        } elseif ($aColumns[$i] == 'total') {
            $_data = format_currency($_data);  // Formatear el total como moneda
        }
        $row[] = $_data;  // Agregar el dato procesado a la fila
    }

    // Agregar el estado de la factura (por ejemplo, "pagada", "pendiente", etc.)
    ob_start();
    $estado = $aRow['estado'];  // Obtener el estado de la factura
    $estado_label = format_estado_factura($estado);  // Formatear el estado
    ?>
    <span class="estado-factura" style="color: <?php echo $estado_label['color']; ?>"><?php echo $estado_label['label']; ?></span>
    <?php
    $estado_factura = ob_get_contents();
    ob_end_clean();
    $row[] = $estado_factura;  // Agregar el estado a la fila

    // Agregar la clase de la fila para opciones
    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;  // Agregar la fila al array de salida
   
}

// Devolver los datos en formato JSON para que DataTables los consuma
// echo json_encode($output);

?>
