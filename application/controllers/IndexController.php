<?php

class IndexController extends Zend_Controller_Action
{
    private $session = null;

    private $auth = null;

    private $form = null;
    
    private $sql=null;

    /**
      * \brief Contruye las variables de la clase
      *
      * @return N/A
      *
      */

    public function init()
    {
      $this->sql = new Application_Model_SQL();
      $this->session=new Zend_Session_Namespace('Users');
      $this->form=$this->loginForm();
      $this->auth = Zend_Auth::getInstance();
        
    }

    /**
     * \brief Formulario para crear usuario
     *
     * @return Formulario a action createUser
     *
     */

    public function createUserForm()
    {
    $form=new Zend_Form;
    $form->setAttrib('class','createuser');
    $form->setAction($this->view->url(array("controller" => "index", "action" => "create-user")))
	 ->setMethod('post');
    
    $image = new Zend_Form_Element_File('user');
    $image->setLabel('Load the image')
	  ->setDestination(APPLICATION_PATH."/../public/img/usr")
	  ->setMaxFileSize(2097152); // limits the filesize on the client side	  
    $image->addValidator('Count', false, 1);                // ensure only 1 file
    $image->addValidator('Size', false, 2097152);            // limit to 2 meg
    $image->addValidator('Extension', false, 'jpg,jpeg,png,gif');// only JPEG, PNG, and GIFs

    $form->addElement($image);

    $form->addElement('text','cc',array('label'=>'CC','required'=>true,'validator'=>'StringLength',false,array(6,10),'validator'=>'alnum'));

    $form->addElement('password','password',array('label'=>'Password','required'=>true,'validator'=>'StringLength',false,array(6,40)));

    $form->addElement('password','verifypassword',array('label'=>'Verify Password','required'=>true,'validator'=>'StringLength',false,array(6,40)));

    $form->addElement('text','names',array('label'=>'Names','required'=>true,'filter'=>'StringToLower','validator'=>'alfa','validator'=>'StringLength',false,array(4,25)));

    $form->addElement('text','lastnames',array('label'=>'Last Names','required'=>true,'filter'=>'StringToLower','validator'=>'alfa','validator'=>'StringLength',false,array(4,25)));

    $form->addElement('text','telephone',array('label'=>'Telephone','validator'=>'digits','validator'=>'StringLength',false,array(0,7)));

    $form->addElement('text','movil',array('label'=>'Movil','validator'=>'digits','validator'=>'StringLength',false,array(0,10)));

    $form->addElement('submit','create',array('label'=>'Create'));
    
    return $form;
    }

    /**
     * \brief Formulario para crear noticia
     *
     * @return Formulario a action createNews
     *
     */

    public function createNewsForm()
    {
    $form=new Zend_Form;
    $form->setAttrib('class','createnews');
    $form->setAction($this->view->url(array("controller" => "index", "action" => "create-news")))
	 ->setMethod('post');
    
    $image = new Zend_Form_Element_File('image');
    $image->setLabel('Load the image')
	  ->setDestination(APPLICATION_PATH."/../public/img/news")
	  ->setMaxFileSize(2097152); // limits the filesize on the client side	  
    $image->addValidator('Count', false, 1);                // ensure only 1 file
    $image->addValidator('Size', false, 2097152);            // limit to 2 meg
    $image->addValidator('Extension', false, 'jpg,jpeg,png,gif');// only JPEG, PNG, and GIFs

    $form->addElement($image);

    $form->addElement('text','title',array('label'=>'Title','required'=>true,'validator'=>'StringLength',false,array(1,20),'validator'=>'alnum'));

    $form->addElement('textarea','description',array('label'=>'Description','required'=>true));

    $form->addElement('submit','create',array('label'=>'Create'));
    
    return $form;
    }


    /**
     * \brief Formulario para autenticar el usuario.
     *
     * @return Formulario a action login
     *
     */

    public function loginForm()
    {
    $form=new Zend_Form;
    $form->setAttrib('class','login');
    $form->setAction($this->view->url(array("controller" => "index", "action" => "login")))
	 ->setMethod('post');
    
    $form->addElement('text','user',array('label'=>'User','required'=>true,'filter'=>'StringToLower','validator'=>'StringLength',false,array(6,10),'validator'=>'alnum','validator'=>'regex', false, array('/^[a-z]+/')));

    $form->addElement('password','password',array('label'=>'Password','required'=>true,'validator'=>'StringLength',false,array(6,40)));
	 
    $form->addElement('submit','login',array('label'=>'Login'));
    
    return $form;
    }

    /**
     * \brief action para noticias e ingreso de usuario
     *
     * @return N/A
     *
     */


    public function indexAction()
    {
	$this->view->headTitle("Inicio");
	
	if(!$this->auth->hasIdentity())
        {
	    echo $this->form;
        }
	elseif( $this->session->type == '1')
	{
	echo "<div id='adminMenu' class='menu'>

	    <div>
	      <span>Cuentas</span>
	      <ul>
		<a href=".$this->view->url(array('controller'=>'index', 'action'=>'create-user','id'=>'1')).">Crear Cuenta Administrador</a><br>
		<a href=".$this->view->url(array('controller'=>'index', 'action'=>'create-user','id'=>'2')).">Crear Cuenta Cliente</a><br>
		<a href=".$this->view->url(array('controller'=>'index', 'action'=>'create-user','id'=>'3')).">Crear Cuenta Desarrollador</a><br>
	      </ul>
	    </div>

	    <div>
	      <span>Noticias</span>	
	      <ul>
		<a href=".$this->view->url(array('controller'=>'index', 'action'=>'create-news')).">Crear Noticia</a><br>
		<a href=".$this->view->url(array('controller'=>'index', 'action'=>'update-news')).">Modificar Noticia</a><br>
		<a href=".$this->view->url(array('controller'=>'index', 'action'=>'delete-news')).">Borrar Noticia</a><br>
	      </ul>
	    </div>
	</div>";
	}

	$this->view->news = $this->sql->listNews();

	}

