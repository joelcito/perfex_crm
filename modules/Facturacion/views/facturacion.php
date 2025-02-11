<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-<?php echo !isset($factura) ? '8 col-md-offset-2' : 6; ?>">
                <h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700"><?php echo e($title); ?></h4>
                <?php echo form_open($this->uri->uri_string()); ?>
                <div class="panel_s">
                    <div class="panel-body">

                        <?php $attrs = (isset($factura) ? [] : ['autofocus' => true]); ?>
                        <?php $value = (isset($factura) ? $factura->numero_factura : ''); ?>
                        <?php echo render_input('numero_factura', 'factura_numero', $value, 'text', $attrs); ?>

                        <div class="form-group select-placeholder">
                            <label for="cliente_id" class="control-label"><?php echo _l('factura_cliente'); ?></label>
                            <select name="cliente_id" class="selectpicker" data-width="100%"
                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                <option value=""></option>
                                <?php foreach ($clientes as $cliente) { ?>
                                    <option value="<?php echo e($cliente['id']); ?>" <?php if (isset($factura) && $factura->cliente_id == $cliente['id']) { echo 'selected'; } ?>>
                                        <?php echo e($cliente['nombre']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <?php $value = (isset($factura) ? _d($factura->fecha_emision) : _d(date('Y-m-d'))); ?>
                        <?php echo render_date_input('fecha_emision', 'factura_fecha_emision', $value); ?>

                        <?php $value = (isset($factura) ? _d($factura->fecha_vencimiento) : ''); ?>
                        <?php echo render_date_input('fecha_vencimiento', 'factura_fecha_vencimiento', $value); ?>

                        <?php $value = (isset($factura) ? $factura->total : ''); ?>
                        <?php echo render_input('total', 'factura_total', $value, 'number'); ?>

                        <div class="form-group select-placeholder">
                            <label for="estado" class="control-label"><?php echo _l('factura_estado'); ?></label>
                            <select name="estado" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                <option value=""><?php echo _l('factura_estado_opciones'); ?></option>
                                <option value="1" <?php if (isset($factura) && $factura->estado == 1) { echo 'selected'; } ?>><?php echo _l('factura_estado_pagado'); ?></option>
                                <option value="0" <?php if (isset($factura) && $factura->estado == 0) { echo 'selected'; } ?>><?php echo _l('factura_estado_pendiente'); ?></option>
                            </select>
                        </div>

                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="notify_when_paid" id="notify_when_paid" <?php if (isset($factura)) { if ($factura->notify_when_paid == 1) { echo 'checked'; } } else { echo 'checked'; } ?>>
                            <label for="notify_when_paid"><?php echo _l('factura_notify_when_paid'); ?></label>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <button type="submit" class="btn btn-primary "><?php echo _l('submit'); ?></button>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
            <?php if (isset($factura)) { ?>
            <div class="col-md-6">
                <h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700">
                    <?php echo _l('factura_achievement'); ?>
                </h4>
                <div class="panel_s">
                    <div class="panel-body">

                        <h3 class="text-center tw-font-semibold"><?php echo _l('factura_total_paid'); ?>
                            <small class="tw-font-medium"><?php echo _l('factura_total', $factura->total); ?></small>
                        </h3>

                        <div class="achievement mtop30" data-toggle="tooltip" title="<?php echo _l('factura_total', $factura->total); ?>">
                            <div class="goal-progress" data-thickness="40" data-reverse="true">
                                <strong class="goal-percent"></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
$(function() {
    appValidateForm($('form'), {
        numero_factura: 'required',
        cliente_id: 'required',
        fecha_emision: 'required',
        fecha_vencimiento: 'required',
        total: 'required',
        estado: 'required'
    });

    <?php if (isset($factura)) { ?>
    var circle = $('.goal-progress').circleProgress({
        value: '<?php echo e($factura->progress_percent); ?>',
        size: 250,
        fill: {
            gradient: ["#28b8da", "#059DC1"]
        }
    }).on('circle-animation-progress', function(event, progress, stepValue) {
        $(this).find('strong.goal-percent').html(parseInt(100 * stepValue) + '<i>%</i>');
    });
    <?php } ?>
});
</script>
</body>
</html>
