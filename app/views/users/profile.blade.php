@extends('layout')

@section('content')
    <div class='col-md-10 col-md-offset-1'>
      <div class="panel panel-default">
        <div class="panel-body">
          {{ BootForm::openHorizontal(2,10)->action(action('users.profile')) }}
              {{ BootForm::bind(Auth::user()) }}
              <h2>About Me</h2>
              {{ BootForm::text('Age', 'age') }}
              {{ BootForm::text('Zip Code', 'zip') }}
              {{ BootForm::select('I am', 'gender')->options(['1' => 'a male', '2' => 'a female', '4'=>'intersex']) }}
              <h2>Seeking</h2>
              @foreach([1=>'Males',2=>'Females',4=>'Intersex'] as $mask=>$gender)
                @if(Auth::user()->seeking_gender & $mask)
                  {{ BootForm::checkbox($gender, 'seeking_gender_'.$mask)->value($mask)->check() }}
                @else
                  {{ BootForm::checkbox($gender, 'seeking_gender_'.$mask)->value($mask) }}
                @endif
              @endforeach
              {{ BootForm::text('Between', 'seeking_age_min') }}
              {{ BootForm::text('And', 'seeking_age_max') }}
              {{ BootForm::select('Within', 'seeking_proximity')->options([
                '1' => '1 mile', 
                '5' => '5 miles',
                '10' => '10 miles',
                '25' => '25 miles',
                '50' => '50 miles',
                '100' => '100 miles',
                '300' => '300 miles',
                '500' => '500 miles',
                '1,000' => '1,000 miles',
                '0' => 'Anywhere',
              ]) }}
              {{ BootForm::submit("Let's Bang >") }}
          {{ BootForm::close() }}
        </div>
      </div>
    </div>
  </div>
@stop