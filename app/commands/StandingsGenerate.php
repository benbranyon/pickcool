<?php
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class StandingsGenerate extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'standings:generate';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate standings';

	/**
	 * The file system instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->files = new \Illuminate\Filesystem\Filesystem;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
    Contest::calc_stats();
    User::calc_stats();
	}
}