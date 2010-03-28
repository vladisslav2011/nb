
function init()
{
	chse.callback_uri=window.location.href;
	setInterval('chse.timerch(false);',1000);
}

init();







chse.fetchfuncs.push(function (o)
{
	if(o.objtype.match(/editor_text_.*/) ||
	o.objtype.match(/editor_select/) ||
	(o.objtype=='editor_text') ||
	(o.objtype=='m_text')
	)
	return function()
	{
		this.obj.style.backgroundColor='#d0d0ff';
		return encodeURIComponent(this.obj.value);
	};
	if(o.objtype.match(/editor_checkbox_.*/) || o.objtype=='editor_checkbox')
		return function()
		{
			return this.obj.checked?1:0;
		}
	return null;
}
);

chse.checkerfuncs.push(function (o)
{
	if(o.objtype.match(/editor_text_.*/)||
	(o.objtype=='editor_text')||
	(o.objtype=='m_text')
	)
	{
		if((! o.obj.oldval)&&(o.obj.oldval != '') )
		{
			o.obj['oldval']=o.obj.value;
		}
		return function()
		{
			if(this.obj.oldval==this.obj.value) return false;
			this.obj.oldval=this.obj.value;
			return true;
		}
	}
	if(o.objtype.match(/editor_select/))
	{
		if((! o.obj.oldval)&&(o.obj.oldval != '') )
		{
			o.obj['oldval']=o.obj.value;
		}
		return function()
		{
			if(this.obj.oldval==this.obj.value) return false;
			this.obj.oldval=this.obj.value;
			return true;
		}
	}
	if(o.objtype.match(/editor_checkbox_.*/) || o.objtype=='editor_checkbox')
		return function()
		{
			return true;
		}
	
	return null;
}
);


//fix middle click paste in opera
function opera_fix(o)
{
		if((! o.oldval)&&(o.oldval != '') )
			o['oldval']=o.value;
}

chse.safe_alert=function(a,b){if($i('debug'))$i('debug').value += (b + '\\n ');};




////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//component events
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function editor_text_autosuggest_keypress(object,event,inp_id,dst_id,div_id)
{
	var mkc=event_to_mkc(event);
	var inp=$i(inp_id);
	var div=$i(div_id);
	var dst=$i(dst_id);
	switch(mkc.keycode)
	{
	case 38:
		if(object.as_objects)
		{
			if(object.as_id || object.as_id==0)
			{
				var s;
				if(object.as_objects[object.as_id])s=$i(object.as_objects[object.as_id].id).style;
				else break;
				s.backgroundColor='white';
				s.color='';
				object.as_id--;
				if(object.as_id<0)object.as_id=null;
			}else{
				object.as_id=object.as_objects.length-1;
				//inp.value=object.as_objects[object.as_id].val;
			}
			if(object.as_id || object.as_id==0)
			{
				var o=0;
				if(object.as_objects[object.as_id])o=$i(object.as_objects[object.as_id].id);
				else break;
				//var o=$i(object.as_objects[object.as_id].id);
				var s=o.style;
				s.backgroundColor='blue';
				s.color='white';
				if(inp_id==dst_id)div.style.display='block';
				//o.scrollIntoView();
				var sctop=o.offsetTop;
				if(sctop<div.scrollTop)div.scrollTop=sctop;
				if(sctop>div.scrollTop+div.clientHeight)div.scrollTop=(o.offsetTop+o.offsetHeight)-div.clientHeight;
				//div.scrollTop=(sctop<div.scrollTop)?sctop;
			}else{
				if(inp_id==dst_id)div.style.display='none';
			}
			
		}
		break;
	case 40:
		if(object.as_objects)
		{
			if(object.as_id || object.as_id==0)
			{
				var s=0;
				if(object.as_objects[object.as_id])s=$i(object.as_objects[object.as_id].id).style;
				else break;
				s.backgroundColor='white';
				s.color='';
				object.as_id++;
				if(object.as_id>=object.as_objects.length)object.as_id=null;
			}else{
				object.as_id=0;
				//inp.value=object.as_objects[object.as_id].val;
			}
			if(object.as_id || object.as_id==0)
			{
				var o=0;
				if(object.as_objects[object.as_id])o=$i(object.as_objects[object.as_id].id);
				else break;
				//var o=$i(object.as_objects[object.as_id].id);
				var s=o.style;
				s.backgroundColor='blue';
				s.color='white';
				if(inp_id==dst_id)div.style.display='block';
				//o.scrollIntoView();
				var sctop=(o.offsetTop+o.offsetHeight)-div.clientHeight;
				if(sctop<0)sctop=0;
				if(sctop>div.scrollTop)div.scrollTop=sctop;
				if(sctop+div.clientHeight<div.scrollTop)div.scrollTop=0;
				//div.scrollTop=(sctop>0)?sctop:0;
				
				//from up handler
				var sctop=o.offsetTop;
				if(sctop<div.scrollTop)div.scrollTop=sctop;
				if(sctop>div.scrollTop+div.clientHeight)div.scrollTop=(o.offsetTop+o.offsetHeight)-div.clientHeight;
				//from up handler
				
			}else{
				if(inp_id==dst_id)div.style.display='none';
			}
		}
		break;
	case 13:
		if(object.as_objects && (object.as_id || object.as_id==0))
		{
		if(inp_id!=dst_id)dst.focus();
		dst.value=object.as_objects[object.as_id].val;
		if(inp_id==dst_id)div.style.display='none';
		
		}else dst.oldval+='-';
		break;
	case 9:
	case 27:
		return true;
	default:
		if(object.refresh_timeout)clearTimeout(object.refresh_timeout);
		//object.refresh_timeout=setTimeout('chse.send_or_push({static:\'$object->send.\',val:encodeURIComponent($i(\'js_escape($object->text->id_gen()).\').value)});',1000);
		return true;
	};
//	event.stopPropagation();
//	event.preventDefault();
	return stop_event(event);
}


