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
class vfu_docs extends fs_controller
{
   public function __construct() {
      parent::__construct(__CLASS__, 'Docs PDF - Enlaces DGT', 'VFU', FALSE, TRUE);
      /// cualquier cosa que pongas aquí se ejecutará DESPUÉS de process()
   }

   /**
    * esta función se ejecuta si el usuario ha hecho login,
    * a efectos prácticos, este es el constructor
    */
   protected function process()
   {
      /// desactivamos la barra de botones
      $this->show_fs_toolbar = TRUE;
   }
      
}
