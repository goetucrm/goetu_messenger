{{-- -------------------- Saved Messages -------------------- --}}
@if($get == 'saved')
    <table class="messenger-list-item m-li-divider @if('user_'.Auth::user()->id == $id && $id != "0") m-list-active @endif">
        <tr data-action="0">
            {{-- Avatar side --}}
            <td>
            <div class="avatar av-m" style="background-color: #d9efff; text-align: center;">
                <span class="far fa-bookmark" style="font-size: 22px; color: #68a5ff; margin-top: calc(50% - 10px);"></span>
            </div>
            </td>
            {{-- center side --}}
            <td>
                <p data-id="{{ 'user_'.Auth::user()->id }}">Saved Messages <span>You</span></p>
                <span>Save messages secretly</span>
            </td>
        </tr>
    </table>
@endif

{{-- -------------------- All users/group list -------------------- --}}
@if($get == 'user')
<table class="messenger-list-item @if($user->id == $id && $id != "0") m-list-active @endif" data-contact="user-{{ $user->id }}">
    <tr data-action="0">
        {{-- Avatar side --}}
        <td style="position: relative">
            @if($user->active_status)
                <span class="activeStatus"></span>
            @endif
        <div class="avatar av-m" 
        style="background-image: url('{{ $user->image }}');">
        </div>
        </td>
        {{-- center side --}}
        <td>
        <p data-id="{{ $type.'_'.$user->id }}">
            {{ strlen($user->first_name.' '.$user->last_name) > 30 ? trim(substr($user->first_name.' '.$user->last_name,0,30)).'..' : $user->first_name.' '.$user->last_name }} 
            <span>{{ $lastMessage->created_at->diffForHumans() }}</span></p>
        <span>
            {{-- Last Message user indicator --}}
            {!!
                $lastMessage->from_id == Auth::user()->id 
                ? '<span class="lastMessageIndicator">You :</span>'
                : ''
            !!}
            {{-- Last message body --}}
            @if($lastMessage->attachment == null)
            {!!
                strlen($lastMessage->body) > 30 
                ? trim(substr($lastMessage->body, 0, 30)).'..'
                : $lastMessage->body
            !!}
            @else
            <span class="fas fa-file"></span> Attachment
            @endif
        </span>
        {{-- New messages counter --}}
            {!! $unseenCounter > 0 ? "<b>".$unseenCounter."</b>" : '' !!}
        </td>
    </tr>
</table>
@endif