function editor_text_autosuggest_list_mouseover(text_id,cont_id,num)
{
		var text_inp=$i(text_id);
		var cont_inp=$i(cont_id);
		if(cont_inp.as_objects)
		{
			if(cont_inp.as_id || cont_inp.as_id==0)
			{
				var s=$i(cont_inp.as_objects[cont_inp.as_id].id).style;
				s.backgroundColor='white';
				s.color='';
			};
			cont_inp.as_id=num;
			if(cont_inp.as_id || cont_inp.as_id==0)
			{
				var s=$i(cont_inp.as_objects[cont_inp.as_id].id).style;
				s.backgroundColor='blue';
				s.color='white';
			}
		}
}


function editor_text_autosuggest_list_mouseout(text_id,cont_id,num)
{
		var text_inp=$i(text_id);
		var cont_inp=$i(cont_id);
		if(cont_inp.as_objects)
		{
			if(cont_inp.as_id || cont_inp.as_id==0)
			{
				var s=$i(cont_inp.as_objects[cont_inp.as_id].id).style;
				s.backgroundColor='white';
				s.color='';
			};
			cont_inp.as_id=null;
		}
}


function editor_text_autosuggest_list_mouseup(text_id,cont_id,num)
{
		var text_inp=$i(text_id);
		var cont_inp=$i(cont_id);
		text_inp.focus();
		if(cont_inp.as_objects)
		{
			if(cont_inp.as_id || cont_inp.as_id==0)
			{
				text_inp.value=cont_inp.as_objects[cont_inp.as_id].val;
				text_inp.focus();
				chse.timerch();
			};
		}
}



function editor_dropdown_button_keypress(object,event,div_id)
{
	var mkc=event_to_mkc(event);
	var div=$i(div_id);
	switch(mkc.keycode)
	{
	case 38:
		if(object.as_objects)
		{
			if(object.as_id || object.as_id==0)
			{
				var s;
				if(object.as_objects[object.as_id])s=$i(object.as_objects[object.as_id].id).style;
				else break;
				s.backgroundColor='white';
				s.color='';
				object.as_id--;
				if(object.as_id<0)object.as_id=null;
			}else{
				object.as_id=object.as_objects.length-1;
				//inp.value=object.as_objects[object.as_id].val;
			}
			if(object.as_id || object.as_id==0)
			{
				var o=0;
				if(object.as_objects[object.as_id])o=$i(object.as_objects[object.as_id].id);
				else break;
				//var o=$i(object.as_objects[object.as_id].id);
				var s=o.style;
				s.backgroundColor='blue';
				s.color='white';
				//o.scrollIntoView();
				var sctop=o.offsetTop;
				if(sctop<div.scrollTop)div.scrollTop=sctop;
				if(sctop>div.scrollTop+div.clientHeight)div.scrollTop=(o.offsetTop+o.offsetHeight)-div.clientHeight;
				//div.scrollTop=(sctop<div.scrollTop)?sctop;
			}
			
		}
		break;
	case 40:
		if(object.as_objects)
		{
			if(object.as_id || object.as_id==0)
			{
				var s=0;
				if(object.as_objects[object.as_id])s=$i(object.as_objects[object.as_id].id).style;
				else break;
				s.backgroundColor='white';
				s.color='';
				object.as_id++;
				if(object.as_id>=object.as_objects.length)object.as_id=null;
			}else{
				object.as_id=0;
				//inp.value=object.as_objects[object.as_id].val;
			}
			if(object.as_id || object.as_id==0)
			{
				var o=0;
				if(object.as_objects[object.as_id])o=$i(object.as_objects[object.as_id].id);
				else break;
				//var o=$i(object.as_objects[object.as_id].id);
				var s=o.style;
				s.backgroundColor='blue';
				s.color='white';
				//o.scrollIntoView();
				var sctop=(o.offsetTop+o.offsetHeight)-div.clientHeight;
				if(sctop<0)sctop=0;
				if(sctop>div.scrollTop)div.scrollTop=sctop;
				if(sctop+div.clientHeight<div.scrollTop)div.scrollTop=0;
				//div.scrollTop=(sctop>0)?sctop:0;
				
				//from up handler
				var sctop=o.offsetTop;
				if(sctop<div.scrollTop)div.scrollTop=sctop;
				if(sctop>div.scrollTop+div.clientHeight)div.scrollTop=(o.offsetTop+o.offsetHeight)-div.clientHeight;
				//from up handler
				
			}
		}
		break;
	case 13:
		if(object.as_objects && (object.as_id || object.as_id==0))
		{
		return object.as_objects[object.as_id].val;
		
		};
		break;
	case 9:
	case 27:
		return true;
	default:
		for(var k=0;k<object.as_objects.length;k++)
			if(object.as_objects[k].acc==String.fromCharCode((mkc.keycode==0)?mkc.charcode:mkc.keycode))
			{
			//	event.stopPropagation();
			//	event.preventDefault();
				stop_event(event);
				return object.as_objects[k].val;
			}
		//if(object.refresh_timeout)clearTimeout(object.refresh_timeout);
		//object.refresh_timeout=setTimeout('chse.send_or_push({static:\'$object->send.\',val:encodeURIComponent($i(\'js_escape($object->text->id_gen()).\').value)});',1000);
		return true;
	};
//	event.stopPropagation();
//	event.preventDefault();
	return stop_event(event);
}


