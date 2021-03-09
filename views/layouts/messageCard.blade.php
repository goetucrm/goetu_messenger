{{-- -------------------- The default card (white) -------------------- --}}


@if($viewType == 'default')
    @if($from_id != $to_id)
    @if('GC-'.$to_id.'-created-by-'.$from_id.'' == ''.$message.'')
        <div style="text-align:center;" data-id="{{ $id }}">
            <p class="sender-name"><strong>{{ $from_id_name }}</strong> created this Group Chat. </p>
        </div>
    @endif
    @if('GC-'.$to_id.'-leave-by-'.$from_id.'' == ''.$message.'')
        <div style="text-align:center;" data-id="{{ $id }}">
            <p class="sender-name"><strong>{{ $from_id_name }}</strong> leave the Group Chat. </p>
        </div>
    @endif
    @if($member_id.'-GC-'.$to_id.'-added-by-'.$from_id.'' == ''.$message.'')
        <div style="text-align:center;" data-id="{{ $id }}">
            <p class="sender-name"><strong>{{ $member_name }}</strong> added by <strong>{{ $from_id_name }}</strong>. </p>
        </div>
    @endif
    @if('GC-'.$to_id.'-update-by-'.$from_id.'' == ''.$message.'')
        <div style="text-align:center;" data-id="{{ $id }}">
            <p class="sender-name"><strong>{{ $from_id_name }}</strong> update the Group Chat information. </p>
        </div>
    @endif
    @if($member_id.'-GC-'.$to_id.'-removed-by-'.$from_id.'' == ''.$message.'')
        <div style="text-align:center;" data-id="{{ $id }}">
            <p class="sender-name"><strong>{{ $member_name }}</strong> was remove by <strong>{{ $from_id_name }}</strong>. </p>
        </div>
    @endif
    @if('GC-'.$to_id.'-created-by-'.$from_id.'' != ''.$message.'' && 'GC-'.$to_id.'-leave-by-'.$from_id.'' != ''.$message.'' && $member_id.'-GC-'.$to_id.'-added-by-'.$from_id.'' != ''.$message.'' && $member_id.'-GC-'.$to_id.'-removed-by-'.$from_id.'' != ''.$message.'' && 'GC-'.$to_id.'-update-by-'.$from_id.'' != ''.$message.'')
    <div style="margin-top:15px;">
        <p class="sender-name" style="margin-left:70px; font-size:10px; margin-bottom:0px;"><strong>{{ $from_id_name }}</strong> | <small title="{{ $fullTime }}" style="font-size:8px;"> {{ $time }}</small></p>
        <div class="message-card" data-id="{{ $id }}">
            <div class="avatar av-m mr-2" style="background-image: url('{{ file_exists(public_path($image)) ? asset($image) : asset('/storage/users-avatar/avatar.png')  }}');"></div>
            <p>{!! ($message == null && $attachment != null && @$attachment[2] != 'file') ? $attachment[1] : nl2br($message) !!}
                {{-- If attachment is a file --}}
                @if(@$attachment[2] == 'file')
                <a href="{{ route(config('chatify.attachments.route'),['fileName'=>$attachment[0]]) }}" style="color: #595959;" class="file-download">
                    <span class="fas fa-file"></span> {{$attachment[1]}}</a>
                @endif
            </p>
        </div>
    </div>
    {{-- If attachment is an image --}}
    @if($attachment[2] == 'image')
    <div>
        <div class="message-card pl-5">
            <div class="image-file chat-image" style="width: 250px; height: 150px;background-image: url('{{file_exists(public_path('storage/public/attachments/'.$attachment[0])) ? url('storage/public/attachments/'.$attachment[0]) : url((App::environment() == 'local' ? config('services.mobile_link').'storage/storage/public/attachments/'.$attachment[0] : config('services.mobile_link').'storage/'.$attachment[0]))}}')">
            </div>
        </div>
    </div>
    @endif
    @endif
    @endif
@endif

