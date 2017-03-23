<!DOCTYPE html>
<html>
    <body>
        <h1>Parking Lot</h1>

        <!-- Let's display the parking spots -->
        @php ($spot_count = 0)

        @foreach($parkingspots as $spot)

            {{ $spot->id }} &nbsp;

            @if($spot->occupied)
                {{ $spot->licenseplate }}
                @php ($spot_count++)
            @else
                None
            @endif

            &nbsp;

            {{ $spot->ticket_id }}

            <br/>
        @endforeach

        <br/>

        @if($spot_count == sizeof($parkingspots))
            <b>LOT IS FULL</b>
        @else
            <b>{{ sizeof($parkingspots) - $spot_count }} available spots</b>

            <br/>
            <a href='/parking/enter'>Enter Lot</a><br/>
            <a href='/parking/exit'>Exit Lot</a>

        @endif

        
    </body>
</html>
