{{{$candidate_first_name}}},

You are currently #{{{$rank}}} in {{{$contest_name}}}. This email outlines the daily standings and what you can do to make the most of this pick.

# How to win the pick

Your most critical path to winning the pick is SHARING your voting link DAILY. 

* Did we mention DAILY? It is important because not everyone will see all your posts. Post up to twice per day at differing times of day.
* [Share to Facebook](http://www.facebook.com/sharer/sharer.php?u={{urlencode($candidate_url)}}) - be sure to tag your friends
* [Share to Twitter](http://twitter.com/share?text={{urlencode($call_to_action)}}&url={{urlencode($candidate_url)}}&hashtags={{urlencode(join(',',$hashtags))}})
* Make funny or informative posts on Facebook and tag your friends. Tagging will increase your visibility in their feeds and in some cases will make you visible to *their* friends - even better!
* Ask all your friends to SHARE your link DAILY. Be brave about this, make sure they know what's at stake and why it matters. Make a direct ask and make it EASY for them to find the link to share.
* The most successful pick winners make videos and start friendly rivalries to cook up voter interest.

Example post text:

> Could you do me a huge favor and please share my link on Facebook and Twitter at least once per day? It will help spread the word and build support for {{{$contest_name}}}. Here is the link to share:
> 
> {{{$candidate_url}}}

# Daily Standings

<table border=1 cellspacing=0 cellpadding=10>
  <tr>
    <th >Change</th>
    <th >Standing</th>
    <th >Name</th>
    <th >Votes</th>
  </tr>
  @foreach($standings as $data)
    <tr>
      <td style="text-align: center; color: {{{$data['color']}}}">{{$data['prefix']}}{{{$data['change']}}}</td>
      <td style="text-align: center; ">{{{$data['current']}}}</td>
      <td style="text-align: left; ">{{{$data['name']}}}</td>
      <td style="text-align: center; ">{{{$data['votes']}}}</td>
    </tr>
  @endforeach

</table>

# Sponsors

Please remember to support and promote our sponsors too. This is one more way you can get involved in the pick and show your support for not only your own position, but the pick overall. It gives you another reason to share and talk about the pick.

* [Ben Allfree](http://www.benallfree.com) - prizes and support
* [Amazon.com](http://www.amazon.com) - Want to have karma and great blessings heaped upon you? Encourage your friends to visit the pick sponsors. In particular, bookmarking our Amazon.com link is one of the most powerful things your fans can do to support the pick. It doesn't cost them anything. Just tell them to bookmark it and use it the next time they plan to go to Amazon.com to make a purchase:

Best,  
the pick.cool team  
Need help with Facebook sharing? [Get help]({{$help_url}})  
Need to stop these messages? [Unfollow this contest]({{$unfollow_url}}) 