{{-- -------------------- All group list -------------------- --}}
@if($get == 'group')
<table class="messenger-list-item @if($user->id == $id && $id != "0") m-list-active @endif" data-contact="group-{{ $user->id }}">
    <tr data-action="0">
        {{-- Avatar side --}}
        <td style="position: relative">
        <!--<div class="avatar av-m" 
        style="background-image: url('{{ 'storage/public/users-avatar/'.$user->avatar.'' }}');">
        </div>-->
        <div class="avatar av-m" 
        style="background-image: url('{{ $avatar }}');">
        </div>
        </td>
        {{-- center side --}}

        @if('GC-'.$lastMessage->to_id.'-created-by-'.$lastMessage->from_id.'' == ''.$lastMessage->body.'')
        <td>
        <p data-id="{{ $type.'_'.$user->id }}">
            {{ strlen($user->group_chat_name) > 30 ? trim(substr($user->group_chat_name,0,30)).'..' : $user->group_chat_name }} 
            <span>{{ $lastMessage->created_at->diffForHumans() }}</span></p>
        <span>
            {{-- Last Message user indicator --}}
            {!!
                $lastMessage->from_id == Auth::user()->id 
                ? '<span class="lastMessageIndicator">You :</span>'
                : $from_user_name.' :'
            !!}
            {{-- Last message body --}}

             Created this Group Chat
        </span>
        {{-- New messages counter --}}
            {!! $unseenCounter > 0 ? "<b>".$unseenCounter."</b>" : '' !!}
        </td>
        @endif

        @if('GC-'.$lastMessage->to_id.'-leave-by-'.$lastMessage->from_id.'' == ''.$lastMessage->body.'')
        <td>
        <p data-id="{{ $type.'_'.$user->id }}">
            {{ strlen($user->group_chat_name) > 30 ? trim(substr($user->group_chat_name,0,30)).'..' : $user->group_chat_name }} 
            <span>{{ $lastMessage->created_at->diffForHumans() }}</span></p>
        <span>
            {{-- Last Message user indicator --}}
            {!!
                $lastMessage->from_id == Auth::user()->id 
                ? '<span class="lastMessageIndicator">You :</span>'
                : $from_user_name.' :'
            !!}
            {{-- Last message body --}}

             Leave the Group Chat
        </span>
        {{-- New messages counter --}}
            {!! $unseenCounter > 0 ? "<b>".$unseenCounter."</b>" : '' !!}
        </td>
        @endif

        @if('GC-'.$lastMessage->to_id.'-update-by-'.$lastMessage->from_id.'' == ''.$lastMessage->body.'')
        <td>
        <p data-id="{{ $type.'_'.$user->id }}">
            {{ strlen($user->group_chat_name) > 30 ? trim(substr($user->group_chat_name,0,30)).'..' : $user->group_chat_name }} 
            <span>{{ $lastMessage->created_at->diffForHumans() }}</span></p>
        <span>
            {{-- Last Message user indicator --}}
            {!!
                $lastMessage->from_id == Auth::user()->id 
                ? '<span class="lastMessageIndicator">You :</span>'
                : $from_name
            !!}
            {{-- Last message body --}}

            Update the group Chat information
        </span>
        {{-- New messages counter --}}
            {!! $unseenCounter > 0 ? "<b>".$unseenCounter."</b>" : '' !!}
        </td>
        @endif
        
        @if($user_id.'-GC-'.$lastMessage->to_id.'-added-by-'.$lastMessage->from_id.'' == ''.$lastMessage->body.'')
        <td>
        <p data-id="{{ $type.'_'.$user->id }}">
            {{ strlen($user->group_chat_name) > 30 ? trim(substr($user->group_chat_name,0,30)).'..' : $user->group_chat_name }} 
            <span>{{ $lastMessage->created_at->diffForHumans() }}</span></p>
        <span>
            {{-- Last Message user indicator --}}
            {!!
                $lastMessage->from_id == Auth::user()->id 
                ? '<span class="lastMessageIndicator">You :</span>'
                : $from_name
            !!}
            {{-- Last message body --}}

            {{ $member_name }} was added to this group chat.
        </span>
        {{-- New messages counter --}}
            {!! $unseenCounter > 0 ? "<b>".$unseenCounter."</b>" : '' !!}
        </td>
        @endif

        @if($user_id.'-GC-'.$lastMessage->to_id.'-removed-by-'.$lastMessage->from_id.'' == ''.$lastMessage->body.'')
        <td>
        <p data-id="{{ $type.'_'.$user->id }}">
            {{ strlen($user->group_chat_name) > 30 ? trim(substr($user->group_chat_name,0,30)).'..' : $user->group_chat_name }} 
            <span>{{ $lastMessage->created_at->diffForHumans() }}</span></p>
        <span>
            {{-- Last Message user indicator --}}
            {!!
                $lastMessage->from_id == Auth::user()->id 
                ? '<span class="lastMessageIndicator">You :</span>'
                : $from_name
            !!}
            {{-- Last message body --}}

            {{ $member_name }} was removed from this group chat.
        </span>
        {{-- New messages counter --}}
            {!! $unseenCounter > 0 ? "<b>".$unseenCounter."</b>" : '' !!}
        </td>
        @endif


        @if('GC-'.$lastMessage->to_id.'-update-by-'.$lastMessage->from_id.'' != ''.$lastMessage->body.'' && 'GC-'.$lastMessage->to_id.'-leave-by-'.$lastMessage->from_id.'' != ''.$lastMessage->body.'' && 'GC-'.$lastMessage->to_id.'-created-by-'.$lastMessage->from_id.'' != ''.$lastMessage->body.'' && $user_id.'-GC-'.$lastMessage->to_id.'-added-by-'.$lastMessage->from_id.'' != ''.$lastMessage->body.'' && $user_id.'-GC-'.$lastMessage->to_id.'-removed-by-'.$lastMessage->from_id.'' != ''.$lastMessage->body.'')
        <td>
        <p data-id="{{ $type.'_'.$user->id }}">
            {{ strlen($user->group_chat_name) > 30 ? trim(substr($user->group_chat_name,0,30)).'..' : $user->group_chat_name }} 
            <span>{{ $lastMessage->created_at->diffForHumans() }}</span></p>
        <span>
            {{-- Last Message user indicator --}}
            {!!
                $lastMessage->from_id == Auth::user()->id 
                ? '<span class="lastMessageIndicator">You :</span>'
                : ''
            !!}
            {{-- Last message body --}}
            @if($lastMessage->attachment == null)
            {!!
                strlen($lastMessage->body) > 30 
                ? trim(substr($lastMessage->body, 0, 30)).'..'
                : $lastMessage->body
            !!}
            @else
            <span class="fas fa-file"></span> Attachment
            @endif
        </span>
        {{-- New messages counter --}}
            {!! $unseenCounter > 0 ? "<b>".$unseenCounter."</b>" : '' !!}
        </td>
        @endif
    </tr>
