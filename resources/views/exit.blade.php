<!DOCTYPE html>
<html>
    <body>
        <h1>Exit the parking lot</h1>

        {{ Form::open(array('action' => 'ParkingController@update', 'method' => 'PATCH')) }}
            {{ Form::hidden('id', '1') }}
            {{ Form::label('ticket_id', 'Your Parking Ticket #') }}
            {{ Form::text('ticket_id') }}
            <br/>
            {{ Form::submit('Submit') }}
        {{ Form::close() }}
        
    </body>
</html>
