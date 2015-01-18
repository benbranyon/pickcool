@extends('contests.candidates.layout')

@section('contests.candidates.content')
  <h1>Add Picture</h1>
  <p>Upload a picture to display for voting. Our content approval team will review it ASAP.
  {{Form::open(['files'=>true])}}
    Picture: {{Form::file('picture')}}<br/> {{Form::submit('Upload')}}
  {{Form::close()}}
  <h2>Picture Guidelines</h2>
  <h3>Standard Approval</h3>
  Upload just about anything you want. It can be G, PG, or PG-13. Selfies are allowed, as are cat pictures. These pictures will appear in your portfolio, but they can not be used on the leaderboard or voting links.
  <h3>Featured Approval</h3>
  <p class="text-danger">All Featured Pictures must be of you and you alone. No pets, children, or other people in the shot. Upload SQUARE pictures for best results.</p>
  <p>Featured means we have approved the picture to be used as a featured picture as well as in your portfolio. These pictures must be G, PG, PG-13 and will appear on on the contest leaderboard and sharing links.
  <p>We want everyone to look their best, including the pick as a whole. Your voting profile will not be active until you have at least 1 approved Featured picture. For faster approval, follow the guidelines below. Our content team may approve or decline a picture at its sole discretion. In general, we like thoughtful, artistic pictures and dislike cell phone or selfie pictures.</p>
  <p style="color: green">Do:</p>
  <ul>
    <li>Use a professional or semi-professional photograph
  </ul>
  <p style="color: red">Avoid:</p>
  <ul>
    <li>Selfies (or pictures that might be confused with a selfie)
    <li>Grainy/blurry/poor contrast shots
    <li>Extreme angles 
    <li>Busy backgrounds or backgrounds featuring ordinary residential settings (couches, bathrooms, doors, etc).
  </ul>
  <h3>18+ Approval</h3>
  This means we approved the picture to be used in your portfolio. These pictures can be R or X rated, but are only visible to verified members 18+ years of age. They never show to anonymous visitors, on the leaderboard, or in share links.
@stop
