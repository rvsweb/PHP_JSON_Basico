<!--Añadirle seguridad a los formularios -->
<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Objetivo : Volcar datos a las 'VISTAS' usando funciones 'MODELO' 
 */
class Controlador_usuarios extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('modelo_documentos'); // sacar todos los usuarios
    }

    /**
     * 1º Vista : Carga la vista 'login/view_login'
     * y luego el mensaje
     */
    public function index() {
        $this->load->model('modelo_documentos'); // sacar todos los usuarios
        $data['texto'] = "Bienvenido a la aplicación !";
        $data['documentos'] = $this->modelo_documentos->get_all_documentos();
        $this->load->view('panel/documentos_admin',$data);
//      $this->load->view('login/view_login', $data); // carga la vista login_seguro
    }

    /**
     * Crea objeto 'form_validation'
     * 
     * Añade las reglas al formulario usuario
     * Envia el mensaje al formulario usuario
     * Invoca el metodo 'check_login_modelo'
     * 
     */
    public function check_login() {
//Cargo la libreria y creo el OBJETO model_adm y model_adm para invocar sus metodos
        $this->load->model('modelo_usuarios'); // sacar todos los usuarios
//Cargo la libreria y creo el OBJETO - form_validation
        $this->load->library('form_validation');
//Personaliza los mensaje de errores      
        $this->form_validation->set_error_delimiters('<div class="error"> Atención : ', '</div>');
//Fijo las reglas
        $this->form_validation->set_rules('usr', 'Usuario', 'required|min_length[1]');
        $this->form_validation->set_rules('pass', 'Password', 'required|min_length[1]');
//Mensaje para las reglas    
        $this->form_validation->set_message("required", "El campo %s es obligatorio");
//Si la VALIDACION NO ES CORRECTA
        if ($this->form_validation->run() == FALSE) {

            $this->index(); // regresa index / probar con redirigir
            
        } else { // VALIDACION CORRECTA
//Crea objeto 'model_login' para invocar metodo "check_login_modelo"
//Recogo en Array los datos del modelo del formulario
            $datos_usuario = $this->modelo_usuarios->check_login_modelo(
                    $this->input->get_post("usr"), 
                    $this->input->get_post("pass"));
// "$datos_usuario" devuelve Array_String y entra en el If
            if ($datos_usuario != null) {
// Carga libreria Sessiones y crea objeto 
                $this->load->library('session');
//Recojo los datos del metodo check_login_modelo del Modelo
                $idusr = $datos_usuario["idusr"]; // array con los valores recuperados del formulario 
                $tipousr = $datos_usuario["tipousr"]; // array con los valores recuperados del formulario 
//Creo objeto $session , meto los 'String' -> iduser , tipouser, nombreusr como 'Array Asociativo"
                $session = array(
                    'idusr' => $idusr,
                    'tipousr' => $tipousr,
                    'nombreusr' => $this->input->get_post("usr")); // ♦ funcion global - coge el usr que viene del formulario - 'nombre'
//Agregamos los datos y guardamos la session del usuario
                $this->session->set_userdata($session); // se almacenan como array el seguimiento del usuario
//El objeto session tiene los datos de idusr , tipousr , nombreusr
//Comparo el $tipousr          
//                Usuario user
                if ($tipousr == 1) {

                    // Cargo la vista de Usuario convencional
                    $this->load->view("panel/panel_user");
                    
                    // Usuario administrador
                } else if ($tipousr == 0) {
                    
                    $this->load->model('modelo_documentos'); // sacar todos los usuarios
                    
                    $data["resultados"] = $this->modelo_usuarios->get_all_users();
                    
                    $data["documentos"] = $this->modelo_documentos->get_all_documentos();
                    
                    
                    $data["texto"] = "Todos los usuarios";
                    $data["texto2"] = "Todos los documentos";
                    
                    $this->load->view("panel/panel_admin", $data); // "V" carga todos los usuarios y texto en la vista
                }
                
            } else { // Datos No correctos (login fallido) reenvia login
                
                $data['error'] = "<p style='color: red;'>Nombre de usuario y/o contraseña incorrectos</p>";
                $this->load->view('view_login', $data);
            }
        }
    }
    
    
