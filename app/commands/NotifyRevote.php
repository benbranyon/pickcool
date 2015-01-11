<?php
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class NotifyRevote extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'notify:revote';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send out daily notifications to revote';

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
  	$votes = Vote::where('candidate_id', '=', '0')->orderBy('created_at')->take(1)->get();
  	foreach($votes as $v)
  	{
    	$u = $v->user;
    	$contest = $v->contest;
      $vars = [
        'subject'=>"[{$contest->title}] - CRITICAL MESSAGE TO VOTERS",
        'to_email'=>$u->email,
        'user'=>$u,
        'vote'=>$v,
        'contest'=>$contest,
      ];
      echo("Sending to {$vars['to_email']}\n");
      Mail::send('emails.revote', $vars, function($message) use ($vars)
      {
          $message->to($vars['to_email'])->subject($vars['subject']);
      });
    }
	}
}