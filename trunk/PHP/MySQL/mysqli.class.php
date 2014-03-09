<?php
defined("__HVE") or die("<tt>HansVon Engine</tt>");
ini_set('display_errors', TRUE);
/* ///////////Developed By
  _   _       ___   __   _   _____        _     _   _____   __   _  
 | | | |     /   | |  \ | | /  ___/      | |   / / /  _  \ |  \ | | 
 | |_| |    / /| | |   \| | | |___       | |  / /  | | | | |   \| | 
 |  _  |   / / | | | |\   | \___  \      | | / /   | | | | | |\   | 
 | | | |  / /  | | | | \  |  ___| |      | |/ /    | |_| | | | \  | 
 |_| |_| /_/   |_| |_|  \_| /_____/      |___/     \_____/ |_|  \_| 
*/
//  //     Manejador de Base de Datos para MySQL (Edición para producción).
///////////////////////////////////////////////////////////////////
//  //       Autor : Hans Von Herrera Ortega
//  //     Versión : 2.0 Producción.
//  //      Bugs a : hans.php@gmail.com

class H_MYSQLi extends mysqli{
	private $conectado = false; # De entrada no debería de estar conectado.
	private $resultado;
	private $respuesta;
	public $with_htmlentities = true;
	private $sql;
	public $depurador = false;
	public $Merr=true; # Mostrar error 
	public static $instancias;
		
	function __construct() {
		if(!H_MYSQLi::$instancias++){ 
			@parent::__construct(H_MYSQL_HOST, H_MYSQL_USUARIO, H_MYSQL_CLAVE, H_MYSQL_BD);
			if (!$this->connect_error){
				$this->query("SET NAMES 'utf8'");# Charset UTF-8 
				$this->conectado = true;
			}
		}else
			die("Hay mas de una instancia del objeto <b>'H_MYSQL'</b>.");
	}
	function __destruct() {
		if($this->conectado)
			$this->close();
	}
	public function conectado(){
		return $this->conectado;
	}
	public function fila($sql,$depurar=0){
		$this->sql=$sql;
		return $this->iconsulta('fila', $depurar);
	}
	public function consulta($sql,$depurar=0){
		$this->sql=$sql;
		return $this->iconsulta('consulta', $depurar);
	}
	public function update($sql,$depurar=0){
		$this->sql=$sql;
		return $this->iconsulta('update', $depurar);
	}
	public function insert($sql,$depurar=0){
		$this->sql=$sql;
		return $this->iconsulta('insert', $depurar);
	}
	public function delete($sql,$depurar=0){
		$this->sql=$sql;
		return $this->iconsulta('delete', $depurar);
	}
	private function iconsulta($tipo, $depurar){
		if($this->conectado && $this->sql){
			$this->resultado = false;
			
			if($this->resultado = $this->query($this->sql)){
				if($tipo=='fila'){
					$this->respuesta[0] = $this->resultado->fetch_object();
				}else if($tipo=='consulta'){
					$i=0;
					$this->respuesta = array();
					while($this->respuesta[] = $this->resultado->fetch_object()){
						$i++;
						if($i > 1000) 
							die("<b>FATAL:</b>  Su consulta genera mas de mil registros.");
					}
					array_pop($this->respuesta);
				}
				
				if($tipo=='fila')
					return $this->respuesta[0];
				else if($tipo=='consulta')
					return $this->respuesta;
				else if($tipo=='insert')
					return ($this->insert_id)?$this->insert_id:$this->affected_rows;
				else if($tipo==('update' || 'delete'))
					return $this->affected_rows;
			}
		}
	}
}