////////////////////////////////////////////////////////////////////////////////    
////////////////////////////////////////////////////////////////////////////////    
////////////////////////////////////////////////////////////////////////////////    
    
    /**
     * necesita insertar foto
     */
    public function user_add() {
    
//    Array asociativo coge le valor del formulario
    	$data = array('nombre' => $this->input->get_post('nombre'));
    
    	$insert = $this->modelo_usuarios->user_add($data); // el insert no hace falta
    
    	var_dump($insert);
    
    	echo json_encode(array("status" => TRUE)); // siempre enviara el json como una cadena correcta
    }
     
    /**
     * necesita insertar foto
     */
    public function user_add_() {
    
    	$data = array(
    			'nombre' => $this->input->get_post('nombre'),
    			'apellidos' => $this->input->get_post('apellidos'),
    			'password' => $this->input->get_post('password'),
    			//          'password' => $this->input->post('password2'),
    			'email' => $this->input->get_post('email'),
    			'telefono' => $this->input->get_post('telefono'),
    			'tipo' => $this->input->get_post('tipo'),
    			'fotografia' => $this->input->get_post('userfile'));
    
    
    	var_dump($insert);
    
    	echo json_encode(array("status" => TRUE)); // siempre enviara el json como una cadena correcta
    }
    
    public function user_update() {
    
    	$data = array(
    			'nombre' => $this->input->get_post('nombre'),
    			'apellidos' => $this->input->get_post('apellidos'),
    			'password' => $this->input->get_post('password'),
    			'password2' => $this->input->get_post('password2'),
    			'email' => $this->input->get_post('email'),
    			'telefono' => $this->input->get_post('telefono'),
    			'tipo' => $this->input->get_post('tipo'));
    
    	$insert = $this->model_user->user_add($data);
      
    	echo json_encode(array("status" => TRUE));
    }
    
    public function user_add_foto() {
    
    	//         Array con la configuracion de la foto
    	$config['upload_path'] = 'uploads/';
    	$config['allowed_types'] = 'gif|jpg|png';
    	$config['max_size'] = '120';
    	$config['max_width'] = '180';
    	$config['max_height'] = '180';
    
    	//         Carga la libreria con la configuracion
    	$this->load->library('upload', $config);
    
    	if (!$this->upload->do_upload()) { //do_upload devuelve false si produce error en el archivo
    		// Ha fallado la subida de la imagen
    		$data['error'] = $this->upload->display_errors();
    
    		$this->load->view("formularios/view_add_user", $data);
    		 
    	} else {
    		// Obtenemos todos los datos del 'filename' de la imagen subida
    		$upload_img_data = $this->upload->data(); //Array para obtener datos
    
    		$upload_img_name = $upload_img_data['file_name']; //devuelve el nombre y extension de la imagen
    		//            Funcion add_user() se encarga de obtener los datos del formulario y enviarlos
    		$r = $this->model_adm->add_user(
    				$this->input->post('nombre'),
    				$this->input->post('apellidos'),
    				$this->input->post('password'),
    				$this->input->post('telefono'),
    				$this->input->post('email'),
    				$this->input->post('tipo'),
    				$upload_img_name);
    
    		if ($r == 'ok') {
    
    			$data['mensaje'] = "Correcto";
    			$this->load->view('formularios/view_add_user', $data);
                  
    		} else if ($r == 'error') {
    
    			$data['mensaje'] = "Error";
    			$this->load->view('formularios/view_add_user', $data);
    		}
    	}
    }
    
    public function add_message_user() {
    	$this->load->library('form_validation');
    	$data['title'] = 'Crear una peticion';
    	$this->form_validation->set_rules('title', 'Título', 'required');
    	$this->form_validation->set_rules('text', 'Texto', 'required');
      
    	if ($this->form_validation->run() === FALSE) {
    
    		$this->load->view('templates/header', $data);
    		$this->load->view('news/create');
    		$this->load->view('templates/footer');
    
    		$nombre = $this->input->get_post('nombre');
    		$archivo = $this->input->get_post('fecha_subida');
    		$notas = $this->input->get_post('notas');
    	}
    }
    
    function show_add_user() {
    	$this->load->view('formularios/view_add_user'); // carga la vista
    }
    
    function show_delete_user($id = null) {
    	if ($id != null) {
    		$this->modelo_usuarios->delete_user($id);
    		redirect('');
    	}
    	$this->load->view('formularios/view_add_user'); // carga la vista
    }
    
    public function add_user() {
    
    	$this->load->library('form_validation');
    	//      Cambia delimitadores de error en los formularios
    	$this->form_validation->set_error_delimiters('<span class="error">', '</span>');
    
    	$this->form_validation->set_rules('nombre', 'Nombre de usuario', 'required');
    	$this->form_validation->set_rules('apellidos', 'Apellidos', 'required');
    	$this->form_validation->set_rules('password', 'Contraseña', 'required|matches[password2]');
    	$this->form_validation->set_rules('password2', 'Confirmación de contraseña 2', 'required');
    	$this->form_validation->set_rules('telefono', 'Telefono', 'required');
    	$this->form_validation->set_rules('email', 'Email', 'required');
    	$this->form_validation->set_rules('tipo', 'Tipo', 'required');
    	$this->form_validation->set_rules('userfile', 'Fotografia'); // por defecto 'userfile'
    
    	$this->form_validation->set_message('required', 'El campo %s es obligatorio');
    	$this->form_validation->set_message('matches', 'El campo %s debe coincidir con el campo %s');
    
    	if ($this->form_validation->run() == FALSE) {
    
    		$this->load->view('formularios/view_add_user');
    		 
    	} else {
    
    		//         Array con la configuracion de la foto
    		$config['upload_path'] = 'uploads/';
    		$config['allowed_types'] = 'gif|jpg|png';
    		$config['max_size'] = '120';
    		$config['max_width'] = '180';
    		$config['max_height'] = '180';
    
    		//         Carga la libreria con la configuracion
    		$this->load->library('upload', $config);
    
    		if (!$this->upload->do_upload()) { //do_upload devuelve false si produce error en el archivo
    			// Ha fallado la subida de la imagen
    			$data['error'] = $this->upload->display_errors();
    
    			$this->load->view("formularios/view_add_user", $data);
    
    		} else {
    			// Obtenemos todos los datos del 'filename' de la imagen subida
    			$upload_img_data = $this->upload->data(); //Array para obtener datos
    
    			$upload_img_name = $upload_img_data['file_name']; //devuelve el nombre y extension de la imagen
    			//            Funcion add_user() se encarga de obtener los datos del formulario y enviarlos
    			$r = $this->model_adm->add_user(
    					$this->input->get_post('nombre'),
    					$this->input->get_post('apellidos'),
    					$this->input->get_post('password'),
    					$this->input->get_post('telefono'),
    					$this->input->get_post('email'),
    					$this->input->get_post('tipo'),
    					$upload_img_name);
    
    			if ($r == 'ok') {
    
    				$data['mensaje'] = "Correcto";
    				$this->load->view('formularios/view_add_user', $data);
    				 
    			} else if ($r == 'error') {
    
    				$data['mensaje'] = "Error";
    				$this->load->view('formularios/view_add_user', $data);
    			}
    		}
    	}
    }
    
    /**
     *
     */
    function show_usuarios_id() {
    
    	$usuario_id = $this->uri->segment(3); // Obtiene 3 segmento de la direccion
    	$data['usuarios'] = $this->modelo_usuarios->show_usuarios(); // Almacena los objetos en el array
    	$data['usuario_id'] = $this->modelo_usuarios->show_usuarios_id($usuario_id); //
    	$this->load->view('pages/view_update_user', $data);
    }
    
    /**
     * Cambiar el modelo
     */
    function update_usuarios_id() {
    
    	$usuario_id = $this->input->get_post('usuario_id');
    
    	$upload_img_data = $this->upload->data(); //Array para obtener datos
    	$upload_img_name = $upload_img_data['file_name'];
    
    	$data = array(
    			$this->input->get_post('nombre'),
    			$this->input->get_post('apellidos'),
    			$this->input->get_post('password'),
    			$this->input->get_post('telefono'),
    			$this->input->get_post('email'),
    			$this->input->get_post('tipo'),
    			$upload_img_name);
    
    	$this->update_model->update_usuarios_id($usuario_id, $data);
    	$this->show_usuarios_id();
    }

}

