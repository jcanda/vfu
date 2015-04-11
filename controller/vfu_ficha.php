<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of vfu_docs
 *
 * @author Zapasoft
 */
require_model('agente.php');
require_model('proveedor.php');
require_model('pais.php');
require_model('registro_vfu.php');

class vfu_ficha extends fs_controller {

    public $agente;
    public $proveedor;
    public $registro_vfu;
    public $busqueda;
    public $resultado;
    public $estado;
    public $pais;
    public $offset;

    public function __construct() {
        parent::__construct(__CLASS__, 'Fichas Vehiculos', 'VFU', FALSE, TRUE);
        /// cualquier cosa que pongas aquí se ejecutará DESPUÉS de process()
    }

    /**
     * esta función se ejecuta si el usuario ha hecho login,
     * a efectos prácticos, este es el constructor
     */
    protected function process() {
        /// desactivamos la barra de botones
        //$this->show_fs_toolbar = FALSE;

        $this->agente = FALSE;
        $this->proveedor = new proveedor();
        $this->registro_vfu = new registro_vfu();
        $this->pais = new pais();

        $this->busqueda = array(
            'contenido' => '',
            'desde' => '',
            'hasta' => '',
            'estado' => ''
        );

        //Realizamos un anidado segun los GET que recibamos
        // Primero para buscar proveedor:
        if (isset($_REQUEST['buscar_proveedor'])) {
            /// desactivamos la plantilla HTML
            $this->template = FALSE;

            $json = array();
            foreach ($this->proveedor->search($_REQUEST['buscar_proveedor']) as $prove) {
                $json[] = array('value' => $prove->nombre, 'data' => $prove->codproveedor);
            }

            header('Content-Type: application/json');
            echo json_encode(array('query' => $_REQUEST['buscar_proveedor'], 'suggestions' => $json));
        }
        //Para editar un vfu
        else if (isset($_GET['id'])) {
            $this->template = "vfu_edita";
            $this->page->title = "Edita VFU: " . $_GET['id'];
            $this->edita_vfu();
        }
        //PAra añadir VFU nuevo
        else if (isset($_GET['opcion'])) {
            if ($_GET['opcion'] == "nuevovfu") {
                $this->page->title = "Nueva BAJA Vehiculo";

                //Si ya existe proveedor entro aqui
                if (isset($_GET['codproveedor']) AND !empty($_GET['codproveedor'])) {
                    if (isset($_POST['matricula']) AND !empty($_POST['matricula'])) { /// editar
                        //si recibe matricula desde plantilla de AGREGA_VFU se GRABA y luego LISTO TODO
                        $proveedor = $this->proveedor->get($_GET['codproveedor']);
                        $proveedor->nombre = $_POST['nombre_proveedor'];
                        $proveedor->telefono1 = $_POST['telefono1'];
                        $proveedor->cifnif = $_POST['cifnif'];

                        if ($proveedor->save()) {
                            $this->new_message('Proveedor actualizado correctamente.');
                        } else
                            $this->new_error_msg('Error al guardar los datos del proveedor.');

                        $this->proveedor = $proveedor;
                        $vfu = $this->agrega_vfu();
                        
                        //$this->page->title = "Nueva VFU incluida: " . $vfu;
                        //$this->resultado = $this->registro_vfu->get($vfu);
                        //$this->template = "vfu_edita";
                        
                        $this->resultado = $this->registro_vfu->all();
                        $this->template = "vfu_ficha";
                        
                    }
                    else {
                        //$this->resultado = $this->proveedor->get($_GET['codproveedor']);
                        $this->resultado = $this->registro_vfu->getproduct_all($_GET['codproveedor']);
                        $this->template = "vfu_agrega";
                    }
                }
                //NUEVO proveedor y pasamos ID para crear el nuevo VFU
                elseif (!isset($_GET['codproveedor'])) {
                    $proveedor_id = $this->nuevo_proveedor();
                    $proveedor = $this->registro_vfu->getproduct_all($proveedor_id);
                    $this->proveedor = $proveedor;
                    $this->resultado = $proveedor;
                    $this->template = "vfu_agrega";
                } else {
                    $this->new_error_msg('Proveedor no encontrado.');
                    $this->resultado = $this->registro_vfu->all();
                    $this->template = "vfu_ficha";
                }
            }
        }
        //Para eliminar 
        else if (isset($_GET['delete'])) {
            $vfu = $this->registro_vfu->get($_GET['delete']);
            if ($vfu) {
                if ($vfu->delete()) {
                    $this->new_message('Registro eliminado correctamente.');
                } else
                    $this->new_error_msg('Imposible eliminar el registro.');
            } else
                $this->new_error_msg('Registro no encontrado.');

            $this->offset = 0;
            if (isset($_GET['offset']))
                $this->offset = intval($_GET['offset']); 
            
            $this->resultado = $this->registro_vfu->all($this->offset);
            $this->template = "vfu_ficha";
        }
        //Para listar todo
        else {
            $this->offset = 0;
            if (isset($_GET['offset']))
                $this->offset = intval($_GET['offset']);            
            
            $this->resultado = $this->registro_vfu->all($this->offset);
            $this->template = "vfu_ficha";
        }
    }

