
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

<script>
  // Enable pusher logging - don't include this in production
  Pusher.logToConsole = true;
  var pusher = new Pusher("{{ config('chatify.pusher.key') }}", {
    encrypted: true,
    cluster: "{{ config('chatify.pusher.options.cluster') }}",
    authEndpoint: '{{route("chatify.pusher.auth")}}',
    auth: {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    }
  });
</script>
<script>
  // Messenger global variable - 0 by default
  messenger = "{{ @$id }}";
</script>
<script>
/**
 *-------------------------------------------------------------
 * Global variables
 *-------------------------------------------------------------
 */
var messenger,
    auth_id = $('meta[name=url]').attr('data-user'),
    route = $('meta[name=route]').attr('content'),
    url = $('meta[name=url]').attr('content'),
    access_token = $('meta[name="csrf-token"]').attr('content'),
    typingTimeout,
    typingNow = 0,
    temporaryMsgId = 0,
    loading = 0,
    last_date = null,
    defaultAvatarInSettings = null,
    messengerColor,
    searchingMode,
    dark_mode;
const messagesContainer = $('.messenger-messagingView .m-body'),
    messengerTitleDefault = $('.messenger-headTitle').text(),
    messageInput = $('#message-form .m-send');
// console.log(auth_id);

/**
 *-------------------------------------------------------------
 * Global Templates
 *-------------------------------------------------------------
 */
// Loading svg
function loadingSVG(w_h = '25px', className = null) {
    return `
    <svg class="loadingSVG `+ className + `" xmlns="http://www.w3.org/2000/svg" width="` + w_h + `" height="` + w_h + `" viewBox="0 0 40 40" stroke="#2196f3">
      <g fill="none" fill-rule="evenodd">
        <g transform="translate(2 2)" stroke-width="3">
          <circle stroke-opacity=".1" cx="18" cy="18" r="18"></circle>
          <path d="M36 18c0-9.94-8.06-18-18-18" transform="rotate(349.311 18 18)">
              <animateTransform attributeName="transform" type="rotate" from="0 18 18" to="360 18 18" dur=".8s" repeatCount="indefinite"></animateTransform>
          </path>
        </g>
      </g>
    </svg>
    `;
}

// loading placeholder for users list item
function listItemLoading(items) {
    let template = '';
    for (let i = 0; i < items; i++) {
        template += `
        <div class="loadingPlaceholder">
          <div class="loadingPlaceholder-wrapper">
            <div class="loadingPlaceholder-body">
            <table class="loadingPlaceholder-header">
              <tr>
                <td style="width: 45px;"><div class="loadingPlaceholder-avatar"></div></td>
                <td>
                  <div class="loadingPlaceholder-name"></div>
                      <div class="loadingPlaceholder-date"></div>
                </td>
              </tr>
            </table>
            </div>
          </div>
      </div>
        `;
    }
    return template;
}


// loading placeholder for avatars
function avatarLoading(items) {
    let template = '';
    for (let i = 0; i < items; i++) {
        template += `
        <div class="loadingPlaceholder">
        <div class="loadingPlaceholder-wrapper">
            <div class="loadingPlaceholder-body">
                <table class="loadingPlaceholder-header">
                    <tr>
                        <td style="width: 45px;">
                            <div class="loadingPlaceholder-avatar" style="margin: 2px;"></div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        </div>
        `;
    }
    return template;
}

// While sending a message, show this temporary message card.
function sendigCard(message, id) {
    return `
    <div class="message-card mc-sender" data-id="`+ id + `">
        <p>`+ message + `<sub><span class="far fa-clock"></span></sub></p>
    </div>
    `;
}
// upload image preview card.
function attachmentTemplate(fileType, fileName, imgURL = null) {
    if (fileType != 'image') {
        return`
        <div class="attachment-preview">
            <span class="fas fa-times cancel"></span>
            <p style="padding:0px 30px;"><span class="fas fa-file"></span> `+ fileName + `</p>
        </div>
        `;
    } else {
        return `
        <div class="attachment-preview">
            <span class="fas fa-times cancel"></span>
            <div class="image-file chat-image" style="background-image: url('`+ imgURL + `');"></div>
            <p><span class="fas fa-file-image"></span> `+ fileName + `</p>
        </div>
        `;
    }
}

// Active Status Circle
function activeStatusCircle() {
    return `<span class="activeStatus"></span>`;
}

/**
 *-------------------------------------------------------------
 * Css Media Queries [For responsive design]
 *-------------------------------------------------------------
 */
$(window).resize(function () {
    cssMediaQueries();
});

function cssMediaQueries() {
    if (window.matchMedia('(min-width: 980px)').matches) {
        $('.messenger-listView').removeAttr('style');
    }
    if (window.matchMedia('(max-width: 980px)').matches) {
        $('body').find('.messenger-list-item').find('tr[data-action]').attr('data-action', '1');
        $('body').find('.favorite-list-item').find('div').attr('data-action', '1');
        $('body').find('.messenger-list-item-group').find('tr[data-action]').attr('data-action', '1');
        $('body').find('.favorite-list-item-group').find('div').attr('data-action', '1');
    } else {
        $('body').find('.messenger-list-item').find('tr[data-action]').attr('data-action', '0');
        $('body').find('.favorite-list-item').find('div').attr('data-action', '0');
        $('body').find('.messenger-list-item-group').find('tr[data-action]').attr('data-action', '0');
        $('body').find('.favorite-list-item-group').find('div').attr('data-action', '0');
    }
}


/**
*-------------------------------------------------------------
* App Modal
*-------------------------------------------------------------
*/
let app_modal = function ({
    show = true,
    name,
    data = 0,
    buttons = true,
    header = null,
    body = null,
}) {
    const modal = $('.app-modal[data-name=' + name + ']');
    // header
    header ? modal.find('.app-modal-header').html(header) : '';

    // body
    body ? modal.find('.app-modal-body').html(body) : '';

    // buttons
    buttons == true
        ? modal.find('.app-modal-footer').show()
        : modal.find('.app-modal-footer').hide();

    // show / hide
    if (show == true) {
        modal.show();
        $('.app-modal-card[data-name=' + name + ']').addClass('app-show-modal');
        $('.app-modal-card[data-name=' + name + ']').attr('data-modal', data);
    } else {
        modal.hide();
        $('.app-modal-card[data-name=' + name + ']').removeClass('app-show-modal');
        $('.app-modal-card[data-name=' + name + ']').attr('data-modal', data);
    }

};


/**
 *-------------------------------------------------------------
 * Slide to bottom on [action] - e.g. [message received, sent, loaded]
 *-------------------------------------------------------------
 */
function scrollBottom(container) {
    $(container).stop().animate({
        scrollTop: $(container)[0].scrollHeight
    });
}

/**
 *-------------------------------------------------------------
 * click and drag to scroll - function
 *-------------------------------------------------------------
 */
