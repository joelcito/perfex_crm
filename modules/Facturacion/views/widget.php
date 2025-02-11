<?php
$facturas = [];
if (is_staff_member()) {
    $this->load->model('facturacion/facturacion_model');
    $facturas = $this->facturacion_model->get_todas_las_facturas();
}
?>
<div class="widget<?php if (count($facturas) == 0 || !is_staff_member()) {
    echo ' hide';
} ?>" id="widget-<?php echo create_widget_id('facturas'); ?>">
    <?php if (is_staff_member()) { ?>
    <div class="row">
        <div class="col-md-12">
            <div class="panel_s">
                <div class="panel-body padding-10">
                    <div class="widget-dragger"></div>

                    <p class="tw-font-semibold tw-flex tw-items-center tw-mb-0 tw-space-x-1.5 rtl:tw-space-x-reverse tw-p-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="tw-w-6 tw-h-6 tw-text-neutral-500">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                        </svg>
                        <span class="tw-text-neutral-700">
                            <?php echo _l('facturas'); ?>
                        </span>
                    </p>

                    <hr class="-tw-mx-3 tw-mt-3 tw-mb-6">

                    <?php foreach ($facturas as $factura) { ?>
                    <div class="invoice tw-px-1 tw-pb-1">
                        <h4 class="pull-left font-medium no-mtop">
                            <?php echo e($factura['numero_factura']); ?>
                            <br />
                            <small><?php echo e($factura['cliente']); ?></small>
                        </h4>
                        <h4 class="pull-right bold no-mtop text-success text-right">
                            <?php echo $factura['monto_total']; ?>
                            <br />
                            <small><?php echo _l('monto_factura'); ?></small>
                        </h4>
                        <div class="clearfix"></div>
                        <div class="progress no-margin progress-bar-mini">
                            <div class="progress-bar progress-bar-danger no-percent-text not-dynamic" role="progressbar"
                                aria-valuenow="<?php echo $factura['estado_pago']; ?>" aria-valuemin="0"
                                aria-valuemax="100" style="width: <?php echo $factura['estado_pago']; ?>%">
                            </div>
                        </div>
                        <p class="text-muted pull-left mtop5"><?php echo _l('estado_pago'); ?></p>
                        <p class="text-muted pull-right mtop5"><?php echo $factura['estado_pago']; ?>%</p>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
