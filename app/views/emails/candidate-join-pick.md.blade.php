{{{$candidate_first_name}}},

Welcome to {{{$contest_name}}}! By entering, you are establishing yourself as one of the region's most favored names.

If this is your first time entering a pick at pick.cool, don't worry! Take a moment to read through this email. There are many useful tips to help you maximize exposure and maybe even win the pick.

# How to look good

Make the best first impression you can. You can change your pick picture at any time, but it can only be changed to your Facbeook profile picture. So if you want to make a change, go change your Facebook profile picture first and then come back. We'll pull in the fresh one at your command.

# How to win the pick

This is not fight club. The first rule of being in a pick is you ALWAYS talk about the pick! Your most critical path to winning the pick is SHARING your voting link DAILY. 

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

# Sponsors

Our sponsors are what make the pick possible. Please remember to support and spread the word about our sponsors. This is one more way you can get involved in the pick and show your support for not only your own position, but the pick overall. It gives you another reason to share and talk about the pick, and it tells people that you are in it for the community as well as yourself.

Share and promote these sponsors as well as your own voting link:

@foreach($sponsors as $sponsor)
* [{{{$sponsor->name}}}]({{{r('sponsor', [$sponsor->id])}}}) - {{{$sponsor->description}}}
@endforeach

Best,  
the pick.cool team  
Need help with Facebook sharing? [Get help]({{$help_url}})  
