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
 * Arquivo: Classe para realizar algumas operacoes com datas.
 *
 */

/**
 * @author Marcus
 *
 */
class MMDateHelper {
	const HR_LOWER='00:00:00';
	const HR_GREATER='23:59:59';
	const DTHR_LOWER='1000-01-01 00:00:00';
	const DTHR_GREATER='9999-12-31 23:59:59';
	
	/* Tipos enumerado do Tempo Verbal: @see self::tvDthrIntervalo() */
	const tvPresente = 2;
	const tvPassado = 3;
	const tvFuturo = 4;
	
	/**
	 * Verifica se data estah dentro de um caso de nulidade.
	 *
	 * @param string $dt
	 * @return boolean
	 */
	public static function dtNula($dt) {
		return ($dt == '' ||
				$dt == null ||
				$dt == '0000-00-00 00:00:00' ||
				$dt == '0000-00-00' ||
				$dt == '0000-01-01 00:00:00' );
	}
	
	/**
	 * Verifica se uma data é valida.
	 *
	 * @param string $dthr Data hora em um $format (Ex: 'Y-m-d H:i:s' ou 'Y-m-d')
	 * @return boolean
	 */
	public static function dthrValida($dthr, $format='Y-m-d H:i:s') {
		$d = DateTime::createFromFormat($format, $dthr);
		return $d && ($d->format($format) == $dthr);
	}
	
	/**
	 * Retorna o primeiro dia do mes em relação à data $time.
	 *
	 * @param timestamp $time
	 * @return string Data no formato 'Y-m-d'
	 */
	public static function primeiroDiaMes($time) {
		return date('Y-m-d', strtotime('first day of this month', $time));
	}
	
	/**
	 * Retorna o primeiro dia do mes passado em relação à data $time.
	 *
	 * @param timestamp $time
	 * @return string Data no formato 'Y-m-d'
	 */
	public static function primeiroDiaMesPassado($time) {
		return date('Y-m-d', strtotime('first day of previous month', $time));
	}
	
	/**
	 * Retorna o ultimo dia do mes em relação à data $time.
	 *
	 * @param timestamp $time
	 * @return string Data no formato 'Y-m-d'
	 */
	public static function ultimoDiaMes($time) {
		return date('Y-m-d', strtotime('last day of this month', $time));
	}
	
	/**
	 * Retorna o ultimo dia do mes passado em relação à data $time.
	 *
	 * @param timestamp $time
	 * @return string Data no formato 'Y-m-d'
	 */
	public static function ultimoDiaMesPassado($time) {
		return date('Y-m-d', strtotime('last day of previous month', $time));
	}
	
	/**
	 * Retorna a data do ultimo domingo em relação a data $time.
	 *
	 * @param timestamp $time
	 * @return string Data no formato 'Y-m-d'
	 */
	public static function ultimoDomingo($time, $inclusive=true) {
		if(date('D', $time) == 'Sun' && $inclusive) // já é um domingo?
			return date('Y-m-d', $time);
		else
			return date('Y-m-d', strtotime('last sunday', $time));
	}
	
	/**
	 * Retorna a data do proximo sabado em relacao à data $time.
	 *
	 * @param timestamp $time
	 * @return string Data no formato 'Y-m-d'
	 */
	public static function proximoSabado($time, $inclusive=true) {
		if(date('D', $time) == 'Sat' && $inclusive) // já é um sábado?
			return date('Y-m-d', $time);
		else
			return date('Y-m-d', strtotime('next saturday', $time));
	}
	
	/**
	 * Retorna a data da proxima segunda em relacao à data $time.
	 *
	 * @param timestamp $time
	 * @return string Data no formato 'Y-m-d'
	 */
	public static function proximaSegunda($time, $inclusive=true) {
		if(date('D', $time) == 'Mon' && $inclusive) // já é segunda?
			return date('Y-m-d', $time);
			else
				return date('Y-m-d', strtotime('next monday', $time));
	}
	
	/**
	 * Retorna a data da proxima sexta em relacao à data $time.
	 *
	 * @param timestamp $time
	 * @return string Data no formato 'Y-m-d'
	 */
	public static function proximaSexta($time, $inclusive=true) {
		if(date('D', $time) == 'Fri' && $inclusive) // já é sexta?
			return date('Y-m-d', $time);
			else
				return date('Y-m-d', strtotime('next friday', $time));
	}
	
	/**
	 * Retorna o ultimo dia do ano em relação à data $time.
	 *
	 * @param timestamp $time
	 * @return string Data no formato 'Y-m-d'
	 */
	public static function ultimoDiaAno($time) {
		$ultimo = strtotime(date('Y', $time) . '-12-31');
		return date('Y-m-d', $ultimo);
	}
	
