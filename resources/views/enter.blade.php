<!DOCTYPE html>
<html>
    <body>
        <h1>Request a parking spot</h1>

        {{ Form::open(array('action' => 'ParkingController@store')) }}
            {{ Form::label('licenseplate', 'Your License Plate #') }}
            {{ Form::text('licenseplate') }}
            <br/>
            {{ Form::submit('Submit') }}
        {{ Form::close() }}

        
    </body>
</html>
