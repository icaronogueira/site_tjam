<?php
/**
 * @package    Joomla.Libraries
 *
 * @copyright  Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

/**
 * TRIBUNAL DE JUSTIÇA DO ESTADO DO AMAZONAS
 * Divisão da Tecnologia da Informação e Comunicação (DVTIC)
 * Setor de Desenvolvimento de Sistemas (SDS)
 * Projeto: Joomla Internet 2017
 * Arquivo: Classe para realizar chamadas de funções remotas de APIs tipo REST.
 *
 */

class RESTApi {
	const NOT_KEYBOARD_CHARS = '/[^A-Za-z0-9 ºª!?\/@#$%^~`\'&*_\-().;,:<>"\\\[\]{}\-]/';   // nega (^) todos os caracteres de teclado
	
	private $_username='';
	private $_password='';	
	
	private $_response='';
	
	private $_data=array(); // tipo nome => valor
	public $erro='';
	
	/**
	 * @param string $username Nome do usuario autorizado a requisitar/enviar info via serviço REST
	 * @param string $password Senha do usuario autorizado a requisitar/enviar info via serviço REST
	 */
	public function __construct($username='admin@restuser', $password='admin@Access') {
		$this->_username = $username;
		$this->_password = $password;
	}
	
	/**
	 * Compara duas strings
	 * 
	 * @param string $string1
	 * @param string $string2
	 * @return string
	 */
	private function _whatsDiff($string1, $string2) {
		// explodindo as variaveis :)
		$arr1 = explode(" ", $string1);
		$arr2 = explode(" ", $string2);
		$dife1 = $arr1;
		$dife2 = $arr2;
		for ($i1 = 0; $i1 <= sizeof($arr1); $i1++) {
			for ($i2 = 0; $i2 <= sizeof($arr2); $i2++) {
				if($arr1[$i1] == $arr2[$i2]){
					unset($dife1[$i1]);
					unset($dife2[$i2]);
				}
			}
		}
		return 
			"A string 1 possui: <b>  " . implode(" ", $dife1) . "</b>  que a 2 não tem. <br />" . 
			"A string 2 possui: <b>  " . implode(" ", $dife2) . "</b>  que a 1 não tem. <br />";		
	}
	
	/**
	 * Transforma o formato json em formato nome => valor.
	 * 
	 * @return array
	 */
	public function getData() {
		return json_decode($this->_data);
	}
	
	/**
	 * Transforma o formato nome => valor em formato json. 
	 * 
	 * @param array $data
	 */
	public function setData($data=array()) {
		$this->_data = json_encode($data);
	}
	
	/**
	 * @return mixed
	 */
	public function response() {		
		return $this->_response <> '' ? json_decode($this->_response) : '';
	}

	/**
	 * Utiliza a função curl para enviar uma requisição 
	 * do tipo 'type' (default: GET), guardando o resultado
	 * na variável _response, que pode ser lida pela função 
	 * pública response().
	 * 
	 * @param string $url
	 * @param string $type
	 * @return boolean
	 */
	public function request($url, $type='GET') {
		$user_agent = "Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.130 Safari/537.36";
		
		// tratamento da URL: decompor e recompor removendo a porta...
		$url_array = parse_url($url);
	
		if(array_key_exists('scheme', $url_array))
			$scheme = $url_array['scheme'] ? $url_array['scheme'] . '://' : '';
		else
			$scheme = '';
	
		$host = array_key_exists('host', $url_array) ? $url_array['host'] : 'localhost';
		$path = array_key_exists('path', $url_array) ? $url_array['path'] : '';
	
		if(array_key_exists('port', $url_array))
			$port = is_integer($url_array['port']) ? $url_array['port'] : 80;
		else
			$port = 80;
		
		if(array_key_exists('query', $url_array)) { 
			$query = '?' . $url_array['query'];
		} else {
			$query = '';
		}
	
		$urlcurl = $scheme . $host . $path . $query;
	
		$ch = curl_init($urlcurl);    // initialize curl handle
	
		if($ch <> false) {
			switch ($type) {
				case 'POST' : // tipos de requisição que precisam de dados embutidos...
				case 'PUT' :
					$ret1 = curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_data);
					break;				
				case 'DELETE' : // tipos de requisição que não precisam de dados embutidos...
				case 'GET' : 
					$ret1 = true;
					break;
				default : 
					$ret1 = false;
					break;
			}
			$ret2 = curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
			$ret3 = curl_setopt($ch, CURLOPT_VERBOSE, true);
			$ret4 = curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Accept: application/json', 
				'Accept-Charset: windows-1251,utf-8;q=0.7,*;q=0.7',
				'X-REST-USERNAME: ' . $this->_username, 
				'X-REST-PASSWORD: ' . $this->_password,
				'Content-Type: application/json',
			));
			$ret5 = curl_setopt($ch, CURLOPT_HEADER, false);
			$ret6 = curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // retornar em uma variavel
			$ret7 = curl_setopt($ch, CURLOPT_COOKIESESSION, true);  // marcar como uma nova sessao.
			$ret8 = curl_setopt($ch, CURLOPT_PORT, (int) $port);   // numero da porta do servico
			$ret9 = curl_setopt($ch, CURLOPT_TIMEOUT, 10); // limite de tempo: 10s
			$ret10 = curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);  // configura o user agent
			$ret11 = curl_setopt($ch, CURLOPT_BUFFERSIZE, 10240); // tamanho do buffer
			$ret12 = curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // limite de tempo de conexão
			
			if($ret1 && $ret2 && $ret3 && $ret4 && $ret5 && $ret6 && 
			   $ret7 && $ret8 && $ret9 && $ret10 && $ret11 && $ret12) {
				
				$curl_response = curl_exec($ch);
				
				if($curl_response === false ) { // se não pôde executar o curl...
					$info = curl_getinfo($ch);
					curl_close($ch); // fecha a sessao do curl
					$this->erro = 'ERRO: curl_exec ao tentar acessar a URL ' . $url; // ...salva o erro
					$this->_response = '';
					return false;
				} else {  // ...ainda podem retornar erros na resposta, mas foi possivel executar...
					curl_close($ch); // fecha a sessao do curl
					$this->erro = ''; // nenhum erro encontrado
					$this->_response = preg_replace(self::NOT_KEYBOARD_CHARS,'', $curl_response); // remova os caracteres que não são do teclado 
					return true;
				}
			} else {
				$this->erro = 'ERRO: curl_setopt' ;
				return false;
			}
		} else {
			$this->erro = 'ERRO: curl_init';
			return false;
		}
	}
	
}