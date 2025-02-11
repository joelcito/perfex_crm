<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Facturacion_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param  integer (optional)
     * @return object
     * Get single invoice
     */
    public function get_factura($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'tblfacturacion')->row();
        }
        return $this->db->get(db_prefix() . 'tblfacturacion')->result_array();
    }

    /**
     * Get all invoices
     * @param  boolean $exclude_paid Exclude paid invoices (default: true)
     * @return array
     */
    public function get_todas_las_facturas($exclude_paid = true)
    {

        print_r("holas");
        
        if ($exclude_paid) {
            $this->db->where('estado', 'pendiente');
        }

        $this->db->order_by('fecha_vencimiento', 'asc');
        $facturas = $this->db->get(db_prefix() . 'tblfacturacion')->result_array();

        foreach ($facturas as $key => $val) {
            $facturas[$key]['monto_total'] = $this->calcular_monto_factura($val['id']);
        }

        return $facturas;
    }

    /**
     * Create a new invoice
     * @param mixed $data All form data
     * @return mixed
     */
    public function crear_factura($data)
    {
        $data['estado'] = isset($data['estado']) ? $data['estado'] : 'pendiente';
        $data['fecha_emision'] = to_sql_date($data['fecha_emision']);
        $data['fecha_vencimiento'] = to_sql_date($data['fecha_vencimiento']);
        $this->db->insert(db_prefix() . 'tblfacturacion', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('Nueva Factura Creada [ID:' . $insert_id . ']');
            return $insert_id;
        }
        return false;
    }

    /**
     * Update invoice
     * @param mixed $data All form data
     * @param mixed $id Invoice ID
     * @return boolean
     */
    public function actualizar_factura($data, $id)
    {
        $data['fecha_emision'] = to_sql_date($data['fecha_emision']);
        $data['fecha_vencimiento'] = to_sql_date($data['fecha_vencimiento']);
        
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'tblfacturacion', $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('Factura Actualizada [ID:' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Delete invoice
     * @param mixed $id Invoice ID
     * @return boolean
     */
    public function eliminar_factura($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'tblfacturacion');
        if ($this->db->affected_rows() > 0) {
            log_activity('Factura Eliminada [ID:' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Calculate total amount of an invoice
     * @param mixed $id Invoice ID
     * @return float
     */
    public function calcular_monto_factura($id)
    {
        $this->db->select_sum('monto_pago');
        $this->db->where('factura_id', $id);
        $pagos = $this->db->get(db_prefix() . 'pagos')->row()->monto_pago;
        
        $factura = $this->get_factura($id);
        return floatval($factura->monto_total) - floatval($pagos);
    }

    /**
     * Mark an invoice as paid
     * @param mixed $id Invoice ID
     * @return boolean
     */
    public function marcar_como_pagada($id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'tblfacturacion', ['estado' => 'pagada']);
        if ($this->db->affected_rows() > 0) {
            log_activity('Factura Pagada [ID:' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Notify customer about invoice status
     * @param mixed $id Invoice ID
     * @param string $status Payment status (paid/overdue)
     * @return boolean
     */
    public function notificar_cliente($id, $status)
    {
        $factura = $this->get_factura($id);
        $cliente = $this->db->get_where(db_prefix() . 'clientes', ['id' => $factura->cliente_id])->row();

        $descripcion = $status == 'pagada' ? 'notificacion_factura_pagada' : 'notificacion_factura_vencida';

        $this->load->model('notificaciones_model');
        $notificacion = $this->notificaciones_model->agregar_notificacion([
            'usuario_id' => $cliente->id,
            'descripcion' => $descripcion,
            'fecha' => date('Y-m-d H:i:s'),
            'datos_adicionales' => serialize([
                'monto_total' => $factura->monto_total,
                'fecha_emision' => _d($factura->fecha_emision),
                'fecha_vencimiento' => _d($factura->fecha_vencimiento),
            ]),
        ]);

        return $notificacion;
    }
}

