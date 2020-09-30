<tr>
    <td>
        <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0">
            <tr>
                <td class="content-cell" align="center">
                    <strong>{{ env('BUSINESS_COMPANY') }}</strong>
                    <br>{{ env('BUSINESS_ADDRESS') }}
                    <br>{{ env('BUSINESS_SECONDARY_ADDRESS') }}
                    <br>{{ env('BUSINESS_CITY') }}, {{ env('BUSINESS_STATE') }} {{ env('BUSINESS_ZIP') }}
                    <br>{{ env('BUSINESS_PHONE') }}
                    <br><a href="{{ env('BUSINESS_WEBSITE') }}">{{ env('BUSINESS_WEBSITE') }}</a>
                    
                </td>
            </tr>
            <tr>
                <td class="content-cell" align="center">
                    {{ Illuminate\Mail\Markdown::parse($slot) }}
                </td>
            </tr>
        </table>
    </td>
</tr>
