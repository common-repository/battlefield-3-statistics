<?php

/**
 * @package battlefield-3-statistics
 * @version 1.1
 * @license GPLv2 - http://www.gnu.org/licenses/gpl-2.0.html
 */

/*
Plugin Name: Battlefield 3 Statistics
Plugin URI: http://www.jeroenweustink.nl/battlefield-3-statistics/
Description: A widget that shows player data received from the bf3stats.com API. There are several options a user can enable and disable in the widget settings. Shown data will be: Progress, Ranking, Kill / death ratio, Win / lose ratio, Accuracy, Longest headshot. 
Author: Jeroen Weustink
Version: 1.1
Author URI: http://jeroenweustink.nl/
*/

class Battlefield_3_Statistics_Widget extends WP_Widget 
{
	/**
	 * Setup widget
	 */
	public function Battlefield_3_Statistics_Widget() 
	{
		parent::WP_Widget(false, $name = 'Battlefield 3 Statistics');
	}

	/**
	 * Output to show in widget
	 * @param array $args
	 * @param array $instance
	 */	
	public function widget($args, $instance) 
	{
		require 'classes/api.class.php';
		
		$data = array();
		$data['ident']	= 'TprOtCfUVU';
		$data['time']	= time();
		$data['player']	= 'The-Dutch-Guy';
		
		$player 	= (!empty($instance['player'])) ? $instance['player'] : false;
		$platform 	= (!empty($instance['platform'])) ? $instance['platform'] : false;
		
		if($player) {
			if($platform) {
				$playerData = get_option('playerData', null);
				
				if(get_option('player') != $instance['player']) {
					$playerData = null;
				}
				
				if(is_null($playerData)) {
					$api 		= new api();
					$playerData = $api->getPlayer($player, $platform);
					
					add_option('playerData', time() . '|' . $playerData);
					add_option('player', $player);
				} else {
					$tempData 	= $playerData;
					$tempData 	= explode('|', $tempData);
					$cacheTime	= $tempData[0];
					$playerData	= $tempData[1];
					
					if((time() - $cacheTime) > $instance['cacheTime']) {
						$playerData = $this->_getByApi($player, $platform);
						update_option('playerData', time() . '|' . $playerData);
					}
				}
				
				$playerData = json_decode($playerData, true);
				
				
				
				if($playerData['status'] == 'data') {
					if(is_array($playerData)) {
						$stats = array();
						$stats['img'] 				= $playerData['stats']['rank']['img_medium'];
						$stats['score_current']		= $playerData['stats']['rank']['score'];
						$stats['score_needed'] 		= $playerData['stats']['nextranks'][0]['score'];
						$stats['process'] 			= round($stats['score_current'] / ($stats['score_needed'] / 100), 2);
						$stats['kills'] 			= $playerData['stats']['global']['kills'];
						$stats['deaths'] 			= $playerData['stats']['global']['deaths'];
						$stats['kd-ratio'] 			= round($stats['kills'] / $stats['deaths'], 2);
						$stats['wins'] 				= $playerData['stats']['global']['wins'];
						$stats['losses'] 			= $playerData['stats']['global']['losses'];
						$stats['wl-ratio'] 			= round($stats['wins'] / $stats['losses'], 2);
						$stats['hits'] 				= $playerData['stats']['global']['hits'];
						$stats['shots'] 			= $playerData['stats']['global']['shots'];
						$stats['accuracy'] 			= round($stats['hits'] / ($stats['shots'] / 100), 2);
						$stats['longest-headshot']	= $playerData['stats']['global']['longesths'];
						$stats['last-update']		= date('d-m-Y H:i:s',$playerData['date_update']);
				
						require 'views/statistics.phtml';
					} else {
						$error = 'Could not get data from bf3stats.com';
						require_once 'views/error.phtml';
					}
				} else {
					$error = 'Api error: ' . $playerData['status'];
					require_once 'views/error.phtml';
					
					delete_option('player');
				}
			} else {
				$error = 'No player set';
				require_once 'views/error.phtml';
			}
		} else {
			$error = 'No player set';
			require_once 'views/error.phtml';
		}
	}

	/**
	 * Widget settings form
	 * @param array $instance
	 */
	public function form($instance) 
	{
		$player 			= (!empty($instance['player'])) ? $instance['player'] : '';
		$platform 			= (!empty($instance['platform'])) ? $instance['platform'] : '';
		$cacheTime 			= (!empty($instance['cacheTime'])) ? $instance['cacheTime'] : '1800';
		$cacheTimeValues 	= array(
			300		=> '5 min',
			600		=> '10 min',
			900		=> '15 min',
			1800	=> '30 min',
			3600	=> '60 min'
		);
		
		require 'views/form.phtml';
	}

	/**
	 * Update posted parameters from $this->form()
	 * @param array $newInstance
	 * @param array $oldInstance
	 * @return array $instance
	 */
	public function update($newInstance, $oldInstance)
	{
		$instance 				= $oldInstance;
		$instance['player'] 	= strip_tags($newInstance['player']);
		$instance['platform'] 	= $newInstance['platform'];
		$instance['cacheTime']	= $newInstance['cacheTime'];
		
		return $instance;
	}

	/**
	 * 
	 * Get playerData from bf3stats.com API
	 * @param string $player
	 * @param string $platform
	 * @return string 
	 */
	private function _getByApi($player, $platform)
	{
		$api = new api();
		return $api->getPlayer($player, $platform);
	}
}

add_action('widgets_init',create_function('', 'return register_widget("Battlefield_3_Statistics_Widget");'));

?>
