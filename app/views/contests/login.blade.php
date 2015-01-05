@extends('app')

@section('head')
<title>{{{$contest->title}}} | pick.cool</title>
<meta property="fb:app_id" content="1497159643900204"/>
<meta property="og:type" content="website" />
<meta property="og:title" content="{{{$contest->title}}}"/>
<meta property="og:site_name" content="pick.cool"/>
<meta property="og:url" content="{{{$contest->canonical_url}}}"/>
<meta property="og:description" content="{{{$contest->description}}}"/>
<meta property="og:image" content="{{{$contest->image_url}}}?cachebuster={{{uniqid()}}}"/>
@stop

@section('content')
<div ng-controller="ContestViewCtrl" class="view">
  <div class="contest">
    <h1>
      {{{$contest->title}}}
    </h1>
    <div >
      <p class="alert alert-danger">This pick has not opened yet. All details are subject to change without notice. No voting or sharing can take place at this time. We will send you an email when the pick opens to the public. If you have the earlybird password, please enter it below.</p>
      {{ Form::open() }}
        Earlybird Password: <input type="password" name="password"/> <input type="submit" value="Enter">
      {{ Form::close() }}
      </form>
    </div>
  </div>
</div>


@stop
