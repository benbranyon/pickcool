<?php
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class NotifyDailyCandidates extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'notify:daily:candidates';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send out daily notifications to candidates';

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
    $contests = Contest::all();
    foreach($contests as $contest)
    {
      $standings = $contest->standings();
      $standings_data = [];
      foreach($standings as $standing)
      {
        $change = $standing->previous_rank-$standing->current_rank;
        $standings_data[$standing->id] = [
          'color'=> $change > 0 ? 'green' : ($change < 0 ? 'red' : 'gray'),
          'prefix'=> $change > 0 ? '&uarr;' : ($change < 0 ? '&darr;' : ''),
          'change'=>$change != 0 ? abs($change) : '-',
          'current'=>$standing->current_rank,
          'last'=>$standing->previous_rank,
          'name'=>$standing->name,
          'votes'=>$standing->total_votes+2,
        ];
      }
      foreach($contest->fb_candidates()->take(2) as $c)
      {
        $u = $c->user();
        $vars = [
          'subject'=>"[{$contest->title}] - Daily Standings",
          'to_email'=>$u->email,
          'candidate_name'=>$u->full_name(),
          'rank'=>$standings_data[$c->id]['current'],
          'candidate_first_name'=>$u->first_name,
          'contest_name'=>$contest->title,
          'candidate_url'=>$c->share_url(),
          'help_url'=>'http://pick.cool/help/sharing',
          'unfollow_url'=>$c->unfollow_url(),
          'call_to_action'=>"Vote {$u->full_name()} in {$contest->name}",
          'hashtags'=>['PickCool', $u->toHashTag(), $contest->toHashTag()],
          'standings'=>$standings_data,
          'sponsors'=>$contest->sponsors,
        ];
        Mail::send('emails.candidate-daily-update', $vars, function($message) use ($vars)
        {
            $message->to($vars['to_email'])->subject($vars['subject']);
        });
      }
    }
	}
}