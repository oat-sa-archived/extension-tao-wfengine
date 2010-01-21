/*
 * ***** BEGIN LICENSE BLOCK *****
 * This file is part of "myWiWall".
 * Copyright (c) 2007-2008 CRP Henri Tudor and contributors.
 * All rights reserved.
 *
 * "myWiWall" is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2 as published by
 * the Free Software Foundation.
 * 
 * "myWiWall" is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with "myWiWall"; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * ***** END LICENSE BLOCK *****
 */

// The language is known by the server for the currently logged
// in user.
function I18n(){}

I18n.init = function (messages)
{	
	I18n.messages = <?php echo $I18nViewData['translations']; ?>;
}

I18n.__ = function(string)
{
	var i = 0;
	
	for (i; i < I18n.messages.length; i++)
	{
		if (I18n.messages[i].original == string)
			break;
	}
	
	if (i < I18n.messages.length)
		return I18n.messages[i].translated;
	else
		return '<unknown internationalized string>';
}

// I18n initialisation.
I18n.init();