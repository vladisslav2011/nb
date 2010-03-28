var SDF_LANG = new Object();

String.prototype.replaceAll = function( strTarget, strSubString )
{
	var strText = this.toString();
	var intIndexOfMatch = this.indexOf( strTarget );
	while ( intIndexOfMatch != -1 )
	{
		strText = this.replace( strTarget, strSubString );
		intIndexOfMatch = strText.indexOf( strTarget );
	}
	return( strText );
}

function isArray( obj )
{
	return typeof( obj ) == 'object' && obj.constructor == Array;
}

function sdfReplaceParams( s, params )
{
	if ( params == undefined ) {
		return s;
	}
	if ( isArray( params ) ) 
	{
		for ( var i=0; i<params.length; i++ ) {
			s = s.replaceAll( '%'+(i+1)+'%', params[i] );
		}
	}
	else {
		s = s.replaceAll( '%1%', params );
	}
	return s;
}

function sdfGetText( s, params, cnt )
{
	if ( ! SDF_LANG.hasOwnProperty( s ) ) {
		return sdfReplaceParams( s, params );
	}
	if ( cnt == undefined ) {
		return sdfReplaceParams( SDF_LANG[s], params );
	}
	if ( isArray( SDF_LANG[s] ) ) 
	{
		var pluralFormIndex = sdfGetPluralFormIndex( cnt );
		return SDF_LANG[s].length > pluralFormIndex ? sdfReplaceParams( SDF_LANG[s][pluralFormIndex], params ) : sdfReplaceParams( SDF_LANG[s][0], params );
	}
	return sdfReplaceParams( SDF_LANG[s], params );
}