// editor_dropdown_set



function tddcb_activate(input_ref,div_id,full,chse_struct)
{
	
	chse.activatemon(chse_struct);
	input_ref.chse_struct=chse_struct;
	var dd_div=document.getElementById(div_id);
	clearTimeout(input_ref.hidetimeout);
	if(input_ref.variant_list)
	{
	}else{
		input_ref.variant_list=full.split(" ");
		
		var mk=input_ref.variant_list.length;
		input_ref.variant_selection=input_ref.value.split(" ");
		input_ref.variant_cb=[];
		var tbl = document.createElement("table");
		dd_div.appendChild(tbl);
		for(var k=0;k<mk;k++)
		{
			var tr=document.createElement("tr");
			tbl.appendChild(tr);
			tr.style.outlineColor="white";
			tr.style.outlineWidth="1px";
			tr.style.outlineStyle="solid";
			tr.setAttribute("onmouseover","this.style.outlineColor='blue';");
			tr.setAttribute("onmouseout","this.style.outlineColor='white';");
			var td=document.createElement("td");
			tr.appendChild(td);
			var cb=document.createElement("input");
			cb.type="checkbox";
			for(var t=0;t<input_ref.variant_selection.length;t++)
				if(input_ref.variant_selection[t]==input_ref.variant_list[k])
					cb.checked=true;
			input_ref.variant_cb[k]=cb;
			cb.input_ref=input_ref;
			cb.tr_ref=tr;
			cb.setAttribute("onclick","tddcb_update(this);");
			cb.setAttribute("onfocus","tddcb_cb_focus(this);");
			cb.setAttribute("onblur","tddcb_cb_blur(this,'"+div_id+"');");
			cb.setAttribute("onkeypress","tddcb_cb_keypress(event,this.input_ref," + k + ");");
			td.appendChild(cb);
			var td=document.createElement("td");
			tr.appendChild(td);
			td.input_ref=input_ref;
			td.setAttribute("onmousedown","event.preventDefault();event.stopPropagation();return false;");
			td.setAttribute("onmouseup","tddcb_td_click(this,event,"+k+");");
			var txt=document.createTextNode(input_ref.variant_list[k]);
			td.appendChild(txt);
		}
	}
	dd_div.style.display="block";
		
}


function tddcb_update(ref)
{
	//var input_ref=document.getElementById(input_id);
	var tmpv="";
	for(var k=0;k<ref.input_ref.variant_list.length;k++)
		if(ref.input_ref.variant_cb[k].checked)
		{
			if(tmpv!="")tmpv+=" ";
			tmpv+=ref.input_ref.variant_list[k];
		}
	ref.input_ref.value=tmpv;
}

function tddcb_td_click(ref,ev,t_k)
{
	//var input_ref=document.getElementById(input_id);
	ref.input_ref.variant_cb[t_k].checked=(ref.input_ref.variant_cb[t_k].checked)?false:true;
	tddcb_update(ref);
	ev.stopPropagation();
	ref.input_ref.variant_cb[t_k].focus();
	return false;
	
}

function tddcb_cb_focus(ref)
{
	ref.tr_ref.style.backgroundColor='blue';
	clearTimeout(ref.input_ref.hidetimeout);
	chse.activatemon(ref.input_ref.chse_struct);
}

