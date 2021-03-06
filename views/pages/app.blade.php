@include('Chatify::layouts.headLinks')
<div class="messenger">
    {{-- ----------------------Users/Groups lists side---------------------- --}}
    <div class="messenger-listView" id="chatHistory">
        {{-- Header and search bar --}}
        <div class="m-header">
            <nav>
                <div>
                    <div class="btn-group dropright">
                        <button type="button" class="btn p-0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <div class="avatar av-m" 
                            style="background-image: url('{{ asset(Auth::user()->image) }}');">
                            </div>
                        </button>
                        <div class="dropdown-menu">
                            <a href="#" class="dropdown-item settings-btn" style="color: #6c7283;">Settings</a>
                            <a href="#" class="dropdown-item" id="showContacts" style="color: #6c7283;">Show Contacts</a>
                            <hr class="my-2">
                            <a href="/logout" class="dropdown-item" style="color: #6c7283;">Logout</a>
                        </div>
                    </div>
                    <span class="messenger-headTitle {{Auth::user()->dark_mode == '1' ? 'text-white' : ''}}">{{Auth::user()->first_name.' '.Auth::user()->last_name}}</span> 
                    {{-- header buttons --}}
                    <nav class="m-header-right my-2">
                        <a href="#"><i class="fas fa-cog settings-btn"></i></a>
                        <a href="#" class="listView-x"><i class="fas fa-times"></i></a>
                    </nav>
                </div>
            </nav>
            {{-- Search input --}}
            <input type="text" class="messenger-search from-control" placeholder="Search" />
            {{-- Tabs --}}
            <div class="messenger-listView-tabs">
                <a href="#" @if($route == 'user') class="active-tab" @endif data-view="users">
                    <span class="far fa-user"></span> People</a>
                <a href="#" @if($route == 'group') class="active-tab" @endif data-view="groups">
                    <span class="fas fa-users"></span> Groups</a>
            </div>
        </div>
        {{-- tabs and lists --}}
        <div class="m-body">
           {{-- Lists [Users/Group] --}}
           {{-- ---------------- [ User Tab ] ---------------- --}}
           <div class="@if($route == 'user') show @endif messenger-tab" data-view="users">

               {{-- Favorites --}}
               <p class="messenger-title">Favorites</p>
                <div class="messenger-favorites app-scroll-thin"></div>

               {{-- Saved Messages --}}
               {!! view('Chatify::layouts.listItem', ['get' => 'saved','id' => $id])->render() !!}

               {{-- Contact --}}
               <div class="listOfContacts" style="width: 100%;height: calc(100% - 200px);"></div>
               
           </div>

            {{-- ---------------- [ Group Tab ] ---------------- --}}
            <div class="@if($route == 'group') show @endif messenger-tab" data-view="groups">
                {{-- items --}}
                <table class="messenger-list-item-group m-li-divider create-group">
                    <tr data-action="0">
                        <td>
                        <div class="avatar av-m" style="background-color: #d9efff; text-align: center;">
                            <span class="fa fa-users" style="font-size: 22px; color: #68a5ff; margin-top: calc(50% - 10px);"></span>
                        </div>
                        </td>
                        <td>
                            <p data-id="user_1">Create a new group</p>
                            <span>Make a conversation with a group of people</span>
                        </td>
                    </tr>
                </table>

                <div class="listOfContacts" style="width: 100%;height: calc(100% - 200px);"></div>
            </div>

             {{-- ---------------- [ Search Tab ] ---------------- --}}
           <div class="messenger-tab" data-view="search">
                {{-- items --}}
                <p class="messenger-title">Search</p>
                <div class="search-records">
                    <p class="message-hint"><span>Type to search..</span></p>
                </div>
             </div>
        </div>
    </div>

    <div class="messenger-listView d-none {{Auth::user()->dark_mode == '1' ? 'text-white' : ''}}" id="contacts">
        <div class="m-header">
            <nav>
                <a href="#" class="btn" id="closeContacts"><i class="fas fa-arrow-left"></i></a>
                <span>Contacts</span>
            </nav>
        </div>
        <div class="mt-5 ml-4"> 
            {{-- <div class="row">
                <div class="col 6">
                    <select id="countryFilter" class="form-control bg-dark text-white" name="">
                        <option disabled selected>Filter By Country</option>
                        @foreach ($countries as $country)
                            <option value="{{$country->id}}">{{$country->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 pl-0">
                    <select id="departmentFilter" class="form-control bg-dark text-white d-none" name="">
                        <option disabled selected>Filter By Department</option>
                    </select>
                </div>
            </div> --}}
            <div class="online-offline-tabs">
                <a href="#" contact-status="online" class="active-tab">
                    <span class="count badge badge-success">{{$onlineCount}}</span> Online </span></a>
                <a href="#" contact-status="offline" class="">
                    <span class="count badge badge-secondary">{{$offlineCount}}</span> Offline </a>
            </div>
            <div class="messenger-tab  {{Auth::user()->dark_mode == '1' ? 'text-white' : ''}}" data-view="contacts">
                <hr class="my-2">
                <div id="listOfDeptContacts"><div class="text-center">Loading contacts...</div></div>
            </div>
        </div>
    </div>

    {{-- ----------------------Messaging side---------------------- --}}
    <div class="messenger-messagingView">
        {{-- header title [conversation name] amd buttons --}}
        <div class="m-header m-header-messaging">
            <nav>
                {{-- header back button, avatar and user name --}}
                <div style="display: inline-flex;">
                    <a href="#" class="show-listView"><i class="fas fa-arrow-left"></i></a>
                    <div class="avatar av-s header-avatar" style="margin: 0px 10px; margin-top: -5px; margin-bottom: -5px;">
                    </div>
                    <a href="#" class="user-name">{{ config('chatify.name') }}</a>
                </div>
                {{-- header buttons --}}
                <nav class="m-header-right">
                    <a href="#" class="add-to-favorite"><i class="fas fa-star"></i></a>
                    <a href="#" class="group-settings" style="display:none;"><i class="fas fa-cogs"></i></a>
                    <a href="#" class="add-members" style="display:none;"><i class="fas fa-user-plus"></i></a>
                    <a href="{{ route('chatify.chatify') }}"><i class="fas fa-home"></i></a>
                    <a href="#" class="show-infoSide"><i class="fas fa-info-circle"></i></a>
                </nav>
            </nav>
        </div>
        {{-- Internet connection --}}
        <div class="internet-connection">
            <span class="ic-connected">Connected</span>
            <span class="ic-connecting">Connecting...</span>
            <span class="ic-noInternet">No internet access</span>
        </div>
        {{-- Messaging area --}}
        <div class="m-body app-scroll">
            <div class="messages" style="margin-bottom:10px;">
                <p class="message-hint" style="margin-top: calc(30% - 126.2px);"><span>Please select a chat to start messaging</span></p>
            </div>
            {{-- Typing indicator --}}
            <div class="typing-indicator">
                <div class="message-card typing">
                    <p>
                        <span class="typing-dots">
                            <span class="dot dot-1"></span>
                            <span class="dot dot-2"></span>
                            <span class="dot dot-3"></span>
                        </span>
                    </p>
                </div>
            </div>
            {{-- Send Message Form --}}
            @include('Chatify::layouts.sendForm')
        </div>
    </div>
    {{-- ---------------------- Info side ---------------------- --}}
    <div class="messenger-infoView app-scroll">
        {{-- nav actions --}}
        <nav>
            <a href="#"><i class="fas fa-times"></i></a>
        </nav>
        {!! view('Chatify::layouts.info')->render() !!}
    </div>
</div>

@include('Chatify::layouts.modals')
@include('Chatify::layouts.footerLinks')