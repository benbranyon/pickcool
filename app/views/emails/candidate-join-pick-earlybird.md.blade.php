{{{$candidate_first_name}}},

Welcome to {{{$contest_name}}}! By entering, you are establishing yourself as one of the region's most favored names.

This pick is currently in EARLY BIRD mode, which means the pick is still being set up. You are a valued member of this community, and are someone we feel should be in the pick early. But since things are still in flux, please keep the link private... we are TRUSTING that you will keep it private.

When the pick opens to the public, we'll send you more information about how to win the pick and get the most votes. Until then, you have a few assignments:

1. Get your best picture in there. Make the best first impression you can. What you choose now will set the tone for all who enter later. You can change your pick picture at any time, but it can only be changed to your Facbeook profile picture. So if you want to make a change, go change your Facebook profile picture first and then come back. We'll pull in the fresh one at your command.

2. Get in touch with one or more sponsors and see how you might be able to collaborate to cross-promote one another. This is a GREAT opportunity for you, before other contestants are let in.

# Sponsors

Our sponsors are what make the pick possible. Please remember to support and spread the word about our sponsors. This is one more way you can get involved in the pick and show your support for not only your own position, but the pick overall. It gives you another reason to share and talk about the pick, and it tells people that you are in it for the community as well as yourself.

Get in touch with these sponsors soon:

@foreach($sponsors as $sponsor)
* [{{{$sponsor->name}}}]({{{route('sponsor', [$sponsor->id])}}}) - {{{$sponsor->description}}}
@endforeach

Best,  
the pick.cool team  
Need help with Facebook sharing? [Get help]({{$help_url}})  