function tddcb_cb_blur(ref,div_id)
{
//	var input_ref=document.getElementById(input_id);
	ref.tr_ref.style.backgroundColor='white';
	ref.input_ref.hidetimeout=setTimeout("document.getElementById('"+div_id+"').style.display='none';",200);
	chse.latedeactivate(ref.input_ref);
}

function tddcb_deactivate(ref,div_id)
{
	ref.hidetimeout=setTimeout("document.getElementById('"+div_id+"').style.display='none';",200);
	chse.latedeactivate(ref);
}



function tddcb_keypress(ev,input_ref,div_id)
{
	var mkc=event_to_mkc(ev);
	var dd_div=document.getElementById(div_id);
	switch(mkc.keycode)
	{
	case 38://up
		if(input_ref.variant_cb)
		{
			input_ref.variant_cb[input_ref.variant_cb.length-1].focus();
		}
		break;
	case 40://down
		if(input_ref.variant_cb)
		{
			input_ref.variant_cb[0].focus();
		}
		break;
	case 13:
		if(dd_div.style.display=='none')dd_div.style.display='block';
		else dd_div.style.display='none';
		break;
	case 9:
	case 27:
		return true;
	default:
	//	event.stopPropagation();
	//	event.preventDefault();
		return stop_event(event);
	};
//	event.stopPropagation();
//	event.preventDefault();
	return stop_event(event);
}

function tddcb_cb_keypress(ev,input_ref,t_k)
{
	var mkc=event_to_mkc(ev);
//	var dd_div=document.getElementById(div_id);
	switch(mkc.keycode)
	{
	case 38://up
		if(input_ref.variant_cb)
		{
			var k=t_k-1;
			if(k<0)input_ref.focus();
			else input_ref.variant_cb[k].focus();
		}
		break;
	case 40://down
		if(input_ref.variant_cb)
		{
			var k=t_k+1;
			if(k==input_ref.variant_cb.length)input_ref.focus();
			else input_ref.variant_cb[k].focus();
		}
		break;
	case 13:
//		if(dd_div.style.display=='none')dd_div.style.display='block';
//		else dd_div.style.display='none';
		break;
	case 9:
	case 27:
	case 32:
		return true;
	default:
	//	event.stopPropagation();
	//	event.preventDefault();
		return stop_event(event);
	};
//	event.stopPropagation();
//	event.preventDefault();
	return stop_event(event);
}

function mn_keypress(ev,ref)
{
	var mkc=event_to_mkc(ev);
	switch(mkc.charcode)
	{
	case 13:
		try_evaluate_update(ref);
		return false;
	default:
		return true;
	};
//	event.stopPropagation();
//	event.preventDefault();
	return stop_event(event);
	
}

