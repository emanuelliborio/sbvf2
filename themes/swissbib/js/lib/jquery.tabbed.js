/**
 * Toggles tab areas.
 *
 * @author NOSE
 * @version 1.0.0	initial version			
 */

var sbTabbedSettings = {
	selectorTab:		"ul li",
	selectorTabbed:		".tabbed",
	classTabSelected:	"selected",
	classTabbedSelected:"tabbed_selected",
	classTabbedHidden:	"tabbed_hidden",
	animate:			true,
	timeAnimate:		600,
	easingAnimate:		"easeOutQuad",
	cookie:				"tabbed_",
	persist:			true,
	debug:				true
};

jQuery.fn.tabbed = function(op) {
	jQuery.extend(sbTabbedSettings, op);
	
		// Elements
	var elContainer = jQuery(this);
	var elsTabs = jQuery(sbTabbedSettings.selectorTab, elContainer);
	var elsTabbed = jQuery(sbTabbedSettings.selectorTabbed);
	
		// Events
	jQuery(elsTabs).bind("click", actionTabbed);
	
		// Initialize
	initialize();



	/**
	 * Initializes the tabbed.
	 */
	function initialize() {
		dlog("initialize");
		
			// Restore from cookie
		if (sbTabbedSettings.persist) {
				// ID
			var tid = jQuery("." + sbTabbedSettings.classTabSelected, elContainer).attr("id");
			dlog("tid: " + tid);
			
				// Cookie
			var cname = encodeURI(sbTabbedSettings.cookie + jQuery(elContainer).attr("rel"));
			if (jQuery.cookie(cname)) {
				var tid = jQuery.cookie(cname);
				dlog("cookie: " + tid);
			}
			
				// Change
			if (tid) {
				changeTabbed(tid);
			}
		}
	}



	/**
	 * Changes the tabs.
	 *
	 * @param	{Event}	e
	 */
	function actionTabbed(e) {
		dlog("actionTabbed");
		
			// Prevent
		var evt = jQuery.Event(e);
		evt.preventDefault();
		evt.stopPropagation();	
		
		var tabID = jQuery(this).attr("id");
		
			// Persist active tab preference?
		if (sbTabbedSettings.persist) {
				// Write cookie
			var cookieName = encodeURI(sbTabbedSettings.cookie + jQuery(elContainer).attr("rel"));
			var cookieValue= tabID;
			jQuery.cookie(cookieName, cookieValue, {path: '/'});
		}
		
			// Change active tab
		changeTabbed(tabID, sbTabbedSettings.animate);
	}



	/**
	 * @param	{String}	tabId
	 * @param	{Boolean}	animate
	 */
	function changeTabbed(tabId, animate) {
		dlog("changeTabbed: " + tabId);
		
			// Selected
		jQuery(elsTabs).removeClass(sbTabbedSettings.classTabSelected);
		jQuery("#"+tabId, elContainer).addClass(sbTabbedSettings.classTabSelected);

		var elsTabbedSelected = jQuery("."+tabId);
		
			// Change
		jQuery(elsTabbed).addClass(sbTabbedSettings.classTabbedHidden);
		jQuery(elsTabbed).removeClass(sbTabbedSettings.classTabbedSelected);
		jQuery(elsTabbedSelected).removeClass(sbTabbedSettings.classTabbedHidden);
		jQuery(elsTabbedSelected).addClass(sbTabbedSettings.classTabbedSelected);
		
			// Animate
		if (animate) {
			jQuery(elsTabbedSelected).css({"opacity":0});
			jQuery(elsTabbedSelected).animate({"opacity":1}, sbTabbedSettings.timeAnimate, sbTabbedSettings.easingAnimate, function() {
				jQuery(elsTabbedSelected).css({"opacity":null});
			});
		}
	}
	
	
	
	/*
	 * Debug log.
	 *
	 * @param	{String) msg
	 */
	function dlog(msg) {
		if (sbTabbedSettings.debug) {
			console.log("Tabbed: " + msg);
		}
	}

    return this;
};