function sdfUrlTo()
{
	var url = '';
	var controller = '';
	var action = '';
	var view = '.html';
	var anchor = '';
	var id = '';
	var params = {};
	
	var arg = '';
	var firstChar = '';
	for ( i=0; i<arguments.length; i++ ) 
	{
		arg = arguments[i];
		if ( typeof( arg ) == 'object' ) {
			params = arg;
			continue;
		}
		firstChar = typeof(arg)=='string' && ( arg[0] || arg.charAt( 0 ) );
		if ( firstChar == '/' ) {
			url = arg;
		}
		else if ( firstChar >= 'A' && firstChar <= 'Z' ) {
			controller = arg;
		}
		else if ( firstChar >= 'a' && firstChar <= 'z' ) {
			action = arg;
		}
		else if ( /^[0-9]+$/.test( arg ) ) {
			id = arg;
		}
		else if ( firstChar == '.' ) {
			view = arg;
		}
		else if ( firstChar == '#' ) {
			anchor = arg;
		}
	}
	
	if ( ! url ) 
	{
		var p = controller.indexOf( 'C_' );
		if ( p >= 0 ) {
			url = '/'+controller.substr( 0, p )+'/'+controller.substr( p+2 );
		}
		else {
			url = '/'+controller;
		}
		if ( action ) {
			url += '/'+action; 
		}
		if ( id ) {
			url += '/'+id;
		}
		url += view;
	}
	
	if ( params ) 
	{
		var urlParams = '';
		for ( var i in params ) {
			urlParams += ( urlParams ? '&' : '?' ) + encodeURIComponent( i ) + '=' + encodeURIComponent( params[i] ); 
		}
		url += urlParams;
	}
	if ( anchor ) {
		url += anchor;
	}
	return url;
}

 (function(){
/*
 * jQuery 1.2.3 - New Wave Javascript
 *
 * Copyright (c) 2008 John Resig (jquery.com)
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 *
 * $Date: 2008-02-06 00:21:25 -0500 (Wed, 06 Feb 2008) $
 * $Rev: 4663 $
 */

// Map over jQuery in case of overwrite
if ( window.jQuery )
	var _jQuery = window.jQuery;

var jQuery = window.jQuery = function( selector, context ) {
	// The jQuery object is actually just the init constructor 'enhanced'
	return new jQuery.prototype.init( selector, context );
};

// Map over the $ in case of overwrite
if ( window.$ )
	var _$ = window.$;
	
// Map the jQuery namespace to the '$' one
window.$ = jQuery;

// A simple way to check for HTML strings or ID strings
// (both of which we optimize for)
var quickExpr = /^[^<]*(<(.|\s)+>)[^>]*$|^#(\w+)$/;

// Is it a simple selector
var isSimple = /^.[^:#\[\.]*$/;

jQuery.fn = jQuery.prototype = {
	init: function( selector, context ) {
		// Make sure that a selection was provided
		selector = selector || document;

		// Handle $(DOMElement)
		if ( selector.nodeType ) {
			this[0] = selector;
			this.length = 1;
			return this;

		// Handle HTML strings
		} else if ( typeof selector == "string" ) {
			// Are we dealing with HTML string or an ID?
			var match = quickExpr.exec( selector );

			// Verify a match, and that no context was specified for #id
			if ( match && (match[1] || !context) ) {

				// HANDLE: $(html) -> $(array)
				if ( match[1] )
					selector = jQuery.clean( [ match[1] ], context );

				// HANDLE: $("#id")
				else {
					var elem = document.getElementById( match[3] );

					// Make sure an element was located
					if ( elem )
						// Handle the case where IE and Opera return items
						// by name instead of ID
						if ( elem.id != match[3] )
							return jQuery().find( selector );

						// Otherwise, we inject the element directly into the jQuery object
						else {
							this[0] = elem;
							this.length = 1;
							return this;
						}

					else
						selector = [];
				}

			// HANDLE: $(expr, [context])
			// (which is just equivalent to: $(content).find(expr)
			} else
				return new jQuery( context ).find( selector );

		// HANDLE: $(function)
		// Shortcut for document ready
		} else if ( jQuery.isFunction( selector ) )
			return new jQuery( document )[ jQuery.fn.ready ? "ready" : "load" ]( selector );

		return this.setArray(
			// HANDLE: $(array)
			selector.constructor == Array && selector ||

			// HANDLE: $(arraylike)
			// Watch for when an array-like object, contains DOM nodes, is passed in as the selector
			(selector.jquery || selector.length && selector != window && !selector.nodeType && selector[0] != undefined && selector[0].nodeType) && jQuery.makeArray( selector ) ||

			// HANDLE: $(*)
			[ selector ] );
	},
	
	// The current version of jQuery being used
	jquery: "1.2.3",

	// The number of elements contained in the matched element set
	size: function() {
		return this.length;
	},
	
	// The number of elements contained in the matched element set
	length: 0,

	// Get the Nth element in the matched element set OR
	// Get the whole matched element set as a clean array
	get: function( num ) {
		return num == undefined ?

			// Return a 'clean' array
			jQuery.makeArray( this ) :

			// Return just the object
			this[ num ];
	},
	
	// Take an array of elements and push it onto the stack
	// (returning the new matched element set)
	pushStack: function( elems ) {
		// Build a new jQuery matched element set
		var ret = jQuery( elems );

		// Add the old object onto the stack (as a reference)
		ret.prevObject = this;

		// Return the newly-formed element set
		return ret;
	},
	
	// Force the current matched set of elements to become
	// the specified array of elements (destroying the stack in the process)
	// You should use pushStack() in order to do this, but maintain the stack
	setArray: function( elems ) {
		// Resetting the length to 0, then using the native Array push
		// is a super-fast way to populate an object with array-like properties
		this.length = 0;
		Array.prototype.push.apply( this, elems );
		
		return this;
	},

	// Execute a callback for every element in the matched set.
	// (You can seed the arguments with an array of args, but this is
	// only used internally.)
	each: function( callback, args ) {
		return jQuery.each( this, callback, args );
	},

	// Determine the position of an element within 
	// the matched set of elements
	index: function( elem ) {
		var ret = -1;

		// Locate the position of the desired element
		this.each(function(i){
			if ( this == elem )
				ret = i;
		});

		return ret;
	},

	attr: function( name, value, type ) {
		var options = name;
		
		// Look for the case where we're accessing a style value
		if ( name.constructor == String )
			if ( value == undefined )
				return this.length && jQuery[ type || "attr" ]( this[0], name ) || undefined;

			else {
				options = {};
				options[ name ] = value;
			}
		
		// Check to see if we're setting style values
		return this.each(function(i){
			// Set all the styles
			for ( name in options )
				jQuery.attr(
					type ?
						this.style :
						this,
					name, jQuery.prop( this, options[ name ], type, i, name )
				);
		});
	},

	css: function( key, value ) {
		// ignore negative width and height values
		if ( (key == 'width' || key == 'height') && parseFloat(value) < 0 )
			value = undefined;
		return this.attr( key, value, "curCSS" );
	},

	text: function( text ) {
		if ( typeof text != "object" && text != null )
			return this.empty().append( (this[0] && this[0].ownerDocument || document).createTextNode( text ) );

		var ret = "";

		jQuery.each( text || this, function(){
			jQuery.each( this.childNodes, function(){
				if ( this.nodeType != 8 )
					ret += this.nodeType != 1 ?
						this.nodeValue :
						jQuery.fn.text( [ this ] );
			});
		});

		return ret;
	},

	wrapAll: function( html ) {
		if ( this[0] )
			// The elements to wrap the target around
			jQuery( html, this[0].ownerDocument )
				.clone()
				.insertBefore( this[0] )
				.map(function(){
					var elem = this;

					while ( elem.firstChild )
						elem = elem.firstChild;

					return elem;
				})
				.append(this);

		return this;
	},

	wrapInner: function( html ) {
		return this.each(function(){
			jQuery( this ).contents().wrapAll( html );
		});
	},

	wrap: function( html ) {
		return this.each(function(){
			jQuery( this ).wrapAll( html );
		});
	},

	append: function() {
		return this.domManip(arguments, true, false, function(elem){
			if (this.nodeType == 1)
				this.appendChild( elem );
		});
	},

	prepend: function() {
		return this.domManip(arguments, true, true, function(elem){
			if (this.nodeType == 1)
				this.insertBefore( elem, this.firstChild );
		});
	},
	
	before: function() {
		return this.domManip(arguments, false, false, function(elem){
			this.parentNode.insertBefore( elem, this );
		});
	},

	after: function() {
		return this.domManip(arguments, false, true, function(elem){
			this.parentNode.insertBefore( elem, this.nextSibling );
		});
	},

	end: function() {
		return this.prevObject || jQuery( [] );
	},

	find: function( selector ) {
		var elems = jQuery.map(this, function(elem){
			return jQuery.find( selector, elem );
		});

		return this.pushStack( /[^+>] [^+>]/.test( selector ) || selector.indexOf("..") > -1 ?
			jQuery.unique( elems ) :
			elems );
	},

	clone: function( events ) {
		// Do the clone
		var ret = this.map(function(){
			if ( jQuery.browser.msie && !jQuery.isXMLDoc(this) ) {
				// IE copies events bound via attachEvent when
				// using cloneNode. Calling detachEvent on the
				// clone will also remove the events from the orignal
				// In order to get around this, we use innerHTML.
				// Unfortunately, this means some modifications to 
				// attributes in IE that are actually only stored 
				// as properties will not be copied (such as the
				// the name attribute on an input).
				var clone = this.cloneNode(true),
					container = document.createElement("div");
				container.appendChild(clone);
				return jQuery.clean([container.innerHTML])[0];
			} else
				return this.cloneNode(true);
		});

		// Need to set the expando to null on the cloned set if it exists
		// removeData doesn't work here, IE removes it from the original as well
		// this is primarily for IE but the data expando shouldn't be copied over in any browser
		var clone = ret.find("*").andSelf().each(function(){
			if ( this[ expando ] != undefined )
				this[ expando ] = null;
		});
		
		// Copy the events from the original to the clone
		if ( events === true )
			this.find("*").andSelf().each(function(i){
				if (this.nodeType == 3)
					return;
				var events = jQuery.data( this, "events" );

				for ( var type in events )
					for ( var handler in events[ type ] )
						jQuery.event.add( clone[ i ], type, events[ type ][ handler ], events[ type ][ handler ].data );
			});

		// Return the cloned set
		return ret;
	},

	filter: function( selector ) {
		return this.pushStack(
			jQuery.isFunction( selector ) &&
			jQuery.grep(this, function(elem, i){
				return selector.call( elem, i );
			}) ||

			jQuery.multiFilter( selector, this ) );
	},

	not: function( selector ) {
		if ( selector.constructor == String )
			// test special case where just one selector is passed in
			if ( isSimple.test( selector ) )
				return this.pushStack( jQuery.multiFilter( selector, this, true ) );
			else
				selector = jQuery.multiFilter( selector, this );

		var isArrayLike = selector.length && selector[selector.length - 1] !== undefined && !selector.nodeType;
		return this.filter(function() {
			return isArrayLike ? jQuery.inArray( this, selector ) < 0 : this != selector;
		});
	},

	add: function( selector ) {
		return !selector ? this : this.pushStack( jQuery.merge( 
			this.get(),
			selector.constructor == String ? 
				jQuery( selector ).get() :
				selector.length != undefined && (!selector.nodeName || jQuery.nodeName(selector, "form")) ?
					selector : [selector] ) );
	},

	is: function( selector ) {
		return selector ?
			jQuery.multiFilter( selector, this ).length > 0 :
			false;
	},

	hasClass: function( selector ) {
		return this.is( "." + selector );
	},
	
	val: function( value ) {
		if ( value == undefined ) {

			if ( this.length ) {
				var elem = this[0];

				// We need to handle select boxes special
				if ( jQuery.nodeName( elem, "select" ) ) {
					var index = elem.selectedIndex,
						values = [],
						options = elem.options,
						one = elem.type == "select-one";
					
					// Nothing was selected
					if ( index < 0 )
						return null;

					// Loop through all the selected options
					for ( var i = one ? index : 0, max = one ? index + 1 : options.length; i < max; i++ ) {
						var option = options[ i ];

						if ( option.selected ) {
							// Get the specifc value for the option
							value = jQuery.browser.msie && !option.attributes.value.specified ? option.text : option.value;
							
							// We don't need an array for one selects
							if ( one )
								return value;
							
							// Multi-Selects return an array
							values.push( value );
						}
					}
					
					return values;
					
				// Everything else, we just grab the value
				} else
					return (this[0].value || "").replace(/\r/g, "");

			}

			return undefined;
		}

		return this.each(function(){
			if ( this.nodeType != 1 )
				return;

			if ( value.constructor == Array && /radio|checkbox/.test( this.type ) )
				this.checked = (jQuery.inArray(this.value, value) >= 0 ||
					jQuery.inArray(this.name, value) >= 0);

			else if ( jQuery.nodeName( this, "select" ) ) {
				var values = value.constructor == Array ?
					value :
					[ value ];

				jQuery( "option", this ).each(function(){
					this.selected = (jQuery.inArray( this.value, values ) >= 0 ||
						jQuery.inArray( this.text, values ) >= 0);
				});

				if ( !values.length )
					this.selectedIndex = -1;

			} else
				this.value = value;
		});
	},
	
	html: function( value ) {
		return value == undefined ?
			(this.length ?
				this[0].innerHTML :
				null) :
			this.empty().append( value );
	},

	replaceWith: function( value ) {
		return this.after( value ).remove();
	},

	eq: function( i ) {
		return this.slice( i, i + 1 );
	},

	slice: function() {
		return this.pushStack( Array.prototype.slice.apply( this, arguments ) );
	},

	map: function( callback ) {
		return this.pushStack( jQuery.map(this, function(elem, i){
			return callback.call( elem, i, elem );
		}));
	},

	andSelf: function() {
		return this.add( this.prevObject );
	},

	data: function( key, value ){
		var parts = key.split(".");
		parts[1] = parts[1] ? "." + parts[1] : "";

		if ( value == null ) {
			var data = this.triggerHandler("getData" + parts[1] + "!", [parts[0]]);
			
			if ( data == undefined && this.length )
				data = jQuery.data( this[0], key );

			return data == null && parts[1] ?
				this.data( parts[0] ) :
				data;
		} else
			return this.trigger("setData" + parts[1] + "!", [parts[0], value]).each(function(){
				jQuery.data( this, key, value );
			});
	},

	removeData: function( key ){
		return this.each(function(){
			jQuery.removeData( this, key );
		});
	},
	
	domManip: function( args, table, reverse, callback ) {
		var clone = this.length > 1, elems; 

		return this.each(function(){
			if ( !elems ) {
				elems = jQuery.clean( args, this.ownerDocument );

				if ( reverse )
					elems.reverse();
			}

			var obj = this;

			if ( table && jQuery.nodeName( this, "table" ) && jQuery.nodeName( elems[0], "tr" ) )
				obj = this.getElementsByTagName("tbody")[0] || this.appendChild( this.ownerDocument.createElement("tbody") );

			var scripts = jQuery( [] );

			jQuery.each(elems, function(){
				var elem = clone ?
					jQuery( this ).clone( true )[0] :
					this;

				// execute all scripts after the elements have been injected
				if ( jQuery.nodeName( elem, "script" ) ) {
					scripts = scripts.add( elem );
				} else {
					// Remove any inner scripts for later evaluation
					if ( elem.nodeType == 1 )
						scripts = scripts.add( jQuery( "script", elem ).remove() );

					// Inject the elements into the document
					callback.call( obj, elem );
				}
			});

			scripts.each( evalScript );
		});
	}
};

// Give the init function the jQuery prototype for later instantiation
jQuery.prototype.init.prototype = jQuery.prototype;

function evalScript( i, elem ) {
	if ( elem.src )
		jQuery.ajax({
			url: elem.src,
			async: false,
			dataType: "script"
		});

	else
		jQuery.globalEval( elem.text || elem.textContent || elem.innerHTML || "" );

	if ( elem.parentNode )
		elem.parentNode.removeChild( elem );
}

jQuery.extend = jQuery.fn.extend = function() {
	// copy reference to target object
	var target = arguments[0] || {}, i = 1, length = arguments.length, deep = false, options;

	// Handle a deep copy situation
	if ( target.constructor == Boolean ) {
		deep = target;
		target = arguments[1] || {};
		// skip the boolean and the target
		i = 2;
	}

	// Handle case when target is a string or something (possible in deep copy)
	if ( typeof target != "object" && typeof target != "function" )
		target = {};

	// extend jQuery itself if only one argument is passed
	if ( length == 1 ) {
		target = this;
		i = 0;
	}

	for ( ; i < length; i++ )
		// Only deal with non-null/undefined values
		if ( (options = arguments[ i ]) != null )
			// Extend the base object
			for ( var name in options ) {
				// Prevent never-ending loop
				if ( target === options[ name ] )
					continue;

				// Recurse if we're merging object values
				if ( deep && options[ name ] && typeof options[ name ] == "object" && target[ name ] && !options[ name ].nodeType )
					target[ name ] = jQuery.extend( target[ name ], options[ name ] );

				// Don't bring in undefined values
				else if ( options[ name ] != undefined )
					target[ name ] = options[ name ];

			}

	// Return the modified object
	return target;
};

var expando = "jQuery" + (new Date()).getTime(), uuid = 0, windowData = {};

// exclude the following css properties to add px
var exclude = /z-?index|font-?weight|opacity|zoom|line-?height/i;

jQuery.extend({
	noConflict: function( deep ) {
		window.$ = _$;

		if ( deep )
			window.jQuery = _jQuery;

		return jQuery;
	},

	// See test/unit/core.js for details concerning this function.
	isFunction: function( fn ) {
		return !!fn && typeof fn != "string" && !fn.nodeName && 
			fn.constructor != Array && /function/i.test( fn + "" );
	},
	
	// check if an element is in a (or is an) XML document
	isXMLDoc: function( elem ) {
		return elem.documentElement && !elem.body ||
			elem.tagName && elem.ownerDocument && !elem.ownerDocument.body;
	},

	// Evalulates a script in a global context
	globalEval: function( data ) {
		data = jQuery.trim( data );

		if ( data ) {
			// Inspired by code by Andrea Giammarchi
			// http://webreflection.blogspot.com/2007/08/global-scope-evaluation-and-dom.html
			var head = document.getElementsByTagName("head")[0] || document.documentElement,
				script = document.createElement("script");

			script.type = "text/javascript";
			if ( jQuery.browser.msie )
				script.text = data;
			else
				script.appendChild( document.createTextNode( data ) );

			head.appendChild( script );
			head.removeChild( script );
		}
	},

	nodeName: function( elem, name ) {
		return elem.nodeName && elem.nodeName.toUpperCase() == name.toUpperCase();
	},
	
	cache: {},
	
	data: function( elem, name, data ) {
		elem = elem == window ?
			windowData :
			elem;

		var id = elem[ expando ];

		// Compute a unique ID for the element
		if ( !id ) 
			id = elem[ expando ] = ++uuid;

		// Only generate the data cache if we're
		// trying to access or manipulate it
		if ( name && !jQuery.cache[ id ] )
			jQuery.cache[ id ] = {};
		
		// Prevent overriding the named cache with undefined values
		if ( data != undefined )
			jQuery.cache[ id ][ name ] = data;
		
		// Return the named cache data, or the ID for the element	
		return name ?
			jQuery.cache[ id ][ name ] :
			id;
	},
	
	removeData: function( elem, name ) {
		elem = elem == window ?
			windowData :
			elem;

		var id = elem[ expando ];

		// If we want to remove a specific section of the element's data
		if ( name ) {
			if ( jQuery.cache[ id ] ) {
				// Remove the section of cache data
				delete jQuery.cache[ id ][ name ];

				// If we've removed all the data, remove the element's cache
				name = "";

				for ( name in jQuery.cache[ id ] )
					break;

				if ( !name )
					jQuery.removeData( elem );
			}

		// Otherwise, we want to remove all of the element's data
		} else {
			// Clean up the element expando
			try {
				delete elem[ expando ];
			} catch(e){
				// IE has trouble directly removing the expando
				// but it's ok with using removeAttribute
				if ( elem.removeAttribute )
					elem.removeAttribute( expando );
			}

			// Completely remove the data cache
			delete jQuery.cache[ id ];
		}
	},

	// args is for internal usage only
	each: function( object, callback, args ) {
		if ( args ) {
			if ( object.length == undefined ) {
				for ( var name in object )
					if ( callback.apply( object[ name ], args ) === false )
						break;
			} else
				for ( var i = 0, length = object.length; i < length; i++ )
					if ( callback.apply( object[ i ], args ) === false )
						break;

		// A special, fast, case for the most common use of each
		} else {
			if ( object.length == undefined ) {
				for ( var name in object )
					if ( callback.call( object[ name ], name, object[ name ] ) === false )
						break;
			} else
				for ( var i = 0, length = object.length, value = object[0]; 
					i < length && callback.call( value, i, value ) !== false; value = object[++i] ){}
		}

		return object;
	},
	
	prop: function( elem, value, type, i, name ) {
			// Handle executable functions
			if ( jQuery.isFunction( value ) )
				value = value.call( elem, i );
				
			// Handle passing in a number to a CSS property
			return value && value.constructor == Number && type == "curCSS" && !exclude.test( name ) ?
				value + "px" :
				value;
	},

	className: {
		// internal only, use addClass("class")
		add: function( elem, classNames ) {
			jQuery.each((classNames || "").split(/\s+/), function(i, className){
				if ( elem.nodeType == 1 && !jQuery.className.has( elem.className, className ) )
					elem.className += (elem.className ? " " : "") + className;
			});
		},

		// internal only, use removeClass("class")
		remove: function( elem, classNames ) {
			if (elem.nodeType == 1)
				elem.className = classNames != undefined ?
					jQuery.grep(elem.className.split(/\s+/), function(className){
						return !jQuery.className.has( classNames, className );	
					}).join(" ") :
					"";
		},

		// internal only, use is(".class")
		has: function( elem, className ) {
			return jQuery.inArray( className, (elem.className || elem).toString().split(/\s+/) ) > -1;
		}
	},

	// A method for quickly swapping in/out CSS properties to get correct calculations
	swap: function( elem, options, callback ) {
		var old = {};
		// Remember the old values, and insert the new ones
		for ( var name in options ) {
			old[ name ] = elem.style[ name ];
			elem.style[ name ] = options[ name ];
		}

		callback.call( elem );

		// Revert the old values
		for ( var name in options )
			elem.style[ name ] = old[ name ];
	},

	css: function( elem, name, force ) {
		if ( name == "width" || name == "height" ) {
			var val, props = { position: "absolute", visibility: "hidden", display:"block" }, which = name == "width" ? [ "Left", "Right" ] : [ "Top", "Bottom" ];
		
			function getWH() {
				val = name == "width" ? elem.offsetWidth : elem.offsetHeight;
				var padding = 0, border = 0;
				jQuery.each( which, function() {
					padding += parseFloat(jQuery.curCSS( elem, "padding" + this, true)) || 0;
					border += parseFloat(jQuery.curCSS( elem, "border" + this + "Width", true)) || 0;
				});
				val -= Math.round(padding + border);
			}
		
			if ( jQuery(elem).is(":visible") )
				getWH();
			else
				jQuery.swap( elem, props, getWH );
			
			return Math.max(0, val);
		}
		
		return jQuery.curCSS( elem, name, force );
	},

	curCSS: function( elem, name, force ) {
		var ret;

		// A helper method for determining if an element's values are broken
		function color( elem ) {
			if ( !jQuery.browser.safari )
				return false;

			var ret = document.defaultView.getComputedStyle( elem, null );
			return !ret || ret.getPropertyValue("color") == "";
		}

		// We need to handle opacity special in IE
		if ( name == "opacity" && jQuery.browser.msie ) {
			ret = jQuery.attr( elem.style, "opacity" );

			return ret == "" ?
				"1" :
				ret;
		}
		// Opera sometimes will give the wrong display answer, this fixes it, see #2037
		if ( jQuery.browser.opera && name == "display" ) {
			var save = elem.style.outline;
			elem.style.outline = "0 solid black";
			elem.style.outline = save;
		}
		
		// Make sure we're using the right name for getting the float value
		if ( name.match( /float/i ) )
			name = styleFloat;

		if ( !force && elem.style && elem.style[ name ] )
			ret = elem.style[ name ];

		else if ( document.defaultView && document.defaultView.getComputedStyle ) {

			// Only "float" is needed here
			if ( name.match( /float/i ) )
				name = "float";

			name = name.replace( /([A-Z])/g, "-$1" ).toLowerCase();

			var getComputedStyle = document.defaultView.getComputedStyle( elem, null );

			if ( getComputedStyle && !color( elem ) )
				ret = getComputedStyle.getPropertyValue( name );

			// If the element isn't reporting its values properly in Safari
			// then some display: none elements are involved
			else {
				var swap = [], stack = [];

				// Locate all of the parent display: none elements
				for ( var a = elem; a && color(a); a = a.parentNode )
					stack.unshift(a);

				// Go through and make them visible, but in reverse
				// (It would be better if we knew the exact display type that they had)
				for ( var i = 0; i < stack.length; i++ )
					if ( color( stack[ i ] ) ) {
						swap[ i ] = stack[ i ].style.display;
						stack[ i ].style.display = "block";
					}

				// Since we flip the display style, we have to handle that
				// one special, otherwise get the value
				ret = name == "display" && swap[ stack.length - 1 ] != null ?
					"none" :
					( getComputedStyle && getComputedStyle.getPropertyValue( name ) ) || "";

				// Finally, revert the display styles back
				for ( var i = 0; i < swap.length; i++ )
					if ( swap[ i ] != null )
						stack[ i ].style.display = swap[ i ];
			}

			// We should always get a number back from opacity
			if ( name == "opacity" && ret == "" )
				ret = "1";

		} else if ( elem.currentStyle ) {
			var camelCase = name.replace(/\-(\w)/g, function(all, letter){
				return letter.toUpperCase();
			});

			ret = elem.currentStyle[ name ] || elem.currentStyle[ camelCase ];

			// From the awesome hack by Dean Edwards
			// http://erik.eae.net/archives/2007/07/27/18.54.15/#comment-102291

			// If we're not dealing with a regular pixel number
			// but a number that has a weird ending, we need to convert it to pixels
			if ( !/^\d+(px)?$/i.test( ret ) && /^\d/.test( ret ) ) {
				// Remember the original values
				var style = elem.style.left, runtimeStyle = elem.runtimeStyle.left;

				// Put in the new values to get a computed value out
				elem.runtimeStyle.left = elem.currentStyle.left;
				elem.style.left = ret || 0;
				ret = elem.style.pixelLeft + "px";

				// Revert the changed values
				elem.style.left = style;
				elem.runtimeStyle.left = runtimeStyle;
			}
		}

		return ret;
	},
	
	clean: function( elems, context ) {
		var ret = [];
		context = context || document;
		// !context.createElement fails in IE with an error but returns typeof 'object'
		if (typeof context.createElement == 'undefined') 
			context = context.ownerDocument || context[0] && context[0].ownerDocument || document;

		jQuery.each(elems, function(i, elem){
			if ( !elem )
				return;

			if ( elem.constructor == Number )
				elem = elem.toString();
			
			// Convert html string into DOM nodes
			if ( typeof elem == "string" ) {
				// Fix "XHTML"-style tags in all browsers
				elem = elem.replace(/(<(\w+)[^>]*?)\/>/g, function(all, front, tag){
					return tag.match(/^(abbr|br|col|img|input|link|meta|param|hr|area|embed)$/i) ?
						all :
						front + "></" + tag + ">";
				});

				// Trim whitespace, otherwise indexOf won't work as expected
				var tags = jQuery.trim( elem ).toLowerCase(), div = context.createElement("div");

				var wrap =
					// option or optgroup
					!tags.indexOf("<opt") &&
					[ 1, "<select multiple='multiple'>", "</select>" ] ||
					
					!tags.indexOf("<leg") &&
					[ 1, "<fieldset>", "</fieldset>" ] ||
					
					tags.match(/^<(thead|tbody|tfoot|colg|cap)/) &&
					[ 1, "<table>", "</table>" ] ||
					
					!tags.indexOf("<tr") &&
					[ 2, "<table><tbody>", "</tbody></table>" ] ||
					
				 	// <thead> matched above
					(!tags.indexOf("<td") || !tags.indexOf("<th")) &&
					[ 3, "<table><tbody><tr>", "</tr></tbody></table>" ] ||
					
					!tags.indexOf("<col") &&
					[ 2, "<table><tbody></tbody><colgroup>", "</colgroup></table>" ] ||

					// IE can't serialize <link> and <script> tags normally
					jQuery.browser.msie &&
					[ 1, "div<div>", "</div>" ] ||
					
					[ 0, "", "" ];

				// Go to html and back, then peel off extra wrappers
				div.innerHTML = wrap[1] + elem + wrap[2];
				
				// Move to the right depth
				while ( wrap[0]-- )
					div = div.lastChild;
				
				// Remove IE's autoinserted <tbody> from table fragments
				if ( jQuery.browser.msie ) {
					
					// String was a <table>, *may* have spurious <tbody>
					var tbody = !tags.indexOf("<table") && tags.indexOf("<tbody") < 0 ?
						div.firstChild && div.firstChild.childNodes :
						
						// String was a bare <thead> or <tfoot>
						wrap[1] == "<table>" && tags.indexOf("<tbody") < 0 ?
							div.childNodes :
							[];
				
					for ( var j = tbody.length - 1; j >= 0 ; --j )
						if ( jQuery.nodeName( tbody[ j ], "tbody" ) && !tbody[ j ].childNodes.length )
							tbody[ j ].parentNode.removeChild( tbody[ j ] );
					
					// IE completely kills leading whitespace when innerHTML is used	
					if ( /^\s/.test( elem ) )	
						div.insertBefore( context.createTextNode( elem.match(/^\s*/)[0] ), div.firstChild );
				
				}
				
				elem = jQuery.makeArray( div.childNodes );
			}

			if ( elem.length === 0 && (!jQuery.nodeName( elem, "form" ) && !jQuery.nodeName( elem, "select" )) )
				return;

			if ( elem[0] == undefined || jQuery.nodeName( elem, "form" ) || elem.options )
				ret.push( elem );

			else
				ret = jQuery.merge( ret, elem );

		});

		return ret;
	},
	
	attr: function( elem, name, value ) {
		// don't set attributes on text and comment nodes
		if (!elem || elem.nodeType == 3 || elem.nodeType == 8)
			return undefined;

		var fix = jQuery.isXMLDoc( elem ) ?
			{} :
			jQuery.props;

		// Safari mis-reports the default selected property of a hidden option
		// Accessing the parent's selectedIndex property fixes it
		if ( name == "selected" && jQuery.browser.safari )
			elem.parentNode.selectedIndex;
		
		// Certain attributes only work when accessed via the old DOM 0 way
		if ( fix[ name ] ) {
			if ( value != undefined )
				elem[ fix[ name ] ] = value;

			return elem[ fix[ name ] ];

		} else if ( jQuery.browser.msie && name == "style" )
			return jQuery.attr( elem.style, "cssText", value );

		else if ( value == undefined && jQuery.browser.msie && jQuery.nodeName( elem, "form" ) && (name == "action" || name == "method") )
			return elem.getAttributeNode( name ).nodeValue;

		// IE elem.getAttribute passes even for style
		else if ( elem.tagName ) {

			if ( value != undefined ) {
				// We can't allow the type property to be changed (since it causes problems in IE)
				if ( name == "type" && jQuery.nodeName( elem, "input" ) && elem.parentNode )
					throw "type property can't be changed";

				// convert the value to a string (all browsers do this but IE) see #1070
				elem.setAttribute( name, "" + value );
			}

			if ( jQuery.browser.msie && /href|src/.test( name ) && !jQuery.isXMLDoc( elem ) ) 
				return elem.getAttribute( name, 2 );

			return elem.getAttribute( name );

		// elem is actually elem.style ... set the style
		} else {
			// IE actually uses filters for opacity
			if ( name == "opacity" && jQuery.browser.msie ) {
				if ( value != undefined ) {
					// IE has trouble with opacity if it does not have layout
					// Force it by setting the zoom level
					elem.zoom = 1; 
	
					// Set the alpha filter to set the opacity
					elem.filter = (elem.filter || "").replace( /alpha\([^)]*\)/, "" ) +
						(parseFloat( value ).toString() == "NaN" ? "" : "alpha(opacity=" + value * 100 + ")");
				}
	
				return elem.filter && elem.filter.indexOf("opacity=") >= 0 ?
					(parseFloat( elem.filter.match(/opacity=([^)]*)/)[1] ) / 100).toString() :
					"";
			}

			name = name.replace(/-([a-z])/ig, function(all, letter){
				return letter.toUpperCase();
			});

			if ( value != undefined )
				elem[ name ] = value;

			return elem[ name ];
		}
	},
	
	trim: function( text ) {
		return (text || "").replace( /^\s+|\s+$/g, "" );
	},

	makeArray: function( array ) {
		var ret = [];

		// Need to use typeof to fight Safari childNodes crashes
		if ( typeof array != "array" )
			for ( var i = 0, length = array.length; i < length; i++ )
				ret.push( array[ i ] );
		else
			ret = array.slice( 0 );

		return ret;
	},

	inArray: function( elem, array ) {
		for ( var i = 0, length = array.length; i < length; i++ )
			if ( array[ i ] == elem )
				return i;

		return -1;
	},

	merge: function( first, second ) {
		// We have to loop this way because IE & Opera overwrite the length
		// expando of getElementsByTagName

		// Also, we need to make sure that the correct elements are being returned
		// (IE returns comment nodes in a '*' query)
		if ( jQuery.browser.msie ) {
			for ( var i = 0; second[ i ]; i++ )
				if ( second[ i ].nodeType != 8 )
					first.push( second[ i ] );

		} else
			for ( var i = 0; second[ i ]; i++ )
				first.push( second[ i ] );

		return first;
	},

	unique: function( array ) {
		var ret = [], done = {};

		try {

			for ( var i = 0, length = array.length; i < length; i++ ) {
				var id = jQuery.data( array[ i ] );

				if ( !done[ id ] ) {
					done[ id ] = true;
					ret.push( array[ i ] );
				}
			}

		} catch( e ) {
			ret = array;
		}

		return ret;
	},

	grep: function( elems, callback, inv ) {
		var ret = [];

		// Go through the array, only saving the items
		// that pass the validator function
		for ( var i = 0, length = elems.length; i < length; i++ )
			if ( !inv && callback( elems[ i ], i ) || inv && !callback( elems[ i ], i ) )
				ret.push( elems[ i ] );

		return ret;
	},

	map: function( elems, callback ) {
		var ret = [];

		// Go through the array, translating each of the items to their
		// new value (or values).
		for ( var i = 0, length = elems.length; i < length; i++ ) {
			var value = callback( elems[ i ], i );

			if ( value !== null && value != undefined ) {
				if ( value.constructor != Array )
					value = [ value ];

				ret = ret.concat( value );
			}
		}

		return ret;
	}
});

var userAgent = navigator.userAgent.toLowerCase();

// Figure out what browser is being used
jQuery.browser = {
	version: (userAgent.match( /.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/ ) || [])[1],
	safari: /webkit/.test( userAgent ),
	opera: /opera/.test( userAgent ),
	msie: /msie/.test( userAgent ) && !/opera/.test( userAgent ),
	mozilla: /mozilla/.test( userAgent ) && !/(compatible|webkit)/.test( userAgent )
};

var styleFloat = jQuery.browser.msie ?
	"styleFloat" :
	"cssFloat";
	
jQuery.extend({
	// Check to see if the W3C box model is being used
	boxModel: !jQuery.browser.msie || document.compatMode == "CSS1Compat",
	
	props: {
		"for": "htmlFor",
		"class": "className",
		"float": styleFloat,
		cssFloat: styleFloat,
		styleFloat: styleFloat,
		innerHTML: "innerHTML",
		className: "className",
		value: "value",
		disabled: "disabled",
		checked: "checked",
		readonly: "readOnly",
		selected: "selected",
		maxlength: "maxLength",
		selectedIndex: "selectedIndex",
		defaultValue: "defaultValue",
		tagName: "tagName",
		nodeName: "nodeName"
	}
});

jQuery.each({
	parent: function(elem){return elem.parentNode;},
	parents: function(elem){return jQuery.dir(elem,"parentNode");},
	next: function(elem){return jQuery.nth(elem,2,"nextSibling");},
	prev: function(elem){return jQuery.nth(elem,2,"previousSibling");},
	nextAll: function(elem){return jQuery.dir(elem,"nextSibling");},
	prevAll: function(elem){return jQuery.dir(elem,"previousSibling");},
	siblings: function(elem){return jQuery.sibling(elem.parentNode.firstChild,elem);},
	children: function(elem){return jQuery.sibling(elem.firstChild);},
	contents: function(elem){return jQuery.nodeName(elem,"iframe")?elem.contentDocument||elem.contentWindow.document:jQuery.makeArray(elem.childNodes);}
}, function(name, fn){
	jQuery.fn[ name ] = function( selector ) {
		var ret = jQuery.map( this, fn );

		if ( selector && typeof selector == "string" )
			ret = jQuery.multiFilter( selector, ret );

		return this.pushStack( jQuery.unique( ret ) );
	};
});

jQuery.each({
	appendTo: "append",
	prependTo: "prepend",
	insertBefore: "before",
	insertAfter: "after",
	replaceAll: "replaceWith"
}, function(name, original){
	jQuery.fn[ name ] = function() {
		var args = arguments;

		return this.each(function(){
			for ( var i = 0, length = args.length; i < length; i++ )
				jQuery( args[ i ] )[ original ]( this );
		});
	};
});

jQuery.each({
	removeAttr: function( name ) {
		jQuery.attr( this, name, "" );
		if (this.nodeType == 1) 
			this.removeAttribute( name );
	},

	addClass: function( classNames ) {
		jQuery.className.add( this, classNames );
	},

	removeClass: function( classNames ) {
		jQuery.className.remove( this, classNames );
	},

	toggleClass: function( classNames ) {
		jQuery.className[ jQuery.className.has( this, classNames ) ? "remove" : "add" ]( this, classNames );
	},

	remove: function( selector ) {
		if ( !selector || jQuery.filter( selector, [ this ] ).r.length ) {
			// Prevent memory leaks
			jQuery( "*", this ).add(this).each(function(){
				jQuery.event.remove(this);
				jQuery.removeData(this);
			});
			if (this.parentNode)
				this.parentNode.removeChild( this );
		}
	},

	empty: function() {
		// Remove element nodes and prevent memory leaks
		jQuery( ">*", this ).remove();
		
		// Remove any remaining nodes
		while ( this.firstChild )
			this.removeChild( this.firstChild );
	}
}, function(name, fn){
	jQuery.fn[ name ] = function(){
		return this.each( fn, arguments );
	};
});

jQuery.each([ "Height", "Width" ], function(i, name){
	var type = name.toLowerCase();
	
	jQuery.fn[ type ] = function( size ) {
		// Get window width or height
		return this[0] == window ?
			// Opera reports document.body.client[Width/Height] properly in both quirks and standards
			jQuery.browser.opera && document.body[ "client" + name ] || 
			
			// Safari reports inner[Width/Height] just fine (Mozilla and Opera include scroll bar widths)
			jQuery.browser.safari && window[ "inner" + name ] ||
			
			// Everyone else use document.documentElement or document.body depending on Quirks vs Standards mode
			document.compatMode == "CSS1Compat" && document.documentElement[ "client" + name ] || document.body[ "client" + name ] :
		
			// Get document width or height
			this[0] == document ?
				// Either scroll[Width/Height] or offset[Width/Height], whichever is greater
				Math.max( 
					Math.max(document.body["scroll" + name], document.documentElement["scroll" + name]), 
					Math.max(document.body["offset" + name], document.documentElement["offset" + name]) 
				) :

				// Get or set width or height on the element
				size == undefined ?
					// Get width or height on the element
					(this.length ? jQuery.css( this[0], type ) : null) :

					// Set the width or height on the element (default to pixels if value is unitless)
					this.css( type, size.constructor == String ? size : size + "px" );
	};
});

var chars = jQuery.browser.safari && parseInt(jQuery.browser.version) < 417 ?
		"(?:[\\w*_-]|\\\\.)" :
		"(?:[\\w\u0128-\uFFFF*_-]|\\\\.)",
	quickChild = new RegExp("^>\\s*(" + chars + "+)"),
	quickID = new RegExp("^(" + chars + "+)(#)(" + chars + "+)"),
	quickClass = new RegExp("^([#.]?)(" + chars + "*)");

jQuery.extend({
	expr: {
		"": function(a,i,m){return m[2]=="*"||jQuery.nodeName(a,m[2]);},
		"#": function(a,i,m){return a.getAttribute("id")==m[2];},
		":": {
			// Position Checks
			lt: function(a,i,m){return i<m[3]-0;},
			gt: function(a,i,m){return i>m[3]-0;},
			nth: function(a,i,m){return m[3]-0==i;},
			eq: function(a,i,m){return m[3]-0==i;},
			first: function(a,i){return i==0;},
			last: function(a,i,m,r){return i==r.length-1;},
			even: function(a,i){return i%2==0;},
			odd: function(a,i){return i%2;},

			// Child Checks
			"first-child": function(a){return a.parentNode.getElementsByTagName("*")[0]==a;},
			"last-child": function(a){return jQuery.nth(a.parentNode.lastChild,1,"previousSibling")==a;},
			"only-child": function(a){return !jQuery.nth(a.parentNode.lastChild,2,"previousSibling");},

			// Parent Checks
			parent: function(a){return a.firstChild;},
			empty: function(a){return !a.firstChild;},

			// Text Check
			contains: function(a,i,m){return (a.textContent||a.innerText||jQuery(a).text()||"").indexOf(m[3])>=0;},

			// Visibility
			visible: function(a){return "hidden"!=a.type&&jQuery.css(a,"display")!="none"&&jQuery.css(a,"visibility")!="hidden";},
			hidden: function(a){return "hidden"==a.type||jQuery.css(a,"display")=="none"||jQuery.css(a,"visibility")=="hidden";},

			// Form attributes
			enabled: function(a){return !a.disabled;},
			disabled: function(a){return a.disabled;},
			checked: function(a){return a.checked;},
			selected: function(a){return a.selected||jQuery.attr(a,"selected");},

			// Form elements
			text: function(a){return "text"==a.type;},
			radio: function(a){return "radio"==a.type;},
			checkbox: function(a){return "checkbox"==a.type;},
			file: function(a){return "file"==a.type;},
			password: function(a){return "password"==a.type;},
			submit: function(a){return "submit"==a.type;},
			image: function(a){return "image"==a.type;},
			reset: function(a){return "reset"==a.type;},
			button: function(a){return "button"==a.type||jQuery.nodeName(a,"button");},
			input: function(a){return /input|select|textarea|button/i.test(a.nodeName);},

			// :has()
			has: function(a,i,m){return jQuery.find(m[3],a).length;},

			// :header
			header: function(a){return /h\d/i.test(a.nodeName);},

			// :animated
			animated: function(a){return jQuery.grep(jQuery.timers,function(fn){return a==fn.elem;}).length;}
		}
	},
	
	// The regular expressions that power the parsing engine
	parse: [
		// Match: [@value='test'], [@foo]
		/^(\[) *@?([\w-]+) *([!*$^~=]*) *('?"?)(.*?)\4 *\]/,

		// Match: :contains('foo')
		/^(:)([\w-]+)\("?'?(.*?(\(.*?\))?[^(]*?)"?'?\)/,

		// Match: :even, :last-chlid, #id, .class
		new RegExp("^([:.#]*)(" + chars + "+)")
	],

	multiFilter: function( expr, elems, not ) {
		var old, cur = [];

		while ( expr && expr != old ) {
			old = expr;
			var f = jQuery.filter( expr, elems, not );
			expr = f.t.replace(/^\s*,\s*/, "" );
			cur = not ? elems = f.r : jQuery.merge( cur, f.r );
		}

		return cur;
	},

	find: function( t, context ) {
		// Quickly handle non-string expressions
		if ( typeof t != "string" )
			return [ t ];

		// check to make sure context is a DOM element or a document
		if ( context && context.nodeType != 1 && context.nodeType != 9)
			return [ ];

		// Set the correct context (if none is provided)
		context = context || document;

		// Initialize the search
		var ret = [context], done = [], last, nodeName;

		// Continue while a selector expression exists, and while
		// we're no longer looping upon ourselves
		while ( t && last != t ) {
			var r = [];
			last = t;

			t = jQuery.trim(t);

			var foundToken = false;

			// An attempt at speeding up child selectors that
			// point to a specific element tag
			var re = quickChild;
			var m = re.exec(t);

			if ( m ) {
				nodeName = m[1].toUpperCase();

				// Perform our own iteration and filter
				for ( var i = 0; ret[i]; i++ )
					for ( var c = ret[i].firstChild; c; c = c.nextSibling )
						if ( c.nodeType == 1 && (nodeName == "*" || c.nodeName.toUpperCase() == nodeName) )
							r.push( c );

				ret = r;
				t = t.replace( re, "" );
				if ( t.indexOf(" ") == 0 ) continue;
				foundToken = true;
			} else {
				re = /^([>+~])\s*(\w*)/i;

				if ( (m = re.exec(t)) != null ) {
					r = [];

					var merge = {};
					nodeName = m[2].toUpperCase();
					m = m[1];

					for ( var j = 0, rl = ret.length; j < rl; j++ ) {
						var n = m == "~" || m == "+" ? ret[j].nextSibling : ret[j].firstChild;
						for ( ; n; n = n.nextSibling )
							if ( n.nodeType == 1 ) {
								var id = jQuery.data(n);

								if ( m == "~" && merge[id] ) break;
								
								if (!nodeName || n.nodeName.toUpperCase() == nodeName ) {
									if ( m == "~" ) merge[id] = true;
									r.push( n );
								}
								
								if ( m == "+" ) break;
							}
					}

					ret = r;

					// And remove the token
					t = jQuery.trim( t.replace( re, "" ) );
					foundToken = true;
				}
			}

			// See if there's still an expression, and that we haven't already
			// matched a token
			if ( t && !foundToken ) {
				// Handle multiple expressions
				if ( !t.indexOf(",") ) {
					// Clean the result set
					if ( context == ret[0] ) ret.shift();

					// Merge the result sets
					done = jQuery.merge( done, ret );

					// Reset the context
					r = ret = [context];

					// Touch up the selector string
					t = " " + t.substr(1,t.length);

				} else {
					// Optimize for the case nodeName#idName
					var re2 = quickID;
					var m = re2.exec(t);
					
					// Re-organize the results, so that they're consistent
					if ( m ) {
						m = [ 0, m[2], m[3], m[1] ];

					} else {
						// Otherwise, do a traditional filter check for
						// ID, class, and element selectors
						re2 = quickClass;
						m = re2.exec(t);
					}

					m[2] = m[2].replace(/\\/g, "");

					var elem = ret[ret.length-1];

					// Try to do a global search by ID, where we can
					if ( m[1] == "#" && elem && elem.getElementById && !jQuery.isXMLDoc(elem) ) {
						// Optimization for HTML document case
						var oid = elem.getElementById(m[2]);
						
						// Do a quick check for the existence of the actual ID attribute
						// to avoid selecting by the name attribute in IE
						// also check to insure id is a string to avoid selecting an element with the name of 'id' inside a form
						if ( (jQuery.browser.msie||jQuery.browser.opera) && oid && typeof oid.id == "string" && oid.id != m[2] )
							oid = jQuery('[@id="'+m[2]+'"]', elem)[0];

						// Do a quick check for node name (where applicable) so
						// that div#foo searches will be really fast
						ret = r = oid && (!m[3] || jQuery.nodeName(oid, m[3])) ? [oid] : [];
					} else {
						// We need to find all descendant elements
						for ( var i = 0; ret[i]; i++ ) {
							// Grab the tag name being searched for
							var tag = m[1] == "#" && m[3] ? m[3] : m[1] != "" || m[0] == "" ? "*" : m[2];

							// Handle IE7 being really dumb about <object>s
							if ( tag == "*" && ret[i].nodeName.toLowerCase() == "object" )
								tag = "param";

							r = jQuery.merge( r, ret[i].getElementsByTagName( tag ));
						}

						// It's faster to filter by class and be done with it
						if ( m[1] == "." )
							r = jQuery.classFilter( r, m[2] );

						// Same with ID filtering
						if ( m[1] == "#" ) {
							var tmp = [];

							// Try to find the element with the ID
							for ( var i = 0; r[i]; i++ )
								if ( r[i].getAttribute("id") == m[2] ) {
									tmp = [ r[i] ];
									break;
								}

							r = tmp;
						}

						ret = r;
					}

					t = t.replace( re2, "" );
				}

			}

			// If a selector string still exists
			if ( t ) {
				// Attempt to filter it
				var val = jQuery.filter(t,r);
				ret = r = val.r;
				t = jQuery.trim(val.t);
			}
		}

		// An error occurred with the selector;
		// just return an empty set instead
		if ( t )
			ret = [];

		// Remove the root context
		if ( ret && context == ret[0] )
			ret.shift();

		// And combine the results
		done = jQuery.merge( done, ret );

		return done;
	},

	classFilter: function(r,m,not){
		m = " " + m + " ";
		var tmp = [];
		for ( var i = 0; r[i]; i++ ) {
			var pass = (" " + r[i].className + " ").indexOf( m ) >= 0;
			if ( !not && pass || not && !pass )
				tmp.push( r[i] );
		}
		return tmp;
	},

	filter: function(t,r,not) {
		var last;

		// Look for common filter expressions
		while ( t && t != last ) {
			last = t;

			var p = jQuery.parse, m;

			for ( var i = 0; p[i]; i++ ) {
				m = p[i].exec( t );

				if ( m ) {
					// Remove what we just matched
					t = t.substring( m[0].length );

					m[2] = m[2].replace(/\\/g, "");
					break;
				}
			}

			if ( !m )
				break;

			// :not() is a special case that can be optimized by
			// keeping it out of the expression list
			if ( m[1] == ":" && m[2] == "not" )
				// optimize if only one selector found (most common case)
				r = isSimple.test( m[3] ) ?
					jQuery.filter(m[3], r, true).r :
					jQuery( r ).not( m[3] );

			// We can get a big speed boost by filtering by class here
			else if ( m[1] == "." )
				r = jQuery.classFilter(r, m[2], not);

			else if ( m[1] == "[" ) {
				var tmp = [], type = m[3];
				
				for ( var i = 0, rl = r.length; i < rl; i++ ) {
					var a = r[i], z = a[ jQuery.props[m[2]] || m[2] ];
					
					if ( z == null || /href|src|selected/.test(m[2]) )
						z = jQuery.attr(a,m[2]) || '';

					if ( (type == "" && !!z ||
						 type == "=" && z == m[5] ||
						 type == "!=" && z != m[5] ||
						 type == "^=" && z && !z.indexOf(m[5]) ||
						 type == "$=" && z.substr(z.length - m[5].length) == m[5] ||
						 (type == "*=" || type == "~=") && z.indexOf(m[5]) >= 0) ^ not )
							tmp.push( a );
				}
				
				r = tmp;

			// We can get a speed boost by handling nth-child here
			} else if ( m[1] == ":" && m[2] == "nth-child" ) {
				var merge = {}, tmp = [],
					// parse equations like 'even', 'odd', '5', '2n', '3n+2', '4n-1', '-n+6'
					test = /(-?)(\d*)n((?:\+|-)?\d*)/.exec(
						m[3] == "even" && "2n" || m[3] == "odd" && "2n+1" ||
						!/\D/.test(m[3]) && "0n+" + m[3] || m[3]),
					// calculate the numbers (first)n+(last) including if they are negative
					first = (test[1] + (test[2] || 1)) - 0, last = test[3] - 0;
 
				// loop through all the elements left in the jQuery object
				for ( var i = 0, rl = r.length; i < rl; i++ ) {
					var node = r[i], parentNode = node.parentNode, id = jQuery.data(parentNode);

					if ( !merge[id] ) {
						var c = 1;

						for ( var n = parentNode.firstChild; n; n = n.nextSibling )
							if ( n.nodeType == 1 )
								n.nodeIndex = c++;

						merge[id] = true;
					}

					var add = false;

					if ( first == 0 ) {
						if ( node.nodeIndex == last )
							add = true;
					} else if ( (node.nodeIndex - last) % first == 0 && (node.nodeIndex - last) / first >= 0 )
						add = true;

					if ( add ^ not )
						tmp.push( node );
				}

				r = tmp;

			// Otherwise, find the expression to execute
			} else {
				var fn = jQuery.expr[ m[1] ];
				if ( typeof fn == "object" )
					fn = fn[ m[2] ];

				if ( typeof fn == "string" )
					fn = eval("false||function(a,i){return " + fn + ";}");

				// Execute it against the current filter
				r = jQuery.grep( r, function(elem, i){
					return fn(elem, i, m, r);
				}, not );
			}
		}

		// Return an array of filtered elements (r)
		// and the modified expression string (t)
		return { r: r, t: t };
	},

	dir: function( elem, dir ){
		var matched = [];
		var cur = elem[dir];
		while ( cur && cur != document ) {
			if ( cur.nodeType == 1 )
				matched.push( cur );
			cur = cur[dir];
		}
		return matched;
	},
	
	nth: function(cur,result,dir,elem){
		result = result || 1;
		var num = 0;

		for ( ; cur; cur = cur[dir] )
			if ( cur.nodeType == 1 && ++num == result )
				break;

		return cur;
	},
	
	sibling: function( n, elem ) {
		var r = [];

		for ( ; n; n = n.nextSibling ) {
			if ( n.nodeType == 1 && (!elem || n != elem) )
				r.push( n );
		}

		return r;
	}
});

/*
 * A number of helper functions used for managing events.
 * Many of the ideas behind this code orignated from 
 * Dean Edwards' addEvent library.
 */
jQuery.event = {

	// Bind an event to an element
	// Original by Dean Edwards
	add: function(elem, types, handler, data) {
		if ( elem.nodeType == 3 || elem.nodeType == 8 )
			return;

		// For whatever reason, IE has trouble passing the window object
		// around, causing it to be cloned in the process
		if ( jQuery.browser.msie && elem.setInterval != undefined )
			elem = window;

		// Make sure that the function being executed has a unique ID
		if ( !handler.guid )
			handler.guid = this.guid++;
			
		// if data is passed, bind to handler 
		if( data != undefined ) { 
			// Create temporary function pointer to original handler 
			var fn = handler; 

			// Create unique handler function, wrapped around original handler 
			handler = function() { 
				// Pass arguments and context to original handler 
				return fn.apply(this, arguments); 
			};

			// Store data in unique handler 
			handler.data = data;

			// Set the guid of unique handler to the same of original handler, so it can be removed 
			handler.guid = fn.guid;
		}

		// Init the element's event structure
		var events = jQuery.data(elem, "events") || jQuery.data(elem, "events", {}),
			handle = jQuery.data(elem, "handle") || jQuery.data(elem, "handle", function(){
				// returned undefined or false
				var val;

				// Handle the second event of a trigger and when
				// an event is called after a page has unloaded
				if ( typeof jQuery == "undefined" || jQuery.event.triggered )
					return val;
		
				val = jQuery.event.handle.apply(arguments.callee.elem, arguments);
		
				return val;
			});
		// Add elem as a property of the handle function
		// This is to prevent a memory leak with non-native
		// event in IE.
		handle.elem = elem;
			
			// Handle multiple events seperated by a space
			// jQuery(...).bind("mouseover mouseout", fn);
			jQuery.each(types.split(/\s+/), function(index, type) {
				// Namespaced event handlers
				var parts = type.split(".");
				type = parts[0];
				handler.type = parts[1];

				// Get the current list of functions bound to this event
				var handlers = events[type];

				// Init the event handler queue
				if (!handlers) {
					handlers = events[type] = {};
		
					// Check for a special event handler
					// Only use addEventListener/attachEvent if the special
					// events handler returns false
					if ( !jQuery.event.special[type] || jQuery.event.special[type].setup.call(elem) === false ) {
						// Bind the global event handler to the element
						if (elem.addEventListener)
							elem.addEventListener(type, handle, false);
						else if (elem.attachEvent)
							elem.attachEvent("on" + type, handle);
					}
				}

				// Add the function to the element's handler list
				handlers[handler.guid] = handler;

				// Keep track of which events have been used, for global triggering
				jQuery.event.global[type] = true;
			});
		
		// Nullify elem to prevent memory leaks in IE
		elem = null;
	},

	guid: 1,
	global: {},

	// Detach an event or set of events from an element
	remove: function(elem, types, handler) {
		// don't do events on text and comment nodes
		if ( elem.nodeType == 3 || elem.nodeType == 8 )
			return;

		var events = jQuery.data(elem, "events"), ret, index;

		if ( events ) {
			// Unbind all events for the element
			if ( types == undefined || (typeof types == "string" && types.charAt(0) == ".") )
				for ( var type in events )
					this.remove( elem, type + (types || "") );
			else {
				// types is actually an event object here
				if ( types.type ) {
					handler = types.handler;
					types = types.type;
				}
				
				// Handle multiple events seperated by a space
				// jQuery(...).unbind("mouseover mouseout", fn);
				jQuery.each(types.split(/\s+/), function(index, type){
					// Namespaced event handlers
					var parts = type.split(".");
					type = parts[0];
					
					if ( events[type] ) {
						// remove the given handler for the given type
						if ( handler )
							delete events[type][handler.guid];
			
						// remove all handlers for the given type
						else
							for ( handler in events[type] )
								// Handle the removal of namespaced events
								if ( !parts[1] || events[type][handler].type == parts[1] )
									delete events[type][handler];

						// remove generic event handler if no more handlers exist
						for ( ret in events[type] ) break;
						if ( !ret ) {
							if ( !jQuery.event.special[type] || jQuery.event.special[type].teardown.call(elem) === false ) {
								if (elem.removeEventListener)
									elem.removeEventListener(type, jQuery.data(elem, "handle"), false);
								else if (elem.detachEvent)
									elem.detachEvent("on" + type, jQuery.data(elem, "handle"));
							}
							ret = null;
							delete events[type];
						}
					}
				});
			}

			// Remove the expando if it's no longer used
			for ( ret in events ) break;
			if ( !ret ) {
				var handle = jQuery.data( elem, "handle" );
				if ( handle ) handle.elem = null;
				jQuery.removeData( elem, "events" );
				jQuery.removeData( elem, "handle" );
			}
		}
	},

	trigger: function(type, data, elem, donative, extra) {
		// Clone the incoming data, if any
		data = jQuery.makeArray(data || []);

		if ( type.indexOf("!") >= 0 ) {
			type = type.slice(0, -1);
			var exclusive = true;
		}

		// Handle a global trigger
		if ( !elem ) {
			// Only trigger if we've ever bound an event for it
			if ( this.global[type] )
				jQuery("*").add([window, document]).trigger(type, data);

		// Handle triggering a single element
		} else {
			// don't do events on text and comment nodes
			if ( elem.nodeType == 3 || elem.nodeType == 8 )
				return undefined;

			var val, ret, fn = jQuery.isFunction( elem[ type ] || null ),
				// Check to see if we need to provide a fake event, or not
				event = !data[0] || !data[0].preventDefault;
			
			// Pass along a fake event
			if ( event )
				data.unshift( this.fix({ type: type, target: elem }) );

			// Enforce the right trigger type
			data[0].type = type;
			if ( exclusive )
				data[0].exclusive = true;

			// Trigger the event
			if ( jQuery.isFunction( jQuery.data(elem, "handle") ) )
				val = jQuery.data(elem, "handle").apply( elem, data );

			// Handle triggering native .onfoo handlers
			if ( !fn && elem["on"+type] && elem["on"+type].apply( elem, data ) === false )
				val = false;

			// Extra functions don't get the custom event object
			if ( event )
				data.shift();

			// Handle triggering of extra function
			if ( extra && jQuery.isFunction( extra ) ) {
				// call the extra function and tack the current return value on the end for possible inspection
				ret = extra.apply( elem, val == null ? data : data.concat( val ) );
				// if anything is returned, give it precedence and have it overwrite the previous value
				if (ret !== undefined)
					val = ret;
			}

			// Trigger the native events (except for clicks on links)
			if ( fn && donative !== false && val !== false && !(jQuery.nodeName(elem, 'a') && type == "click") ) {
				this.triggered = true;
				try {
					elem[ type ]();
				// prevent IE from throwing an error for some hidden elements
				} catch (e) {}
			}

			this.triggered = false;
		}

		return val;
	},

	handle: function(event) {
		// returned undefined or false
		var val;

		// Empty object is for triggered events with no data
		event = jQuery.event.fix( event || window.event || {} ); 

		// Namespaced event handlers
		var parts = event.type.split(".");
		event.type = parts[0];

		var handlers = jQuery.data(this, "events") && jQuery.data(this, "events")[event.type], args = Array.prototype.slice.call( arguments, 1 );
		args.unshift( event );

		for ( var j in handlers ) {
			var handler = handlers[j];
			// Pass in a reference to the handler function itself
			// So that we can later remove it
			args[0].handler = handler;
			args[0].data = handler.data;

			// Filter the functions by class
			if ( !parts[1] && !event.exclusive || handler.type == parts[1] ) {
				var ret = handler.apply( this, args );

				if ( val !== false )
					val = ret;

				if ( ret === false ) {
					event.preventDefault();
					event.stopPropagation();
				}
			}
		}

		// Clean up added properties in IE to prevent memory leak
		if (jQuery.browser.msie)
			event.target = event.preventDefault = event.stopPropagation =
				event.handler = event.data = null;

		return val;
	},

	fix: function(event) {
		// store a copy of the original event object 
		// and clone to set read-only properties
		var originalEvent = event;
		event = jQuery.extend({}, originalEvent);
		event.originalEvent = originalEvent;
		
		// add preventDefault and stopPropagation since 
		// they will not work on the clone
		event.preventDefault = function() {
			// if preventDefault exists run it on the original event
			if (originalEvent.preventDefault)
				originalEvent.preventDefault();
			// otherwise set the returnValue property of the original event to false (IE)
			originalEvent.returnValue = false;
		};
		event.stopPropagation = function() {
			// if stopPropagation exists run it on the original event
			if (originalEvent.stopPropagation)
				originalEvent.stopPropagation();
			// otherwise set the cancelBubble property of the original event to true (IE)
			originalEvent.cancelBubble = true;
		};
		
		// Fix target property, if necessary
		if ( !event.target )
			event.target = event.srcElement || document; // Fixes #1925 where srcElement might not be defined either
				
		// check if target is a textnode (safari)
		if ( event.target.nodeType == 3 )
			event.target = originalEvent.target.parentNode;

		// Add relatedTarget, if necessary
		if ( !event.relatedTarget && event.fromElement )
			event.relatedTarget = event.fromElement == event.target ? event.toElement : event.fromElement;

		// Calculate pageX/Y if missing and clientX/Y available
		if ( event.pageX == null && event.clientX != null ) {
			var doc = document.documentElement, body = document.body;
			event.pageX = event.clientX + (doc && doc.scrollLeft || body && body.scrollLeft || 0) - (doc.clientLeft || 0);
			event.pageY = event.clientY + (doc && doc.scrollTop || body && body.scrollTop || 0) - (doc.clientTop || 0);
		}
			
		// Add which for key events
		if ( !event.which && ((event.charCode || event.charCode === 0) ? event.charCode : event.keyCode) )
			event.which = event.charCode || event.keyCode;
		
		// Add metaKey to non-Mac browsers (use ctrl for PC's and Meta for Macs)
		if ( !event.metaKey && event.ctrlKey )
			event.metaKey = event.ctrlKey;

		// Add which for click: 1 == left; 2 == middle; 3 == right
		// Note: button is not normalized, so don't use it
		if ( !event.which && event.button )
			event.which = (event.button & 1 ? 1 : ( event.button & 2 ? 3 : ( event.button & 4 ? 2 : 0 ) ));
			
		return event;
	},
	
	special: {
		ready: {
			setup: function() {
				// Make sure the ready event is setup
				bindReady();
				return;
			},
			
			teardown: function() { return; }
		},
		
		mouseenter: {
			setup: function() {
				if ( jQuery.browser.msie ) return false;
				jQuery(this).bind("mouseover", jQuery.event.special.mouseenter.handler);
				return true;
			},
		
			teardown: function() {
				if ( jQuery.browser.msie ) return false;
				jQuery(this).unbind("mouseover", jQuery.event.special.mouseenter.handler);
				return true;
			},
			
			handler: function(event) {
				// If we actually just moused on to a sub-element, ignore it
				if ( withinElement(event, this) ) return true;
				// Execute the right handlers by setting the event type to mouseenter
				arguments[0].type = "mouseenter";
				return jQuery.event.handle.apply(this, arguments);
			}
		},
	
		mouseleave: {
			setup: function() {
				if ( jQuery.browser.msie ) return false;
				jQuery(this).bind("mouseout", jQuery.event.special.mouseleave.handler);
				return true;
			},
		
			teardown: function() {
				if ( jQuery.browser.msie ) return false;
				jQuery(this).unbind("mouseout", jQuery.event.special.mouseleave.handler);
				return true;
			},
			
			handler: function(event) {
				// If we actually just moused on to a sub-element, ignore it
				if ( withinElement(event, this) ) return true;
				// Execute the right handlers by setting the event type to mouseleave
				arguments[0].type = "mouseleave";
				return jQuery.event.handle.apply(this, arguments);
			}
		}
	}
};

jQuery.fn.extend({
	bind: function( type, data, fn ) {
		return type == "unload" ? this.one(type, data, fn) : this.each(function(){
			jQuery.event.add( this, type, fn || data, fn && data );
		});
	},
	
	one: function( type, data, fn ) {
		return this.each(function(){
			jQuery.event.add( this, type, function(event) {
				jQuery(this).unbind(event);
				return (fn || data).apply( this, arguments);
			}, fn && data);
		});
	},

	unbind: function( type, fn ) {
		return this.each(function(){
			jQuery.event.remove( this, type, fn );
		});
	},

	trigger: function( type, data, fn ) {
		return this.each(function(){
			jQuery.event.trigger( type, data, this, true, fn );
		});
	},

	triggerHandler: function( type, data, fn ) {
		if ( this[0] )
			return jQuery.event.trigger( type, data, this[0], false, fn );
		return undefined;
	},

	toggle: function() {
		// Save reference to arguments for access in closure
		var args = arguments;

		return this.click(function(event) {
			// Figure out which function to execute
			this.lastToggle = 0 == this.lastToggle ? 1 : 0;
			
			// Make sure that clicks stop
			event.preventDefault();
			
			// and execute the function
			return args[this.lastToggle].apply( this, arguments ) || false;
		});
	},

	hover: function(fnOver, fnOut) {
		return this.bind('mouseenter', fnOver).bind('mouseleave', fnOut);
	},
	
	ready: function(fn) {
		// Attach the listeners
		bindReady();

		// If the DOM is already ready
		if ( jQuery.isReady )
			// Execute the function immediately
			fn.call( document, jQuery );
			
		// Otherwise, remember the function for later
		else
			// Add the function to the wait list
			jQuery.readyList.push( function() { return fn.call(this, jQuery); } );
	
		return this;
	}
});

jQuery.extend({
	isReady: false,
	readyList: [],
	// Handle when the DOM is ready
	ready: function() {
		// Make sure that the DOM is not already loaded
		if ( !jQuery.isReady ) {
			// Remember that the DOM is ready
			jQuery.isReady = true;
			
			// If there are functions bound, to execute
			if ( jQuery.readyList ) {
				// Execute all of them
				jQuery.each( jQuery.readyList, function(){
					this.apply( document );
				});
				
				// Reset the list of functions
				jQuery.readyList = null;
			}
		
			// Trigger any bound ready events
			jQuery(document).triggerHandler("ready");
		}
	}
});

var readyBound = false;

function bindReady(){
	if ( readyBound ) return;
	readyBound = true;

	// Mozilla, Opera (see further below for it) and webkit nightlies currently support this event
	if ( document.addEventListener && !jQuery.browser.opera)
		// Use the handy event callback
		document.addEventListener( "DOMContentLoaded", jQuery.ready, false );
	
	// If IE is used and is not in a frame
	// Continually check to see if the document is ready
	if ( jQuery.browser.msie && window == top ) (function(){
		if (jQuery.isReady) return;
		try {
			// If IE is used, use the trick by Diego Perini
			// http://javascript.nwbox.com/IEContentLoaded/
			document.documentElement.doScroll("left");
		} catch( error ) {
			setTimeout( arguments.callee, 0 );
			return;
		}
		// and execute any waiting functions
		jQuery.ready();
	})();

	if ( jQuery.browser.opera )
		document.addEventListener( "DOMContentLoaded", function () {
			if (jQuery.isReady) return;
			for (var i = 0; i < document.styleSheets.length; i++)
				if (document.styleSheets[i].disabled) {
					setTimeout( arguments.callee, 0 );
					return;
				}
			// and execute any waiting functions
			jQuery.ready();
		}, false);

	if ( jQuery.browser.safari ) {
		var numStyles;
		(function(){
			if (jQuery.isReady) return;
			if ( document.readyState != "loaded" && document.readyState != "complete" ) {
				setTimeout( arguments.callee, 0 );
				return;
			}
			if ( numStyles === undefined )
				numStyles = jQuery("style, link[rel=stylesheet]").length;
			if ( document.styleSheets.length != numStyles ) {
				setTimeout( arguments.callee, 0 );
				return;
			}
			// and execute any waiting functions
			jQuery.ready();
		})();
	}

	// A fallback to window.onload, that will always work
	jQuery.event.add( window, "load", jQuery.ready );
}

jQuery.each( ("blur,focus,load,resize,scroll,unload,click,dblclick," +
	"mousedown,mouseup,mousemove,mouseover,mouseout,change,select," + 
	"submit,keydown,keypress,keyup,error").split(","), function(i, name){
	
	// Handle event binding
	jQuery.fn[name] = function(fn){
		return fn ? this.bind(name, fn) : this.trigger(name);
	};
});

// Checks if an event happened on an element within another element
// Used in jQuery.event.special.mouseenter and mouseleave handlers
var withinElement = function(event, elem) {
	// Check if mouse(over|out) are still within the same parent element
	var parent = event.relatedTarget;
	// Traverse up the tree
	while ( parent && parent != elem ) try { parent = parent.parentNode; } catch(error) { parent = elem; }
	// Return true if we actually just moused on to a sub-element
	return parent == elem;
};

// Prevent memory leaks in IE
// And prevent errors on refresh with events like mouseover in other browsers
// Window isn't included so as not to unbind existing unload events
jQuery(window).bind("unload", function() {
	jQuery("*").add(document).unbind();
});
jQuery.fn.extend({
	load: function( url, params, callback ) {
		if ( jQuery.isFunction( url ) )
			return this.bind("load", url);

		var off = url.indexOf(" ");
		if ( off >= 0 ) {
			var selector = url.slice(off, url.length);
			url = url.slice(0, off);
		}

		callback = callback || function(){};

		// Default to a GET request
		var type = "GET";

		// If the second parameter was provided
		if ( params )
			// If it's a function
			if ( jQuery.isFunction( params ) ) {
				// We assume that it's the callback
				callback = params;
				params = null;

			// Otherwise, build a param string
			} else {
				params = jQuery.param( params );
				type = "POST";
			}

		var self = this;

		// Request the remote document
		jQuery.ajax({
			url: url,
			type: type,
			dataType: "html",
			data: params,
			complete: function(res, status){
				// If successful, inject the HTML into all the matched elements
				if ( status == "success" || status == "notmodified" )
					// See if a selector was specified
					self.html( selector ?
						// Create a dummy div to hold the results
						jQuery("<div/>")
							// inject the contents of the document in, removing the scripts
							// to avoid any 'Permission Denied' errors in IE
							.append(res.responseText.replace(/<script(.|\s)*?\/script>/g, ""))

							// Locate the specified elements
							.find(selector) :

						// If not, just inject the full result
						res.responseText );

				self.each( callback, [res.responseText, status, res] );
			}
		});
		return this;
	},

	serialize: function() {
		return jQuery.param(this.serializeArray());
	},
	serializeArray: function() {
		return this.map(function(){
			return jQuery.nodeName(this, "form") ?
				jQuery.makeArray(this.elements) : this;
		})
		.filter(function(){
			return this.name && !this.disabled && 
				(this.checked || /select|textarea/i.test(this.nodeName) || 
					/text|hidden|password/i.test(this.type));
		})
		.map(function(i, elem){
			var val = jQuery(this).val();
			return val == null ? null :
				val.constructor == Array ?
					jQuery.map( val, function(val, i){
						return {name: elem.name, value: val};
					}) :
					{name: elem.name, value: val};
		}).get();
	}
});

// Attach a bunch of functions for handling common AJAX events
jQuery.each( "ajaxStart,ajaxStop,ajaxComplete,ajaxError,ajaxSuccess,ajaxSend".split(","), function(i,o){
	jQuery.fn[o] = function(f){
		return this.bind(o, f);
	};
});

var jsc = (new Date).getTime();

jQuery.extend({
	get: function( url, data, callback, type ) {
		// shift arguments if data argument was ommited
		if ( jQuery.isFunction( data ) ) {
			callback = data;
			data = null;
		}
		
		return jQuery.ajax({
			type: "GET",
			url: url,
			data: data,
			success: callback,
			dataType: type
		});
	},

	getScript: function( url, callback ) {
		return jQuery.get(url, null, callback, "script");
	},

	getJSON: function( url, data, callback ) {
		return jQuery.get(url, data, callback, "json");
	},

	post: function( url, data, callback, type ) {
		if ( jQuery.isFunction( data ) ) {
			callback = data;
			data = {};
		}

		return jQuery.ajax({
			type: "POST",
			url: url,
			data: data,
			success: callback,
			dataType: type
		});
	},

	ajaxSetup: function( settings ) {
		jQuery.extend( jQuery.ajaxSettings, settings );
	},

	ajaxSettings: {
		global: true,
		type: "GET",
		timeout: 0,
		contentType: "application/x-www-form-urlencoded",
		processData: true,
		async: true,
		data: null,
		username: null,
		password: null,
		accepts: {
			xml: "application/xml, text/xml",
			html: "text/html",
			script: "text/javascript, application/javascript",
			json: "application/json, text/javascript",
			text: "text/plain",
			_default: "*/*"
		}
	},
	
	// Last-Modified header cache for next request
	lastModified: {},

	ajax: function( s ) {
		var jsonp, jsre = /=\?(&|$)/g, status, data;

		// Extend the settings, but re-extend 's' so that it can be
		// checked again later (in the test suite, specifically)
		s = jQuery.extend(true, s, jQuery.extend(true, {}, jQuery.ajaxSettings, s));

		// convert data if not already a string
		if ( s.data && s.processData && typeof s.data != "string" )
			s.data = jQuery.param(s.data);

		// Handle JSONP Parameter Callbacks
		if ( s.dataType == "jsonp" ) {
			if ( s.type.toLowerCase() == "get" ) {
				if ( !s.url.match(jsre) )
					s.url += (s.url.match(/\?/) ? "&" : "?") + (s.jsonp || "callback") + "=?";
			} else if ( !s.data || !s.data.match(jsre) )
				s.data = (s.data ? s.data + "&" : "") + (s.jsonp || "callback") + "=?";
			s.dataType = "json";
		}

		// Build temporary JSONP function
		if ( s.dataType == "json" && (s.data && s.data.match(jsre) || s.url.match(jsre)) ) {
			jsonp = "jsonp" + jsc++;

			// Replace the =? sequence both in the query string and the data
			if ( s.data )
				s.data = (s.data + "").replace(jsre, "=" + jsonp + "$1");
			s.url = s.url.replace(jsre, "=" + jsonp + "$1");

			// We need to make sure
			// that a JSONP style response is executed properly
			s.dataType = "script";

			// Handle JSONP-style loading
			window[ jsonp ] = function(tmp){
				data = tmp;
				success();
				complete();
				// Garbage collect
				window[ jsonp ] = undefined;
				try{ delete window[ jsonp ]; } catch(e){}
				if ( head )
					head.removeChild( script );
			};
		}

		if ( s.dataType == "script" && s.cache == null )
			s.cache = false;

		if ( s.cache === false && s.type.toLowerCase() == "get" ) {
			var ts = (new Date()).getTime();
			// try replacing _= if it is there
			var ret = s.url.replace(/(\?|&)_=.*?(&|$)/, "$1_=" + ts + "$2");
			// if nothing was replaced, add timestamp to the end
			s.url = ret + ((ret == s.url) ? (s.url.match(/\?/) ? "&" : "?") + "_=" + ts : "");
		}

		// If data is available, append data to url for get requests
		if ( s.data && s.type.toLowerCase() == "get" ) {
			s.url += (s.url.match(/\?/) ? "&" : "?") + s.data;

			// IE likes to send both get and post data, prevent this
			s.data = null;
		}

		// Watch for a new set of requests
		if ( s.global && ! jQuery.active++ )
			jQuery.event.trigger( "ajaxStart" );

		// If we're requesting a remote document
		// and trying to load JSON or Script with a GET
		if ( (!s.url.indexOf("http") || !s.url.indexOf("//")) && s.dataType == "script" && s.type.toLowerCase() == "get" ) {
			var head = document.getElementsByTagName("head")[0];
			var script = document.createElement("script");
			script.src = s.url;
			if (s.scriptCharset)
				script.charset = s.scriptCharset;

			// Handle Script loading
			if ( !jsonp ) {
				var done = false;

				// Attach handlers for all browsers
				script.onload = script.onreadystatechange = function(){
					if ( !done && (!this.readyState || 
							this.readyState == "loaded" || this.readyState == "complete") ) {
						done = true;
						success();
						complete();
						head.removeChild( script );
					}
				};
			}

			head.appendChild(script);

			// We handle everything using the script element injection
			return undefined;
		}

		var requestDone = false;

		// Create the request object; Microsoft failed to properly
		// implement the XMLHttpRequest in IE7, so we use the ActiveXObject when it is available
		var xml = window.ActiveXObject ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();

		// Open the socket
		xml.open(s.type, s.url, s.async, s.username, s.password);

		// Need an extra try/catch for cross domain requests in Firefox 3
		try {
			// Set the correct header, if data is being sent
			if ( s.data )
				xml.setRequestHeader("Content-Type", s.contentType);

			// Set the If-Modified-Since header, if ifModified mode.
			if ( s.ifModified )
				xml.setRequestHeader("If-Modified-Since",
					jQuery.lastModified[s.url] || "Thu, 01 Jan 1970 00:00:00 GMT" );

			// Set header so the called script knows that it's an XMLHttpRequest
			xml.setRequestHeader("X-Requested-With", "XMLHttpRequest");

			// Set the Accepts header for the server, depending on the dataType
			xml.setRequestHeader("Accept", s.dataType && s.accepts[ s.dataType ] ?
				s.accepts[ s.dataType ] + ", */*" :
				s.accepts._default );
		} catch(e){}

		// Allow custom headers/mimetypes
		if ( s.beforeSend )
			s.beforeSend(xml);
			
		if ( s.global )
			jQuery.event.trigger("ajaxSend", [xml, s]);

		// Wait for a response to come back
		var onreadystatechange = function(isTimeout){
			// The transfer is complete and the data is available, or the request timed out
			if ( !requestDone && xml && (xml.readyState == 4 || isTimeout == "timeout") ) {
				requestDone = true;
				
				// clear poll interval
				if (ival) {
					clearInterval(ival);
					ival = null;
				}
				
				status = isTimeout == "timeout" && "timeout" ||
					!jQuery.httpSuccess( xml ) && "error" ||
					s.ifModified && jQuery.httpNotModified( xml, s.url ) && "notmodified" ||
					"success";

				if ( status == "success" ) {
					// Watch for, and catch, XML document parse errors
					try {
						// process the data (runs the xml through httpData regardless of callback)
						data = jQuery.httpData( xml, s.dataType );
					} catch(e) {
						status = "parsererror";
					}
				}

				// Make sure that the request was successful or notmodified
				if ( status == "success" ) {
					// Cache Last-Modified header, if ifModified mode.
					var modRes;
					try {
						modRes = xml.getResponseHeader("Last-Modified");
					} catch(e) {} // swallow exception thrown by FF if header is not available
	
					if ( s.ifModified && modRes )
						jQuery.lastModified[s.url] = modRes;

					// JSONP handles its own success callback
					if ( !jsonp )
						success();	
				} else
					jQuery.handleError(s, xml, status);

				// Fire the complete handlers
				complete();

				// Stop memory leaks
				if ( s.async )
					xml = null;
			}
		};
		
		if ( s.async ) {
			// don't attach the handler to the request, just poll it instead
			var ival = setInterval(onreadystatechange, 13); 

			// Timeout checker
			if ( s.timeout > 0 )
				setTimeout(function(){
					// Check to see if the request is still happening
					if ( xml ) {
						// Cancel the request
						xml.abort();
	
						if( !requestDone )
							onreadystatechange( "timeout" );
					}
				}, s.timeout);
		}
			
		// Send the data
		try {
			xml.send(s.data);
		} catch(e) {
			jQuery.handleError(s, xml, null, e);
		}
		
		// firefox 1.5 doesn't fire statechange for sync requests
		if ( !s.async )
			onreadystatechange();

		function success(){
			// If a local callback was specified, fire it and pass it the data
			if ( s.success )
				s.success( data, status );

			// Fire the global callback
			if ( s.global )
				jQuery.event.trigger( "ajaxSuccess", [xml, s] );
		}

		function complete(){
			// Process result
			if ( s.complete )
				s.complete(xml, status);

			// The request was completed
			if ( s.global )
				jQuery.event.trigger( "ajaxComplete", [xml, s] );

			// Handle the global AJAX counter
			if ( s.global && ! --jQuery.active )
				jQuery.event.trigger( "ajaxStop" );
		}
		
		// return XMLHttpRequest to allow aborting the request etc.
		return xml;
	},

	handleError: function( s, xml, status, e ) {
		// If a local callback was specified, fire it
		if ( s.error ) s.error( xml, status, e );

		// Fire the global callback
		if ( s.global )
			jQuery.event.trigger( "ajaxError", [xml, s, e] );
	},

	// Counter for holding the number of active queries
	active: 0,

	// Determines if an XMLHttpRequest was successful or not
	httpSuccess: function( r ) {
		try {
			// IE error sometimes returns 1223 when it should be 204 so treat it as success, see #1450
			return !r.status && location.protocol == "file:" ||
				( r.status >= 200 && r.status < 300 ) || r.status == 304 || r.status == 1223 ||
				jQuery.browser.safari && r.status == undefined;
		} catch(e){}
		return false;
	},

	// Determines if an XMLHttpRequest returns NotModified
	httpNotModified: function( xml, url ) {
		try {
			var xmlRes = xml.getResponseHeader("Last-Modified");

			// Firefox always returns 200. check Last-Modified date
			return xml.status == 304 || xmlRes == jQuery.lastModified[url] ||
				jQuery.browser.safari && xml.status == undefined;
		} catch(e){}
		return false;
	},

	httpData: function( r, type ) {
		var ct = r.getResponseHeader("content-type");
		var xml = type == "xml" || !type && ct && ct.indexOf("xml") >= 0;
		var data = xml ? r.responseXML : r.responseText;

		if ( xml && data.documentElement.tagName == "parsererror" )
			throw "parsererror";

		// If the type is "script", eval it in global context
		if ( type == "script" )
			jQuery.globalEval( data );

		// Get the JavaScript object, if JSON is used.
		if ( type == "json" )
			data = eval("(" + data + ")");

		return data;
	},

	// Serialize an array of form elements or a set of
	// key/values into a query string
	param: function( a ) {
		var s = [];

		// If an array was passed in, assume that it is an array
		// of form elements
		if ( a.constructor == Array || a.jquery )
			// Serialize the form elements
			jQuery.each( a, function(){
				s.push( encodeURIComponent(this.name) + "=" + encodeURIComponent( this.value ) );
			});

		// Otherwise, assume that it's an object of key/value pairs
		else
			// Serialize the key/values
			for ( var j in a )
				// If the value is an array then the key names need to be repeated
				if ( a[j] && a[j].constructor == Array )
					jQuery.each( a[j], function(){
						s.push( encodeURIComponent(j) + "=" + encodeURIComponent( this ) );
					});
				else
					s.push( encodeURIComponent(j) + "=" + encodeURIComponent( a[j] ) );

		// Return the resulting serialization
		return s.join("&").replace(/%20/g, "+");
	}

});
jQuery.fn.extend({
	show: function(speed,callback){
		return speed ?
			this.animate({
				height: "show", width: "show", opacity: "show"
			}, speed, callback) :
			
			this.filter(":hidden").each(function(){
				this.style.display = this.oldblock || "";
				if ( jQuery.css(this,"display") == "none" ) {
					var elem = jQuery("<" + this.tagName + " />").appendTo("body");
					this.style.display = elem.css("display");
					// handle an edge condition where css is - div { display:none; } or similar
					if (this.style.display == "none")
						this.style.display = "block";
					elem.remove();
				}
			}).end();
	},
	
	hide: function(speed,callback){
		return speed ?
			this.animate({
				height: "hide", width: "hide", opacity: "hide"
			}, speed, callback) :
			
			this.filter(":visible").each(function(){
				this.oldblock = this.oldblock || jQuery.css(this,"display");
				this.style.display = "none";
			}).end();
	},

	// Save the old toggle function
	_toggle: jQuery.fn.toggle,
	
	toggle: function( fn, fn2 ){
		return jQuery.isFunction(fn) && jQuery.isFunction(fn2) ?
			this._toggle( fn, fn2 ) :
			fn ?
				this.animate({
					height: "toggle", width: "toggle", opacity: "toggle"
				}, fn, fn2) :
				this.each(function(){
					jQuery(this)[ jQuery(this).is(":hidden") ? "show" : "hide" ]();
				});
	},
	
	slideDown: function(speed,callback){
		return this.animate({height: "show"}, speed, callback);
	},
	
	slideUp: function(speed,callback){
		return this.animate({height: "hide"}, speed, callback);
	},

	slideToggle: function(speed, callback){
		return this.animate({height: "toggle"}, speed, callback);
	},
	
	fadeIn: function(speed, callback){
		return this.animate({opacity: "show"}, speed, callback);
	},
	
	fadeOut: function(speed, callback){
		return this.animate({opacity: "hide"}, speed, callback);
	},
	
	fadeTo: function(speed,to,callback){
		return this.animate({opacity: to}, speed, callback);
	},
	
	animate: function( prop, speed, easing, callback ) {
		var optall = jQuery.speed(speed, easing, callback);

		return this[ optall.queue === false ? "each" : "queue" ](function(){
			if ( this.nodeType != 1)
				return false;

			var opt = jQuery.extend({}, optall);
			var hidden = jQuery(this).is(":hidden"), self = this;
			
			for ( var p in prop ) {
				if ( prop[p] == "hide" && hidden || prop[p] == "show" && !hidden )
					return jQuery.isFunction(opt.complete) && opt.complete.apply(this);

				if ( p == "height" || p == "width" ) {
					// Store display property
					opt.display = jQuery.css(this, "display");

					// Make sure that nothing sneaks out
					opt.overflow = this.style.overflow;
				}
			}

			if ( opt.overflow != null )
				this.style.overflow = "hidden";

			opt.curAnim = jQuery.extend({}, prop);
			
			jQuery.each( prop, function(name, val){
				var e = new jQuery.fx( self, opt, name );

				if ( /toggle|show|hide/.test(val) )
					e[ val == "toggle" ? hidden ? "show" : "hide" : val ]( prop );
				else {
					var parts = val.toString().match(/^([+-]=)?([\d+-.]+)(.*)$/),
						start = e.cur(true) || 0;

					if ( parts ) {
						var end = parseFloat(parts[2]),
							unit = parts[3] || "px";

						// We need to compute starting value
						if ( unit != "px" ) {
							self.style[ name ] = (end || 1) + unit;
							start = ((end || 1) / e.cur(true)) * start;
							self.style[ name ] = start + unit;
						}

						// If a +=/-= token was provided, we're doing a relative animation
						if ( parts[1] )
							end = ((parts[1] == "-=" ? -1 : 1) * end) + start;

						e.custom( start, end, unit );
					} else
						e.custom( start, val, "" );
				}
			});

			// For JS strict compliance
			return true;
		});
	},
	
	queue: function(type, fn){
		if ( jQuery.isFunction(type) || ( type && type.constructor == Array )) {
			fn = type;
			type = "fx";
		}

		if ( !type || (typeof type == "string" && !fn) )
			return queue( this[0], type );

		return this.each(function(){
			if ( fn.constructor == Array )
				queue(this, type, fn);
			else {
				queue(this, type).push( fn );
			
				if ( queue(this, type).length == 1 )
					fn.apply(this);
			}
		});
	},

	stop: function(clearQueue, gotoEnd){
		var timers = jQuery.timers;

		if (clearQueue)
			this.queue([]);

		this.each(function(){
			// go in reverse order so anything added to the queue during the loop is ignored
			for ( var i = timers.length - 1; i >= 0; i-- )
				if ( timers[i].elem == this ) {
					if (gotoEnd)
						// force the next step to be the last
						timers[i](true);
					timers.splice(i, 1);
				}
		});

		// start the next in the queue if the last step wasn't forced
		if (!gotoEnd)
			this.dequeue();

		return this;
	}

});

var queue = function( elem, type, array ) {
	if ( !elem )
		return undefined;

	type = type || "fx";

	var q = jQuery.data( elem, type + "queue" );

	if ( !q || array )
		q = jQuery.data( elem, type + "queue", 
			array ? jQuery.makeArray(array) : [] );

	return q;
};

jQuery.fn.dequeue = function(type){
	type = type || "fx";

	return this.each(function(){
		var q = queue(this, type);

		q.shift();

		if ( q.length )
			q[0].apply( this );
	});
};

jQuery.extend({
	
	speed: function(speed, easing, fn) {
		var opt = speed && speed.constructor == Object ? speed : {
			complete: fn || !fn && easing || 
				jQuery.isFunction( speed ) && speed,
			duration: speed,
			easing: fn && easing || easing && easing.constructor != Function && easing
		};

		opt.duration = (opt.duration && opt.duration.constructor == Number ? 
			opt.duration : 
			{ slow: 600, fast: 200 }[opt.duration]) || 400;
	
		// Queueing
		opt.old = opt.complete;
		opt.complete = function(){
			if ( opt.queue !== false )
				jQuery(this).dequeue();
			if ( jQuery.isFunction( opt.old ) )
				opt.old.apply( this );
		};
	
		return opt;
	},
	
	easing: {
		linear: function( p, n, firstNum, diff ) {
			return firstNum + diff * p;
		},
		swing: function( p, n, firstNum, diff ) {
			return ((-Math.cos(p*Math.PI)/2) + 0.5) * diff + firstNum;
		}
	},
	
	timers: [],
	timerId: null,

	fx: function( elem, options, prop ){
		this.options = options;
		this.elem = elem;
		this.prop = prop;

		if ( !options.orig )
			options.orig = {};
	}

});

jQuery.fx.prototype = {

	// Simple function for setting a style value
	update: function(){
		if ( this.options.step )
			this.options.step.apply( this.elem, [ this.now, this ] );

		(jQuery.fx.step[this.prop] || jQuery.fx.step._default)( this );

		// Set display property to block for height/width animations
		if ( this.prop == "height" || this.prop == "width" )
			this.elem.style.display = "block";
	},

	// Get the current size
	cur: function(force){
		if ( this.elem[this.prop] != null && this.elem.style[this.prop] == null )
			return this.elem[ this.prop ];

		var r = parseFloat(jQuery.css(this.elem, this.prop, force));
		return r && r > -10000 ? r : parseFloat(jQuery.curCSS(this.elem, this.prop)) || 0;
	},

	// Start an animation from one number to another
	custom: function(from, to, unit){
		this.startTime = (new Date()).getTime();
		this.start = from;
		this.end = to;
		this.unit = unit || this.unit || "px";
		this.now = this.start;
		this.pos = this.state = 0;
		this.update();

		var self = this;
		function t(gotoEnd){
			return self.step(gotoEnd);
		}

		t.elem = this.elem;

		jQuery.timers.push(t);

		if ( jQuery.timerId == null ) {
			jQuery.timerId = setInterval(function(){
				var timers = jQuery.timers;
				
				for ( var i = 0; i < timers.length; i++ )
					if ( !timers[i]() )
						timers.splice(i--, 1);

				if ( !timers.length ) {
					clearInterval( jQuery.timerId );
					jQuery.timerId = null;
				}
			}, 13);
		}
	},

	// Simple 'show' function
	show: function(){
		// Remember where we started, so that we can go back to it later
		this.options.orig[this.prop] = jQuery.attr( this.elem.style, this.prop );
		this.options.show = true;

		// Begin the animation
		this.custom(0, this.cur());

		// Make sure that we start at a small width/height to avoid any
		// flash of content
		if ( this.prop == "width" || this.prop == "height" )
			this.elem.style[this.prop] = "1px";
		
		// Start by showing the element
		jQuery(this.elem).show();
	},

	// Simple 'hide' function
	hide: function(){
		// Remember where we started, so that we can go back to it later
		this.options.orig[this.prop] = jQuery.attr( this.elem.style, this.prop );
		this.options.hide = true;

		// Begin the animation
		this.custom(this.cur(), 0);
	},

	// Each step of an animation
	step: function(gotoEnd){
		var t = (new Date()).getTime();

		if ( gotoEnd || t > this.options.duration + this.startTime ) {
			this.now = this.end;
			this.pos = this.state = 1;
			this.update();

			this.options.curAnim[ this.prop ] = true;

			var done = true;
			for ( var i in this.options.curAnim )
				if ( this.options.curAnim[i] !== true )
					done = false;

			if ( done ) {
				if ( this.options.display != null ) {
					// Reset the overflow
					this.elem.style.overflow = this.options.overflow;
				
					// Reset the display
					this.elem.style.display = this.options.display;
					if ( jQuery.css(this.elem, "display") == "none" )
						this.elem.style.display = "block";
				}

				// Hide the element if the "hide" operation was done
				if ( this.options.hide )
					this.elem.style.display = "none";

				// Reset the properties, if the item has been hidden or shown
				if ( this.options.hide || this.options.show )
					for ( var p in this.options.curAnim )
						jQuery.attr(this.elem.style, p, this.options.orig[p]);
			}

			// If a callback was provided, execute it
			if ( done && jQuery.isFunction( this.options.complete ) )
				// Execute the complete function
				this.options.complete.apply( this.elem );

			return false;
		} else {
			var n = t - this.startTime;
			this.state = n / this.options.duration;

			// Perform the easing function, defaults to swing
			this.pos = jQuery.easing[this.options.easing || (jQuery.easing.swing ? "swing" : "linear")](this.state, n, 0, 1, this.options.duration);
			this.now = this.start + ((this.end - this.start) * this.pos);

			// Perform the next step of the animation
			this.update();
		}

		return true;
	}

};

jQuery.fx.step = {
	scrollLeft: function(fx){
		fx.elem.scrollLeft = fx.now;
	},

	scrollTop: function(fx){
		fx.elem.scrollTop = fx.now;
	},

	opacity: function(fx){
		jQuery.attr(fx.elem.style, "opacity", fx.now);
	},

	_default: function(fx){
		fx.elem.style[ fx.prop ] = fx.now + fx.unit;
	}
};
// The Offset Method
// Originally By Brandon Aaron, part of the Dimension Plugin
// http://jquery.com/plugins/project/dimensions
jQuery.fn.offset = function() {
	var left = 0, top = 0, elem = this[0], results;
	
	if ( elem ) with ( jQuery.browser ) {
		var parent       = elem.parentNode, 
		    offsetChild  = elem,
		    offsetParent = elem.offsetParent, 
		    doc          = elem.ownerDocument,
		    safari2      = safari && parseInt(version) < 522 && !/adobeair/i.test(userAgent),
		    fixed        = jQuery.css(elem, "position") == "fixed";
	
		// Use getBoundingClientRect if available
		if ( elem.getBoundingClientRect ) {
			var box = elem.getBoundingClientRect();
		
			// Add the document scroll offsets
			add(box.left + Math.max(doc.documentElement.scrollLeft, doc.body.scrollLeft),
				box.top  + Math.max(doc.documentElement.scrollTop,  doc.body.scrollTop));
		
			// IE adds the HTML element's border, by default it is medium which is 2px
			// IE 6 and 7 quirks mode the border width is overwritable by the following css html { border: 0; }
			// IE 7 standards mode, the border is always 2px
			// This border/offset is typically represented by the clientLeft and clientTop properties
			// However, in IE6 and 7 quirks mode the clientLeft and clientTop properties are not updated when overwriting it via CSS
			// Therefore this method will be off by 2px in IE while in quirksmode
			add( -doc.documentElement.clientLeft, -doc.documentElement.clientTop );
	
		// Otherwise loop through the offsetParents and parentNodes
		} else {
		
			// Initial element offsets
			add( elem.offsetLeft, elem.offsetTop );
			
			// Get parent offsets
			while ( offsetParent ) {
				// Add offsetParent offsets
				add( offsetParent.offsetLeft, offsetParent.offsetTop );
			
				// Mozilla and Safari > 2 does not include the border on offset parents
				// However Mozilla adds the border for table or table cells
				if ( mozilla && !/^t(able|d|h)$/i.test(offsetParent.tagName) || safari && !safari2 )
					border( offsetParent );
					
				// Add the document scroll offsets if position is fixed on any offsetParent
				if ( !fixed && jQuery.css(offsetParent, "position") == "fixed" )
					fixed = true;
			
				// Set offsetChild to previous offsetParent unless it is the body element
				offsetChild  = /^body$/i.test(offsetParent.tagName) ? offsetChild : offsetParent;
				// Get next offsetParent
				offsetParent = offsetParent.offsetParent;
			}
		
			// Get parent scroll offsets
			while ( parent && parent.tagName && !/^body|html$/i.test(parent.tagName) ) {
				// Remove parent scroll UNLESS that parent is inline or a table to work around Opera inline/table scrollLeft/Top bug
				if ( !/^inline|table.*$/i.test(jQuery.css(parent, "display")) )
					// Subtract parent scroll offsets
					add( -parent.scrollLeft, -parent.scrollTop );
			
				// Mozilla does not add the border for a parent that has overflow != visible
				if ( mozilla && jQuery.css(parent, "overflow") != "visible" )
					border( parent );
			
				// Get next parent
				parent = parent.parentNode;
			}
		
			// Safari <= 2 doubles body offsets with a fixed position element/offsetParent or absolutely positioned offsetChild
			// Mozilla doubles body offsets with a non-absolutely positioned offsetChild
			if ( (safari2 && (fixed || jQuery.css(offsetChild, "position") == "absolute")) || 
				(mozilla && jQuery.css(offsetChild, "position") != "absolute") )
					add( -doc.body.offsetLeft, -doc.body.offsetTop );
			
			// Add the document scroll offsets if position is fixed
			if ( fixed )
				add(Math.max(doc.documentElement.scrollLeft, doc.body.scrollLeft),
					Math.max(doc.documentElement.scrollTop,  doc.body.scrollTop));
		}

		// Return an object with top and left properties
		results = { top: top, left: left };
	}

	function border(elem) {
		add( jQuery.curCSS(elem, "borderLeftWidth", true), jQuery.curCSS(elem, "borderTopWidth", true) );
	}

	function add(l, t) {
		left += parseInt(l) || 0;
		top += parseInt(t) || 0;
	}

	return results;
};
})();
 /* Copyright (c) 2007 Paul Bakaus (paul.bakaus@googlemail.com) and Brandon Aaron (brandon.aaron@gmail.com || http://brandonaaron.net)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 *
 * $LastChangedDate$
 * $Rev$
 *
 * Version: @VERSION
 *
 * Requires: jQuery 1.2+
 */

(function($){
	
$.dimensions = {
	version: '@VERSION'
};

// Create innerHeight, innerWidth, outerHeight and outerWidth methods
$.each( [ 'Height', 'Width' ], function(i, name){
	
	// innerHeight and innerWidth
	$.fn[ 'inner' + name ] = function() {
		if (!this[0]) return;
		
		var torl = name == 'Height' ? 'Top'    : 'Left',  // top or left
		    borr = name == 'Height' ? 'Bottom' : 'Right'; // bottom or right
		
		return this.css( name.toLowerCase() ) + num(this, 'padding' + torl) + num(this, 'padding' + borr);
	};
	
	// outerHeight and outerWidth
	$.fn[ 'outer' + name ] = function(options) {
		if (!this[0]) return;
		
		var torl = name == 'Height' ? 'Top'    : 'Left',  // top or left
		    borr = name == 'Height' ? 'Bottom' : 'Right'; // bottom or right
		
		options = $.extend({ margin: false }, options || {});
		
		return this.css( name.toLowerCase() )
				+ num(this, 'border' + torl + 'Width') + num(this, 'border' + borr + 'Width')
				+ num(this, 'padding' + torl) + num(this, 'padding' + borr)
				+ (options.margin ? (num(this, 'margin' + torl) + num(this, 'margin' + borr)) : 0);
	};
});

// Create scrollLeft and scrollTop methods
$.each( ['Left', 'Top'], function(i, name) {
	$.fn[ 'scroll' + name ] = function(val) {
		if (!this[0]) return;
		
		return val != undefined ?
		
			// Set the scroll offset
			this.each(function() {
				this == window || this == document ?
					window.scrollTo( 
						name == 'Left' ? val : $(window)[ 'scrollLeft' ](),
						name == 'Top'  ? val : $(window)[ 'scrollTop'  ]()
					) :
					this[ 'scroll' + name ] = val;
			}) :
			
			// Return the scroll offset
			this[0] == window || this[0] == document ?
				self[ (name == 'Left' ? 'pageXOffset' : 'pageYOffset') ] ||
					$.boxModel && document.documentElement[ 'scroll' + name ] ||
					document.body[ 'scroll' + name ] :
				this[0][ 'scroll' + name ];
	};
});

$.fn.extend({
	position: function() {
		var left = 0, top = 0, elem = this[0], offset, parentOffset, offsetParent, results;
		
		if (elem) {
			// Get *real* offsetParent
			offsetParent = this.offsetParent();
			
			// Get correct offsets
			offset       = this.offset();
			parentOffset = offsetParent.offset();
			
			// Subtract element margins
			offset.top  -= num(elem, 'marginTop');
			offset.left -= num(elem, 'marginLeft');
			
			// Add offsetParent borders
			parentOffset.top  += num(offsetParent, 'borderTopWidth');
			parentOffset.left += num(offsetParent, 'borderLeftWidth');
			
			// Subtract the two offsets
			results = {
				top:  offset.top  - parentOffset.top,
				left: offset.left - parentOffset.left
			};
		}
		
		return results;
	},
	
	offsetParent: function() {
		var offsetParent = this[0].offsetParent;
		while ( offsetParent && (!/^body|html$/i.test(offsetParent.tagName) && $.css(offsetParent, 'position') == 'static') )
			offsetParent = offsetParent.offsetParent;
		return $(offsetParent);
	}
});

function num(el, prop) {
	return parseInt($.css(el.jquery?el[0]:el,prop))||0;
};

})(jQuery); /*
 * jQuery Form Plugin
 * @requires jQuery v1.1 or later
 *
 * Examples at: http://malsup.com/jquery/form/
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * Revision: $Id$
 */
 (function($) {
/**
 * ajaxSubmit() provides a mechanism for submitting an HTML form using AJAX.
 *
 * ajaxSubmit accepts a single argument which can be either a success callback function
 * or an options Object.  If a function is provided it will be invoked upon successful
 * completion of the submit and will be passed the response from the server.
 * If an options Object is provided, the following attributes are supported:
 *
 *  target:   Identifies the element(s) in the page to be updated with the server response.
 *            This value may be specified as a jQuery selection string, a jQuery object,
 *            or a DOM element.
 *            default value: null
 *
 *  url:      URL to which the form data will be submitted.
 *            default value: value of form's 'action' attribute
 *
 *  type:     The method in which the form data should be submitted, 'GET' or 'POST'.
 *            default value: value of form's 'method' attribute (or 'GET' if none found)
 *
 *  data:     Additional data to add to the request, specified as key/value pairs (see $.ajax).
 *
 *  beforeSubmit:  Callback method to be invoked before the form is submitted.
 *            default value: null
 *
 *  success:  Callback method to be invoked after the form has been successfully submitted
 *            and the response has been returned from the server
 *            default value: null
 *
 *  dataType: Expected dataType of the response.  One of: null, 'xml', 'script', or 'json'
 *            default value: null
 *
 *  semantic: Boolean flag indicating whether data must be submitted in semantic order (slower).
 *            default value: false
 *
 *  resetForm: Boolean flag indicating whether the form should be reset if the submit is successful
 *
 *  clearForm: Boolean flag indicating whether the form should be cleared if the submit is successful
 *
 *
 * The 'beforeSubmit' callback can be provided as a hook for running pre-submit logic or for
 * validating the form data.  If the 'beforeSubmit' callback returns false then the form will
 * not be submitted. The 'beforeSubmit' callback is invoked with three arguments: the form data
 * in array format, the jQuery object, and the options object passed into ajaxSubmit.
 * The form data array takes the following form:
 *
 *     [ { name: 'username', value: 'jresig' }, { name: 'password', value: 'secret' } ]
 *
 * If a 'success' callback method is provided it is invoked after the response has been returned
 * from the server.  It is passed the responseText or responseXML value (depending on dataType).
 * See jQuery.ajax for further details.
 *
 *
 * The dataType option provides a means for specifying how the server response should be handled.
 * This maps directly to the jQuery.httpData method.  The following values are supported:
 *
 *      'xml':    if dataType == 'xml' the server response is treated as XML and the 'success'
 *                   callback method, if specified, will be passed the responseXML value
 *      'json':   if dataType == 'json' the server response will be evaluted and passed to
 *                   the 'success' callback, if specified
 *      'script': if dataType == 'script' the server response is evaluated in the global context
 *
 *
 * Note that it does not make sense to use both the 'target' and 'dataType' options.  If both
 * are provided the target will be ignored.
 *
 * The semantic argument can be used to force form serialization in semantic order.
 * This is normally true anyway, unless the form contains input elements of type='image'.
 * If your form must be submitted with name/value pairs in semantic order and your form
 * contains an input of type='image" then pass true for this arg, otherwise pass false
 * (or nothing) to avoid the overhead for this logic.
 *
 *
 * When used on its own, ajaxSubmit() is typically bound to a form's submit event like this:
 *
 * $("#form-id").submit(function() {
 *     $(this).ajaxSubmit(options);
 *     return false; // cancel conventional submit
 * });
 *
 * When using ajaxForm(), however, this is done for you.
 *
 * @example
 * $('#myForm').ajaxSubmit(function(data) {
 *     alert('Form submit succeeded! Server returned: ' + data);
 * });
 * @desc Submit form and alert server response
 *
 *
 * @example
 * var options = {
 *     target: '#myTargetDiv'
 * };
 * $('#myForm').ajaxSubmit(options);
 * @desc Submit form and update page element with server response
 *
 *
 * @example
 * var options = {
 *     success: function(responseText) {
 *         alert(responseText);
 *     }
 * };
 * $('#myForm').ajaxSubmit(options);
 * @desc Submit form and alert the server response
 *
 *
 * @example
 * var options = {
 *     beforeSubmit: function(formArray, jqForm) {
 *         if (formArray.length == 0) {
 *             alert('Please enter data.');
 *             return false;
 *         }
 *     }
 * };
 * $('#myForm').ajaxSubmit(options);
 * @desc Pre-submit validation which aborts the submit operation if form data is empty
 *
 *
 * @example
 * var options = {
 *     url: myJsonUrl.php,
 *     dataType: 'json',
 *     success: function(data) {
 *        // 'data' is an object representing the the evaluated json data
 *     }
 * };
 * $('#myForm').ajaxSubmit(options);
 * @desc json data returned and evaluated
 *
 *
 * @example
 * var options = {
 *     url: myXmlUrl.php,
 *     dataType: 'xml',
 *     success: function(responseXML) {
 *        // responseXML is XML document object
 *        var data = $('myElement', responseXML).text();
 *     }
 * };
 * $('#myForm').ajaxSubmit(options);
 * @desc XML data returned from server
 *
 *
 * @example
 * var options = {
 *     resetForm: true
 * };
 * $('#myForm').ajaxSubmit(options);
 * @desc submit form and reset it if successful
 *
 * @example
 * $('#myForm).submit(function() {
 *    $(this).ajaxSubmit();
 *    return false;
 * });
 * @desc Bind form's submit event to use ajaxSubmit
 *
 *
 * @name ajaxSubmit
 * @type jQuery
 * @param options  object literal containing options which control the form submission process
 * @cat Plugins/Form
 * @return jQuery
 */
$.fn.ajaxSubmit = function(options) {
    if (typeof options == 'function')
        options = { success: options };

    options = $.extend({
        url:  this.attr('action') || window.location,
        type: this.attr('method') || 'GET'
    }, options || {});

    // hook for manipulating the form data before it is extracted;
    // convenient for use with rich editors like tinyMCE or FCKEditor
    var veto = {};
    $.event.trigger('form.pre.serialize', [this, options, veto]);
    if (veto.veto) return this;

    var a = this.formToArray(options.semantic);
	if (options.data) {
	    for (var n in options.data)
	        a.push( { name: n, value: options.data[n] } );
	}

    // give pre-submit callback an opportunity to abort the submit
    if (options.beforeSubmit && options.beforeSubmit(a, this, options) === false) return this;

    // fire vetoable 'validate' event
    $.event.trigger('form.submit.validate', [a, this, options, veto]);
    if (veto.veto) return this;

    var q = $.param(a);//.replace(/%20/g,'+');

    if (options.type.toUpperCase() == 'GET') {
        options.url += (options.url.indexOf('?') >= 0 ? '&' : '?') + q;
        options.data = null;  // data is null for 'get'
    }
    else
        options.data = q; // data is the query string for 'post'

    var $form = this, callbacks = [];
    if (options.resetForm) callbacks.push(function() { $form.resetForm(); });
    if (options.clearForm) callbacks.push(function() { $form.clearForm(); });

    // perform a load on the target only if dataType is not provided
    if (!options.dataType && options.target) {
        var oldSuccess = options.success || function(){};
        callbacks.push(function(data) {
            if (this.evalScripts)
                $(options.target).attr("innerHTML", data).evalScripts().each(oldSuccess, arguments);
            else // jQuery v1.1.4
                $(options.target).html(data).each(oldSuccess, arguments);
        });
    }
    else if (options.success)
        callbacks.push(options.success);

    options.success = function(data, status) {
        for (var i=0, max=callbacks.length; i < max; i++)
            callbacks[i](data, status, $form);
    };

    // are there files to upload?
    var files = $('input:file', this).fieldValue();
    var found = false;
    for (var j=0; j < files.length; j++)
        if (files[j])
            found = true;

    if (options.iframe || found) // options.iframe allows user to force iframe mode
        fileUpload();
    else
        $.ajax(options);

    // fire 'notify' event
    $.event.trigger('form.submit.notify', [this, options]);
    return this;


    // private function for handling file uploads (hat tip to YAHOO!)
    function fileUpload() {
        var form = $form[0];
        var opts = $.extend({}, $.ajaxSettings, options);

        var id = 'jqFormIO' + $.fn.ajaxSubmit.counter++;
        var $io = $('<iframe id="' + id + '" name="' + id + '" />');
        var io = $io[0];
        var op8 = $.browser.opera && window.opera.version() < 9;
        if ($.browser.msie || op8) io.src = 'javascript:false;document.write("");';
        $io.css({ position: 'absolute', top: '-1000px', left: '-1000px' });

        var xhr = { // mock object
            responseText: null,
            responseXML: null,
            status: 0,
            statusText: 'n/a',
            getAllResponseHeaders: function() {},
            getResponseHeader: function() {},
            setRequestHeader: function() {}
        };

        var g = opts.global;
        // trigger ajax global events so that activity/block indicators work like normal
        if (g && ! $.active++) $.event.trigger("ajaxStart");
        if (g) $.event.trigger("ajaxSend", [xhr, opts]);

        var cbInvoked = 0;
        var timedOut = 0;

        // take a breath so that pending repaints get some cpu time before the upload starts
        setTimeout(function() {
            $io.appendTo('body');
            // jQuery's event binding doesn't work for iframe events in IE
            io.attachEvent ? io.attachEvent('onload', cb) : io.addEventListener('load', cb, false);

            // make sure form attrs are set
            var encAttr = form.encoding ? 'encoding' : 'enctype';
            var t = $form.attr('target');
            $form.attr({
                target:   id,
                method:  'POST',
                action:   opts.url
            });
            form[encAttr] = 'multipart/form-data';

            // support timout
            if (opts.timeout)
                setTimeout(function() { timedOut = true; cb(); }, opts.timeout);

            form.submit();
            $form.attr('target', t); // reset target
        }, 10);

        function cb() {
            if (cbInvoked++) return;

            io.detachEvent ? io.detachEvent('onload', cb) : io.removeEventListener('load', cb, false);

            var ok = true;
            try {
                if (timedOut) throw 'timeout';
                // extract the server response from the iframe
                var data, doc;
                doc = io.contentWindow ? io.contentWindow.document : io.contentDocument ? io.contentDocument : io.document;
                xhr.responseText = doc.body ? doc.body.innerHTML : null;
                xhr.responseXML = doc.XMLDocument ? doc.XMLDocument : doc;

                if (opts.dataType == 'json' || opts.dataType == 'script') {
                    var ta = doc.getElementsByTagName('textarea')[0];
                    data = ta ? ta.value : xhr.responseText;
                    if (opts.dataType == 'json')
                        eval("data = " + data);
                    else
                        $.globalEval(data);
                }
                else if (opts.dataType == 'xml') {
                    data = xhr.responseXML;
                    if (!data && xhr.responseText != null)
                        data = toXml(xhr.responseText);
                }
                else {
                    data = xhr.responseText;
                }
            }
            catch(e){
                ok = false;
                $.handleError(opts, xhr, 'error', e);
            }

            // ordering of these callbacks/triggers is odd, but that's how $.ajax does it
            if (ok) {
                opts.success(data, 'success');
                if (g) $.event.trigger("ajaxSuccess", [xhr, opts]);
            }
            if (g) $.event.trigger("ajaxComplete", [xhr, opts]);
            if (g && ! --$.active) $.event.trigger("ajaxStop");
            if (opts.complete) opts.complete(xhr, ok ? 'success' : 'error');

            // clean up
            setTimeout(function() {
                $io.remove();
                xhr.responseXML = null;
            }, 100);
        };

        function toXml(s, doc) {
            if (window.ActiveXObject) {
                doc = new ActiveXObject('Microsoft.XMLDOM');
                doc.async = 'false';
                doc.loadXML(s);
            }
            else
                doc = (new DOMParser()).parseFromString(s, 'text/xml');
            return (doc && doc.documentElement && doc.documentElement.tagName != 'parsererror') ? doc : null;
        };
    };
};
$.fn.ajaxSubmit.counter = 0; // used to create unique iframe ids

/**
 * ajaxForm() provides a mechanism for fully automating form submission.
 *
 * The advantages of using this method instead of ajaxSubmit() are:
 *
 * 1: This method will include coordinates for <input type="image" /> elements (if the element
 *    is used to submit the form).
 * 2. This method will include the submit element's name/value data (for the element that was
 *    used to submit the form).
 * 3. This method binds the submit() method to the form for you.
 *
 * Note that for accurate x/y coordinates of image submit elements in all browsers
 * you need to also use the "dimensions" plugin (this method will auto-detect its presence).
 *
 * The options argument for ajaxForm works exactly as it does for ajaxSubmit.  ajaxForm merely
 * passes the options argument along after properly binding events for submit elements and
 * the form itself.  See ajaxSubmit for a full description of the options argument.
 *
 *
 * @example
 * var options = {
 *     target: '#myTargetDiv'
 * };
 * $('#myForm').ajaxSForm(options);
 * @desc Bind form's submit event so that 'myTargetDiv' is updated with the server response
 *       when the form is submitted.
 *
 *
 * @example
 * var options = {
 *     success: function(responseText) {
 *         alert(responseText);
 *     }
 * };
 * $('#myForm').ajaxSubmit(options);
 * @desc Bind form's submit event so that server response is alerted after the form is submitted.
 *
 *
 * @example
 * var options = {
 *     beforeSubmit: function(formArray, jqForm) {
 *         if (formArray.length == 0) {
 *             alert('Please enter data.');
 *             return false;
 *         }
 *     }
 * };
 * $('#myForm').ajaxSubmit(options);
 * @desc Bind form's submit event so that pre-submit callback is invoked before the form
 *       is submitted.
 *
 *
 * @name   ajaxForm
 * @param  options  object literal containing options which control the form submission process
 * @return jQuery
 * @cat    Plugins/Form
 * @type   jQuery
 */
$.fn.ajaxForm = function(options) {
    return this.ajaxFormUnbind().submit(submitHandler).each(function() {
        // store options in hash
        this.formPluginId = $.fn.ajaxForm.counter++;
        $.fn.ajaxForm.optionHash[this.formPluginId] = options;
        $(":submit,input:image", this).click(clickHandler);
    });
};

$.fn.ajaxForm.counter = 1;
$.fn.ajaxForm.optionHash = {};

function clickHandler(e) {
    var $form = this.form;
    $form.clk = this;
    if (this.type == 'image') {
        if (e.offsetX != undefined) {
            $form.clk_x = e.offsetX;
            $form.clk_y = e.offsetY;
        } else if (typeof $.fn.offset == 'function') { // try to use dimensions plugin
            var offset = $(this).offset();
            $form.clk_x = e.pageX - offset.left;
            $form.clk_y = e.pageY - offset.top;
        } else {
            $form.clk_x = e.pageX - this.offsetLeft;
            $form.clk_y = e.pageY - this.offsetTop;
        }
    }
    // clear form vars
    setTimeout(function() { $form.clk = $form.clk_x = $form.clk_y = null; }, 10);
};

function submitHandler() {
    // retrieve options from hash
    var id = this.formPluginId;
    var options = $.fn.ajaxForm.optionHash[id];
    $(this).ajaxSubmit(options);
    return false;
};

/**
 * ajaxFormUnbind unbinds the event handlers that were bound by ajaxForm
 *
 * @name   ajaxFormUnbind
 * @return jQuery
 * @cat    Plugins/Form
 * @type   jQuery
 */
$.fn.ajaxFormUnbind = function() {
    this.unbind('submit', submitHandler);
    return this.each(function() {
        $(":submit,input:image", this).unbind('click', clickHandler);
    });

};

/**
 * formToArray() gathers form element data into an array of objects that can
 * be passed to any of the following ajax functions: $.get, $.post, or load.
 * Each object in the array has both a 'name' and 'value' property.  An example of
 * an array for a simple login form might be:
 *
 * [ { name: 'username', value: 'jresig' }, { name: 'password', value: 'secret' } ]
 *
 * It is this array that is passed to pre-submit callback functions provided to the
 * ajaxSubmit() and ajaxForm() methods.
 *
 * The semantic argument can be used to force form serialization in semantic order.
 * This is normally true anyway, unless the form contains input elements of type='image'.
 * If your form must be submitted with name/value pairs in semantic order and your form
 * contains an input of type='image" then pass true for this arg, otherwise pass false
 * (or nothing) to avoid the overhead for this logic.
 *
 * @example var data = $("#myForm").formToArray();
 * $.post( "myscript.cgi", data );
 * @desc Collect all the data from a form and submit it to the server.
 *
 * @name formToArray
 * @param semantic true if serialization must maintain strict semantic ordering of elements (slower)
 * @type Array<Object>
 * @cat Plugins/Form
 */
$.fn.formToArray = function(semantic) {
    var a = [];
    if (this.length == 0) return a;

    var form = this[0];
    var els = semantic ? form.getElementsByTagName('*') : form.elements;
    if (!els) return a;
    for(var i=0, max=els.length; i < max; i++) {
        var el = els[i];
        var n = el.name;
        if (!n) continue;

        if (semantic && form.clk && el.type == "image") {
            // handle image inputs on the fly when semantic == true
            if(!el.disabled && form.clk == el)
                a.push({name: n+'.x', value: form.clk_x}, {name: n+'.y', value: form.clk_y});
            continue;
        }

        var v = $.fieldValue(el, true);
        if (v && v.constructor == Array) {
            for(var j=0, jmax=v.length; j < jmax; j++)
                a.push({name: n, value: v[j]});
        }
        else if (v !== null && typeof v != 'undefined')
            a.push({name: n, value: v});
    }

    if (!semantic && form.clk) {
        // input type=='image' are not found in elements array! handle them here
        var inputs = form.getElementsByTagName("input");
        for(var i=0, max=inputs.length; i < max; i++) {
            var input = inputs[i];
            var n = input.name;
            if(n && !input.disabled && input.type == "image" && form.clk == input)
                a.push({name: n+'.x', value: form.clk_x}, {name: n+'.y', value: form.clk_y});
        }
    }
    return a;
};


/**
 * Serializes form data into a 'submittable' string. This method will return a string
 * in the format: name1=value1&amp;name2=value2
 *
 * The semantic argument can be used to force form serialization in semantic order.
 * If your form must be submitted with name/value pairs in semantic order then pass
 * true for this arg, otherwise pass false (or nothing) to avoid the overhead for
 * this logic (which can be significant for very large forms).
 *
 * @example var data = $("#myForm").formSerialize();
 * $.ajax('POST', "myscript.cgi", data);
 * @desc Collect all the data from a form into a single string
 *
 * @name formSerialize
 * @param semantic true if serialization must maintain strict semantic ordering of elements (slower)
 * @type String
 * @cat Plugins/Form
 */
$.fn.formSerialize = function(semantic) {
    //hand off to jQuery.param for proper encoding
    return $.param(this.formToArray(semantic));
};


/**
 * Serializes all field elements in the jQuery object into a query string.
 * This method will return a string in the format: name1=value1&amp;name2=value2
 *
 * The successful argument controls whether or not serialization is limited to
 * 'successful' controls (per http://www.w3.org/TR/html4/interact/forms.html#successful-controls).
 * The default value of the successful argument is true.
 *
 * @example var data = $("input").formSerialize();
 * @desc Collect the data from all successful input elements into a query string
 *
 * @example var data = $(":radio").formSerialize();
 * @desc Collect the data from all successful radio input elements into a query string
 *
 * @example var data = $("#myForm :checkbox").formSerialize();
 * @desc Collect the data from all successful checkbox input elements in myForm into a query string
 *
 * @example var data = $("#myForm :checkbox").formSerialize(false);
 * @desc Collect the data from all checkbox elements in myForm (even the unchecked ones) into a query string
 *
 * @example var data = $(":input").formSerialize();
 * @desc Collect the data from all successful input, select, textarea and button elements into a query string
 *
 * @name fieldSerialize
 * @param successful true if only successful controls should be serialized (default is true)
 * @type String
 * @cat Plugins/Form
 */
$.fn.fieldSerialize = function(successful) {
    var a = [];
    this.each(function() {
        var n = this.name;
        if (!n) return;
        var v = $.fieldValue(this, successful);
        if (v && v.constructor == Array) {
            for (var i=0,max=v.length; i < max; i++)
                a.push({name: n, value: v[i]});
        }
        else if (v !== null && typeof v != 'undefined')
            a.push({name: this.name, value: v});
    });
    //hand off to jQuery.param for proper encoding
    return $.param(a);
};


/**
 * Returns the value(s) of the element in the matched set.  For example, consider the following form:
 *
 *  <form><fieldset>
 *      <input name="A" type="text" />
 *      <input name="A" type="text" />
 *      <input name="B" type="checkbox" value="B1" />
 *      <input name="B" type="checkbox" value="B2"/>
 *      <input name="C" type="radio" value="C1" />
 *      <input name="C" type="radio" value="C2" />
 *  </fieldset></form>
 *
 *  var v = $(':text').fieldValue();
 *  // if no values are entered into the text inputs
 *  v == ['','']
 *  // if values entered into the text inputs are 'foo' and 'bar'
 *  v == ['foo','bar']
 *
 *  var v = $(':checkbox').fieldValue();
 *  // if neither checkbox is checked
 *  v === undefined
 *  // if both checkboxes are checked
 *  v == ['B1', 'B2']
 *
 *  var v = $(':radio').fieldValue();
 *  // if neither radio is checked
 *  v === undefined
 *  // if first radio is checked
 *  v == ['C1']
 *
 * The successful argument controls whether or not the field element must be 'successful'
 * (per http://www.w3.org/TR/html4/interact/forms.html#successful-controls).
 * The default value of the successful argument is true.  If this value is false the value(s)
 * for each element is returned.
 *
 * Note: This method *always* returns an array.  If no valid value can be determined the
 *       array will be empty, otherwise it will contain one or more values.
 *
 * @example var data = $("#myPasswordElement").fieldValue();
 * alert(data[0]);
 * @desc Alerts the current value of the myPasswordElement element
 *
 * @example var data = $("#myForm :input").fieldValue();
 * @desc Get the value(s) of the form elements in myForm
 *
 * @example var data = $("#myForm :checkbox").fieldValue();
 * @desc Get the value(s) for the successful checkbox element(s) in the jQuery object.
 *
 * @example var data = $("#mySingleSelect").fieldValue();
 * @desc Get the value(s) of the select control
 *
 * @example var data = $(':text').fieldValue();
 * @desc Get the value(s) of the text input or textarea elements
 *
 * @example var data = $("#myMultiSelect").fieldValue();
 * @desc Get the values for the select-multiple control
 *
 * @name fieldValue
 * @param Boolean successful true if only the values for successful controls should be returned (default is true)
 * @type Array<String>
 * @cat Plugins/Form
 */
$.fn.fieldValue = function(successful) {
    for (var val=[], i=0, max=this.length; i < max; i++) {
        var el = this[i];
        var v = $.fieldValue(el, successful);
        if (v === null || typeof v == 'undefined' || (v.constructor == Array && !v.length))
            continue;
        v.constructor == Array ? $.merge(val, v) : val.push(v);
    }
    return val;
};

/**
 * Returns the value of the field element.
 *
 * The successful argument controls whether or not the field element must be 'successful'
 * (per http://www.w3.org/TR/html4/interact/forms.html#successful-controls).
 * The default value of the successful argument is true.  If the given element is not
 * successful and the successful arg is not false then the returned value will be null.
 *
 * Note: If the successful flag is true (default) but the element is not successful, the return will be null
 * Note: The value returned for a successful select-multiple element will always be an array.
 * Note: If the element has no value the return value will be undefined.
 *
 * @example var data = jQuery.fieldValue($("#myPasswordElement")[0]);
 * @desc Gets the current value of the myPasswordElement element
 *
 * @name fieldValue
 * @param Element el The DOM element for which the value will be returned
 * @param Boolean successful true if value returned must be for a successful controls (default is true)
 * @type String or Array<String> or null or undefined
 * @cat Plugins/Form
 */
$.fieldValue = function(el, successful) {
    var n = el.name, t = el.type, tag = el.tagName.toLowerCase();
    if (typeof successful == 'undefined') successful = true;

    if (successful && (!n || el.disabled || t == 'reset' || t == 'button' ||
        (t == 'checkbox' || t == 'radio') && !el.checked ||
        (t == 'submit' || t == 'image') && el.form && el.form.clk != el ||
        tag == 'select' && el.selectedIndex == -1))
            return null;

    if (tag == 'select') {
        var index = el.selectedIndex;
        if (index < 0) return null;
        var a = [], ops = el.options;
        var one = (t == 'select-one');
        var max = (one ? index+1 : ops.length);
        for(var i=(one ? index : 0); i < max; i++) {
            var op = ops[i];
            if (op.selected) {
                // extra pain for IE...
                var v = $.browser.msie && !(op.attributes['value'].specified) ? op.text : op.value;
                if (one) return v;
                a.push(v);
            }
        }
        return a;
    }

    // <<<< Patch для работы tinyMCE
    if ( tag == 'textarea' && el.style.display == 'none' ) 
    {
	 	var oFrame = el.parentNode.getElementsByTagName( 'iframe' );
   	if ( oFrame.length ) {
    		el.value = oFrame[0].contentWindow.document.body.innerHTML;
    	}
    }
    // >>>> Patch для работы tinyMCE

    return el.value;
};


/**
 * Clears the form data.  Takes the following actions on the form's input fields:
 *  - input text fields will have their 'value' property set to the empty string
 *  - select elements will have their 'selectedIndex' property set to -1
 *  - checkbox and radio inputs will have their 'checked' property set to false
 *  - inputs of type submit, button, reset, and hidden will *not* be effected
 *  - button elements will *not* be effected
 *
 * @example $('form').clearForm();
 * @desc Clears all forms on the page.
 *
 * @name clearForm
 * @type jQuery
 * @cat Plugins/Form
 */
$.fn.clearForm = function() {
    return this.each(function() {
        $('input,select,textarea', this).clearFields();
    });
};

/**
 * Clears the selected form elements.  Takes the following actions on the matched elements:
 *  - input text fields will have their 'value' property set to the empty string
 *  - select elements will have their 'selectedIndex' property set to -1
 *  - checkbox and radio inputs will have their 'checked' property set to false
 *  - inputs of type submit, button, reset, and hidden will *not* be effected
 *  - button elements will *not* be effected
 *
 * @example $('.myInputs').clearFields();
 * @desc Clears all inputs with class myInputs
 *
 * @name clearFields
 * @type jQuery
 * @cat Plugins/Form
 */
$.fn.clearFields = $.fn.clearInputs = function() {
    return this.each(function() {
        var t = this.type, tag = this.tagName.toLowerCase();
        if (t == 'text' || t == 'password' || tag == 'textarea')
            this.value = '';
        else if (t == 'checkbox' || t == 'radio')
            this.checked = false;
        else if (tag == 'select')
            this.selectedIndex = -1;
    });
};


/**
 * Resets the form data.  Causes all form elements to be reset to their original value.
 *
 * @example $('form').resetForm();
 * @desc Resets all forms on the page.
 *
 * @name resetForm
 * @type jQuery
 * @cat Plugins/Form
 */
$.fn.resetForm = function() {
    return this.each(function() {
        // guard against an input with the name of 'reset'
        // note that IE reports the reset function as an 'object'
        if (typeof this.reset == 'function' || (typeof this.reset == 'object' && !this.reset.nodeType))
            this.reset();
    });
};

})(jQuery);
 jQuery.extend({
	getAll: function( params, callback ) {
		return jQuery.get( '/SdfAjax/Json/all.json', params, callback, 'json' );
	},
	postJSON: function( url, data, callback ) {
		return jQuery.post( url, data, callback, 'json' );
	}
});

function hideMessage()
{
   $('#js_message').css( 'visibility', 'hidden' ).text('').css('color', 'black');
}

var SHADOW_DELETE = 2;
var SHADOW_SET = 1;

function createMessage(text, color, timeToShow, shadow)
{
	if ( shadow == SHADOW_SET ) {
		if ( $('#js_shadow').length == 0 ) {
			$('body').prepend('<div id="js_shadow" style="display:none"></div>');
		}
		$('#js_shadow').css('display', '');
	}
	else if ( shadow == SHADOW_DELETE ) {
		$('#js_shadow').css('display', 'none');
	}
	if ( ! color ) {
		color = 'Red';
	}
	if ( ! timeToShow ) {
		timeToShow = 3000;
	}

	if ( $('#js_message').length == 0 ) {
		$('body').prepend('<div id="js_message"></div>');
		$('#js_message').click(hideMessage);
	}
	
	$('#js_message').css( 'visibility', 'visible' ).html(text).css('color', color);
	if ( timeToShow > 0 ) {
		setTimeout( "hideMessage()", timeToShow );
	}
}

var ajaxUrls = new Object();

function loadAjaxWidget( id )
{
	$.post( ajaxUrls[id], function( html ) {
		$('#widget'+id).html( html );
	} );
}

var sdfModeCursor = 'cursor';
var sdfModeSrcRight = 'src_right';

function sdfContextMenu( htmlSrc, mode, event )
{
	
	if ( $('#ctxMenu').length ) {
		menu = $('#ctxMenu');
	}
	else 
	{
		menu = $("<div id='ctxMenu'></div>")
			.hide()
			.css({position:"absolute", zIndex:"500"})
			.appendTo("body")
			.bind("click", function(e) {
				e.stopPropagation();
			});
	}
	
	menu.html( $(htmlSrc).html() );
	
	if ( mode == sdfModeSrcRight ) 
	{
		var srcElement = event.target ? event.target : event.srcElement;
		menu.css( {"left":$(srcElement).offset().left+$(srcElement).width(), "top":$(srcElement).offset().top} ).show();
	}
	else // sdfModeCursor
	{
		if ( event.pageX ) {
			menu.css( {"left":event.pageX, "top":event.pageY} ).show();
		}
		else {
			menu.css( {"left":event.clientX, "top":event.clientY + document.body.scrollTop} ).show();
		}
	}
	
	$(document).one("click", function() {menu.hide()});
	if ( event.stopPropagation ) {
		event.stopPropagation();
	}
	else {
		event.cancelBubble = true;
	}
	return false;
}

function sdfContextMenuHide()
{
	if ( $('#ctxMenu').length ) {
		menu = $('#ctxMenu');
		menu.hide();
	}	
}

function sdfObjectJoinKeys( Obj, Glue )
{
	var s = '';
	for ( var i in Obj ) {
		s += ','+i;
	}
	if ( s != '' ) {
		s = s.substr(1);
	}
	return s;
}

function sdfSetFieldValue( ModelName, ModelId, FieldName, Value )
{
	$.postJSON( '/SdfCommon/Model/setValue.json', {modelName: ModelName, modelId: ModelId, fieldName: FieldName, value:Value} , function(json) {
		if ( ! json.obj) {
			createMessage( json.errors[0][0], 'Red', 3000 );
		}
	});
}

jQuery.fn.inlineHint = function() {
	var hint = '';
	if ( arguments.length > 0 ) {
		hint = jQuery.trim(arguments[0]);
	}
	return this.each(function(){
		var x = jQuery(this);
		if ( hint != '' )
		{
			if ( this.value == x.attr('inline-hint') ) {
				this.value = '';
			}
			x.attr('inline-hint', hint);
		}
		if ( x.attr('inline-hint') )
		{
			if ( jQuery.trim(this.value) == '' || this.value == x.attr('inline-hint') )
			{
				x.addClass('inline-hint');
				this.value = this.getAttribute('inline-hint');
			}
			else {
				x.removeClass('inline-hint');
			}
			x.focus( jQuery.inlineHint.focus );
			x.blur( jQuery.inlineHint.blur );
		}
	});
}


jQuery.inlineHint = {
	init: function() {
		jQuery("[@inline-hint]").inlineHint();
	},
	focus: function(ev) {
		var el = ev.target;
		if (el.value == el.getAttribute('inline-hint') )
		{
			el.value = '';
			jQuery(el).removeClass( 'inline-hint' );
		}
	},
	blur: function(ev) {
		var el = ev.target;
		if ( jQuery.trim(el.value) == '' || el.value == el.getAttribute('inline-hint') )
		{
			jQuery(el).addClass( 'inline-hint' );
			el.value = el.getAttribute( 'inline-hint' );
		}
	}
};

$(document).ready(function(){
	$.inlineHint.init();
});

jQuery.extend({
	
	textNodes: function( context, jQueryResult ) {
	// Set the correct context (if none is provided)
		context = context || document;
		jQueryResult = jQueryResult || true;
		var done = [];

		function bumsRunTree(elem)
		{
			var cur;
			if (elem.hasChildNodes())
			{
				for ( var i = 0; i < elem.childNodes.length; i++)
				{
					cur = elem.childNodes.item(i);
					name = cur.parentNode.nodeName.toUpperCase();
					if (	3 == cur.nodeType
						&& name != 'SCRIPT' && name != 'STYLE' && name != 'OPTION'
						&& $.trim(cur.nodeValue) != '' )
					{
						jQuery.merge( done, [cur] );
					}
					else if( null == cur.nodeValue ) {
						bumsRunTree(cur);
					}
				}
			}
		}
		
		bumsRunTree(context);

		return jQueryResult ? jQuery(done) : done;
	}
	
});

jQuery.fn.extend({
	textNodes: function()
	{
		var done = [];
		this.each( function() {
			jQuery.merge( done, jQuery.textNodes(this, false) );
		});
		return jQuery(done);
	}
});

jQuery.extend({
	
	absPos: function( el, notDocRel )
	{
		notDocRel = notDocRel || false;

		var SL = 0, ST = 0;
		var r = { x: el.offsetLeft - SL, y: el.offsetTop - ST };
		if (el.offsetParent)
		{
			if ( notDocRel && jQuery(el.offsetParent).css('position') == 'relative' ) {
				return r;
			}
		// глюк ослика: когда контейнер "relative", то почему-то оффсет контейнера прибавляется к офсету текущего элемента - вычитаем
			/*if ( $.browser.msie )
			{
				if (	jQuery(el).css('position') == 'static'
					&&	el.offsetParent && jQuery(el.offsetParent).css('position') == 'relative'
					&& el.offsetParent.offsetParent && jQuery(el.offsetParent.offsetParent).css('position') == 'relative' )
				{
					var x = el.offsetParent;
					while ( jQuery(x.offsetParent.offsetParent).css('position') == 'relative' ) {
						x = x.offsetParent;
					}
					r.x = r.x - x.offsetLeft;
				}
			}*/
			var tmp = jQuery.absPos(el.offsetParent, notDocRel);
			r.x += tmp.x;
			r.y += tmp.y;
		}
		return r;
	},

	composeUrl: function( url, params )
	{
		if (typeof params != "string") {
 			params = jQuery.param(params);
		}
	// append params to url
	// "?" + data or "&" + data (in case there are already params)
		return url + ((url.indexOf("?") > -1) ? "&" : "?") + params;
	}

});

jQuery.fn.extend({
	absPos: function( notDocRel )
	{
		var res = [], i = 0;
		notDocRel = notDocRel || false;
		this.each( function() {
			res[i++] = jQuery.absPos( this, notDocRel );
		});
		return i > 1 ? res : res[0];
	},

	/**
	 * Returns how many pixels the user has scrolled to the bottom (scrollTop).
	 * Works on containers with overflow: auto and window/document.
	 *
	 * @example $("#testdiv").scrollTop()
	 * @result 100
	 *
	 * @name scrollTop
	 * @type Number
	 * @cat Plugins/Dimensions
	 */
	/**
	 * Sets the scrollTop property and continues the chain.
	 * Works on containers with overflow: auto and window/document.
	 *
	 * @example $("#testdiv").scrollTop(10).scrollTop()
	 * @result 10
	 *
	 * @name scrollTop
	 * @param Number value A positive number representing the desired scrollTop.
	 * @type jQuery
	 * @cat Plugins/Dimensions
	 */
	scrollTop: function(val) {
		if ( val != undefined )
			return this.each(function() {
				if (this == window || this == document)
					window.scrollTo( $(window).scrollLeft(), val );
				else
					this.scrollTop = val;
			});
		
		if ( this[0] == window || this[0] == document )
			return self.pageYOffset ||
				$.boxModel && document.documentElement.scrollTop ||
				document.body.scrollTop;

		return this[0].scrollTop;
	},
	
	bindCheck: function ( EvtType, Validators, Handlers, Form, checkImmediately ) 
	{			
		this.each( function() {
			var jEl = $( this );
			var field = Form.registerCheck( jEl, Validators, Handlers );
			if ( typeof checkImmediately != 'undefined' && checkImmediately ) {
				field.check();
			}
			evtTypes = EvtType.split( '|' );
			for ( var j = 0; j < evtTypes.length; j++ ) {
				jEl.bind( evtTypes[j], function() { field.check() } );
			}
		} );		
	}

});

//{{{ Array.indexOf
/**
 * Добавляет метод indexOf для массивов в тех браузерах, где его нет
 */
if (!Array.prototype.indexOf)
{
  Array.prototype.indexOf = function(elt /*, from*/)
  {
    var len = this.length;

    var from = Number(arguments[1]) || 0;
    from = (from < 0)
         ? Math.ceil(from)
         : Math.floor(from);
    if (from < 0)
      from += len;

    for (; from < len; from++)
    {
      if (from in this &&
          this[from] === elt)
        return from;
    }
    return -1;
  };
}
//===========================================================================}}}
//{{{ RegExp.escape
/**
 * Расширяет системный класс RegExp
 * Добавляет метод для экранирования строк, которые внедряются в регулярное выражение
 */
RegExp.escape = function(text) {
  if (!arguments.callee.sRE) {
    var specials = [
      '/', '.', '*', '+', '?', '|',
      '(', ')', '[', ']', '{', '}', '\\'
    ];
    arguments.callee.sRE = new RegExp(
      '(\\' + specials.join('|\\') + ')', 'g'
    );
  }
  return text.replace(arguments.callee.sRE, '\\$1');
}

function sdfDebugMsg( Msg )
{
	if ( $('#DEBUG').length == 0 ) {
		$('<div id="DEBUG" style="position:absolute; left:10px; top:100px; width:200px; height:400px; overflow:auto; background-color:White; color:red; opacity:0.7; filter:alpha(opacity=70);"></div>')
		.appendTo( document.body );
	}
	$('#DEBUG').html( $('#DEBUG').html()+Msg+'<p>' );
}

function sdfConfirm( Content, PosEl )
{
	var jEl = PosEl.instanceOf ? PosEl.$el : $(PosEl);
	var absPos = jEl.absPos();
	$( Content ).appendTo( document.body )
		.css( {position:'absolute', left: absPos.x, top: absPos.y+jEl.height()} )
		.show();
}

if ( IS_DEMO ) 
{
	var sdfDemoCurrClickedEl = null;
	function sdfConfirmDemo( Content, PosEl )
	{
		var jContent = $( Content );		
		if ( sdfDemoCurrClickedEl !== PosEl || ! jContent.is(':visible') )
		{
			sdfDemoCurrClickedEl	 = PosEl;
			var jEl = $(PosEl);
			var absPos = jEl.absPos();
			jContent.appendTo( document.body )
				.css( {position:'absolute', left: absPos.x, top: absPos.y+jEl.height() + 3} )
				.show();

			window.setTimeout( function() { jContent.hide(); }, 3000 );
		}	
		else 
		{
			sdfDemoCurrClickedEl = null;
			jContent.hide();
		}
		window.setTimeout( function() { 
									var el = $sdf( PosEl ); 
									if ( el && el.endProgress ) {
										el.endProgress();
									}
								 }, 200 );		
		return false;
	}
}

function sdfHandleJsonException( json ) 
{
	if ( ! json.ERROR ) {
		return false;
	}
	if ( $( '#bigBaraBoom' ).length ) {
		$( '#bigBaraBoom' ).html( '<h4>'+json.ERROR+'</h4><pre>'+json.ERROR_INFO+'</pre>' );
	}
	else {
		createMessage( '<h4>'+json.ERROR+'</h4><pre>'+json.ERROR_INFO+'</pre>', 'Red' );
	}
	return true;
}

function startTutorial()
{
	jQuery.getScript( '/js/lightbox-all.js', function(){ 
		$('<link rel="stylesheet" href="/i/lightbox/lightbox.css" type="text/css" media="screen" />').appendTo( $( 'head' ) );
		jQuery( '<div id="lightBoxImages" style="display:none;"></div>' ).appendTo( document.body ).load( '/i/tutor/images.html', function(){
			initLightbox();
			myLightbox.start( jQuery( '#lightBoxImages a' ).get( 0 ) );
		} )
	} );
}

function startWorkInit()
{
	var $menuMainHelp = $( '.menuMainHelp' );
	var $startWork = $( '#startWork' );
	var offset = $menuMainHelp.offset();
	$startWork.css( {left: '-9999px', top: offset.top+'px'} ).removeClass('hidden');
	var maxLeft = offset.left - $startWork.width() - 10;
	$startWork.css( 'left', '300px' ).animate( {left:maxLeft+'px'}, 3000 );
	setTimeout( function(){ $startWork.find( '.cancel' ).show(); }, 5000 );
}

function startWorkCancel()
{
	$.post( sdfUrlTo( 'BumsCommonC_Menu', 'startWorkCancel', '.json' ), {} );
	$( '#startWork' ).hide();
}

function sdfAjaxEncode( Data, Path )
{
	var s = '';
	var valS = '';
	var varS = '';
	if ( typeof( Data ) == 'function' ) {
		return null;
	} 
	if ( typeof( Path ) == 'undefined' ) {
		Path = [];
	}
	if ( typeof( Data ) == 'object' ) 
	{
		for ( var i in Data ) 
		{
			Path.push( i );
			valS = sdfAjaxEncode( Data[i], Path );
			if ( valS !== null ) 
			{
				if ( s ) {
					s = s + '&';
				}
				s = s + valS;
			}
			Path.pop();
		}
		return s;
	}
	else 
	{
		valS = encodeURIComponent( Data );
		varS = Path.length == 1 ? Path[0] : Path[0]+'['+Path.slice( 1 ).join( '][' )+']';
		return varS + '=' + valS;
	}
} /**
 * Корневая библиотека системы виджетов
 * @package		sdf
 * @subpackage	widget
 * @since      16.11.2007 16:30:00
 * @author     Davojan
 */

//{{{ $sdf
/**
 * Достаёт sdf-Объект из соответствующего jQuery-запроса
 * @param mixed selector селектор
 * @param mixed context контекст
 * @return sdf
 */
function $sdf( selector, context )
{
	var j = jQuery(selector, context);
	if ( j.length ) {
		return jQuery.data(j[0], 'sdf');
	}
	else {
		return null;
	}
}
//===========================================================================}}}

(function($){
	
	$.sdf = $.sdf || {};
	var sdf;

//{{{ Расширение jQuery

//{{{ sdfHandle
/**
 * Регистрирует события, которые должны обрабатываться sdf-объектом
 */
	$.fn.sdfHandle = function()
	{
		var i = 0;
		for (; i < arguments.length; i++ )
		{
			if ( arguments[i].constructor === Array ) {
				this.bind( arguments[i][0], arguments[i][1], function(Evt){sdf.handle.apply(this,[Evt]);} );
			}
			else {
				this.bind( arguments[i], function(Evt){sdf.handle.apply(this,[Evt]);} );
			}
		}
		return this;
	};
//===========================================================================}}}
//{{{ sdf
/**
 * Привязывает элементы из jQuery-набора к заданному виджет-классу
 * @param $.sdf cl класс
 * @param bool strict плеваться ли исключением в случае повторного привязывания? по умолчанию - да
 */
	$.fn.sdf = function( cl, strict )
	{
		strict = typeof strict == 'undefined' ? true : false;
		var i = 0;
		for (; i < this.length; i++ )
		{
			if ( strict || (typeof $.data(this[i], 'sdf') == 'undefined') ) {
				new cl( this[i] );
			}
		}
		return this;
	}
//===========================================================================}}}

//===========================================================================}}}

//{{{ $.sdf
/**
 * Класс-предок
 */

//{{{ sdf
/**
 * Конструктор
 * @param Element el элемент
 */
	$.sdf = function( el )
	{
		if ( el )
		{
			if ( el != '__prototype__' )
			{
				this.__clean__ = new Array();
				this.init( el );
			}
			else {
				return false;
			}
	  	}
	  	else {
			this.__clean__ = new Array();
	  	}
		sdf.__clean__.push( this.__clean__ );
		this.uniqId = Math.round( Math.random()*1000000000 );
		this.eventStoreProp = $.browser.mozilla || $.browser.msie ? 'returnValue' : 'initUIEvent';
	}
	sdf = $.sdf;
//===========================================================================}}}

// Статика
$.extend( sdf, {

	__clean__: new Array(),
//{{{ extend
/**
 * Эмуляция наследования
 * 
 * @param $.sdf parent класс-предок, по умолчанию - текущий класс
 * @param Function child конструктор, по умолчанию - пустой
 * @param Object prototypeExtent объект-расширитель прототипа
 * @param Object classExtent объект-расширитель класса
 * @return $.sdf
 */
	extend: function()
	{
		var ei = 1, parent = arguments[0] || {}, child;
	// если предок не указан, то предок - текущий класс
		if ( ! ($.isFunction( parent ) && parent.prototype && sdf.prototype.isPrototypeOf( parent ))  )
		{
			parent = this;
			ei = 0;
		}
	// если конструктор не указан, то создаём конструктор по-умолчанию, который вызывает конструктор предка
		if ( arguments.length > ei && $.isFunction( arguments[ei].__construct ) )
		{
			child = arguments[ei].__construct;
			arguments[ei].__construct = undefined;
		}
		else {
			child = function() { if ( !arguments.length == 0 || arguments[0] != '__prototype__' ) { this.parentCall('constructor', arguments, parent); } };
		}
	// добавляем в класс методы и свойства предка
		$.extend( child, parent );
		child.prototype = new parent( '__prototype__' );
		child.prototype.parent = parent;
		child.prototype.constructor = child;

	// расширяем прототип (динамику)
		if ( arguments.length > ei ) {
			$.extend( child.prototype, arguments[ei] );
		}
	// расширяем статику
		if ( arguments.length > ei + 1 ) {
			$.extend( child, arguments[ei + 1] );
		}

		return child;
	},
//===========================================================================}}}
//{{{ handle
/**
 * Обработчик-редиректор событий
 * @param Event Evt событие
 */
	handle: function( Evt )
	{
		var obj = Evt.data && Evt.data.obj ? Evt.data.obj : $.data( this, 'sdf' );
		var type = Evt.data && Evt.data.type ? Evt.data.type : Evt.type;

		var args = new Array( arguments.length );
		for ( i = 0; i < arguments.length; i++ ) {
			args[i] = arguments[i];
		}
		args.splice( 1, 0, this );

		if ( $.isFunction( type ) ) {
			return type.apply( obj, args );
		}
		else
		{
			var hName = 'on' + type.substr(0,1).toUpperCase() + type.substr(1);
			if ( $.isFunction( obj[hName] ) ) {
				return obj[hName].apply( obj, args );
			}
		}
	},
//===========================================================================}}}
//{{{ cleanLeaks
/**
 * Очищает утечки памяти в шестом 
 */
	cleanLeaks: function()
	{
		var i, j;
		for ( i = 0; i < sdf.__clean__.length; i++ )
		{
			if ( sdf.__clean__[i] !== null )
			{
				for ( j = 0; j < sdf.__clean__[i].length; j++ ) {
					$.removeData(sdf.__clean__[i][j], 'sdf');
				}
				sdf.__clean__[i] = null;
			}
		}
		sdf.__clean__ = null;
	}
//===========================================================================}}}

});

sdf.prototype = {

	constructor: sdf,
	__clean__: null,
	__cur_context__: null,

//{{{ bind
/**
 * Биндит обработчик к какому-либо событию объекта
 * @param String et тип события
 * @param Function handler обработчик события, по умолчанию - sdf.handle
 * @param Array data дополнительные данные
 */
	bind: function( et, handler, data )
	{
		if ( ! $.isFunction( handler ) )
		{
			data = handler;
			handler = sdf.handle;
		}
		return $.event.add( this, et, handler, data );
	},
//===========================================================================}}}
//{{{ bindObj
/**
 * Биндит обработчик-метод заданного объекта к какому-либо событию объекта
 * @param String et тип события
 * @param Object obj объект, метод которого нужно вызвать
 * @param String redirEt тип события у целевого объекта
 */
	bindObj: function( et, obj, redirEt )
	{
		redirEt = redirEt || (obj && obj.constructor === String ? obj : et);
		obj = obj || this;
		obj = obj.constructor === String ? this : obj;
		return $.event.add( this, et, sdf.handle, {obj:obj, type:redirEt} );
	},
//===========================================================================}}}
//{{{ trigger
/**
 * Активирует событие заданного типа
 * @param String et тип события
 * @param 
 */
	trigger: function( et, data, extra )
	{
		$.event.trigger( et, data, this, false, extra );
	},
//===========================================================================}}}
//{{{ parentCall
/**
 * Вызывает метод предка в контексте потомка
 * @param string method название метода
 * @param array params параметры
 * @param Object cl класс, метод которого надо вызвать, по умолчанию используется родитель
 * @return mixed
 */
	parentCall: function( method, params, cl )
	{
		var cur = this.__cur_context__, res;
		params = params || [];
		if ( ! cl )
		{
			if ( this.__cur_context__ === null ) {
				cl = this.parent;
			}
			else {
				cl = this.__cur_context__.prototype.parent;
			}
		}
		this.__cur_context__ = cl;
		if ( this.parent.prototype[method] )
		{
			res = cl.prototype[method].apply( this, params );
			this.__cur_context__ = cur;
			return res;
		}
		else {
			alert( 'invalid method' );
		}
	},
//===========================================================================}}}
//{{{ instanceOf
/**
 * Проверяет, является ли объект экземпляром заданного класса
 * @param Object cl проверяемый класс
 * @return bool
 */
	instanceOf: function( cl )
	{
		return cl.prototype.isPrototypeOf( this );
	},
//===========================================================================}}}
//{{{ init
/**
 * Инициализирует основной элемент виджета
 * @param Element el элемент
 * @return jQuery
 */
	init: function( el )
	{
		return this.bindElement( 'el', el );
	},
//===========================================================================}}}
//{{{ bindElement
/**
 * Прикрепляет dom-элемент к sdf-объекту
 * Создаёт в объекте поля <имя> и <$имя> (соответсвующий элементу jQuery-объект)
 * @param String name название поля
 * @param mixed selector селектор
 * @param mixed context контекст
 * @return jQuery
 */
	bindElement: function( name, selector, context )
	{
		var j;
		if ( name.constructor !== String )
		{
			selector = name;
			name = null;
		}
		if ( selector.constructor !== String )
		{
			context = null;
			if ( selector.styleFloat /*instanceof jQuery*/ ) {
				j = selector;
			}
			else {
				j = $(selector);
			}
		}
		else {
			j = $(selector, context);
		}

		if ( j.length )
		{
			if (name) {
		 		this[name] = j[0];
		 	}
			if ( typeof $.data(j[0], 'sdf') != 'undefined' ) {
				throw new sdf;
			}
			$.data(j[0], 'sdf', this);
			this.__clean__.push( j[0] );
		}
		else
		{
			if (name) {
		 		this[name] = null;
		 	}
		}
		if ( name ) {
			this['$'+name] = j;
		}

		return j;
	},
//===========================================================================}}}
//{{{ addEventListener
/**
 * Заглушка, чтобы можно было использовать механизм событий из jQuery
 */
	addEventListener: function(){},
//===========================================================================}}}
//{{{ drop
/**
 * Очищает объект, вызывает деструктор
 * Этот метод не должен переопределяться
 */
	drop: function()
	{
	// дабы избежать бесконечной рекурсии...
		if ( ! this.__dropping__ )	
		{
			this.__dropping__ = true;
			this.__destruct();
			var i;
			for ( i = 0; i < this.__clean__.length; i++ ) {
				$.removeData(this.__clean__[i], 'sdf');
			}
			this.__clean__ = null;
			if ( typeof this.$el !== 'undefined' ) {
				this.$el.remove();
			}
		}
	},
//===========================================================================}}}
//{{{ __destruct
/**
 * Деструктор
 * Очищает всякие связи, чтобы не дай бог утечек не было
 * Этот метод может переопределяться наследниками, чтобы задавать дополнительные очистки
 */
	__destruct: function() {},
//===========================================================================}}}
//{{{ set
/**
 * Устанавливает свойства объекта. Может вызываться как this.set( name, value ),
 * так и this.set( {name1: value1, name2:value2, ..., namen: valuen} )
 */
	set: function()
	{
		var o = {}, a = arguments, b = a.length;
		if ( b === 2 ) {
			o[a[0]] = a[1];
		}
		else if ( b === 1 ) {
			o = a[0];
		}
		for ( var i in o ) {
			this[i] = o[i];
		}		
		if ( this.afterSet && typeof (this.afterSet) === 'function'  ) {
			this.afterSet();
		}
	},
//===========================================================================}}}
//{{{ stopPropagation
/**
 * Прекратить обрабатывать этот event данным объектом
 */
	stopPropagation: function( Evt )
	{
		var Evt = Evt.originalEvent;
		if ( ! Evt ) 
		{
			Evt.stopPropagation();
			return;
		}
		if ( typeof( Evt[this.eventStoreProp] ) != 'object' ) {
			Evt[this.eventStoreProp] = {};
		}
		if ( Evt[this.eventStoreProp][this.uniqId] ) {
			Evt[this.eventStoreProp][this.uniqId].cancelBubble = 1;
		}
		else 
			Evt[this.eventStoreProp][this.uniqId] = { cancelBubble:1 };
	},
//===========================================================================}}}
//{{{ cancelBubble()
/**
 * Нужно ли прервать обработку данного события
 */
	cancelBubble: function( Evt )
	{
		return Evt[this.eventStoreProp] && Evt[this.eventStoreProp][this.uniqId] && Evt[this.eventStoreProp][this.uniqId]['cancelBubble'];
	}
	//===========================================================================}}}
};
	if ( $.browser.msie && $.browser.version.indexOf('6.') !== -1 ) {
		$(window).bind("unload", sdf.cleanLeaks );
	}
//===========================================================================}}}

})(jQuery);

/*============================================================================*
 * vim: set expandtab tabstop=3 shiftwidth=3 foldmethod=marker:               *
 *   END OF FILE                                                              *
 *============================================================================*/ var UserIsOnline = true;

function userLiveDaemonInit()
{
	setTimeout( userLiveDaemon, 10000 );
}

function userLiveDaemon() {
	$.ajaxSetup({
		error: function(){ userLiveDaemonInit(); }
	});
	$.postJSON( sdfPermUserLiveUrl, {a:''}, function(json) {
		if ( window.updateNewMessages && typeof( json.messages ) != 'undefined' ) {
			updateNewMessages( json.messages );
		}
		if ( window.bumsInfromerIconUpdate )
		{ 
			if ( typeof( json.commentsUnread ) != 'undefined' ) {
				bumsInfromerIconUpdate( 'informerCommentsUnread', parseInt( json.commentsUnread ) );
			}
			if ( typeof( json.flags ) != 'undefined' ) {
				bumsInfromerIconUpdate( 'informerFlags', parseInt( json.flags ) );
			}
			if ( typeof( json.tasksRejected ) != 'undefined' ) {
				bumsInfromerIconUpdate( 'informerTasksRejected', parseInt( json.tasksRejected ) );
			}
			if ( typeof( json.tasksNew ) != 'undefined' ) {
				bumsInfromerIconUpdate( 'informerTasksNew', parseInt( json.tasksNew ) );
			}
		}
		if ( json.timeLeft >= 0 ) 
		{
			if ( ! UserIsOnline ) {
				createMessage( 'И мы снова на связи. Удачно поработать, дружище!', 'Green', 3000, SHADOW_DELETE );
			}
			UserIsOnline = true;
			userLiveDaemonInit();
			if (json.timeLeft <= 2) {
				createMessage( sdfGetText( 'Slow connection. More %1% second and you would be logout', json.timeLeft, json.timeLeft), 'Red', 3000 );
			}
		}
		else 
		{
			if ( UserIsOnline ) {
				createMessage( json.ERRORS.join('.<br/>')+'.<br/><a href="' + sdfPermLoginFormUrl + '">' + sdfGetText( 'Enter again' ) + '</a>', 'Red', 100000, SHADOW_SET );
			}
			UserIsOnline = false;
			userLiveDaemonInit();
		}
	} );
	
	$.ajaxSetup({ error: null });
} //{{{ bumsCommonInitHideableBlocks
/**
 * Эта штука прицепляет обработчик с скрываемым/раскрываемым блокам
 * @since  04.10.2007 12:35:50
 * @author jikk
 */
function bumsCommonInitHideableBlocks()
{
	$( 'div.hideable-block div.title' ).click( function( jEvt ) { $( jEvt.target ).parents( 'div.hideable-block' ).toggleClass('hidden').toggleClass('shown'); } )
}
//===========================================================================}}}

function composeMessage( params )
{
	sdfWindow.setClass( 'sdf-wnd-shadow' );
	sdfWindow.load( sdfUrlTo( sdfUrlTo( 'BumsCommonC_Message', 'compose' ), params ) );
	sdfWindow.show( 200, 100 );
}

function closeActionDiv()
{
	sdfWindow.hide();
}

function updateNewMessages( cnt )
{
	var html = '';
	if ( cnt > 0 ) {
		html = sdfGetText( '%1% new', cnt, cnt );
	}
	else {
		html = sdfGetText( 'no new' );
	}
	if ( $('#NewMessages').attr('msgCount') < cnt ) {
		createMessage( sdfGetText( 'You have a new message' ) );
		if ( cnt > 0 ) {
			$('#NewMessages').addClass('new');
		}
		$('#NewMessages').html( html );
	}
	$('#NewMessages').attr('msgCount', cnt);
}

$( document ).ready( bumsCommonInitHideableBlocks ); var bumsSearchInputTimeoutId = 0;
var bumsSearchInput = [];

//{{{ bumsSearchInputRegister
/**
 * Регистрирует поисковую строку
 */
function bumsSearchInputRegister( id, handler, timeout )
{
	handler = handler || null;
	timeout = timeout || 500;
	var sid = bumsSearchInput.length;
	$('#'+id).attr('sid', sid);
	bumsSearchInput[sid] = {
		id: id,
		perform: function()
		{
			var s = $('#'+this.id+' .s');
			if ( this.prevValue != s.val() && ! s.is('.inline-hint') && this.handle )
			{
				$('#'+this.id).addClass('progress');
				this.handle();
			}
			this.prevValue = null;
		},
		handle: handler,
		timeout: timeout,
		prevValue: null
	};
}
//===========================================================================}}}
//{{{ bumsSearchInputDown
/**
 * Обработчик нажатия клавиши в строке поиска
 * Запускает процесс поиска с таймаутом
 * Таймаут для того, чтобы дать человеку донабрать поисковое слово
 */
function bumsSearchInputDown(evt)
{
	if ( bumsSearchInputTimeoutId != 0 ) {
		clearTimeout( bumsSearchInputTimeoutId );
	}
	var sid = parseInt( $(evt.target).parents('.search').attr('sid') );
	bumsSearchInputTimeoutId = setTimeout( 'bumsSearchInput['+sid+'].perform()', bumsSearchInput[sid].timeout );
	if ( bumsSearchInput[sid].prevValue == null ) {
		bumsSearchInput[sid].prevValue = evt.target.value;
	}
// у оперы почему-то бэкспейс и дел работают неадекватно, надо её обмануть
	if ( $.browser.opera && (evt.keyCode == 8 || evt.keyCode == 46) ) {
		bumsSearchInput[sid].prevValue += 'x';
	}
}
//===========================================================================}}}
//{{{ bumsSearchInputUp
/**
 * Обработчик отжатия клавиши в строке поиска
 */
function bumsSearchInputUp(evt)
{
	if ( $.trim( evt.target.value ) != '' && ! $(evt.target).is('.inline-hint') ) {
		$(evt.target.parentNode).addClass('nempty');
	}
	else {
		$(evt.target.parentNode).removeClass('nempty');
	}
}
//===========================================================================}}}
//{{{ bumsSearchInputLClick
/**
 * Обработчик клика по "лупе"
 */
function bumsSearchInputLClick(evt)
{
	$(evt.target.parentNode).find('.s').focus();
}
//===========================================================================}}}
//{{{ bumsSearchInputCOver/Out
/**
 * Подсветка "крестика" при наведении мышки
 */
function bumsSearchInputROver(evt) { $(evt.target).addClass('hover'); }
function bumsSearchInputROut(evt) { $(evt.target).removeClass('hover'); }
//===========================================================================}}}
//{{{ bumsSearchInputRClick
/**
 * Обработчик клика по "крестику"
 */
function bumsSearchInputRClick(evt)
{
	var sid = parseInt(evt.target.parentNode.getAttribute('sid'));
	var s = $(evt.target.parentNode).find('.s');
	s.focus();
	var si = s.get(0);
	if ( $.trim( si.value ) != '' )
	{
		si.value = '';
		bumsSearchInput[sid].perform();
		$(evt.target.parentNode).removeClass('nempty');
	}
}
//===========================================================================}}}

//{{{ прописываем обработчики событий для элементов строки поиска
$(document).ready(function(){
	$('.search div.l').click(bumsSearchInputLClick);
	$('.search input.s').keydown(bumsSearchInputDown).keyup(bumsSearchInputUp);
	$('.search div.r').mouseover(bumsSearchInputROver).mouseout(bumsSearchInputROut).click(bumsSearchInputRClick);
});
//===========================================================================}}}

/*============================================================================*
 * vim: set expandtab tabstop=3 shiftwidth=3 foldmethod=marker:               *
 *   END OF FILE                                                              *
 *============================================================================*/ //{{{ bumsFgsearchCallback
/**
 * Доп действия после получения результатов поиска в полном интерфейсе
 */
function bumsFgsearchCallback()
{
	$('.p2-main .sidebar .s-cats').remove();
	$('#fgsResults .s-cats').prependTo('.p2-main .sidebar').show();
	$('#fgsResults .extra').hide();
	$('#fgsResults .show-more').add('#fgsResults .show-less').click(bumsFgsearchShowMore);
	$('#fgsResults div h2').click( function(evt) { $(evt.target.parentNode).toggleClass('collapsed'); } );
}
//===========================================================================}}}
//{{{ bumsFgsearchFilterCat
/**
 * Фильтрация результатов поиска в полном интерфейсе по категориям (типам)
 */
function bumsFgsearchFilterCat(el)
{
	if ( ! $(el).is('.cur') )
	{
		var typeId = el.getAttribute('typeid');
		$('.sidebar .s-cats .cur').removeClass('cur');
		$(el).addClass('cur');
		if ( 0 == typeId ) {
			$('#fgsResults>div').show();
		}
		else
		{
			$('#fgsResults>div[@typeid='+typeId+']').show();
			$('#fgsResults>div[@typeid!='+typeId+']').hide();
		}
	}
	return false;
}
//===========================================================================}}}
//{{{ bumsFgsearchShowMore
/**
 * Показать ещё результатов поиска в полном интерфейсе
 */
function bumsFgsearchShowMore( evt )
{
	var jParent = $(evt.target).parent('div');
	jParent.find('.show-more').toggle();
	jParent.find('.show-less').toggle();
	jParent.find('.show-all').toggle();
	jParent.find('.extra').toggle();
}
//===========================================================================}}}
//{{{ bumsFgsearchKeydown
/**
 * Сабмит формы поиска в полном интерфейсе
 */
function bumsFgsearchKeydown( evt )
{
	var keyCode = undefined == evt.which ? evt.keyCode : evt.which;
	if ( 13 == keyCode && $.trim(evt.target.value) != '' ) {
		evt.target.form.submit();
	}
}
//===========================================================================}}}
/*============================================================================*
 * vim: set expandtab tabstop=3 shiftwidth=3 foldmethod=marker:               *
 *   END OF FILE                                                              *
 *============================================================================*/ var bumsInformerTimeoutId = 0;
var bumsInformerPrevValue = null;
//------------------------------------------------------------------------------

//{{{ bumsInformerPerformSearch
/**
 * Запуск процесса информерского поиска
 */
function bumsInformerPerformSearch()
{
	if ( $( '#informerSearchIsLocal' ).attr( 'checked' ) ) 
	{
		if ( $('#iSearch .l').is('.disabled') ) {
			bumsInformerHighlight();
		}
		else {
			bumsInformerUpdateTable();
		}
	}
	else {
		bumsGsearchPerform();
	}
}
//===========================================================================}}}
//{{{ bumsInformerHighlight
/**
 * Подсвечивает строки на странице, совпадающие со значением строки поиска информера
 */
function bumsInformerHighlight( s )
{
	if ( typeof( s ) == 'undefined' ) {
		s = $.trim( $('#iSearch .s').get(0).value );
	}
	var sp, re;
// сбрасываем предыдущее выделение...
// ... текста
	$('.ihlBlock hib').each( function() {
		sp = document.createTextNode(this.getAttribute('informer-replaced'));
		this.parentNode.insertBefore(sp, this);
		this.parentNode.removeChild(this);
	});
// ... input'ов
	$('.ihlBlock input[@value]').removeClass('ihl');
// ... select'ов
	$('.ihlBlock select').removeClass('ihl');
//------------------------------------------------------------------------------
// если строка поиска непустая, то делаем подстветку...
	if ( s != '' )
	{
	// ... обычного текста
		re = new RegExp( "("+RegExp.escape(s)+")", "ig" );
		$('.ihlBlock').textNodes().each( function() {
			if ( re.test(this.nodeValue) )
			{
				sp = document.createElement('hib');
				sp.innerHTML = this.nodeValue.replace(re, '<q>$1</q>');
				sp.setAttribute('informer-replaced', this.nodeValue);
				this.parentNode.insertBefore(sp, this);
				this.parentNode.removeChild(this);
			// сука-ослик - тупой, ему надо сбрасывать счётчик
				re.lastIndex = 0;
			}
		});
	//---------------------------------------------------------------------------
	// ... input'ов
		re = new RegExp( RegExp.escape(s), "i" );
		$('.ihlBlock input[@value]').each( function() {
			if ( re.test(this.value) ) {
				$(this).addClass('ihl');
			}
		});
	//---------------------------------------------------------------------------
	// ... select'ов
		$('.ihlBlock select > option').each( function() {
			if ( re.test(this.innerHTML) ) {
				$(this.parentNode).addClass('ihl');
			}
		});
	}
//------------------------------------------------------------------------------
	$('#iSearch').removeClass('progress');
}
//===========================================================================}}}
//{{{ bumsInformerLookClick
/**
 * Обработчик клика по "лупе" информера
 */
function bumsInformerLookClick(ev)
{
	var sv = $('#informerSearchVariants');
	sv.toggle();
	if ( sv.css('display') == 'block' ) {
		$(document).one('click', function() { $('#informerSearchVariants').hide(); } );
	}
	ev.stopPropagation();
}
//===========================================================================}}}
//{{{ bumsInformerSetOption
/**
 * Обработка выбора опции информера
 */
function bumsInformerSetOption( elem, optId )
{
	var sv = $('#informerSearchVariants');
	var prev = sv.find('.current');
	if ( prev.get(0) != elem )
	{
		prev.removeClass('current');
		$(elem).addClass('current')
		$('#iSearch .l').attr('curtype', optId);
		sv.hide();
		var s = $('#iSearch .s');
		s.inlineHint(elem.innerHTML);
		if ( $.trim( s.get(0).value ) != s.attr('inline-hint') ) {
			bumsInformerUpdateTable();
		}
	}
}
//===========================================================================}}}
//{{{ bumsInformerHotKey
/**
 * Делает горячую клавишу для перехода на строку поиска информера (Alt+q или Ctrl+q)
 */
function bumsInformerHotKey( ev )
{
	var keyCode = undefined == ev.which ? ev.keyCode : ev.which;
	if ( (ev.altKey == true || ev.ctrlKey) && keyCode == 81) {
		$('#iSearch .s').focus();
	}
}
//===========================================================================}}}
/* Разные иконки с количеством чего-нибудь */
var bumsInformerIcons = {
	informerCommentsUnread: { 
		popup: sdfUrlTo( 'BumsTaskC_Comment', 'listUnread' ), 
		url: sdfUrlTo( 'BumsTaskC_Task', 'list' ),
		text: 'You have %1% unread comments.'
	},
	informerFlags: { 
		popup: sdfUrlTo( 'BumsCommonC_Flag', 'list' ), 
		url: sdfUrlTo( 'BumsTaskC_Task', 'list' ),
		text: 'You have %1% flags.' 
	},
	informerTasksRejected: { 
		popup: sdfUrlTo( 'BumsCommonC_Informer', 'tasks', {rejected:1} ), 
		url: sdfUrlTo( 'BumsTaskC_Task', 'list' ),
		text: 'You have %1% rejected tasks.' 
	},
	informerTasksNew: { 
		popup: sdfUrlTo( 'BumsCommonC_Informer', 'tasks', {isNew:1} ), 
		url: sdfUrlTo( 'BumsTaskC_Task', 'list' ),
		text: 'You have %1% new tasks.' 
	}
};
//{{{ bumsInformerIconShow
/**
 * Показывает окошко со списком, соответствующим иконке в информере
 */
function bumsInformerIconClick( evt )
{
	var target = evt.target ? evt.target : evt.srcElement;
	jIcon = $( target );
	if ( ! jIcon.is( '.informer-icon' ) ) {
		jIcon = jIcon.parents( '.informer-icon' );
	}
	sdfWindow.setClass( 'sdf-wnd-bubble wnd-info' );
	sdfWindow.load( bumsInformerIcons[jIcon[0].id].popup );
	var offset = $( target ).offset();
	sdfWindow.show( offset.left+target.offsetWidth/2+21, 20, 'br', 'bottom' );
	$(sdfWindow.jBody).click( function(evt) {evt.stopPropagation(); } )
	$(document).one( 'click', function(){ sdfWindow.hide(); } )
	if ( evt.stopPropagation ) {
		evt.stopPropagation();
	}
	else {
		evt.cancelBubble = true;
	}
}
//===========================================================================}}}
//{{{ bumsInfromerIconUpdate
/**
 * Обновляет информацию о количестве в иконке информера
 */
function bumsInfromerIconUpdate( Id, Cnt )
{
	var jIcon = $('#'+Id);
	var curCnt = jIcon.length > 0 ? parseInt( jIcon.children('span').text() ) : 0;
	if ( Cnt == curCnt ) {
		return;
	}
	if ( Cnt == 0 && jIcon.length > 0 ) 
	{
		jIcon.remove();
		return;
	}
	var text = sdfGetText( bumsInformerIcons[Id].text, Cnt, Cnt );
	if ( jIcon.length == 0 ) {
		jIcon = $('<div id="'+Id+'" onclick="bumsInformerIconClick(event)" class="informer-icon" title="'+text+'" class=""><span>'+Cnt+'</span></div>').appendTo( '#informerIcons' );
	}
	else 
	{
		jIcon.children('span').text( Cnt );
		jIcon.attr( 'title', text );
		if ( Cnt >= 10 ) {
			jIcon.addClass( 'digits2' );
		}
	}
// Это чтобы нас заметили :)
	jIcon.fadeOut( 'fast' ).fadeIn('fast').fadeOut( 'fast' ).fadeIn('fast').fadeOut( 'fast' ).fadeIn('fast').fadeOut( 'fast' ).fadeIn('fast');
}
//===========================================================================}}}
var bumsGsearchUrl = '';

//{{{ bumsGsearchPerform
/**
 * Запуск процесса быстрого глобального поиска
 */
function bumsGsearchPerform()
{
// Ищем только слова не менее 2 символов в длину
	if ( $.trim( $('#iSearch .s').val() ).length > 1 ) 
	{
		bumsInformerSearchWnd.load( bumsGsearchUrl, { qs: $('#iSearch .s').val() }, bumsGsearchCallback );
		offset = $('#iSearch .l').offset();
		bumsInformerSearchWnd.show( offset.left-15, 20, 'bl', 'bottom' );
	}
	else
	{
		bumsInformerSearchWnd.hide();
		$('#iSearch').removeClass('progress');
	}
}
//===========================================================================}}}
//{{{ bumsGsearchCallback
/**
 * Доп действия после получения результатов поиска
 */
function bumsGsearchCallback()
{
	$(document).one('click', function() { bumsInformerSearchWnd.hide(); } );
	bumsInformerSearchWnd.jBody.find( 'div a' ).hover( bumsGsearchResultOver, bumsGsearchResultOut );
	$( '#iSearch' ).removeClass('progress');
	bumsInformerSearchWnd.jBody.find( 'div.no' ).click( function() { bumsInformerSearchWnd.hide(); } );
}
//===========================================================================}}}
//{{{ bumsGsearchEmptyClose
/**
 * Обработчик клика по пустой панели поиска
 */
function bumsGsearchEmptyClose()
{
	bumsInformerSearchWnd.hide();
}
//===========================================================================}}}
//{{{ bumsGsearchClick
/**
 * Обработчик клика по полю поискового запроса
 */
function bumsGsearchClick( evt )
{
	if ( bumsInformerSearchWnd.jEl.is(':visible') ) {
		evt.stopPropagation();
	}
}
//===========================================================================}}}
//{{{ bumsGsearchFocus
/**
 * Обработчик фокуса поля поискового запроса
 */
function bumsGsearchFocus( evt )
{
	if ( bumsInformerSearchWnd.jEl.is(':hidden') && $.trim($('#iSearch .s').val() ) != '' )
	{
		bumsInformerSearchWnd.show();
		$(document).one('click', function() { bumsInformerSearchWnd.hide(); } );
	}
}
//===========================================================================}}}
//{{{ bumsInformerKeydown
/**
 * Обработчик нажатия стрелочек вверх/вниз для навигации по списку результатов
 * Да и нажатие Enter тут обрабатывается
 */
function bumsInformerKeydown( evt )
{
	var keyCode = undefined == evt.which ? evt.keyCode : evt.which;
	if ( bumsInformerSearchWnd.jEl.is(':visible') && bumsInformerSearchWnd.jBody.find('div a').length > 0 )
	{
		if ( keyCode == 40 || keyCode == 38 )
		{
			var next = (keyCode == 40);
			if ( bumsInformerSearchWnd.jBody.find('div a.hover').length )
			{
				var x = bumsInformerSearchWnd.jBody.find('div a.hover');
				var s = next ? x.next() : x.prev();
				if ( ! s.length )
				{
					s = next ? x.parent().parent().next() : x.parent().parent().prev();
					if ( s.length) {
						s = next ? s.find('a:first-child') : s.find('a:last-child');
					}
				}
				if ( s.length )
				{
					x.removeClass('hover cur');
					s.addClass('hover cur');
				}
			}
			else 
			{
				var jLinks = bumsInformerSearchWnd.jBody.children('div').children('div').children('div').children('a');
				if ( next ) {
					$(jLinks[0]).addClass('hover cur');
				}
				else {
					$(jLinks[jLinks.length-1]).addClass('hover cur');
				}
			}
		}
		else if ( keyCode == 13 )
		{
			var href = bumsInformerSearchWnd.jBody.find('a.cur').attr('href');
			if ( href ) {
				document.location.href = href;
			}
			else {
				document.location.href = $.composeUrl( bumsFullGsearchUrl, { qs: $('#iSearch .s').val() } );
			}
		}
	}
}
//===========================================================================}}}
//{{{ bumsGsearchResultOver/Out
/**
 * Подсветка результата при наведении мышки
 */
function bumsGsearchResultOver(evt)
{
	bumsInformerSearchWnd.jBody.find('a.hover').removeClass('hover cur');
	var a = $(evt.target).is('a') ? $(evt.target) : $(evt.target).parents('a');
	a.addClass('hover');
}
function bumsGsearchResultOut(evt)
{
	var a = $(evt.target).is('a.hover') ? $(evt.target) : $(evt.target).parents('a.hover');
	a.removeClass('hover');
}
//===========================================================================}}}
//{{{ bumsInformerIsLocalChange()
/**
 * Изменение режима поиска
 */
function bumsInformerIsLocalChange()
{
	var s = $('#iSearch .s');
	if ( $( '#informerSearchIsLocal' ).attr( 'checked' ) ) 
	{
		if ( s.val().length >= 2 && ! s.is( '.inline-hint' ) )
		{
			if ( $('#iSearch .l').is('.disabled') ) {
				bumsInformerHighlight();
			}
			else {
				bumsInformerUpdateTable();
			}
		}
	}
	else 
	{
		if ( $.trim( s.get(0).value ) == s.attr('inline-hint') ) {
			s.attr( 'value', '' ).removeClass( 'inline-hint' );
		}
		s.focus();
		bumsGsearchPerform();
		if ( $('#iSearch .l').is('.disabled') ) {
			bumsInformerHighlight( '' );
		}
		else {
			bumsInformerUpdateTable( '' );
		}
	}
}
//===========================================================================}}}

$(document).ready(function(){
	bumsSearchInputRegister( 'iSearch', bumsInformerPerformSearch );
	$('#iSearch .l').click(bumsInformerLookClick);
	$(document).keydown( bumsInformerHotKey );
	//$('#iSearch').click( bumsGsearchClick );
	//$('#iSearch .s').focus( bumsGsearchFocus );
	$('#iSearch .s').keydown( bumsInformerKeydown );
	window.bumsInformerSearchWnd = new SdfWindow( 'qSearchWnd' );
	window.bumsInformerSearchWnd.setClass( 'sdf-wnd-bubble qsearch-results' );
});

/*============================================================================*
 * vim: set expandtab tabstop=3 shiftwidth=3 foldmethod=marker:               *
 *   END OF FILE                                                              *
 *============================================================================*/ /**
 * @package		sdf
 * @since      09.10.2007 15:24:18
 * @author     Eugene
 */

$(document).ready( function(){ window.sdfWindow = new SdfWindow(); } );

//{{{ SdfWindow()
/**
 * Конструктор
 * @since 09.10.2007 15:24:18
 * @author Eugene
 * @param string Id код элемента на странице
 */
function SdfWindow( Id )
{
	if ( typeof( Id ) == 'undefined' ) {
		Id = 'sdfWindow';
	}
	this.jEl = $( '<div class="sdf-wnd sdf-wnd-shadow" id="'+Id+'" style="display:none"><table class="sdf-wnd-tbl">'
			+'<tr><td class="sdf-wnd-tl"><div></div></td><td class="sdf-wnd-t"><img src="/i/0.gif"/></td><td class="sdf-wnd-tr"></td></tr>'
			+'<tr><td class="sdf-wnd-l"></td><td class="sdf-wnd-c"><div class="sdf-wnd-body"></div></td><td class="sdf-wnd-r"></td></tr>'
			+'<tr><td class="sdf-wnd-bl"></td><td class="sdf-wnd-b"><img src="/i/0.gif"/></td><td class="sdf-wnd-br"></td></tr>'
			+'</table></div>'
		).prependTo( document.body ).addClass();
	this.jBody = this.jEl.find( '.sdf-wnd-body' );
	this.className = 'sdf-wnd-shadow';
// Способ позиционирования элемента: по левому верхнему углу, по правому верхнему углу и т.д.
	this.pos = ''; // Варианты: tl(по умолчанию), tr, bl, br
// Фиксированное положение на странице
	this.fixed = ''; // Варианты: top, bottom ( считать сверху, считать снизу )
	this.left = 300;
	this.top = 300;
	this.maxHeight = 300;
	this.html = '';
};
//===========================================================================}}}

//{{{ Прототип sdfWindow
SdfWindow.prototype = {
	//{{{ show()
	/**
	 * Показать окно 
	 * @since  09.10.2007 15:53:18
	 * @author Eugene
	 * @param int X Позиция по X
	 * @param int Y Позиция по Y
	 * @param string
	 */
	show: function( X, Y, Pos, Fixed )
	{
		if ( typeof( Fixed ) == 'undefined' ) {
			Fixed = '';
		}
		if ( this.pos ) {
			this.jEl.removeClass( 'sdf-wnd-pos-'+this.pos );
		};
		this.pos = Pos ? Pos : '';
		if ( this.pos ) {
			this.jEl.addClass( 'sdf-wnd-pos-'+this.pos );
		}
		this.left = X;
		this.top = Y;
		this.fixed = Fixed;
		
		this.jEl.css( { left:'-10000px', top:'-10000px' } );
		this.jEl.show();
		this.tunePosition();
	},
	//===========================================================================}}}
	//{{{ load()
	/**
	 * Загрузить контент окна 
	 * @since  09.10.2007 15:56:18
	 * @author Eugene
	 */
	load: function( Url, Params, OnLoad )
	{
		this.jBody.html('<img src="/i/0.gif" width="25" height="20"/>');
		this.jEl.addClass( 'in-progress' );
		var me = this;
		if ( typeof( Params ) == 'function' ) {
			this.html = $.get( Url, function( html ) { me.html = html; me.onLoad(); Params(); } ); 
		}
		else {
			this.html = $.get( Url, Params, function( html ) { me.html = html; me.onLoad(); if ( OnLoad ) OnLoad(); } );
		}
	},
	//===========================================================================}}}
	//{{{ hide()
	/**
	 * Скрыть окно 
	 * @since  09.10.2007 16:43:18
	 * @author Eugene
	 */
	hide: function()
	{
		this.jEl.hide();
	},
	//===========================================================================}}}
	//{{{ html()
	/**
	 * Задать контент окна 
	 * @since  09.10.2007 17:02:18
	 * @author Eugene
	 */
	html: function( Html )
	{
		this.jBody.html( Html );
		this.onLoad();
	},
	//===========================================================================}}}
	//{{{ setClass()
	/**
	 * Задать класс окна (тип) 
	 * @since  09.10.2007 17:02:18
	 * @author Eugene
	 * @param string ClassName Название класса окна
	 */
	setClass: function( ClassName )
	{
		if ( this.className == ClassName ) {
			return false;
		}
		if ( this.className ) {
			this.jEl.removeClass( this.className );
		}
		this.jEl.addClass( ClassName );
		this.className = ClassName;
	},
	//===========================================================================}}}
	//{{{ tunePosition()
	/**
	 * Настроить позицию элемента на экране учитывая его размеры и значение this.pos 
	 * @since  15.10.2007 13:33:18
	 * @author Eugene
	 * @param boolean Delayed Нужно ли выждать время прежде чем настраивать позицию. Только для FF
	 */
	tunePosition: function( Delayed )
	{
		var top = this.top;
		var left = this.left;
		
	// Настраиваем размеры
		if ( this.maxHeight && this.jBody[0].offsetHeight > this.maxHeight ) {
			this.jBody.css( 'height', this.maxHeight+'px' );
		}
		else {
			this.jBody.css( 'height', 'auto' );
		}
		
	// Настраиваем позицию
		if ( this.pos ) 
		{
		// Для ie6 как всегда всё по-особенному
			if ( $.browser.msie && parseInt( $.browser.version ) < 7 && this.pos.indexOf( 'b' ) >= 0 && this.fixed == 'bottom' ) {
				top = this.top + this.jEl.height();
			}
		// Позиционируем по нижнему краю или по верхнему для случая fixed=bottom
			else if ( this.pos.indexOf( 'b' ) >= 0 && this.fixed != 'bottom' || this.pos.indexOf( 't' ) >= 0 && this.fixed == 'bottom' ) {
				top = this.top - this.jEl.height();
			}
		// Позиционируем по правому краю
			if ( this.pos.indexOf( 'r' ) >= 0 ) {
				left = this.left - this.jEl.width();
			}
		}
	// Если мы вылезаем за края экрана, нужно исправить
		if ( left < 20 ) {
			left = 20;
		}
		
		var windowWidth = $(window).width();
	// Опера неправильно определяет размер jEl, если окно выходит за рамки экрана. Используем чилдрена
		var elWidth = this.jEl.children().width();
		if ( left + elWidth + 10 > windowWidth ) {
			left = windowWidth - elWidth - 10;
		}
		
	// Обрабатываем fixed
		if ( this.fixed ) 
		{
			if ( $.browser.msie && parseInt( $.browser.version ) < 7 )
			{
				if ( this.fixed == 'bottom' ) 
				{
					this.jEl.css( { left:left+'px', bottom:''} );
					this.jEl[0].style.setExpression( 'top', "document.getElementsByTagName( 'body' )[0].scrollTop + document.getElementsByTagName( 'body' )[0].clientHeight - "+top+" + 'px'" );
				}
				else 
				{
					this.jEl.css( { left:left+'px', bottom:''} );
					this.jEl[0].style.setExpression( 'top', "document.getElementsByTagName( 'body' )[0].scrollTop + "+top+" + 'px'" );
				}
				document.recalc && document.recalc( true );
			}
			else 
			{
				this.jEl.css( 'position', 'fixed' )
				if ( this.fixed == 'bottom' ) {
					this.jEl.css( { left:left+'px', top:'', bottom:top+'px'} );			
				}
				else {
					this.jEl.css( { left:left+'px', top:top+'px', bottom:''} );
				}
			}
		}
		else {
			this.jEl.css( { left:left+'px', top:top+'px', bottom:''} );
		}

		if ( ( $.browser.mozilla ) && ( typeof(Delayed)!='undefined' && Delayed ) )
		{
			var me = this;
			setTimeout( function(){ me.tunePosition( false ); }, 100 );
			return;
		}

	},
	//===========================================================================}}}
	//{{{ onLoad()
	/**
	 * Вызывается при загрузке контента в окно 
	 * @since  09.10.2007 17:02:18
	 * @author Eugene
	 */
	onLoad: function()
	{
		this.jEl.removeClass( 'in-progress' );
		if ( this.pos ) {
			this.jEl.css( { left:'-10000px', top:'-10000px' } );
		}
		this.jBody.html( this.html );
		this.tunePosition( true );
	}
	//===========================================================================}}}
};

/*============================================================================*
 * vim: set expandtab tabstop=3 shiftwidth=3 foldmethod=marker:               *
 *   END OF FILE                                                              *
 *============================================================================*/ var divListSelectedRow = false;
var listUniqInt = 18181818;
var listDblClickUrl = '/';
var listLastClick = null;

function listGetSrcTr( evt )
{
	var el =  evt && evt.target ? evt.target : event.srcElement;
	var tagNameUpper = '';
	while ( el && el.tagName && ( el.tagName.toUpperCase() != 'TR' || ! el.id ) ) 
	{
		tagNameUpper = el.tagName.toUpperCase();
		if ( tagNameUpper == 'INPUT' || tagNameUpper == 'SELECT' || tagNameUpper == 'A' ) {
			return null;
		}
		el = el.parentNode;
	}
	if ( ! el || el.className == 'separator' ) {
		return null;
	}
	return el;
}

function listOnClick( evt )
{
	tr = listGetSrcTr( evt );
	if ( ! tr ) {
		return;
	}
	jTr = $(tr);
	jTable = jTr.parent();
	if ( jTable.is( '.multi-select' ) )
	{
		if ( evt.shiftKey ) 
		{
			if ( listLastClick && listLastClick != tr ) 
			{
				jLast = $( listLastClick );
				var jRows = jTable.children();
				var selected = [];
				var doSelect = false;
				for ( var i=0; i<jRows.length; i++ ) 
				{
					if ( jRows[i] == tr || jRows[i] == listLastClick ) 
					{
						doSelect = ! doSelect;
						selected.push( jRows[i] );
					}
					else if ( doSelect ) {
						selected.push( jRows[i] );
					}
				}
				var jSelected = $( selected );
			}
			else {
				var jSelected = $( tr );
			}
		}
		else {
			var jSelected = $( tr );
		}
		if ( evt.ctrlKey )
		{
			if ( jTr.is( '.selected' ) ) {
				jSelected.removeClass( 'selected' );
			}
			else {
				jSelected.addClass( 'selected' );
			}
		}
		else 
		{
			jTable.children( '.selected' ).removeClass( 'selected' );
			jSelected.addClass( 'selected' );
		}
	}
	else 
	{
		if ( jTr.is( '.selected' ) ) {
			jTr.removeClass( 'selected' );
		}
		else 
		{
			jTable.children( '.selected' ).removeClass( 'selected' );
			jTr.addClass( 'selected' );
		}
	}
	listLastClick = tr;
}

function listMouseDown( evt )
{
	if ( evt.shiftKey ) {
		return false;
	}
}

function listOnDblClick( evt )
{
	tr = listGetSrcTr( evt );
	if ( ! tr ) {
		return false;
	}
	document.location.href=listDblClickUrl.replace( listUniqInt, $( tr ).attr( 'oid') );
}

function listContextMenu( evt )
{
	if ( $('#listContextMenu').length ) {
		menu = $('#listContextMenu');
	}
	else 
	{
		menu = $("<div id='listContextMenu'></div>")
			.hide()
			.css({position:"absolute", zIndex:"500"})
			.appendTo("body")
			.bind("click", function(e) {
				e.stopPropagation();
			});
	}
	tr = listGetSrcTr( evt )
	if ( ! tr ) {
		return false;
	}
	menu.html( $('#listContextMenu' + tr.id.substr(1)).html() );
	menu.css( {"left":evt.pageX, "top":evt.pageY} ).show();
	$(document).one("click", function() {menu.hide()});
	return false;
}

//{{{ divListGetTarget
/**
 * Определяет строку дивовой таблицы, для которой произошло событие
 */
function divListGetTarget( evt )
{
	var el = evt.target;
	var name = el.nodeName.toUpperCase();
	while ( el && ( name != 'DIV' || ! $(el).is('.row') ) )
	{
		if ( name == 'INPUT' || name == 'SELECT' || name == 'A' ) {
			return false;
		}
		el = el.parentNode;
	}
	if ( ! el || el.className == 'separator' ) {
		return false;
	}
	return el;
}
//===========================================================================}}}
//{{{ divListOnClick
/**
 * Обработчик клика по строке дивовой таблицы
 */
function divListOnClick( evt )
{
	var target = divListGetTarget( evt );
	if ( ! target ) {
		return;
	}
	if ( divListSelectedRow ) {
		$(divListSelectedRow).removeClass('selected'); 
	} 
	if( divListSelectedRow !== target ) 
	{
		$(target).addClass('selected'); 
		divListSelectedRow = target;
	} else {
		divListSelectedRow = null;
	}
}
//===========================================================================}}}
//{{{ divListOnClick
/**
 * Обработчик даблклика по строке дивовой таблицы
 */
function divListOnDblClick( evt )
{
	var target = divListGetTarget( evt );
	if ( ! target ) {
		return false;
	}
	document.location.href=listDblClickUrl.replace( listUniqInt, $( target ).attr( 'oid') );
}
//===========================================================================}}}
//{{{ bumsDelayedResize
/**
 * Обеспечивает отложенное действие события onResize для ослика, чтобы не тормозило
 */
var bumsResizeTimeoutId = 0;
function bumsDelayedResize(ev)
{
	if ( $.browser.msie )
	{
		if ( bumsResizeTimeoutId != 0 ) {
			clearTimeout( bumsResizeTimeoutId );
		}
		bumsResizeTimeoutId = setTimeout( bumsResizeDivTable, 200 );
	}
	else {
		bumsResizeDivTable();
	}
}
//===========================================================================}}}

/*============================================================================*
 * vim: set expandtab tabstop=3 shiftwidth=3 foldmethod=marker:               *
 *   END OF FILE                                                              *
 *============================================================================*/ function bumsFilterExpanderClick( Obj )
{
	var jItem = $( Obj ).parents( '.item' );
	jItem.toggleClass( 'expanded' );
	bumsFilterUpdateVisibility( jItem );
}

function bumsFilterUpdateVisibility( jItem )
{
	var deep = parseInt( jItem.attr( 'deep' ) );
	var jSubItem = jItem.next( '.item' );
// Если нет значка плюс/минус, ничего не делаем
	if ( jItem.find( '.expander' ).length == 0 ) {
		return jSubItem;
	}
	if ( jItem.is( '.expanded' ) ) 
	{
		while ( jSubItem.length && parseInt( jSubItem.attr( 'deep' ) ) > deep ) 
		{
			jSubItem.show();
			jSubItem = bumsFilterUpdateVisibility( jSubItem );
		}
	}
	else 
	{
		while ( jSubItem.length && parseInt( jSubItem.attr( 'deep' ) ) > deep ) 
		{
			jSubItem.hide();
			jSubItem = jSubItem.next( '.item' );
		}
	}
	return jSubItem;
}

function bumsFilterSaveExpand( Obj, Controller, Action, Pref )
{
	var jExpanded = $( Obj ).parents( '.filter' ).find( '.item' ).filter( '.expanded' );
	var val = '';
	for ( var i=0; i < jExpanded.length; i++ ) 
	{
		if ( val ) {
			val = val + ',';
		}
		val = val + $( jExpanded[i] ).attr( 'oid' );  
	}
	$.post( sdfUrlTo( 'SdfCommonC_Pref', 'update', '.easy' ), { ctrl:Controller, act:Action, name:Pref, value:val } );
}

$(document).ready(function(){
	var jItems = $('.filter').find( '.item' ).filter( '.deep0' );
	for ( var i=0; i < jItems.length; i++ ) {
		bumsFilterUpdateVisibility( $( jItems[i] ) );
	}
} ); /**
 * @package		sdf
 * @subpackage	widget
 * @since      16.07.2007 13:29:18
 * @author     Eugene
 */

//{{{ sdfMultiSelect()
/**
 * Конструктор
 * @since  16.07.2007 16:01:18
 * @author Eugene
 * @param Element|Id El Элемент на странице или его id
 * @param array Items Список элементов
 */
function SdfMultiSelect( El, Items, InputName, Value )
{
	this.items = Items;
	this.value = Value;
	
	this.filters = [];
	this.filterDefault = 0;
	
	this.template = '\'+items[i][0]+\'';
	
	this.focusByHand = false;
	
// Запоминаем некоторые основные элементы
	this.jEl = $(El);
	this.jOptions = this.jEl.find( '.options' );
	this.jSelected = this.jEl.find( '.selected' );
	this.jFilters = this.jEl.find( '.filters' );
	this.jBtnSelect = this.jEl.find( '.btn-select' );
	this.jBtnDeSelect = this.jEl.find( '.btn-deselect' );
	
	$('<input type="hidden" class="select-value" name="'+InputName+'" value="'+Value+'" />').appendTo( this.jEl );
	this.jValue = this.jEl.children( 'input.select-value' );

	$('<input type="text" class="key-hook" style="font-size: 1px; border-width:0; width:0" readonly="readonly" />').appendTo(this.jEl);
	this.jKeyHook = this.jEl.children( 'input.key-hook' );

// Вешаем события
	var me = this;
	this.jEl.keydown( function(evt){ me.keyDown(evt); } );
	this.jOptions.click( function(evt){ me.optionClick(evt); } );
	this.jOptions.dblclick( function(evt){ me.optionDblClick(evt, 'options'); } );
	this.jSelected.click( function(evt){ me.optionClick(evt); } );
	this.jSelected.dblclick( function(evt){ me.optionDblClick(evt, 'selected'); } );
	this.jFilters.click( function(evt){ me.filterClick(evt); } );
	this.jBtnSelect.click( function(evt){ me.addItems(); } );
	this.jBtnDeSelect.click( function(evt){ me.deleteItems(); } );
};
//===========================================================================}}}
//{{{ Прототип sdfMultiSelect
SdfMultiSelect.prototype = {
	//{{{ init()
	/**
	 * Инициализация работы элемента 
	 * @since  16.07.2007 16:12:18
	 * @author Eugene
	 */
	init: function() 
	{
	// Заполняем фильтры
		var html = '';
		
		if ( this.filters.length > 0 )
		{
			for ( var i=0, cnt=this.filters.length; i<cnt; i++ ) {
				if ( this.filters[i].title ) {
					html += '<div class="filter" f="'+i+'">'+this.filters[i].title+'</div>';
				}
				else if ( i < cnt-1 ) {
					html += '<div class="separator">&#160;</div>';
				}
			}
			this.jFilters.children('.select').html( html );
		}
		this.setValue( this.value );
		this.setFilter( this.filterDefault );
	},
	//===========================================================================}}}
	//{{{ setTemplate()
	/**
	 * Задать шаблон 
	 * @since  16.07.2007 16:12:18
	 * @author Eugene
	 * @param string Template код шаблона
	 */
	setTemplate: function( Template ) 
	{
		this.template = Template;
	},
	//===========================================================================}}}
	//{{{ setFilters()
	/**
	 * Задать доступные фильтры 
	 * @since  16.07.2007 16:12:18
	 * @author Eugene
	 * @param array Filters массив фильтров
	 */
	setFilters: function( Filters ) 
	{
		this.filters = Filters;
	},
	//===========================================================================}}}
	//{{{ setFilterDefault()
	/**
	 * Задать фильтр по умолчанию 
	 * @since  16.07.2007 16:12:18
	 * @author Eugene
	 * @param int String номер фильтра
	 */
	setFilterDefault: function( Filter ) 
	{
		this.filterDefault = Filter;
	},
	//===========================================================================}}}
	//{{{ init()
	/**
	 * Устанавливаем фокус на элемент 
	 * @since  16.07.2007 16:12:18
	 * @author Eugene
	 */
	focus: function()
	{
		this.setFocus( true );
	},
	//===========================================================================}}}
	//{{{ filterClick()
	/**
	 * Обработка клика на фильтре
	 * @since  10.07.2007 16:21:18
	 * @author Eugene
	 * @param Event event
	 */
	filterClick: function( event )
	{
		var el =  event.target ? event.target : event.srcElement;
		if ( !$(el).is( '.filter' ) ) {
			return false;
		}
		this.applyFilter( $(el).attr('f') );
		this.jFilters.children('.select').children( '.current' ).removeClass( 'current' );
		$( el ).addClass( 'current' );
	},
	//===========================================================================}}}
	//{{{ setFilter()
	/**
	 * Установить фильтр
	 * @since  10.07.2007 16:21:18
	 * @author Eugene
	 * @param string Filter Фильтр, который нужно установить
	 */
	setFilter: function( Filter )
	{
		var jFilters = this.jFilters.children('.select').children( '.filter' );
		var cnt = jFilters.length;
		for ( var i=0; i<cnt; i++ ) 
		{
			if ( $(jFilters[i]).attr( 'f' ) == Filter ) 
			{
				jFilters.removeClass( 'current' );
				$(jFilters[i]).addClass( 'current' );
				this.applyFilter();
				return true;
			}
		}
		this.applyFilter();
	},
	//===========================================================================}}}
	//{{{ applyFilter()
	/**
	 * Отобразить элементы, удовлетворяющие данному фильтру (и текущему текстовому фильтру, если он задан) 
	 * @since  10.07.2007 16:23:18
	 * @author Eugene
	 * @param string Filter Название фильтра. Если не задано, берется выделенный в данный момент фильтр.
	 */
	applyFilter: function( Filter )
	{
		var itemsFiltered = new Object();
		if ( this.filters.length > 0 )
		{ 
			if ( Filter == undefined ) 
			{
				var jFilter = this.jFilters.children('.select').children( '.current' );
				if ( jFilter.length ) {
					Filter = jFilter.attr( 'f' );
				}
				else {
					return false;
				}
			}
		// Фильтруем фильтром
			var oFilter = this.filters[Filter];
			var i = 0;
			var f = 0;
			var items = this.items;
			var code = 'for (i in items){f='+oFilter.code+';if (f){itemsFiltered[i]=items[i]}}';
			eval(code);
			delete( itemsFiltered[0] );
		}
		else 
		{
			for ( i in this.items ) 
			{
				if ( i != 0 ) {
					itemsFiltered[i] = this.items[i];
				}
			}
		}
	// Убираем элементы, которые уже выбраны
		var selected = this.getSelected();
		for ( i in selected ) {
			delete( itemsFiltered[i] );
		}
		this.jOptions.children('.select' ).html( this.getOptionsHtml( itemsFiltered ) );
	},
	//===========================================================================}}}
	//{{{ getOptionsHtml()
	/**
	 * Получить html, содержащий данные элементы 
	 * @since  10.07.2007 16:23:18
	 * @author Eugene
	 * @param array Options Список элементов, которые нужно отобразить в options
	 */
	getOptionsHtml: function( Items )
	{
		var html = '';
		var items = Items;
		var i = 0;
		var code = 'for (i in items){if (i==0)continue;html+=\'<div class="opt" id="o\'+i+\'">'+this.template+'</div>\';}';
		eval( code );
		
		return html;
	},
	//===========================================================================}}}
	//{{{ optionClick()
	/**
	 * Обработка клика по опции
	 * @since  10.07.2007 16:28:18
	 * @author Eugene
	 * @param Event event
	 */
	optionClick: function( event )
	{
		var el =  event.target ? event.target : event.srcElement;
		this.focus();
	// Находим опцию, на которой кликнули
		while ( el && ! $(el).is('.opt') ) {
			el = el.parentNode;
		}
		if ( ! el ) {
			return false;
		}
		var td = el;
		while ( td && ! $(td).is('td') ) {
			td = td.parentNode;
		}
		if ( ! event.ctrlKey ) {
			$( td ).children('.select').children('.current').removeClass( 'current' );
		}
		if ( event.shiftKey && $(td).attr('lastClick') && el.id != $(td).attr('lastClick') ) 
		{
			var jOptions = $(td).children('.select').children('.opt');
			var cur = false;
			var lastClick = $(td).attr('lastClick');
			for (i=0, cnt=jOptions.length; i<cnt; i++)
			{
				if (cur) {
					$(jOptions[i]).addClass('current');
				}
				else if ($(jOptions[i]).is('.current')) {
					$(jOptions[i]).removeClass('current');
				}
				if (jOptions[i].id == el.id || jOptions[i].id == lastClick)
				{
					cur = !cur;
					$(jOptions[i]).addClass('current');
				}
			}
		}
		else
		{
			$(el).toggleClass( 'current' );
			if ($(el).is('.current')) {
				$(td).attr('lastClick', el.id);
			}
			else {
				$(td).attr('lastClick', '');
			}
		}
		this.updateButtons();
	},
	//===========================================================================}}}
	//{{{ optionDblClick()
	/**
	 * Обработка двойного клика по опции
	 * @since  10.07.2007 16:28:18
	 * @author Eugene
	 * @param Event event
	 * @param string Target По какому элементу кликнули: 'selected' или 'options'
	 */
	optionDblClick: function( event, Target )
	{
		this.focus();
		if ( Target == 'options' ) {
			this.addItems();
		}
		if ( Target == 'selected' ) {
			this.deleteItems();
		}
	},
	//===========================================================================}}}
	//{{{ keyDown()
	/**
	 * Обработка нажатия клавиши на всем виджите
	 * @since  10.07.2007 16:34:18
	 * @author Eugene
	 * @param Event event
	 */
	keyDown: function( event )
	{
		if ( event.keyCode == 13 ) // "Enter"
		{
			this.addItems();
		}
		if ( event.keyCode == 46 ) // "Delete"
		{
			this.deleteItems();
		}
	},
	//===========================================================================}}}
	//{{{ setFocus()
	/**
	 * Установить фокус на виджет (специальный инпут, предназначенный для ловли нажатий клавиш)
	 * @since  10.07.2007 16:34:18
	 * @author Eugene
	 * @param bool FocusByHand Передать информацию о том, что мы установили фокус руками
	 */
	setFocus: function( FocusByHand )
	{
		this.focusByHand = FocusByHand;
		this.jKeyHook.focus();
	},
	//===========================================================================}}}
	//{{{ addItems()
	/**
	 * Добавить элементы в выбранные 
	 * @since  10.07.2007 16:34:18
	 * @author Eugene
	 */
	addItems: function()
	{
		this.jOptions.children('.select').children('.current')
			.removeClass('current')
			.appendTo( this.jSelected.children('.select') );
		this.updateButtons();
		this.updateValue();
	},
	//===========================================================================}}}
	//{{{ deleteItems()
	/**
	 * Убрать элементы из выбранных 
	 * @since  10.07.2007 16:34:18
	 * @author Eugene
	 */
	deleteItems: function()
	{
		this.jSelected.children('.select').children('.current').remove();
		this.applyFilter();
		this.updateButtons();
		this.updateValue();
	},
	//===========================================================================}}}
	//{{{ updateButtons()
	/**
	 * Обновить кнопочки 
	 * @since  10.07.2007 16:34:18
	 * @author Eugene
	 */
	updateButtons: function()
	{
		if ( this.jOptions.children('.select').children('.current').length ) {
			this.jBtnSelect.addClass( 'act' );
		}
		else {
			this.jBtnSelect.removeClass( 'act' );
		}

		if ( this.jSelected.children('.select').children('.current').length ) {
			this.jBtnDeSelect.addClass( 'act' );
		}
		else {
			this.jBtnDeSelect.removeClass( 'act' );
		}
	},
	//===========================================================================}}}
	//{{{ getSelected()
	/**
	 * Получить список выбранных элементов 
	 * @since  10.07.2007 16:34:18
	 * @author Eugene
	 * @return array
	 */
	getSelected: function()
	{
		var jOptions = this.jSelected.children('.select').children('.opt');
		var items = {};
		var cnt = jOptions.length;
		for ( var i=0; i<cnt; i++ ) {
			items[jOptions[i].id.substr(1)] = this.items[jOptions[i].id.substr(1)];
		}
		return items;
	},
	//===========================================================================}}}
	//{{{ updateValue()
	/**
	 * Обновить значение в элементе input для сохранения 
	 * @since  10.07.2007 16:34:18
	 * @author Eugene
	 * @return array
	 */
	updateValue: function()
	{
		var val = '';
		var selected = this.getSelected();
		for ( var i in selected ) {
			val += ','+i;
		}
		if ( val ) {
			val = val.substr( 1 );
		}
		this.jValue.attr( 'value', val );
	},
	//===========================================================================}}}
	//{{{ setValue()
	/**
	 * Установить значение 
	 * @since  10.07.2007 16:34:18
	 * @author Eugene
	 */
	setValue: function( Value )
	{
		var items = {};
		for ( var i=0; i<Value.length; i++ ) {
			items[Value[i]] = this.items[Value[i]];
		}
		this.jSelected.children('.select' ).html( this.getOptionsHtml( items ) );
		this.updateValue();
	}
	//===========================================================================}}}
};


 function bumsListLoadCallback( html )
{
	listDblClickUrl = bumsTaskListDblClickUrl;
	$('#listTable').html( html );
	$('#listTable').click(listOnClick).click(bumsTaskListClickHandler).dblclick(listOnDblClick);		
	
	$('#iSearch').removeClass('progress');
	
	$( '#severitySelect' ).click( bumsTaskSeveritySelectOnClick );
	sevSelect = $( '#severitySelect' ).appendTo( 'body' );

	// подсветка при наведение для осла
	if ( $.browser.msie )
	{
		$("#severitySelect div").hover(function(){
			$(this).addClass("hover");
		},function(){
			$(this).removeClass("hover");
		});			
	}
// Дополнительные инициализации
	bumsListInitElements( $('#listTable') );
// Обновляем состояние кнопочек в правом меню
	bumsTaskRightMenuUpdate();	
}

function bumsListInitElements( JEl, IsRows )
{
	JEl.find( 'div.sev' ).click( bumsTaskSeverityOnClick );
	
	// подсветка при наведение для осла
	if ( $.browser.msie )
	{
		if ( typeof( IsRows ) == 'undefined' || ! IsRows ) {
			JEl = JEl.find( 'tr' );
		}
		JEl.hover(function(){
			//$(this).addClass("hover");
			this.style.backgroundColor = '#F8EEC0';
		},function(){
			//$(this).removeClass("hover");
			this.style.backgroundColor = '';
		});
	}
}

function bumsTaskListClickHandler( Event ) 
{
	bumsTaskRightMenuUpdate();
}

function bumsTaskRightMenuUpdate()
{
	var jTask = $('#listTable tr.selected');
	if ( jTask.length == 1 ) {
		var taskId = jTask.attr( 'oid' );
		$( "#taskToolbarSubtask a" ).attr( 'href', sdfUrlTo( 'BumsTaskC_Task', 'add', { SuperTask:taskId } ) );
		$( "#taskToolbarWrite a" ).attr( 'href', sdfUrlTo( 'BumsCommonC_Message', 'add', { Task:taskId } ) );
		$( "#taskRightMenu div.disabled").removeClass( 'disabled' );
		if ( jTask.is( '.archived' ) ) {
			$( "#taskToolbarSubtask" ).addClass( 'disabled' ).children('a').removeAttr('href');
		}
	}
	else {
		$( "#taskRightMenu div.icon:not(.global)").addClass( 'disabled' ).children('a').removeAttr( 'href' );
	}
}

function bumsSubTasksToggle( Event )
{
	var target = listGetSrcTr( Event );
	if ( ! target ) {
		return;
	}
	jTask = $( target );		
	var taskId = jTask.attr( 'oid' );
	var deep = jTask.attr('deep');
	deep = deep ? parseInt( deep ) : 0
	
	jTask.toggleClass( 'expanded' );
	if ( ! jTask.is( '.loaded' ) )
	{
		jTask.addClass( 'loaded' );
		var folder = typeof(bumsTaskListFolder)=='undefined' ? '' : bumsTaskListFolder;
		var status = typeof(bumsTaskListStatus)=='undefined' ? '' : bumsTaskListStatus;
		var employee = typeof(bumsTaskListEmployeeId)=='undefined' ? '' : bumsTaskListEmployeeId;
		var jWaiting = $( '<tr class="waiting"><td colspan="5"><span>'+sdfGetText( 'Please wait...' )+'</span></td></tr>' ).insertAfter( jTask );
		$.post( sdfUrlTo( 'BumsTaskC_Task', 'subTasks' ), { deep:deep+1, taskid: taskId, folder:folder, status:status, employee:employee }, function(html) 
		{
			jWaiting.remove();
			var tt = new Date().getTime();
		// следующая строчка тормозит в IE6 sp2
		// var jRows = $( html ).insertAfter( jTask );
			jTask.after( html );
 			jRows = jTask.nextAll( '[@deep=' + (deep + 1) + ']' );
			bumsListInitElements( jRows.filter( 'tr' ), true );
			bumsSubTaskUpdateVisibility( jTask );
		} );
	}
	else {
		bumsSubTaskUpdateVisibility( jTask );
	}
	if ( Event.stopProragation ) {
		Event.stopProragation();
	} else {
		Event.cancelBubble = 1;
	}
}

function bumsSubTaskUpdateVisibility( jTask )
{
	var deep = parseInt( jTask.attr( 'deep' ) );
	var jSubTask = jTask.next();
	if ( jTask.is( '.expanded' ) ) 
	{
		while ( jSubTask && parseInt( jSubTask.attr( 'deep' ) ) > deep ) 
		{
			jSubTask.show();
			if ( jSubTask.is( '.loaded' ) ) {
				jSubTask = bumsSubTaskUpdateVisibility( jSubTask );
			}
			else {
				jSubTask = jSubTask.next();
			}
		}
	}
	else 
	{
		while ( jSubTask && parseInt( jSubTask.attr( 'deep' ) ) > deep ) 
		{
			jSubTask.hide();
			jSubTask = jSubTask.next();
		}
	}
	return jSubTask;
}

function bumsTaskSeverityOnClick( Event ) 
{	
	if ( IS_DEMO ) {
		return false;
	}
	var el = Event.target;
	var target = listGetSrcTr( Event );
	if ( ! target ) {
		return;
	}
	jTask = $( target );		
	if( jTask.length && $(this).children('img').length )
	{
		pos = $( el ).absPos();
		var sevSel = $( '#severitySelect' );
		sevSel.attr( 'oid', jTask.attr( 'oid' ) );
		sevSel.css( {left: (pos.x - sevSel.width() + 24) + 'px', top: (pos.y + 24) + 'px'} )
		sevSel.show();	
		$(document).one( 'click', function() {sevSel.hide()});
		Event.stopPropagation();
	}
}

function bumsTaskSeveritySelectOnClick( evt ) 
{
	sevSel = $( '#severitySelect' );
	newSevVal = $( evt.target ).attr('sev');
// внимание. тут может быть найдено несколько элементов, т.к. в списке задач благодаря подзадачам
// одна и та же задача может встречаться несколько раз
	oid = sevSel.attr( 'oid' );
	sevEl = $('#listTable tr.item[@oid = ' + oid + '] div.sev' );
// удаляем старое значение. оно записано в атрибуте sev того дива, для которого выпало меню
	oldSev = sevEl.attr( 'sev' );
	if ( typeof oldSev != 'undefined' && bumsTaskSeverityMasters[oldSev] ) {
		sevEl.removeClass( bumsTaskSeverityMasters[oldSev] );
	}
	if ( bumsTaskSeverityMasters[newSevVal] ) {
		sevEl.addClass( bumsTaskSeverityMasters[newSevVal] );
	}
	$.postJSON( bumsTaskSetSeverityUrl, { severity: newSevVal, taskid: oid} );
// меняем текущее значение
	sevEl.attr( 'sev', newSevVal );
	sevEl.attr( 'title', $( evt.target ).attr('title') );
	sevSel.removeAttr( 'oid' );		
	sevSel.hide();
	evt.stopPropagation();			
}

function bumsTaskWriteMessage()
{
	curTaskId = $( '#listTable' ).children( '.selected' ).attr( 'oid' );
	composeMessage( {task:curTaskId} );
}

/* Работа с метками */
function bumsTaskTagTreeOnLoad( ReloadTasks )
{
	$('div.tag').bind('contextmenu', bumsTaskTagContextMenu).click( bumsTaskTagClik );
	if ( typeof( ReloadTasks ) != 'undefined' && ReloadTasks ) {
		$.get( bumsTaskTableUrl, bumsTaskTableUrlParams, bumsListLoadCallback );
	}
}

function bumsTaskTagContextMenu( evt ) 
{
	if ( IS_DEMO ) {
		return false;
	}
	var el = evt ? evt.target : null;
	while ( el && el.className.indexOf('tag') == -1 ) {
		el = el.parentNode;
	}
	if ( el ) 
	{
		if ( $('#tagTreeContextMenuContainer').length ) {
			menu = $('#tagTreeContextMenuContainer');
		}
		else 
		{
			menu = $('<div id="tagTreeContextMenuContainer" style="border:1px solid black;background:white;padding:3px;cursor:pointer;"><\/div>')
						.hide()
						.css({position:"absolute", zIndex:"500"})
						.appendTo("body")
						.bind("click", function(e) {				
							//e.stopPropagation();
						});
		}		
		var hideHtml = '<div onclick="bumsTagHide(\'' + el.id + '\', \'true\')" class="tagTreeContextMenu" style="text-decoration:underline">'+sdfGetText( 'Hide' )+'<\/div>';
		var unHideHtml = '<div onclick="bumsTagHide(\'' + el.id + '\', \'false\')" class="tagTreeContextMenu" style="text-decoration:underline">'+sdfGetText( 'Unhide' )+'<\/div>';
		 
		menu.html( el.className.indexOf( 'hidden' ) == -1 ? hideHtml : unHideHtml );
		menu.css( {"left":evt.pageX, "top":evt.pageY} ).show();
		$(document).one("click", function() { menu.hide()});
	}				
	if ( evt ) {
		evt.stopPropagation();
	}
	return false;
}

function bumsTagDelete( path ) 
{
	var tagId = path.substring( path.lastIndexOf('_') + 1, path.length );
	$.postJSON( bumsTaskTagDeleteJsonUrl, {tagId: tagId},
		function () 
		{
			$('#tagTreeContainer').load( bumsTaskTagTreeLoadUrl, {}, function(){ bumsTaskTagTreeOnLoad( true ) } );	
		}  
	);
}

function bumsTagHide( path, hidden ) 
{
	$('#tagTreeContainer').load( bumsTaskTagTreeLoadUrl, {node_path: path.substr(3), hidden: hidden}, function(){ bumsTaskTagTreeOnLoad( $('#tag'+path).is('.tag-checked') ) } );	
}

function bumsTagTreeShowHideLinkClicked( hdivId ) 
{	
	el = $( '#' + hdivId )[0]; 
	$('#tagTreeContainer').load( bumsTaskTagTreeLoadUrl, {node_path: hdivId.substr(6), hidden_opened: el.style.display == 'none' }, function(){ bumsTaskTagTreeOnLoad( $(el).find('.tag-checked').length ) } );
	$( el ).toggle();		
}

function bumsTaskTagCreateDialog()
{
	jqobj = $("#new_tag_d"); 
	jqobj.toggle();		
	if( jqobj.get(0).style.display != 'none' )
	{
		$("#new_tag_i").focus();
	}
}

function bumsTaskTagCreate( ev, inp )
{
	if ( ev.keyCode == 13 )
	{
		$.postJSON( bumsTaskTagCreateJsonUrl, {tagName: inp.value},
			function () 
			{
				$('#tagTreeContainer').load( bumsTaskTagTreeLoadUrl, {}, bumsTaskTagTreeOnLoad );
			}
		);
		el = inp.parentNode;
		while ( el && el.id && el.id != 'new_tag_d' ) {
			el = el.parent;
		}
		if ( el ) {
			$(el).hide();
		}
	}
}

function bumsTaskTagClik( Evt ) 
{
	var el = this;
	if ( $( Evt.target ).is( '.tag-opener' ) ) {
		$('#tagTreeContainer').load( bumsTaskTagTreeLoadUrl, {node_path:this.id.substr(3), opened:!$(this).is('.tag-opened')}, function(){ bumsTaskTagTreeOnLoad( $(el).next().find('.tag-checked').length ) } );
	}
	else {
		$('#tagTreeContainer').load( bumsTaskTagTreeLoadUrl, {node_path:this.id.substr(3), checked:!$(this).is('.tag-checked')}, function(){ bumsTaskTagTreeOnLoad( true ) } );
	}
	Evt.stopPropagation();
}

function bumsTaskSwitchFilter( FilterType )
{
	$( '#taskFilterChoose' ).removeClass( 'type-simple type-extra' ).addClass( 'type-' + FilterType );
	$.post( sdfUrlTo( 'SdfConfC_Pref', 'update', '.easy' ), { ctrl:'BumsTaskC_Task', act:'list', name:'filterType', value:FilterType } );
}

function bumsTaskAssignTags()
{
	var $task = $('#listTable tr.selected');
	if ( $task.length == 1 ) 
	{
		var $tagBtn = $( '#taskToolbarTag' );
		var offset = $tagBtn.offset();
	// TODO: Приходится пока таким костылём. jquery не смог нормально определить высоту в сафари
		if ( $.browser.safari ) {
			offset.top = offset.top - 95;
		}
		sdfWindow.setClass( 'sdf-wnd-bubble' );
		sdfWindow.load( sdfUrlTo( 'BumsTaskC_Tag', 'assign' ), sdfAjaxEncode( {taskId:$task.attr('oid') } ) );
		sdfWindow.show( offset.left+$tagBtn[0].offsetWidth/2 + 28, offset.top + 36 - $(window).scrollTop(), 'tr', true );
	}
}

$(document).ready(function(){
	$('div.tag').bind('contextmenu', bumsTaskTagContextMenu).click( bumsTaskTagClik );
	$('#taskRightMenu a').click( function( jEvt ) { if ( $(jEvt.target).parents('div.icon').is('.disabled') ) { return false; } } )		

	// подсветка при наведение для осла
	if ( 0 && $.browser.msie )
	{
		$( '#listTable' ).get(0).attachEvent( 'onmouseover', function() {
			event.cancelBubble = true;
			var target = listGetSrcTr( event );
			if ( ! target ) {
				return;
			}
			if ( target.className.indexOf( ' hover' ) < 0 ) {
				target.className = target.className+' hover'; 
			}
		} );
		$( '#listTable' ).get(0).attachEvent( 'onmouseout', function() {
			event.cancelBubble = true;
			var target = listGetSrcTr( event );
			if ( ! target ) {
				return;
			}
			target.className = target.className.replace( ' hover', '' );
		} );
	}
});
 function tagInitForm()
{
	$('#tagForm').sdf( SdfForm );
	oForm = $sdf('#tagForm');
	oForm.setAction( $('#tagForm').attr( 'action' ) );
	oForm.onSuccess = function( json ) {
		$('#sdfWindow').hide();
		$tags = $( '#tags'+$('#tagForm').attr('oid') );
		$tags.show();
		$tags.find( '.tag-list' ).text( json.names.join( ', ' ) );
		$tags.find( 'input' ).val( json.ids.join( ',' ) );
		if ( json.ids.length == 0 ) 
		{
			$tags.addClass( 'tag-empty' );
			$tags.find( '.act-tag span' ).text( sdfGetText( 'Tag task' ) );
		}
		else 
		{
			$tags.removeClass( 'tag-empty' );
			$tags.find( '.act-tag span' ).text( sdfGetText( 'Change tags' ) );
		}
	};
	
	var $tagInput = $('#tagForm input[@name="tag"]').keydown( tagKeyDown ).focus();
	if ( ! IS_DEMO )
	{
		$tagInput.next('i').click( function(){ tagCreate( $tagInput.val() ) } );
	}
	else 
	{		
		var $ii = $tagInput.next('i');
		$ii.click( function(){ sdfConfirmDemo('#taskDemoNotice', $ii) } );
	}
}

function tagClearSelection()
{
	$( '.tag-select input' ).attr( 'checked', false );
}

function tagChangeList( taskId )
{
	var $tagList = $( '#tags'+taskId );
	var offset = $tagList.find( '.act-tag' ).offset(); 
	sdfWindow.setClass( 'sdf-wnd-bubble' );
	var selected = $( '#tags'+taskId ).find( 'input' ).val();
	selected = selected ? selected.split(',') : '';
	sdfWindow.load( sdfUrlTo( 'BumsTaskC_Tag', 'assign', 0 ), sdfAjaxEncode( {taskId:taskId, selected:selected } ) );
	sdfWindow.show( offset.left - 18, offset.top + 36, 'tl' );
}

function tagClose()
{
	sdfWindow.hide();
}

function tagKeyDown( evt )
{
	if ( evt.keyCode == 13 ) 
	{
		tagCreate( $(this).val() );
		return false;
	}
}

function tagCreate( TagName )
{
	if ( IS_DEMO ) {
		return false;
	}
// Сначала определяем, куда будем вставлять новый тег. 
// Если он уже существует, тогда создавать ничего не надо - просто выберем уже созданный.
	var $tagSelect = $('#tagForm .tag-select');
	var $tags = $tagSelect.find( '.item' );
	var tagAfter = null;
	var name = ''; 
	for ( var i = 0; i < $tags.length; i++ ) 
	{
		name = $($tags[i]).text();
		if ( name > TagName ) 
		{
			tagAfter = $tags[i];
			break;
		}
		else if ( name == TagName ) 
		{
			$tagSelect[0].scrollTop = $tags[i].offsetTop;
			$($tags[i]).find( 'input' ).get(0).checked = true;
			$('#tagForm input[@name="tag"]').val('').focus();
			return false;
		}
	}
	
	$.postJSON( sdfUrlTo( 'BumsTaskC_Tag', 'createTag', '.json' ), {tagName:TagName}, function( Json ){
		if ( ! Json.tagId || Json.tagId == 0 ) {
			return;
		}
		var tagHtml = '<div class="item" oid="'+Json.tagId+'"><i></i><input type="checkbox" name="tags[]" id="tag'+Json.tagId+'" value="'+Json.tagId+'" /><label for="tag'+Json.tagId+'">'+Json.tagName+'</label></div>'; 
		var $newTag = null;
		if ( tagAfter ) {
			$newTag = $( tagHtml ).insertBefore( tagAfter );
		}
		else {
			$newTag = $( tagHtml ).appendTo( $tagSelect );
		}
		$tagSelect[0].scrollTop = $newTag[0].offsetTop;
		$newTag.find( 'input' ).get(0).checked = true;
		$('#tagForm input[@name="tag"]').val('').focus();
	} );
}

function tagInitEdit()
{
	var oForm = $sdf( '#tagForm' );
	oForm.disable();
	oForm.$el.find( 'input[@name="tag"]' ).attr( 'disabled', 1 );
	oForm.$el.find( '.tag-select label' ).bind( 'click', tagLabelClick );
	oForm.$el.find( '.item i' ).click( tagDeleteClick );
}

function tagCancelEdit()
{
	var oForm = $sdf( '#tagForm' );
	oForm.$el.find( 'input[@name="tag"]' ).attr( 'disabled', 0 );
	var $tags = oForm.$el.find( '.tag-select .item' );
	for ( var i = 0; i < $tags.length; i++ ) 
	{
		if ( $tags[i].getAttribute( 'oldname' ) ) {
			$( $tags[i] ).find( 'label' ).text( $tags[i].getAttribute( 'oldname' ) );
		}
		$( $tags[i] ).removeClass( 'item-deleted' );
	}
	oForm.enable();
	oForm.$el.find( '.tag-select label' ).unbind( 'click' );
	oForm.$el.find( '.item i' ).unbind( 'click' );
}

function tagLabelClick( evt )
{
	tagEditApply();
	evt.preventDefault();
	var $item = $( this ).parents( '.item' );
	var tagName = $item.text();
	$item.find( 'label' ).hide();
	var $input = $( '<input class="tag-edit" />' ).appendTo( $item ).val( tagName ).focus();
	$input.keydown( tagEditKeyDown ).blur( tagEditApply );
}

function tagEditKeyDown( Evt )
{
	if ( Evt.keyCode == 13 ) {
		tagEditApply();
	}
	if ( Evt.keyCode == 27 ) {
		tagEditCancel();
	}
}

function tagEditApply()
{
	var oForm = $sdf( '#tagForm' );
	var $input = oForm.$el.find( 'input.tag-edit' );
	if ( $input.length == 0 ) {
		return;
	}
	var $item = $input.parents( '.item' );
	var $label = $item.find( 'label' ); 
	if ( ! $item.attr( 'oldname' ) ) {
		$item.attr( 'oldname', $label.text() );
	}
	$item.find( 'label' ).text( $input.val() ).show();
	$input.remove();
}

function tagEditCancel()
{
	var oForm = $sdf( '#tagForm' );
	var $input = oForm.$el.find( 'input.tag-edit' );
	if ( $input.length == 0 ) {
		return;
	}
	var $item = $input.parents( '.item' );
	$item.find( 'label' ).show();
	$input.remove();
}

function tagDeleteClick( Evt )
{
	var $item = $( Evt.target ).parents( '.item' );
	$item.addClass( 'item-deleted' ); 
}

function tagSaveEdit()
{
	tagEditApply();
	var oForm = $sdf( '#tagForm' );
	var $tags = oForm.$el.find( '.tag-select .item' );
	var tags = {};
	for ( var i = 0; i < $tags.length; i++ ) 
	{
		if ( $($tags[i]).is( '.item-deleted' ) ) {
			$($tags[i]).remove();
		}
		else
		{
			tags[$tags[i].getAttribute('oid')] = $($tags[i]).text();
			$tags[i].setAttribute( 'oldname', '' );
		}
	}
	$.postJSON( sdfUrlTo( 'BumsTaskC_Tag', 'saveList', '.json' ), sdfAjaxEncode(tags, ['tags']), function(){
		tagCancelEdit();
	} );
}

 /**
 * @package		bums
 * @subpackage	widget
 * @since      06.11.2007 20:30:00
 * @author     Davojan
 */
(function($){

	$.bums = $.bums || {};
	var bums = $.bums, btn;

//{{{ $.bums.btn
/**
 * Класс-кнопка
 */
bums.btn = $.sdf.extend({

//{{{ __construct
/**
 * Конструктор
 * @param Element el элемент-таблица-кнопка
 * @param bool protect включать ли защиту от двойного нажатия? по умолчанию - да
 */
	__construct: function( el, protect )
	{
		if ( this.parentCall( 'constructor', arguments ) === false ) return false;
		this.focused = false;
		this.disabled = false;
		this.multiClickProtection = protect || true;
		this.$el.sdfHandle( 'click', 'mousedown', 'hover' );
		this.bindElement( 'input', 'input', this.$el ).sdfHandle( 'focus', 'blur', 'click' );
		
		this.type = this.$input.attr( 'type' ).toLowerCase();
		if ( this.type == 'submit' )
		{
			var $form = this.$input.parents( 'form' );
			$form.sdfHandle( ['submit', {obj:this}] );

			this.form = $sdf( $form );
			if ( this.form ) 
			{
				this.form.bindObj( 'fail', this, 'formFail' );
				this.form.bindObj( 'disable', this, 'formDisable' );
				this.form.bindObj( 'enable', this, 'formEnable' );
			}
		}
		btn.all.push( this );
	},
//===========================================================================}}}
//{{{ onFormFail
/**
 * Обработка отлупа ajax-формы
 * Это автоматически работает только для сабмит-кнопки в составе ajax-формы
 */
	onFormFail: function()
	{
		if ( this.multiClickProtection )
		{
			var x = this;
			window.setTimeout( function() { x.endProgress() }, 200 );
		}
	},
//===========================================================================}}}
//{{{ onFormDisable
/**
 * Обработка дизабления формы
 */
	onFormDisable: function()
	{
		this.disable();
	},
//===========================================================================}}}
//{{{ onFormEnable
/**
 * Обработка енабления формы
 */
	onFormEnable: function()
	{
		this.enable();
	},
//===========================================================================}}}
//{{{ onClick
/**
 * Обработчик клика по кнопке
 */
	onClick: function( evt, el )
	{
	// попытка обойти глюк незакрывающихся менюшек из-за stopPropagation()
		$(document).click();

		if ( ! this.disabled )
		{
		  	if ( el !== this.input ) {
		  		this.$input.click();
		  	}
		  	else
			{
	  			this.trigger('click');
	  			if ( 'submit' == this.type  )
				{
	  				evt.preventDefault();
	  				this.$input.parents('form').submit();
		  		}
	  			else
				{
			  		if ( this.multiClickProtection ) {
		  				this.startProgress();
		  			}
	  			}
		  	}
		  	evt.stopPropagation();
		}
	},
//===========================================================================}}}
//{{{ onSubmit
/**
 * Обработчик сабмита формы (если это сабмит-кнопка)
 */
	onSubmit: function()
	{
		if ( this.multiClickProtection ) {
			this.startProgress();
		}
	},
//===========================================================================}}}
//{{{ onMousedown
/**
 * Обработчик нажатия левой кнопки мыши
 */
	onMousedown: function( evt )
	{
		var i;
		for ( i = 0; i < bums.btn.all.length; i++ )
		{
			if ( bums.btn.all[i].focused )
			{
				bums.btn.all[i].onBlur();
				break;
			}
		}
		if ( ! this.disabled )
		{
			this.onFocus();
			$(document).one( 'mouseup', {obj: this}, $.sdf.handle );
		}
		if ( this.focused ) {
			this.preventBlur = true;
		}
		evt.stopPropagation();
		evt.preventDefault();
	},
//===========================================================================}}}
//{{{ onMouseup
/**
 * Обработчик отпускания левой кнопки мыши после нажатия над кнопкой
 */
	onMouseup: function()
	{
		this.preventBlur = false;
		this.focus();
	},
//===========================================================================}}}
//{{{ onFocus
/**
 * Обработчик попадания фокуса на кнопку
 */
	onFocus: function()
	{
		this.focused = true;
		this.$el.find('tr').addClass('focused');
	},
//===========================================================================}}}
//{{{ onBlur
/**
 * Обработчик ухода фокуса с кнопки
 */
	onBlur: function()
	{
		if ( ! this.preventBlur )
		{
			this.focused = false;
			this.$el.find('tr').removeClass('focused');
		}
		else {
			this.preventBlur = false;
		}
	},
//===========================================================================}}}
//{{{ onMouseover
/**
 * Наведение мышки
 */
	onMouseover: function()
	{
		this.$el.find('tr').addClass('hover');
	},
//===========================================================================}}}
//{{{ onMouseout
/**
 * Уведение мышки
 */
	onMouseout: function()
	{
		this.$el.find('tr').removeClass('hover');
	},
//===========================================================================}}}
//{{{ focus
/**
 * Фокусировка на кнопке
 */
	focus: function()
	{
		try {
			this.$input.focus();
		}
		catch( e ) {}
	},
//===========================================================================}}}
//{{{ disable
/**
 * Делает кнопку неактивной
 */
	disable: function()
	{
		this.disabled = true;
		this.$input.attr('disabled', 'true');
		this.preventBlur = false;
		this.onBlur();
		this.$el.find('tr').addClass('disabled');
	},
//===========================================================================}}}
//{{{ enable
/**
 * Делает кнопку активной
 */
	enable: function()
	{
		this.$el.find('tr').removeClass('disabled');
		this.$input.removeAttr('disabled');
		this.disabled = false;
	},
//===========================================================================}}}
//{{{ startProgress
/**
 * Переход в активный режим - типа что-то делаем
 */
	startProgress: function()
	{
		this.disable();
		this.$el.find('tbody').addClass('progress');
	},
//===========================================================================}}}
//{{{ endProgress
/**
 * Переход в режим ожидания, типа закончили
 */
	endProgress: function()
	{
		this.$el.find('tbody').removeClass('progress');
		this.enable();
	}
//===========================================================================}}}
		
},
{
	all:[],

//{{{ cleanLeacks
/**
 * Борьба с утечками памяти в осле
 */
	cleanLeacks: function()
	{
		var i;
		for ( i = 0; i < btn.all.length; i++ ) {
			btn.all[i] = null;
		}
		btn.all = null;
	}
//===========================================================================}}}

});
	btn = bums.btn;
	if ( $.browser.msie && $.browser.version.indexOf('6.') !== -1 ) {
		$(window).bind("unload", btn.cleanLeacks );
	}
//===========================================================================}}}

	$(document).ready(function(){
		$('.bums-btn').sdf( btn );
	});

})(jQuery);
/*============================================================================*
 * vim: set expandtab tabstop=3 shiftwidth=3 foldmethod=marker:               *
 *   END OF FILE                                                              *
 *============================================================================*/ //{{{ sdfArrayAddElementIfNotExists
/**
 * Добавляет елемент в массив, если этого элемента еще нет в массиве.
 * @since  18.09.2007 11:57:54
 * @author jikk
 * @param array arr массив
 * @param mixed el что-то
 */
function sdfArrayAddElementIfNotExists( arr, el )
{
	if ( typeof el != 'undefined' && el )
	{
		for ( var i in arr )
		{
			if ( arr[i] === el ) {
				return;
			}
		}
		arr.push( el );
	}
}
//===========================================================================}}}
//{{{ sdfValidatorNotEmpty
/**
 * Валидирует значение поля на непустоту
 * @since  26.10.2007 15:13:58
 * @author jikk
 */
function sdfValidatorNotEmpty( Field )
{
	var val = Field.val();
	if ( typeof val != 'string' || ! val || ! $.trim( val ) ) {
		Field.addError( 'Field is required' );
	}
}
SDF_LANG["Field is required"] = "это поле надо заполнить";
//===========================================================================}}}
//{{{ sdfEmailValidator
/**
 * Валидирует поле с электропочтой
 * @since  18.09.2007 18:15:20
 * @author jikk
 * @param SdfField Field
 */
function sdfEmailValidator( Field )
{
	var val = Field.val();
	if ( typeof val == 'string' && val ) 
	{
		if ( ! /^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i.test(val) ) {
			Field.addError( 'Invalid email' );
		}
	}
}
SDF_LANG["Invalid email"] = "это не почта";
//===========================================================================}}}
//{{{ sdfRegexpValidator
/**
 * Метод проверяет, что поле состоит из латинских букв
 * @since  30.11.2007 14:02:17
 * @author jikk
 */
function sdfLatinSmallLettersValidator( Field )
{
	var val = Field.val();
	if ( typeof val == 'string' && val ) 
	{
		if ( ! /^([a-z]+)$/.test(val) ) {
			Field.addError( 'Field should contain only small english letters' );
		}
	}
}
SDF_LANG["Field should contain only small english letters"] = "Поле должно состоять из маленьких латинских букв";
//===========================================================================}}}

//{{{ SdfField
/**
 * Класс, отвечающий за логику работы поля формы. 
 * Валидация поля, обработка ошибок, связанных с полем - основные обязанности класса
 * @package    sdf
 * @since      18.09.2007 11:51:12
 * @author     jikk
 * @param SdfFoem Form форма, к которой относится поле
 * @param JQueryObject JEl объект, который соотвествует полю (input, textarea, select, etc)
 * @param function Validator функция-валидатор для поля
 * @param function Handler функция-обработчик для поля
 */
function SdfField( Form, JEl, Validator, Handler ) 
{
	this.form = Form;		// TODO: а зачем это?
	this.jEl = JEl;
	this.validators = [];	// список функций-валидаторов, присоединенных к полю
	this.handlers = [];		// список функций-обработчиков, присоединенных к полю
	this.inputName = JEl.attr( 'name' );  // имя поля
	this.addValidator( Validator ); 
	this.addHandler( Handler );
	this.errors = [];			// ошибки, выявленные при проверке поля
	this.notice = [];	   	// сообщение о нормальном завершении проверки
	this.data = {};			// объект, в который валидаторы могу складывать данные для хэндлеров
}
//-----------------------------------------------------------------------------
SdfField.prototype = {
//{{{ addValidator
/**
 * Присоединяет к полю валидатор, если он не был присоединен ранее
 * @since  18.09.2007 11:42:11
 * @author jikk
 * @param function v может быть null или undefined
 * @return void
 */
	addValidator : function ( v )
	{
		sdfArrayAddElementIfNotExists( this.validators, v );
	},
//===========================================================================}}}
//{{{ addHandler
/**
 * Присоединяет к полю обработчик, если он не был присоединен ранее
 * @since  18.09.2007 11:46:11
 * @author jikk
 * @param function h может быть null или undefined
 */
	addHandler : function ( h )
	{
		sdfArrayAddElementIfNotExists( this.handlers, h );
	},
//===========================================================================}}}	
//{{{ val
/**
 * Возвращает текущее значение поля
 * @since  18.09.2007 11:47:17
 * @author jikk
 * @return mixed, обычно string или null
 */
	val : function ()
	{
		return this.jEl.val();
	},
//===========================================================================}}}	
//{{{ check
/**
 * Запускает проверку корректности значения поля
 * @since  18.09.2007 11:48:12
 * @author jikk
 */
	check : function ()
	{
	// перед очередной проверкой очищаем старые ошибки и сообщения
		this.errors = []; this.notice = [];
		if ( ! this.form.preValidatorCheck || this.form.preValidatorCheck( this ) ) 
		{			
			for ( var i in this.validators ) {
				this.validators[i]( this );
			}
			this.handleErrors();				 		
		}	
	},
//===========================================================================}}}
//{{{ handleErrors
/**
 * Запускает хендлеры ошибок. Выделено в отдельный метод, т.к. иногда ошибки могут прийти от сервера
 * и тогда надо запустить хендлеры, не вызывая валидаторы
 * @since  30.10.2007 12:58:32
 * @author jikk
 */
	handleErrors : function ()
	{
		for ( var i in this.handlers ) {
			this.handlers[i]( this );
		}
	},
//===========================================================================}}}	
//{{{ addError
/**
 * Добавляет ошибку типа error
 * @since  18.09.2007 12:14:59
 * @author jikk
 * @param string EType тип ошибки (error, notice, autofix)
 * @param string LMessage сообщение об ошибке, ожидающее, что его переведут на нужный язык
 * @param string MParams параметры, которыми надо заменить спец-символы в сообщении
 */
	addError : function ( LMessage, MParams )
	{
		this.errors.push( new SdfError( 'error', LMessage, MParams ) );
	},
//===========================================================================}}}
//{{{ addNotice
/**
 * Добавляет ошибку типа notice
 * @since  18.09.2007 12:36:27
 * @author jikk
 * @param string EType тип ошибки (error, notice, autofix)
 * @param string LMessage сообщение об ошибке, ожидающее, что его переведут на нужный язык
 * @param string MParams параметры, которыми надо заменить спец-символы в сообщении
 */
	addNotice : function ( LMessage, MParams )
	{
		this.notice.push( new SdfError( 'notice', LMessage, MParams ) );	
	},
//===========================================================================}}}
//{{{ focus
/**
 * Устанавливает фокус (если это возможно) на элемент, соответствующий объекту.
 * @since  18.09.2007 15:40:58
 * @author jikk
 */
	focus : function ()
	{
		if ( this.jEl.is( 'input|textarea' ) && this.jEl.css( 'display' ) != 'none' && this.jEl.css( 'visibility' ) != 'hidden' ) {
			this.jEl.focus();
		}
	}
//===========================================================================}}}
};
/*============================================================================*
 *  END OF SdfField                                                 				*
 *=========================================================================}}}*/

SdfForm = $.sdf.extend({
//{{{ __construct
/**
 * Класс, отвечающий за логику работы формы. 
 * Валидация формы, отображение ошибок и т.п. - обязанности этого класса
 * @package    sdf
 * @since      18.09.2007 12:00:17
 * @author     jikk
 */
	__construct: function( el )
	{
		if ( this.parentCall( 'constructor', arguments ) === false ) return false;
		this.action = '';
		this.method = 'POST';
		this.fields = {};
		this.defaultErrorHandler = typeof DefaultErrorHandler != 'undefined' ? DefaultErrorHandler : null;
		this.onSuccess = null;
		this.$bigBaraBoom = this.$el.find( '#bigBaraBoom' );
		if ( ! this.$bigBaraBoom.length ) {
			this.$bigBaraBoom = $( '<div id="bigBaraBoom"></div>' ).prependTo( this.$el );
		}
	},
//===========================================================================}}}
//{{{ setAction
/**
 * Устанавливает экшн
 * @param String action урл, короче
 */
	setAction: function( Action )
	{
		this.action = Action;
		var me = this;
		var options = {
			dataType: 'json',
			url: this.action,
			type: this.method,
			beforeSubmit: function() { return me.beforeSubmit(); },
			success: function( json ) { me.submitSuccess( json ); }
		};
		this.$el.ajaxForm( options );
	},
//===========================================================================}}}
	
	beforeSubmit: function()
	{	
		var isValid = true;
		if ( this.beforeSubmitSpecial ) {
			if ( ! this.beforeSubmitSpecial() ) {
				isValid = false;
			}			
		}

		this.$el.find( '.help' ).text( '' );
		this.$bigBaraBoom.text( '' );
		this.$el.find( '.field' ).removeClass( 'error' ).removeClass( 'autofix' ).removeClass( 'notice' );
		var firstFieldWithError = null;
		for ( var inputName in this.fields ) 
		{
			var f = this.fields[inputName];
			if ( f instanceof SdfField ) 
			{
				f.check();
				if ( f.errors.length > 0 && null == firstFieldWithError ) 
				{
					isValid = false;
					firstFieldWithError = f;
				}				
			}
		}
		if ( null != firstFieldWithError ) 
		{
			this.trigger('fail');
			firstFieldWithError.focus();
			return false;
		}
		else if ( ! isValid ) 
		{
			this.trigger('fail');
			return false;
		}
		if ( IS_DEMO ) {
			return false;
		}
		return true;
	},
	
	submitSuccess: function( json )
	{
		var focusSet = false;
		var i = 0;
		var failTriggered = false;
		with ( json ) 
		{
			if ( sdfHandleJsonException( json ) ) 
			{
				this.trigger('fail');				
				return;
			}
			if ( json.formErrors && json.formErrors.length ) 
			{
				this.trigger('fail');
				failTriggered = true;
				for ( i=0; i < formErrors.length; i++ ) 
				{
				// если поле зарегистрировано и это SdfField
					var inputName = formErrors[i].input;
					if ( this.fields[inputName] && this.fields[inputName] instanceof SdfField ) 
					{
						var curField = this.fields[inputName];
					// TODO это надо сделать по-другому
						curField.errors.push( new SdfError( formErrors[i].type, formErrors[i].msg ) );						
						if ( ! focusSet ) 
						{
							curField.focus();
							focusSet = true;
						}
						curField.handleErrors();
					}
				// TODO от этой ситуации надо избавиться. должны быть зарегены поля
					else 
					{					
						var jInput = this.$el.find( 'input[@name="'+formErrors[i].input+'"]' );
						if ( !jInput.length ) {
							jInput = this.$el.find( 'textarea[@name="'+formErrors[i].input+'"]' );
						}
					// Если можем навести фокус на первый элемент с ошибкой, делаем это
						if ( ! focusSet ) 
						{
							if ( jInput.css( 'display' ) != 'none' && jInput.css( 'visibility' ) != 'hidden' ) {
								jInput.focus();
							}
							focusSet = true;
						}
						var jField = jInput.parents( '.field' );
						jField.addClass( formErrors[i].type );
						jField.find('.help').text( formErrors[i].msg );
						if ( formErrors[i].type == 'autofix' ) {
							this.setFieldValue( formErrors[i].input, formErrors[i].value );
						}
					}
				}				
			}
			if ( json.errorSummary ) 
			{		
				$('#bigBaraBoom').text( json.errorSummary );
				document.location.href="#bigBaraBoom";
			}
			else if ( json.redirectTo )
			{	
				document.location.href = redirectTo;
				return;
			}
			else if ( this.onSuccess ) 
			{
				this.onSuccess( json );
				return;
			}
			//this.trigger('fail');
			if ( ! failTriggered ) 
			{
				this.trigger('fail');
				failTriggered = true;
			}
		}
	},

	
	registerField: function( FieldObject )
	{
		this.fields[ FieldObject.getInputName() ] = FieldObject;
	},
	
	setFieldValue: function( InputName, Value )
	{
		if ( ! this.fields[InputName] ) {
			return false;
		}
		this.fields[InputName].setValue( Value );
	},
	
	disable: function()
	{
		this.$el.addClass( 'form-disabled' );
		this.trigger( 'disable' );
	},

	enable: function()
	{
		this.$el.removeClass( 'form-disabled' );
		this.trigger( 'enable' );
	},

	registerCheck: function ( jEl, Validators, Handlers ) 
	{		
		var inputName = jEl.attr( 'name' );
		if ( ! this.fields[inputName] ) {
			this.fields[inputName] = new SdfField( this, jEl );
		}
		if ( this.fields[inputName] instanceof SdfField )
		{
			if ( typeof Validators == 'function' ) {
				Validators = [Validators];
			}
			for ( var i in Validators ) {
				this.fields[inputName].addValidator( Validators[i] );	
			}
			if ( typeof Handlers == 'function' ) {
				Handlers = [Handlers];
			}
			for ( var i in Handlers ) {
				this.fields[inputName].addHandler( Handlers[i] );
			}				
		}
		return this.fields[inputName];
	}
},
{
	defaultErrorHandler: function( Field ) 
	{
		var ers = Field.errors;
	// ищем строку
		jField = Field.jEl.parents( 'div.field' );
		if ( ers.length ) 
		{
		// возможно, элемент с ошибкой уже был создан
			errCell = jField.find( 'div.help' );
		// если нет, создаем его
			if ( 0 == errCell.length ) {
				errCell = $( '<div class="f-err">' + ers[0].message + '</div>' ).appendTo( jField );
			}
		// если уже был, надо удалить "ошибочный" css-класс у строки
			else {
				jField.removeClass( errCell.attr( 'type' ) );
			}
			errCell.html( ers[0].message );
			errCell.attr( 'type', ers[0].type );
			jField.addClass( ers[0].type );
		}
		else 
		{
		// ищем ошибочный элемент
			errCell = jField.find( 'div.help' );
		// если нашли, удаляем его и удаляем "ошибочный" css-класс у строки
			if ( errCell.length )
			{
				jField.removeClass( errCell.attr( 'type' ) );
				errCell.remove();
			}
		}
	}
});
/*============================================================================*
 *  END OF SdfForm                                  	               			*
 *=========================================================================}}}*/

 	$(document).ready(function(){ 	
		$('.sdf-form').sdf( SdfForm );
	});
 
//{{{ SdfError
/**
 * Класс инкапсулирует в себе информацию об ошибке - тип ошибки, сообщение.
 * @package    sdf
 * @since      18.09.2007 12:40:17
 * @author     jikk
 */
function SdfError( type, message, params )
{
	this.message = sdfGetText( message, params );
	this.type = type;
}
//-----------------------------------------------------------------------------
SdfError.prototype = {};
/*============================================================================*
 *  END OF SdfError                                                           *
 *=========================================================================}}}*/
 
 /*============================================================================*
 * vim: set expandtab tabstop=3 shiftwidth=3 foldmethod=marker:               *
 *   END OF FILE                                                              *
 *============================================================================*/ /**
 * @package		sdf
 * @subpackage	widget
 * @since      16.07.2007 13:29:18
 * @author     Eugene
 */

//{{{ SdfSelect()
/**
 * Конструктор
 * @since  16.07.2007 16:01:18
 * @author Eugene
 * @param Element|Id El Элемент на странице или его id
 * @param array Items Список значений для выбора
 * @param string InputName Название input-а, в который будет положен результат выбора
 * @param int Value Начальное значение 
 */
function SdfSelect( El, Items, InputName, Value )
{
	this.items = Items;
	
	this.filters = [];
	this.filterDefault = 0;
	this.value = Value;
	this.textFields = [0];
	this.template = '<div class="text">\'+items[i][0]+\'</div>';
	this.focusByHand = false;
	this.onChange = null;
	this.uniqId = Math.round( Math.random()*1000000000 );
	
// Можно ли добавлять новые варианты
	this.allowCreate = false;

	this.jEl = $(El);
	this.jEl.addClass( 'sdf-select' );
	$('<input type="hidden" class="select-value" name="'+InputName+'" value="'+Value+'" />').appendTo( this.jEl );
	this.jValue = this.jEl.children( 'input.select-value' );
	
	$('<input type="text" class="key-hook" style="font-size: 1px; border-width:0; width:0; z-index:0; position:absolute; left:-9999px " />').prependTo(this.jEl);
	this.jKeyHook = this.jEl.children( 'input.key-hook' );
};
//===========================================================================}}}
//{{{ Прототип sdfSelect
SdfSelect.prototype = {
	//{{{ init()
	/**
	 * Инициализация работы элемента 
	 * @since  16.07.2007 16:12:18
	 * @author Eugene
	 */
	init: function() 
	{
	// Создаем выпадающий див и другие необходимые для работы элементы
		if ( ! this.jEl.children('.drop-down').length ) 
		{
			if ( this.filters.length ) 
			{
				$('<div class="drop-down sdf-select" style="display:none"><table onSelectStart="return false"><tr valign="top">'
					+'<td class="options" width="50%"><div class="select"></div></td>'
					+'<td class="filters" width="50%"><div class="select"></div></td>'
					+'</tr></table></div>').prependTo( this.jEl );
			}
			else 
			{
				$('<div class="drop-down sdf-select" style="display:none"><table onSelectStart="return false"><tr valign="top">'
					+'<td class="options" width="100%"><div class="select"></div></td>'
					+'</tr></table></div>').prependTo( this.jEl );
			}
		}
		if ( ! this.jEl.children('.disabler').length ) {
			$('<div class="disabler"></div>').appendTo( this.jEl );
		}
		if ( ! this.jEl.children('.input').length ) {
			$('<div class="input"></div>').appendTo( this.jEl );
		}
		
	// Запоминаем некоторые основные элементы
		this.jOptions = this.jEl.find( '.options' );
		this.jInput = this.jEl.children( '.input' );
		this.jFilters = this.jEl.find( '.filters' );
		this.jDropDown = this.jEl.children( '.drop-down' ).prependTo( document.body );
		this.jCurrent = this.jOptions.find( '.current' );

		if ( this.filters.length > 0 )
		{
		// Заполняем фильтры
			var html = '';
			for ( var i=0, cnt=this.filters.length; i<cnt; i++ ) 
			{
				if ( this.filters[i].title ) {
					html += '<div class="filter" f="'+i+'">'+this.filters[i].title+'</div>';
				}
				else if ( i < cnt-1 ) {
					html += '<div class="separator">&#160;</div>';
				}
			}
			this.jFilters.children('.select').html( html );
			this.jDropDown.css( 'width', parseFloat( this.jEl.css('width') )*2 + this.jEl.css('width').substr( this.jEl.css('width').length-2, 2 ) );
		}
		else {
			this.jDropDown.css( 'width', this.jEl.css('width') );
		}
	
	// Вешаем события
		var me = this;
		this.jEl.keydown( function(evt){ me.keyDown(evt); } );
		this.jEl.keypress( function(evt){
			if ( evt.keyCode == 13 ) {
				evt.preventDefault();
			}
		});
		this.jInput.click( function(evt){ if (me.cancelBubble(evt)) return true; me.inputClick(evt); me.stopPropagation(evt); } );
		this.jOptions.click( function(evt){ me.optionClick(evt); } );
		this.jOptions.dblclick( function(evt){ me.optionDblClick(evt); } );
		this.jDropDown.click( function(evt){ me.stopPropagation(evt); } );
		this.jFilters.click( function(evt){ me.filterClick(evt); } );
		this.jKeyHook.focus( function(evt){ me.onFocus(evt); } );

		this.updateInput();
		this.setFilter( this.filterDefault );
	},
	//===========================================================================}}}
	//{{{ setTemplate()
	/**
	 * Задать шаблон 
	 * @since  16.07.2007 16:12:18
	 * @author Eugene
	 * @param string Template код шаблона
	 */
	setTemplate: function( Template ) 
	{
		this.template = Template;
	},
	//===========================================================================}}}
	//{{{ setFilters()
	/**
	 * Задать доступные фильтры 
	 * @since  16.07.2007 16:12:18
	 * @author Eugene
	 * @param array Filters массив фильтров
	 */
	setFilters: function( Filters ) 
	{
		this.filters = this.filterEmptyFilters( Filters );
	},
	//===========================================================================}}}
	//{{{ setFilterDefault()
	/**
	 * Задать фильтр по умолчанию 
	 * @since  16.07.2007 16:12:18
	 * @author Eugene
	 * @param int String номер фильтра
	 */
	setFilterDefault: function( Filter ) 
	{
		this.filterDefault = Filter;
	},
	//===========================================================================}}}
	//{{{ setFilters()
	/**
	 * Задать поля, по которым нужно фильтровать при вводе текста 
	 * @since  16.07.2007 16:12:18
	 * @author Eugene
	 * @param array Filters массив фильтров
	 */
	setTextFields: function( TextFields ) 
	{
		this.textFields = TextFields;
	},
	//===========================================================================}}}
	//{{{ init()
	/**
	 * Устанавливаем фокус на элемент 
	 * @since  16.07.2007 16:12:18
	 * @author Eugene
	 */
	focus: function()
	{
		var jFilterText = this.jInput.children('.opt').find('.filter-text');
		if ( jFilterText.length == 0 || jFilterText.css( 'display' ) == 'none' ) {
			this.setFocus();
		}
		else {
			jFilterText.focus();
		}
	},
	//===========================================================================}}}
	//{{{ setCurrent()
	/**
	 * Поменять выбранный элемент
	 * @since  10.07.2007 16:19:18
	 * @author Eugene
	 * @param JNode jEl Элемент
	 */
	setCurrent: function( jEl )
	{
		if ( this.jCurrent.length > 0 ) {
			this.jCurrent.removeClass( 'current' );
		}
		if ( jEl.length ) 
		{
			jEl.addClass( 'current' );
			this.value = jEl.attr( 'id' ).substr( 1 );
		}
		this.jCurrent = jEl;
	},
	//===========================================================================}}}
	//{{{ filterClick()
	/**
	 * Обработка клика на фильтре
	 * @since  10.07.2007 16:21:18
	 * @author Eugene
	 * @param Event Event
	 */
	filterClick: function( Event )
	{
		var el =  Event.target;
		if ( !$(el).is( '.filter' ) ) {
			return false;
		}
		this.applyFilter( $(el).attr('f') );
		this.jFilters.find( '.current' ).removeClass( 'current' );
		$( el ).addClass( 'current' );
	},
	//===========================================================================}}}
	//{{{ setFilter()
	/**
	 * Установить фильтр
	 * @since  10.07.2007 16:21:18
	 * @author Eugene
	 * @param string Filter Фильтр, который нужно установить
	 */
	setFilter: function( Filter )
	{
		var jFilters = this.jFilters.children('.select').children( '.filter' );
		var cnt = jFilters.length;
		for ( var i=0; i<cnt; i++ ) 
		{
			if ( $(jFilters[i]).attr( 'f' ) == Filter ) 
			{
				jFilters.removeClass( 'current' );
				$(jFilters[i]).addClass( 'current' );
				this.applyFilter();
				return true;
			}
		}
	},
	//===========================================================================}}}
	//{{{ applyFilter()
	/**
	 * Отобразить элементы, удовлетворяющие данному фильтру (и текущему текстовому фильтру, если он задан) 
	 * @since  10.07.2007 16:23:18
	 * @author Eugene
	 * @param string Filter Название фильтра. Если не задано, берется выделенный в данный момент фильтр.
	 */
	applyFilter: function( Filter )
	{
		var itemsFiltered = new Object();
		if ( this.filters.length > 0 )
		{ 
			if ( Filter == undefined ) 
			{
				var jFilter = this.jFilters.children('.select').children( '.current' );
				if ( jFilter.length ) {
					Filter = jFilter.attr( 'f' );
				}
				else {
					return false;
				}
			}
			
		// Фильтруем фильтром
			var oFilter = this.filters[Filter];
			var i = 0;
			var f = 0;
			var items = this.items;
			var code = 'for (i in items){if (i==0)continue;f='+oFilter.code+';if (f){itemsFiltered[i]=items[i]}}';
			eval(code);
			delete( itemsFiltered[0] );
		}
		else 
		{
			for ( i in this.items ) 
			{
				if ( i != 0 ) {
					itemsFiltered[i] = this.items[i];
				}
			}
		}
		
	// Фильтруем по имени
		var jFilterText = this.jInput.children('.opt').find( '.filter-text' );
		var filterText = ( jFilterText.css( 'display' ) == 'none' || jFilterText.attr( 'value' ) == undefined ) ? '' : jFilterText.attr( 'value' ).toUpperCase(); 
	
		if ( this.textFields )
		{
			var j = 0;
			var s = '';
			var cnt = this.textFields.length;
			for ( i in itemsFiltered ) 
			{
				s = '';
				for ( j=0; j<cnt; j++ ) {
					s = s + itemsFiltered[i][this.textFields[j]];
				}
				if ( s.toUpperCase().indexOf(filterText) == -1 ) {
					delete(itemsFiltered[i]);
				}
			}
		}
		this.setOptions( itemsFiltered );
	},
	//===========================================================================}}}
	//{{{ setOptions()
	/**
	 * Заполнить список options 
	 * @since  10.07.2007 16:23:18
	 * @author Eugene
	 * @param array Options Список элементов, которые нужно отобразить в options
	 */
	setOptions: function( Items )
	{
		var html = '';
		var items = Items;
		var i = 0;
		var code = 'for (i in items){if (i==0)continue;html+=\'<div class="opt" id="o\'+i+\'">'+this.template+'</div>\';}';
		eval( code );
		
		this.jOptions.children('.select' ).html( html );
		var me = this;
		if ( this.allowCreate ) {
			$( '<div class="opt-new">'+this.allowCreate+'</div>' ).click(function(evt){me.dropDown(1);me.filterTextInit();}).appendTo( this.jOptions.children('.select' ) );
		}
		this.jCurrent = [];
	},
	//===========================================================================}}}
	//{{{ optionClick()
	/**
	 * Обработка клика по опции
	 * @since  10.07.2007 16:28:18
	 * @author Eugene
	 * @param Event Event
	 */
	optionClick: function( Event )
	{
		var el =  Event.target;
		this.focus();
	// Находим опцию, на которой кликнули
		while ( el && ! $(el).is('.opt') ) {
			el = el.parentNode;
		}
		if ( ! el ) {
			return false;
		}
		if ( ! $(el).is('.current') ) 
		{
			this.setCurrent( $(el) );
		}
		this.updateInput();
		this.hide( Event );
	},
	//===========================================================================}}}
	//{{{ optionDblClick()
	/**
	 * Обработка двойного клика по опции
	 * @since  10.07.2007 16:28:18
	 * @author Eugene
	 * @param Event Event
	 */
	optionDblClick: function( Event )
	{
		this.focus();
		this.dropDown( 1, Event );
	},
	//===========================================================================}}}
	//{{{ updateInput()
	/**
	 * Обновление значения в инпуте
	 * @since  10.07.2007 16:28:18
	 * @author Eugene
	 */
	updateInput: function()
	{
		var html = '';
		if ( ! this.value.substr || this.value.substr(0, 1) != '$' ) 
		{
			var items = this.items;
			var i = this.value;
		}
		else 
		{
			var items = [[this.value.substr(1), '', '', '', '', '', '', '']];
			var i = 0;  
		}
		var code = 'html=\'<div class="opt" id="o'+i+'">'+this.template+'</div>\';';
		eval( code );
		
		this.jInput.html( html );
		this.jValue.attr( 'value', this.value );

		var me = this;
		this.jInput.children('.opt').find('.text').click( function(evt) { me.textClick(evt); } );
		
		if ( this.onChange ) {
			this.onChange( this );
		}
		//this.jEl.children('.disabler').remove();
		//$('<div class="disabler"></div>').prependTo( this.jEl );
	},
	//===========================================================================}}}
	//{{{ filterTextInit()
	/**
	 * Инициализация фильтрации по тексту
	 * @since  10.07.2007 16:32:18
	 * @author Eugene
	 */
	filterTextInit: function()
	{
		var jText = this.jInput.children('.opt').find('.text');
		var jFilter = this.jInput.children('.opt').find('.filter-text');
		if ( jFilter.length == 0 ) 
		{
			var me = this;
			jFilter = $('<input class="filter-text"/>')
				.css( 'width', jText.width()+'px' )
				.click( function(evt){ me.stopPropagation(evt); } )
				.keyup( function(evt){me.filterTextKeyUp(evt);} )
				.blur( function(evt){me.filterTextBlur(evt);} )
				.prependTo( jText.parent() );
		}
		jText.hide();
		//this.jInput.children('.opt').children('div').css('background-image', '');
		//this.jInput.children('.opt').children('div').children('.lower').html( '&nbsp;' );
		jFilter.attr( 'value', '' ).show();
	// Если сделать это без таймаута, фокус иногда не будет устанавливаться в IE
		setTimeout( function(){jFilter[0].focus()}, 13 );
	},
	//===========================================================================}}}
	//{{{ inputClick()
	/**
	 * Обработка клика по инпуту (основной части виджета)
	 * @since  10.07.2007 16:28:18
	 * @author Eugene
	 * @param Event Event
	 */
	inputClick: function( Event )
	{
		this.focus();
		this.dropDown( 1, Event );
	},
	//===========================================================================}}}
	//{{{ dropDown()
	/**
	 * Открыть/закрыть окно выбора сотрудника
	 * @since  10.07.2007 16:28:18
	 * @author Eugene
	 * @param bool Toggle Нужно ли закрывать окно, если оно уже открыто
	 * @param Event Event
	 */
	dropDown: function( Toggle, Event )
	{
		var jInput = this.jInput;
		var jDropDown = this.jDropDown;
		if ( jDropDown.css( 'display' ) == 'none' )
		{
			jInput.addClass( 'hl' );
			this.applyFilter();
			jDropDown.css('top', '-10000px').show();

			this.adjustHeight();
			
		// Определяем отступ от верха нижнего края видимой пользователю области
			if ( document.body.parentNode.scrollTop > document.body.parentNode.offsetHeight ) {
				var windowBottom = document.body.parentNode.scrollTop + document.body.parentNode.offsetHeight;
			}
			else 
			{
				var innerHeight = window.innerHeight;
				if ( innerHeight == undefined )
				{
					var jFake = $('<div style="position:absolute;top:100%;"></div>').appendTo(document.body);
					innerHeight = jFake.absPos().y;
					jFake.empty();
				}
				var windowBottom = document.body.parentNode.scrollTop + innerHeight;
			}
		// Определяем координаты дива
			var top = jInput.height()+1;
			var offset = jInput.offset();
			
			if ( offset.top + top + jDropDown[0].offsetHeight > windowBottom ) {
				top = -1 - jDropDown[0].offsetHeight;
			}
			jDropDown.css( {top: ( offset.top + top ) + 'px', left: offset.left + 'px '} );
			var me = this;
			delete( me.onDocumentClick );
			this.stopPropagation( Event );
			me.onDocumentClick = function(evt) {
				if ( me.cancelBubble(evt) )
					return true;
				me.filterTextBlur();
				me.hide( evt );
			};
			setTimeout( function(){$(document).click( me.onDocumentClick )}, 13 );
		}
		else if ( Toggle ) 
		{
			this.hide( Event );
		}
	},
	//===========================================================================}}}
	//{{{ hide()
	/**
	 * Закрыть всплывающее окно
	 * @since  05.10.2007 16:18:18
	 * @author Eugene
	 * @param Event Event
	 */
	hide: function( Event )
	{
		this.jDropDown.hide();
		this.jInput.removeClass('hl');
		$(document).unbind( 'click', this.onDocumentClick );
	},
	//===========================================================================}}}
	//{{{ textClick()
	/**
	 * Обработка клика по тексту в инпуте
	 * @since  10.07.2007 16:28:18
	 * @author Eugene
	 * @param Event Event
	 */
	textClick: function( Event )
	{
		this.focus();
		if ( this.jDropDown.css('display') == 'none' ) {
			this.setFilter( 'all' );
		}
		this.filterTextInit();
		this.dropDown( 0, Event );
		this.stopPropagation( Event );
	},
	//===========================================================================}}}
	//{{{ filterTextKeyUp()
	/**
	 * Обработка отжатия клавищи в поле фильтрации по тексту
	 * @since  10.07.2007 16:33:18
	 * @author Eugene
	 * @param Event Event
	 */
	filterTextKeyUp: function( Event ) 
	{
		this.focus();
		if ( Event.keyCode == 27 ) // "Esc" 
		{ 
			this.jInput.children('.opt').find('.filter-text' ).hide();
			this.jInput.children('.opt').find('.text' ).show();
			this.hide( Event );
		}
		if ( Event.keyCode != 9 ) // нажали не "Tab"
		{
			this.applyFilter();
		}
	},
	//===========================================================================}}}
	//{{{ filterTextBlur()
	/**
	 * Обработка потери фокуса фильтром
	 * @since  10.07.2007 16:34:18
	 * @author Eugene
	 * @param Event Event
	 */
	filterTextBlur: function( Event )
	{
		var jFilterText = this.jInput.children('.opt').find('.filter-text' );
		if ( jFilterText.css( 'display' ) == 'none' ) {
			return;
		}
		var text = jFilterText.attr( 'value' );
		if ( this.allowCreate && this.jOptions.find('.opt').length == 0 && text != '' )
		{
			this.value = '$'+text;
			this.hide();
			this.updateInput();
		}
		else 
		{
			jFilterText.hide();
			this.jInput.children('.opt').find('.text' ).show();
		}
	},
	//===========================================================================}}}
	//{{{ keyDown()
	/**
	 * Обработка нажатия клавиши на всем виджите
	 * @since  10.07.2007 16:34:18
	 * @author Eugene
	 * @param Event Event
	 */
	keyDown: function( Event )
	{
		this.focus();
		
		if ( Event.keyCode == 13 || Event.keyCode == 9 ) // "Enter" or "Tab"
		{
			var jCurrent = this.jCurrent;
			if ( jCurrent.length > 0 ) 
			{
				this.jInput.removeClass('hl');
				this.updateInput();
			}
		// Если никто не выбран, а в списке сейчас один сотрудник, его и выбираем
			else if ( ! this.allowCreate )
			{
				var jOptions = this.jOptions.children( '.select').children('.opt' ); 
				if ( jOptions.length == 1 ) {
					this.setCurrent( jOptions );
				}
				this.updateInput();
			}
			this.hide( Event );
		}
		if ( this.jDropDown.css( 'display' ) == 'none' ) {
			return false;
		}
		if ( Event.keyCode == 38 ) // "Up"
		{
			var jCurrent = this.jCurrent;
			if ( jCurrent.length > 0 )
			{
				var jCurrentPrev = jCurrent.prev();
				while ( jCurrentPrev.length && ! jCurrentPrev.is( '.opt' ) ) {
					jCurrentPrev = jCurrentPrev.prev();
				}
				if ( jCurrentPrev.length > 0 && jCurrentPrev.is( '.opt' ) ) 
				{
					this.setCurrent( jCurrentPrev );
					this.updateInput();
					var jSelect = this.jOptions.children( '.select' );
					if ( jCurrentPrev[0].offsetTop - jCurrentPrev[0].offsetHeight < jSelect[0].scrollTop ) {
						jSelect[0].scrollTop = Math.max( jCurrentPrev[0].offsetTop - jCurrentPrev[0].offsetHeight, 0 ); 
					}
				}
			// Если мы в самом верху, инициализируем фильтрацию по имени
				else 
				{
					this.setCurrent( [] );
					this.filterTextInit();
				}
			}
		}
		if ( Event.keyCode == 40 ) // "Down"
		{
			var jCurrent = this.jCurrent;
		// Если никакой сотрудник в данный момент не выбран, начинаем с начала
			if ( jCurrent.length == 0 ) 
			{
				var jOption = this.jOptions.children( '.select').children('.opt' );
				if ( jOption.length > 0 ) 
				{
					this.setCurrent( $(jOption[0]) );
					this.updateInput();
					var me = this;
					setTimeout( function(){me.setFocus();}, 13 );
				}
			}
			else 
			{
				var jCurrentNext = jCurrent.next();
				while ( jCurrentNext.length && ! jCurrentNext.is( '.opt' ) ) {
					jCurrentNext = jCurrentNext.next();
				}
				if ( jCurrentNext.length > 0 && jCurrentNext.is( '.opt' ) ) 
				{
					this.setCurrent( jCurrentNext );
					this.updateInput();
					var jSelect = this.jOptions.children( '.select' );
					if ( jCurrentNext[0].offsetTop + jCurrentNext[0].offsetHeight > jSelect[0].offsetHeight + jSelect[0].scrollTop ) {
						jSelect[0].scrollTop = jCurrentNext[0].offsetTop - jSelect[0].offsetHeight + jCurrentNext[0].offsetHeight;
					}
				}
			}
		}
	},
	//===========================================================================}}}
	//{{{ setFocus()
	/**
	 * Установить фокус на виджет (специальный инпут, предназначенный для ловли нажатий клавиш)
	 * @since  10.07.2007 16:34:18
	 * @author Eugene
	 */
	setFocus: function()
	{
		this.focusByHand = 1;
		this.jKeyHook.focus();
	},
	//===========================================================================}}}
	//{{{ stopPropagation()
	/**
	 * Прекратить обрабатывать данное событие текущим элементом
	 * @since  10.07.2007 16:34:18
	 * @author Eugene
	 * @param Event Event
	 */
	stopPropagation: function( Event )
	{
		Event.stopPropagation();
		return true;
		//TODO: Убрать эту функцию и cancelBubble за ненадобностью
		var Event = Event.originalEvent;
		if ( typeof( Event.returnValue ) != 'object' ) {
			Event.returnValue = {};
		}
		if ( ! Event.returnValue[this.uniqId] ) {
			Event.returnValue[this.uniqId] = {};
		}
		Event.returnValue[this.uniqId]['cancelBubble'] = 1;
	},
	//===========================================================================}}}
	//{{{ cancelBubble()
	/**
	 * Нужно ли прервать обработку данного события
	 * @since  10.07.2007 16:34:18
	 * @author Eugene
	 * @param Event Event
	 */
	cancelBubble: function( Event )
	{
		return false;
		return Event.returnValue && Event.returnValue[this.uniqId] && Event.returnValue[this.uniqId]['cancelBubble'];
	},
	//===========================================================================}}}
	//{{{ onFocus()
	/**
	 * Обработка получения фокуса специальным инпутом виджета
	 * @since  10.07.2007 16:34:18
	 * @author Eugene
	 * @param Event Event
	 */
	onFocus: function( Event )
	{
		if ( this.focusByHand == 1 ) 
		{
			this.focusByHand = 2;
			return false;
		}
		else if ( this.focusByHand == 2 ) 
		{
		// IE поймал второй onFocus для того же события. Это происходит при ручной установке фокуса в инпут с помощью функции focus()
			//var s = '';
			//for ( i in Event ) {
			//	s += i + ':'+Event[i]+'; ';
			//}
			if ( Event.pageX ) 
			{
				this.focusByHand = 0;
				return false;
			}
			this.focusByHand = 0; 
		}
		this.filterTextInit();
		this.dropDown( 0, Event );
	},
	
	filterEmptyFilters: function( Filters )
	{
		var oFilter = null;
		var i = 0;
		var f = 0;
		var items = this.items;
		var filteredFilters = [];
		for ( var j=0; j<Filters.length; j++ ) 
		{
			f = 0;
			oFilter = Filters[j];
			if ( oFilter.code )
			{
				var code = 'for (i in items){if ('+oFilter.code+') {f=1; break;}}';
				eval(code);
				if ( f ) {
					filteredFilters[filteredFilters.length] = oFilter;
				}
			}
			else {
				filteredFilters[filteredFilters.length] = oFilter;
			}
		}
		return filteredFilters;
	},
	
	adjustHeight: function ()
	{
		if ( this.heightAdjusted ) {
			return true;
		}
		if ( this.filters.length > 0 ) 
		{
			this.heightAdjusted = true;
			return true;
		}
		var jSelect = this.jOptions.children( '.select' );
		var jOptions = jSelect.children( '.opt|.opt-new' );
		if ( jOptions.length == 0 ) {
			return false;
		}
		var lastOption = jOptions[jOptions.length-1];
		var innerHeight = lastOption.offsetTop + lastOption.offsetHeight;
		if ( innerHeight < jSelect[0].offsetHeight ) {
			jSelect.css( 'height', innerHeight+'px' );
		} 
		this.heightAdjusted = true;
	}
	

};

/*============================================================================*
 * vim: set expandtab tabstop=3 shiftwidth=3 foldmethod=marker:               *
 *   END OF FILE                                                              *
 *============================================================================*/ 