    /**
     * \brief action para auntenticacion del usuario
     *
     * @return N/A
     *
     */

    public function loginAction()
    {
	
	if(!$this->getRequest()->isPost())
	{	    
	    echo $this->form;
	    return;
	}
	if(!$this->form->isValid($this->_getAllParams()))
	{
	    echo $this->form;
	    return;
	}

	$values = $this->form->getValues();

	$sCCUser=$values['user'];
	$sPassword=sha1($values['password']);
	
	$authAdapter = $this->sql->getAuthDbTable('tb_user','cc','password');
                
        $authAdapter->setIdentity($sCCUser);
        $authAdapter->setCredential($sPassword);

	$result = $this->auth->authenticate($authAdapter);

 	$this->session->type=$this->sql->userType($sCCUser, $sPassword);
	$this->session->user=$sCCUser;

	if(!$result->isValid())
	{           
    
	    if($this->session->type==null)
	    {
		echo "<h4 id='error' class='login'>El usuario o la contraseña no coincide</h4>";
		echo $this->form;
	    }
	}
	else
	    $this->_helper->redirector('index', 'index');
	    return;
    }

    /**
     * \brief action para borrar session
     *
     * @return N/A
     *
     */

    public function logoutAction()
    {
	Zend_Auth::getInstance()->clearIdentity();
	$this->_helper->redirector('index', 'index');
    }

    
    /**
     * \brief action para crear usuario
     *
     * @return N/A
     *
     */

   public function createUserAction()
   { 
    
    $iUserType = $this->getRequest()->getParam('id');

    $form = $this->createUserForm();

	if(!$this->getRequest()->isPost())
	{
	    echo "<h4 id='infusr'>Datos del Usuario</h4>";
	    echo $form;
	    return;
	}
	if(!$form->isValid($this->_getAllParams()))
	{
	    echo $form;
	    return;
	}

	$values = $form->getValues();

	if($values['password'] != $values['verifypassword'])
	{
	echo "La contraseña no coincide";
	echo $form;
	return;
	}

	if(isset($values['user']))
        {
            $image = APPLICATION_PATH."/../public/img/usr/".$form->user->getFileName(null,false);
        } 
	else 
	{
	    $image = '';
        }

	switch ($iUserType) 
	{
	    case 1:

		$this->sql->insertAdmin($values['cc'],sha1($values['password']),$values['names'],$values['lastnames'],$values['telephone'],$values['movil'],$image);
		break;

	    case 2:

		$this->sql->insertClient($values['cc'],sha1($values['password']),$values['names'],$values['lastnames'],$values['telephone'],$values['movil'],$image);
		break;

	    case 3:

		$this->sql->insertDeveloper($values['cc'],sha1($values['password']),$values['names'],$values['lastnames'],$values['telephone'],$values['movil'],$image);
		break;
	}
     $this->_helper->redirector('index', 'index');
   }

    /**
     * \brief action para modificar usuario
     *
     * @return N/A
     *
     */

  public function updateUserAction()
  {
      $iUserType = $this->getRequest()->getParam('id');

    $form = $this->createUserForm();

	if(!$this->getRequest()->isPost())
	{
	    echo "<h4 id='infusr'>Nuevos Datos De Usuario</h4>";
	    echo $form;
	    return;
	}
	if(!$form->isValid($this->_getAllParams()))
	{
	    echo $form;
	    return;
	}

	$values = $form->getValues();

	if($values['password'] != $values['verifypassword'])
	{
	echo "La contraseña no coincide";
	echo $form;
	return;
	}

	if(isset($values['user']))
        {
            $image = APPLICATION_PATH."/../public/img/usr/".$form->user->getFileName(null,false);
        } 
	else 
	{
	    $image = '';
        }

	switch ($iUserType) 
	{
	    case 1:

		$this->sql->updateAdmin($values['cc'],sha1($values['password']),$values['names'],$values['lastnames'],$values['telephone'],$values['movil'],$image);
		break;

	    case 2:

		$this->sql->updateClient($values['cc'],sha1($values['password']),$values['names'],$values['lastnames'],$values['telephone'],$values['movil'],$image);
		break;

	    case 3:

		$this->sql->updateDeveloper($values['cc'],sha1($values['password']),$values['names'],$values['lastnames'],$values['telephone'],$values['movil'],$image);
		break;
	}
     $this->_helper->redirector('index', 'index');
  }

    /**
     * \brief action para crear noticia
     *
     * @return N/A
     *
     */

   public function createNewsAction()
   {
    $form = $this->createNewsForm();

	if(!$this->getRequest()->isPost())
	{
	    echo "<h4 id='infnews'>Datos de noticia</h4>";
	    echo $form;
	    return;
	}
	if(!$form->isValid($this->_getAllParams()))
	{
	    echo $form;
	    return;
	}
	$values = $form->getValues();
    
	if(isset($values['image']))
        {
            $image = APPLICATION_PATH."/../public/img/news/".$form->image->getFileName(null,false);
        } 
	else 
	{
	    $image = '';
        }
      
    $this->sql->insertNews($values['title'],$values['description'],$this->session->user,$image); 

    $this->_helper->redirector('index', 'index');
   }



}

?>