    public function nuevo_proveedor() {
        //----------------------------------------------
        // agrega un proveedor nuevo y retorna el id
        //----------------------------------------------

        if (isset($_POST['nombre'])) {
            $proveedor = new proveedor();
            $proveedor->codproveedor = $proveedor->get_new_codigo();
            $proveedor->nombre = $_POST['nombre'];
            $proveedor->nombrecomercial = $_POST['nombre'];
            $proveedor->cifnif = $_POST['cifnif'];
            $proveedor->telefono1 = $_POST['telefono1'];
            $proveedor->telefono2 = $_POST['telefono2'];
            $proveedor->codserie = $this->empresa->codserie;

            if ($proveedor->save()) {
                $dirproveedor = new direccion_proveedor();
                $dirproveedor->codproveedor = $proveedor->codproveedor;
                $dirproveedor->codpais = $_POST['pais'];
                $dirproveedor->provincia = $_POST['provincia'];
                $dirproveedor->ciudad = $_POST['ciudad'];
                $dirproveedor->codpostal = $_POST['codpostal'];
                $dirproveedor->direccion = $_POST['direccion'];
                $dirproveedor->descripcion = 'Principal';
                if ($dirproveedor->save()) {
                    $this->new_message('Proveedor agregado correctamente.');
                } else
                    $this->new_error_msg("¡Imposible guardar la dirección del Proveedor!");
            } else
                $this->new_error_msg('Error al agregar los datos del proveedor.');
        }

        return $proveedor->codproveedor;
    }

    public function agrega_vfu() {
        if ($this->proveedor) {
            $this->registro_vfu->codproveedor = $_GET['codproveedor'];
            $this->registro_vfu->marca = $_POST['marca'];
            $this->registro_vfu->modelo = $_POST['modelo'];
            $this->registro_vfu->vfu_tipo = $_POST['vfu_tipo'];
            $this->registro_vfu->color = $_POST['color'];
            $this->registro_vfu->matricula = $_POST['matricula'];

            if ($_POST['f_matriculacion'] == '') {
                $this->registro_vfu->f_matriculacion = NULL;
            } else
                $this->registro_vfu->f_matriculacion = $_POST['f_matriculacion'];

            if ($_POST['f_baja'] == '') {
                $this->registro_vfu->f_baja = NULL;
            } else
                $this->registro_vfu->f_baja = $_POST['f_baja'];

            if ($_POST['f_entrada'] == '') {
                $this->registro_vfu->f_entrada = date('d-m-Y');
            } else
                $this->registro_vfu->f_entrada = $_POST['f_entrada'];

            $this->registro_vfu->pais_matriculacion = $_POST['pais_matriculacion'];
            $this->registro_vfu->consumo = $_POST['consumo'];
            $this->registro_vfu->bastidor = $_POST['bastidor'];
            $this->registro_vfu->estado = $_POST['estado'];
            $this->registro_vfu->otros_datos = $_POST['otros_datos'];
            $this->registro_vfu->representante = $_POST['representante'];
            $this->registro_vfu->r_nif = $_POST['r_nif'];
            $this->registro_vfu->r_concepto = $_POST['r_concepto'];
            $this->registro_vfu->documentacion = $_POST['documentacion'];
            $this->registro_vfu->instock = isset($_POST['instock']);
            $this->registro_vfu->ncertificado = $_POST['ncertificado'];
            $this->registro_vfu->observaciones_vfu = $_POST['observaciones_vfu'];
            $this->registro_vfu->baja_nos = isset($_POST['baja_nos']);

            if ($this->registro_vfu->save()) {
                $this->new_message('Datos del VFU guardados correctamente.');
                return $this->registro_vfu->vfu_id;
            } else {
                $this->new_error_msg('Imposible guardar los datos del VFU.');
                return FALSE;
            }
        } else {
            $this->new_error_msg('Proveedor no encontrado.');
            return FALSE;
        }
    }