function hScroller(scroller) {
    const slider = document.querySelector(scroller);
    let isDown = false;
    let startX;
    let scrollLeft;

    slider.addEventListener('mousedown', (e) => {
        isDown = true;
        startX = e.pageX - slider.offsetLeft;
        scrollLeft = slider.scrollLeft;
    });
    slider.addEventListener('mouseleave', () => {
        isDown = false;
    });
    slider.addEventListener('mouseup', () => {
        isDown = false;
    });
    slider.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - slider.offsetLeft;
        const walk = (x - startX) * 1;
        slider.scrollLeft = scrollLeft - walk;
    });
}

/**
 *-------------------------------------------------------------
 * Disable/enable message form fields, messaging container...
 * on load info or if needed elsewhere.
 *
 * Default : true
 *-------------------------------------------------------------
 */
function disableOnLoad(action = true) {
    if (action == true) {
        // hide star button
        $('.add-to-favorite').hide();
        // hide send card
        $('.messenger-sendCard').hide();
        // add loading opacity to messages container
        messagesContainer.css('opacity', '.5');
        // disable message form fields
        messageInput.attr('readonly', 'readonly');
        $('#message-form button').attr('disabled', 'disabled');
        $('.upload-attachment').attr('disabled', 'disabled');
    } else {
        // show star button
        if (messenger.split('_')[1] != auth_id) {
            if(searchingMode == 'users'){
                $('.add-to-favorite').show();
            }
        }
        // show send card
        $('.messenger-sendCard').show();
        // remove loading opacity to messages container
        messagesContainer.css('opacity', '1');
        // enable message form fields
        messageInput.removeAttr('readonly');
        $('#message-form button').removeAttr('disabled');
        $('.upload-attachment').removeAttr('disabled');
    }
}

/**
 *-------------------------------------------------------------
 * Error message card
 *-------------------------------------------------------------
 */
function errorMessageCard(id) {
    messagesContainer.find('.message-card[data-id=' + id + ']').addClass('mc-error');
    messagesContainer.find('.message-card[data-id=' + id + ']').find('svg.loadingSVG').remove();
    messagesContainer.find('.message-card[data-id=' + id + '] p').prepend('<span class="fas fa-exclamation-triangle"></span>');
}

/**
 *-------------------------------------------------------------
 * Fetch id data (user/group) and update the view
 *-------------------------------------------------------------
 */
function IDinfo(id, type) {
    // clear temporary message id
    temporaryMsgId = 0;
    // clear typing now
    typingNow = 0;
    // show loading bar
    NProgress.start();
    // disable message form
    disableOnLoad();
    if (messenger != 0) {
        // get shared photos
        getSharedPhotos(id, type);
        // Get info

        console.log('type:'+type);
        $.ajax({
            url: url + '/idInfo',
            method: 'POST',
            data: { '_token': access_token, 'id': id, 'type': type },
            dataType: 'JSON',
            success: (data) => {
                // Show shared and actions
                $('.delete-conversation').show();
                $('.messenger-infoView-shared').show();
                // fetch messages
                fetchMessages(id, type);
                // focus on messaging input
                messageInput.focus();
                // update info in view
                $('.messenger-infoView .info-name').html(data.user_name);
                $('.m-header-messaging .user-name').html(data.user_name);
                $('.chat-layers').show();
                if(searchingMode != 'users'){
                    // Star status
                    data.favorite > 0
                    ? $('.add-to-favorite').addClass('favorite')
                    : $('.add-to-favorite').removeClass('favorite');
                    $('.add-to-favorite').hide();    
                }

                if(type == 'group'){
                    // avatar photo
                    $('.messenger-infoView').find('.avatar').css('background-image', 'url("' + data.user_avatar + '")');
                    $('.header-avatar').css('background-image', 'url("' + data.user_avatar + '")');
                    $('.add-members').show();
                    $('.group-settings').show();
                    $('#leave-conversation').show();
                }else{
                    // avatar photo
                    $('.messenger-infoView').find('.avatar').css('background-image', 'url("' + data.user_avatar + '")');
                    $('.header-avatar').css('background-image', 'url("' + data.user_avatar + '")');
                    $('.add-members').hide();
                    $('.group-settings').hide();
                    $('#leave-conversation').hide();
                }    

            
                // form reset and focus
                $("#message-form").trigger("reset");
                cancelAttachment();
                messageInput.focus();
            },
            error: () => {
                console.error('Error, check server response!');
                // remove loading bar
                NProgress.done();
                NProgress.remove();
            }
        });
    } else {
        // remove loading bar
        NProgress.done();
        NProgress.remove();
    }
}

/**
 *-------------------------------------------------------------
 * Send message function
 *-------------------------------------------------------------
 */
function sendMessage() {
    temporaryMsgId += 1;
    let tempID = 'temp_' + temporaryMsgId;
    let hasFile = $('.upload-attachment').val() ? true : false;
    if ($.trim(messageInput.val()).length > 0 || hasFile) {
        const formData = new FormData($("#message-form")[0]);
        formData.append('id', messenger.split('_')[1]);
        formData.append('type', messenger.split('_')[0]);
        formData.append('temporaryMsgId', tempID);
        formData.append('_token', access_token);
        console.log('ID:'+messenger.split('_')[1]);
        console.log('Type:'+messenger.split('_')[0]);
        $.ajax({
            url: $("#message-form").attr('action'),
            method: 'POST',
            data: formData,
            dataType: 'JSON',
            processData: false,
            contentType: false,
            beforeSend: () => {
                // remove message hint
                $(".message-hint").remove();
                // append message
                hasFile
                    ? messagesContainer.find('.messages').append(sendigCard(messageInput.val() + '\n' + loadingSVG('28px'), tempID))
                    : messagesContainer.find('.messages').append(sendigCard(messageInput.val(), tempID));
                // scroll to bottom
                scrollBottom(messagesContainer);
                messageInput.css({ 'height': '42px' });
                // form reset and focus
                $("#message-form").trigger("reset");
                cancelAttachment();
                messageInput.focus();
            },
            success: (data) => {
                console.log(data.tempID);
                if (data.error > 0) {
                    // message card error status
                    errorMessageCard(tempID);
                    console.error(data.error_msg);
                } else {
                    
                    messagesContainer.find('.mc-sender[data-id="sending"]').remove();
                    // get message before the sending one [temporary]
                    messagesContainer.find('.message-card[data-id='+data.tempID+']').before(data.message);
                    // delete the temporary one
                    messagesContainer.find('.message-card[data-id='+data.tempID+']').remove();
                    // scroll to bottom
                    scrollBottom(messagesContainer);
                    // send contact item updates
                    if(sendContactItemUpdates(true)){
                        //setTimeout(function(){
                            updateContatctItem(messenger.split('_')[1]);
                        //}, 4000);
                    }
                    //alert(data.tempID);
                }
            },
            error: () => {
                // message card error status
                errorMessageCard(tempID);
                // error log
                console.error('Failed sending the message! Please, check your server response');
            }
        });
    }
    return false;
}

/**
 *-------------------------------------------------------------
 * Fetch messages from database
 *-------------------------------------------------------------
 */
