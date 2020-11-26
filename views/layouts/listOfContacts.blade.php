<table class="messenger-list-item" data-contact="{{$value->user_id}}">
    <tr>
        <td style="position: relative;">
            <div class="avatar av-m" style="background-image: url('{{ asset($value->image) }}');">
                <span class="{{$value->is_online == '1' ? 'activeStatus' : 'inactiveStatus'}}"></span>
            </div>
        </td>
        <td>
            <p data-id="user_{{$value->user_id}}">
                {{ strlen($value->first_name.' '.$value->last_name) > 20 ? trim(substr($value->first_name.' '.$value->last_name,0,20)).'..' : $value->first_name.' '.$value->last_name }}
            </p>
        </td>
    </tr>
</table>