<?php
	/*
		file: app/lib/memcache.php
		desc: memcache on top of mysql (works when using direct keys, no where/etc)
	*/
	
	class mod_memcache {
		private $memcache;
		private $db;
		private $layout;

		//start
		public function __construct( $mod_db ) {
			global $mod_config;

			//start memcache
			$this->memcache = new Memcache;
			//add servers
			foreach( $mod_config['memcache']['mod'] as $host => $port )
				$this->memcache->addServer( $host, $port );

			//get db
			$this->db = $mod_db;

			//set layout
			$this->layout = $mod_config['dblayout'];
		}

		//build key list based on keys + layout
		private function keyList( $table, $keys, $layout ) {
			//build memcache/sql key list
			$klist = array(
				'memcache' => array(),
				'sql' => array()
			);

			//sql keys same
			$klist['sql'] = $keys;

			//memcache keys
			foreach( $keys as $id => $key ):
				$newkey = $table . '_';
				foreach( $layout as $k ):
					$newkey .= $key[$k] . '_';
				endforeach;
				$klist['memcache'][$id] = rtrim( $newkey, '_' );
			endforeach;

			return $klist;
		}

		//where list for mysql
		private function whereList( $keys, $layout ) {
			$sql = '';
			foreach( $keys as $key ):
				$sql .= ' ( ';
				foreach( $layout as $k ):
					$sql .= $k . ' = "' . $key[$k] . '" AND ';
				endforeach;
				$sql = rtrim( $sql, 'AND ' );
				$sql .=' ) OR ';
			endforeach;
			$sql = rtrim( $sql, 'OR ' );
			return $sql;
		}

		//get (select) by keys (skipsql where getting likes/etc [ie things where there may be no record at all])
		public function get( $table, $keys, $skipsql = false ) {
			//select layout
			$layout = $this->layout[$table];
			//things to return
			$return = array();

			//build key list
			$keys = $this->keyList( $table, $keys, $layout );

			//get memcache objects
			$data = @$this->memcache->get( $keys['memcache'] );
			if( $data )
				foreach( $data as $d )
					$return[] = $d;

			if( !$skipsql ):
				//remove sql where not needed
				foreach( $keys['memcache'] as $id => $key )
					if( isset( $data[$key] ) )
						unset( $keys['sql'][$id] );

				//build mysql query
				if( count( $keys['sql'] ) > 0 ):
					$sql = '
						SELECT * FROM ' . $table . '
						WHERE';
					$sql .= $this->whereList( $keys['sql'], $layout );
					//run query
					$data = $this->db->query( $sql );
					foreach( $data as $d )
						$return[] = $d;

					//new key list to save
					$saves = $this->keyList( $table, $data, $layout );
					foreach( $saves['memcache'] as $id => $save ):
						@$this->memcache->set( $save, $saves['sql'][$id] );
					endforeach;
				endif;
			endif;

			return $return;
		}

		//set (insert, update) by data (each array in data must include all keys) <= overwrites values
		public function set( $table, $keyslist ) {
			//select layout
			$layout = $this->layout[$table];

			//key list
			$keys = $this->keyList( $table, $keyslist, $layout );

			//fetch memcache objects
			$data = @$this->memcache->get( $keys['memcache'] );

			//update all got memcaches
			foreach( $keys['memcache'] as $id => $key )
				foreach( $keys['sql'][$id] as $k => $v )
					$data[$key][$k] = $v;

			//now save each one
			foreach( $data as $k => $v )
				@$this->memcache->set( $k, $v );

			//now build sql query
			$sql = '
				INSERT INTO ' . $table . '
				( ';
			foreach( $keyslist[0] as $l => $v )
				$sql .= $l . ', ';
			$sql = rtrim( $sql, ', ' ) . ' ) VALUES ';
			foreach( $keyslist as $i ):
				$sql .= '( ';
				foreach( $i as $k => $v )
					$sql .= '"' . $v . '", ';
				$sql = rtrim( $sql, ', ' ) . ' ), ';
			endforeach;
			$sql = rtrim( $sql, ', ' );
			$sql .= ' ON DUPLICATE KEY UPDATE ';
			foreach( $keyslist[0] as $l => $v )
				$sql .= $l . ' = VALUES( ' . $l . ' ), ';
			$sql = rtrim( $sql, ', ' );

			//return the query result
			return $this->db->query( $sql );
		}

		//delete by keys
		public function delete( $table, $keys ) {
			//select layout
			$layout = $this->layout[$table];

			//build key list
			$keys = $this->keyList( $table, $keys, $layout );

			//delete objects
			foreach( $keys['memcache'] as $key )
				@$this->memcache->delete( $key );

			//delete from sql & return
			$sql = '
				DELETE FROM ' . $table . '
				WHERE';
			$sql .= $this->whereList( $keys['sql'], $layout );
			return $this->db->query( $sql );
		}

		//sync a whole table by its layout!
		public function sync( $table, $where = 'true' ) {
			$layout = $this->layout[$table];

			//sql
			$sql = 'SELECT * FROM ' . $table;
			$sql .= ' WHERE ' . $where;
			//run sql, load table
			$tabledata = $this->db->query( $sql );

			//set all memcaches for each row based on layout
			$count = 0;
			foreach( $tabledata as $row ):
				$key = $table;
				foreach( $layout as $k ):
					$key .= '_' . $row[$k];
				endforeach;
				if( @$this->memcache->set( $key, $row ) )
					$count++;
			endforeach;

			//return count;
			return $count;
		}
	}
?>