forbiddenKeys = [KeyEvent.DOM_VK_CANCEL,
				 KeyEvent.DOM_VK_HELP,
				 KeyEvent.DOM_VK_TAB,
				 KeyEvent.DOM_VK_CLEAR,
				 KeyEvent.DOM_VK_PAUSE,
				 KeyEvent.DOM_VK_CAPS_LOCK,
				 KeyEvent.DOM_VK_PAGE_UP,
				 KeyEvent.DOM_VK_PAGE_DOWN,
				 KeyEvent.DOM_VK_END,
				 KeyEvent.DOM_VK_HOME,
				 KeyEvent.DOM_VK_LEFT,
				 KeyEvent.DOM_VK_UP,
				 KeyEvent.DOM_VK_RIGHT,
				 KeyEvent.DOM_VK_DOWN,
				 KeyEvent.DOM_VK_PRINTSCREEN,
				 KeyEvent.DOM_VK_INSERT,
				 KeyEvent.DOM_VK_DELETE,
				 KeyEvent.DOM_VK_CONTEXT_MENU,
				 KeyEvent.DOM_VK_F1,
				 KeyEvent.DOM_VK_F2,
				 KeyEvent.DOM_VK_F3,
				 KeyEvent.DOM_VK_F4,
				 KeyEvent.DOM_VK_F5,
				 KeyEvent.DOM_VK_F6,
				 KeyEvent.DOM_VK_F7,
				 KeyEvent.DOM_VK_F8,
				 KeyEvent.DOM_VK_F9,
				 KeyEvent.DOM_VK_F10,
				 KeyEvent.DOM_VK_F11,
				 KeyEvent.DOM_VK_F12,
				 KeyEvent.DOM_VK_F13,
				 KeyEvent.DOM_VK_F14,
				 KeyEvent.DOM_VK_F15,
				 KeyEvent.DOM_VK_F16,
				 KeyEvent.DOM_VK_F17,
				 KeyEvent.DOM_VK_F18,
				 KeyEvent.DOM_VK_F19,
				 KeyEvent.DOM_VK_F20,
				 KeyEvent.DOM_VK_F21,
				 KeyEvent.DOM_VK_F22,
				 KeyEvent.DOM_VK_F23,
				 KeyEvent.DOM_VK_F24,
				 KeyEvent.DOM_VK_NUM_LOCK,
				 KeyEvent.DOM_VK_SCROLL_LOCK,
				 KeyEvent.DOM_VK_ALT,
				 KeyEvent.DOM_VK_CONTROL,
				 KeyEvent.DOM_VK_SHIFT];

// This function will return a list of available shortcuts identifiers
// on basis of a keydown DOM event.
function keyboardFunction(event)
{
	// We have to handle this keyboard event. It should be a keydown or
	// a keyup.

	var i = 0;
	var shortcut = null;
	var masterKeyMatch;
	var useControlMatch;
	var useAlternateMatch;
	var useShiftMatch;
	var shortcutsIdentifiers = new Array();

	// We search for the right key combination.
	for(i; i < shortcuts.length; i++)
	{
		masterKeyMatch, useControlMatch, useAlternateMatch, useShiftMatch = false;
		shortcut = shortcuts[i];

		masterKeyMatch 		= (event.keyCode == KeyEvent[shortcut.masterKey] || String.fromCharCode(event.charCode) == shortcut.masterKey);
		useControlMatch 	= (event.ctrlKey == shortcut.useControl);
		useAlternateMatch 	= (event.altKey == shortcut.useAlternate);
		useShiftMatch 		= (event.shiftKey == shortcut.useShift);
		
		if (masterKeyMatch && useControlMatch && useAlternateMatch && useShiftMatch)
			shortcutsIdentifiers.push(shortcut.functionId);
	}
	
	return shortcutsIdentifiers;
}

// This function will return true if the event matches a particular function Id.
// If not, it will return false ... of course !
function matchKeyboardFunction(event, functionId)
{
	var shortcutsIdentifiers = keyboardFunction(event);
	var index;
	
	for (index in shortcutsIdentifiers)
	{
		if (shortcutsIdentifiers[index] == functionId)
			return true;
	}
	
	return false;
}

function isKeyboardFunction(event)
{
	return (keyboardFunction(event).length) ? true : false;
}

function isCharKey(event)
{
	return true;
}

function isForbiddenKey(event)
{
	for (i in forbiddenKeys)
	{
		if (forbiddenKeys[i] == event.keyCode)
			return true;
	}
	
	return false;
}