	/**
	 * Retorna o primeiro dia do ano em relação à data $time.
	 *
	 * @param timestamp $time
	 * @return string Data no formato 'Y-m-d'
	 */
	public static function primeiroDiaAno($time) {
		$primeiro = strtotime(date('Y', $time) . '-01-01');
		return date('Y-m-d', $primeiro);
	}
	
	/**
	 * Formata qualquer data hora válida como "d de {descricaomes} de Y".
	 *
	 * @param string $dthr No formato 'Y-m-d'
	 * @return string
	 */
	public static function formataDataBrExtenso($dthr) {
		if(!self::dtNula($dthr)) {
			$mesBr = array(
				'01' => 'Janeiro',
				'02' => 'Fevereiro',
				'03' => 'Março',
				'04' => 'Abril',
				'05' => 'Maio',
				'06' => 'Junho',
				'07' => 'Julho',
				'08' => 'Agosto',
				'09' => 'Setembro',
				'10' => 'Outubro',
				'11' => 'Novembro',
				'12' => 'Dezembro'
			);
			$date = new DateTime($dthr);
			$dia = $date->format('d');
			$mes = $date->format('m');
			$ano = $date->format('Y');
			return $dia . ' de ' . $mesBr[$mes] . ' de ' . $ano;
		} else {
			return '';
		}
	}
	
	/**
	 * Formata qualquer data hora válida como "d de {descricaomes} de Y às H:i".
	 *
	 * @param string $dthr No formato "Y-m-d H:i:s"
	 * @return string
	 */
	public static function formataDataHrBrExtenso($dthr) {
		if(!self::dtNula($dthr)) {
			$date = new DateTime($dthr);
			$hora = $date->format('H');
			$minuto = $date->format('i');
			return self::formataDataBrExtenso($dthr) . ' às ' . $hora . ':' . $minuto;
		} else {
			return '';
		}
	}
	
	/**
	 * Formata data como "d/m/Y"
	 *
	 * @param string $dthr No formato "Y-m-d"
	 * @return string
	 */
	public static function formataDataBr($dthr) {
		if(!self::dtNula($dthr)) {
			$date = new DateTime($dthr);
			$dia = $date->format('d');
			$mes = $date->format('m');
			$ano = $date->format('Y');
			return $dia . '/' . $mes . '/' . $ano;
		} else {
			return '';
		}
	}
	
	/**
	 * @param string $dthr No formato "d/m/Y"
	 * @return string
	 */
	public static function dataBr2Iso($dt) {
		$data = explode('/',$dt);
		if(array_key_exists(2,$data) && array_key_exists(1,$data) && array_key_exists(0,$data))
			return $data[2] . '-' . $data[1] . '-' . $data[0];
			else
				return $dt;
	}
	
	/**
	 * Transforma uma data "d/m/Y" para o formato "Y-m-d".
	 *
	 * @param string $dthr No formato "d/m/Y"
	 * @return string Data no formato "Y-m-d"
	 */
	public static function formataDataIso($dt) {
		$dtFinal = '';
		if(!self::dtNula($dt)) { // se não é nula...
			try { // tentativa de transformação...
				$date = new DateTime(self::dataBr2Iso($dt));
	
				$dia = $date->format('d');
				$mes = $date->format('m');
				$ano = $date->format('Y');
	
				$dtFinal = $ano . '-' . $mes . '-' . $dia;
			} catch (Exception $e) {
				$dtFinal = '';
			}
		}
		return $dtFinal;
	}
	
	/**
	 * Formata data hora como "d/m/Y H:i:s"
	 *
	 * @param string $dthr No formato "Y-m-d H:i:s"
	 * @return string
	 */
	public static function formataDataHrBr($dthr) {
		if(!self::dtNula($dthr)) {
			$date = new DateTime($dthr);
			$hora = $date->format('H');
			$minuto = $date->format('i');
			return self::formataDataBr($dthr) . ' ' . $hora . ':' . $minuto;
		} else {
			return '';
		}
	}
	
	/**
	 * Verifica se o intervalo de data hora é válido e se está invertido
	 * (a dthr inicial ser maior que a dthr final).
	 *
	 * @param string $begin Data Hora em um $format (Ex: 'Y-m-d H:i:s' ou 'Y-m-d')
	 * @param string $end Data Hora em um $format (Ex: 'Y-m-d H:i:s' ou 'Y-m-d')
	 * @return null | boolean
	 */
	public static function dthrInvertida($begin, $end, $format='Y-m-d H:i:s') {
		if(self::dthrValida($begin, $format) && self::dthrValida($end, $format)) { // ambas validas...
			$intervalo = date_diff(date_create($begin), date_create($end), false);
			if($intervalo) {
				return $intervalo->invert; // 1 = invertido, 0 = não invertido
			} else { // inválido...
				return null;
			}
		} else { // inválido...
			return null;
		}
	}
	