</table>
@endif


{{-- -------------------- Search Item -------------------- --}}
@if($get == 'search_item')
     @if($type == "user")           
        <table class="messenger-list-item" data-contact="user-{{ $user->id }}">
            <tr data-action="0">
                {{-- Avatar side --}}
                <td>
                <div class="avatar av-m"
                style="background-image: url('{{ $user->image }}');">
                </div>
                </td>
                {{-- center side --}}
                <td>
                <p data-id="{{ $type.'_'.$user->id }}">
                    {{ strlen($user->first_name.' '.$user->last_name) > 30 ? trim(substr($user->first_name.' '.$user->last_name,0,30)).'..' : $user->first_name.' '.$user->last_name }}  
                </td>
            </tr>
        </table>
    @endif
    @if($type == "group")           
        <table class="messenger-list-item" data-contact="group-{{ $group->id }}">
            <tr data-action="0">
                {{-- Avatar side --}}
                <td>
                <div class="avatar av-m" style="background-image: url('{{ $group->avatar }}');">
                </div>
                </td>
                {{-- center side --}}
                <td>
                <p data-id="{{ $type.'_'.$group->id }}">
                    {{ strlen($group->group_chat_name.' '.$group->group_chat_name) > 30 ? trim(substr($group->group_chat_name,0,30)).'..' : $group->group_chat_name }}  
                </td>
            </tr>
        </table>
    @endif
@endif

@if($get == 'list_of_members')
                    <table class="messenger-list-item" style="margin-top:20px; margin-bottom:20px;" id="{{ $list->id }}">
                        <tr data-action="0">
                            <td>
                            <div class="avatar av-m"
                            style="background-image: url('{{ $contact->image }}');">
                            </div>
                            </td>
                            <td>
                            <p>{{ $contact->first_name.' '.$contact->last_name }} </br> {{ $type }}</p>  
                            </td>
                            <td style="text-align:end;">
                            @if($member_type == 'Administrator')
                                @if($type != 'Administrator')
                                    <button class="app-btn a-btn-danger btn-remove-member" id="{{ $list->id }}">remove</button>
                                @endif
                            @endif
                            </td>
                        </tr>
                    </table>
@endif

{{-- -------------------- Shared photos Item -------------------- --}}
@if($get == 'sharedPhoto')
<div class="shared-photo chat-image" style="background-image: url('{{ 'storage/public/attachments/'.$image.'' }}');"></div>
@endif


