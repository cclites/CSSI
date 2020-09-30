<tr>
    <td class="header">
        <a href="{{ $url }}">
        	@if($whitelabel)
        	  <img src="{{ secure_url($whitelabel->path.'images/logos/app.png') }}" class="img-responsive center-block" alt="logo">
        	@else
        	  <img src="{{ secure_url('whitelabels/cssi/images/logos/login.png') }}" class="img-responsive center-block" style="max-width: 200px;">
        	@endif
            
        </a>
    </td>
</tr>