function fetchMessages(id, type) {
    if (messenger != 0) {
        $.ajax({
            url: url + '/fetchMessages',
            method: 'POST',
            data: { '_token': access_token, 'id': id, 'type': type },
            dataType: 'JSON',
            success: (data) => {
                // Enable message form if messenger not = 0; means if data is valid
                
                if (messenger != 0) {
                    disableOnLoad(false);
                }
                messagesContainer.find('.messages').html(data.messages);
                // scroll to bottom
                scrollBottom(messagesContainer);
                // remove loading bar
                NProgress.done();
                NProgress.remove();

                // trigger seen event
                makeSeen(true);
            },
            error: () => {
                // remove loading bar
                NProgress.done();
                NProgress.remove();
                console.error('Failed to fetch messages! check your server response.');
            }
        });
    }
}

/**
 *-------------------------------------------------------------
 * Cancel file attached in the message.
 *-------------------------------------------------------------
 */
function cancelAttachment() {
    $('.messenger-sendCard').find('.attachment-preview').remove();
    $('.upload-attachment').replaceWith($('.upload-attachment').val('').clone(true));
}

/**
 *-------------------------------------------------------------
 * Cancel updating avatar in settings
 *-------------------------------------------------------------
 */
function cancelUpdatingAvatar() {
    $('.upload-avatar-preview').css('background-image', defaultAvatarInSettings);
    $('.upload-avatar').replaceWith($('.upload-avatar').val('').clone(true));
}


/**
 *-------------------------------------------------------------
 * Pusher channels and event listening..
 *-------------------------------------------------------------
 */

// subscribe to the channel
var channel = pusher.subscribe('private-chatify');

// Listen to messages, and append if data received
channel.bind('messaging', function (data) {
    if(data.type == 'user'){
        // console.info(data.from_id+' - '+data.to_id+'\n'+auth_id+' - '+messenger);
        if (data.type == messenger.split('_')[0] && data.from_id == messenger.split('_')[1] && data.to_id == auth_id) {
            // remove message hint
            $(".message-hint").remove();
            // append message
            messagesContainer.find('.messages').append(data.message);
            // scroll to bottom
            scrollBottom(messagesContainer);
            // trigger seen event
            makeSeen(true);
            // remove unseen counter for the user from the contacts list
            $('.messenger-list-item[data-contact="'+messenger.split('_')[0]+'-'+messenger.split('_')[1]+'"]').find('tr>td>b').remove();
        }
    }else{
        // console.info(data.from_id+' - '+data.to_id+'\n'+auth_id+' - '+messenger);
        if (data.type == messenger.split('_')[0] && data.to_id == messenger.split('_')[1]) {
            if(data.from_id != auth_id){
                // remove message hint
                $(".message-hint").remove();
                // append message
                messagesContainer.find('.messages').append(data.message);
                // scroll to bottom
                scrollBottom(messagesContainer);
                // trigger seen event
                makeSeen(true);
                // remove unseen counter for the user from the contacts list
                $('.messenger-list-item[data-contact="'+messenger.split('_')[0]+'-'+messenger.split('_')[1]+'"]').find('tr>td>b').remove();
            }
        }
    }
});

// listen to typing indicator
channel.bind('client-typing', function (data) {
    if(data.type == 'user'){
        if (data.type == messenger.split('_')[0] && data.from_id == messenger.split('_')[1] && data.to_id == auth_id) {
        data.typing == true ? messagesContainer.find('.typing-indicator').show()
            : messagesContainer.find('.typing-indicator').hide();
            // scroll to bottom
            scrollBottom(messagesContainer);
        }
    }else{
        if (data.type == messenger.split('_')[0] && data.to_id == messenger.split('_')[1]) {
            if(data.from_id != auth_id){
                data.typing == true ? messagesContainer.find('.typing-indicator').show()
                : messagesContainer.find('.typing-indicator').hide();
                // scroll to bottom
                scrollBottom(messagesContainer);
            }
        }
    }
});

// listen to seen event
channel.bind('client-seen', function (data) {
    if(data.type == 'user'){
        if (data.type == messenger.split('_')[0] && data.from_id == messenger.split('_')[1] && data.to_id == auth_id) {
            if (data.seen == true) {
                $('.message-time').find('.fa-check').before('<span class="fas fa-check-double seen"></span> ');
                $('.message-time').find('.fa-check').remove();
                console.info('[seen] triggered!');
            } else {
                console.error('[seen] event not triggered!');
            }
        }
    }
});

// listen to contact item updates event
channel.bind('client-contactItem', function (data) {
    if(data.type == 'user'){
        if (data.update_for == auth_id) {
            data.updating == true ? updateContatctItem(data.update_to)
                : console.error('[Contact Item updates] Updating failed!');
        }
    }else{
        
    }
});

// -------------------------------------
// presence channel [User Active Status]
var activeStatusChannel = pusher.subscribe('presence-activeStatus');

// Joined
activeStatusChannel.bind('pusher:member_added', function (member) {
    setActiveStatus(1, member.id);
    $('.messenger-list-item[data-contact='+messenger.split('_')[0]+'-'+ member.id + ']').find('.activeStatus').remove();
    $('.messenger-list-item[data-contact='+messenger.split('_')[0]+'-'+ member.id + ']').find('.avatar').before(activeStatusCircle());
});

// Leaved
activeStatusChannel.bind('pusher:member_removed', function (member) {
    setActiveStatus(0, member.id);
    $('.messenger-list-item[data-contact='+messenger.split('_')[0]+'-'+ member.id + ']').find('.activeStatus').remove();
});

/**
 *-------------------------------------------------------------
 * Trigger typing event
 *-------------------------------------------------------------
 */
function isTyping(status) {
    return channel.trigger('client-typing', {
        type: messenger.split('_')[0],
        from_id: auth_id, // Me
        to_id: messenger.split('_')[1], // Messenger
        typing: status,
    });
}

/**
 *-------------------------------------------------------------
 * Trigger seen event
 *-------------------------------------------------------------
 */
function makeSeen(status) {
    // remove unseen counter for the user from the contacts list
    $('.messenger-list-item[data-contact=' + messenger.split('_')[1] + '-' + messenger.split('_')[1] + ']').find('tr>td>b').remove();
    // seen
    $.ajax({
        url: url + '/makeSeen',
        method: 'POST',
        data: { '_token': access_token, 'id': messenger.split('_')[1], 'type': messenger.split('_')[0]},
        dataType: 'JSON',
        success: (data) => {
            console.log('[seen] Messages seen - ' + messenger.split('_')[1]);
        }
    });
    return channel.trigger('client-seen', {
        type: messenger.split('_')[0],
        from_id: auth_id, // Me
        to_id: messenger.split('_')[1], // Messenger
        seen: status,
    });
}

/**
 *-------------------------------------------------------------
 * Trigger contact item updates
 *-------------------------------------------------------------
 */
function sendContactItemUpdates(status) {
    return channel.trigger('client-contactItem', {
        type: messenger.split('_')[0],
        update_for: messenger.split('_')[1], // Messenger
        update_to: auth_id, // Me
        updating: status,
    });
}

/**
 *-------------------------------------------------------------
 * Check internet connection using pusher states
 *-------------------------------------------------------------
 */
