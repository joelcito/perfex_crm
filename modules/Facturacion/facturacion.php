<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Facturación
Description: Módulo para gestionar las facturas
Version: 1.0.0
Requires at least: 2.3.*
*/

define('FACTURACION_MODULE_NAME', 'facturacion');

hooks()->add_action('after_cron_run', 'facturacion_notification');
hooks()->add_action('admin_init', 'facturacion_module_init_menu_items');
hooks()->add_action('staff_member_deleted', 'facturacion_staff_member_deleted');
hooks()->add_action('admin_init', 'facturacion_permissions');

hooks()->add_filter('migration_tables_to_replace_old_links', 'facturacion_migration_tables_to_replace_old_links');
hooks()->add_filter('global_search_result_query', 'facturacion_global_search_result_query', 10, 3);
hooks()->add_filter('global_search_result_output', 'facturacion_global_search_result_output', 10, 2);
hooks()->add_filter('get_dashboard_widgets', 'facturacion_add_dashboard_widget');

function facturacion_add_dashboard_widget($widgets)
{
    $widgets[] = [
        'path'      => 'facturacion/widget',
        'container' => 'right-4',
    ];

    return $widgets;
}

function facturacion_staff_member_deleted($data)
{
    $CI = &get_instance();
    $CI->db->where('staff_id', $data['id']);
    $CI->db->update(db_prefix() . 'facturacion', [
        'staff_id' => $data['transfer_data_to'],
    ]);
}

function facturacion_global_search_result_output($output, $data)
{
    if ($data['type'] == 'facturacion') {
        $output = '<a href="' . admin_url('facturacion/factura/' . $data['result']['id']) . '">' . $data['result']['numero_factura'] . '</a>';
    }

    return $output;
}

function facturacion_global_search_result_query($result, $q, $limit)
{
    $CI = &get_instance();
    if (staff_can('view', 'facturacion')) {
        // Facturación
        $CI->db->select()->from(db_prefix() . 'facturacion')->like('descripcion', $q)->or_like('numero_factura', $q)->limit($limit);

        $CI->db->order_by('numero_factura', 'ASC');

        $result[] = [
            'result'         => $CI->db->get()->result_array(),
            'type'           => 'facturacion',
            'search_heading' => _l('facturacion'),
        ];
    }

    return $result;
}

function facturacion_migration_tables_to_replace_old_links($tables)
{
    $tables[] = [
        'table' => db_prefix() . 'facturacion',
        'field' => 'descripcion',
    ];

    return $tables;
}

function facturacion_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit'   => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities('facturacion', $capabilities, _l('facturacion'));
}

function facturacion_notification()
{
    $CI = &get_instance();
    $CI->load->model('facturacion/facturacion_model');
    $facturas = $CI->facturacion_model->get('', true);
    foreach ($facturas as $factura) {
        if (date('Y-m-d') >= $factura['fecha_vencimiento'] && $factura['estado'] != 'Pagada') {
            // Enviar notificación si la factura está vencida
            if ($factura['notificado'] == 0) {
                $CI->facturacion_model->notify_cliente($factura['id']);
                $CI->facturacion_model->mark_as_notified($factura['id']);
            }
        }
    }
}

/**
* Register activation module hook
*/
register_activation_hook(FACTURACION_MODULE_NAME, 'facturacion_module_activation_hook');

function facturacion_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
* Register language files
*/
register_language_files(FACTURACION_MODULE_NAME, [FACTURACION_MODULE_NAME]);

/**
* Init facturación module menu items in setup in admin_init hook
*/
function facturacion_module_init_menu_items()
{
    $CI = &get_instance();

    $CI->app->add_quick_actions_link([
        'name'       => _l('factura'),
        'url'        => 'facturacion/factura',
        'permission' => 'facturacion',
        'position'   => 56,
        'icon'       => 'fa-solid fa-file-invoice',
    ]);

    if (staff_can('view', 'facturacion')) {
        $CI->app_menu->add_sidebar_children_item('utilities', [
            'slug'     => 'facturacion-tracking',
            'name'     => _l('facturacion'),
            'href'     => admin_url('facturacion'),
            'position' => 24,
        ]);
    }
}

/**
* Get invoice types
*
* @return array
*/
function get_invoice_types()
{
    $types = [
        [
            'key'       => 1,
            'lang_key'  => 'invoice_type_service',
            'subtext'   => '',
            'dashboard' => has_permission('services', 'view'),
        ],
        [
            'key'       => 2,
            'lang_key'  => 'invoice_type_product',
            'subtext'   => '',
            'dashboard' => has_permission('products', 'view'),
        ],
        [
            'key'       => 3,
            'lang_key'  => 'invoice_type_subscription',
            'subtext'   => '',
            'dashboard' => has_permission('subscriptions', 'view'),
        ],
    ];

    return hooks()->apply_filters('get_invoice_types', $types);
}

/**
* Get invoice type by given key
*
* @param  int $key
*
* @return array
*/
function get_invoice_type($key)
{
    foreach (get_invoice_types() as $type) {
        if ($type['key'] == $key) {
            return $type;
        }
    }
}

/**
* Translate invoice type based on passed key
*
* @param  mixed $key
*
* @return string
*/
function format_invoice_type($key)
{
    foreach (get_invoice_types() as $type) {
        if ($type['key'] == $key) {
            return _l($type['lang_key']);
        }
    }

    return $key;
}
