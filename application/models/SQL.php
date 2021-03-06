<?php

/**
 * version 6
 */

class Application_Model_SQL
{
    protected $dbAdapter = null;
    public function __construct()
    {
        $this->dbAdapter = Zend_Db_Table::getDefaultAdapter();
    }
    /**
     * \brief Obtenemos una instancia de la clase 'Zend_Auth_Adapter_DbTable'.
     *
     * @param $table Tabla en la que se verificará que los datos de usuario son correctos.
     * @param $cc_user Número de indentificación del usuario que queremos autenticar.
     * @param $password Contraseña del usuario a autenticar.
     *
     * @return Adaptador a la base de datos de la clase Zend_Auth_Adapter_DbTable.
     *
     */
    public function getAuthDbTable($table, $cc_user, $password)
    {
        return new Zend_Auth_Adapter_DbTable($this->dbAdapter, $table, $cc_user, $password);
    }

    /**
     * \brief Inserta un administrador en la base de datos.
     *
     * @param $sCC Cédula del administrador.
     * @param $sPassword Contraseña del usuario. La contraseña deberá ser pasada en encriptada, con el algoritmo de encriptación SHA1.
     * @param $sNames Nombres del administrador.
     * @param $sLastNames Apellidos del administrador.
     * @param $sTelephone Teléfono del administrador.
     * @param $sMovil Celular del administrador.
     * @param $sImage Ruta a la imagen donde está el administrador. Este parámetro es opcional.
     *
     */
    public function insertAdmin($sCC, $sPassword, $sNames, $sLastNames, $sTelephone, $sMovil, $sImage = '')
    {
        $sImage = $sImage==''?$sImage:"'$sImage'";
        $this->dbAdapter->fetchRow("SELECT * FROM f_insertadmin('$sCC', '$sPassword', '$sNames', '$sLastNames', '$sTelephone', '$sMovil', $sImage)");
    }

    /**
     * \brief Inserta un cliente en la base de datos.
     *
     * @param $sCC Cédula del cliente.
     * @param $sPassword Contraseña del usuario. La contraseña deberá ser pasada en encriptada, con el algoritmo de encriptación SHA1.
     * @param $sNames Nombres del cliente.
     * @param $sLastNames Apellidos del cliente.
     * @param $sTelephone Teléfono del cliente.
     * @param $sMovil Celular del cliente.
     * @param $sImage Ruta a la imagen donde está el cliente. Este parámetro es opcional.
     *
     */
    public function insertClient($sCC, $sPassword, $sNames, $sLastNames, $sTelephone, $sMovil, $sImage = '')
    {
        $sImage = $sImage==''?$sImage:"'$sImage'";
        $this->dbAdapter->fetchRow("SELECT * FROM f_insertclient('$sCC', '$sPassword', '$sNames', '$sLastNames', '$sTelephone', '$sMovil', $sImage)");
    }

    /**
     * \brief Inserta un programador en la base de datos.
     *
     * @param $sCC Cédula del programador.
     * @param $sPassword Contraseña del usuario. La contraseña deberá ser pasada en encriptada, con el algoritmo de encriptación SHA1.
     * @param $sNames Nombres del programador.
     * @param $sLastNames Apellidos del programador.
     * @param $sTelephone Teléfono del programador.
     * @param $sMovil Celular del programador.
     * @param $sImage Ruta a la imagen donde está el programador. Este parámetro es opcional.
     *
     */
    public function insertDeveloper($sCC, $sPassword, $sNames, $sLastNames, $sTelephone, $sMovil, $sImage = '')
    {
        $sImage = $sImage==''?$sImage:"'$sImage'";
        $this->dbAdapter->fetchRow("SELECT * FROM f_insertdeveloper('$sCC', '$sPassword', '$sNames', '$sLastNames', '$sTelephone', '$sMovil', $sImage)");
    }

    /**
     * \brief Insertamos una noticia.
     *
     * @param $sTitle Título de la noticia.
     * @param $sDescription Descripción o cuerpo de la noticia.
     * @param $sCCOwner Cédula del usuario que crea la noticia.
     * @param $sImage Ruta a la imágen que se adjuntará en la noticia. Este parámetro es opcional.
     *
     */
    public function insertNews($sTitle, $sDescription, $sCCOwner, $sImage = '')
    {
        $sImage = $sImage=="''"?$sImage:"'$sImage'";
        $this->dbAdapter->fetchRow("SELECT * FROM f_insertnews('$sTitle', '$sDescription', '$sCCOwner', $sImage)");
    }

    /**
     * \brief Obtenemos la lista de los usuarios con el rol de administradores.
     *
     * @return Lista de administradores. Cada registro está ordenado así: [cc, names, lastnames, telephone, movil]
     *
     */
    public function listAdmin()
    {
        return $this->dbAdapter->fetchAll("SELECT * FROM v_admin");
    }

    /**
     * \brief Obtenemos la lista de los usuarios con el rol de cliente.
     *
     * @return Lista de clientes. Cada registro está ordenado así: [cc, names, lastnames, telephone, movil]
     *
     */
    public function listClient()
    {
        return $this->dbAdapter->fetchAll("SELECT * FROM v_client");
    }

    /**
     * \brief Obtenemos la lista de los usuarios con el rol de desarrollador.
     *
     * @return Lista de desarrolladores. Cada registro está ordenado así: [cc, names, lastnames, telephone, movil]
     *
     */
    public function listDeveloper()
    {
        return $this->dbAdapter->fetchAll("SELECT * FROM v_developer");
    }

