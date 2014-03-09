<?php defined('__HVE') or die('<tt>HansVon Engine</tt>');
ini_set('display_errors', TRUE);
/* ///////////Developed By
  _   _       ___   __   _   _____        _     _   _____   __   _  
 | | | |     /   | |  \ | | /  ___/      | |   / / /  _  \ |  \ | | 
 | |_| |    / /| | |   \| | | |___       | |  / /  | | | | |   \| | 
 |  _  |   / / | | | |\   | \___  \      | | / /   | | | | | |\   | 
 | | | |  / /  | | | | \  |  ___| |      | |/ /    | |_| | | | \  | 
 |_| |_| /_/   |_| |_|  \_| /_____/      |___/     \_____/ |_|  \_| 
*/
//  //     Manejador de Base de Datos para MSSQL (Edición para producción).
///////////////////////////////////////////////////////////////////
//  //       Autor : Hans Von Herrera Ortega
//  //     Versión : 1.0 Producción.
//  //      Bugs a : hans.php@gmail.com

class H_MSSQL{
	private $conectado = false; # De entrada no debería de estar conectado.
	private $conn;
	private $resultado;
	private $respuesta;
	public $with_htmlentities = true;
	private $sql;
	public $depurador = false;
	public $Merr=true; # Mostrar error 
	public static $instancias;
		
	function __construct() {
		if(!H_MSSQL::$instancias++){ 
			$this->conn = sqlsrv_connect
			(H_MSSQL_HOST, 
			array(	"Database"	=>H_MSSQL_BD,
					"UID"		=>H_MSSQL_USUARIO,
					"PWD"		=>H_MSSQL_CLAVE,
					"CharacterSet"  => 'UTF-8'));
			if($this->conn)
				$this->conectado = true;
			else
				die( print_r( sqlsrv_errors(), true));
		}else
			die("Hay mas de una instancia del objeto <b>'H_MSSQL'</b>.");
	}
	
	function __destruct() {
		if($this->conectado)
			sqlsrv_close($this->conn);
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
			if($this->resultado = sqlsrv_query($this->conn, $this->sql)){
				if($tipo=='fila'){
					$this->respuesta[0] = sqlsrv_fetch_object($this->resultado);
				}else if($tipo=='consulta'){
					$i=0;
					$this->respuesta = array();
					while($this->respuesta[] = sqlsrv_fetch_object($this->resultado)){
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
				else if($tipo==('update' || 'delete' || 'insert'))
					return sqlsrv_rows_affected($this->resultado);
			}else{
				die( print_r( sqlsrv_errors(), true) );
			}
		}
	}
}