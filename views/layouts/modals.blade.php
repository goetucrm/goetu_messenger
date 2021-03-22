{{-- ---------------------- Image modal box ---------------------- --}}
<div id="imageModalBox" class="imageModal">
    <span class="imageModal-close">&times;</span>
    <img class="imageModal-content" id="imageModalBoxSrc">
  </div>
  
  {{-- ---------------------- Delete Modal ---------------------- --}}
  <div class="app-modal" data-name="delete">
      <div class="app-modal-container">
          <div class="app-modal-card" data-name="delete" data-modal='0'>
              <div class="app-modal-header">Are you sure you want to delete this?</div>
              <div class="app-modal-body">You can not undo this action</div>
              <div class="app-modal-footer">
                  <a href="javascript:void(0)" class="app-btn cancel">Cancel</a>
                  <a href="javascript:void(0)" class="app-btn a-btn-danger delete">Delete</a>
              </div>
          </div>
      </div>
  </div>
  {{-- ---------------------- Alert Modal ---------------------- --}}
  <div class="app-modal" data-name="alert">
      <div class="app-modal-container">
          <div class="app-modal-card" data-name="alert" data-modal='0'>
              <div class="app-modal-header"></div>
              <div class="app-modal-body"></div>
              <div class="app-modal-footer">
                  <a href="javascript:void(0)" class="app-btn cancel">Cancel</a>
              </div>
          </div>
      </div>
  </div>
  {{-- ---------------------- Settings Modal ---------------------- --}}
  <div class="app-modal" data-name="settings">
      <div class="app-modal-container">
          <div class="app-modal-card" data-name="settings" data-modal='0'>
              <form id="updateAvatar" action="{{ route('chatify.avatar.update') }}" enctype="multipart/form-data" method="POST">
                  @csrf
                  <div class="app-modal-header">Update your profile settings</div>
                  <div class="app-modal-body">
                      {{-- Udate profile avatar --}}
                      <div class="avatar av-l upload-avatar-preview"
                      style="background-image: url('{{ Auth::user()->image }}');"
                      ></div>
                      <p class="upload-avatar-details"></p>
                      <label class="app-btn a-btn-primary update">
                          Upload profile photo
                          <input class="upload-avatar" accept="image/*" name="avatar" type="file" style="display: none" />
                      </label>
                      {{-- Dark/Light Mode  --}}
                      <p class="divider"></p>
                      <p class="app-modal-header">Dark Mode <span class="
                        {{ Auth::user()->dark_mode > 0 ? 'fas' : 'far' }} fa-moon dark-mode-switch"
                         data-mode="{{ Auth::user()->dark_mode > 0 ? 1 : 0 }}"></span></p>
                      {{-- change messenger color  --}}
                      <p class="divider"></p>
                      <p class="app-modal-header">Change {{ config('chatify.name') }} Color</p>
                      <div class="update-messengerColor">
                            <a href="javascript:void(0)" class="messengerColor-1"></a>
                            <a href="javascript:void(0)" class="messengerColor-2"></a>
                            <a href="javascript:void(0)" class="messengerColor-3"></a>
                            <a href="javascript:void(0)" class="messengerColor-4"></a>
                            <a href="javascript:void(0)" class="messengerColor-5"></a>
                            <br/>
                            <a href="javascript:void(0)" class="messengerColor-6"></a>
                            <a href="javascript:void(0)" class="messengerColor-7"></a>
                            <a href="javascript:void(0)" class="messengerColor-8"></a>
                            <a href="javascript:void(0)" class="messengerColor-9"></a>
                            <a href="javascript:void(0)" class="messengerColor-10"></a>
                      </div>
                  </div>
                  <div class="app-modal-footer">
                      <a href="javascript:void(0)" class="app-btn cancel">Cancel</a>
                      <input type="submit" class="app-btn a-btn-success update" value="Update" />
                  </div>
              </form>
          </div>
      </div>
  </div>

  {{-- ---------------------- New Group Chat Modal ---------------------- --}}
  <div class="app-modal" data-name="new-group-chat">
      <div class="app-modal-container">
          <div class="app-modal-card" data-name="new-group-chat" data-modal='0'>
                  <div class="app-modal-header">Create a new Group Chat</div>
                  <div class="app-modal-body">
                      <div class="avatar av-l upload-group-avatar-preview" style="background-image: url('https://icons-for-free.com/iconfiles/png/512/human+men+people+users+icon-1320196167246530600.png');"></div>
                      <p class="upload-avatar-details"></p>
                      <label class="app-btn a-btn-primary update">
                          Click here upload group profile photo
                          <input class="upload-group-avatar" accept="image/*"  id="txtGroupChatAvatar" type="file" style="display: none" />
                      </label>
                      <input type="text" id="txtGroupChatName" placeholder="Enter the Group Chat Name" style="text-align:center; width:64%; border-radius:5px; margin-top:10px;">
                  </div>
                  <div class="app-modal-footer">
                      <a href="javascript:void(0)" class="app-btn cancel">Cancel</a>
                      <input type="submit" class="app-btn a-btn-success create-group-chat" value="Create" />
                  </div>
              </form>
          </div>
      </div>
  </div>

  {{-- ---------------------- Update Group Chat Modal ---------------------- --}}
  <div class="app-modal" data-name="update-group-chat">
      <div class="app-modal-container">
          <div class="app-modal-card" data-name="update-group-chat" data-modal='0'>
                  <div class="app-modal-header">Update the Group Chat Information</div>
                  <div class="app-modal-body">
                      <div class="avatar av-l update-upload-group-avatar-preview mx-auto" style="background-image: url('https://icons-for-free.com/iconfiles/png/512/human+men+people+users+icon-1320196167246530600.png');"></div>
                      <p class="upload-avatar-details"></p>
                      <label class="app-btn a-btn-primary update">
                          Click here to choose the new group profile photo
                          <input class="update-upload-group-avatar" accept="image/*"  id="txtUpdateGroupChatAvatar" type="file" style="display: none" />
                      </label>
                      <input type="text" id="txtUpdateGroupChatName" placeholder="Enter the Group Chat Name" style="text-align:center; width:64%; border-radius:5px; margin-top:10px;">
                  </div>
                  <div class="app-modal-footer">
                      <a href="javascript:void(0)" class="app-btn cancel-update">Cancel</a>
                      <input type="submit" class="app-btn a-btn-success update-group-chat" value="Create" />
                  </div>
              </form>
          </div>
      </div>
  </div>

  
  {{-- ---------------------- Group Chat Created Modal ---------------------- --}}
  <div class="app-modal" data-name="group-chat-created">
      <div class="app-modal-container">
          <div class="app-modal-card" data-name="group-chat-created" data-modal='0'>
          <div class="avatar av-l group-chat-success" style="background-image: url('https://w0.pngwave.com/png/873/563/computer-icons-icon-design-business-success-png-clip-art-thumbnail.png');"></div>
              <div class="app-modal-header">Group Chat created successfully!</div>
              <div class="app-modal-body">You can now start conversation with your contacts.</div>
              <div class="app-modal-footer">
                  <a href="javascript:void(0)" class="app-btn a-btn-success btn-okay">Okay</a>
              </div>
          </div>
      </div>
  </div>

  {{-- ---------------------- Group Chat Added Member Modal ---------------------- --}}
  <div class="app-modal" data-name="added-member">
      <div class="app-modal-container">
          <div class="app-modal-card" data-name="added-member" data-modal='0'>
          <div class="avatar av-l group-chat-success" style="background-image: url('https://w0.pngwave.com/png/873/563/computer-icons-icon-design-business-success-png-clip-art-thumbnail.png');"></div>
              <div class="app-modal-header">Member Added Successfully!</div>
              <div class="app-modal-body">You can now start conversation with your contacts.</div>
              <div class="app-modal-footer">
                  <a href="javascript:void(0)" class="app-btn a-btn-success member-btn-okay">Okay</a>
              </div>
          </div>
      </div>
  </div>

  {{-- ---------------------- Group Chat Added Member Modal ---------------------- --}}
  <div class="app-modal" data-name="removed-member">
      <div class="app-modal-container">
          <div class="app-modal-card" data-name="removed-member" data-modal='0'>
          <div class="avatar av-l group-chat-success" style="background-image: url('https://w0.pngwave.com/png/873/563/computer-icons-icon-design-business-success-png-clip-art-thumbnail.png');"></div>
              <div class="app-modal-header">Member Removed Successfully!</div>
              <div class="app-modal-footer">
                  <a href="javascript:void(0)" class="app-btn a-btn-success remove-member-btn-okay">Okay</a>
              </div>
          </div>
      </div>
  </div>

  {{-- ---------------------- Group Chat Created Modal ---------------------- --}}
  <div class="app-modal" data-name="group-chat-members">
      <div class="app-modal-container" style="margin-top:-100px;">
          <div class="app-modal-card" data-name="group-chat-members" data-modal='0'>
          <div class="avatar av-l group-chat-success" style="background-image: url('https://www.paidmembershipspro.com/wp-content/uploads/2014/01/Roles-for-members-300x300.png');"></div>
              <div class="app-modal-header"></div>
                <div class="app-modal-body">
                    Manage the members of this Group Chat.
                    <div style="overflow: scroll; overflow-x: hidden; height:70px; margin-top:20px; margin-bottom:20px;">
                        <select id="txtSelectMembers" multiple="multiple" style="width: 100%;"></select>
                    </div>
                    <div id="listOfMember" style="overflow: scroll; overflow-x: hidden; height:200px; margin-top:20px; margin-bottom:20px;">
                    </div>
                </div>
                <div class="app-modal-footer">
                  <a href="javascript:void(0)" class="app-btn cancel-manage-members">Cancel</a>
                  <a href="javascript:void(0)" class="app-btn a-btn-success btn-save-members">Save</a>
                </div>
          </div>
      </div>
  </div>

  
  {{-- ---------------------- Group Chat Created Modal ---------------------- --}}
  <div class="app-modal" data-name="update-chat-success">
      <div class="app-modal-container">
          <div class="app-modal-card" data-name="update-chat-success" data-modal='0'>
          <div class="avatar av-l group-chat-success" style="background-image: url('https://w0.pngwave.com/png/873/563/computer-icons-icon-design-business-success-png-clip-art-thumbnail.png');"></div>
              <div class="app-modal-header">Group Chat information updated successfully!</div>
              <div class="app-modal-footer">
                  <a href="javascript:void(0)" class="app-btn a-btn-success btn-okay-update">Okay</a>
              </div>
          </div>
      </div>
  </div>

  {{------------------------- Delete Modal ------------------------}}
  <div class="app-modal" data-name="leave">
      <div class="app-modal-container">
          <div class="app-modal-card" data-name="leave" data-modal='0'>
              <div class="app-modal-header">Are you sure you want to leave this group?</div>
              <div class="app-modal-body">You can not longer share conversation with this group.</div>
              <div class="app-modal-footer">
                  <a href="javascript:void(0)" class="app-btn cancel-leave">Cancel</a>
                  <a href="javascript:void(0)" class="app-btn a-btn-danger leave">Leave</a>
              </div>
          </div>
      </div>
  </div>

