<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
function subir_fichero($directorio_destino, $nombre_fichero)
{
    //if($nombre_fichero){
        $tmp_name = @$_FILES[$nombre_fichero]['tmp_name'];
        //var_dump($tmp_name);
        //si hemos enviado un directorio que existe realmente y hemos subido el archivo    
        if (is_dir($directorio_destino) && is_uploaded_file($tmp_name))
        {
            $img_file = $_FILES[$nombre_fichero]['name'];
            $img_type = $_FILES[$nombre_fichero]['type'];
            //echo 1;
            // Si se trata de una imagen   
            //if (((strpos($img_type, "gif") || strpos($img_type, "jpeg") || strpos($img_type, "jpg")) || strpos($img_type, "png")))
            //{
                //¿Tenemos permisos para subir la imágen?
                //echo 2;
                if (move_uploaded_file($tmp_name, $directorio_destino . '/' . $img_file))
                {
                    return true;
                }
            //}
        }
    //}
    //Si llegamos hasta aquí es que algo ha fallado
    return false;
}
function crearVistasPrevias150($img,$dir, $width = '150', $height = '150')
    {
                //unset al arreglo config por si existe en memoria
        unset($config);
        $config['image_library']  = 'GD2';
        $config['source_image']   = './'.$dir.'/'.$img;
                //se debe de crear la carpeta thumb dentro de nuestro directorio $dir
        $config['new_image']      = './'.$dir.'/thumbs_150/'.$img;
        $config['create_thumb']   = TRUE;
        $config['maintain_ratio'] = TRUE;
        $config['width']          = $width;
        $config['height']         = $height;
 
                //verificamos que no este bacio nuestro archivo a subir
        if(!empty($config['source_image']))
        {
                        //cargamos desde CI  a nuestra libreria image_lib
            $ci =& get_instance();
            $ci->load->library('image_lib', $config);
                        // iniciamos image_lib con el contenido de $config
            $ci->image_lib->initialize($config);
 
                        //le hacemos resize a nuestra imagen
            if (!$ci->image_lib->resize())
            {
                $error = array('error'=>$ci->image_lib->display_errors());
                return $error;
            }
            else
            {
                return TRUE;
            }
                        //limpeamos el contenido de image_lib esto para crear varias thumbs
            $ci->image_lib->clear();
        }
    }
function crearVistasPreviasCompletas($img,$dir, $width, $height)
    {
                //unset al arreglo config por si existe en memoria
        unset($config);
        $config['image_library']  = 'GD2';
        $config['source_image']   = './'.$dir.'/'.$img;
                //se debe de crear la carpeta thumb dentro de nuestro directorio $dir
        $config['new_image']      = './'.$dir.'/thumbs_completos/'.$img;
        $config['create_thumb']   = TRUE;
        $config['maintain_ratio'] = TRUE;
        $config['width']          = $width;
        $config['height']         = $height;
 
                //verificamos que no este bacio nuestro archivo a subir
        if(!empty($config['source_image']))
        {
                        //cargamos desde CI  a nuestra libreria image_lib
            $ci =& get_instance();
            $ci->load->library('image_lib', $config);
                        // iniciamos image_lib con el contenido de $config
            $ci->image_lib->initialize($config);
 
                        //le hacemos resize a nuestra imagen
            if (!$ci->image_lib->resize())
            {
                $error = array('error'=>$ci->image_lib->display_errors());
                return $error;
            }
            else
            {
                return TRUE;
            }
                        //limpeamos el contenido de image_lib esto para crear varias thumbs
            $ci->image_lib->clear();
        }
    }

    