{{---------------------- Sender card (owner) -------------------- --}}
@if($viewType == 'sender')
    @if('GC-'.$to_id.'-created-by-'.$from_id.'' == ''.$message.'')
        <div style="text-align:center;" data-id="{{ $id }}">
            <p class="sender-name"><strong>{{ $from_id_name }}</strong> created this Group Chat. </p>
        </div>
    @endif
    @if('GC-'.$to_id.'-leave-by-'.$from_id.'' == ''.$message.'')
        <div style="text-align:center;" data-id="{{ $id }}">
            <p class="sender-name"><strong>{{ $from_id_name }}</strong> leave the Group Chat. </p>
        </div>
    @endif
    @if($member_id.'-GC-'.$to_id.'-added-by-'.$from_id.'' == ''.$message.'')
        <div style="text-align:center;" data-id="{{ $id }}">
            <p class="sender-name"><strong>{{ $member_name }}</strong> added by <strong>{{ $from_id_name }}</strong>. </p>
        </div>
    @endif
    @if('GC-'.$to_id.'-update-by-'.$from_id.'' == ''.$message.'')
        <div style="text-align:center;" data-id="{{ $id }}">
            <p class="sender-name"><strong>{{ $from_id_name }}</strong> update the Group Chat information.</p>
        </div>
    @endif
    @if($member_id.'-GC-'.$to_id.'-removed-by-'.$from_id.'' == ''.$message.'')
        <div style="text-align:center;" data-id="{{ $id }}">
            <p class="sender-name"><strong>{{ $member_name }}</strong> was remove by <strong>{{ $from_id_name }}</strong>. </p>
        </div>
    @endif
    @if('GC-'.$to_id.'-created-by-'.$from_id.'' != ''.$message.'' && 'GC-'.$to_id.'-leave-by-'.$from_id.'' != ''.$message.'' && $member_id.'-GC-'.$to_id.'-added-by-'.$from_id.'' != ''.$message.'' && $member_id.'-GC-'.$to_id.'-removed-by-'.$from_id.'' != ''.$message.'' && 'GC-'.$to_id.'-update-by-'.$from_id.'' != ''.$message.'')
    <p class="sender-name" style="text-align:end; margin-right:70px; font-size:10px; margin-bottom:0px;"><strong>You </strong> | <small title="{{ $fullTime }}" style="font-size:8px;"> {{ $time }}</small></p>
    <div class="message-card mc-sender" data-id="{{ $id }}">
        <div class="avatar av-m ml-2" style="background-image: url('{{ file_exists(public_path($image)) ? asset($image) : asset('/storage/users-avatar/avatar.png')  }}');"></div>
        <p>{!! ($message == null && $attachment != null && @$attachment[2] != 'file') ? $attachment[1] : nl2br($message) !!}
            @if(@$attachment[2] == 'file')
            <a href="{{ route(config('chatify.attachments.route'),['fileName'=>$attachment[0]]) }}" class="file-download">
                <span class="fas fa-file"></span> {{$attachment[1]}}</a>
            @endif
            <sub title="{{ $fullTime }}" class="message-time" style="font-size:8px; margin-left:0px;">
                <span class="fas fa-{{ $seen > 0 ? 'check-double' : 'check' }} seen"></span>
            </sub>
            
            {{-- If attachment is a file --}}
        </p>
    </div>
    {{-- If attachment is an image --}}
    @if(@$attachment[2] == 'image')
    <div>
        <div class="message-card mc-sender pr-5">
            <div class="image-file chat-image" style="width: 250px; height: 150px;background-image: url('{{file_exists(public_path('storage/public/attachments/'.$attachment[0])) ? url('storage/public/attachments/'.$attachment[0]) : url((App::environment() == 'local' ? config('services.mobile_link').'storage/storage/public/attachments/'.$attachment[0] : config('services.mobile_link').'storage/'.$attachment[0]))}}')">
            </div>
        </div>
    </div>
    @endif
    @endif
@endif