    public function edita_vfu() {
        $this->resultado = $this->registro_vfu->get($_GET['id']);

        if ($this->resultado) {
            $this->agente = $this->user->get_agente();
        }

        if ($this->resultado AND isset($_POST['matricula']) AND !empty($_POST['matricula'])) {
            $proveedor = $this->proveedor->get($this->resultado->codproveedor);
            if ($proveedor AND isset($_POST['nombre'])) {
                $proveedor->nombre = $_POST['nombre'];
                $proveedor->nombrecomercial = $_POST['nombre'];
                $proveedor->telefono1 = $_POST['telefono1'];
                $proveedor->cifnif = $_POST['cifnif'];

                if ($proveedor->save()) {
                    $this->new_message('Proveedor modificado correctamente.');
                } else
                    $this->new_error_msg('Error al guardar los datos del proveedor.');
            }

            $this->resultado->marca = $_POST['marca'];
            $this->resultado->modelo = $_POST['modelo'];
            $this->resultado->vfu_tipo = $_POST['vfu_tipo'];
            $this->resultado->color = $_POST['color'];
            $this->resultado->matricula = $_POST['matricula'];

            if ($_POST['f_entrada'] != '')
                $this->resultado->f_entrada = $_POST['f_entrada'];

            if ($_POST['f_matriculacion'] != '')
                $this->resultado->f_matriculacion = $_POST['f_matriculacion'];

            if ($_POST['f_baja'] != '')
                $this->resultado->f_baja = $_POST['f_baja'];

            $this->resultado->pais_matriculacion = $_POST['pais_matriculacion'];
            $this->resultado->consumo = $_POST['consumo'];
            $this->resultado->bastidor = $_POST['bastidor'];
            $this->resultado->estado = $_POST['estado'];
            $this->resultado->otros_datos = $_POST['otros_datos'];
            $this->resultado->representante = $_POST['representante'];
            $this->resultado->r_nif = $_POST['r_nif'];
            $this->resultado->r_concepto = $_POST['r_concepto'];
            $this->resultado->documentacion = $_POST['documentacion'];
            $this->resultado->instock = isset($_POST['instock']);
            $this->resultado->observaciones_vfu = $_POST['observaciones_vfu'];
            $this->resultado->ncertificado = $_GET['id'];
            $this->resultado->baja_nos = isset($_POST['baja_nos']);

            if ($this->resultado->save()) {
                $this->new_message('Datos del VFU guardados correctamente.');
            } else {
                $this->new_error_msg('Imposible guardar los datos del VFU.');
            }
        } elseif (!$this->resultado) {
            $this->new_error_msg('Datos no encontrados.');
        } 
    }

    public function listar_tipos() {
        $tipos = array();

        /**
         * En registro_vfu::tipos() nos devuelve un array con todos los estados,
         * pero como queremos también el id, pues hay que hacer este bucle para sacarlos.
         */
        foreach ($this->registro_vfu->tipos() as $i => $value)
            $tipos[] = array('id_tipo' => $i, 'nombre_tipo' => $value);

        return $tipos;
    }

    public function listar_consumos() {
        $consumos = array();

        /**
         * En registro_vfu::consumos() nos devuelve un array con todos los estados,
         * pero como queremos también el id, pues hay que hacer este bucle para sacarlos.
         */
        foreach ($this->registro_vfu->consumos() as $i => $value)
            $consumos[] = array('id_consumo' => $i, 'nombre_consumo' => $value);

        return $consumos;
    }

    public function listar_documentos() {
        $documentos = array();

        /**
         * En registro_vfu::documentos() nos devuelve un array con todos los estados,
         * pero como queremos también el id, pues hay que hacer este bucle para sacarlos.
         */
        foreach ($this->registro_vfu->document() as $i => $value)
            $documentos[] = array('id_documento' => $i, 'nombre_documento' => $value);

        return $documentos;
    }

    public function listar_estados() {
        $estados = array();

        /**
         * En registro_sat::estados() nos devuelve un array con todos los estados,
         * pero como queremos también el id, pues hay que hacer este bucle para sacarlos.
         */
        foreach ($this->registro_vfu->estados() as $i => $value)
            $estados[] = array('id_estado' => $i, 'nombre_estado' => $value);

        return $estados;
    }

   public function anterior_url()
   {
      $url = '';
      
      if($this->offset > 0)
      {
         $url = $this->url()."&offset=".($this->offset-FS_ITEM_LIMIT);
      }
      
      return $url;
   }
   
   public function siguiente_url()
   {
      $url = '';
      
      if( count($this->resultado) == FS_ITEM_LIMIT )
      {
         $url = $this->url()."&offset=".($this->offset+FS_ITEM_LIMIT);
      }
      
      return $url;
   }    
}
