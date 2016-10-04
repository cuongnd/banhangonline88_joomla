/**
 * @package JLive! Chat
 * @version 4.3.3
 * @copyright (C) Copyright 2008-2010 CMS Fruit, CMSFruit.com. All rights reserved.
 * @license GNU/LGPL http://www.gnu.org/licenses/lgpl-3.0.txt

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU Lesser General Public License as published by
 the Free Software Foundation; either version 3 of the License, or (at your
 option) any later version.

 This program is distributed in the hope that it will be useful, but
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public
 License for more details.

 You should have received a copy of the GNU Lesser General Public License
 along with this program.  If not, see http://www.gnu.org/licenses/.
 */

function prepYUI()
{
    var bodyObj = $$('body');

    if(bodyObj) {
	bodyObj.addClass('yui-skin-sam');
    }
}

function trim(stringToTrim)
{
    return stringToTrim.replace(/^\s+|\s+$/g,"");
}

function ltrim(stringToTrim)
{
    return stringToTrim.replace(/^\s+/,"");
}

function rtrim(stringToTrim)
{
    return stringToTrim.replace(/\s+$/,"");
}

function URLEncode(clearString)
{
    var output = '';
    var x = 0;

    clearString = clearString.toString();

    var regex = /(^[a-zA-Z0-9_.]*)/;

    while(x < clearString.length)
    {
	var match = regex.exec(clearString.substr(x));

	if(match != null && match.length > 1 && match[1] != '')
	{
	    output += match[1];
	    x += match[1].length;
	}
	else
	{
	    if(clearString[x] == ' ')
	    {
		output += '+';
	    }
	    else
	    {
		var charCode = clearString.charCodeAt(x);
		var hexVal = charCode.toString(16);
		output += '%' + ( hexVal.length < 2 ? '0' : '' ) + hexVal.toUpperCase();
	    }
	    x++;
	}
    }

    return output;
}

function URLDecode(encodedString)
{
    var output = encodedString;
    var binVal, thisString;
    var myregexp = /(%[^%]{2})/;

    while((match = myregexp.exec(output)) != null && match.length > 1 && match[1] != '')
    {
	binVal = parseInt(match[1].substr(1),16);
	thisString = String.fromCharCode(binVal);
	output = output.replace(match[1], thisString);
    }

    return output;
}

