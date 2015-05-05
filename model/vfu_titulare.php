<?php
/*
* Dependencies generated by the foreign keys
*/


/* Primary key found: codtitular_vfu*/
class vfu_titulare extends fs_model
{
    public $codtitular_vfu;
    public $cifnif;
    public $nombre;
    public $direccion;
    public $codpostal;
    public $ciudad;
    public $telefono1;
    public $email;


    public function __construct($data=FALSE)
    {
        parent::__construct('vfu_titulares', 'plugins/vfu/');
        
        if($data)
        {
            $this->codtitular_vfu = $data['codtitular_vfu'];
            $this->cifnif = $data['cifnif'];
            $this->nombre = $data['nombre'];
            $this->direccion = $data['direccion'];
            $this->codpostal = $data['codpostal'];
            $this->ciudad = $data['ciudad'];
            $this->telefono1 = $data['telefono1'];
            $this->email = $data['email'];
        }else{
            $this->codtitular_vfu = 0;
            $this->cifnif = '';
            $this->nombre = '';
            $this->direccion = '';
            $this->codpostal = '';
            $this->ciudad = '';
            $this->telefono1 = '';
            $this->email = '';            
        }
    }

    /**
     * Esta función es llamada al crear una tabla.
     * Permite insertar valores en la tabla.
     */
    protected function install()
    {
        return '';
    }

    /**
     * Esta función devuelve TRUE si los datos del objeto se encuentran
     * en la base de datos.
     */
    public function exists()
    {
        
        if($this->codtitular_vfu)
        {
            $value = $this->var2str($this->codtitular_vfu);
            return $this->db->select("SELECT * FROM {$this->table_name} WHERE codtitular_vfu = $value");
        }
        
        return false;
    }

   public function exists_cifnif()
   {
      if( is_null($this->cifnif) )
         return FALSE;
      else
         return $this->db->select("SELECT * FROM ".$this->table_name."
            WHERE cifnif = ".$this->var2str($this->cifnif).";");
   }     
    
   public function valida()
   {
      $status = FALSE;
      
      $this->codtitular_vfu = trim($this->codtitular_vfu);
      $this->nombre = $this->no_html($this->nombre);
      $this->cifnif = $this->no_html($this->cifnif);
      $this->cifnif = strtoupper($this->cifnif);
      $this->cifnif = preg_replace('[\s+]', '', $this->cifnif);
      $this->cifnif = preg_replace('[\-]', '', $this->cifnif);
      $this->cifnif = preg_replace('[\.]', '', $this->cifnif);
      $this->cifnif = preg_replace('[\/]', '', $this->cifnif);
      $this->codpostal = $this->no_html($this->codpostal);
      $this->direccion = $this->no_html($this->direccion);
      $this->ciudad = $this->no_html($this->ciudad);
      
      if( strlen($this->nombre) < 1 OR strlen($this->nombre) > 100 )
         $this->new_error_msg("Nombre de titular no válido.");
      else if( strlen($this->cifnif) < 7 OR strlen($this->cifnif) > 10 )
         $this->new_error_msg("CIF o NIF de titular no válido."); 
      else if($this->exists_cifnif() AND !$this->exists())
         $this->new_error_msg("CIF o NIF del titular ya existente.");
      else
         $status = TRUE;
      
      return $status;
   }   
   
    /**
     * Esta función sirve tanto para insertar como para actualizar
     * los datos del objeto en la base de datos.
     */
    public function save() {

        if ($this->valida()) {
            if ($this->exists()) {
                $sql = "UPDATE " . $this->table_name . " SET cifnif = " . $this->var2str($this->cifnif) . "
                        , nombre = " . $this->var2str($this->nombre) . "
                        , direccion = " . $this->var2str($this->direccion) . "
                        , codpostal = " . $this->var2str($this->codpostal) . "
                        , ciudad = " . $this->var2str($this->ciudad) . "
                        , telefono1 = " . $this->var2str($this->telefono1) . "
                        , email = " . $this->var2str($this->email) . "
                          WHERE codtitular_vfu = " . $this->var2str($this->codtitular_vfu) . ";";
                
                return $this->db->exec($sql);
                
            } else {
                $sql = "INSERT INTO " . $this->table_name . " (
                                    cifnif
                                    , nombre
                                    , direccion
                                    , codpostal
                                    , ciudad
                                    , telefono1
                                    , email
                                    
                                ) VALUES (
                                    " . $this->var2str($this->cifnif) . "
                                    ,  " . $this->var2str($this->nombre) . "
                                    ,  " . $this->var2str($this->direccion) . "
                                    ,  " . $this->var2str($this->codpostal) . "
                                    ,  " . $this->var2str($this->ciudad) . "
                                    ,  " . $this->var2str($this->telefono1) . "
                                    ,  " . $this->var2str($this->email) . "
                                    
                                )";
                
                if ($this->db->exec($sql)) {
                    $this->codtitular_vfu = $this->db->lastval();
                    return TRUE;
                } else
                    return FALSE;                
            }

        } else
            return FALSE;
    }

    /**
     * Esta función sirve para eliminar los datos del objeto de la base de datos
     */
    
   public function delete()
   {
       return $this->db->exec("DELETE FROM ".$this->table_name." WHERE codtitular_vfu = ".$this->var2str($this->codtitular_vfu).";");
   }
   
    public function get($id)
   {
      $sql = "SELECT * FROM ". $this->table_name ." WHERE codtitular_vfu = " . $this->var2str($id) . ";";
        
      $data = $this->db->select($sql);
      
      if($data)
         return new vfu_titulare($data[0]);
      else
         return FALSE;       
   } 
    
    public function get_all($offset=0, $limit=FS_ITEM_LIMIT)
    {
        return $this->parse($this->db->select_limit("SELECT * FROM {$this->table_name} ORDER BY codtitular_vfu DESC", $offset, $limit));
    }
    public function parse($items)
    {
        if(count($items) > 1)
        {
            $list = array();
            foreach($items as $item)
            {
                $list[] = new vfu_titulare($item);
            }
            return $list;
        }
        else if(count($items) == 0)
        {
            return new vfu_titulare($items[0]);
        }
        return null;
    }
    
   public function search($query, $offset=0)
   {
      $titulist = array();
      $query = strtolower( $this->no_html($query) );
      
      $consulta = "SELECT * FROM ".$this->table_name." WHERE ";
      
      if( is_numeric($query) )
         $consulta .= "codtitular_vfu LIKE '%".$query."%' OR cifnif LIKE '%".$query."%' OR telefono1 LIKE '%".$query."%'";
      else
      {
         $buscar = str_replace(' ', '%', $query);
         $consulta .= "lower(nombre) LIKE '%".$buscar."%' OR lower(cifnif) LIKE '%".$buscar."%'
            OR lower(direccion) LIKE '%".$buscar."%'";
      }   
      
      $consulta .= " ORDER BY nombre ASC";
      
      $titulares = $this->db->select_limit($consulta, FS_ITEM_LIMIT, $offset);
      if($titulares)
      {
         foreach($titulares as $p)
            $titulist[] = new vfu_titulare($p);
      }
      return $titulist;      
   }
}