function try_evaluate(i)
{
	var tf=i;
	var ft=tf.replace(/abs|acos|asin|atan|atan2|ceil|cos|exp|floor|log|max|min|pow|random|round|sin|sqrt|tan/g,"");
	if(ft.match(/[^ 0-9\/+*%!<>?,.;:'"[\]\\{}()-](\w|\d)*/))
	{
		return "undefined";
	}
	tf=tf.replace(/abs\(/g,   "Math.abs(");
	tf=tf.replace(/acos\(/g,  "Math.acos(");
	tf=tf.replace(/asin\(/g,  "Math.asin(");
	tf=tf.replace(/atan\(/g,  "Math.atan(");
	tf=tf.replace(/atan2\(/g, "Math.atan2(");
	tf=tf.replace(/ceil\(/g,  "Math.ceil(");
	tf=tf.replace(/cos\(/g,   "Math.cos(");
	tf=tf.replace(/exp\(/g,   "Math.exp(");
	tf=tf.replace(/floor\(/g, "Math.floor(");
	tf=tf.replace(/log\(/g,   "Math.log(");
	tf=tf.replace(/max\(/g,   "Math.max(");
	tf=tf.replace(/min\(/g,   "Math.min(");
	tf=tf.replace(/pow\(/g,   "Math.pow(");
	tf=tf.replace(/random\(/g,"Math.random(");
	tf=tf.replace(/round\(/g, "Math.round(");
	tf=tf.replace(/sin\(/g,   "Math.sin(");
	tf=tf.replace(/sqrt\(/g,  "Math.sqrt(");
	tf=tf.replace(/tan\(/g,   "Math.tan(");
	
	var newval=eval(tf);
	if(typeof(newval) == 'undefined')
	{
		return "undefined";
	}
	return newval;
}

chse.fetchfuncs.push(function (o)
{
	
	if(o.objtype=='m_number')
	return function()
	{
		this.obj.style.backgroundColor='#d0d0ff';
		var r=try_evaluate(this.obj.value);
		return encodeURIComponent(r);
	};
	if(o.objtype=='date_selector')
	return function()
	{
		this.obj.style.backgroundColor='#d0d0ff';
		var r=ds_parse_eu(this.obj.value);
		if(typeof(r)!='object')
			r=ds_parse_iso(this.obj.value);
		if(typeof(r)!='object')
			r=ds_parse_us(this.obj.value);
		return encodeURIComponent(r.year+"-"+r.month+"-"+r.day);
	};
	return null;
}
);

chse.checkerfuncs.push(function (o)
{
	if(o.objtype=='m_number')
	{
		if((! o.obj.oldval)&&(o.obj.oldval != '') )
		{
			o.obj['oldval']=o.obj.value;
		}
		return function()
		{
			var r=try_evaluate(this.obj.value);
			this.obj.style.backgroundColor='red';
			if(r=="undefined" || r=="Infinity") return false;
			this.obj.style.backgroundColor='';
			if(this.obj.oldval==r) return false;
			this.obj.oldval=r;
			return true;
		}
	}
	if(o.objtype=='date_selector')
	{
		if((! o.obj.oldval)&&(o.obj.oldval != '') )
		{
			o.obj['oldval']=o.obj.value;
		}
		return function()
		{
			if(this.obj.oldval==o.obj.value) return false;
			this.obj.style.backgroundColor='red';
			var r=ds_parse_eu(this.obj.value);
			if(typeof(r)!='object')
				r=ds_parse_iso(this.obj.value);
			if(typeof(r)!='object')
				r=ds_parse_us(this.obj.value);
			if(typeof(r)!='object') return false;
			o.obj['oldval']=o.obj.value;
			this.obj.style.backgroundColor='';
			return true;
		};
	}
}
);

function try_evaluate_update(i)
{
	var tf=i.value;
	var ft=tf.replace(/abs|acos|asin|atan|atan2|ceil|cos|exp|floor|log|max|min|pow|random|round|sin|sqrt|tan/g,"");
	if(ft.match(/[^ 0-9\/+*%!<>?,.;:'"[\]\\{}()-](\w|\d)*/))
	{
		i.style.background='red';
		return false;
	}
	tf=tf.replace(/abs\(/g,   "Math.abs(");
	tf=tf.replace(/acos\(/g,  "Math.acos(");
	tf=tf.replace(/asin\(/g,  "Math.asin(");
	tf=tf.replace(/atan\(/g,  "Math.atan(");
	tf=tf.replace(/atan2\(/g, "Math.atan2(");
	tf=tf.replace(/ceil\(/g,  "Math.ceil(");
	tf=tf.replace(/cos\(/g,   "Math.cos(");
	tf=tf.replace(/exp\(/g,   "Math.exp(");
	tf=tf.replace(/floor\(/g, "Math.floor(");
	tf=tf.replace(/log\(/g,   "Math.log(");
	tf=tf.replace(/max\(/g,   "Math.max(");
	tf=tf.replace(/min\(/g,   "Math.min(");
	tf=tf.replace(/pow\(/g,   "Math.pow(");
	tf=tf.replace(/random\(/g,"Math.random(");
	tf=tf.replace(/round\(/g, "Math.round(");
	tf=tf.replace(/sin\(/g,   "Math.sin(");
	tf=tf.replace(/sqrt\(/g,  "Math.sqrt(");
	tf=tf.replace(/tan\(/g,   "Math.tan(");
	
	var newval=eval(tf);
	if(typeof(newval) == 'undefined')
	{
		i.style.background='red';
		return false;
	}
	if(newval == 'Infinity')
	{
		i.style.background='red';
		return false;
	}
	i.value=newval;
	i.style.background='';
	return true;
}

function ds_parse_gen(d)
{
	if(! d[0].match(/\d+/))return false;
	if(! d[1].match(/\d+/))return false;
	if(! d[2].match(/\d+/))return false;
	var r=new Object();
	var date=new Date();
	r['day']=d[2]*1;
	r['month']=d[1]*1;
	r['year']=d[0]*1;
	if(isNaN(r.day))return false;
	if(isNaN(r.month))return false;
	if(isNaN(r.year))return false;
	if(r.year<0)return false;
	$i('debug').textContent=d[0]+'/'+d[1]+'/'+d[2]+':'+date.getFullYear() +'-'+ date.getFullYear()%100 +'-'+ ((r.year>(date.getFullYear()%100))?100:0)+":"+r['year'];
	if(r.year<100)r.year+=(date.getFullYear() - date.getFullYear()%100 - ((r.year>(date.getFullYear()%100))?100:0));
	if(r.month>12)return false;
	if(r.month<1)return false;
	if(r.month==12)
	{
		date.setFullYear(r.year+1,0,0)
	}else{
		date.setFullYear(r.year,r.month,0)
	}
	if(r.day>date.getDate())return false;
	if(r.day<0)return false;
	return r;
}

function ds_parse_eu(v)
{
	/*31.12.2009*/
	if(v.match(/^\d\d?.\d\d?.(?:\d\d){1,2}$/))
	{
		var d=v.replace(/^(\d\d?).(\d\d?).((?:\d\d){1,2})$/,'$3 $2 $1').split(/ /);
		return ds_parse_gen(d);
	}else{
		return false;
	}
}

function ds_parse_iso(v)
{
	/*31.12.2009*/
	if(v.match(/^\d\d(?:\d\d)?.\d\d?.\d\d?$/))
	{
		var d=v.replace(/^(\d\d(?:\d\d)?).(\d\d?).(\d\d?)$/,'$1 $2 $3').split(/ /);
		return ds_parse_gen(d);
	}else{
		return false;
	}
}

function ds_parse_us(v)
{
	/*31.12.2009*/
	if(v.match(/^\d\d?.\d\d?.\d\d(?:\d\d)?$/))
	{
		var d=v.replace(/^(\d\d?).(\d\d?).(\d\d(?:\d\d)?)$/,'$3 $1 $2').split(/ /);
		return ds_parse_gen(d);
	}else{
		return false;
	}
}


function emo_highlight(o_id,on)
{
	var o=document.getElementById(o_id);
	var i;
	try{if(o.sel_n!=-1)i=$i(o.sel_path[o.sel_level].items[o.sel_n].id_r);}catch(e){i=null;};
	var col='';
	switch(on)
	{
	case 0://off
		col='';
		break;
	case 1://active
		col='blue';
		break;
	case 2://prev
		col='grey';
		break;
	};
	if(i!=null)i.style.backgroundColor=col;
	if(col=='blue')col='grey';
	for(var k=0;k<o.sel_path.length;k++)
	{
		if(k!=o.sel_level)try{$i(o.sel_path[k].items[o.sel_path[k].old_n].id_r).style.backgroundColor=(k>o.sel_level)?'':col;}catch(e){};
	};
}

function emo_keypress(ev,o_id)
{
	var mkc=event_to_mkc(ev);
	var o=document.getElementById(o_id);
	if(typeof(o.sel_level)=='undefined')
		o.sel_level=0;
	if(typeof(o.sel_n)=='undefined')
		o.sel_n=-1;
	var i;
	switch(mkc.keycode)
	{
	case 38://up
			emo_highlight(o_id,0);
			i=null;
			o.sel_n--;if(o.sel_n<-1)o.sel_n=o.sel_path[o.sel_level].items.length-1;
			emo_highlight(o_id,1);
		break;
	case 40://down
			emo_highlight(o_id,0);
			i=null;
			o.sel_n++;if(o.sel_n>o.sel_path[o.sel_level].items.length-1)o.sel_n=-1;
			emo_highlight(o_id,1);
		break;
	case 39://right
			try
			{
				var b=$i(o.sel_path[o.sel_level].items[o.sel_n].id_btn);
				if(b==null)break;
				if(typeof(b.onclick)!='function')break;
				if(o.sel_n!=-1)
				{
					i=$i(o.sel_path[o.sel_level].items[o.sel_n].id_r);
					o.sel_path[o.sel_level].old_n=o.sel_n;
				};
				emo_highlight(o_id,1);
				i=null;
				if(b!=null)b.onclick();
			}catch(e){};
			
		break;
	case 37://left
			emo_highlight(o_id,0);
			i=null;
			var b;
			//try{b=$i(o.sel_path[o.sel_level].items[o.sel_n].id_btn);}catch(e){b=null;};
			//if(o.sel_level>0)$i(o.sel_path[o.sel_level].id).style.display='none';
			o.sel_level--;if(o.sel_level<0)o.sel_level=0;
			o.sel_n=o.sel_path[o.sel_level].old_n;
			emo_highlight(o_id,1);
			
		break;
	case 13:
		try{if(o.sel_n!=-1)i=$i(o.sel_path[o.sel_level].items[o.sel_n].id_0);}catch(e){i=null;};
		if(i==null)break;
		if(typeof(i.onclick)!='function')break;
		i.onclick();
		break;
	case 9:
	case 27:
	case 32:
		return true;
	default:
	//	event.stopPropagation();
	//	event.preventDefault();
		return stop_event(event);
	};
//	event.stopPropagation();
//	event.preventDefault();
	return stop_event(event);
}

function ed_tree_main_ctl_k(event,object,t)
{
	var is=is_special(event,t);
	if(!is.special)return true;
	switch(event.keyCode)
	{
	case 38://up
		if(!is.leave && !is.repeated)return stop_event(event);
		if(object.id_current!=-1)$i(object.id_list[object.id_current].cid).style.backgroundColor='';
		
		if(object.id_current==-1)object.id_current=object.id_list.length-1;
		else object.id_current--;
		
		if(object.id_current!=-1)
		{
			$i(object.id_list[object.id_current].cid).style.backgroundColor='#d0d0ff';
			$i(object.id_list[object.id_current].cid).scrollIntoView();
			ed_tree_item_act(object);
		}
		return stop_event(event);
		
	case 40://down
		if(!is.leave && !is.repeated)return stop_event(event);
		if(object.id_current!=-1)$i(object.id_list[object.id_current].cid).style.backgroundColor='';
		
		if(object.id_current==object.id_list.length-1)object.id_current=-1;
		else object.id_current++;
		
		if(object.id_current!=-1)
		{
			$i(object.id_list[object.id_current].cid).style.backgroundColor='#d0d0ff';
			$i(object.id_list[object.id_current].cid).scrollIntoView();
			ed_tree_item_act(object);
		}
		return stop_event(event);
	case 46://del
		if(!is.leave && !is.repeated)return stop_event(event);
		if(object.id_current==-1)break;
		$i('debug')[text_content]='del '+object.id_list[object.id_current].keys;
		chse.send_or_push({static:object.send_static+'=del&path='+
			encodeURIComponent(object.id_list[object.id_current].keys)+
			'&parent_id='+encodeURIComponent(object.id_list[object.id_current].pcid)+'&n',val:'',c_id:this.id});
		return stop_event(event);
	}
	
	return true;
}

function ed_tree_fa_item_click(object_id,path)
{
	var id_list=$i(object_id).id_list;
	for(var k=0;k<id_list.length;k++)
		if(id_list[k].keys==path)break;
	var id_current=$i(object_id).id_current;
	if(id_current!=-1)$i(id_list[id_current].cid).style.backgroundColor='';
	$i(object_id).id_current=k;
	$i(id_list[k].cid).style.backgroundColor='#d0d0ff';
	$i(object_id).focus();
	ed_tree_item_act($i(object_id));
	
}

function ed_tree_item_act(object)
{
	if(object.id_current==-1)return;
	if(object.act_timeout)clearTimeout(object.act_timeout);
	object.act_timeout=setTimeout(function(){
		chse.send_or_push({static:object.send_static+'=activate&path='+
			encodeURIComponent(object.id_list[object.id_current].keys)+
			'&n',val:'',c_id:this.id});
		},100);
}

function ed_tree_fa_item_up(event,object_id,path)
{
	if(resizer.drag_context.active)
	{
		var object=$i(object_id);
		var id_list=object.id_list;
		for(var k=0;k<id_list.length;k++)
			if(id_list[k].keys==path)break;
		var act='moveti';
		if(event.ctrlKey)act='copyti';
		if(resizer.drag_context.data.t=='ti')
		{
			if(path==resizer.drag_context.data.d)return;
			chse.send_or_push({static:object.send_static+'='+act+'&before='+
				encodeURIComponent(object.id_list[k].keys)+
				'&path='+
				encodeURIComponent(resizer.drag_context.data.d)+
				'&parent_id='+encodeURIComponent(object.id_list[k].pcid)+'&n',val:'',c_id:object.id});
		}
		if(resizer.drag_context.data.t=='cl')
		{
			chse.send_or_push({static:object.send_static+'=copycl&before='+
				encodeURIComponent(object.id_list[k].keys)+
				'&clipboard=1&n',val:'',c_id:object.id});
		}
	}
}

function ed_tree_fa_item_mov(event,object_id,path,s)
{
	if(resizer.drag_context.active)
	{
		if(event.ctrlKey)resizer.drag_context.plus.style.display='block';else resizer.drag_context.plus.style.display='none';
		s.style.backgroundColor='#ffeeee';
	}
	return true;
}

function ed_tree_fa_item_mou(event,object_id,path,s)
{
	if(resizer.drag_context.active)
	{
		resizer.drag_context.plus.style.display='none';
		s.style.backgroundColor='';
	}
	return true;
}

//--------------- ed_tree_clip
function ed_tree_clip_up(event,object)
{
	if(resizer.drag_context.active)
	{
		var act='moveti';
		if(event.ctrlKey)act='copyti';
		if(resizer.drag_context.data.t=='ti')
		{
			$i('debug')[text_content]='enn';
			chse.send_or_push({static:object.send_static+'='+act+
				'&path='+
				encodeURIComponent(resizer.drag_context.data.d)+'&n',val:'',c_id:object.id});
		}
	}
}

function ed_tree_clip_mov(event,object)
{
}

function ed_tree_clip_mou(event,object)
{
}

//------------------------- Keyboard support functions
//------------------------- Move from this file later

function key_from_code(code)
{
	var r={code:code,special:false,printable:true,toggle:false,name:''};
	switch(code)
	{
	case 9:
	case 8:
	case 13:
	case 19:
	case 27:
		r.printable=true;
		r.special=true;
		break;
	case 33:
	case 34:
	case 35:
	case 36:
	case 37:
	case 38:
	case 39:
	case 40:
	case 45:
	case 46:
		r.printable=false;
		r.special=true;
		break;
	case 20:
	case 144:
	case 145:
		r.printable=false;
		r.special=true;
		r.toggle=true;
		break;
	}
	if(code>=112 && code <= 123){r.printable=false;r.special=true;r.name='F'+(code-111);};//F1-F12
	if(code==8)r.name='Backspace';
	if(code==9)r.name='Tab';
	if(code==13)r.name='Enter';
	if(code==19)r.name='Pause/Break';
	if(code==27)r.name='Escape';
	if(code==33)r.name='PageUp';
	if(code==34)r.name='PageDn';
	if(code==35)r.name='End';
	if(code==36)r.name='Home';
	if(code==37)r.name='Left';
	if(code==38)r.name='Up';
	if(code==39)r.name='Right';
	if(code==40)r.name='Down';
	if(code==45)r.name='Insert';
	if(code==46)r.name='Delete';
	if(code==20)r.name='CapsLock';
	if(code==144)r.name='NumLock';
	if(code==145)r.name='ScrollLock';
	return r;
}

function is_special(ev,t)
{
	//return 0 if normal; |1 if special; |2 if special and repeated
	var ret={leave:false,special:false,repeated:false};
	switch(window.keyboard_fix)
	{
	case 0://opera
		//onkeypress event and keyCode
		var r=key_from_code(ev.keyCode);
		if(r.special && (ev.which==0) && (t==1))ret.special=true;
		if(r.special && (ev.which==ev.keyCode) && (t!=1))ret.special=true;
		if(t==1)ret.repeated=true;
		return ret;
	case 1://firefox
		//onkeypress event and keyCode, same as opera
		var r=key_from_code(ev.keyCode);
		if(r.special && (ev.which==0) && (t==1))ret.special=true;
		if(r.special && (ev.which==ev.keyCode) && (t!=1))ret.special=true;
		if(t==1)ret.repeated=true;
		return ret;
	case 2://konqueror
		//onkeypress event and keyCode, same as opera and mozilla, good
		var r=key_from_code(ev.keyCode);
		if(r.special)ret.special=true;
		if((t==0) && ret.special)ret.leave=true;
		if(t==1)ret.repeated=true;
		return ret;
	case 3://AppleWebkit
		//onkeydown event and keyCode not good
		var r=key_from_code(ev.keyCode);
		if(r.special)ret.special=true;
		if(t==0)ret.repeated=true;
		return ret;
	case 4://MSIE
		//onkeydown event and keyCode not good, same as webkit
		var r=key_from_code(ev.keyCode);
		if(r.special)ret.special=true;
		if(t==0)ret.repeated=true;
		return ret;
	}
	return ret;
}




function keyboard_test_btn_key(event,obj,t)
{
	var tv=0;
	if(!event)event=window.event;
	switch(t)
	{
	case 0:
		
		tv=78;
		try{if(event.charCode==tv)obj.testnum=1;}catch(e){};
		try{if(event.keyCode==tv)obj.testnum=1;}catch(e){};
		try{if(event.which==tv)obj.testnum=1;}catch(e){};
		tv=16;
		try{if(event.charCode==tv)obj.testnum=2;}catch(e){};
		try{if(event.keyCode==tv)obj.testnum=2;}catch(e){};
		try{if(event.which==tv)obj.testnum=2;}catch(e){};
		tv=13;
		try{if(event.charCode==tv)obj.testnum=3;}catch(e){};
		try{if(event.keyCode==tv)obj.testnum=3;}catch(e){};
		try{if(event.which==tv)obj.testnum=3;}catch(e){};
		tv=40;
		try{if(event.charCode==tv)obj.testnum=4;}catch(e){};
		try{if(event.keyCode==tv)obj.testnum=4;}catch(e){};
		try{if(event.which==tv)obj.testnum=4;}catch(e){};
	case 1:
	case 2:
		if(typeof(obj.testnum)=='undefined')obj.testnum=0;
		if(typeof(obj.result_struct[obj.testnum])=='undefined')obj.result_struct[obj.testnum]=new Array();
		if(typeof(obj.result_struct[obj.testnum][t])=='undefined')
		{
			obj.result_struct[obj.testnum][t]={ok:1,cnt:1};
			if(typeof(event.charCode)!='undefined')obj.result_struct[obj.testnum][t].cc=event.charCode;
			if(typeof(event.keyCode)!='undefined')obj.result_struct[obj.testnum][t].kc=event.keyCode;
			if(typeof(event.which)!='undefined')obj.result_struct[obj.testnum][t].wh=event.which;
		}
		else obj.result_struct[obj.testnum][t].cnt++;
	}
	if(obj.testnum != 0)
		$i(obj.result_divs[obj.testnum][t])[text_content]=obj.result_struct[obj.testnum][t].cc+'/'
			+obj.result_struct[obj.testnum][t].kc+'/'+obj.result_struct[obj.testnum][t].wh+'/'+obj.result_struct[obj.testnum][t].cnt;
}