    /**
     * \brief Obtenemos la información de la empreza.
     * Esta información puede ser, ¿Quiénes somos?, misión, visión, etc.
     *
     * @return Lista con la información. Cada registro está ordenado así: [id, title, description, image]
     *
     */
    public function listInformation()
    {
        $r = $this->dbAdapter->fetchAll("SELECT id, title, description, image FROM tb_information");
        foreach($r as $row)
        {
            $this->dbAdapter->fetchRow("SELECT lo_export(".$row['image'].", '".getcwd()."/img/inf/".$row['image']."')");
            $row['image'] = getcwd()."/img/inf/".$row['id'];
        }
        return $r;
    }

    /**
     * \brief Obtenemos una lista de noticias.
     *
     * @param $sImageDir Directorio donde se guardarán las imágenes.
     *
     * @return Lista de noticias. Cada registro está ordenado así: [id, title, description, image]
     *
     */
    public function listNews($sImageDir)
    {
        $r = $this->dbAdapter->fetchAll("SELECT id, title, description, image FROM tb_news");
        foreach($r as $row)
        {
            $this->dbAdapter->fetchRow("SELECT lo_export(".$row['image'].", '"$sImageDir."/".$row['image']."')");
            $row['image'] = getcwd()."/img/news/".$row['id'];
        }
        return $r;
    }

    /**
     * \brief Actualiza una cuenta de administrador en la base de datos.
     *
     * @param $sCC Cédula del administrador.
     * @param $sNames Nombres del administrador.
     * @param $sLastNames Apellidos del administrador.
     * @param $sTelephone Teléfono del administrador.
     * @param $sMovil Celular del administrador.
     * @param $sImage Ruta a la imagen donde está el administrador. Este parámetro es opcional.
     *
     */
    public function updateAdmin($sCC, $sNames, $sLastNames, $sTelephone, $sMovil, $sImage = '')
    {
        $sImage = $sImage==''?$sImage:"'$sImage'";
        $this->dbAdapter->fetchRow("SELECT * FROM f_updateuser('$sCC', '$sNames', '$sLastNames', '$sTelephone', '$sMovil', $sImage)");
    }

    /**
     * \brief Actualiza una cuenta de cliente en la base de datos.
     *
     * @param $sCC Cédula del cliente.
     * @param $sNames Nombres del cliente.
     * @param $sLastNames Apellidos del cliente.
     * @param $sTelephone Teléfono del cliente.
     * @param $sMovil Celular del cliente.
     * @param $sImage Ruta a la imagen donde está el cliente. Este parámetro es opcional.
     *
     */
    public function updateClient($sCC, $sNames, $sLastNames, $sTelephone, $sMovil, $sImage = '')
    {
        $sImage = $sImage==''?$sImage:"'$sImage'";
        $this->dbAdapter->fetchRow("SELECT * FROM f_updateuser('$sCC', '$sNames', '$sLastNames', '$sTelephone', '$sMovil', $sImage)");
    }

    /**
     * \brief Actualiza una cuenta de programador en la base de datos.
     *
     * @param $sCC Cédula del programador.
     * @param $sNames Nombres del programador.
     * @param $sLastNames Apellidos del programador.
     * @param $sTelephone Teléfono del programador.
     * @param $sMovil Celular del programador.
     * @param $sImage Ruta a la imagen donde está el programador. Este parámetro es opcional.
     *
     */
    public function updateDeveloper($sCC, $sNames, $sLastNames, $sTelephone, $sMovil, $sImage = '')
    {
        $sImage = $sImage==''?$sImage:"'$sImage'";
        $this->dbAdapter->fetchRow("SELECT * FROM f_updateuser('$sCC', '$sNames', '$sLastNames', '$sTelephone', '$sMovil', $sImage)");
    }

    /**
     * \brief Cambia la contraseña de un usuario.
     *
     * @param $sCC Cédula del usuario al que cambiaremos la contraseña.
     * @param $sNewPassword Nueva contraseña del usuario. La contraseña deberá ser pasada en encriptada, con el algoritmo de encriptación SHA1.
     *
     */
    public function updatePassword($sCC, $sNewPassword)
    {
        $thsis->dbAdapter->fetchRow("UPDATE tb_user SET password='$sNewPassword' WHERE cc='$sCC'");
    }

    /**
     * \brief Obtenemos el tipo de usuario (nombre) con base en el identificador.
     * Cada tipo de usuario tiene un nombre y un identificador único.
     * Ejemplo: Nombre 'Desarrollador', identificador 3
     * Este método retorna el nombre asociado a un identificador de tipo.
     *
     * @return Nombre asociado a un identificador de tipo.
     *
     */
    public function _userType($iId)
    {
        $r = $this->dbAdapter->fetchRow("SELECT name FROM tb_usertype WHERE id=$iId");
        return $r['name'];
    }

    /**
     * \brief Obtenemos el tipo del usuario en el momento de autenticarse.
     *
     * @param $sCCUser Cédula del usuario a validar.
     * @param $sPassword Contraseña del usuario. La contraseña deberá ser pasada en encriptada, con el algoritmo de encriptación SHA1.
     *
     * @return Si el usuario y la contraseña coinciden, se retornará el tipo de usuario; de lo contrario se retornará null.
     *
     */
    public function userType($sCCUser, $sPassword)
    {
        $r = $this->dbAdapter->fetchRow("SELECT id_usertype AS type FROM tb_user WHERE cc='$sCCUser' AND password='$sPassword'");
        return $r['type'];
    }

}