	/**
	 * Verifica se o intervalo de data hora é válido.
	 *
	 * @param string $begin Data Hora em um $format (Ex: 'Y-m-d H:i:s' ou 'Y-m-d')
	 * @param string $end Data Hora em um $format (Ex: 'Y-m-d H:i:s' ou 'Y-m-d')
	 * @return boolean
	 */
	public static function dthrIntervaloOK($begin, $end, $format='Y-m-d H:i:s') {
		$invertida = self::dthrInvertida($begin, $end, $format);
		if($invertida === 1) {
			return false;
		} else {
			if($invertida === 0) {
				return true;
			} else { // null
				return false;
			}
		}
	}
	
	/**
	 * Calcula a diferença entre duas datas horas em termos de
	 * anos, meses, dias, horas, minutos e segundos.
	 *
	 * @param string $begin Data Hora no formato 'Y-m-d H:i:s'
	 * @param string $end Data Hora no formato 'Y-m-d H:i:s'
	 * @param boolean $out_in_array Se for para retorna um array (true) ou uma string (false)
	 * @return multitype array | string
	 */
	public static function dtDiferenca($begin, $end, $out_in_array=false){
		$intervalo = date_diff(date_create($begin), date_create($end));
		$out = $intervalo->format("Years:%Y,Months:%M,Days:%d,Hours:%H,Minutes:%i,Seconds:%s");
		if(!$out_in_array)
			return $out;
			$a_out = array();
			array_walk(explode(',',$out),
					function($val,$key) use(&$a_out){
						$v=explode(':',$val);
						$a_out[$v[0]] = $v[1];
					});
			return $a_out;
	}
	
	/**
	 * Formata $tmp em uma unidade mais legível (horas, minutos ou segundos)
	 *
	 * @param integer $tmp Tempo em segundos ou minutos
	 * @return string Valor de tempo mais fácil de ler, com sua respectiva unidade
	 */
	public static function tmpHumano($tmp) {
		if(is_null($tmp)) {
			$tmp = 0;
		}
		$unidade = array( 0 => 'seg.', 1 => 'min.', 2 => 'hora(s)');
		$i = 0;
		while($tmp > 60) {
			$tmp = round($tmp / 60, 0);
			$i++;
		}
		return $tmp . ' ' . $unidade[$i];
	}
	
	/**
	 * Verifica se $dthr pertence ao intervalo de datas-horas $begin e $end.
	 *
	 * @param string $dthr Data Hora no formato 'Y-m-d H:i:s'
	 * @param string $begin Data Hora no formato 'Y-m-d H:i:s'
	 * @param string $end Data Hora no formato 'Y-m-d H:i:s'
	 * @return boolean
	 */
	public static function dtEntre($dthr, $begin, $end, $exclusive=true) {
		$left = date_create($begin);
		$right = date_create($end);
		$value = date_create($dthr);
	
		switch($exclusive) {
			case false : return ($value >= $left && $value <= $right) || ($value <= $left && $value >= $right);
			default : return ($value > $left && $value < $right) || ($value < $left && $value > $right);
		}
	}
	
	/**
	 * Retorna o tempo verbal de um intervalo de data-hora.
	 *
	 * @param string $begin Data Hora no formato 'Y-m-d H:i:s'
	 * @param string $end Data Hora no formato 'Y-m-d H:i:s'
	 * @param boolean $exclusive
	 * @return Tempo Verbal
	 */
	public static function tvDthrIntervalo($begin, $end, $exclusive=true) {
		$left = date_create($begin);
		$right = date_create($end);
		$now = date_create();
		switch($exclusive) {
			case false :        // normal                         // invertido
				if (($now >= $left && $now <= $right) || ($now <= $left && $now >= $right)) {
					return self::tvPresente;
				} else if ($now >= $left && $now >= $right) {
					return self::tvPassado;
				} else {
					return self::tvFuturo;
				}
			break;
			default :
				if (($now > $left && $now < $right) || ($now < $left && $now > $right)) {
					return self::tvPresente;
				} else if ($now > $left && $now > $right) {
					return self::tvPassado;
				} else {
					return self::tvFuturo;
				}
			break;
		}
	}
	
	/**
	 * Verifica se a hora de $dthr pertence ao intervalo de horas entre $begin e $end.
	 *
	 * @param string $dthr Data Hora no formato 'Y-m-d H:i:s'
	 * @param string $begin Data Hora no formato 'H:i:s'
	 * @param string $end Data Hora no formato 'H:i:s'
	 * @return boolean
	 */
	public static function hrEntre($dthr, $begin, $end, $exclusive=true) {
		$left = strtotime($begin);
		$right = strtotime($end);
		$value = strtotime(date('H:i:s', strtotime($dthr)));
	
		switch($exclusive) {
			case false : return ($value >= $left && $value <= $right) || ($value <= $left && $value >= $right);
			default : return ($value > $left && $value < $right) || ($value < $left && $value > $right);
		}
	}
	
}