function checkInternet(state, selector) {
    let net_errs = 0;
    const messengerTitle = $('.messenger-headTitle');
    switch (state) {
        case 'connected':
            if (net_errs < 1) {
                messengerTitle.text(messengerTitleDefault);
                selector.addClass('successBG-rgba');
                selector.find('span').hide();
                selector.slideDown('fast', function () {
                    selector.find('.ic-connected').show();
                });
                setTimeout(function () {
                    $('.internet-connection').slideUp('fast');
                }, 3000);
            }
            break;
        case 'connecting':
            messengerTitle.text($('.ic-connecting').text());
            selector.removeClass('successBG-rgba');
            selector.find('span').hide();
            selector.slideDown('fast', function () {
                selector.find('.ic-connecting').show();
            });
            net_errs = 1;
            break;
        // Not connected
        default:
            messengerTitle.text($('.ic-noInternet').text());
            selector.removeClass('successBG-rgba');
            selector.find('span').hide();
            selector.slideDown('fast', function () {
                selector.find('.ic-noInternet').show();
            });
            net_errs = 1;
            break;
    }
}

/**
 *-------------------------------------------------------------
 * Get contacts
 *-------------------------------------------------------------
 */
function getContacts() {
    $('.listOfContacts').html(listItemLoading(4));
    $.ajax({
        url: url + '/getContacts',
        method: 'POST',
        data: { '_token': access_token, 'messenger_id': messenger.split('_')[1], 'type': searchingMode  },
        dataType: 'JSON',
        success: (data) => {
            console.log(data);
            $('.listOfContacts').html('');
            $('.listOfContacts').html(data.contacts);
            // update data-action required with [responsive design]
            cssMediaQueries();
        },
        error: () => {
            console.error('Server error, check your response');
        }
    });
}

/**
 *-------------------------------------------------------------
 * Update contact item
 *-------------------------------------------------------------
 */
function updateContatctItem(user_id) {
    if (user_id != auth_id) {
        let listItem = $('body').find('.listOfContacts').find('.messenger-list-item[data-contact='+messenger.split('_')[0]+'-'+ user_id + ']');
        $.ajax({
            url: url + '/updateContacts',
            method: 'POST',
            data: { '_token': access_token, 'user_id': user_id, 'messenger_id': messenger.split('_')[1], 'type': messenger.split('_')[0] },
            dataType: 'JSON',
            beforeSend: () => {
                listItem.remove();
            },
            success: (data) => {
                // update data-action required with [responsive design]
                cssMediaQueries();
                if(listItem.length >= 1){
                    $('.listOfContacts').prepend(data.contactItem);
                }
                console.log('length:'+ listItem.length);
            },
            error: () => {
                console.error('Server error, check your response');
            }
        });
    }
}
/**
 *-------------------------------------------------------------
 * Star
 *-------------------------------------------------------------
 */
function star(user_id) {
    console.log(messenger);
    if (messenger.split('_')[1] != auth_id) {
        $.ajax({
            url: url + '/star',
            method: 'POST',
            data: { '_token': access_token, 'user_id': user_id },
            dataType: 'JSON',
            success: (data) => {
                data.status > 0
                    ? $('.add-to-favorite').addClass('favorite')
                    : $('.add-to-favorite').removeClass('favorite');

            },
            error: () => {
                console.error('Server error, check your response');
            }
        });
    }
}

/**
 *-------------------------------------------------------------
 * Get favorite list
 *-------------------------------------------------------------
 */
function getFavoritesList() {
    $('.messenger-favorites').html(avatarLoading(4));
    $.ajax({
        url: url + '/favorites',
        method: 'POST',
        data: { '_token': access_token },
        dataType: 'JSON',
        success: (data) => {
            $('.messenger-favorites').html('');
            $('.messenger-favorites').html(data.favorites);
            // update data-action required with [responsive design]
            cssMediaQueries();
        },
        error: () => {
            console.error('Server error, check your response');
        }
    });
}

/**
 *-------------------------------------------------------------
 * Get shared photos
 *-------------------------------------------------------------
 */
function getSharedPhotos(user_id, type) {
    $.ajax({
        url: url + '/shared',
        method: 'POST',
        data: { '_token': access_token, 'user_id': user_id, 'type': type },
        dataType: 'JSON',
        success: (data) => {
            $('.shared-photos-list').html(data.shared);
        },
        error: () => {
            console.error('Server error, check your response');
        }
    });
}

/**
 *-------------------------------------------------------------
 * Search in messenger
 *-------------------------------------------------------------
 */
function messengerSearch(searchingMode, input) {
    $.ajax({
        url: url + '/search',
        method: 'POST',
        data: { '_token': access_token, 'input': input, 'searchingMode': searchingMode },
        dataType: 'JSON',
        beforeSend: () => {
            $('.search-records').html(listItemLoading(4));
        },
        success: (data) => {
            $('.search-records').find('svg').remove();
            data.addData == 'append'
                ? $('.search-records').append(data.records)
                : $('.search-records').html(data.records);
            // update data-action required with [responsive design]
            cssMediaQueries();
        },
        error: () => {
            console.error('Server error, check your response');
        }
    });
}

/**
 *-------------------------------------------------------------
 * Delete Conversation
 *-------------------------------------------------------------
 */
function deleteConversation(id) {
    $.ajax({
        url: url + '/deleteConversation',
        method: 'POST',
        data: { '_token': access_token, 'id': id, 'type': messenger.split('_')[0] },
        dataType: 'JSON',
        beforeSend: () => {
            // hide delete modal
            app_modal({
                show: false,
                name: 'delete',
            });
            // Show waiting alert modal
            app_modal({
                show: true,
                name: 'alert',
                buttons: false,
                body: loadingSVG('32px'),
            });
        },
        success: (data) => {
            // delete contact from the list
            $('.listOfContacts').find('.messenger-list-item[data-contact='+messenger.split('_')[0]+'-'+ id + ']').remove();
            // refresh info
            IDinfo(id, messenger.split('_')[0]);

            data.deleted ? '' : console.error('Error occured!');

            // Hide waiting alert modal
            app_modal({
                show: false,
                name: 'alert',
                buttons: true,
                body: '',
            });
        },
        error: () => {
            console.error('Server error, check your response');
        }
    });
}


function updateSettings() {
    const formData = new FormData($("#updateAvatar")[0]);
    if (messengerColor) {
        formData.append('messengerColor', messengerColor);
    }
    if (dark_mode) {
        formData.append('dark_mode', dark_mode);
    }
    $.ajax({
        url: url + '/updateSettings',
        method: 'POST',
        data: formData,
        dataType: 'JSON',
        processData: false,
        contentType: false,
        beforeSend: () => {
            // close settings modal
            app_modal({
                show: false,
                name: 'settings',
            });
            // Show waiting alert modal
            app_modal({
                show: true,
                name: 'alert',
                buttons: false,
                body: loadingSVG('32px'),
            });
        },
        success: (data) => {
            if (data.error) {
                // Show error message in alert modal
                app_modal({
                    show: true,
                    name: 'alert',
                    buttons: true,
                    body: data.msg,
                });
            } else {
                // Hide alert modal
                app_modal({
                    show: false,
                    name: 'alert',
                    buttons: true,
                    body: '',
                });

                // reload the page
                location.reload(true);
            }
        },
        error: () => {
            console.error('Server error, check your response');
        }
    });
}

