<?php
	
	function wpvr_substr( $str, $length = 15 ) {
		
		if ( $length <= 5 ) {
			return $str;
		}
		
		if ( strlen( $str ) >= $length ) {
			return substr( $str, 0, $length - 5 ) . ' (...)';
		}
		
		return $str;
	}
	
	function wpvr_get_available_post_types() {
		$post_types      = array();
		$forbidden_types = array(
			'attachment',
		);
		$types           = get_post_types( array(
			'public'             => true,
			'publicly_queryable' => true,
		), 'objects' );
		foreach ( (array) $types as $type ) {
			if ( ! in_array( $type->name, $forbidden_types ) ) {
				if ( $type->name == 'post' ) {
					$post_types[ $type->name ] = 'Regular Post (post)';
				} elseif ( $type->name == 'wpvr_video' ) {
					$post_types[ $type->name ] = 'WPVR Video (wpvr_video)';
				} else {
					$post_types[ $type->name ] = $type->labels->singular_name . ' (' . $type->name . ') ';
				}
			}
		}
		
		if ( ! isset( $post_types['wpvr_video'] ) ) {
			$post_types['wpvr_video'] = 'WPVR Video (wpvr_video)';
		}
		
		return $post_types;
	}
	
	function wpvr_encrypt_string( $q ) {
		$cryptKey = 'qJB0rGtIn5UB1xG03efyCp';
		$qEncoded = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q, MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
		
		return ( $qEncoded );
	}
	
	function wpvr_decrypt_string( $q ) {
		$cryptKey = 'qJB0rGtIn5UB1xG03efyCp';
		$qDecoded = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ), "\0" );
		
		return ( $qDecoded );
	}
	
	function wpvr_debug_echo( $str, $kint = false ) {
		echo '<pre style="margin:10px 0; padding:10px;border:1px dashed #CCC;background: bisque;width: 90%;overflow-x: auto;">';
		if ( $kint ) {
			d( $str );
		} else {
			print_r( $str );
		}
		echo '</pre>';
	}
	
	if ( ! function_exists( 'wpvr_retreive_video_id_from_param' ) ) {
		function wpvr_retreive_video_id_from_param( $param, $service ) {
			if ( $service == 'youtube' ) {
				////////////// YOUTUBE //////////////
				//https://youtu.be/uIi0xm_tlCU
				if ( strpos( $param, 'youtu.be' ) !== false ) {
					$separator = ( strpos( $param, 'https://' ) !== false ) ? 'https://youtu.be/' : 'http://youtu.be/';
					$x         = explode( $separator, $param );
					if ( ! isset( $x[1] ) ) {
						return false;
					} else {
						return $x[1];
					}
					
				} elseif ( strpos( $param, 'youtube.com' ) === false ) {
					return $param;
				} else {
					parse_str( parse_url( $param, PHP_URL_QUERY ), $args );
					if ( isset( $args['v'] ) ) {
						return $args['v'];
					} else {
						return false;
					}
				}
			} elseif ( $service == 'vimeo' ) {
				////////////// VIMEO //////////////
				if ( strpos( $param, 'vimeo.com' ) === false ) {
					return $param;
				} else {
					if ( strpos( $param, 'www.vimeo' ) === false ) {
						$separator = ( strpos( $param, 'https://' ) !== false ) ? 'https://vimeo.com/' : 'http://vimeo.com/';
					} else {
						$separator = ( strpos( $param, 'https://' ) !== false ) ? 'https://www.vimeo.com/' : 'http://www.vimeo.com/';
					}
					$x = explode( $separator, $param );
					if ( ! isset( $x[1] ) ) {
						return false;
					} else {
						$y = explode( '/', $x[1] );
						
						return $y[0];
					}
				}
			} elseif ( $service == 'facebook' ) {
				////////////// VIMEO //////////////
				if ( strpos( $param, 'facebook.com' ) === false ) {
					return $param;
				} else {
					$separator = '/videos/';
					$x         = explode( $separator, $param );
					if ( ! isset( $x[1] ) ) {
						return false;
					} else {
						$y = explode( '/', $x[1] );
						
						return $y[0];
					}
				}
			} elseif ( $service == 'dailymotion' ) {
				
				////////////// DAILYMOTION //////////////
				//http://dai.ly/x346uwt
				if ( strpos( $param, 'dai.ly' ) !== false ) {
					$separator = ( strpos( $param, 'https://' ) !== false ) ? 'https://dai.ly/' : 'http://dai.ly/';
					$x         = explode( $separator, $param );
					if ( ! isset( $x[1] ) ) {
						return false;
					} else {
						return $x[1];
					}
				} elseif ( strpos( $param, 'dailymotion.com' ) === false ) {
					return $param;
				} else {
					
					if ( strpos( $param, 'www.dailymotion' ) !== false ) {
						$separator = ( strpos( $param, 'https://' ) !== false ) ? 'https://www.dailymotion.com/video/' : 'http://www.dailymotion.com/video/';
					} else {
						$separator = ( strpos( $param, 'https://' ) !== false ) ? 'https://dailymotion.com/video/' : 'http://dailymotion.com/video/';
					}
					$x = explode( $separator, $param );
					if ( ! isset( $x[1] ) ) {
						return false;
					} else {
						$y = explode( '_', $x[1] );
						
						return $y[0];
					}
				}
				
			} elseif ( $service == 'ted' ) {
				
				////////////// TED //////////////
				if ( strpos( $param, 'ted.com' ) === false ) {
					return $param;
				} else {
					if ( strpos( $param, 'www.ted.com' ) !== false ) {
						$separator = ( strpos( $param, 'https://' ) !== false ) ? 'https://www.ted.com/talks/' : 'http://www.ted.com/talks/';
					} else {
						$separator = ( strpos( $param, 'https://' ) !== false ) ? 'https://ted.com/talks/' : 'http://ted.com/talks/';
					}
					$x = explode( $separator, $param );
					if ( ! isset( $x[1] ) ) {
						return false;
					} else {
						$y = explode( '/', $x[1] );
						
						return $y[0];
					}
				}
				
			} elseif ( $service == 'youku' ) {
				
				////////////// YOUKU //////////////
				if ( strpos( $param, 'youku.com' ) === false ) {
					return $param;
				} else {
					$separator = ( strpos( $param, 'https://' ) !== false ) ? 'https://v.youku.com/v_show/id_' : 'http://v.youku.com/v_show/id_';
					$x         = explode( $separator, $param );
					if ( ! isset( $x[1] ) ) {
						return false;
					} else {
						$y = explode( '.', $x[1] );
						
						return $y[0];
					}
				}
				
			} else {
				return $param;
			}
		}
	}
	
	if ( ! function_exists( 'wpvr_get_system_info' ) ) {
		function wpvr_get_system_info() {
			$php_version = explode( '+', PHP_VERSION );
			
			$meminfo   = wpvr_get_meminfo();
			$curl_info = curl_version();
			$infos     = array(
				
				'server'      => array(
					'label'  => __( 'Server Software', WPVR_LANG ),
					'value'  => '<br/>' . $_SERVER['SERVER_SOFTWARE'],
					'status' => '',
				),
				'php_version' => array(
					'label'  => __( 'PHP Version', WPVR_LANG ),
					'value'  => $php_version[0],
					'status' => version_compare( PHP_VERSION, WPVR_REQUIRED_PHP_VERSION, '>=' ) ? 'good' : 'bad',
				),
				
				'memory_available'   => array(
					'label'  => __( 'Memory Available', WPVR_LANG ),
					'value'  => $meminfo['available'] . 'M',
					'status' => $meminfo['available'] > 128 ? 'good' : 'bad',
				),
				'memory_limit'       => array(
					'label'  => __( 'PHP Memory Limit', WPVR_LANG ),
					'value'  => ini_get( 'memory_limit' ),
					'status' => '',
				),
				'post_max_size'      => array(
					'label'  => __( 'Post Max Size', WPVR_LANG ),
					'value'  => ini_get( 'post_max_size' ),
					'status' => '',
				),
				'max_input_time '    => array(
					'label'  => __( 'Maximum Input Time', WPVR_LANG ),
					'value'  => ini_get( 'max_input_time' ),
					'status' => '',
				),
				'max_execution_time' => array(
					'label'  => __( 'Maximum Execution Time', WPVR_LANG ),
					'value'  => ini_get( 'max_execution_time' ),
					'status' => '',
				),
				'safe_mode'          => array(
					'label'  => __( 'PHP Safe Mode', WPVR_LANG ),
					'value'  => ini_get( 'safe_mode' ) ? 'ON' : 'OFF',
					'status' => ini_get( 'safe_mode' ) ? 'bad' : 'good',
				),
				'cURL_status'        => array(
					'label'  => __( 'Curl Status', WPVR_LANG ),
					'value'  => function_exists( 'curl_version' ) ? 'ON' : 'OFF',
					'status' => function_exists( 'curl_version' ) ? 'good' : 'bad',
				),
				
				'curl_version' => array(
					'label'  => __( 'Curl Version', WPVR_LANG ),
					'value'  => $curl_info['version'],
					'status' => version_compare( $curl_info['version'], WPVR_REQUIRED_CURL_VERSION, '>=' ) ? 'good' : 'bad',
				),
				
				'allow_url_fopen' => array(
					'label'  => __( 'Allow URL Fopen', WPVR_LANG ),
					'value'  => ini_get( 'allow_url_fopen' ) == '1' ? 'ON' : 'OFF',
					'status' => ini_get( 'allow_url_fopen' ) == '1' ? 'good' : 'bad',
				),
				'openssl_status'  => array(
					'label'  => __( 'OpenSSL Extension', WPVR_LANG ),
					'value'  => extension_loaded( 'openssl' ) ? 'ON' : 'OFF',
					'status' => extension_loaded( 'openssl' ) ? 'good' : 'bad',
				),
				'wpvr_folder'     => array(
					'label'  => __( 'Plugin Folder', WPVR_LANG ),
					'value'  => WPVR_PATH,
					'status' => '',
				),
				'folder_writable' => array(
					'label'  => __( 'Plugin Folder Writable', WPVR_LANG ),
					'value'  => ( is_writable( WPVR_PATH ) === true ) ? 'ON' : 'OFF',
					'status' => ( is_writable( WPVR_PATH ) === true ) ? 'good' : 'bad',
				),
				'multisite'       => array(
					'label'  => __( 'WordPress MultiSite', WPVR_LANG ),
					'value'  => is_multisite() ? __( 'Enabled', WPVR_LANG ) : __( 'Disabled', WPVR_LANG ),
					'status' => '',
				),
			
			);
			
			$act  = wpvr_get_act_data( 'wpvr' );
			$wpvr = array(
				
				'wpvr_url' => array(
					'label'  => __( 'Website URL', WPVR_LANG ),
					'value'  => WPVR_SITE_URL,
					'status' => '',
				),
				
				'wpvr_version' => array(
					'label'  => __( 'WPVR Version', WPVR_LANG ),
					'value'  => WPVR_VERSION,
					'status' => '',
				),
				
				'wpvr_act_status' => array(
					'label'  => __( 'WPVR Activation Status', WPVR_LANG ),
					'value'  => $act['act_status'],
					'status' => '',
				),
				
				'wpvr_act_code' => array(
					'label'  => __( 'WPVR Activation Code', WPVR_LANG ),
					'value'  => $act['act_code'],
					'status' => '',
				),
				
				'wpvr_act_date' => array(
					'label'  => __( 'WPVR Activation Date', WPVR_LANG ),
					'value'  => $act['act_date'],
					'status' => '',
				),
				
				'wpvr_act_id' => array(
					'label'  => __( 'WPVR Activation ID', WPVR_LANG ),
					'value'  => $act['act_id'],
					'status' => '',
				),
			
			);
			
			return array(
				'sys'  => $infos,
				'wpvr' => $wpvr,
			);
			
		}
	}
	
	if ( ! function_exists( 'wpvr_add_multiple_post_meta' ) ) {
		function wpvr_add_multiple_post_meta( $post_id, $metas = array(), $only_new = false ) {
			global $wpdb;
			
			if ( count( $metas ) == 0 ) {
				return false;
			}
			
			$db_done    = false;
			$sql_insert = array();
			$sql_delete = array();
			
			$old_metas = get_post_meta( $post_id );
			foreach ( (array) $metas as $meta_key => $meta_value ) {
				
				$meta_key   = esc_sql( $meta_key );
				$meta_value = esc_sql( maybe_serialize( $meta_value ) );
				
				
				if (
					isset( $old_metas[ $meta_key ] )
					&& isset( $old_metas[ $meta_key ][0] )
					&& $old_metas[ $meta_key ][0] == $meta_value
				) {
					continue;
				}
				if ( isset( $old_metas[ $meta_key ] ) ) {
					//$sql_delete[ $meta_key ] = "(" . "'" . $post_id . "'," . "'" . $meta_key . "'," . "'" . $meta_value . "'" . ")";
					$sql_delete[ $meta_key ] = "( '{$post_id}' , '{$meta_key}' )";
				}
				$sql_insert[ $meta_key ] = "(" . "'" . $post_id . "'," . "'" . $meta_key . "'," . "'" . $meta_value . "'" . ")";
				
			}
			if ( $only_new === true ) {
				if ( count( $sql_delete ) != 0 ) {
					$db_done = $wpdb->query( "
			      DELETE FROM  {$wpdb->postmeta} 
			      WHERE (post_id , meta_key) IN ( " . implode( ", ", $sql_delete ) . " )
				" );
				}
			}
			
			if ( count( $sql_insert ) != 0 ) {
				$sql     = "
			      INSERT INTO $wpdb->postmeta (post_id , meta_key , meta_value) 
			      VALUES " . "\n" . implode( ",\n", $sql_insert ) . " ";
				$db_done = $wpdb->query( $sql );
				
				//_d( $sql );
				
			}
			
			return $db_done;
		}
	}
	
	if ( ! function_exists( 'wpvr_get_meminfo' ) ) {
		function wpvr_get_meminfo() {
			$data    = explode( "\n", file_get_contents( "/proc/meminfo" ) );
			$meminfo = array();
			foreach ( (array) $data as $line ) {
				list( $key, $val ) = explode( ":", $line );
				
				$val             = str_replace( ' kB', '', trim( $val ) );
				$meminfo[ $key ] = ceil( $val / 1000 );
			}
			
			// Memory in Mo
			return array(
				'available' => $meminfo['MemTotal'],
			);
		}
	}
	if ( ! function_exists( 'wpvr_render_system_info' ) ) {
		function wpvr_render_system_info( $info_blocks ) {
			$html = " WP Video Robot : SYSTEM INFORMATION \r\n";
			foreach ( (array) $info_blocks as $infos ) {
				$html .= "----------------------------------------------------------------- \r\n";
				foreach ( (array) $infos as $info ) {
					
					if ( is_bool( $info['value'] ) && $info['value'] === true ) {
						$info['value'] = "TRUE";
					} elseif ( is_bool( $info['value'] ) && $info['value'] === true ) {
						$info['value'] = "FALSE";
					}
					$html .= " - " . $info['label'] . " : " . $info['value'] . " \r\n";
				}
				$html .= "----------------------------------------------------------------- \r\n";
			}
			
			return $html;
		}
	}
	
	if ( ! function_exists( 'wpvr_get_service_labels' ) ) {
		function wpvr_get_service_labels( $data ) {
			global $wpvr_vs;
			if (
				! isset( $data['sourceService'] )
				|| ! isset( $wpvr_vs[ $data['sourceService'] ] )
				|| ! isset( $wpvr_vs[ $data['sourceService'] ]['types'][ $data['sourceType'] ] )
			) {
				return array(
					'service'       => '',
					'service_label' => '',
					'type'          => '',
					'type_label'    => '',
					'type_HTML'     => '',
				);
			}
			
			return array(
				'service'       => $wpvr_vs[ $data['sourceService'] ]['id'],
				'service_label' => $wpvr_vs[ $data['sourceService'] ]['label'],
				'type'          => $wpvr_vs[ $data['sourceService'] ]['types'][ $data['sourceType'] ]['id'],
				'type_label'    => $wpvr_vs[ $data['sourceService'] ]['types'][ $data['sourceType'] ]['label'],
				'type_HTML'     => wpvr_render_vs_source_type(
					$wpvr_vs[ $data['sourceService'] ]['types'][ $data['sourceType'] ],
					$wpvr_vs[ $data['sourceService'] ]
				),
			);
		}
	}
	
	if ( ! function_exists( 'wpvr_json_encode' ) ) {
		function wpvr_json_encode( $data, $utf = false ) {
			if ( $utf ) {
				$data = wpvr_utf8_converter( $data );
			}
			
			return json_encode( $data );
		}
	}
	
	if ( ! function_exists( 'wpvr_json_decode' ) ) {
		function wpvr_json_decode( $data, $utf = false ) {
			if ( $utf ) {
				$data = utf8_decode( $data );
			}
			$decoded = json_decode( $data );
			
			//$decoded = wpvr_utf8_recursive_decode( $decoded );
			return $decoded;
		}
	}
	
	
	if ( ! function_exists( 'wpvr_utf8_converter' ) ) {
		function wpvr_utf8_converter( $array ) {
			array_walk_recursive( $array, function ( &$item, $key ) {
				if ( is_string( $item ) && ! mb_detect_encoding( $item, 'utf-8', true ) ) {
					$item = utf8_encode( $item );
				}
			} );
			
			return $array;
		}
	}
	
	if ( ! function_exists( 'wpvr_utf8_recursive_encode' ) ) {
		function wpvr_utf8_recursive_encode( &$input ) {
			if ( is_string( $input ) ) {
				$input = utf8_encode( $input );
			} else if ( is_array( $input ) ) {
				foreach ( $input as &$value ) {
					wpvr_utf8_recursive_encode( $value );
				}
				unset( $value );
			} else if ( is_object( $input ) ) {
				$vars = array_keys( get_object_vars( $input ) );
				foreach ( $vars as $var ) {
					wpvr_utf8_recursive_encode( $input->$var );
				}
			}
		}
	}
	if ( ! function_exists( 'wpvr_object_to_array' ) ) {
		function wpvr_object_to_array( $obj ) {
			if ( is_object( $obj ) ) {
				$obj = (array) $obj;
			}
			if ( is_array( $obj ) ) {
				$new = array();
				foreach ( $obj as $key => $val ) {
					$new[ $key ] = wpvr_object_to_array( $val );
				}
			} else {
				$new = $obj;
			}
			
			return $new;
		}
	}
	
	if ( ! function_exists( 'wpvr_utf8_recursive_decode' ) ) {
		function wpvr_utf8_recursive_decode( &$input ) {
			if ( is_string( $input ) ) {
				$input = utf8_decode( $input );
			} else if ( is_array( $input ) ) {
				foreach ( $input as &$value ) {
					wpvr_utf8_recursive_decode( $value );
				}
				unset( $value );
			} else if ( is_object( $input ) ) {
				$vars = array_keys( get_object_vars( $input ) );
				foreach ( $vars as $var ) {
					wpvr_utf8_recursive_decode( $input->$var );
				}
			}
		}
	}
	
	if ( ! function_exists( 'render_source_insights' ) ) {
		function render_source_insights( $insights, $class = '' ) {
			?>
			
			<?php foreach ( (array) $insights as $insight ) { ?>
                <div
                        class="wpvr_source_insights_item pull-left <?php echo $class; ?>"
                        title="<?php echo $insight['title']; ?>"
                >
				<span class="wpvr_source_insights_item_icon">
					<i class="fa <?php echo $insight['icon']; ?>"></i>
				</span>
                    <span class="wpvr_source_insights_item_value">
					<?php echo $insight['value']; ?>
				</span>
                </div>
			<?php } ?>
            <div class="wpvr_clearfix"></div>
			
			<?php
		}
	}
	
	if ( ! function_exists( 'wpvr_d' ) ) {
		function wpvr_d( $debug_response, $separator = false ) {
			ob_start();
			d( $debug_response );
			$output = ob_get_clean();
			
			return $separator . $output . $separator;
		}
	}
	
	if ( ! function_exists( 'wpvr_is_theme' ) ) {
		function wpvr_is_theme( $name ) {
			$theme = wp_get_theme();
			
			$possible_names = array(
				$theme->stylesheet,
				$theme->template,
				$theme->parent,
				$theme->get( 'Name' ),
			);
			//d( $name ) ;
			//d( $possible_names ) ;
			return in_array( $name, $possible_names );
		}
	}
	
	//if ( ! function_exists( 'wpvr_object_to_array' ) ) {
	//	function wpvr_object_to_array( $obj ) {
	//		if ( is_object( $obj ) ) {
	//			$obj = (array) $obj;
	//		}
	//		if ( is_array( $obj ) ) {
	//			$new = array();
	//			foreach ( (array) $obj as $key => $val ) {
	//				$new[ $key ] = wpvr_object_to_array( $val );
	//			}
	//		} else {
	//			$new = $obj;
	//		}
	//
	//		return $new;
	//	}
	//}
	
	if ( ! function_exists( 'wpvr_chrono_time' ) ) {
		function wpvr_chrono_time( $start = false, $round = 6 ) {
			$time = explode( ' ', microtime() );
			if ( $start === false ) {
				return $time[0] + $time[1];
			} else {
				return round( wpvr_chrono_time() - $start, $round );
			}
			
			return true;
		}
	}
	
	if ( ! function_exists( 'wpvr_render_multiselect' ) ) {
		function wpvr_render_multiselect( $option, $value = null, $echo = true ) {
			if ( $echo === false ) {
				ob_start();
			}
			
			
			if ( is_string( $value ) ) {
				$option_value = stripslashes( $value );
			} else {
				$option_value = $value;
			}
			
			if ( isset( $option['tab_class'] ) ) {
				$tab_class = $option['tab_class'];
			} else {
				$tab_class = '';
			}
			
			$option_name = $option['id'];
			
			//new dBug( $option );
			
			if ( ! isset( $option['masterOf'] ) || ! is_array( $option['masterOf'] ) || count( $option['masterOf'] ) == 0 ) {
				$masterOf = '';
				$isMaster = '';
			} else {
				$masterOf = ' masterOf = "' . implode( ',', $option['masterOf'] ) . '" ';
				$isMaster = 'isMaster';
			}
			
			if ( ! isset( $option['masterValue'] ) ) {
				$masterValue = '';
			} else {
				$masterValue = ' masterValue = "' . $option['masterValue'] . '" ';
			}
			
			if ( ! isset( $option['hasMasterValue'] ) ) {
				$hasMasterValue = '';
			} else {
				$hasMasterValue = ' hasMasterValue = "' . $option['hasMasterValue'] . '" ';
			}
			
			if ( ! isset( $option['class'] ) ) {
				$option_class = '';
			} else {
				$option_class = $option['class'];
			}
			
			if ( ! isset( $option['values'] ) || ! is_array( $option['values'] ) ) {
				echo "NO OPTION DEFINED FOR THIS SELECT";
			} else {
				
				if ( isset( $option['source'] ) && $option['source'] == 'categories' ) {
					
					// GET ALL CATEGORIES
					$cats = wpvr_get_categories_count();
					foreach ( (array) $cats as $cat ) {
						$option['values'][ $cat['value'] ] = $cat['label'] . ' (' . $cat['count'] . ')';
					}
					
				} elseif ( isset( $option['source'] ) && $option['source'] == 'all_categories' ) {
					
					// GET ALL CATEGORIES
					$cats = wpvr_get_categories_count( false, true );
					foreach ( (array) $cats as $cat ) {
						$option['values'][ $cat['value'] ] = $cat['label'] . ' (' . $cat['count'] . ')';
					}
					
				} elseif ( isset( $option['source'] ) && $option['source'] == 'post_types' ) {
					
					// GET ALL POST TYPES
					$post_types = get_post_types( array(
						'public' => true,
					) );
					foreach ( (array) $post_types as $cpt ) {
						$option['values'][ $cpt ] = $cpt;
					}
					
					
				} elseif ( isset( $option['source'] ) && $option['source'] == 'taxonomies' ) {
					
					// GET ALL TAXONOMIES
					$taxonomies = get_taxonomies( array(
						'_builtin' => false,
					), 'objects' );
					foreach ( (array) $taxonomies as $tax ) {
						//new dBug( $tax );
						$option['values'][ $tax->name ] = $tax->label;
					}
					
					
				} elseif ( isset( $option['source'] ) && $option['source'] == 'post_types_ext' ) {
					$internal_cpts = array(
						//'page' ,
						'post',
						WPVR_VIDEO_TYPE,
						'attachment',
						'revision',
						WPVR_SOURCE_TYPE,
						'nav_menu_item',
					);
					// GET ALL POST TYPES
					$post_types = get_post_types( array(//'public' => true ,
					) );
					foreach ( (array) $post_types as $cpt ) {
						if ( ! in_array( $cpt, $internal_cpts ) ) {
							$option['values'][ $cpt ] = $cpt;
						}
					}
					
				} elseif ( isset( $option['source'] ) && $option['source'] == 'tags' ) {
					
					// GET ALL TAGS
					$tags = get_tags();
					foreach ( (array) $tags as $tag ) {
						$option['values'][ $tag->term_id ] = $tag->slug;
					}
					
				} elseif ( isset( $option['source'] ) && $option['source'] == 'authors' ) {
					
					// GET ALL AUTHORS
					$all_users = get_users( 'orderby=post_count&order=DESC' );
					foreach ( (array) $all_users as $user ) {
						if ( ! in_array( 'subscriber', $user->roles ) ) {
							$option['values'][ $user->data->ID ] = $user->data->user_nicename;
						}
					}
					
				} elseif ( isset( $option['source'] ) && $option['source'] == 'services' ) {
					
					// GET ALL AUTHORS
					global $wpvr_vs;
					foreach ( (array) $wpvr_vs as $vs ) {
						$option['values'][ $vs['id'] ] = $vs['label'];
					}
					
				}
			}
			
			if ( ! isset( $option['maxItems'] ) || $option['maxItems'] == 1 ) {
				$mv = "1";
			} elseif ( $option['maxItems'] === false ) {
				$mv = '255';
			} else {
				$mv = $option['maxItems'];
			}
			
			if ( ! isset( $option['placeholder'] ) || $option['placeholder'] == '' ) {
				$option['placeholder'] = 'Pick one or more values';
			}
			?>
            <div class="wpvr_select_wrap">
                <input type="hidden" value="0" name="<?php echo $option_name; ?>[]"/>
                <select
                        class="wpvr_field_selectize "
                        name="<?php echo $option_name; ?>[]"
                        id="<?php echo $option_name; ?>"
                        maxItems="<?php echo $mv; ?>"
                        placeholder="<?php echo $option['placeholder']; ?>"
                >
                    <option value=""> <?php echo $option['placeholder']; ?> </option>
					<?php foreach ( (array) $option['values'] as $oValue => $oLabel ) { ?>
						<?php
						
						if ( is_array( $option_value ) && in_array( $oValue, $option_value ) ) {
							$checked  = ' selected="selected" ';
							$oChecked = ' c="1" ';
							
						} elseif ( ! is_array( $option_value ) && $oValue == $option_value ) {
							$checked  = ' selected="selected" ';
							$oChecked = ' c="1" ';
						} else {
							$checked  = '';
							$oChecked = ' c="0" ';
						}
						?>
                        <option value="<?php echo $oValue; ?>" <?php echo $checked; ?> <?php echo $oChecked; ?> >
							<?php echo $oLabel; ?>
                        </option>
					<?php } ?>
                </select>
            </div>
			<?php
			
			if ( $echo === false ) {
				$rendered_option = ob_get_contents();
				ob_get_clean();
				
				return $rendered_option;
			}
			
		}
	}
	
	/* CHECKS IF A REMOTE FILE EXISTS */
	if ( ! function_exists( 'wpvr_get_folders_simple' ) ) {
		function wpvr_get_folders_simple() {
			$terms   = get_terms( WPVR_SFOLDER_TYPE, array( 'hide_empty' => false, ) );
			$folders = array();
			foreach ( (array) $terms as $term ) {
				$folders[ $term->term_id ] = $term->name . ' (' . $term->count . ') ';
			}
			
			return $folders;
		}
	}
	
	
	/* CHECKS IF A REMOTE FILE EXISTS */
	if ( ! function_exists( 'wpvr_touch_remote_file' ) ) {
		function wpvr_touch_remote_file( $url ) {
			$capi = wpvr_capi_remote_get( $url );
			if ( ! isset( $capi['status'] ) || $capi['status'] != 200 ) {
				return false;
			} else {
				return true;
			}
		}
	}
	
	/* CHECKS IF A REMOTE FILE EXISTS */
	if ( ! function_exists( 'wpvr_curl_check_remote_file_exists' ) ) {
		function wpvr_curl_check_remote_file_exists( $url ) {
			$ch = curl_init( $url );
			curl_setopt( $ch, CURLOPT_NOBODY, true );
			curl_exec( $ch );
			if ( curl_getinfo( $ch, CURLINFO_HTTP_CODE ) == 200 ) {
				$status = true;
			} else {
				$status = false;
			}
			curl_close( $ch );
			
			return $status;
		}
	}
	
	/* GETTING REAL TIME DIFF */
	if ( ! function_exists( 'wpvr_human_time_diff' ) ) {
		function wpvr_human_time_diff( $post_id ) {
			$post          = get_post( $post_id );
			$now_date_obj  = DateTime::createFromFormat( 'Y-m-d H:i:s', current_time( 'Y-m-d H:i:s' ) );
			$now_date      = $now_date_obj->format( 'U' );
			$post_date_obj = DateTime::createFromFormat( 'Y-m-d H:i:s', $post->post_date );
			$post_date     = $post_date_obj->format( 'U' );
			
			return human_time_diff( $post_date, $now_date ) . ' ago';
		}
	}
	
	/* GETTING ADD DATA FROM URL */
	if ( ! function_exists( 'wpvr_extract_data_from_url' ) ) {
		function wpvr_extract_data_from_url( $html, $searches = array() ) {
			$results = array();
			if ( count( $searches ) == 0 ) {
				return array();
			}
			foreach ( (array) $searches as $s ) {
				
				if ( $s['target_name'] === false ) {
					if ( $s['marker_double_quotes'] === true ) {
						$marker = '<' . $s['tag'] . ' ' . $s['marker_name'] . '="' . $s['marker_value'] . '"';
					} else {
						$marker = "<" . $s['tag'] . " " . $s['marker_name'] . "='" . $s['marker_value'] . "'";
					}
					$x = explode( $marker, $html );
					//d($x );
					if ( $x == $html ) {
						$results[ $s['target'] ] = false;
						continue;
					}
					
					$z = array_pop( $x );
					$y = explode( '</' . $s['tag'] . '>', $z );
					
					$tv                      = $y[0];
					$tv                      = str_replace( array( '<', '>', ',', ' ' ), '', $tv );
					$results[ $s['target'] ] = $tv;
					continue;
				}
				
				
				if ( $s['marker_double_quotes'] === true ) {
					$marker = '' . $s['marker_name'] . '="' . $s['marker_value'] . '"';
				} else {
					$marker = "" . $s['marker_name'] . "='" . $s['marker_value'] . "'";
				}
				
				$x = explode( $marker, $html );
				//d( $marker );d( $x );
				
				if ( $x[0] == $html ) {
					$results[ $s['target'] ] = false;
					continue;
				}
				$y = explode( '<' . $s['tag'], $x[0] );
				if ( $y[0] == $x[0] ) {
					$results[ $s['target'] ] = false;
					continue;
				}
				$z = array_pop( $y );
				if ( $s['target_double_quotes'] === true ) {
					$target = '' . $s['target_name'] . '="';
				} else {
					$target = "" . $s['target_name'] . "='";
				}
				//d( $target);
				$w = explode( $target, $z );
				if ( $w == $z || ! isset( $w[1] ) ) {
					$results[ $s['target'] ] = false;
					continue;
				}
				
				$target_value            = str_replace( '"', "", $w[1] );
				$target_value            = str_replace( "'", "", $target_value );
				$results[ $s['target'] ] = $target_value;
			}
			
			return $results;
		}
	}
	
	/* SETTING DEBUG VALUES */
	if ( ! function_exists( 'wpvr_set_debug' ) ) {
		function wpvr_set_debug( $var = null, $append = false ) {
			
			$new = get_option( 'wpvr_debug' );
			if ( ! is_array( $new ) ) {
				$new = array();
			}
			if ( $append === false ) {
				$new = array( $var );
			} else {
				$new[] = $var;
			}
			
			update_option( 'wpvr_debug', $new );
		}
	}
	
	/* ShOW UP DEBUG VALUES */
	if ( ! function_exists( 'wpvr_get_debug' ) ) {
		function wpvr_get_debug( $var = null ) {
			
			$wpvr_debug = get_option( 'wpvr_debug' );
			d( $wpvr_debug );
		}
	}
	
	/* EMPTY DEBUG VALUES */
	if ( ! function_exists( 'wpvr_reset_debug' ) ) {
		function wpvr_reset_debug() {
			update_option( 'wpvr_debug', array() );
		}
	}
	
	/* MAKE CURL REQUEST */
	if ( ! function_exists( 'wpvr_make_curl_request' ) ) {
		function wpvr_make_curl_request( $api_url = '', $api_args = array(), $curl_object = null, $debug = false, $curl_options = array(), $get_headers = false ) {
			
			$timer = wpvr_chrono_time();
			if ( $curl_object === null || ! is_resource( $curl_object ) ) {
				$curl_object = curl_init();
			}
			if ( is_array( $api_args ) && count( $api_args ) > 0 ) {
				$api_url .= '?' . http_build_query( $api_args );
			}
			//d( is_resource( $curl_object ) );
			curl_setopt( $curl_object, CURLOPT_URL, $api_url );
			curl_setopt( $curl_object, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $curl_object, CURLOPT_RETURNTRANSFER, true );
			
			$headers = false;
			if ( $get_headers ) {
				curl_setopt( $curl_object, CURLOPT_HEADER, true );
				curl_setopt( $curl_object, CURLOPT_VERBOSE, true );
			} else {
				curl_setopt( $curl_object, CURLOPT_HEADER, false );
			}
			
			
			if ( $curl_options != array() ) {
				foreach ( (array) $curl_options as $key => $value ) {
					curl_setopt( $curl_object, $key, $value );
				}
			}
			
			$data = curl_exec( $curl_object );
			//d( $data );
			if ( $get_headers ) {
				$header_size = curl_getinfo( $curl_object, CURLINFO_HEADER_SIZE );
				$headers     = explode( "\n", substr( $data, 0, $header_size ) );
				$data        = substr( $data, $header_size );
			}
			
			if ( $debug === true ) {
				echo $data;
				d( $data );
				d( $api_url );
				d( $api_args );
			}
			$status = curl_getinfo( $curl_object, CURLINFO_HTTP_CODE );
			
			//curl_close( $curl_object );
			
			return array(
				'exec_time' => wpvr_chrono_time( $timer ),
				'status'    => $status,
				'data'      => $data,
				'error'     => curl_error( $curl_object ),
				'json'      => (array) wpvr_json_decode( $data ),
				'headers'   => $headers,
				'caller'    => array(
					'url'  => $api_url,
					'args' => $api_args,
				),
			);
		}
	}
	
	/* Prepare JSON Reponse for ajax communications */
	if ( ! function_exists( 'wpvr_get_json_response' ) ) {
		function wpvr_get_json_response( $data, $response_status = 1, $response_msg = '', $response_count = 0 ) {
			$response         = array(
				'status' => $response_status,
				'msg'    => $response_msg,
				'count'  => $response_count,
				'data'   => $data,
			);
			$encoded_response = WPVR_JS . wpvr_json_encode( $response ) . WPVR_JS;
			
			return $encoded_response;
		}
	}
	
	/* Render HTML attributes from PHP array*/
	if ( ! function_exists( 'wpvr_render_html_attributes' ) ) {
		function wpvr_render_html_attributes( $attr = array() ) {
			$output = '';
			if ( ! is_array( $attr ) || count( $attr ) == 0 ) {
				return $output;
			}
			foreach ( (array) $attr as $key => $value ) {
				if ( $value == '' || empty( $value ) ) {
					$output .= ' ' . $key . ' ';
				} else {
					$output .= ' ' . $key . ' = "' . $value . '" ';
				}
			}
			
			//_d( $output );
			return $output;
		}
	}
	
	/* Update Dynamic Video Views custom fields */
	if ( ! function_exists( 'wpvr_update_dynamic_video_views' ) ) {
		function wpvr_update_dynamic_video_views( $post_id, $new_views ) {
			$wpvr_fillers = get_option( 'wpvr_fillers' );
			$count        = 0;
			if ( ! is_array( $wpvr_fillers ) || count( $wpvr_fillers ) == 0 ) {
				return 0;
			}
			foreach ( (array) $wpvr_fillers as $filler ) {
				if ( $filler['from'] == 'wpvr_dynamic_views' ) {
					update_post_meta( $post_id, $filler['to'], $new_views );
					$count ++;
				}
			}
			
			return $count;
		}
	}
	
	/* Render NOt Found */
	if ( ! function_exists( 'wpvr_render_video_permalink' ) ) {
		function wpvr_render_video_permalink( $post = null, $permalink_structure = null ) {
			if ( $post == null ) {
				global $post;
			}
			
			if ( $permalink_structure == null ) {
				global $wp_rewrite;
				$permalink_structure = $wp_rewrite->permalink_structure;
			}
			
			$var_names       = array(
				'%year%',
				'%monthnum%',
				'%day%',
				'%hour%',
				'%minute%',
				'%second%',
				'%post_id%',
				'%postname%',
				'%category%',
				'%author%',
			);
			$date            = DateTime::createFromFormat( 'Y-m-d H:i:s', $post->post_date_gmt, new DateTimeZone( 'UTC' ) );
			$post_categories = wp_get_post_categories( $post->ID, array( 'fields' => 'slugs' ) );
			if ( count( $post_categories ) == 0 || ! is_array( $post_categories ) ) {
				$post_category = '';
			} else {
				$post_category = $post_categories[0];
			}
			$var_values = array(
				$date->format( 'Y' ),
				$date->format( 'm' ),
				$date->format( 'd' ),
				$date->format( 'G' ),
				$date->format( 'i' ),
				$date->format( 's' ),
				$post->ID,
				$post->post_name,
				$post_category,
				get_the_author_meta( 'user_nicename', $post->post_author ),
			);
			$permalink  = WPVR_SITE_URL . str_replace( $var_names, $var_values, $permalink_structure );
			
			return $permalink;
			
		}
	}
	
	/* Render NOt Found */
	if ( ! function_exists( 'wpvr_render_not_found' ) ) {
		function wpvr_render_not_found( $msg = '' ) {
			?>

            <div class="wpvr_not_found">
                <i class="fa fa-frown-o"></i><br/>
				<?php echo $msg; ?>
            </div>
			
			<?php
		}
	}
	
	/* Render buttons of Source Screen */
	if ( ! function_exists( 'wpvr_render_source_actions' ) ) {
		function wpvr_render_source_actions( $post_id = '' ) {
			$o = array( 'test' => '', 'run' => '', 'save' => '', 'trash' => '', 'clone' => '' );
			
			$o['save'] .= '<br/><button id="wpvr_save_source_btn" class="wpvr_wide_button wpvr_source_actions_btn wpvr_button wpvr_black_button">';
			$o['save'] .= '<i class="wpvr_button_icon fa fa-save"></i>';
			$o['save'] .= '<span>' . __( 'Save Source', WPVR_LANG ) . '</span>';
			$o['save'] .= '</button><br/>';
			
			
			if ( $post_id == '' ) {
				$o['test'] = '<div class="wpvr_no_actions">' . __( 'Start by saving your source', WPVR_LANG ) . '</div>';
				
				return $o;
			}
			
			$testLink  = admin_url( 'admin.php?page=wpvr&test_sources&ids=' . $post_id, 'http' );
			$runLink   = admin_url( 'admin.php?page=wpvr&run_sources&ids=' . $post_id, 'http' );
			$cloneLink = admin_url( 'admin.php?page=wpvr&clone_source=' . $post_id, 'http' );
			$trashLink = wpvr_get_post_links( $post_id, 'trash' );
			
			$o['test'] .= '<button ready="1" url="' . $testLink . '" id="wpvr_metabox_test" class="wpvr_source_actions_btn wpvr_button wpvr_metabox_button test">';
			$o['test'] .= '<i class="wpvr_button_icon fa fa-eye"></i>';
			$o['test'] .= '<span>' . __( 'Test Source', WPVR_LANG ) . '</span>';
			$o['test'] .= '</button><br/>';
			
			$o['run'] .= '<button ready="1" url="' . $runLink . '" id="wpvr_metabox_run" class="wpvr_source_actions_btn wpvr_button wpvr_metabox_button run">';
			$o['run'] .= '<i class="wpvr_button_icon fa fa-bolt"></i>';
			$o['run'] .= '<span>' . __( 'Run Source', WPVR_LANG ) . '</span>';
			$o['run'] .= '</button><br/>';
			
			$o['clone'] .= '<button url="' . $cloneLink . '" id="wpvr_metabox_clone" class="wpvr_source_actions_btn wpvr_button wpvr_metabox_button clone">';
			$o['clone'] .= '<i class="wpvr_button_icon fa fa-copy"></i>';
			$o['clone'] .= '<span>' . __( 'Clone Source', WPVR_LANG ) . '</span>';
			$o['clone'] .= '</button><br/>';
			
			
			$o['trash'] .= '<button url="' . $trashLink
			               . '" id="wpvr_trash_source_btn" class="wpvr_source_actions_btn wpvr_wide_button wpvr_button wpvr_red_button wpvr_metabox_button trash sameWindow">';
			$o['trash'] .= '<i class="wpvr_button_icon fa fa-trash-o"></i>';
			$o['trash'] .= '<span>' . __( 'Trash Source', WPVR_LANG ) . '</span>';
			$o['trash'] .= '</button><br/>';
			
			
			return $o;
		}
	}
	
	/* Get taxonomies data from ids */
	if ( ! function_exists( 'wpvr_get_tax_data' ) ) {
		function wpvr_get_tax_data( $taxonomy, $ids ) {
			global $wpdb;
			if ( ! is_array( $ids ) ) {
				return array();
			}
			$ids    = "'" . implode( "','", $ids ) . "'";
			$sql
			        = "
			select 
				T.term_id as id,
				T.slug,
				T.name
			from
				$wpdb->terms T 
				INNER JOIN $wpdb->term_taxonomy TT ON T.term_id  = TT.term_taxonomy_id
			where
				T.term_id IN ( $ids )
				AND TT.taxonomy = '$taxonomy'
		";
			$terms  = $wpdb->get_results( $sql );
			$return = array();
			foreach ( (array) $terms as $term ) {
				$return[ $term->id ] = array(
					'id'   => $term->id,
					'slug' => $term->slug,
					'name' => $term->name,
				);
			}
			
			return $return;
		}
	}
	
	/* Show An Update Is Availabe message function */
	if ( ! function_exists( 'wpvr_show_available_update_message' ) ) {
		function wpvr_show_available_update_message() {
			global $wpvr_new_version_available, $wpvr_new_version_msg;
			?>
            <div class="updated">
                <p>
                    <strong>WP Video Robot</strong><br/>
					<?php _e( 'There is a new update available !', WPVR_LANG ); ?> (<strong>
                        Version <?php echo $wpvr_new_version_available; ?></strong>)
					
					<?php if ( ! empty( $wpvr_new_version_msg ) ) { ?>
                        <br/><br/><?php echo $wpvr_new_version_msg; ?>
					<?php } ?>
					
					<?php
						$link = WPVR_SITE_URL . "/wp-admin/plugin-install.php?tab=plugin-information&plugin=" . WPVR_LANG . "&section=changelog&TB_iframe=true&width=640&height=662";
						echo '<br/><br/><a href="' . $link . '" > UPDATE NOW </a>';
					?>

                </p>
            </div>
			<?php
		}
	}
	
	/*Draw Stress Graph for selected day */
	if ( ! function_exists( 'wpvr_draw_stress_graph_by_day' ) ) {
		function wpvr_draw_stress_graph_by_day( $date, $hex_color ) {
			
			$stress_data = wpvr_get_schedule_stress( $date->format( 'Y-m-d' ) );
			
			//d( $stress_data );
			
			
			//new dBug( $stress_data );
			list( $r, $g, $b ) = sscanf( $hex_color, "#%02x%02x%02x" );
			$jsData = array(
				'name'               => 'Stress on ' . $date->format( 'Y-m-d' ),
				'fillColor'          => 'rgba(' . $r . ',' . $g . ',' . $b . ',0.2)',
				'strokeColor'        => 'rgba(' . $r . ',' . $g . ',' . $b . ',0.8)',
				'pointColor'         => 'rgba(' . $r . ',' . $g . ',' . $b . ',0.8)',
				'pointHighlightFill' => 'rgba(255,255,255,0.9)',
				'labels'             => '',
				'count'              => '',
				'stress'             => '',
				'max'                => '',
			);
			foreach ( (array) $stress_data as $hour => $data ) {
				$jsData['labels'] .= ' "' . $hour . '" ,';
				$jsData['count'] .= ' ' . $data['count'] . ' ,';
				$jsData['max'] .= ' 100 ,';
				//$jsData['stress'] .= ' '.(100*round( $data['stress']/800 , 2 )).' ,';
				$jsData['stress'] .= $data['wanted'] . ' ,';
			}
			$jsData['labels'] = '[' . substr( $jsData['labels'], 0, - 1 ) . ']';
			$jsData['count']  = '[' . substr( $jsData['count'], 0, - 1 ) . ']';
			$jsData['stress'] = '[' . substr( $jsData['stress'], 0, - 1 ) . ']';
			$jsData['max']    = '[' . substr( $jsData['max'], 0, - 1 ) . ']';
			
			$graph_id = 'wpvr_chart_stress_graph-' . rand( 100, 10000 );
			
			
			?>
            <!-- DAY STRESS GRAPH -->
            <div id="" class="postbox ">
                <h3 class="hndle">
                    <span> <?php echo __( 'Stress Forecast for :', WPVR_LANG ) . ' ' . $date->format( 'l d F Y' ); ?> </span>
                </h3>

                <div class=" inside">
                    <div class="wpvr_graph_wrapper" style="width:100% !important; height:400px !important;">
                        <canvas id="<?php echo $graph_id; ?>" width="900" height="400"></canvas>
                    </div>
                    <script>
                        var data_stress = {
                            labels: <?php echo $jsData['labels']; ?>,
                            datasets: [
                                {
                                    label: "<?php echo $jsData['name'] . ""; ?>",
                                    fillColor: "<?php echo $jsData['fillColor']; ?>",
                                    strokeColor: "<?php echo $jsData['strokeColor']; ?>",
                                    pointColor: "<?php echo $jsData['pointColor']; ?>",
                                    pointHighlightFill: "<?php echo $jsData['pointHighlightFill']; ?>",
                                    data: <?php echo $jsData['stress']; ?>,
                                },
                            ]
                        };
                        jQuery(document).ready(function ($) {
                            wpvr_draw_chart(
                                $('#<?php echo $graph_id; ?>'),
                                $('#<?php echo $graph_id; ?>_legend'),
                                data_stress,
                                'radar'
                            );
                        });
                    </script>
                </div>
            </div>
			<?php
		}
	}
	
	if ( ! function_exists( 'wpvr_async_draw_stress_graph_by_day' ) ) {
		function wpvr_async_draw_stress_graph_by_day( $date, $hex_color ) {
			$chart_id = 'wpvr_chart_stress_graph_' . rand( 0, 1000000 );
			?>
            <!-- DAY STRESS GRAPH -->
            <div
                    class="wpvr_async_graph postbox"
                    day="<?php echo strtolower( $date->format( 'l' ) ); ?>"
                    daylabel="<?php echo( $date->format( 'Y-m-d' ) ); ?>"
                    daytime="<?php echo( $date->format( 'c' ) ); ?>"
                    hex_color="<?php echo $hex_color; ?>"
                    url="<?php echo WPVR_ACTIONS_URL; ?>"
                    chart_id="<?php echo $chart_id; ?>"
            >
                <h3 class="hndle">
					<span>
						<?php echo ucfirst( $date->format( 'l' ) ) . ' ' . __( 'Stress Forecast', WPVR_LANG ); ?>
					</span>
                </h3>

                <div class=" inside">
                    <div class="wpvr_insite_loading">
                        <i class="fa fa-refresh fa-spin"></i>
                        <span>Please Wait ... </span>
                    </div>
                    <div class="wpvr_graph_wrapper"
                         style="display:none;width:100% !important; height:400px !important;">
                        <canvas id="<?php echo $chart_id; ?>" width="900" height="400"></canvas>
                    </div>
                </div>
            </div>
			<?php
		}
	}
	
	/* Generate stress schedule array */
	if ( ! function_exists( 'wpvr_async_get_schedule_stress' ) ) {
		function wpvr_async_get_schedule_stress( $date = '' ) {
			$stress_data = false;
			$stress_data = apply_filters( 'wpvr_extend_schedule_stress', $stress_data, $date );
			
			return $stress_data;
		}
	}
	
	if ( ! function_exists( 'wpvr_get_schedule_stress' ) ) {
		function wpvr_get_schedule_stress( $day = '' ) {
			global $wpvr_options, $wpvr_stress, $wpvr_days;
			//new dBug( $wpvr_days );
			
			if ( $day == '' ) {
				$day_name = $wpvr_days[ strtolower( date( 'N' ) ) ];
			} else {
				$day_num  = strtolower( date( 'N', strtotime( $day ) ) );
				$day_name = $wpvr_days[ $day_num ];
			}
			
			//new dBug( $day_name );
			
			$stress_per_hour = array(
				'00H00' => array(
					'max'     => $wpvr_stress['max'],
					'stress'  => 0,
					'count'   => 0,
					'wanted'  => 0,
					'sources' => array(),
				),
				'01H00' => array(
					'max'     => $wpvr_stress['max'],
					'stress'  => 0,
					'count'   => 0,
					'wanted'  => 0,
					'sources' => array(),
				),
				'02H00' => array(
					'max'     => $wpvr_stress['max'],
					'stress'  => 0,
					'count'   => 0,
					'wanted'  => 0,
					'sources' => array(),
				),
				'03H00' => array(
					'max'     => $wpvr_stress['max'],
					'stress'  => 0,
					'count'   => 0,
					'wanted'  => 0,
					'sources' => array(),
				),
				'04H00' => array(
					'max'     => $wpvr_stress['max'],
					'stress'  => 0,
					'count'   => 0,
					'wanted'  => 0,
					'sources' => array(),
				),
				'05H00' => array(
					'max'     => $wpvr_stress['max'],
					'stress'  => 0,
					'count'   => 0,
					'wanted'  => 0,
					'sources' => array(),
				),
				'06H00' => array(
					'max'     => $wpvr_stress['max'],
					'stress'  => 0,
					'count'   => 0,
					'wanted'  => 0,
					'sources' => array(),
				),
				'07H00' => array(
					'max'     => $wpvr_stress['max'],
					'stress'  => 0,
					'count'   => 0,
					'wanted'  => 0,
					'sources' => array(),
				),
				'08H00' => array(
					'max'     => $wpvr_stress['max'],
					'stress'  => 0,
					'count'   => 0,
					'wanted'  => 0,
					'sources' => array(),
				),
				'09H00' => array(
					'max'     => $wpvr_stress['max'],
					'stress'  => 0,
					'count'   => 0,
					'wanted'  => 0,
					'sources' => array(),
				),
				'10H00' => array(
					'max'     => $wpvr_stress['max'],
					'stress'  => 0,
					'count'   => 0,
					'wanted'  => 0,
					'sources' => array(),
				),
				'11H00' => array(
					'max'     => $wpvr_stress['max'],
					'stress'  => 0,
					'count'   => 0,
					'wanted'  => 0,
					'sources' => array(),
				),
				'12H00' => array(
					'max'     => $wpvr_stress['max'],
					'stress'  => 0,
					'count'   => 0,
					'wanted'  => 0,
					'sources' => array(),
				),
				'13H00' => array(
					'max'     => $wpvr_stress['max'],
					'stress'  => 0,
					'count'   => 0,
					'wanted'  => 0,
					'sources' => array(),
				),
				'14H00' => array(
					'max'     => $wpvr_stress['max'],
					'stress'  => 0,
					'count'   => 0,
					'wanted'  => 0,
					'sources' => array(),
				),
				'15H00' => array(
					'max'     => $wpvr_stress['max'],
					'stress'  => 0,
					'count'   => 0,
					'wanted'  => 0,
					'sources' => array(),
				),
				'16H00' => array(
					'max'     => $wpvr_stress['max'],
					'stress'  => 0,
					'count'   => 0,
					'wanted'  => 0,
					'sources' => array(),
				),
				'17H00' => array(
					'max'     => $wpvr_stress['max'],
					'stress'  => 0,
					'count'   => 0,
					'wanted'  => 0,
					'sources' => array(),
				),
				'18H00' => array(
					'max'     => $wpvr_stress['max'],
					'stress'  => 0,
					'count'   => 0,
					'wanted'  => 0,
					'sources' => array(),
				),
				'19H00' => array(
					'max'     => $wpvr_stress['max'],
					'stress'  => 0,
					'count'   => 0,
					'wanted'  => 0,
					'sources' => array(),
				),
				'20H00' => array(
					'max'     => $wpvr_stress['max'],
					'stress'  => 0,
					'count'   => 0,
					'wanted'  => 0,
					'sources' => array(),
				),
				'21H00' => array(
					'max'     => $wpvr_stress['max'],
					'stress'  => 0,
					'count'   => 0,
					'wanted'  => 0,
					'sources' => array(),
				),
				'22H00' => array(
					'max'     => $wpvr_stress['max'],
					'stress'  => 0,
					'count'   => 0,
					'wanted'  => 0,
					'sources' => array(),
				),
				'23H00' => array(
					'max'     => $wpvr_stress['max'],
					'stress'  => 0,
					'count'   => 0,
					'wanted'  => 0,
					'sources' => array(),
				),
			);
			$sources         = wpvr_get_sources( array(
				'status' => 'on',
			) );
			$sources         = wpvr_multiplicate_sources( $sources );
			foreach ( (array) $sources as $source ) {
				//new dBug($source);
				
				//d( $source );
				
				$wantedVideos  = ( $source->wantedVideosBool == 'default' ) ? $wpvr_options['wantedVideos'] : $source->wantedVideos;
				$getTags       = ( $source->getVideoTags == 'default' ) ? $wpvr_options['getTags'] : ( ( $source->getVideoTags == 'on' ) ? true : false );
				$getStats      = ( $source->getVideoStats == 'default' ) ? $wpvr_options['getStats'] : ( ( $source->getVideoStats == 'on' ) ? true : false );
				$onlyNewVideos = ( $source->onlyNewVideos == 'default' ) ? $wpvr_options['onlyNewVideos'] : ( ( $source->onlyNewVideos == 'on' ) ? true : false );
				
				$source_stress = 0;
				if ( $getTags ) {
					$source_stress += $wantedVideos * $wpvr_stress['getTags'];
				}
				if ( $getStats ) {
					$source_stress += $wantedVideos * $wpvr_stress['getStats'];
				}
				if ( $onlyNewVideos ) {
					$source_stress += $wantedVideos * $wpvr_stress['onlyNewVideos'];
				}
				$source_stress = $source_stress * $wpvr_stress['wantedVideos'] * $wpvr_stress['base'];
				
				if ( $source->schedule == 'hourly' ) {
					foreach ( (array) $stress_per_hour as $hour => $value ) {
						$myhour    = explode( 'H', $hour );
						$isWorking = wpvr_is_working_hour( $myhour[0] );
						
						if ( $isWorking ) {
							$stress_per_hour[ $hour ]['stress'] += $source_stress;
							$stress_per_hour[ $hour ]['count'] ++;
							$stress_per_hour[ $hour ]['wanted'] += $wantedVideos;
							$stress_per_hour[ $hour ]['sources'][] = $source;
						}
					}
				} elseif ( $source->schedule == 'daily' ) {
					$myhour    = explode( 'H', $source->scheduleTime );
					$isWorking = wpvr_is_working_hour( $myhour[0] );
					
					if ( $isWorking ) {
						$stress_per_hour[ $source->scheduleTime ]['stress'] += $source_stress;
						$stress_per_hour[ $source->scheduleTime ]['count'] ++;
						$stress_per_hour[ $source->scheduleTime ]['wanted'] += $wantedVideos;
						$stress_per_hour[ $source->scheduleTime ]['sources'][] = $source;
					}
				} elseif ( $source->schedule == 'weekly' ) {
					
					if ( $day_name == $source->scheduleDay ) {
						
						$myhour    = explode( 'H', $source->scheduleTime );
						$isWorking = wpvr_is_working_hour( $myhour[0] );
						
						if ( $isWorking ) {
							$stress_per_hour[ $source->scheduleTime ]['stress'] += $source_stress;
							$stress_per_hour[ $source->scheduleTime ]['count'] ++;
							$stress_per_hour[ $source->scheduleTime ]['wanted'] += $wantedVideos;
							$stress_per_hour[ $source->scheduleTime ]['sources'][] = $source;
						}
					}
				}
			}
			
			return ( $stress_per_hour );
		}
	}
	
	/* Init cAPI */
	if ( ! function_exists( 'wpvr_capi_init' ) ) {
		function wpvr_capi_init() {
			if ( isset( $_GET['capi'] ) ) {
				if ( isset( $_POST['action'] ) ) {
					wpvr_capi_do( $_POST['action'], $_POST );
				} else {
					echo "SILENCE IS GOLDEN.";
				}
				exit;
			}
		}
	}
	
	/* Do cAPI */
	if ( ! function_exists( 'wpvr_capi_do' ) ) {
		function wpvr_capi_do( $action, $_post ) {
			$r = array(
				'status' => false,
				'msg'    => '',
				'data'   => null,
			);
			
			if ( $action == 'add_notice' ) {
				if ( ! isset( $_post['notice'] ) ) {
					$r['status'] = false;
					$r['msg']    = 'Notice variable missing. EXIT...';
					echo wpvr_json_encode( $r );
				}
				$notice = (array) wpvr_json_decode( base64_decode( $_post['notice'] ) );
				$slug   = wpvr_add_notice( $notice );
				if ( $slug != false ) {
					$r['status'] = true;
					$r['msg']    = 'Notice Added (slug = ' . $slug . '). DONE...';
					$r['data']   = $slug;
					echo wpvr_json_encode( $r );
				} else {
					$r['status'] = false;
					$r['msg']    = 'Error adding the notice. EXIT...';
					echo wpvr_json_encode( $r );
				}
				
				return false;
			}
			
			if ( $action == 'get_activation' ) {
				
				$act = wpvr_get_activation( $_post['slug'] );
				
				echo wpvr_json_encode( array(
					'status' => $act['act_status'],
					'msg'    => 'Activation returned.',
					'data'   => $act,
				) );
				
				return false;
			}
			
			if ( $action == 'reset_activation' ) {
				
				wpvr_set_activation( $_post['slug'], array() );
				echo wpvr_json_encode( array(
					'status' => 1,
					'msg'    => 'Reset Completed.',
					'data'   => null,
				) );
				
				return false;
			}
			
			if ( $action == 'reload_addons' ) {
				update_option( 'wpvr_addons_list', '' );
				$r['status'] = true;
				$r['msg']    = 'ADDONS LIST RESET ...';
				echo wpvr_json_encode( $r );
				
				return false;
			}
			
		}
	}
	
	/*Get Act data even empty */
	if ( ! function_exists( 'wpvr_can_show_menu_links' ) ) {
		function wpvr_can_show_menu_links( $user_id = '' ) {
			global $wpvr_options, $user_ID;
			
			if ( $user_id == '' ) {
				$user_id = $user_ID;
			}
			$user       = new WP_User( $user_id );
			$user_roles = $user->roles;
			
			$super_roles = array( 'administrator', 'superadmin' );
			foreach ( (array) $user_roles as $role ) {
				if ( in_array( $role, $super_roles ) ) {
					return true;
				}
			}
			
			if ( $wpvr_options['showMenuFor'] == null ) {
				return false;
			}
			foreach ( (array) $wpvr_options['showMenuFor'] as $role ) {
				if ( in_array( $role, $user_roles ) ) {
					return true;
				}
			}
			
			return false;
		}
	}
	
	/*Get Act data even empty */
	
	if ( ! function_exists( 'wpvr_get_act_data' ) ) {
		function wpvr_get_act_data( $slug = 'wpvr' ) {
			global $wpvr_empty_activation;
			$wpvr_acts = get_option( 'wpvr_activations' );
			if ( ! array( $wpvr_acts ) ) {
				$wpvr_acts = array();
			}
			if ( ! isset( $wpvr_acts[ $slug ] ) ) {
				$wpvr_acts[ $slug ] = $wpvr_empty_activation;
			}
			
			if ( ! isset( $wpvr_acts[ $slug ]['buy_expires'] ) ) {
				$now                               = new Datetime();
				$wpvr_acts[ $slug ]['buy_expires'] = $now->format( 'Y-m-d H:i:s' );
			}
			
			if ( $wpvr_acts[ $slug ] != '' ) {
				return array(
					'act_status'  => $wpvr_acts[ $slug ]['act_status'],
					'act_id'      => $wpvr_acts[ $slug ]['act_id'],
					'act_email'   => $wpvr_acts[ $slug ]['act_email'],
					'act_code'    => $wpvr_acts[ $slug ]['act_code'],
					'act_date'    => $wpvr_acts[ $slug ]['act_date'],
					'buy_date'    => $wpvr_acts[ $slug ]['buy_date'],
					'buy_user'    => $wpvr_acts[ $slug ]['buy_user'],
					'buy_licence' => $wpvr_acts[ $slug ]['buy_licence'],
					'act_addons'  => $wpvr_acts[ $slug ]['act_addons'],
					'buy_expires' => $wpvr_acts[ $slug ]['buy_expires'],
				);
			}
		}
	}
	
	if ( ! function_exists( 'wpvr_set_act_data' ) ) {
		function wpvr_set_act_data( $slug = 'wpvr', $new_data ) {
			$wpvr_acts = get_option( 'wpvr_activations' );
			if ( ! array( $wpvr_acts ) ) {
				$wpvr_acts = array();
			}
			$wpvr_acts[ $slug ] = $new_data;
			update_option( 'wpvr_activations', $wpvr_acts );
		}
	}
	
	if ( ! function_exists( 'wpvr_refresh_act_data' ) ) {
		function wpvr_refresh_act_data( $slug = 'wpvr', $do_refresh = false ) {
			global $WPVR_SERVER;
			$act = wpvr_get_act_data( $slug );
			$url = wpvr_capi_build_query( WPVR_API_REQ_URL, array(
				'api_key'         => WPVR_API_REQ_KEY,
				'action'          => 'check_license',
				'products_slugs'  => $slug,
				'act_id'          => $act['act_id'], //921
				'encrypt_results' => 1,
				'only_results'    => 1,
				'origin'          => $WPVR_SERVER['HTTP_HOST'],
			) );
			
			$response = wpvr_capi_remote_get( $url, false );
			//d( $response );
			
			if ( $response['status'] != 200 ) {
				echo "CAPI Unreachable !";
				
				return false;
			}
			$fresh_license           = wpvr_json_decode( base64_decode( $response['data'] ), true );
			$fresh_license           = wpvr_object_to_array( $fresh_license );
			$new_data                = $act;
			$new_data['act_status']  = $fresh_license['state'];
			$new_data['act_id']      = $fresh_license['id'];
			$new_data['act_email']   = $fresh_license['act_email'];
			$new_data['act_code']    = $fresh_license['act_code'];
			$new_data['act_date']    = $fresh_license['act_date'];
			$new_data['buy_date']    = $fresh_license['buy_date'];
			$new_data['buy_user']    = $fresh_license['buy_user'];
			$new_data['buy_licence'] = 'inactive';
			$new_data['act_addons']  = array();
			$new_data['buy_expires'] = $fresh_license['buy_expires'];
			if ( $do_refresh ) {
				wpvr_set_act_data( $slug, $new_data );
			}
			
			return $new_data;
		}
	}
	
	
	if ( ! function_exists( 'wpvr_license_is_expired' ) ) {
		function wpvr_license_is_expired( $slug ) {
			$new    = wpvr_refresh_act_data( $slug, true );
			$now    = new Datetime();
			$expire = new Datetime( $new['buy_expires'] );
			
			return ( $now > $expire );
		}
	}
	
	
	//Set Activation
	if ( ! function_exists( 'wpvr_set_activation' ) ) {
		function wpvr_set_activation( $product_slug = '', $act = array() ) {
			global $wpvr_empty_activation;
			$act              = wpvr_extend( $act, $wpvr_empty_activation );
			$wpvr_activations = get_option( 'wpvr_activations' );
			if ( ! array( $wpvr_activations ) ) {
				$wpvr_activations = array();
			}
			
			$wpvr_activations[ $product_slug ] = $act;
			
			update_option( 'wpvr_activations', $wpvr_activations );
			
			
		}
	}
	// Is a free addon ?
	if ( ! function_exists( 'wpvr_is_free_addon' ) ) {
		function wpvr_is_free_addon( $product_slug = '' ) {
			global $wpvr_addons;
			if (
				isset( $wpvr_addons[ $product_slug ]['infos']['free_addon'] )
				&& $wpvr_addons[ $product_slug ]['infos']['free_addon'] === true
			) {
				return true;
			} else {
				return false;
			}
			
			
		}
	}
	
	// Get Multisite Actctivation
	if ( ! function_exists( 'wpvr_get_multisite_activation' ) ) {
		function wpvr_get_multisite_activation( $product_slug = '', $_blog_id = null, $first_only = false ) {
			global $wpvr_empty_activation, $wpvr_addons;
			
			
			$blogs = wp_get_sites( array() );
			//d( $blogs );
			$returned_activations   = array();
			$first_valid_activation = false;
			foreach ( (array) $blogs as $blog ) {
				
				$blog_id = $blog['blog_id'];
				
				if ( $_blog_id != null && $_blog_id != $blog_id ) {
					continue;
				}
				
				$wpvr_activations = get_blog_option( $blog_id, 'wpvr_activations' );
				
				//if( $product_slug == 'wpvr-fbvs' ){
				//	d( $wpvr_activations[ $product_slug ] );
				//}
				
				if ( $wpvr_activations != false ) {
					
					if ( $product_slug == '' ) {
						$returned_activations[ $blog_id ] = $wpvr_activations;
					} elseif ( isset( $wpvr_activations[ $product_slug ] ) ) {
						
						$returned_activations[ $blog_id ] = $wpvr_activations[ $product_slug ];
						if ( $wpvr_activations[ $product_slug ]['act_status'] == 1 ) {
							$first_valid_activation = $wpvr_activations[ $product_slug ];
						}
					} else {
						$returned_activations[ $blog_id ] = $wpvr_empty_activation;
					}
					
					//if( $first_only ) break;
					
				}
				
				
				//d( $blog['path'] );
				
				//d( $old_activations );
			}
			
			//d( $returned_activations );
			if ( count( $returned_activations ) == 0 ) {
				return false;
			}
			
			if ( $first_only ) {
				//return array_pop( $returned_activations );
				return $first_valid_activation;
			}
			
			return $returned_activations;
			
			
			//$wpvr_activations = get_option( 'wpvr_activations' );
			//$old_activation   = get_option( 'wpvr_activation' );
			//
			//if( $product_slug == '' ) return $wpvr_activations;
			//if( ! array( $wpvr_activations ) ) $wpvr_activations = array();
			//
			//if( ! isset( $wpvr_activations[ $product_slug ] ) ) {
			//	if( $product_slug == 'wpvr' && is_array( $old_activation ) ) {
			//		$wpvr_activations[ $product_slug ] = $old_activation;
			//	} else {
			//		$wpvr_activations[ $product_slug ] = $wpvr_empty_activation;
			//	}
			//}
			//
			//return $wpvr_activations[ $product_slug ];
			
		}
	}
	
	// Get Actctivation
	if ( ! function_exists( 'wpvr_get_activation' ) ) {
		function wpvr_get_activation( $product_slug = '' ) {
			global $wpvr_empty_activation, $wpvr_addons;
			
			$wpvr_activations = get_option( 'wpvr_activations' );
			$old_activation   = get_option( 'wpvr_activation' );
			
			if ( $product_slug == '' ) {
				return $wpvr_activations;
			}
			if ( ! array( $wpvr_activations ) ) {
				$wpvr_activations = array();
			}
			
			if ( ! isset( $wpvr_activations[ $product_slug ] ) ) {
				if ( $product_slug == 'wpvr' && is_array( $old_activation ) ) {
					$wpvr_activations[ $product_slug ] = $old_activation;
				} else {
					$wpvr_activations[ $product_slug ] = $wpvr_empty_activation;
				}
			}
			
			return $wpvr_activations[ $product_slug ];
			
		}
	}
	
	if ( ! function_exists( 'wpvr_set_activation' ) ) {
		function wpvr_set_activation( $product_slug = '', $activation ) {
			$wpvr_activations                  = get_option( 'wpvr_activations' );
			$wpvr_activations[ $product_slug ] = $activation;
			update_option( 'wpvr_activations', $wpvr_activations );
		}
	}
	
	
	/* Useful function for tracking activation/deactivation errors */
	if ( ! function_exists( 'wpvr_save_errors' ) ) {
		function wpvr_save_errors( $error ) {
			$errors = get_option( 'wpvr_errors' );
			if ( ! is_array( $errors ) ) {
				$errors = array();
			}
			if ( $error != '' ) {
				$errors[] = $error;
			}
			update_option( 'wpvr_errors', $errors );
		}
	}
	
	if ( ! function_exists( 'wpvr_reset_on_activation' ) ) {
		function wpvr_reset_on_activation() {
			global $wpvr_imported;
			
			//reset tables
			update_option( 'wpvr_deferred', array() );
			update_option( 'wpvr_deferred_ids', array() );
			update_option( 'wpvr_imported', array() );
			
			//Update IMPORTED
			wpvr_update_imported_videos();
			$wpvr_imported = get_option( 'wpvr_imported' );
			
		}
	}
	
	
	/* GET CATEGORIES with count */
	if ( ! function_exists( 'wpvr_get_categories_count' ) ) {
		function wpvr_get_categories_count( $invert = false, $get_empty = false, $hierarchy = false, $ids = '' ) {
			$items = get_categories( $args = array(
				'type'         => array( WPVR_VIDEO_TYPE ),
				'child_of'     => 0,
				'parent'       => '',
				'orderby'      => 'name',
				'order'        => 'ASC',
				'hide_empty'   => 0,
				'hierarchical' => 0,
				'exclude'      => '',
				'include'      => $ids,
				'number'       => '',
				'taxonomy'     => 'category',
				'pad_counts'   => false,
			
			) );
			
			//new dBug( $items );
			
			if ( count( $items ) == 0 ) {
				return array();
			}
			$rCats = array();
			
			foreach ( (array) $items as $item ) {
				
				$cat_item = array(
					'slug'  => $item->slug,
					'label' => $item->name,
					'value' => $item->term_id,
					'count' => $item->count,
				);
				
				if ( $get_empty === true ) {
					$rCats[ $cat_item['value'] ] = $cat_item;
				} else {
					if ( $cat_item['count'] > 0 ) {
						$rCats[ $cat_item['value'] ] = $cat_item;
					}
				}
			}
			
			return $rCats;
		}
	}
	
	
	/* GET CATEGORIES FOR DROPDOWN */
	if ( ! function_exists( 'wpvr_get_categories' ) ) {
		function wpvr_get_categories( $invert = false ) {
			
			$catsArray = array();
			$wp_cats   = get_categories( array(
				'type'       => array( 'post', WPVR_VIDEO_TYPE ),
				'orderby'    => 'name',
				'hide_empty' => false,
			) );
			foreach ( (array) $wp_cats as $cat ) {
				if ( $invert ) {
					$catsArray[ $cat->term_id ] = $cat->name;
				} else {
					$catsArray[ $cat->name ] = $cat->term_id;
				}
			}
			
			return $catsArray;
		}
	}
	
	/* GET AUTHORS POST DATES */
	if ( ! function_exists( 'wpvr_get_dates_count' ) ) {
		function wpvr_get_dates_count() {
			global $wpdb;
			$sql
				= "
			select 
				DATE_FORMAT( P.post_date ,'%M %Y') as label,
				DATE_FORMAT( P.post_date ,'%Y-%m') as value,
				count( distinct P.ID) as count
				
			FROM 
				$wpdb->posts P 
			WHERE 
				P.post_type = '" . WPVR_VIDEO_TYPE . "'
				AND P.post_status IN ('publish','trash','pending','invalid','draft')
			GROUP BY 
				YEAR(P.post_date),MONTH(P.post_date)
				
		";
			
			$items = $wpdb->get_results( $sql, OBJECT );
			if ( count( $items ) == 0 ) {
				return array();
			}
			$rDates = array();
			
			foreach ( (array) $items as $item ) {
				$rDates[ $item->value ] = array(
					'label' => $item->label,
					'value' => $item->value,
					'count' => $item->count,
				);
			}
			
			return $rDates;
		}
	}
	
	/* GET SERVICES FOR DROPDOWN */
	if ( ! function_exists( 'wpvr_get_services_count' ) ) {
		function wpvr_get_services_count() {
			global $wpdb, $wpvr_services;
			global $wpvr_vs;
			$sql
				= "
			select 
				M_SERVICE.meta_value as value,
				1 as label,
				count(distinct P.ID) as found_videos
			FROM 
				$wpdb->posts P 
				INNER JOIN $wpdb->postmeta M_SERVICE ON P.ID = M_SERVICE.post_id
			WHERE 
				P.post_type = '" . WPVR_VIDEO_TYPE . "'
				AND P.post_status IN ('publish','trash','pending','invalid','draft')
				AND (M_SERVICE.meta_key = 'wpvr_video_service' )
			GROUP BY M_SERVICE.meta_value
			ORDER BY found_videos DESC
				
		";
			//$sql =
			$items = $wpdb->get_results( $sql, OBJECT );
			
			if ( count( $items ) == 0 ) {
				return array();
			}
			$rServices = array();
			
			foreach ( (array) $items as $item ) {
				if ( isset( $wpvr_vs[ $item->value ] ) ) {
					$rServices[ $item->value ] = array(
						'label' => $wpvr_vs[ $item->value ]['label'],
						'value' => $item->value,
						'count' => $item->found_videos,
					);
				}
			}
			
			return $rServices;
		}
	}
	
	/* GET AUTHORS FOR DROPDOWN */
	if ( ! function_exists( 'wpvr_get_authors_count' ) ) {
		function wpvr_get_authors_count() {
			global $wpdb;
			$sql
				= "
			select 
				U.user_login as label,
				U.ID as value,
				COUNT(distinct P.ID ) as count				
			FROM 
				$wpdb->posts P 
				left join $wpdb->users U on U.ID = P.post_author
			WHERE 
				P.post_type = '" . WPVR_VIDEO_TYPE . "'
				AND P.post_status IN ('publish','trash','pending','invalid','draft')
			GROUP BY U.ID
		";
			
			$items = $wpdb->get_results( $sql, OBJECT );
			
			
			if ( count( $items ) == 0 ) {
				return array();
			}
			$rItems = array();
			
			foreach ( (array) $items as $item ) {
				$rItems[ $item->value ] = array(
					'label' => $item->label,
					'value' => $item->value,
					'count' => $item->count,
				);
			}
			
			return $rItems;
			
		}
	}
	
	/* GET STATUSES FOR DROPDOWN */
	if ( ! function_exists( 'wpvr_get_status_count' ) ) {
		function wpvr_get_status_count() {
			global $wpdb, $wpvr_status;
			$sql
				= "
			select 
				1 as label,
				P.post_status as value,
				COUNT(distinct P.ID ) as count				
			FROM 
				$wpdb->posts P 
			WHERE 
				P.post_type = '" . WPVR_VIDEO_TYPE . "'
				AND P.post_status IN ('publish','trash','pending','invalid','draft')
			GROUP BY 
				P.post_status
		";
			
			$items = $wpdb->get_results( $sql, OBJECT );
			if ( count( $items ) == 0 ) {
				return array();
			}
			$rItems = array();
			
			foreach ( (array) $items as $item ) {
				if ( isset( $wpvr_status[ $item->value ] ) ) {
					$rItems[ $item->value ] = array(
						'label' => $wpvr_status[ $item->value ]['label'],
						'value' => $item->value,
						'count' => $item->count,
					);
				}
			}
			
			return $rItems;
			
		}
	}
	
	/* GET AUTHORS */
	if ( ! function_exists( 'wpvr_get_authors' ) ) {
		function wpvr_get_authors( $invert = false, $default = false, $restrict = false ) {
			$options   = array(
				'orderby'  => 'login',
				'order'    => 'ASC',
				'show'     => 'login',
				'role__in' => array( 'author', 'administrator', 'editor' ),
			);
			$blogusers = get_users( $options );
			
			$authors      = array();
			$current_user = wp_get_current_user();
			//new dBug( $blogusers) ;
			
			if ( current_user_can( WPVR_USER_CAPABILITY ) && $default ) {
				if ( ! $invert ) {
					$authors[ __( 'Pick an author', WPVR_LANG ) . ' ...' ] = "";
					$authors[' - Default - ']                              = "default";
				} else {
					$authors['']        = __( 'Pick an author', WPVR_LANG ) . ' ...';
					$authors['default'] = ' - Default - ';
				}
			}
			
			if ( ! current_user_can( WPVR_USER_CAPABILITY ) ) {
				if ( $invert ) {
					$authors[ $current_user->ID ] = $current_user->user_login;
				} else {
					$authors[ $current_user->user_login ] = $current_user->ID;
				}
				
				return $authors;
			} else {
				foreach ( (array) $blogusers as $user ) {
					$user_id = $user->data->ID;
					if ( $invert ) {
						$authors[ $user->ID ] = $user->user_login;
					} else {
						$authors[ $user->user_login ] = $user->ID;
					}
				}
				
				
				return $authors;
			}
		}
	}
	
	/* Returns formatted and abreviated number */
	if ( ! function_exists( 'wpvr_numberK' ) ) {
		function wpvr_numberK( $n, $double = false ) {
			
			if ( $n <= 999 ) {
				if ( $double && $n < 10 ) {
					return '0' . $n;
				} else {
					return $n;
				}
			} elseif ( $n > 999 && $n < 999999 ) {
				return round( $n / 1000, 2 ) . 'K';
			} elseif ( $n > 999999 ) {
				return round( $n / 1000000, 2 ) . 'M';
			} else {
				return false;
			}
		}
	}
	
	/* Return formated duration */
	if ( ! function_exists( 'wpvr_human_duration' ) ) {
		function wpvr_human_duration( $seconds ) {
			if ( $seconds > 86400 ) {
				$seconds -= 86400;
				
				return ( gmdate( "j\d H:i:s", $seconds ) );
			} else {
				return ( gmdate( "H:i:s", $seconds ) );
			}
		}
	}
	
	/* DECIDE WETHER TO RUN CRON OR NO */
	if ( ! function_exists( 'wpvr_doWork' ) ) {
		function wpvr_doWork() {
			global $wpvr_options;
			$doWork   = false;
			$now      = new DateTime();
			$hour_now = $now->format( 'H' );
			if ( $wpvr_options['autoRunMode'] === false ) {
				//echo "AUTORUN MODE DISABLED ! ";
				return false;
			}
			if ( $wpvr_options['wakeUpHours'] ) {
				$wuhA = $wpvr_options['wakeUpHoursA'];
				$wuhB = $wpvr_options['wakeUpHoursB'];
				if ( $wuhA == 'empty' || $wuhB == 'empty' ) {
					$doWork = true;
				} else {
					$doWork = ( $hour_now >= $wuhA && $hour_now <= $wuhB );
				}
			} else {
				$doWork = true;
			}
			
			return $doWork;
		}
	}
	
	/* Extends variables with default values */
	if ( ! function_exists( 'wpvr_extend' ) ) {
		function wpvr_extend( $params, $params_def, $strict = false ) {
			foreach ( (array) $params_def as $key => $val ) {
				if ( ! isset( $params[ $key ] ) ) {
					
					$params[ $key ] = $val;
					
				} elseif ( $strict === false && $params[ $key ] == "" && ! is_bool( $params[ $key ] ) ) {
					$params[ $key ] = $val;
					
				} elseif ( isset( $params[ $key ] ) && is_bool( $params[ $key ] ) ) {
					
					
				}
			}
			
			return $params;
		}
	}
	
	/* Generate recursive log messages */
	if ( ! function_exists( 'wpvr_recursive_log_msgs' ) ) {
		function wpvr_recursive_log_msgs( $log_msgs, $lineHTML ) {
			foreach ( (array) $log_msgs as $msg ) {
				if ( ! is_array( $msg ) ) {
					$lineHTML .= "<div class='wpvr_log_msgs'>" . $msg . "</div>";
				} else {
					$lineHTML .= "<div class='wpvr_log_msgs_rec'>";
					$lineHTML = wpvr_recursive_log_msgs( $msg, $lineHTML );
					$lineHTML .= "</div>";
				}
				
				return $lineHTML;
			}
		}
	}
	
	/* Return random post date according to wpvr options */
	if ( ! function_exists( 'wpvr_random_postdate' ) ) {
		function wpvr_make_postdate( $post_date = '' ) {
			global $wpvr_options;
			
			if ( $post_date == '' ) {
				$post_date = new DateTime();
			} else {
				$post_date = new DateTime( $post_date );
			}
			if ( $wpvr_options['randomize'] && $wpvr_options['randomizeStep'] != 'empty' ) {
				$step = $wpvr_options['randomizeStep'];
				if ( $step == "minute" ) {
					$interval = new DateInterval( 'PT' . mt_rand( 0, 60 ) . 'S' );
				} elseif ( $step == "hour" ) {
					$interval = new DateInterval( 'PT' . mt_rand( 0, 60 ) . 'M' );
				} elseif ( $step == "day" ) {
					$interval = new DateInterval( 'PT' . mt_rand( 0, 24 ) . 'H' );
				} else {
					return false;
				}
				
				$signs = array( '-', '+' );
				if ( $signs[ rand( 0, 1 ) ] == '-' ) {
					$post_date->add( $interval );
				} else {
					$post_date->add( $interval );
				}
				
				return $post_date;
				
			} else {
				$post_date = new DateTime();
				
				return $post_date;
			}
		}
	}
	
	/* Generate Colors */
	if ( ! function_exists( 'wpvr_generate_colors' ) ) {
		function wpvr_generate_colors( $ColorSteps = 0 ) {
			$flat_colors = array(
				'#D24D57',
				'#F22613',
				'#FF0000',
				'#D91E18',
				'#96281B',
				'#E74C3C',
				'#CF000F',
				'#C0392B',
				'#D64541',
				'#EF4836',
				'#DB0A5B',
				'#F64747',
				'#E08283',
				'#F62459',
				'#E26A6A',
				'#D2527F',
				'#F1A9A0',
				'#16A085',
				'#2ECC71',
				'#27AE60',
				'#3498DB',
				'#2980B9',
				'#9B59B6',
				'#8E44AD',
				'#34495E',
				'#2C3E50',
				'#2C3E50',
				'#F1C40F',
				'#F39C12',
				'#E67E22',
				'#D35400',
				'#E74C3C',
				'#C0392B',
				'#BDC3C7',
				'#95A5A6',
				'#7F8C8D',
				'#1F3A93',
				'#4B77BE',
				'#34495E',
				'#336E7B',
				'#22A7F0',
				'#3498DB',
				'#2C3E50',
				'#22313F',
				'#52B3D9',
				'#1F3A93',
				'#65C6BB',
				'#68C3A3',
				'#26A65B',
				'#66CC99',
				'#019875',
				'#1E824C',
				'#00B16A',
				'#1BA39C',
				'#2ABB9B',
				'#6C7A89',
				'#F89406',
				'#F9690E',
				'#EB974E',
				'#E67E22',
				'#F39C12',
				'#F4D03F',
				'#F7CA18',
				'#F5D76E',
				'#A1B9C7',
				'#334433',
				'#88aaaa',
				'#447799',
				'#bbeeff',
				'#EEEEEE',
				'#ECECEC',
				'#CCCCCC',
				'#003366',
				'#CCCC99',
				'#217C7E',
				'#9A3334',
				'#3399FF',
				'#F3EFE0',
			);
			
			shuffle( $flat_colors );
			
			$count = count( $flat_colors );
			if ( $ColorSteps === false ) {
				return '#27A1CA';
			}
			if ( $ColorSteps == 0 ) {
				return $flat_colors[ rand( 0, $count - 1 ) ];
			}
			
			return $flat_colors;
			
		}
	}
	
	/* Refuse Access for none Admin Users */
	if ( ! function_exists( 'wpvr_refuse_access' ) ) {
		function wpvr_refuse_access( $a = false ) {
			if ( $a === false ) {
				?>
                <div class="wpvr_no_access"
                     style='margin-top:50px;background: #fff;color: #444;font-family: "Open Sans", sans-serif;margin: 2em auto;padding: 1em 2em;max-width: 700px;-webkit-box-shadow: 0 1px 3px rgba(0,0,0,0.13);box-shadow: 0 1px 3px rgba(0,0,0,0.13);'>
                    <p>

                    <h2> WP Video Robot </h2>
					<?php _e( 'You do not have sufficient permissions to access this page.', WPVR_LANG ); ?>
                    </p>
                </div>
				<?php
			} else {
				?>
                <div class="wpvr_no_access error"
                     style='margin-top:50px;background: #fff;color: #444;font-family: "Open Sans", sans-serif;margin: 2em auto;padding: 1em 2em;max-width: 700px;-webkit-box-shadow: 0 1px 3px rgba(0,0,0,0.13);box-shadow: 0 1px 3px rgba(0,0,0,0.13);'>
                    <h2> WP VIDEO ROBOT </h2>

                    <p>
						<?php _e( 'Your copy licence is not activated.', WPVR_LANG ); ?><br/>
						<?php _e( 'You cannot use WP VIDEO ROBOT.', WPVR_LANG ); ?>

                    </p>
                </div>
				<?php
			}
		}
	}
	
	/* Get customer update infos */
	if ( ! function_exists( 'wpvr_get_customer_infos' ) ) {
		function wpvr_get_customer_infos() {
			global $wpvr_options;
			$customer_infos = array(
				'purchase_code'    => $wpvr_options['purchaseCode'],
				'site_name'        => get_bloginfo( 'name' ),
				'site_url'         => get_bloginfo( 'url' ),
				'site_description' => get_bloginfo( 'description' ),
				'site_language'    => ( is_rtl() ) ? 'RTL' : 'LTR',
				'admnin_email'     => get_bloginfo( 'admin_email' ),
				'wp_version'       => get_bloginfo( 'version' ),
				'wp_url'           => get_bloginfo( 'wpurl' ),
				'wp_rtl'           => is_rtl(),
				'sources_stats'    => wpvr_sources_stats(),
				'videos_stats'     => wpvr_videos_stats(),
			);
			
			return ( base64_encode( wpvr_json_encode( $customer_infos ) ) );
		}
	}
	
	/* Remove all tmp files from tmp directory */
	if ( ! function_exists( 'wpvr_remove_tmp_files' ) ) {
		function wpvr_remove_tmp_files() {
			$dirHandle = opendir( WPVR_TMP_PATH );
			while ( $file = readdir( $dirHandle ) ) {
				if ( ! is_dir( $file ) ) {
					unlink( WPVR_TMP_PATH . "$file" );
				}
			}
			closedir( $dirHandle );
		}
	}
	
	/* Make interval from two datetime */
	if ( ! function_exists( 'wpvr_make_interval' ) ) {
		function wpvr_make_interval( $start, $end, $bool = true ) {
			
			if ( $start == '' || $end == '' ) {
				return array();
			}
			
			$workingHours = array();
			for ( $i = 0; $i < 24; $i ++ ) {
				if ( strlen( $i ) == 1 ) {
					$i = '0' . $i;
				}
				$workingHours[ $i ] = ! $bool;
			}
			if ( $start > $end ) {
				return wpvr_make_interval( $end, $start, ! $bool );
			} elseif ( $start == $end ) {
				return array();
			} else {
				if ( $start <= 12 && $end <= 12 ) {
					for ( $i = $start; $i <= $end; $i ++ ) {
						if ( strlen( $i ) == 1 ) {
							$i = '0' . $i;
						}
						$workingHours[ $i ] = $bool;
					}
				} elseif ( $start > 12 && $end > 12 ) {
					for ( $i = $start; $i <= $end; $i ++ ) {
						if ( strlen( $i ) == 1 ) {
							$i = '0' . $i;
						}
						$workingHours[ $i ] = $bool;
					}
					
				} elseif ( $start < 12 && $end > 12 ) {
					for ( $i = $start; $i < 12; $i ++ ) {
						if ( strlen( $i ) == 1 ) {
							$i = '0' . $i;
						}
						$workingHours[ $i ] = $bool;
					}
					
					for ( $i = 12; $i <= $end; $i ++ ) {
						if ( strlen( $i ) == 1 ) {
							$i = '0' . $i;
						}
						$workingHours[ $i ] = $bool;
					}
					
					$workingHours[ $start ] = $workingHours[ $end ] = true;
					
				}
			}
			
			return $workingHours;
		}
	}
	
	/* Check if it is a working Hour */
	if ( ! function_exists( 'wpvr_is_working_hour' ) ) {
		function wpvr_is_working_hour( $hour ) {
			global $wpvr_options;
			$wh = $wpvr_options['wakeUpHours'];
			
			if ( $wh === false ) {
				return true;
			}
			
			$whA = $wpvr_options['wakeUpHoursA'];
			$whB = $wpvr_options['wakeUpHoursB'];
			
			$whArray = wpvr_make_interval( $whA, $whB, true );
			if ( isset( $whArray[ $hour ] ) ) {
				return $whArray[ $hour ];
			} else {
				return array();
			}
		}
	}
	
	/* Add WPVR Notice */
	if ( ! function_exists( 'wpvr_render_notice' ) ) {
		function wpvr_render_notice( $notice = array() ) {
			global $current_user, $default_notice, $rendered_notices;
			$user_id = $current_user->ID;
			
			if ( ! is_array( $notice ) ) {
				$notices = get_option( 'wpvr_notices' );
				$notice  = $notices[ $notice ];
			}
			
			$notice = wpvr_extend( $notice, $default_notice );
			
			
			if ( isset( $rendered_notices[ $notice['slug'] ] ) ) {
				return false;
			} else {
				$rendered_notices[ $notice['slug'] ] = 1;
			}
			
			if ( $notice['title'] === false ) {
				$notice['title'] = '';
			} elseif ( $notice['title'] == '' ) {
				$notice['title'] = 'WP VIDEO ROBOT';
			}
			
			//d( $notice );
			
			if ( isset( $notice['single_line'] ) && $notice['single_line'] === true ) {
				$line_break = '';
			} else {
				$line_break = '<br/>';
			}
			$notice_style = $icon_style = "";
			
			if ( isset( $notice['color'] ) && $notice['color'] != '' ) {
				$notice_style = ' border-color: ' . $notice['color'] . '; ';
				$icon_style   = ' color: ' . $notice['color'] . '; ';
			}
			
			if ( isset( $notice['icon'] ) && $notice['icon'] != '' ) {
				$icon = $notice['icon'];
			} else {
				$icon = '';
			}
			
			if ( $notice['is_dialog'] === true ) {
				wpvr_render_dialog_notice( $notice );
				
				return true;
			}
			
			/* Check that the user hasn't already clicked to ignore the message */
			if ( ! get_user_meta( $user_id, $notice['slug'] ) ) {
				$hideLink = "?wpvr_hide_notice=" . $notice['slug'] . "";
				foreach ( (array) $_GET as $key => $value ) {
					//d( $value );d( $key );
					if ( is_string( $value ) && $key != 'wpvr_hide_notice' ) {
						$hideLink .= "&$key=$value";
					}
				}
				?>
                <div class="error <?php echo $notice['class']; ?> wpvr_wp_notice"
                     style="display:none; <?php echo $notice_style; ?>">
					<?php if ( $icon != '' ) { ?>
                        <div class="pull-left wpvr_notice_icon" style="<?php echo $icon_style; ?>">
                            <i class="fa <?php echo $icon; ?>"></i>
                        </div>
					<?php } ?>
					<?php if ( $notice['hidable'] ) { ?>
                        <a class="pull-right" href="<?php echo $hideLink; ?>">
							<?php _e( 'Hide this notice', WPVR_LANG ); ?>
                        </a>
					<?php } ?>
                    <div class="wpvr_notice_content">
                        <strong><?php echo $notice['title']; ?></strong>
						<?php echo $line_break; ?>

                        <div><?php echo $notice['content']; ?></div>
                    </div>
                    <div class="wpvr_clearfix"></div>
                </div>
				<?php
			}
			
			if ( isset( $notice['show_once'] ) && $notice['show_once'] === true ) {
				wpvr_remove_notice( $notice['slug'] );
			}
			
		}
	}
	
	
	/* Add WPVR Notice */
	if ( ! function_exists( 'wpvr_render_done_notice_redirect' ) ) {
		function wpvr_render_done_notice_redirect( $msg, $unique = true ) {
			wpvr_add_notice( array(
				'title'     => 'WP Video Robot : ',
				'class'     => 'updated', //updated or warning or error
				'content'   => $msg,
				'hidable'   => false,
				'is_dialog' => false,
				'show_once' => true,
				'color'     => '#27A1CA',
				'icon'      => 'fa-check-circle',
			) );
		}
	}
	
	/* Add WPVR Notice */
	if ( ! function_exists( 'wpvr_render_done_notice' ) ) {
		function wpvr_render_done_notice( $msg, $unique = true ) {
			$error_notice_slug = wpvr_add_notice( array(
				'title'     => 'WP Video Robot : ',
				'class'     => 'updated', //updated or warning or error
				'content'   => $msg,
				'hidable'   => false,
				'is_dialog' => false,
				'show_once' => true,
				'color'     => '#27A1CA',
				'icon'      => 'fa-check-circle',
			) );
			wpvr_render_notice( $error_notice_slug );
			wpvr_remove_notice( $error_notice_slug );
		}
	}
	
	/* Add WPVR Notice */
	if ( ! function_exists( 'wpvr_render_error_notice' ) ) {
		function wpvr_render_error_notice( $msg, $unique = true ) {
			$error_notice = array(
				'title'     => 'WP Video Robot ERROR :',
				'class'     => 'error', //updated or warning or error
				'content'   => $msg,
				'hidable'   => false,
				'is_dialog' => false,
				'show_once' => true,
				'color'     => '#E4503C',
				'icon'      => 'fa-exclamation-triangle',
			);
			
			if ( is_string( $unique ) ) {
				$error_notice['slug'] = $unique;
			}
			
			$error_notice_slug = wpvr_add_notice( $error_notice );
			wpvr_render_notice( $error_notice_slug );
			wpvr_remove_notice( $error_notice_slug );
			
		}
	}
	
	/* Add WPVR Notice */
	if ( ! function_exists( 'wpvr_add_notice' ) ) {
		function wpvr_add_notice( $notice = array(), $unique = true, $multisite = false ) {
			global $default_notice;
			if ( ! $multisite ) {
				$notices = get_option( 'wpvr_notices' );
			} else {
				$notices = get_site_option( 'wpvr_notices' );
			}
			if ( $notices == '' ) {
				$notices = array();
			}
			
			$notice         = wpvr_extend( $notice, $default_notice );
			$nowObj         = new Datetime();
			$notice['date'] = $nowObj->format( 'Y-m-d H:i:s' );
			if ( $unique === true ) {
				$notices[ $notice['slug'] ] = $notice;
			} else {
				$notices[] = $notice;
			}
			
			
			if ( ! $multisite ) {
				update_option( 'wpvr_notices', $notices );
			} else {
				update_site_option( 'wpvr_notices', $notices );
			}
			//d( $notices );
			//return $notices;
			return $notice['slug'];
		}
	}
	
	/* TEsting if Count Videos has reached one of our levels */
	if ( ! function_exists( 'wpvr_is_reaching_level' ) ) {
		function wpvr_is_reaching_level( $count ) {
			global $wpvr_rating_levels;
			foreach ( (array) $wpvr_rating_levels as $level ) {
				if ( $count >= $level ) {
					return $level;
				}
			}
			
			return false;
		}
	}
	
	/* Add WPVR Notice */
	if ( ! function_exists( 'wpvr_render_dialog_notice' ) ) {
		function wpvr_render_dialog_notice( $notice ) {
			//new dBug( $notice );
			global $current_user;
			$user_id = $current_user->ID;
			/* Check that the user hasn't already clicked to ignore the message */
			if ( get_user_meta( $user_id, $notice['slug'] ) ) {
				return false;
			}
			
			if ( ! isset( $notice['dialog_ok_url'] ) ) {
				$notice['dialog_ok_url'] = false;
			}
			if ( $notice['dialog_modal'] === true ) {
				$isModal = 'true';
			} else {
				$isModal = 'false';
			}
			
			?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {

                    setTimeout(function () {

                        var noticeBoxArgs = {
                            title: '<?php echo addslashes( $notice['title'] ); ?>',
                            text: '<?php echo addslashes( $notice['content'] ); ?>',
                            isModal: ( '<?php echo $isModal; ?>' === 'true' ),
                            boxClass: 'noticeBox <?php echo $notice['dialog_class']; ?>',
							<?php if( $notice['dialog_ok_button'] != false ) { ?>
                            pauseButton: '<?php echo addslashes( $notice['dialog_ok_button'] ); ?>',
							<?php } ?>
                        };
						<?php if( $notice['hidable'] === true ){ ?>
                        noticeBoxArgs.cancelButton = '<?php echo addslashes( $notice['dialog_hide_button'] ); ?>';
						<?php } ?>
                        var noticeBox = wpvr_show_loading(noticeBoxArgs);
						
						<?php if( $notice['dialog_ok_url'] === false ) { ?>
                        noticeBox.doPause(function () {
                            noticeBox.remove();
                        });
						<?php } else{ ?>
                        noticeBox.doPause(function () {
                            $('.wpvr_loading_cancel', noticeBox).attr('has_voted', 'yes').trigger('click');
                            window.open('<?php echo $notice['dialog_ok_url']; ?>', '_blank');
                        });
						<?php } ?>
						
						<?php if( $notice['hidable'] === true ){ ?>
                        noticeBox.doCancel(function () {
                            var btn = $('.wpvr_loading_cancel', noticeBox);
                            var has_voted = btn.attr('has_voted');
                            var btn_label = btn.html();
                            $('i', btn).addClass('fa-spin');
                            //btn.html( btn_label+' ....');
                            $.ajax({
                                type: 'POST',
                                url: wpvr_globals.ajax_url,
                                data: {
                                    wpvr_wpload: 1,
                                    action: 'dismiss_dialog_notice',
                                    has_voted: has_voted,
                                    notice_slug: '<?php echo $notice['slug']; ?>'
                                },
                                success: function (data) {
                                    //btn.html( btn_label);
                                    $('i', btn).removeClass('fa-spin');
                                    $json = wpvr_get_json(data);
                                    if ($json.status == '1' && $json.data == 'ok') noticeBox.remove();
                                },
                                error: function (xhr, ajaxOptions, thrownError) {
                                    alert(thrownError);
                                }
                            });
                        });
						<?php } ?>


                    }, <?php echo $notice['dialog_delay']; ?>);

                });
            </script>
			<?php
		}
	}
	
	/* Add WPVR Notice */
	if ( ! function_exists( 'wpvr_remove_notice' ) ) {
		function wpvr_remove_notice( $notice_slug, $multisite = false ) {
			$notices = $multisite ? get_site_option( 'wpvr_notices' ) : get_option( 'wpvr_notices' );
			if ( $notices == '' ) {
				$notices = array();
			}
			foreach ( (array) $notices as $k => $notice ) {
				if ( $notice['slug'] == $notice_slug ) {
					unset( $notices[ $k ] );
				}
			}
			
			if ( $multisite ) {
				update_site_option( 'wpvr_notices', $notices );
			} else {
				update_option( 'wpvr_notices', $notices );
			}
			
			return $notices;
		}
	}
	
	/* Add WPVR Notice */
	if ( ! function_exists( 'wpvr_remove_all_notices' ) ) {
		function wpvr_remove_all_notices() {
			update_option( 'wpvr_notices', array() );
		}
	}
	
	/* Get Cats Recursively */
	if ( ! function_exists( 'wpvr_rec_get_cats' ) ) {
		function wpvr_rec_get_cats( $hCats = array(), $parent_id = null, $level = 0 ) {
			global $wpvr_hierarchical_cats;
			$args = array(
				'type'         => array( WPVR_VIDEO_TYPE ),
				'child_of'     => 0,
				'parent'       => '',
				'orderby'      => 'name',
				'order'        => 'ASC',
				'hide_empty'   => 0,
				'hierarchical' => 0,
				'exclude'      => '',
				'include'      => '',
				'number'       => '',
				'taxonomy'     => 'category',
				'pad_counts'   => false,
			);
			if ( $parent_id != null ) {
				$args['parent'] = $parent_id;
			}
			$items = get_categories( $args );
			$hCats = array();
			if ( count( $items ) == 0 ) {
				return $hCats;
			}
			foreach ( (array) $items as $item ) {
				$int_level = $level;
				if ( $item->parent != 0 && $parent_id == null ) {
					continue;
				}
				$prefix = '';
				for ( $i = 0; $i < $level; $i ++ ) {
					$prefix .= '&nbsp;&nbsp;&nbsp;&nbsp;';
				}
				$cat_item                 = array(
					'slug'  => $item->slug,
					'label' => $prefix . $item->name . ' (' . $item->count . ') ',
					'value' => $item->term_id,
					'count' => $item->count,
					'level' => $level,
				);
				$wpvr_hierarchical_cats[] = array(
					'label' => $cat_item['label'],
					'value' => $cat_item['value'],
				);
				$int_level ++;
				$hCats[ $item->term_id ] = array(
					'item' => $cat_item,
					'subs' => wpvr_rec_get_cats( $hCats, $item->term_id, $int_level ),
				);
			}
			
			return $hCats;
		}
	}
	
	/* Get Hierarchical Array of Categories with Counts*/
	if ( ! function_exists( 'wpvr_get_hierarchical_cats' ) ) {
		function wpvr_get_hierarchical_cats( $return_tree = false ) {
			global $wpvr_hierarchical_cats;
			$tree_cats = wpvr_rec_get_cats();
			if ( $return_tree ) {
				return $tree_cats;
			} else {
				return $wpvr_hierarchical_cats;
			}
		}
	}
	
	/* Get Taxonomy TErms array with count */
	if ( ! function_exists( 'wpvr_get_taxonomy_terms' ) ) {
		function wpvr_get_taxonomy_terms( $taxonomy ) {
			$terms      = get_terms( $taxonomy, array(
				'orderby'    => 'name',
				'hide_empty' => false,
			) );
			$termsArray = array();
			foreach ( (array) $terms as $term ) {
				$termsArray[ $term->term_id ] = $term->name . ' (' . $term->count . ') ';
			}
			
			return $termsArray;
		}
	}
	
	/* Check for performance security condition */
	if ( ! function_exists( 'wpvr_max_fetched_videos_per_run' ) ) {
		function wpvr_max_fetched_videos_per_run() {
			global $wpvr_options;
			
			$sources = wpvr_get_sources( array( 'status' => 'on' ) );
			$sources = wpvr_multiplicate_sources( $sources );
			$data    = array();
			//new dBug( $sources );
			
			foreach ( (array) $sources as $source ) {
				if ( ! isset( $data[ $source->id ] ) ) {
					$data[ $source->id ] = array(
						'source_name'   => $source->name,
						'wanted_videos' => 0,
						'sub_sources'   => 0,
						'warning'       => false,
					);
				}
				$wantedVideos = ( $source->wantedVideosBool == 'default' ) ? $wpvr_options['wantedVideos'] : $source->wantedVideos;
				$data[ $source->id ]['wanted_videos'] += $wantedVideos;
				$data[ $source->id ]['sub_sources'] ++;
				
				if ( $data[ $source->id ]['wanted_videos'] > WPVR_SECURITY_WANTED_VIDEOS ) {
					$data[ $source->id ]['warning'] = true;
				}
				
			}
			
			return $data;
		}
	}
	
	if ( ! function_exists( 'wpvr_download_attachment_image' ) ) {
		function wpvr_download_attachment_image( $image_url = '', $image_title = '', $image_desc = '', $unique_id = '' ) {
			
			//if( WPVR_DISABLE_THUMBS_DOWNLOAD === TRUE ) return '';
			
			if ( $image_url == '' ) {
				return false;
			}
			if ( $unique_id == '' ) {
				$unique_id = md5( uniqid( rand(), true ) );
			}
			
			$upload_dir     = wp_upload_dir(); // Set upload folder
			$image_data
			                =  // Get image data
			$file_extension = pathinfo( $image_url, PATHINFO_EXTENSION );
			$fe             = explode( '?', $file_extension );
			$file_extension = $fe[0];
			if ( $file_extension == '' || $file_extension == null ) {
				$file_extension = 'jpg';
			}
			$filename = sanitize_file_name( $image_title );
			if ( preg_match( '/[^\x20-\x7f]/', $filename ) ) {
				$filename = md5( $filename );
			}
			$filename_ext = $filename . '.' . $file_extension;
			//ppg_set_debug( $filename_ext , TRUE);
			
			//if( ! file_exists( $filename ) ) {
			if ( wp_mkdir_p( $upload_dir['path'] ) ) {
				$file = $upload_dir['path'] . '/' . $filename_ext;
			} else {
				$file = $upload_dir['basedir'] . '/' . $filename_ext;
			}
			@file_put_contents(
				$file,
				apply_filters(
					'wpvr_extend_attachment_image_raw_content',
					@file_get_contents( $image_url )
				)
			);
			
			$wp_filetype = @wp_check_filetype( $filename_ext, null );
			$attachment  = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title'     => $filename . "-attachment",
				'post_name'      => sanitize_title( $image_title . "-attachment" ),
				'post_content'   => $image_desc,
				'post_excerpt'   => $filename,
				'post_status'    => 'inherit',
			);
			
			$attach_id = @wp_insert_attachment( $attachment, $file );
			update_post_meta( $attach_id, '_wp_attachment_image_alt', $filename );
			@require_once( ABSPATH . 'wp-admin/includes/image.php' );
			$attach_data = @wp_generate_attachment_metadata( $attach_id, $file );
			@wp_update_attachment_metadata( $attach_id, $attach_data );
			
			//wpvr_set_debug( $file );
			
			return array(
				'file'         => $file,
				'att'          => $attachment,
				'att_id'       => $attach_id,
				'att_metadata' => $attach_data,
			);
			
		}
	}
	
	/* Download Thumbnail from URL */
	if ( ! function_exists( 'wpvr_download_featured_image' ) ) {
		function wpvr_download_featured_image( $image_url = '', $fallback_image_url = '', $image_title = '', $image_desc = '', $post_id = '', $unique_id = '' ) {
			
			if ( WPVR_DISABLE_THUMBS_DOWNLOAD === true ) {
				return false;
			}
			
			if ( $image_url == '' ) {
				return false;
			}
			if ( $unique_id == '' ) {
				$unique_id = md5( uniqid( rand(), true ) );
			}
			
			if ( $image_url === false || wpvr_touch_remote_file( $image_url ) === false ) {
				$image_url = $fallback_image_url;
			}
			
			$upload_dir     = wp_upload_dir(); // Set upload folder
			$image_data
			                =  // Get image data
			$file_extension = pathinfo( $image_url, PATHINFO_EXTENSION );
			$fe             = explode( '?', $file_extension );
			$file_extension = $fe[0];
			if ( $file_extension == '' || $file_extension == null ) {
				$file_extension = 'jpg';
			}
			$filename = sanitize_file_name( $image_title );
			if ( preg_match( '/[^\x20-\x7f]/', $filename ) ) {
				$filename = md5( $filename );
			}
			$filename_ext = $filename . '.' . $file_extension;
			//ppg_set_debug( $filename_ext , TRUE);
			
			//if( ! file_exists( $filename ) ) {
			if ( wp_mkdir_p( $upload_dir['path'] ) ) {
				$file = $upload_dir['path'] . '/' . $filename_ext;
			} else {
				$file = $upload_dir['basedir'] . '/' . $filename_ext;
			}
			
			
			@file_put_contents(
				$file,
				apply_filters(
					'wpvr_extend_featured_image_raw_content',
					@file_get_contents( $image_url ),
					$post_id
				)
			);
			
			$wp_filetype = @wp_check_filetype( $filename_ext, null );
			$attachment  = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title'     => $filename . "-attachment",
				'post_name'      => sanitize_title( $image_title . "-attachment" ),
				'post_content'   => $image_desc,
				'post_excerpt'   => $filename,
				'post_status'    => 'inherit',
			);
			if ( $post_id != '' ) {
				$attach_id = @wp_insert_attachment( $attachment, $file, $post_id );
				update_post_meta( $attach_id, '_wp_attachment_image_alt', $filename );
				@require_once( ABSPATH . 'wp-admin/includes/image.php' );
				$attach_data = @wp_generate_attachment_metadata( $attach_id, $file );
				@wp_update_attachment_metadata( $attach_id, $attach_data );
				@set_post_thumbnail( $post_id, $attach_id );
			} else {
				$attach_id = @wp_insert_attachment( $attachment, $file );
				update_post_meta( $attach_id, '_wp_attachment_image_alt', $filename );
				@require_once( ABSPATH . 'wp-admin/includes/image.php' );
				$attach_data = @wp_generate_attachment_metadata( $attach_id, $file );
				@wp_update_attachment_metadata( $attach_id, $attach_data );
			}
			
			//wpvr_set_debug( $file );
			
			return array(
				'file'          => $file,
				'attachment'    => $attach_data,
				'attachment_id' => $attach_id,
			);
			
		}
	}
	
	if ( ! function_exists( 'wpvr_render_add_unwanted_button' ) ) {
		function wpvr_render_add_unwanted_button( $post_id ) {
			global $wpvr_unwanted_ids, $wpvr_unwanted;
			$video_id      = get_post_meta( $post_id, 'wpvr_video_id', true );
			$video_service = get_post_meta( $post_id, 'wpvr_video_service', true );
			//d( $wpvr_unwanted_ids );
			//d( $wpvr_unwanted_ids[$video_service] );
			if ( $video_id == '' || $post_id == '' ) {
				return '';
			}
			if ( isset( $wpvr_unwanted_ids[ $video_service ][ $video_id ] ) ) {
				$action = 'remove';
				$icon   = 'fa-undo';
				$label  = __( 'Remove from Unwanted', WPVR_LANG );
				$class  = "wpvr_black_button";
			} else {
				$action = 'add';
				$icon   = 'fa-ban';
				$label  = __( 'Add to Unwanted', WPVR_LANG );
				$class  = "wpvr_red_button";
				
			}
			
			$unwanted_button
				= '

				<button
					url = "' . WPVR_ACTIONS_URL . '"
					class=" ' . $class . ' wpvr_button wpvr_full_width wpvr_single_unwanted"
					post_id="' . $post_id . '"
					action="' . $action . '"
				>
					<i class="fa ' . $icon . '" iclass="' . $icon . '"></i>
					<span>' . $label . '</span>
				</button>
			';
			
			return $unwanted_button;
		}
	}
	
	if ( ! function_exists( 'wpvr_async_balance_items' ) ) {
		function wpvr_async_balance_items( $items, $buffer ) {
			$k        = $j = 0;
			$balanced = array( 0 => array(), );
			foreach ( (array ) $items as $item_id => $item ) {
				if ( $k >= $buffer ) {
					$k = 0;
					$j ++;
					$balanced[ $j ] = array();
				}
				
				$balanced[ $j ][ $item_id ] = $item;
				$k ++;
			}
			
			return $balanced;
		}
	}
	
	if ( ! function_exists( 'wpvr_get_cron_url' ) ) {
		function wpvr_get_cron_url( $query = '' ) {
			global $wpvr_cron_token;
			
			return get_home_url( null, '/' . WPVR_CRON_ENDPOINT . '/' . $wpvr_cron_token . '/' . $query );
		}
	}
	
	if ( ! function_exists( 'wpvr_render_copy_button' ) ) {
		function wpvr_render_copy_button( $target ) {
			
			?>
            <button
                    class="wpvr_copy_btn wpvr_button wpvr_black_button pull-right"
                    data-clipboard-target="#<?php echo $target; ?>"
                    done=""
            >
                <i class="wpvr_green fa fa-check"></i>
                <i class="wpvr_black fa fa-copy"></i>
                <span class="wpvr_black"><?php echo __( 'COPY', WPVR_LANG ); ?></span>
                <span class="wpvr_green"><?php echo __( 'COPIED !', WPVR_LANG ); ?></span>
            </button>
			<?php
			
			
		}
	}
	
	if ( ! function_exists( 'wpvr_import_sample_sources' ) ) {
		function wpvr_import_sample_sources( $service ) {
			$sample_file = WPVR_PATH . 'assets/json/' . $service . '.json';
			if ( ! file_exists( $sample_file ) ) {
				return wpvr_get_json_response( null, 0, __( 'Could not fine the service sample file.', WPVR_LANG ) );
			}
			$json = (array) json_decode( file_get_contents( $sample_file ) );
			if ( ! isset( $json['version'] ) || ! isset( $json['data'] ) || ! isset( $json['type'] ) || $json['type'] != 'sources' ) {
				return wpvr_get_json_response( 0, 0, 'Could not import sample sources.', 0 );
			}
			if ( count( $json['data'] ) == 0 ) {
				return wpvr_get_json_response( 0, 0, 'No sample source found.', 0 );
			}
			$count   = 0;
			$sources = $json['data'];
			$total   = count( $sources );
			d( $sources );
			foreach ( (array) $sources as $source ) {
				wpvr_import_source( $source, true );
				$count ++;
			}
			
			return wpvr_get_json_response( null, 1,
				$count . '/' . $total . ' ' . __( 'Sample sources imported successfully.', WPVR_LANG ),
				$total
			);
		}
	}
	
	if ( ! function_exists( 'wpvr_render_source_filters' ) ) {
		function wpvr_render_source_filters( $filter, $GET ) {
			global $wpvr_vs;
			if ( $filter == 'types' ) {
				$typesArray = array();
				foreach ( (array) $wpvr_vs as $vs ) {
					foreach ( (array) $vs['types'] as $vs_type ) {
						if ( $vs_type['global_id'] == 'group_' ) {
							$label = 'Group';
						} else {
							$label = ucfirst( $vs_type['global_id'] );
						}
						$typesArray[ $vs_type['global_id'] ] = $label;
					}
				}
				
				$isActive = ( isset( $GET['source_type'] ) && $GET['source_type'] != '0' ) ? "active" : "";
				?>
                <div class="wpvr_filter_dropdown <?php echo $isActive; ?>">
                    <select name="source_type">
                        <option value="0"><?php _e( '- All types', WPVR_LANG ); ?></option>
						<?php
							$current_v = isset( $GET['source_type'] ) ? $GET['source_type'] : '';
							foreach ( (array) $typesArray as $value => $label ) {
								printf(
									'<option value="%s"%s>%s</option>',
									$value,
									$value == $current_v ? ' selected="selected"' : '',
									$label
								);
							}
						?>
                    </select>
                </div>
				<?php
			} elseif ( $filter == 'services' ) {
				$isActive = ( isset( $GET['source_service'] ) && $GET['source_service'] != '0' ) ? "active" : "";
				?>
                <div class="wpvr_filter_dropdown <?php echo $isActive; ?>">
                    <select name="source_service">
                        <option value="0"><?php _e( '- All services', WPVR_LANG ); ?></option>
						<?php
							global $wpvr_vs;
							$current_v = isset( $_GET['source_service'] ) ? $_GET['source_service'] : '';
							foreach ( (array) $wpvr_vs as $value => $vs ) {
								if ( ! isset( $vs['skipThis'] ) && ! $vs['skipThis'] ) {
									$s    = ( $vs['id'] == $current_v ) ? ' selected="selected"' : '';
									$echo = '<option value="' . $vs['id'] . '" ' . $s . ' >';
									$echo .= $vs['label'];
									$echo .= '</option>';
									echo $echo;
								}
							}
						?>
                    </select>
                </div>
				<?php
			} elseif ( $filter == 'folders' ) {
				$folders  = wpvr_get_folders_simple();
				$isActive = ( isset( $GET['source_folder'] ) && $GET['source_folder'] != '0' ) ? "active" : "";
				?>
                <div class="wpvr_filter_dropdown <?php echo $isActive; ?>">
                    <select name="source_folder">
                        <option value="0"><?php _e( '- All folders', WPVR_LANG ); ?></option>
						<?php
							global $wpvr_vs;
							$current_v = isset( $_GET['source_folder'] ) ? $_GET['source_folder'] : '';
							foreach ( (array) $folders as $value => $label ) {
								$s    = ( $value == $current_v ) ? ' selected="selected"' : '';
								$echo = '<option value="' . $value . '" ' . $s . ' >';
								$echo .= $label;
								$echo .= '</option>';
								echo $echo;
							}
						?>
                    </select>
                </div>
				<?php
			} elseif ( $filter == 'categories' ) {
				$isActive = ( isset( $GET['source_cats'] ) && $GET['source_cats'] != '0' ) ? "active" : "";
				$cats     = wpvr_get_categories_count( false, true );
				
				?>
                <div class="wpvr_filter_dropdown <?php echo $isActive; ?>">
                    <select name="source_cats">
                        <option value="0"><?php _e( '- All categories', WPVR_LANG ); ?></option>
						<?php
							global $wpvr_vs;
							$current_v = isset( $_GET['source_cats'] ) ? $_GET['source_cats'] : '';
							foreach ( (array) $cats as $value => $cat ) {
								$s    = ( $value == $current_v ) ? ' selected="selected"' : '';
								$echo = '<option value="' . $value . '" ' . $s . ' >';
								$echo .= $cat['label'] . ' (' . $cat['count'] . ')';
								$echo .= '</option>';
								echo $echo;
							}
						?>
                    </select>
                </div>
				<?php
			} elseif ( $filter == 'authors' ) {
				$isActive     = ( isset( $GET['source_author'] ) && $GET['source_author'] != '0' ) ? "active" : "";
				$authorsArray = wpvr_get_authors( $invert = false, $default = false, $restrict = true );
				?>
                <div class="wpvr_filter_dropdown <?php echo $isActive; ?>">
                    <select name="source_author">
                        <option value="0"><?php _e( '- All authors', WPVR_LANG ); ?></option>
						<?php
							$current_v = isset( $_GET['source_author'] ) ? $_GET['source_author'] : '';
							foreach ( (array) $authorsArray as $label => $value ) {
								printf
								(
									'<option value="%s"%s>%s</option>',
									$value,
									$value == $current_v ? ' selected="selected"' : '',
									$label
								);
							}
						?>
                    </select>
                </div>
				<?php
			}
		}
	}
	
	if ( ! function_exists( 'wpvr_render_video_filters' ) ) {
		function wpvr_render_video_filters( $filter, $GET ) {
			global $wpvr_vs;
			if ( $filter == 'services' ) {
				$isActive = ( isset( $GET['video_service'] ) && $GET['video_service'] != '0' ) ? "active" : "";
				?>
                <div class="wpvr_filter_dropdown <?php echo $isActive; ?>">
                    <select name="video_service">
                        <option value="0"><?php _e( '- All services', WPVR_LANG ); ?></option>
						<?php
							global $wpvr_vs;
							$current_v = isset( $_GET['video_service'] ) ? $_GET['video_service'] : '';
							foreach ( (array) $wpvr_vs as $value => $vs ) {
								if ( ! isset( $vs['skipThis'] ) && ! $vs['skipThis'] ) {
									$s    = ( $vs['id'] == $current_v ) ? ' selected="selected"' : '';
									$echo = '<option value="' . $vs['id'] . '" ' . $s . ' >';
									$echo .= $vs['label'];
									$echo .= '</option>';
									echo $echo;
								}
							}
						?>
                    </select>
                </div>
				<?php
			} elseif ( $filter == 'wpvr_only' ) {
				$isActive      = ( isset( $GET['wpvr_only'] ) && $GET['wpvr_only'] != '0' ) ? "active" : "";
				$wpvr_only     = ( isset( $GET['wpvr_only'] ) && $GET['wpvr_only'] == '1' ) ? ' selected="selected" ' : "";
				$not_wpvr_only = ( isset( $GET['wpvr_only'] ) && $GET['wpvr_only'] == '-1' ) ? ' selected="selected" ' : "";
				
				?>
                <div class="wpvr_filter_dropdown <?php echo $isActive; ?>">
                    <select name="wpvr_only">

                        <option value="0"><?php _e( '- All videos', WPVR_LANG ); ?></option>
                        <option value="1" <?php echo $wpvr_only; ?> ><?php _e( 'WPVR Videos Only', WPVR_LANG ); ?></option>
                        <option value="-1" <?php echo $not_wpvr_only; ?> ><?php _e( 'Non WPVR Videos Only', WPVR_LANG ); ?></option>

                    </select>
                </div>
				<?php
			} elseif ( $filter == 'folders' ) {
				$folders  = wpvr_get_folders_simple();
				$isActive = ( isset( $GET['source_folder'] ) && $GET['source_folder'] != '0' ) ? "active" : "";
				?>
                <div class="wpvr_filter_dropdown <?php echo $isActive; ?>">
                    <select name="source_folder">
                        <option value="0"><?php _e( '- All folders', WPVR_LANG ); ?></option>
						<?php
							global $wpvr_vs;
							$current_v = isset( $_GET['source_folder'] ) ? $_GET['source_folder'] : '';
							foreach ( (array) $folders as $value => $label ) {
								$s    = ( $value == $current_v ) ? ' selected="selected"' : '';
								$echo = '<option value="' . $value . '" ' . $s . ' >';
								$echo .= $label;
								$echo .= '</option>';
								echo $echo;
							}
						?>
                    </select>
                </div>
				<?php
			} elseif ( $filter == 'categories' ) {
				$isActive = ( isset( $GET['source_cats'] ) && $GET['source_cats'] != '0' ) ? "active" : "";
				$cats     = wpvr_get_categories_count( false, true );
				
				?>
                <div class="wpvr_filter_dropdown <?php echo $isActive; ?>">
                    <select name="source_cats">
                        <option value="0"><?php _e( '- All categories', WPVR_LANG ); ?></option>
						<?php
							global $wpvr_vs;
							$current_v = isset( $_GET['source_cats'] ) ? $_GET['source_cats'] : '';
							foreach ( (array) $cats as $value => $cat ) {
								$s    = ( $value == $current_v ) ? ' selected="selected"' : '';
								$echo = '<option value="' . $value . '" ' . $s . ' >';
								$echo .= $cat['label'] . ' (' . $cat['count'] . ')';
								$echo .= '</option>';
								echo $echo;
							}
						?>
                    </select>
                </div>
				<?php
			} elseif ( $filter == 'authors' ) {
				$isActive     = ( isset( $GET['video_author'] ) && $GET['video_author'] != '0' ) ? "active" : "";
				$authorsArray = wpvr_get_authors( $invert = false, $default = false, $restrict = true );
				?>
                <div class="wpvr_filter_dropdown <?php echo $isActive; ?>">
                    <select name="video_author">
                        <option value="0"><?php _e( '- All authors', WPVR_LANG ); ?></option>
						<?php
							$current_v = isset( $_GET['video_author'] ) ? $_GET['video_author'] : '';
							foreach ( (array) $authorsArray as $label => $value ) {
								printf
								(
									'<option value="%s"%s>%s</option>',
									$value,
									$value == $current_v ? ' selected="selected"' : '',
									$label
								);
							}
						?>
                    </select>
                </div>
				<?php
			}
		}
	}
	
	if ( ! function_exists( 'wpvr_get_sources_posting_to_a_category' ) ) {
		function wpvr_get_sources_posting_to_a_category( $category_id ) {
			global $wpdb;
			$sql
				= "
		select 
			P.ID
		from 
			$wpdb->posts P
			left join $wpdb->postmeta M on P.ID = M.post_id
		WHERE 
			P.post_type = 'wpvr_source'
			AND M.meta_key = 'wpvr_source_postCats'
			AND M.meta_value LIKE '%\"" . $category_id . "\"%'
		";
			
			$r   = $wpdb->get_results( $sql, ARRAY_A );
			$ids = array();
			foreach ( (array) $r as $id ) {
				$ids[] = intval( $id['ID'] );
			}
			
			return $ids;
		}
	}
	
	
	function wpvr_render_switch_option( $args = array(), $value = 0 ) {
		$args = wp_parse_args( $args, array(
			'tab'          => '',
			'id'           => '',
			'class'        => '',
			'option_class' => '',
			'label'        => '',
			'desc'         => '',
			'function_in'  => function () {
			},
			'function_out' => function () {
			},
		) );
		//d( $args );
		$option_state = $value ? 'on' : 'off';
		
		?>
        <div
                class="wpvr_option wpvr_option_switch <?php echo $option_state; ?> <?php echo $args['option_class']; ?>"
                tab="<?php echo $args['tab']; ?>"
        >
            <div class="wpvr_option_button pull-right">
				<?php wpvr_make_switch_button_new( $args['id'], $value ); ?>
				<?php $args['function_in'](); ?>
            </div>

            <div class="option_text">
				<span class="wpvr_option_title">
					<?php echo $args['label']; ?>
				</span>
                <br/>
                <p class="wpvr_option_desc">
					<?php echo $args['desc']; ?>
                </p>
            </div>
			<?php $args['function_out'](); ?>
            <div class="wpvr_clearfix"></div>
        </div>
		<?php
	}
	
	function wpvr_render_hybrid_option( $args = array(), $value = 0 ) {
		$args = wp_parse_args( $args, array(
			'tab'          => '',
			'id'           => '',
			'class'        => '',
			'option_class' => '',
			'label'        => '',
			'desc'         => '',
			'render_fct'   => function () {
			},
		) );
		// d( $args );return false;
		//$option_state = $value ? 'on' : 'off';
		
		?>
        <div
                class="wpvr_option wpvr_option_switch on"
                tab="<?php echo $args['tab']; ?>"
        >
            <div class="wpvr_option_button pull-right">
				<?php $args['render_fct'](); ?>
            </div>
            <div class="option_text">
				<span class="wpvr_option_title">
					<?php echo $args['label']; ?>
				</span>
                <br/>
                <p class="wpvr_option_desc">
					<?php echo $args['desc']; ?>
                </p>
            </div>
        </div>
		<?php
	}
	
	function wpvr_render_input_option( $args = array(), $value = 0 ) {
		$args       = wp_parse_args( $args, array(
			'tab'          => '',
			'id'           => '',
			'class'        => '',
			'option_class' => '',
			'label'        => '',
			'desc'         => '',
			'placeholder'  => '',
			'size'         => 'medium',
			'attributes'   => array(),
		) );
		$attributes = "";
		foreach ( (array) $args['attributes'] as $attr_key => $attr_value ) {
			$attributes .= ' ' . $attr_key . ' = "' . $attr_value . '" ';
		}
		?>
        <div
                class="wpvr_option wpvr_option_input wpvr_input <?php echo $args['option_class']; ?> on"
                tab="<?php echo $args['tab']; ?>"
        >
            <div class="wpvr_option_button pull-right">
                <input
                        type="text"
                        class="<?php echo $args['class']; ?> wpvr_input"
                        name="<?php echo $args['id']; ?>"
                        id="<?php echo $args['id']; ?>"
                        placeholder="<?php echo $args['placeholder']; ?>"
					<?php echo $attributes; ?>
                        value="<?php echo $value; ?>"
                />
            </div>
            <div class="option_text">
				<span class="wpvr_option_title">
					<?php echo $args['label']; ?>
				</span>
                <br/>
                <p class="wpvr_option_desc">
					<?php echo $args['desc']; ?>
                </p>
            </div>
        </div>
		<?php
	}
	
	function wpvr_render_selectize_option( $args = array(), $value = 0 ) {
		$args = wp_parse_args( $args, array(
			'tab'          => '',
			'id'           => '',
			'class'        => '',
			'option_class' => '',
			'label'        => '',
			'desc'         => '',
			'size'         => 'medium',
			'maxItems'     => '1',
			'options'      => array(),
			'placeholder'  => __( '', WPVR_LANG ),
		) );
		
		
		?>
        <div
                class="wpvr_option wpvr_option_input<?php echo $args['option_class']; ?> on"
                tab="<?php echo $args['tab']; ?>"
        >
            <div class="wpvr_option_button pull-right">
				<?php wpvr_render_selectized_field( array(
					'name'        => $args['id'],
					'class'       => $args['class'],
					'placeholder' => $args['placeholder'],
					'values'      => $args['options'],
					'maxItems'    => 1,
				), $value ); ?>
            </div>
            <div class="option_text">
				<span class="wpvr_option_title">
					<?php echo $args['label']; ?>
				</span>
                <br/>
                <p class="wpvr_option_desc">
					<?php echo $args['desc']; ?>
                </p>
            </div>
        </div>
		<?php
	}
	
	function wpvr_render_select_option( $args = array(), $value = 0 ) {
		$args = wp_parse_args( $args, array(
			'tab'          => '',
			'id'           => '',
			'class'        => '',
			'option_class' => '',
			'label'        => '',
			'desc'         => '',
			'default'      => '',
			'size'         => 'medium',
			'maxItems'     => '1',
			'options'      => array(),
			'placeholder'  => __( '', WPVR_LANG ),
			'attributes'   => array(),
		) );
		
		$attributes = "";
		foreach ( (array) $args['attributes'] as $attr_key => $attr_value ) {
			$attributes .= ' ' . $attr_key . ' = "' . $attr_value . '" ';
		}
		?>
        <div
                class="wpvr_option wpvr_option_input <?php echo $args['option_class']; ?> on"
                tab="<?php echo $args['tab']; ?>"
        >
            <div class="wpvr_option_button pull-right">

                <select
                        name="<?php echo $args['id']; ?>"
                        id="<?php echo $args['id']; ?>"
                        class="<?php echo $args['class']; ?> wpvr_select "
                        placeholder="<?php echo $args['placeholder']; ?>"
					<?php echo $attributes; ?>
                >
					<?php foreach ( (array) $args['options'] as $option_value => $option_label ) { ?>
						<?php $selected = ( $value == $option_value ) ? ' selected="selected" ' : ''; ?>
                        <option value="<?php echo $option_value; ?>" <?php echo $selected; ?>>
							<?php echo $option_label; ?>
                        </option>
					<?php } ?>
                </select>
            </div>
            <div class="option_text">
				<span class="wpvr_option_title">
					<?php echo $args['label']; ?>
				</span>
                <br/>
                <p class="wpvr_option_desc">
					<?php echo $args['desc']; ?>
                </p>
            </div>
        </div>
		<?php
	}
	
	function wpvr_render_automation_data() {
		ob_start();
		global $wpvr_cron_token;
		$cron_url = wpvr_get_cron_url();
		?>
		<?php do_action( 'wpvr_autorun_option_description' ); ?>
        <br/><strong>CRON URL</strong>

        <div class="wpvr_code_url">
								<span class="pull-left" id="wpvr_code_url">
									<?php echo $cron_url; ?>
								</span>
			<?php wpvr_render_copy_button( 'wpvr_code_url' ); ?>
        </div>

        <br/><strong>Crontab line to add ( via URL )</strong>

        <div class="wpvr_code_url">
								<span class="pull-left" id="wpvr_code_url_cron">
									<?php echo ' */10 * * * * wget -q -O /dev/null ' . $cron_url; ?>
								</span>
			<?php wpvr_render_copy_button( 'wpvr_code_url_cron' ); ?>
        </div>
        <br/><strong>Crontab line to add ( via PATH )</strong>

        <div class="wpvr_code_url">
								<span class="pull-left" id="wpvr_code_url_cron_path">
									<?php echo ' */10 * * * * php -f ' . WPVR_CRON_PATH . ' ' . $wpvr_cron_token; ?>
								</span>
			<?php wpvr_render_copy_button( 'wpvr_code_url_cron_path' ); ?>
        </div>


        <br/>
        <a href="http://support.wpvideorobot.com/how-to-configure-cron-on-wp-video-robot/">
			<?php _e( 'Help on Cron Configuring', WPVR_LANG ); ?>
        </a> |
        <a class="wpvr_button small" href="https://store.wpvideorobot.com/addons/autopilot/" target="_blank">
			<?php _e( 'Discover AutoPilot', WPVR_LANG ); ?>
        </a> |
        <a href="http://support.wpvideorobot.com"><?php _e( 'Get Support', WPVR_LANG ); ?></a>
		
		<?php
		
		
		$output = ob_get_clean();
		
		return $output;
	}
	
	function wpvr_o( $var ) {
		new dBug( $var );
	}
	
	function wpvr_oo( $var ) {
		wpvrKint::$theme = 'aante-light';
		echo @wpvrKint::dump( $var );
	}
	
	function wpvr_ooo( $var ) {
		wpvrKint::$theme = 'aante-light';
		?>
        <div
                style="position: fixed;left: 0;top: 0;background: #FFF;padding: 1em;border: 5px solid red;z-index: 99999;max-height: 400px;min-width:350px;overflow-y: auto;">
			<?php @wpvrKint::dump( $var ); ?>
        </div>
		<?php
	}
	
	function wpvr_render_wake_up_hours() {
		global $wpvr_options;
		ob_start();
		?><br/>
        <div class="wpvr_wuh_wrap">
            <input type="hidden" class="wpvr_wuh_input a" name="wakeUpHoursA"
                   value="<?php echo $wpvr_options['wakeUpHoursA']; ?>"/>
            <input type="hidden" class="wpvr_wuh_input b" name="wakeUpHoursB"
                   value="<?php echo $wpvr_options['wakeUpHoursB']; ?>"/>
            <div
                    class="wpvr_wuh_slider"
                    data-min="0"
                    data-max="23"
                    data-step="1"
            ></div>
        </div>
        <br/>
		
		
		<?php
		
		
		$output = ob_get_clean();
		
		return $output;
	}
	
	function wpvr_is_imported_video( $post_id ) {
		global $wpvr_imported;
		
		foreach ( (array) $wpvr_imported as $service ) {
			if ( ! is_bool( $service ) && array_search( $post_id, $service ) ) {
				return true;
			}
		}
		
		return false;
	}
	
	function wpvr_render_vs_styles( $vs ) {
		$vs_id    = $vs['id'];
		$vs_color = $vs['color'];
		
		$styles
			= "
			.wpvr_service_icon.$vs_id{ background-color:$vs_color;}\n
			.wpvr_video_author.$vs_id{ background-color:$vs_color;}\n
            .wpvr_source_icon_right.$vs_id{ background-color:$vs_color;}\n
            .wpvrArgs[service=$vs_id] , .wpvr_source_icon[service=$vs_id]{ border-color:$vs_color;}\n
            .wpvr_source_icon[service=$vs_id] .wpvr_source_icon_icon{ background-color:$vs_color;}\n
		";
		
		return $styles;
	}
	
	function wpvr_is_hd( $video ) {
		if ( $video['service'] == 'youtube' ) {
			if ( strpos( $video['hqthumb'], 'maxres' ) !== false ) {
				return true;
			} else {
				return false;
			}
		} else {
			if ( $video['hqthumb'] !== false ) {
				return true;
			} else {
				return false;
			}
		}
	}