<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class registro_vfu extends fs_model
{
   public $vfu_id;
   public $marca;
   public $modelo;
   public $vfu_tipo;
   public $color;
   public $matricula;
   public $f_matriculacion;
   public $f_baja;
   public $f_entrada;
   public $pais_matriculacion;
   public $consumo;
   public $bastidor;
   public $estado;
   public $otros_datos;
   public $codtitular_vfu;
   public $representante;
   public $r_nif;
   public $r_concepto;
   public $documentacion;
   public $instock;
   public $observaciones_vfu;
   public $ncertificado;
   public $baja_nos;
   
   /// Estos datos los usas, pero no los guardas en la base de datos
   public $nombre_titular;
   public $telefono1_titular;
   public $cifnif_titular;
   
   public $ciudad;
   public $direccion;
   public $codpostal;
   public $email;


   public function __construct($s = FALSE)
   {
      parent::__construct('vfu', 'plugins/vfu/');
      
      if($s)
      {
         $this->vfu_id = intval($s['vfu_id']);
         $this->marca = $s['marca'];
         $this->modelo = $s['modelo'];
         $this->vfu_tipo = intval($s['vfu_tipo']);
         $this->color = $s['color'];
         $this->matricula = $s['matricula'];
         
         $this->f_matriculacion = NULL;
         if(isset($s['f_matriculacion']) )
             $this->f_matriculacion = date('d-m-Y', strtotime($s['f_matriculacion']));
         
         $this->f_baja = NULL;
         if(isset($s['f_baja']) )
             $this->f_baja = date('d-m-Y', strtotime($s['f_baja']));
         
         $this->f_entrada = NULL;
         if(isset($s['f_entrada']) )
             $this->f_entrada = date('d-m-Y', strtotime($s['f_entrada']));
             
         $this->pais_matriculacion = $s['pais_matriculacion'];
         $this->consumo = intval($s['consumo']);
         $this->bastidor = $s['bastidor'];
         $this->estado = intval($s['estado']);
         $this->otros_datos = $s['otros_datos'];
         $this->codtitular_vfu = $s['codtitular_vfu'];
         $this->representante = $s['representante'];      
         $this->r_nif = $s['r_nif'];
         $this->r_concepto = $s['r_concepto'];
         $this->documentacion = intval($s['documentacion']); 
         $this->instock = $this->str2bool($s['instock']);
         $this->observaciones_vfu = $this->no_html($s['observaciones_vfu']);
         $this->ncertificado = intval($s['ncertificado']);
         $this->baja_nos = $this->str2bool($s['baja_nos']);
         
         $this->nombre_titular = $s['nombre'];
         $this->telefono1_titular = $s['telefono1'];
         $this->cifnif_titular = $s['cifnif'];
         $this->ciudad = $s['ciudad'];
         $this->codpostal = $s['codpostal'];
         $this->direccion = $s['direccion'];
         $this->email = $s['email'];
         
      }
      else
      {
         $this->vfu_id = NULL;
         $this->marca = '';
         $this->modelo = '';
         $this->vfu_tipo = 0;
         $this->color = '';
         $this->matricula = '';
         $this->f_matriculacion = NULL;
         $this->f_baja = NULL;
         $this->f_entrada = date('d-m-Y');
         $this->pais_matriculacion = '';
         $this->consumo = 0;
         $this->bastidor = '';
         $this->estado = 0;
         $this->otros_datos = '';
         $this->codtitular_vfu = NULL;
         $this->representante = '';     
         $this->r_nif = '';
         $this->r_concepto = '';
         $this->documentacion = 0;    
         $this->instock =  1;
         $this->observaciones_vfu = '';
         $this->ncertificado = 0;
         $this->baja_nos = 1;
      
         $this->nombre_titular = '';
         $this->telefono1_titular = '';
         $this->cifnif_titular = '';
         $this->email = '';
         $this->ciudad = '';
         $this->codpostal = '';
         $this->direccion = '';         
      }
   }
   
   public function install()
   {
      return '';
   }   

   public function tipos()
   {
      $tipos = array(
          0 => '-',
          1 => 'Turismo',
          2 => 'Furgon',
          3 => 'Furgoneta',
          4 => 'Furgoneta Mixta',          
          5 => 'Ciclomotor',
          6 => 'Motocicleta',
          7 => 'Camión',
          8 => 'Camión basculante',
          9 => 'Mixto Adaptable',
          10 => 'Tractor',
          11 => 'Remolque',
          12 => 'Vehiculo comercial'    
      );
      
      return $tipos;
   }

   public function nombre_tipo()
   {
      $tipos = $this->tipos();
      return $tipos[$this->vfu_tipo];
   }
   
   public function estados()
   {
      $estados = array(
          0 => '-',
          1 => 'Entero',
          2 => 'Parcialmente desmontado',
          3 => 'Siniestrado'
      );
      
      return $estados;
   }
   
   public function consumos()
   {
      $consumos = array(
          0 => '-',
          1 => 'Gasolina',
          2 => 'Gasoil',
          3 => 'Electrico',
          4 => 'Hibrido',
          5 => 'Otros'
      );
      
      return $consumos;
   }

   public function document()
   {
      $document = array(
          0 => '-',
          1 => 'Se Adjunta',
          2 => 'Inexistente',
          3 => 'Pendiente'
      );
      
      return $document;
   }
   
   public function nombre_document()
   {
      $document = $this->document();
      return $document[$this->documentacion];
   }
   
   public function url()
   {
      if( is_null($this->vfu_id) )
      {
         return 'index.php?page=vfu_ficha';
      }
      else
      {
         return 'index.php?page=vfu_ficha&id='.$this->vfu_id;
      }
   }  
   
   public function get($id)
   {
      $sql = "SELECT * FROM
         (vfu INNER JOIN vfu_titulares ON vfu.codtitular_vfu = vfu_titulares.codtitular_vfu)
         WHERE vfu.vfu_id = ".$this->var2str($id).";";
      
      $data = $this->db->select($sql);
      if($data)
         return new registro_vfu($data[0]);
      else
         return FALSE;
   }

   public function get_all_titular($id)
   {
      $sql = "SELECT * FROM
         (vfu INNER JOIN vfu_titulares ON vfu.codtitular_vfu = vfu_titulares.codtitular_vfu)
         WHERE vfu.codtitular_vfu = ".$this->var2str($id)." ORDER BY vfu.vfu_id DESC";
      
      $data = $this->db->select($sql.";");
      if($data)
      {
         foreach($data as $d)
            $vfulist[] = new registro_vfu($d);
      }
      
      return $vfulist;
   }

   public function get_titular($id)
   {
      $sql = "SELECT * FROM vfu_titulares WHERE codtitular_vfu = " . $this->var2str($id) . ";";
        
      $data = $this->db->select($sql);
      
      if($data)
         return new registro_vfu($data[0]);
      else
         return FALSE;         
   }
   
   public function exists()
   {
      if( is_null($this->vfu_id) )
      {
         return FALSE;
      }
      else
      {
         return $this->db->select("SELECT * FROM vfu WHERE vfu_id = ".$this->var2str($this->vfu_id).";");
      }
   }
   
   public function valida()
   {
      $this->marca = $this->no_html($this->marca);
      $this->modelo = $this->no_html($this->modelo);
      $this->color = $this->no_html($this->color);
      $this->observaciones_vfu = $this->no_html($this->observaciones_vfu);
      $this->matricula = $this->no_html($this->matricula);
      $this->representante = $this->no_html($this->representante);
      $this->r_nif = $this->no_html($this->r_nif);
      $this->r_concepto = $this->no_html($this->r_concepto);
      
      $this->matricula = strtoupper($this->matricula);
      $this->matricula = preg_replace('[\s+]', '', $this->matricula);
      $this->matricula = preg_replace('[\-]', '', $this->matricula);
      $this->marca = ucfirst($this->marca);
      $this->modelo = ucwords($this->modelo);
      $this->color = ucfirst($this->color);
      $this->pais_matriculacion = ucfirst($this->pais_matriculacion);
      $this->representante = ucwords($this->representante);
      $this->r_nif = strtoupper($this->r_nif);
      $this->r_concepto = strtoupper($this->r_concepto);
      
      /// valido las variables, cambio MAY/MIN y simplemente eliminar el html de las variables
      return TRUE;       
   }   

   public function save()
   {
      if( $this->valida() )
      {
         if( $this->exists() )
         {
            $sql = "UPDATE vfu SET marca = ".$this->var2str($this->marca).",
               modelo = ".$this->var2str($this->modelo).", vfu_tipo = ".$this->var2str($this->vfu_tipo).",
               color = ".$this->var2str($this->color).", matricula = ".$this->var2str($this->matricula).",
               f_matriculacion = ".$this->var2str($this->f_matriculacion).", f_baja = ".$this->var2str($this->f_baja).",
               f_entrada = ".$this->var2str($this->f_entrada).", pais_matriculacion = ".$this->var2str($this->pais_matriculacion).",
               consumo = ".$this->var2str($this->consumo).", bastidor = ".$this->var2str($this->bastidor).",
               estado = ".$this->var2str($this->estado).", otros_datos = ".$this->var2str($this->otros_datos).",
               codtitular_vfu = ".$this->var2str($this->codtitular_vfu).", representante = ".$this->var2str($this->representante).",
               r_nif = ".$this->var2str($this->r_nif).", r_concepto = ".$this->var2str($this->r_concepto).",
               documentacion = ".$this->var2str($this->documentacion).", instock = ".$this->var2str($this->instock).",
               observaciones_vfu = ".$this->var2str($this->observaciones_vfu).", ncertificado = ".$this->var2str($this->ncertificado).",
               baja_nos = ".$this->var2str($this->baja_nos)." WHERE vfu_id = ".$this->var2str($this->vfu_id).";";
            
            return $this->db->exec($sql);
         }
         else
         {
            $sql = "INSERT INTO vfu (marca, modelo, vfu_tipo, color, matricula, f_matriculacion, f_baja, f_entrada, pais_matriculacion,
               consumo, bastidor, estado, otros_datos, codtitular_vfu, representante, r_nif, r_concepto, documentacion, instock, 
               observaciones_vfu, ncertificado, baja_nos) VALUES (".$this->var2str($this->marca).",
               ".$this->var2str($this->modelo).",".$this->var2str($this->vfu_tipo).",".$this->var2str($this->color).",
               ".$this->var2str($this->matricula).",".$this->var2str($this->f_matriculacion).",
               ".$this->var2str($this->f_baja).",".$this->var2str($this->f_entrada).",".$this->var2str($this->pais_matriculacion).",
               ".$this->var2str($this->consumo).",".$this->var2str($this->bastidor).",".$this->var2str($this->estado).",
               ".$this->var2str($this->otros_datos).",".$this->var2str($this->codtitular_vfu).",".$this->var2str($this->representante).",
               ".$this->var2str($this->r_nif).",".$this->var2str($this->r_concepto).",".$this->var2str($this->documentacion).",
               ".$this->var2str($this->instock).",".$this->var2str($this->observaciones_vfu).",".$this->var2str($this->ncertificado).",
               ".$this->var2str($this->baja_nos).");";
            
            if( $this->db->exec($sql) )
            {
               $this->vfu_id = $this->db->lastval();
               return TRUE;
            }
            else
               return FALSE;
         }
      }
      else
         return FALSE;      
   }
   
   public function delete()
   {
       return $this->db->exec("DELETE FROM vfu WHERE vfu_id = ".$this->var2str($this->vfu_id).";");
   }
   
   public function all($offset=0, $limit=FS_ITEM_LIMIT)
   {
      $vfulist = array();

      $sql = "SELECT * FROM
         vfu INNER JOIN vfu_titulares ON vfu.codtitular_vfu = vfu_titulares.codtitular_vfu
         WHERE vfu.vfu_id != 0 ORDER BY vfu_id DESC";
      
      $data = $this->db->select_limit($sql, $limit, $offset);
      if($data)
      {
         foreach($data as $d)
            $vfulist[] = new registro_vfu($d);
      }
      
      return $vfulist;
   }
   
   public function search($buscar='', $desde='', $hasta='', $filtro_tipo='', $orden='vfu_id', $offset=0)
   {
      $entidadlist = array();
      $buscar = strtolower( trim($buscar) );
      
      $sql = "SELECT * FROM
        vfu INNER JOIN vfu_titulares ON vfu.codtitular_vfu = vfu_titulares.codtitular_vfu
        WHERE vfu.vfu_id != 0 ";
      
      //Primero compruebo si hay texto a buscar
      if($buscar != '')
      {
          if( is_numeric($buscar) ){
                $sql .= " AND (vfu.ncertificado = ". $this->var2str($buscar) ." OR (lower(vfu.otros_datos) LIKE lower('%".$buscar."%'))
                OR (lower(vfu.modelo) LIKE lower('%".$buscar."%')) OR (lower(vfu_titulares.cifnif) LIKE lower('%".$buscar."%'))
                OR (lower(vfu.matricula) LIKE upper('%".$buscar."%')))";
          }else{
                $sql .= " AND ((upper(vfu.matricula) LIKE upper('%".$buscar."%')) OR (lower(vfu.otros_datos) LIKE lower('%".$buscar."%'))
                OR (lower(vfu.observaciones_vfu) LIKE lower('%".$buscar."%')))";              
          }
      }
      
      if($desde != '')
      {
         $sql .= " AND vfu.f_entrada >= ".$this->var2str($desde);
      }
      
      if($hasta != '')
      {
         $sql .= " AND vfu.f_entrada <= ".$this->var2str($hasta);
      }      
      
      //Segundo compruebo el parametro para filtrar
      if($filtro_tipo != '')
      {
          if($filtro_tipo == 'instock')
            $sql .= " AND vfu.instock = 1";
          elseif ($filtro_tipo == 'nosotros') 
            $sql .= " AND vfu.baja_nos = 1";
          elseif ($filtro_tipo == 'pendientes')
            $sql .= " AND vfu.documentacion = 3";  
      }      
      
      //Tercero compruebo el orden
      $sql.= " ORDER BY ".$orden." DESC ";
      
      $data = $this->db->select_limit($sql, FS_ITEM_LIMIT, $offset);
      if($data)
      {
         foreach($data as $d)
            $entidadlist[] = new registro_vfu($d);
      }
      
      return $entidadlist;        
   }  
   
   public function nextvalue(){

        $data = $this->db->select("SELECT MAX(ncertificado) AS id FROM vfu");
        
        if ($data)
            return ($data[0]['id'])+1;
        else
            return FALSE;
   }
}