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