<table width="600" style="table-layout: fixed; max-width: 600px;">
	
	<tr class="header" style="background-color: #eee;">
		<td></td>
		<td><h3>Background Check</h3></td>
		<td></td>
	</tr>

    <tr>
        <td></td>
        <td style="background-color: #fff;">

            {{-- Greeting --}}
			@if (! empty($greeting))
				{{ $greeting }}
			@else
				@if ($level == 'error')
					Whoops!
				@else
					Hello!
				@endif
			@endif
		    
		    {{-- Intro Lines --}}
			@foreach ($introLines as $line)
			  {{ $line }}
			  
			@endforeach
			
			
			
			{{-- Action Button --}}
			@isset($actionText)
			
			<br><br>
			
			<?php
			    switch ($level) {
			        case 'success':
			            $color = 'green';
			            break;
			        case 'error':
			            $color = 'red';
			            break;
			        default:
			            $color = 'blue';
			    }
			?>
			    
	        @component('mail::button', ['url' => $actionUrl, 'color' => $color])
				{{ $actionText }}
			@endcomponent
			
			<br>
				
			@endisset
			
			{{-- Outro Lines --}}
			@foreach ($outroLines as $line)
				{{ $line }}
		
			@endforeach

        </td>
        <td></td>
    </tr>
    
    <tr class="footer">
    	<td></td>
    	<td style="text-overflow: ellipsis; overflow-wrap: break-word; word-break: break-all; word-wrap: break-word; background-color: #eee; color: #000; max-width: 600px;">
    		@isset($actionText)
				@component('mail::subcopy')
					If you’re having trouble clicking the "{{ $actionText }}" button, copy and paste the URL below into your web browser:
					<br>
					 [{{ $actionUrl }}]({{ $actionUrl }})
				@endcomponent
			@endisset
    	</td>
    	<td></td>
    </tr>

</table>

