@extends('app')

@section('head')
    <title>Frequently Asked Questions | pick.cool</title>
    <script src="//cdn.jsdelivr.net/jquery/2.1.3/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
@stop

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>F.A.Q.</h1>
            <div class="panel-group faq-accordion" id="accordion">
                <div class="panel panel-default">
                    <a class="panel-header-link" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                1. What is Pick.Cool?
                            </h4>
                        </div>
                    </a>
                    <div id="collapseOne" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <p>We’re a site where locals pick what’s cool. We run local contests on anything that’s popular and let you, the public, pick what you think is the best. Our very first pick, “Reno’s Favorite Musicians” was launched on December 16th 2014, closed on December 31st at midnight, and won by Shane Whitecloud. We are still in beta and working to make the user experience as smooth as possible while expanding the functionality and performance.  If you have any problems please contact us via our <a href="https://www.facebook.com/the.pick.cool">Pick.Cool</a> app page on facebook.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <a class="panel-header-link" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                2. What does Beta mean?
                            </h4>
                        </div>
                    </a>
                    <div id="collapseTwo" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p> Websites will often introduce themselves to the world by selecting a small to mid-sized market (ie. Reno which happens to be our home town) and launching a few features at a time in a gradual rollout. This way, we can find any issues (bugs) which we may have missed during our internal testing and fix them before they’re seen millions of times. You are our beta users and for that we thank you. If you find a bug, please reach out to us <a href="https://www.facebook.com/the.pick.cool">on Facebook</a> and try to be as specific as possible about any problems you may encounter. This will help us make Pick.Cool as reliable as possible for every user on every platform as we rollout the rest of our system to the world.
 
                            <p>Remember, we just launched in Mid-December 2014 so we’re still a very young site and we already have several thousand members! Thanks Reno for your support!  We can’t wait to see your faces when we hand out all the sponsorship prizes and awards at our first wrap party on January 31st!</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <a class="panel-header-link" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                3. How does it work?
                            </h4>
                        </div>
                    </a>
                    <div id="collapseThree" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>Some of our picks are based on businesses, like restaurants and bars, while others are based on people like you. Obviously, the business related contests are limited to real establishments with a physical presence, but the model, musicians, sports, and many more picks are open to anyone who can get the votes. All you have to do is enroll in the pick, invite your friends by sharing your link via your profile page, and watching the fun as your vote count goes up and up.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <a class="panel-header-link" data-toggle="collapse" data-parent="#accordion" href="#collapseFour">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                4. What do I need to do to win?
                            </h4>
                        </div>
                    </a>
                    <div id="collapseFour" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>Share your profile pic and have your friends do the same. The more you get your image shared, the more people will vote for you. This is a social based contest, it is designed to not only find talented people in each area of specialization, but to find the people who have the best marketing abilities. Talent agencies today don’t just want talented people, they want talented people who can promote themselves! So go out there and show them that you have what it takes.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <a class="panel-header-link" data-toggle="collapse" data-parent="#accordion" href="#collapseFive">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                5. Can I change my image?
                            </h4>
                        </div>
                    </a>
                    <div id="collapseFive" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>Yes. Simply go to your face book profile and change your pic to something you prefer. Then come back to your profile page and hit the refresh image option.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <a class="panel-header-link" data-toggle="collapse" data-parent="#accordion" href="#collapseSix">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                6. Are there any restrictions on images?
                            </h4>
                        </div>
                    </a>
                    <div id="collapseSix" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>Yes. They need to be PG 13 in nature. Your image should also show you and mostly/only you.  If you’re image is of you with a crowd of friends it will be rejected.  Your image should not be taken from the front seat of a car or in front of a mirror. This isn’t doing you any favors. Please put some thought to your contest image as many thousands of people will see it… put your best face forward.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <a class="panel-header-link" data-toggle="collapse" data-parent="#accordion" href="#collapseSeven">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                7. How many times can I vote?
                            </h4>
                        </div>
                    </a>
                    <div id="collapseSeven" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>Only once per contest. We are considering some ways to reward our most loyal members with additional, ‘bonus’ votes, but have not implemented anything at this time. You only get one vote per pick, but you can change your vote as often as you like. And though you can vote only once, you can share every day. If it looks like you have not voted its because your Facebook token has expired. But rest assured that your vote is in our system. To confirm, simply go to any other contest on Pick.Cool and cast a vote. Once your token with Facebook has been re-established, your votes in all other contests will show up again. We apologize for any confusion this has caused and are looking into ways to correct it.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <a class="panel-header-link" data-toggle="collapse" data-parent="#accordion" href="#collapseEight">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                8. What’s Next?
                            </h4>
                        </div>
                    </a>
                    <div id="collapseEight" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>We’re planning a steady flow of local artist, entertainer, and business contests in the Reno area as we continue to improve and expand the site. Pick.Cool wants to be your one stop resource to find out what’s cool in Reno. We ask your help by voting in our contests, sharing them on Facebook, twitter, etc. and telling the world your opinions in the comment sections of each pick.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <a class="panel-header-link" data-toggle="collapse" data-parent="#accordion" href="#collapseNine">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                9. Why do I have to login to vote?
                            </h4>
                        </div>
                    </a>
                    <div id="collapseNine" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>We need to make sure your vote counts. If everyone could come and vote without any kind of verification then the vote counts would be meaningless. We chose Facebook because they’re a trusted third party we can count on to have valid membership credentials. By using Facebook connect, we offer a simple, one click solution which allows us to verify who you are and therefore make your vote count.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop