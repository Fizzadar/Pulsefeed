<?php
	/*
		file: app/lib/daemon.php
		desc: daemon class
	*/
	
	class mod_daemon {
		private $jobs = array();
		private $threads = array();
		private $dbfunc;
		private $threadfunc;
		private $maxthreads;
		private $threadtime;
		private $dbupdate;
		private $lock;

		//construct
		public function __construct( $dbfunc, $threadfunc, $maxthreads = 10, $threadtime = 60, $dbupdate = 300, $lock = '' ) {
			$this->dbfunc = $dbfunc;
			$this->threadfunc = $threadfunc;
			$this->maxthreads = $maxthreads;
			$this->threadtime = $threadtime;
			$this->dbupdate = $dbupdate;
			$this->lock = $lock;
		}

		public function start() {
			global $c_config, $argv;
			$arg = count( $argv ) - 1;
			if( empty( $this->lock ) )
				$lockfile = $c_config['core_dir'] . '/../tmp/' . $this->threadfunc . '.lock';
			else
				$lockfile = $c_config['core_dir'] . '/../tmp/' . $this->lock . '.lock';

			//check for lock file
			if( file_exists( $lockfile ) and filemtime( $lockfile ) > time() - 60 and ( !isset( $argv[$arg] ) or $argv[$arg] != 'force' ) ):
				echo $this->threadfunc . ' lock file in place, daemon exiting' . PHP_EOL;
				return;
			elseif( file_exists( $lockfile  ) and isset( $argv[$arg] ) and $argv[$arg] == 'force' ):
				posix_kill( file_get_contents( $lockfile ), SIGKILL );
				unlink( $lockfile );
				echo 'dead previous daemon killed ' . PHP_EOL;
			endif;

			//create lock file
			file_put_contents( $lockfile, posix_getpid() );
			echo 'lock file created' . PHP_EOL;

			//threads array & counter
			$threadcount = 0;
			//db update time
			$dbtime = $this->dbupdate;

			//loop time
			while( true ):
				//get new jobs
				if( count( $this->jobs ) <= 0 and count( $this->threads ) <= 0 and $dbtime >= $this->dbupdate ):
					//get jobs
					$j = call_user_func( $this->dbfunc );
					echo 'daemon has ' . count( $j ) . ' jobs' . PHP_EOL;
					foreach( $j as $job ):
						$this->jobs[] = $job;
					endforeach;
					//reset db timer
					$dbtime = 0;
				elseif( count( $this->jobs ) <= 0 and count( $this->threads ) <= 0 ):
					echo 'waiting for dbtimer... ' . ( $this->dbupdate - $dbtime ) . PHP_EOL;
				endif;

				//add threads until max reached
				while( count( $this->threads ) < $this->maxthreads and count( $this->jobs ) > 0 ):
					//get our job
					reset( $this->jobs );
					$key = key( $this->jobs );

					//create thread
					$this->threads[$threadcount] = array(
						'thread' => new Thread( $this->threadfunc ),
						'time' => 0,
						'job' => $key
					);

					//start thread
					$this->threads[$threadcount]['thread']->start( $this->jobs[$key] );
					echo 'new thread spawned: #' . $threadcount . PHP_EOL;
					$threadcount++;

					//remove from job queue
					unset( $this->jobs[$key] );
				endwhile;

				//loop all current threads, check if dead
				foreach( $this->threads as $key => $thread ):
					//update thread timer
					$this->threads[$key]['time']++;

					//thread dead?
					if( !$thread['thread']->isAlive() ):
						unset( $this->threads[$key] );
						echo 'thread stopped: #' . $key . PHP_EOL;
					endif;

					//thread over times timer?
					if( $thread['time'] > $this->threadtime ):
						$thread['thread']->stop();
						unset( $this->threads[$key] );
						echo 'thread force-stopped: #' . $key . PHP_EOL;
					endif;
				endforeach;

				//end, sleep, update timers
				sleep( 1 );
				$dbtime++;

				//touch our lock file
				touch( $lockfile );
			endwhile;
		}
	}
?>