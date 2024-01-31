jQuery(document).ready(function($) {
    var userDetailsCache = {};
    var loadingHtml = '<div id="loadingPopup" style="display:none">';
    loadingHtml += '<img src="' + ajax_object.gif_url + '" alt="Loading...">';
    loadingHtml += '</div>';
    $('body').append(loadingHtml);
    var isPopupOpen = false;
    var popupHtml = '<div id="userDetailsPopup">';
    popupHtml += '<div id="userDetailsContent"></div>';
    popupHtml += '<button id="closePopup">Kapat</button>';
    popupHtml += '</div>';
    $('body').append(popupHtml);

    // cache temizle butonu
    $('#clearCacheButton').click(function() {
        $.ajax({
            url: ajax_object.ajaxurl,
            type: 'POST',
            data: {
                action: 'clear_user_cache',
                nonce: ajax_object.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('Önbellek temizlendi, sayfa yenileniyor...');
                    location.reload();
                } else {
                    alert('Önbellek temizlenemedi');
                }
            }
        });
    });

    // kullanıcı detaylarına erişim
    $(document).on('click', '.user-row', function(e) {
        e.preventDefault();
        var userId = $(this).data('user-id');
		
		 $('#userDetailsPopup').hide();
        $('#loadingPopup').show();
        isPopupOpen = false;

        if (userDetailsCache[userId]) {
            displayUserDetails(userDetailsCache[userId]);
        } else {
            fetchUserDetails(userId);
        }
    });

    function fetchUserDetails(userId) {
        $('#loadingPopup').show();

        $.ajax({
            url: ajax_object.ajaxurl,
            type: 'POST',
            data: {
                action: 'get_user_details',
                user_id: userId,
				nonce: ajax_object.nonce
            },
            success: function(response) {
                if (response.success) {
                    userDetailsCache[userId] = response.data; // cacheye al
                    displayUserDetails(response.data);
                } else {
                    $('#loadingPopup').hide();
                    alert('Kullanıcı bilgileri alınamadı');
                }
            }
        });
    }

    function displayUserDetails(user) {
        $('#loadingPopup').hide();
        var html = '<p><strong>İsim:</strong> ' + user.name + '</p>';
        html += '<p><strong>Kullanıcı Adı:</strong> ' + user.username + '</p>';
        html += '<p><strong>Email:</strong> ' + user.email + '</p>';
        html += '<p><strong>Telefon:</strong> ' + user.phone + '</p>';
        html += '<p class="highlight-address"><strong>Adres:</strong></p>';
        html += '<p><strong>Sokak:</strong> ' + user.address.street + '</p>';
        html += '<p><strong>Apartman:</strong> ' + user.address.suite + '</p>';
        html += '<p><strong>Şehir:</strong> ' + user.address.city + '</p>';
        html += '<p><strong>Posta Kodu:</strong> ' + user.address.zipcode + '</p>';
        html += '<p class="highlight-company"><strong>Şirket:</strong></p>';
        html += '<p><strong>Şirket Adı:</strong> ' + user.company.name + '</p>';
        html += '<p><strong>Slogan:</strong> ' + user.company.catchPhrase + '</p>';

        $('#userDetailsContent').html(html);
        $('#userDetailsPopup').show();
        isPopupOpen = true;
    }

    // kapat butonu
    $('#closePopup').click(function() {
        $('#userDetailsPopup').hide();
        isPopupOpen = false;
    });
	
	   // popup dışına tıklanırsa kapat
    $(document).mouseup(function(e) {
        var container = $("#userDetailsPopup");
       if (!container.is(e.target) && container.has(e.target).length === 0 && !$(e.target).closest('.user-row').length && isPopupOpen) {
            container.fadeOut();
            isPopupOpen = false;
        }
});
    });