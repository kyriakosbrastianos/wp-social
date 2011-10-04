/**
 * This plugin was ported over as a jQuery plugin from Janis Elsts' Lazy Avatars Plugin. {@link http://w-shadow.com/blog/)
 *
 * @author Crowd Favorite
 * @version 1.0
 */
(function($){
    var methods = {
        init: function(){
            $(document).bind('scroll', $.avatarLoader('checkVisibility'));
            $.avatarLoader('checkVisibility');
        },
        checkVisibility: function() {
            var $pendingAvatars = $('.lazy-avatar pending');
            $pendingAvatars.each(function(i){
                var viewportTop = window.pageYOffset, avatarTop = $(this).offsetTop;
                var inView = ((avatarTop + $(this).offsetHeight >= viewportTop) && (avatarTop <= viewportTop + $(document).height()));

                if (inView) {
                    $.avatarLoader('load', $(this));
                }

                if (!i) {
                    $(document).unbind('scroll', $.avatarLoader('checkVisibility'));
                }
            });
        },
        load: function($avatar) {
            var $img = $('img');

            var data = $avatar.data();
            for (var key in data) {
                $img.attr(key, data[key]);
            }

            $avatar.append($img).removeClass('pending');
        }
    };

    $.fn.avatarLoader = function(method){
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        }
        else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        }
        else {
            $.error('Method '+method+' does no exist on jQuery.avatarLoader');
        }
    };
})(jQuery);

(function(win){
	// Storing commonly used values into local variables makes minification more effective.
	var doc = document, de = doc.documentElement,
		addEvent='addEventListener', eventName = 'scroll', eventPhase = false;

	if ( win[addEvent] ){
		win[addEvent]('load', doLazyAvatars, eventPhase);
	}

	function doLazyAvatars(){
		// Find the avatars waiting to be loaded.
		var pendingAvatars = doc.getElementsByClassName('lazy-avatar pending');

	    function loadLazyAvatar(avatar){
	    	// Create the avatar image inside the container.
			var img = doc.createElement('img');
			img.src = avatar.getAttribute('data-avatar-src');
			img.width = avatar.getAttribute('data-avatar-width');
			img.height = avatar.getAttribute('data-avatar-height');
	    	avatar.appendChild(img);
	    	// Remove the "pending" class. Since pendingAvatars is "live" DOM element collection,
	    	// this change will cause the avatar to be automatically removed from pendingAvatars.
	    	avatar.className = avatar.className.replace(/\bpending\b/, '');
	    }

	    // Load the avatar images when they come into view.
	    function checkVisibility(){
	    	var i = 0;
	    	while(i < pendingAvatars.length){
	    		// Check whether a DOM element is inside the current viewport.
	    		var viewportTop = win.pageYOffset, avatarTop = pendingAvatars[i].offsetTop;
	    		var inView = ((avatarTop + pendingAvatars[i].offsetHeight >= viewportTop) && (avatarTop <= viewportTop + de.clientHeight));

	    		if ( inView ){
	    			loadLazyAvatar(pendingAvatars[i]);
	    		} else {
	    			i++;
	    		}
	    	}
	    	// Remove our event listener once all avatars have been loaded. This is not strictly
	    	// necessary, but it's polite to clean up after yourself.
	    	if ( !i ){
	    		window.removeEventListener(eventName, checkVisibility, eventPhase);
	    	}
	    }
		win[addEvent](eventName, checkVisibility, eventPhase);

	    // In case the user goes directly to the comment form, i.e. by anchor :
	    checkVisibility();
	}
})(window);