/**
 *-------------------------------------------------------------
 * Set Active status
 *-------------------------------------------------------------
 */
function setActiveStatus(status, user_id) {
    $.ajax({
        url: url + '/setActiveStatus',
        method: 'POST',
        data: { '_token': access_token, 'user_id': user_id, 'status': status },
        dataType: 'JSON',
        success: (data) => {
            // Nothing to do
        },
        error: () => {
            console.error('Server error, check your response');
        }
    });
}



/**
 *-------------------------------------------------------------
 * On DOM ready
 *-------------------------------------------------------------
 */
$(document).ready(function () {
    searchingMode = "users";

    console.log(searchingMode);
    // Header avatar
    $('.header-avatar').css('background-image', 'url("/images/user_img/goetu-profile.png")');
    // avatar photo
    $('.messenger-infoView').find('.avatar').css('background-image', 'url("/images/user_img/goetu-profile.png")');

    // get contacts list
    getContacts();

    // get contacts list
    getFavoritesList();

    // Clear typing timeout
    clearTimeout(typingTimeout);

    // NProgress configurations
    NProgress.configure({ showSpinner: false, minimum: 0.7, speed: 500 });

    // make message input autosize.
    autosize($('.m-send'));

    // check if pusher has access to the channel [Internet status]
    pusher.connection.bind('state_change', function (states) {
        let selector = $('.internet-connection');
        checkInternet(states.current, selector);
        // listening for pusher:subscription_succeeded
        channel.bind('pusher:subscription_succeeded', function () {
            // On connection state change [Updating] and get [info & msgs]
            IDinfo(messenger.split('_')[1], messenger.split('_')[0]);
        });
    });

    $('.messenger-listView-tabs a[data-view="users"]').addClass('active-tab')
    $('.messenger-tab[data-view="users"]').show();

    $(document).on('click', '.btn-hide-shared-photos', function(){
        $('.btn-hide-shared-photos').addClass('btn-show-shared-photos');
        $('.btn-hide-shared-photos').removeClass('btn-hide-shared-photos');
        $('#shared-photo-icon').removeClass("fa-chevron-circle-down");
        $('#shared-photo-icon').addClass("fa-chevron-circle-right");
        $('.shared-photos-list').slideUp("fast");
    });

    $(document).on('click', '.btn-show-shared-photos', function(){
        $('.btn-show-shared-photos').addClass('btn-hide-shared-photos');
        $('.btn-show-shared-photos').removeClass('btn-show-shared-photos');
        $('#shared-photo-icon').addClass("fa-chevron-circle-down");
        $('#shared-photo-icon').removeClass("fa-chevron-circle-right");
        $('.shared-photos-list').slideDown("fast");
    });

    // tabs on click, show/hide...
    $('.messenger-listView-tabs a').on('click', function () {
        var dataView = $(this).attr('data-view');
        $('.messenger-listView-tabs a').removeClass('active-tab');
        $(this).addClass('active-tab');
        $('.messenger-tab').hide();
        $('.messenger-tab[data-view=' + dataView + ']').show();
        if(dataView == "users"){
            searchingMode = "users";
            getContacts()
            console.log(searchingMode);
        }else{
            searchingMode = "groups";
            getContacts()
            console.log(searchingMode);
        }
    });

    // set item active on click
    $('body').on('click', '.messenger-list-item', function () {
        $('.messenger-list-item').removeClass('m-list-active');
        $(this).addClass('m-list-active');
    });

    // show info side button
    $('.messenger-infoView nav a , .show-infoSide').on('click', function () {
        $('.messenger-infoView').toggle();
    });

    // x button for info section to show the main button.
    $('.messenger-infoView nav a').on('click', function () {
        $('.show-infoSide').show();
    });

    // hide showing button for info section.
    $('.show-infoSide').on('click', function () {
        $(this).hide();
    });

    // make favorites card dragable on click to slide.
    hScroller('.messenger-favorites');

    // click action for list item [user/group]
    $('body').on('click', '.messenger-list-item', function () {
        if ($(this).find('tr[data-action]').attr('data-action') == "1") {
            $('.messenger-listView').hide();
        }
        messenger = $(this).find('p[data-id]').attr('data-id');
        IDinfo(messenger.split('_')[1], messenger.split('_')[0]);
        console.log(messenger.split('_')[1]+' + '+messenger.split('_')[0]);
    });

    // click action for favorite button
    $('body').on('click', '.favorite-list-item', function () {
        if ($(this).find('div').attr('data-action') == "1") {
            $('.messenger-listView').hide();
        }
        messenger = 'user_' + $(this).find('div.avatar').attr('data-id');
        IDinfo(messenger.split('_')[1], messenger.split('_')[0]);
    });

    // list view buttons
    $('.listView-x').on('click', function () {
        $('.messenger-listView').hide();
    });
    $('.show-listView').on('click', function () {
        $('.messenger-listView').show();
    });

    // click action for [add to favorite] button.
    $('.add-to-favorite').on('click', function () {
        star(messenger.split('_')[1]);
    });

    // calling Css Media Queries
    cssMediaQueries();

    // message form on submit.
    $('#message-form').on('submit', (e) => {
        e.preventDefault();
        sendMessage();
    });

    // message input on keyup [Enter to send, Enter+Shift for new line]
    $('#message-form .m-send').on('keyup', (e) => {
        // if enter key pressed.
        if (e.which == 13 || e.keyCode == 13) {
            // if shift + enter key pressed, do nothing (new line).
            // if only enter key pressed, send message.
            if (!e.shiftKey) {
                triggered = isTyping(false);
                sendMessage();
            }
        }
    });

    // On [upload attachment] input change, show a preview of the image/file.
    $('body').on('change', ".upload-attachment", (e) => {
        let file = e.target.files[0];
        let reader = new FileReader();
        let sendCard = $('.messenger-sendCard');
        reader.readAsDataURL(file);
        reader.addEventListener('loadstart', (e) => {
            $('#message-form').before(loadingSVG());
        });
        reader.addEventListener('load', (e) => {
            $('.messenger-sendCard').find('.loadingSVG').remove();
            if (!file.type.match("image.*")) {
                // if the file not image
                sendCard.find('.attachment-preview').remove(); // older one
                sendCard.prepend(attachmentTemplate('file', file.name));
            } else {
                // if the file is an image
                sendCard.find('.attachment-preview').remove(); // older one
                sendCard.prepend(attachmentTemplate('image', file.name, e.target.result));
            }
        });
    });

    // Attachment preview cancel button.
    $('body').on('click', ".attachment-preview .cancel", (e) => {
        cancelAttachment();
    });

    // typing indicator on [input] keyDown
    $('#message-form .m-send').on('keydown', () => {
        if (typingNow < 1) {
            // Trigger typing
            let triggered = isTyping(true);
            triggered ? console.info('[+] Triggered')
                : console.error('[+] Not triggered');
            // Typing now
            typingNow = 1;
        }
        // Clear typing timeout
        clearTimeout(typingTimeout);
        // Typing timeout
        typingTimeout = setTimeout(function () {
            triggered = isTyping(false);
            triggered ? console.info('[-] Triggered')
                : console.error('[-] Not triggered');
            // Clear typing now
            typingNow = 0;
        }, 1000);
    });

    // Image modal
    $('body').on('click', ".chat-image", function () {
        let src = $(this).css("background-image").split(/"/)[1];
        $("#imageModalBox").show();
        $("#imageModalBoxSrc").attr('src', src);
    });
    $('.imageModal-close').on('click', function () {
        $("#imageModalBox").hide();
    });

    // Search input on focus
    $('.messenger-search').on('focus', function () {
        $('.messenger-tab').hide();
        $('.messenger-tab[data-view="search"]').show();
    });
    // Search action on keyup
    $('.messenger-search').on('keyup', function (e) {
        $.trim($(this).val()).length > 0
            ? $('.messenger-search').trigger('focus') + messengerSearch(searchingMode, $(this).val())
            : $('.messenger-tab').hide() +
            $('.messenger-listView-tabs a[data-view="users"]').trigger('click');
    });

    // Delete Conversation button
    $('.delete-conversation').on('click', function () {
        app_modal({
            name: 'delete',
        });
    });
    // delete modal [delete button]
    $('.app-modal[data-name=delete]').find('.app-modal-footer .delete').on('click', function () {
        deleteConversation(messenger.split('_')[1]);
        app_modal({
            show: false,
            name: 'delete',
        });
    });
    // delete modal [cancel button]
    $('.app-modal[data-name=delete]').find('.app-modal-footer .cancel').on('click', function () {
        app_modal({
            show: false,
            name: 'delete',
        });
    });

    // Settings button action to show settings modal
    $('.settings-btn').on('click', function () {
        app_modal({
            name: 'settings',
        });
    });

    // on submit settings' form
    $('#updateAvatar').on('submit', (e) => {
        e.preventDefault();
        updateSettings();
    });
    // Settings modal [cancel button]
    $('.app-modal[data-name=settings]').find('.app-modal-footer .cancel').on('click', function () {
        app_modal({
            show: false,
            name: 'settings',
        });
        cancelUpdatingAvatar();
    });

    // upload avatar on change
    $('body').on('change', ".upload-avatar", (e) => {
        // store the original avatar
        if (defaultAvatarInSettings == null) {
            defaultAvatarInSettings = $('.upload-avatar-preview').css('background-image');
        }
        let file = e.target.files[0];
        let reader = new FileReader();
        reader.readAsDataURL(file);
        reader.addEventListener('loadstart', (e) => {
            $('.upload-avatar-preview').append(loadingSVG('42px', 'upload-avatar-loading'));
        });
        reader.addEventListener('load', (e) => {
            $('.upload-avatar-preview').find('.loadingSVG').remove();
            if (!file.type.match("image.*")) {
                // if the file is not an image
                console.error('File you selected is not an image!');
            } else {
                // if the file is an image
                $('.upload-avatar-preview').css('background-image', 'url("' + e.target.result + '")');
            }
        });
    });

    // change messenger color button
    $('body').on('click', '.update-messengerColor a', function () {
        messengerColor = $(this).attr('class').split(' ')[0];
        $('.update-messengerColor a').removeClass('m-color-active');
        $(this).addClass('m-color-active');
    });

    // Switch to Dark/Light mode
    $('body').on('click', '.dark-mode-switch', function () {
        if ($(this).attr('data-mode') == '0') {
            $(this).attr('data-mode', '1');
            $(this).removeClass('far');
            $(this).addClass('fas');
            dark_mode = 'dark';
        } else {
            $(this).attr('data-mode', '0');
            $(this).removeClass('fas');
            $(this).addClass('far');
            dark_mode = 'light';
        }
    });

    /**
 *-------------------------------------------------------------
 * Create a group
 *-------------------------------------------------------------
 */

    // Show Create GC Modal
    $('.create-group').on('click', function(){
        $(this).addClass('m-list-active');
        app_modal({
            show: true,
            name: 'new-group-chat',
        });
    })

    // Show Create GC Modal
    $('.add-members').on('click', function(){
        console.log(messenger);
        $.ajax({
            url: url+'/listOfMembers',
            method: 'POST',
            data:{
                '_token':access_token, 'group_chat': messenger.split('_')[1]
            },
            beforeSend: () => {
                $('#listOfMember').find('svg').remove();
                $('#listOfMember').html(listItemLoading(4));
            },
            success: (data) => {
                data.addData == 'append' 
                ? $('#listOfMember').append(data.records) :
                $('#listOfMember').html(data.records);
            }
        });
        
        app_modal({
            show: true,
            name: 'group-chat-members',
        });
    })

    // Show Create GC Modal
    $('.cancel-manage-members').on('click', function(){
        app_modal({
            show: false,
            name: 'group-chat-members',
        });
    });

    $("#txtSelectMembers").select2({
        minimumResultsForSearch: 10,
        width:'100%',
        ajax: {
        url: url + '/selectUsers',
        dataType: "json",
        type: 'POST',
        searching:function(){
            return "Please wait while searching the requestor ..."
        },
        data: function (params) {
        var queryParameters = {
            _token: access_token,
            search: params.term,
            group_chat: messenger.split('_')[1]
        }
        return queryParameters;
        },
        processResults:function(dataItems) {
            return {
                results: $.map(dataItems.data, function (item) {
            return {
                text: item.name,
                id: item.id
            }
        })
        };
        }
    } // need to override the changed default
    });

    $('.btn-save-members').on('click', function(){
    
        $.ajax({
            url: url + '/updateGroupChatMembers',
            method: 'POST',
            data:{
                'added_members': $('#txtSelectMembers').val(), '_token': access_token, 'group':messenger.split('_')[1] 
            },
            beforeSend: () => {
                app_modal({
                    show: false,
                    name: 'group-chat-members',
                });    
                // Show waiting alert modal
                app_modal({
                    show: true,
                    name: 'alert',
                    buttons: false,
                    body: loadingSVG('32px'),
                });
            },
            success: (response) => {
                if(response.system_message == 1){
                    $("#txtSelectMembers").val([]).change();
                    // Show waiting alert modal
                    app_modal({
                        show: false,
                        name: 'alert',
                        buttons: false,
                        body: loadingSVG('32px'),
                    });
                    app_modal({
                        show: true,
                        name: 'added-member',
                    });
                    if (messenger.split('_')[1] != auth_id) {
                        let listItem = $('body').find('.listOfContacts').find('.messenger-list-item[data-contact='+messenger.split('_')[0]+'-'+ messenger.split('_')[1] + ']');
                        $.ajax({
                            url: url + '/updateContacts',
                            method: 'POST',
                            data: { '_token': access_token, 'user_id': messenger.split('_')[1], 'messenger_id': messenger.split('_')[1], 'type': 'group'},
                            dataType: 'JSON',
                            success: (data) => {
                                listItem.remove();
                                $('.listOfContacts').prepend(data.contactItem);
                                // update data-action required with [responsive design]
                                cssMediaQueries();
                                if (messenger != 0) {
                                    $.ajax({
                                        url: url + '/fetchMessages',
                                        method: 'POST',
                                        data: { '_token': access_token, 'id': messenger.split('_')[1], 'type': 'group' },
                                        dataType: 'JSON',
                                        success: (data) => {
                                            // Enable message form if messenger not = 0; means if data is valid
                                            
                                            if (messenger != 0) {
                                                disableOnLoad(false);
                                            }
                                            messagesContainer.find('.messages').html(data.messages);
                                            // scroll to bottom
                                            scrollBottom(messagesContainer);
                                            // remove loading bar
                                            NProgress.done();
                                            NProgress.remove();
                                            // trigger seen event
                                            makeSeen(true);
                                        },
                                        error: () => {
                                            // remove loading bar
                                            NProgress.done();
                                            NProgress.remove();
                                            console.error('Failed to fetch messages! check your server response.');
                                        }
                                    });
                                }
                            },
                            error: () => {
                                console.error('Server error, check your response');
                            }
                        });
                    }
                }
            }
        });
    })
    

    $('.app-scroll').scroll(function(){
        var pos = $('.app-scroll').scrollTop();
        if (pos == 0) {
            if(loading == 0){
              
                $.ajax({
                                        url: url + '/fetchMessages',
                                        method: 'POST',
                                        data: { '_token': access_token, 'id': messenger.split('_')[1], 'type': 'group', 'last_date': last_date },
                                        dataType: 'JSON',
                                        beforeSend: () => {
                                            loading = 1;
                                        },
                                        success: (data) => {
                                            loading = 0
                                            last_date = data.last_date.date;
                                            // Enable message form if messenger not = 0; means if data is valid
                                            if (messenger != 0) {
                                                disableOnLoad(false);
                                            }
                                            messagesContainer.find('.messages').prepend(data.messages);
                                        },
                                        error: () => {
                                            // remove loading bar
                                            NProgress.done();
                                            NProgress.remove();
                                            console.error('Failed to fetch messages! check your server response.');
                                        }
                                    });
            }                 
        }

    });
  
    $('.member-btn-okay').on('click', function(){
                    app_modal({
                        show: false,
                        name: 'added-member',
                    });
    })

    // Create GC modal [cancel button]
    $('.app-modal[data-name=new-group-chat]').find('.app-modal-footer .cancel').on('click', function () {
        $('.create-group').removeClass('m-list-active');
        app_modal({
            show: false,
            name: 'new-group-chat',
        });
    });

    // upload avatar on change
    $('body').on('change', ".upload-group-avatar", (e) => {
        // store the original avatar
        if (defaultAvatarInSettings == null) {
            defaultAvatarInSettings = $('.upload-group-avatar-preview').css('background-image');
        }
        let file = e.target.files[0];
        let reader = new FileReader();
        reader.readAsDataURL(file);
        reader.addEventListener('loadstart', (e) => {
            $('.upload-group-avatar-preview').append(loadingSVG('42px', 'upload-avatar-loading'));
        });
        reader.addEventListener('load', (e) => {
            $('.upload-group-avatar-preview').find('.loadingSVG').remove();
            if (!file.type.match("image.*")) {
                // if the file is not an image
                console.error('File you selected is not an image!');
            } else {
                // if the file is an image
                $('.upload-group-avatar-preview').css('background-image', 'url("' + e.target.result + '")');
            }
        });
    });

    $(document).on('click', '.btn-remove-member', function(){
                    $.ajax({
                    url: url+'/removeMember',
                    method: 'POST',
                    data:{
                        '_token':access_token, 'ID':this.id
                    },
                    beforeSend:function(){
                        app_modal({
                                    show: true,
                                    name: 'alert',
                                    buttons: false,
                                    body: loadingSVG('32px'),
                        });
                        app_modal({
                            show: false,
                            name: 'group-chat-members',
                        });
                    },
                    success:function(response){
                        if(response.system_message == 1){
                                app_modal({
                                    show: false,
                                    name: 'alert',
                                    buttons: false,
                                    body: loadingSVG('32px'),
                                });
                                app_modal({
                                    show: true,
                                    name: 'removed-member',
                                });
                        messenger = response.messenger;
                        if (messenger.split('_')[1] != auth_id) {
                        let listItem = $('body').find('.listOfContacts').find('.messenger-list-item[data-contact='+messenger.split('_')[0]+'-'+ messenger.split('_')[1] + ']');
                        $.ajax({
                            url: url + '/updateContacts',
                            method: 'POST',
                            data: { '_token': access_token, 'user_id': messenger.split('_')[1], 'messenger_id': messenger.split('_')[1], 'type': 'group'},
                            dataType: 'JSON',
                            success: (data) => {
                                listItem.remove();
                                $('.listOfContacts').prepend(data.contactItem);
                                // update data-action required with [responsive design]
                                cssMediaQueries();
                                if (messenger != 0) {
                                    $.ajax({
                                        url: url + '/fetchMessages',
                                        method: 'POST',
                                        data: { '_token': access_token, 'id': messenger.split('_')[1], 'type': 'group' },
                                        dataType: 'JSON',
                                        success: (data) => {
                                            // Enable message form if messenger not = 0; means if data is valid
                                            
                                            if (messenger != 0) {
                                                disableOnLoad(false);
                                            }
                                            messagesContainer.find('.messages').html(data.messages);
                                            // scroll to bottom
                                            scrollBottom(messagesContainer);
                                            // remove loading bar
                                            NProgress.done();
                                            NProgress.remove();
                                            // trigger seen event
                                            makeSeen(true);
                                        },
                                        error: () => {
                                            // remove loading bar
                                            NProgress.done();
                                            NProgress.remove();
                                            console.error('Failed to fetch messages! check your server response.');
                                        }
                                    });
                                }
                            },
                            error: () => {
                                console.error('Server error, check your response');
                            }
                        });
                    }
                        }
                    }
                });
                
    });

    $('.remove-member-btn-okay').click(function(){
        app_modal({
            show: false,
            name: 'removed-member',
        });
    });

    
    //Button for creating the group chat
    $('.create-group-chat').on('click', function(){
        
        var countError = 0;
        if($('#txtGroupChatName').val() == ""){
            countError += 1;
            document.getElementById('txtGroupChatName').style.border = "3px solid red";
        }else{
            document.getElementById('txtGroupChatName').style.border = "";
        }

        if(countError == 0){
            var Avatar = $("#txtGroupChatAvatar").prop("files")[0];
            var formData = new FormData();
            formData.append("GroupChatAvatar", Avatar);
            formData.append("_token", access_token);
            formData.append("GroupChatName", $('#txtGroupChatName').val());
            $.ajax({
                url: url+'/createGroupChat',
                method: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: () => {
                    app_modal({
                        show: false,
                        name: 'new-group-chat',
                    }); 
                    // Show waiting alert modal
                    app_modal({
                            show: true,
                            name: 'alert',
                            buttons: false,
                            body: loadingSVG('32px'),
                    });
                },
                success: (response) =>{
                    if(response.system_message == 1){
                        app_modal({
                            show: false,
                            name: 'alert',
                            buttons: false,
                            body: loadingSVG('32px'),
                        });
                        app_modal({
                            show: true,
                            name: 'group-chat-created',
                        }); 
                    }
                    if (response.id != auth_id) {
                        let listItem = $('body').find('.listOfContacts').find('.messenger-list-item[data-contact="group-'+response.id+'"]');
                        $.ajax({
                            url: url + '/updateContacts',
                            method: 'POST',
                            data: { '_token': access_token, 'user_id': response.id, 'messenger_id': response.id, 'type': 'group'},
                            dataType: 'JSON',
                            success: (data) => {
                                listItem.remove();
                                $('.listOfContacts').prepend(data.contactItem);
                                // update data-action required with [responsive design]
                                cssMediaQueries();
                            },
                            error: () => {
                                console.error('Server error, check your response');
                            }
                        });
                    }   

                }
            });
        }
    });

    $('.btn-okay').on('click', function(){
        $('.create-group').removeClass('m-list-active');
        app_modal({
            show: false,
            name: 'group-chat-created',
        });
    });

    
    // upload avatar on change
    $('body').on('change', ".update-upload-group-avatar", (e) => {
        // store the original avatar
        if (defaultAvatarInSettings == null) {
            defaultAvatarInSettings = $('.update-upload-group-avatar-preview').css('background-image');
        }
        let file = e.target.files[0];
        let reader = new FileReader();
        reader.readAsDataURL(file);
        reader.addEventListener('loadstart', (e) => {
            $('.update-upload-group-avatar-preview').append(loadingSVG('42px', 'update-upload-group-avatar-loading'));
        });
        reader.addEventListener('load', (e) => {
            $('.update-upload-group-avatar-preview').find('.loadingSVG').remove();
            if (!file.type.match("image.*")) {
                // if the file is not an image
                console.error('File you selected is not an image!');
            } else {
                // if the file is an image
                $('.update-upload-group-avatar-preview').css('background-image', 'url("' + e.target.result + '")');
            }
        });
    });


    $('.group-settings').click(function(){
        $.ajax({
            url: url +'/getTheGroupInfo',
            method: 'POST',
            data:{
                '_token':access_token, 'ID': messenger.split('_')[1]
            },
            beforeSend: () => {
                app_modal({
                    show: true,
                    name: 'alert',
                    buttons: false,
                    body: loadingSVG('32px'),
                });
            },
            success: (data) => {
                $('#txtUpdateGroupChatName').val(data.group_chat_name);
                $('.update-upload-group-avatar-preview').css('background-image', 'url("' + data.avatar + '")');
                app_modal({
                    show: false,
                    name: 'alert',
                    buttons: false,
                    body: loadingSVG('32px'),
                });
                app_modal({
                    show: true,
                    name: 'update-group-chat',
                });
            }
        });
    });

    $('.cancel-update').click(function(){
        app_modal({
            show: false,
            name: 'update-group-chat',
        });
    });

    $('.update-group-chat').click(function(){
        
        var countError = 0;
        if($('#txtUpdateGroupChatName').val() == ""){
            countError += 1;
            document.getElementById('txtUpdateGroupChatName').style.border = "3px solid red";
        }else{
            document.getElementById('txtUpdateGroupChatName').style.border = "";
        }

        if(countError == 0){
                var Avatar = $("#txtUpdateGroupChatAvatar").prop("files")[0];
                var formData = new FormData();
                formData.append("ID", messenger.split('_')[1]);
                formData.append("GroupChatAvatar", Avatar);
                formData.append("_token", access_token);
                formData.append("GroupChatName", $('#txtUpdateGroupChatName').val());
                $.ajax({
                    url: url+'/updateGroupChat',
                    method: 'POST',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: () => {
                        app_modal({
                            show: false,
                            name: 'update-group-chat',
                        }); 
                        // Show waiting alert modal
                        app_modal({
                                show: true,
                                name: 'alert',
                                buttons: false,
                                body: loadingSVG('32px'),
                        });
                    },
                    success: (response) =>{
                        if(response.system_message == 1){
                            app_modal({
                                show: false,
                                name: 'alert',
                                buttons: false,
                                body: loadingSVG('32px'),
                            });
                            app_modal({
                                show: true,
                                name: 'update-chat-success',
                            }); 
                        }
                        if (response.id != auth_id) {
                            let listItem = $('body').find('.listOfContacts').find('.messenger-list-item[data-contact="group-'+response.id+'"]');
                            $.ajax({
                                url: url + '/updateContacts',
                                method: 'POST',
                                data: { '_token': access_token, 'user_id': response.id, 'messenger_id': response.id, 'type': 'group'},
                                dataType: 'JSON',
                                success: (data) => {
                                    listItem.remove();
                                    $('.listOfContacts').prepend(data.contactItem);
                                    // update data-action required with [responsive design]
                                    cssMediaQueries();
                                },
                                error: () => {
                                    console.error('Server error, check your response');
                                }
                            });
                            if (messenger != 0) {
                                    $.ajax({
                                        url: url + '/fetchMessages',
                                        method: 'POST',
                                        data: { '_token': access_token, 'id': messenger.split('_')[1], 'type': 'group' },
                                        dataType: 'JSON',
                                        success: (data) => {
                                            // Enable message form if messenger not = 0; means if data is valid
                                            
                                            if (messenger != 0) {
                                                disableOnLoad(false);
                                            }
                                            messagesContainer.find('.messages').html(data.messages);
                                            // scroll to bottom
                                            scrollBottom(messagesContainer);
                                            // remove loading bar
                                            NProgress.done();
                                            NProgress.remove();
                                            // trigger seen event
                                            makeSeen(true);
                                        },
                                        error: () => {
                                            // remove loading bar
                                            NProgress.done();
                                            NProgress.remove();
                                            console.error('Failed to fetch messages! check your server response.');
                                        }
                                    });
                                }
                        }   
                    }
                });
            }
        });

        $('.btn-okay-update').click(function(){
            app_modal({
                show: false,
                name: 'update-chat-success',
            });               
        });

        $('#leave-conversation').click(function(){
            app_modal({
                show: true,
                name: 'leave',
            }); 
        });

        $('.cancel-leave').click(function(){
            app_modal({
                show: false,
                name: 'leave',
            }); 
        });

        $('.leave').click(function(){
        
            $.ajax({
                url: url+'/leaveGroup',
                method: 'POST',
                data:{
                    '_token': access_token, 'ID' :messenger.split('_')[1]
                },
                beforeSend: () => {
                    app_modal({
                        show: false,
                        name: 'leave',
                    });
                    app_modal({
                        show: true,
                        name: 'alert',
                        buttons: false,
                        body: loadingSVG('32px'),
                    });
                },
                success: (response) => {
                    if(response.system_message == 1){
                        app_modal({
                            show: false,
                            name: 'alert',
                            buttons: false,
                            body: loadingSVG('32px'),
                        });
                        location.reload();
                    }
                }

            });
        
        });

});

</script>