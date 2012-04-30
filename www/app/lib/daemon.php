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
		private $stopthreads;

		//construct
		public function __construct( $dbfunc, $threadfunc, $maxthreads = 10, $threadtime = 60, $dbupdate = 300, $lock = '', $stopthreads = 0 ) {
			global $c_config;

			//basic data
			$this->dbfunc = $dbfunc;
			$this->threadfunc = $threadfunc;
			$this->maxthreads = $maxthreads;
			$this->threadtime = $threadtime;
			$this->dbupdate = $dbupdate;

			//lock file
			if( empty( $lock ) )
				$this->lock = $c_config['core_dir'] . '/../tmp/' . $threadfunc . '.lock';
			else
				$this->lock = $c_config['core_dir'] . '/../tmp/' . $lock . '.lock';

			//max threads before stopping daemon
			$this->stopthreads = $stopthreads;
		}

		//run daemon
		public function start() {
			global $c_config, $argv;
			$arg = count( $argv ) - 1;

			//check for lock file
			if( file_exists( $this->lock ) and filemtime( $this->lock ) > time() - 60 and ( !isset( $argv[$arg] ) or $argv[$arg] != 'force' ) ):
				echo $this->threadfunc . ' lock file in place, daemon exiting' . PHP_EOL;
				return;
			elseif( file_exists( $this->lock  ) and isset( $argv[$arg] ) and $argv[$arg] == 'force' ):
				posix_kill( file_get_contents( $this->lock ), SIGKILL );
				unlink( $this->lock );
				echo 'dead previous daemon killed ' . PHP_EOL;
			endif;

			//create lock file
			file_put_contents( $this->lock, posix_getpid() );
			echo 'lock file created' . PHP_EOL;

			//threads array & counter
			$threadcount = 0;
			//db update time
			$dbtime = $this->dbupdate;

			//loop time
			while( true and ( $this->stopthreads == 0 or $threadcount < $this->stopthreads ) ):
				//get new jobs (if current queue less than maxthreads, and threads less than max, and db time)
				if( count( $this->jobs ) < $this->maxthreads and count( $this->threads ) < $this->maxthreads and $dbtime >= $this->dbupdate ):
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

				//check for threads
				$this->checkThreads();

				//end, sleep, update timers
				sleep( 1 );
				$dbtime++;

				//touch our lock file
				touch( $this->lock );
			endwhile;

			//we're here? stopthreads must be set
			while( count( $this->threads ) > 0 ):
				//check for threads
				$this->checkThreads();

				sleep( 1 );
			endwhile;

			//and we're done, remove the lock file
			unlink( $this->lock );
			echo 'daemon run over: ' . $this->lock . ' freed' . PHP_EOL;
			exit( 0 );
		}

		//check threads
		public function checkThreads() {
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
		}
	}
?>