<?php 

/**
 * @package battlefield-3-statistics
 * @version 1.0.1
 * @license GPLv2 - http://www.gnu.org/licenses/gpl-2.0.html
 * @author Jeroen Weustink
 *
 */

class api
{
	private $_ident 	= 'TprOtCfUVU';
	private $_key		= 'yqQwAiFA89FTk3XiK1OPKQebdw8O0wXn';
	
	/**
	 * Get data from battlefield API
	 * @param string $player
	 * @param string $platform
	 * @return array|string $response;
	 */
	public function getPlayer($player, $platform)
	{
		$client = $this->_updatePlayer($player, $platform);
		
		if($client !== false) {
			
			$postData = array(
				'player' 	=> $player,
				'opt' 		=> json_encode(array(
					'all' => true
				))
			);
			
			return $this->_curlRequest('player', $platform, $postData);
			
		} else {
			return false;
		}
	}
	
	private function _updatePlayer($player, $platform)
	{
		$ident	= get_option('ident', false);
		$key	= get_option('key', false);
		$ident	= false;
		$key	= false;
		
		if(!$key || !$ident) {
			
			$clientData = $this->_setupClient($player . '|' . $platform);
			$clientData	= json_decode($clientData, true);
			
			if($clientData['status'] == 'ok') {
				
				$ident	= $clientData['ident'];
				$key	= $clientData['key'];
				
				add_option('ident', $ident);
				add_option('key', $key);
				
			} else {
				return false;
			}
		}
		
		$postData	= array(
			'ident'			=> $ident,
			'time'			=> time(),
			'player'		=> $player
		);
		
		$update = json_decode($this->_curlRequestSigned('playerupdate', $platform, $postData, $key), true);
		if(in_array($update['task']['state'], array('queued', 'started'))){
			$this->_updatePlayer($player, $platform);
		}
		
		return array(
			'ident' => $ident,
			'key' => $key
		);
	}
	
	private function _setupClient($id) 
	{
		$data	= array(
			'ident'			=> $this->_ident,
			'time'			=> time(),
			'clientident'	=> null,
			'name'			=> $id,
			'player'		=> 'Bassfunker'
		);
		
		return $this->_curlRequestSigned('setupkey', 'global', $data, $this->_key);
	}
	
	private function _curlRequest($type, $platform, $postData)
	{
		$curl = curl_init('http://api.bf3stats.com/' . $platform . '/' . $type .'/');
		
		curl_setopt($curl,CURLOPT_HEADER,false);
		curl_setopt($curl,CURLOPT_POST,true);
		curl_setopt($curl,CURLOPT_USERAGENT,'BF3StatsAPI/0.1');
		curl_setopt($curl,CURLOPT_HTTPHEADER,array('Expect:'));
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_POSTFIELDS,$postData);
		
		$data		= curl_exec($curl);
		$statuscode	= curl_getinfo($curl,CURLINFO_HTTP_CODE);
		
		curl_close($curl);
		
		if($statuscode == 200) {
			return $data;
		} else {
			return false;
		}
	}
	
	private function _curlRequestSigned($type, $platform, $postData, $key)
	{
		$url		= array('+'=>'-','/'=>'_','='=>'');
		$postData	= strtr(base64_encode(json_encode($postData)), $url);
		$postData	= array(
			'data'	=> $postData,
			'sig'	=> strtr(base64_encode(hash_hmac('sha256', $postData, $key, true)), $url)
		);
		
		$curl	= curl_init('http://api.bf3stats.com/' . $platform . '/' . $type . '/');
		
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_USERAGENT, 'BF3StatsAPI/0.1');
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
		
		$data		= curl_exec($curl);
		$statuscode	= curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		curl_close($curl);
		
		if($statuscode == 200) {
			return $data;
		} else {
			return false;
		}
	}
}

?>