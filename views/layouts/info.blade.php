{{-- user info and avatar --}}
<div class="avatar av-l"></div>
<p class="info-name">{{ config('chatify.name') }}</p>

<ul class="list-group chat-layers" style="margin-top:20px; display:none;">
  <li class="list-group-item d-flex justify-content-between align-items-center messenger-infoView-btns delete-conversation" style="cursor:pointer;">
    Delete Conversation
    <span class="badge badge-primary badge-pill"><i class="fa fa-trash"></i></span>
  </li>
  <div id="leave-conversation" style="display:none; cursor:pointer;">
  <li class="list-group-item d-flex justify-content-between align-items-center">
    Leave Group
    <span class="badge badge-primary badge-pill"><i class="fa fa-door-open"></i></span>
  </li>
  </div>
  <li class="list-group-item d-flex justify-content-between align-items-center">
    Shared Photos
    <span class="badge badge-primary badge-pill btn-hide-shared-photos" style="cursor:pointer;"><i id="shared-photo-icon" class="fa fa-chevron-circle-down"></i></span>
  </li>
  <li class="list-group-item shared-photos-list align-items-center" style="padding-left:39px;">    
    {{-- shared photos --}}
    <div class="messenger-infoView-shared">
        <div class="shared-photos-list"></div>
    </div>
  </li>
</ul>