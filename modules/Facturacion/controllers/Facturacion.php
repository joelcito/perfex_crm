<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Facturacion extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('facturacion_model');
    }

    /* Listar todas las facturas */
    public function index()
    {
        if (staff_cant('view', 'facturacion')) {
            access_denied('facturacion');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('facturacion', 'table'));
        }
        $data['title'] = _l('facturacion_tracking');
        $this->load->view('manage', $data);
    }

    public function factura($id = '')
    {
        
        if (staff_cant('view', 'facturacion')) {
            access_denied('facturacion');
        }
        if ($this->input->post()) {
            if ($id == '') {
                if (staff_cant('create', 'facturacion')) {
                    access_denied('facturacion');
                }
                $id = $this->facturacion_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('factura')));
                    redirect(admin_url('facturacion/factura/' . $id));
                }
            } else {
                if (staff_cant('edit', 'facturacion')) {
                    access_denied('facturacion');
                }
                $success = $this->facturacion_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('factura')));
                }
                redirect(admin_url('facturacion/factura/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('factura_lowercase'));
        } else {
            $data['factura'] = $this->facturacion_model->get($id);
            $title = _l('edit', _l('factura_lowercase'));
        }

        // var_dump(
        //     $id, 
        //     $this->input->post(), 
        //     (($this->input->post())? true: false),
        //     $title
        // );
        // exit;

        // $this->load->model('clientes_model');
        // $data['clientes'] = $this->clientes_model->get('', ['active' => 1]);

        // $this->load->model('productos_model');
        // $data['productos'] = $this->productos_model->get_all();

        $data['title'] = $title;
        $this->load->view('factura', $data);
    }

    /* Eliminar factura de la base de datos */
    public function delete($id)
    {
        if (staff_cant('delete', 'facturacion')) {
            access_denied('facturacion');
        }
        if (!$id) {
            redirect(admin_url('facturacion'));
        }
        $response = $this->facturacion_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('factura')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('factura_lowercase')));
        }
        redirect(admin_url('facturacion'));
    }

    public function notify($id, $notify_type)
    {
        if (staff_cant('edit', 'facturacion') && staff_cant('create', 'facturacion')) {
            access_denied('facturacion');
        }
        if (!$id) {
            redirect(admin_url('facturacion'));
        }
        $success = $this->facturacion_model->notify_staff_members($id, $notify_type);
        if ($success) {
            set_alert('success', _l('factura_notify_staff_notified_manually_success'));
        } else {
            set_alert('warning', _l('factura_notify_staff_notified_manually_fail'));
        }
        redirect(admin_url('facturacion/factura/' . $id));
    }
}
