<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <?php if (staff_can('create', 'facturas')) { ?>
                <div class="tw-mb-2">
                    <a href="<?php echo admin_url('facturacion/factura'); ?>" class="btn btn-primary">
                        <i class="fa-regular fa-plus tw-mr-1"></i>
                        <?php echo _l('nueva_factura'); ?>
                    </a>
                </div>
                <?php } ?>
                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php
                        //  render_datatable([
                        // _l('asunto_factura'),
                        // _l('cliente'),
                        // _l('monto_total'),
                        // _l('monto_pagado'),
                        // _l('monto_pendiente'),
                        // _l('fecha_emision'),
                        // _l('fecha_vencimiento'),
                        // _l('estado_factura'),
                        // ], 'facturas'); 

                         render_datatable([
                        _l('numero_factura'),
                        _l('fecha_emision'),
                        _l('fecha_vencimiento'),
                        _l('total'),
                        _l('estado')
                        ], 'facturas'); 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
$(function() {

    initDataTable('.table-facturas', window.location.href, [4], [4]);
    $('.table-facturas').DataTable().on('draw', function() {
        var rows = $('.table-facturas').find('tr');

        console.log(rows);
        
        

        // $.each(rows, function() {
        //     var td = $(this).find('td').eq(4);  // Aquí se ajusta al índice correcto de tu columna
        //     var percent = $(td).find('input[name="percent"]').val();
        //     $(td).find('.factura-progress').circleProgress({
        //         value: percent,
        //         size: 45,
        //         animation: false,
        //         fill: {
        //             gradient: ["#28b8da", "#059DC1"]
        //         }
        //     });
        // });
    });
});
</script>
</body>
</html>
