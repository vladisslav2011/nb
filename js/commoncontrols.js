


function barcode_bin(l,width,height,mode)
{
	var r=document.createElement('table');
	r.style.borderCollapse='collapse';
	var tr=document.createElement('tr');
	r.appendChild(tr);
	var current_color='black';
	var d,k,tl,tv;
	k=0;
	while(k<l.length)
	{
		tl=0;
		tv=l[k];
		while(tv==l[k])
		{
			tl++;
			k++;
			if(k==l.length)break;
		}
		var td=document.createElement('td');
		td.style.border='0px solid white';
		td.style.padding='0px';
		td.style.margin='0px';
		if(mode==0)//backgroundColor
		{
			d=document.createElement('div');
			d.style.border='0px solid white';
			d.style.padding='0px';
			d.style.margin='0px';
			td.appendChild(d);
			d.appendChild(document.createTextNode(' '));
			tr.appendChild(td);
			d.style.backgroundColor=current_color;
		}else{
			d=document.createElement('img');
			d.style.border='0px solid white';
			d.style.padding='0px';
			d.style.margin='0px';
			td.appendChild(d);
			tr.appendChild(td);
			d.setAttribute('src','/i/'+current_color+'.png');
		}
		d.style.width=(parseInt(width.replace(/[^0-9]/,''))*tl).toString()+width.replace(/[0-9]/,'');
		d.style.height=height;
		if(current_color=='black')
			current_color='white';
		else
			current_color='black';
			
	}
	return r;
}

function barcode_len(l,width,height,mode)
{
	var r=document.createElement('table');
	r.style.borderCollapse='collapse';
	var tr=document.createElement('tr');
	r.appendChild(tr);
	var current_color='black';
	var d;
	for(var k=0;k<l.length;k++)
	{
		var td=document.createElement('td');
		td.style.border='0px solid white';
		td.style.padding='0px';
		td.style.margin='0px';
		if(mode==0)//backgroundColor
		{
			d=document.createElement('div');
			d.style.border='0px solid white';
			d.style.padding='0px';
			d.style.margin='0px';
			td.appendChild(d);
			d.appendChild(document.createTextNode(' '));
			tr.appendChild(td);
			d.style.backgroundColor=current_color;
		}else{
			d=document.createElement('img');
			d.style.border='0px solid white';
			d.style.padding='0px';
			d.style.margin='0px';
			td.appendChild(d);
			tr.appendChild(td);
			d.setAttribute('src','/i/'+current_color+'.png');
		}
		d.style.width=(parseInt(width.replace(/[^0-9]/,''))*parseInt(l[k])).toString()+width.replace(/[0-9]/,'');
		d.style.height=height;
		if(current_color=='black')
			current_color='white';
		else
			current_color='black';
			
	}
	return r;
}


function code128l(start,code,width,height,mode)
{
	var code128=Array(
		Array(0,"212222"," "," ","00"),
		Array(1,"222122","!","!","01"),
		Array(2,"222221","\"","\"","02"),
		Array(3,"121223","#","#","03"),
		Array(4,"121322","$","$","04"),
		Array(5,"131222","%","%","05"),
		Array(6,"122213","&","&","06"),
		Array(7,"122312","'","'","07"),
		Array(8,"132212","(","(","08"),
		Array(9,"221213",")",")","09"),
		Array(10,"221312","*","*","10"),
		Array(11,"231212","+","+","11"),
		Array(12,"112232",",",",","12"),
		Array(13,"122132","-","-","13"),
		Array(14,"122231",".",".","14"),
		Array(15,"113222","/","/","15"),
		Array(16,"123122","0","0","16"),
		Array(17,"123221","1","1","17"),
		Array(18,"223211","2","2","18"),
		Array(19,"221132","3","3","19"),
		Array(20,"221231","4","4","20"),
		Array(21,"213212","5","5","21"),
		Array(22,"223112","6","6","22"),
		Array(23,"312131","7","7","23"),
		Array(24,"311222","8","8","24"),
		Array(25,"321122","9","9","25"),
		Array(26,"321221",":",":","26"),
		Array(27,"312212",";",";","27"),
		Array(28,"322112","<","<","28"),
		Array(29,"322211","=","=","29"),
		Array(30,"212123",">",">","30"),
		Array(31,"212321","?","?","31"),
		Array(32,"232121","@","@","32"),
		Array(33,"111323","A","A","33"),
		Array(34,"131123","B","B","34"),
		Array(35,"131321","C","C","35"),
		Array(36,"112313","D","D","36"),
		Array(37,"132113","E","E","37"),
		Array(38,"132311","F","F","38"),
		Array(39,"211313","G","G","39"),
		Array(40,"231113","H","H","40"),
		Array(41,"231311","I","I","41"),
		Array(42,"112133","J","J","42"),
		Array(43,"112331","K","K","43"),
		Array(44,"132131","L","L","44"),
		Array(45,"113123","M","M","45"),
		Array(46,"113321","N","N","46"),
		Array(47,"133121","O","O","47"),
		Array(48,"313121","P","P","48"),
		Array(49,"211331","Q","Q","49"),
		Array(50,"231131","R","R","50"),
		Array(51,"213113","S","S","51"),
		Array(52,"213311","T","T","52"),
		Array(53,"213131","U","U","53"),
		Array(54,"311123","V","V","54"),
		Array(55,"311321","W","W","55"),
		Array(56,"331121","X","X","56"),
		Array(57,"312113","Y","Y","57"),
		Array(58,"312311","Z","Z","58"),
		Array(59,"332111","[","[","59"),
		Array(60,"314111","\\","\\","60"),
		Array(61,"221411","]","]","61"),
		Array(62,"431111","^","^","62"),
		Array(63,"111224","_","_","63"),
		Array(64,"111422","NUL","`","64"),
		Array(65,"121124","SOH","a","65"),
		Array(66,"121421","STX","b","66"),
		Array(67,"141122","ETX","c","67"),
		Array(68,"141221","EOT","d","68"),
		Array(69,"112214","ENQ","e","69"),
		Array(70,"112412","ACK","f","70"),
		Array(71,"122114","BEL","g","71"),
		Array(72,"122411","BS","h","72"),
		Array(73,"142112","HT","i","73"),
		Array(74,"142211","LF","j","74"),
		Array(75,"241211","VT","k","75"),
		Array(76,"221114","FF","l","76"),
		Array(77,"413111","CR","m","77"),
		Array(78,"241112","SO","n","78"),
		Array(79,"134111","SI","o","79"),
		Array(80,"111242","DLE","p","80"),
		Array(81,"121142","DC1","q","81"),
		Array(82,"121241","DC2","r","82"),
		Array(83,"114212","DC3","s","83"),
		Array(84,"124112","DC4","t","84"),
		Array(85,"124211","NAK","u","85"),
		Array(86,"411212","SYN","v","86"),
		Array(87,"421112","ETB","w","87"),
		Array(88,"421211","CAN","x","88"),
		Array(89,"212141","EM","y","89"),
		Array(90,"214121","SUB","z","90"),
		Array(91,"412121","ESC","{","91"),
		Array(92,"111143","FS","|","92"),
		Array(93,"111341","GS","}","93"),
		Array(94,"131141","RS","~","94"),
		Array(95,"114113","US","DEL","95"),
		Array(96,"114311","FNC 3","FNC 3","96"),
		Array(97,"411113","FNC 2","FNC 2","97"),
		Array(98,"411311","Shift B","Shift A","98"),
		Array(99,"113141","Code C","Code C","99"),
		Array(100,"114131","Code B","FNC4","Code B"),
		Array(101,"311141","FNC 4","Code A","Code A"),
		Array(102,"411131","FNC 1","FNC 1","FNC 1"),
		Array(103,"211412","Start Code A","Start Code A","Start Code A"),
		Array(104,"211214","Start Code B","Start Code B","Start Code B"),
		Array(105,"211232","Start Code C","Start Code C","Start Code C"),
		Array(106,"2331112","Stop","Stop","Stop")
	);
	
	var seq=Array();
	//iterate over objects
	var set=2;
	var reset_to=0;
	switch(start)
	{
	case "A":
	case "a":
		seq.push(103);
		set=2;
	break;
	case "B":
	case "b":
		seq.push(104);
		set=3;
	break;
	case "C":
	case "c":
		seq.push(105);
		set=4;
	break;
	};
	var inent=false;
	var ent="";
	for(var k=0;k<code.length;k++)
	{
		if(inent && code[k]!=">")
		{
			ent+=code[k];
			continue;
		}
		if(inent && code[k]==">")
		{
			var ient=0;
			inent=false;
			if(ent.match(/[0-9+]/))
			{
				ient=parseInt(ent);
			}else{
				for(var t=0;t<107;t++)
					if(code128[t][set]==ent)
					{
						ient=t;
						break;
					}
			}
			if(reset_to!=0)
			{
				set=reset_to;
				reset_to=0;
			}
			seq.push(ient);
			if((ient==99)&&(set!=4))
			{
				set=4;
				continue;
			};
			if((ient==100)&&(set!=3))
			{
				set=3;
				continue;
			};
			if((ient==101)&&(set!=2))
			{
				set=2;
				continue;
			};
			if((ient==98)&&(set==2))
			{
				set=3;
				reset_to=2;
				continue;
			};
			if((ient==98)&&(set==3))
			{
				set=2;
				reset_to=3;
				continue;
			};
			continue;
		}
		if(code[k]=="<")
		{
			inent=true;
			ent="";
		}else{
			if(set==2)//a
			{
				for(var t=0;t<107;t++)
					if(code128[t][set]==code[k])
					{
						seq.push(t);
						break;
					}
			}
			if(set==3)//b
			{
				for(var t=0;t<107;t++)
					if(code128[t][set]==code[k])
					{
						seq.push(t);
						break;
					}
			}
			if(set==4)//c
			{
				if(k+1 < code.length)
				{
					seq.push(parseInt(((code[k]==0)?"":code[k].toString())+code[k+1].toString()));
					k++;
				}else{
					seq.push(101);
					set=2;
				}
			}
		}
		if(reset_to!=0)
		{
			set=reset_to;
			reset_to=0;
		}
	}
	var res="";
	var check=0;
	for(var k=0;k<seq.length;k++)
	{
		if(k==0)check+=seq[k];
		else check+=seq[k]*k;
		res+=code128[seq[k]][1];
	}
	check=check % 103;
	res+=code128[check][1];
	res+=code128[106][1];
	return barcode_len(res,width,height,mode);
}

function barcode_gen_ean_sum(ean){
  var even=true; var esum=0; var osum=0;
  var se=ean.toString();
  for (var i=se.length-1;i>=0;i--)
  {
	if (even) esum+=parseInt(se[i]);	else osum+=parseInt(se[i]);
	even=!even;
  }
  return (10-((3*esum+osum)%10))%10;
}

function code39l(notused,code,width,height,mode)
{
	var code39=Array(
{c:"0",s:"1113313111"},
{c:"1",s:"3113111131"},
{c:"2",s:"1133111131"},
{c:"3",s:"3133111111"},
{c:"4",s:"1113311131"},
{c:"5",s:"3113311111"},
{c:"6",s:"1133311111"},
{c:"7",s:"1113113131"},
{c:"8",s:"3113113111"},
{c:"9",s:"1133113111"},
{c:"A",s:"3111131131"},
{c:"B",s:"1131131131"},
{c:"C",s:"3131131111"},
{c:"D",s:"1111331131"},
{c:"E",s:"3111331111"},
{c:"F",s:"1131331111"},
{c:"G",s:"1111133131"},
{c:"H",s:"3111133111"},
{c:"I",s:"1131133111"},
{c:"J",s:"1111333111"},
{c:"K",s:"3111111331"},
{c:"L",s:"1131111331"},
{c:"M",s:"3131111311"},
{c:"N",s:"1111311331"},
{c:"O",s:"3111311311"},
{c:"P",s:"1131311311"},
{c:"Q",s:"1111113331"},
{c:"R",s:"3111113311"},
{c:"S",s:"1131113311"},
{c:"T",s:"1111313311"},
{c:"U",s:"3311111131"},
{c:"V",s:"1331111131"},
{c:"W",s:"3331111111"},
{c:"X",s:"1311311131"},
{c:"Y",s:"3311311111"},
{c:"Z",s:"1331311111"},
{c:"-",s:"1311113131"},
{c:".",s:"3311113111"},
{c:" ",s:"1331113111"},
{c:"$",s:"1313131111"},
{c:"/",s:"1313111311"},
{c:"+",s:"1311131311"},
{c:"%",s:"1113131311"},
{c:"*",s:"1311313111"}
);
	
	var cs="*"+(code.toString().replace(/\*/,''))+"*";
	var rc="";
	
	for(var k=0;k<cs.length;k++)
		for(var t=0;t<code39.length;t++)
			if(code39[t].c==cs[k])
				rc=rc+code39[t].s;
	return barcode_len(rc,width,height,mode);
}


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
		return this.obj.value;
	};
	if(o.objtype.match(/editor_checkbox_.*/) || o.objtype=='editor_checkbox')
		return function()
		{
			return this.obj.checked?1:0;
		}
	if(o.objtype=='editor_password_md5')
		return function()
		{
			this.obj.style.backgroundColor='#d0d0ff';
			return window.md5.hex_md5(this.obj.value);
		}
	return null;
}
);

chse.checkerfuncs.push(function (o)
{
	if(o.objtype.match(/editor_text_.*/)||
	(o.objtype=='editor_text')||
	(o.objtype=='editor_password_md5')||
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



function my_scroll_into_view(i,o)
{
	var co=o.offsetTop;
	if(i.offsetParent != o.offsetParent)
	{
		var m=i;
		while(m.offsetParent != o.offsetParent)
		{
			m=m.offsetParent;
			//compensate offset. Another way is to increment b...
			co-=m.offsetTop;
		};
	};
	//test if top border of i is less than o.scrollTop
	var b=i.offsetTop;
	if(b<o.scrollTop+co)o.scrollTop=b-co;
	//test if botom border of i is greater than o.scrollTop+o.clientHeight
	b+=i.offsetHeight;
	if(b>o.scrollTop+o.clientHeight+co)o.scrollTop=b-o.clientHeight-co;
	//my be it is better to scroll outer element to the top border of inner than to the bottom in case i.offsetHeight>o.clientHeight???
}



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
				my_scroll_into_view(o,div);
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
				my_scroll_into_view(o,div);
				
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
				my_scroll_into_view(o,div);
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
				my_scroll_into_view(o,div);
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
		return r;
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
		return (r.year+"-"+r.month+"-"+r.day);
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
		if(is.leave && !is.repeated)return true;
		if(object.id_current!=-1)$i(object.id_list[object.id_current].cid).style.backgroundColor='';
		
		if(object.id_current==-1)object.id_current=object.id_list.length-1;
		else object.id_current--;
		
		if(object.id_current!=-1)
		{
			var i=$i(object.id_list[object.id_current].cid);
			i.style.backgroundColor='#d0d0ff';
			my_scroll_into_view(i,$i(object.fa_id));
			//$i(object.id_list[object.id_current].cid).scrollIntoView();
			ed_tree_item_act(object,false);
		}
		return stop_event(event);
		
	case 40://down
		if(!is.leave && !is.repeated)return stop_event(event);
		if(is.leave && !is.repeated)return true;
		if(object.id_current!=-1)$i(object.id_list[object.id_current].cid).style.backgroundColor='';
		
		if(object.id_current==object.id_list.length-1)object.id_current=-1;
		else object.id_current++;
		
		if(object.id_current!=-1)
		{
			var i=$i(object.id_list[object.id_current].cid);
			i.style.backgroundColor='#d0d0ff';
			my_scroll_into_view(i,$i(object.fa_id));
			ed_tree_item_act(object,false);
		}
		return stop_event(event);
	case 46://del
		if(!is.leave && !is.repeated)return stop_event(event);
		if(is.leave && !is.repeated)return true;
		if(object.id_current==-1)break;
		var m=event_to_mkc(event);
		var st=object.send_static;
		st.last_generated_id=last_generated_id;
		if(m.m==m.SHIFT)//cut
		{
			st.path=object.id_list[object.id_current].keys;
			chse.send_or_push({static:st,val:'movecl',c_id:object.id});
			return stop_event(event);
		}
		if(m.m==m.CTRL)//clear
		{
			chse.send_or_push({static:st,val:'clipboard_clear',c_id:object.id});
			return stop_event(event);
		}
		st.path=object.id_list[object.id_current].keys;
		st.parent_id=object.id_list[object.id_current].pcid;
		chse.send_or_push({static:st,val:'del',c_id:this.id});
		return stop_event(event);
	case 45://insert
		if(!is.leave && !is.repeated)return stop_event(event);
		if(is.leave && !is.repeated)return true;
		var m=event_to_mkc(event);
		var st=object.send_static;
		st.last_generated_id=last_generated_id;
		if(m.m==m.CTRL)//copy
		{
			st.path=object.id_list[object.id_current].keys;
			chse.send_or_push({static:st,val:'copycl',c_id:object.id});
		}
		if(m.m==m.SHIFT)//paste
		{
			st.before=object.id_list[object.id_current].keys;
			chse.send_or_push({static:st,val:'pastecl',c_id:object.id});
		}
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
	ed_tree_item_act($i(object_id),true);
	
}

function ed_tree_item_act(object,mouse)
{
	if(object.id_current==-1)return;
	if(object.act_timeout)clearTimeout(object.act_timeout);
	object.act_timeout=setTimeout(function(){
		var st=object.send_static;
		st.path=object.id_list[object.id_current].keys;
		st.mouse=(mouse?1:0);
		st.cid=object.id_list[object.id_current].cid;
		st.last_generated_id=last_generated_id;
		chse.send_or_push({static:st,val:'activate',c_id:this.id});
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
		var st=object.send_static;
		st.last_generated_id=last_generated_id;
		if(resizer.drag_context.data.t=='ti')
		{
			if(path==resizer.drag_context.data.d)return;
			st.before=object.id_list[k].keys;
			st.path=resizer.drag_context.data.d;
			st.parent_id=object.id_list[k].pcid;
			chse.send_or_push({static:st,val:act,c_id:object.id});
			return;
		}
		if(resizer.drag_context.data.t=='cl')
		{
			st.before=object.id_list[k].keys;
			st.clipboard=1;
			chse.send_or_push({static:st,val:'pastecl',c_id:object.id});
			return;
		}
		st.before=object.id_list[k].keys;
		st.cn=resizer.drag_context.data.t;
		chse.send_or_push({static:st,val:'pastenew',c_id:object.id});
	}
}

function ed_tree_fa_item_mov(event,object_id,path,s,move)
{
	if(resizer.drag_context.active)
	{
		if(event.ctrlKey)resizer.drag_context.plus.style.display='block';else resizer.drag_context.plus.style.display='none';
		s.style.backgroundColor='#ffeeee';
		return true;
	}
	var object=$i(object_id);
	if((object.hint_path==path) &&object.hint_displayed)
	{
		clearTimeout(object.hint_hide_timeout);
		return true;
	}else{
		if(move)return true;
		if((object.hint_path!=path) && object.hint_displayed)
		{
			object.hint_div.parentNode.removeChild(object.hint_div);
			delete object.hint_div;
			object.hint_displayed=false;
			clearTimeout(object.hint_hide_timeout);
		}
		object.hint_path=path;
		for(var k=0;k<object.id_list.length;k++)if(object.id_list[k].keys==path)break;
		
		try_show_hint(object,s,Array(
			{	src:'/i/copy.png',
				alt:'copy',
				title:'Copy to clipboard(Ctrl+Ins)',
				onclick:'remove_hint($i("'+object_id+'"));chse.send_or_push({static:'+object_serialize(object_merge($i(object_id).send_static,
				{path:path,last_generated_id:last_generated_id}))+',val:"copycl",c_id:"'+object_id+'"});'
			},
			{	src:'/i/paste.png',
				alt:'paste',
				title:'Paste from clipboard before this element(Ctrl+Ins)',
				onclick:'remove_hint($i("'+object_id+'"));chse.send_or_push({static:'+object_serialize(object_merge($i(object_id).send_static,
				{before:path,last_generated_id:last_generated_id}))+',val:"pastecl",c_id:"'+object_id+'"});'
			},
			{	src:'/i/cancel-delete.png',
				alt:'delete',
				title:'Delete object',
				onclick:'remove_hint($i("'+object_id+'"));chse.send_or_push({static:'+object_serialize(
				object_merge($i(object_id).send_static,
				{path:path,parent_id:object.id_list[k].pcid,last_generated_id:last_generated_id}))+',val:"del",c_id:"'+object_id+'"});'
			}));
//		object.hint_path=path;
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
	var object=$i(object_id);
	if(object.hint_displayed)
	{
		remove_hint(object);
		return true;
	}
	return true;
}

//--------------- ed_tree_clip
function ed_tree_clip_up(event,object)
{
	if(resizer.drag_context.active)
	{
		var act='movecl';
		if(event.ctrlKey)act='copycl';
		if(resizer.drag_context.data.t=='ti')
		{
			var st=object.send_static;
			st.path=resizer.drag_context.data.d;
			st.last_generated_id=last_generated_id;
			chse.send_or_push({static:st,val:act,c_id:object.id});
		}
	}
}

function ed_tree_clip_mov(event,object,ctl_id)
{
	if(resizer.drag_context.active)
	{
		if(event.ctrlKey)resizer.drag_context.plus.style.display='block';else resizer.drag_context.plus.style.display='none';
		object.style.backgroundColor='#ffeeee';
	}else{
		if(object.hint_displayed)
		{
			clearTimeout(object.hint_hide_timeout);
			return true;
		}else{
			if(ctl_id=='')return true;
			try_show_hint(object,object,Array(
				{	src:'/i/cancel-delete.png',
					alt:'clear',
					title:'Clear clipboard(Ctrl_Del)',
					onclick:'remove_hint($i("'+ctl_id+'"));chse.send_or_push({static:'+object_serialize(object_merge($i(ctl_id).send_static,
					{last_generated_id:last_generated_id}))+',val:"clipboard_clear",c_id:"'+ctl_id+'"});'
				}
				));
/*			object.hint_div=document.createElement('div');
			object.hint_div.style.position='absolute';
			object.hint_div.style.backgroundColor='white';
			object.hint_div.style.border='1px solid grey';
			var img=document.createElement('img');
			img.style.width='20px';
			img.style.height='20px';
			img.setAttribute('src','/i/cancel-delete.png');
			img.setAttribute('alt','clear');
			img.setAttribute('title','Clear clipboard(Ctrl_Del)');
			img.setAttribute('onclick',
			'chse.send_or_push({static:$i("'+ctl_id+'").send_static+"=clipboard_clear&last_generated_id=" + last_generated_id +"&n",val:"",c_id:"'+ctl_id+'"});');
			img.setAttribute('onmouseover','clearTimeout($i("'+object.id+'").hint_hide_timeout);');
			img.setAttribute('onmouseout','remove_hint($i("'+object.id+'"));');
			object.hint_div.appendChild(img);
			document.body.appendChild(object.hint_div);
			object.hint_displayed=true;
			var r=findPosXY(object);
			var nl=r.x+object.offsetWidth-object.hint_div.offsetWidth;
			if(nl<0)nl=0;
			object.hint_div.style.top=(r.y+object.offsetHeight)+'px';
			object.hint_div.style.left="-100px";
			setTimeout(function(){object.hint_div.style.left=(nl)+'px';},0);*/
		}
	}
	return true;
}





function ed_tree_clip_mou(event,object)
{
	if(resizer.drag_context.active)
	{
		resizer.drag_context.plus.style.display='none';
		object.style.backgroundColor='';
	}
	if(object.hint_displayed)
	{
		remove_hint(object);
		return true;
	}
	return true;
}

function remove_hint(object)
{
	object.hint_hide_timeout=setTimeout(function()
		{
			if(typeof(object.hint_div)!='undefined')
			{
				object.hint_div.parentNode.removeChild(object.hint_div);
				delete object.hint_div;
			};
			if(object.hint_displayed)object.hint_displayed=false;
		},200);
}

function try_show_hint(object,bind,struct)
{
	object.hint_div=document.createElement('div');
	object.hint_div.style.position='absolute';
	object.hint_div.style.backgroundColor='white';
	object.hint_div.style.border='1px solid grey';
	object.hint_div.style.zIndex='1000';
	var omover='clearTimeout($i("'+object.id+'").hint_hide_timeout);$i("'+bind.id+'").style.color="red";';
	var omout='remove_hint($i("'+object.id+'"));$i("'+bind.id+'").style.color="";';
	object.hint_div.setAttribute('onmouseover',omover);
	object.hint_div.setAttribute('onmouseout',omout);
	for(var k=0;k<struct.length;k++)
	{
		if(typeof(struct[k].href)!='undefined')
		{
			var a=document.createElement('a');
			var img=document.createElement('img');
			a.appendChild(img);
			a.setAttribute('href',struct[k].href);
			var d=a;
		}else{
			var img=document.createElement('img');
			var d=img;
		}
			
		if(typeof(struct[k].width)=='undefined')img.style.width='20px';else img.style.width=struct[k].width;
		if(typeof(struct[k].height)=='undefined')img.style.height='20px';else img.style.height=struct[k].height;
		if(typeof(struct[k].src)=='undefined')img.setAttribute('src','/i/unknown.png');else img.setAttribute('src',struct[k].src);
		if(typeof(struct[k].alt)=='undefined')img.setAttribute('alt',' ');else img.setAttribute('alt',struct[k].alt);
		if(typeof(struct[k].title)!='undefined')img.setAttribute('title',struct[k].title);
		if(typeof(struct[k].onclick)!='undefined')img.setAttribute('onclick',struct[k].onclick);
		//img.setAttribute('onmouseover',omover);
		//img.setAttribute('onmouseout',omout);
		object.hint_div.appendChild(d);
	}
	bind.parentNode.appendChild(object.hint_div);
	object.hint_displayed=true;
	var r=findPosXY(bind);
	var nl=r.x+bind.offsetWidth-object.hint_div.offsetWidth;
	if(nl<0)nl=0;
	object.hint_div.style.top=(r.y+bind.offsetHeight-3)+'px';
	object.hint_div.style.left="-100px";
	setTimeout(function(){if(object.hint_div)object.hint_div.style.left=(nl)+'px';},0);
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
		if(t==0)
		{
			if(
				(ev.keyCode==36 && ev.which==36)||
				(ev.keyCode==35 && ev.which==35)||
				(ev.keyCode==45 && ev.which==45)||
				(ev.keyCode==46 && ev.which==46)
			)
				window.opera_keyboard_fix0=true;
			else
				window.opera_keyboard_fix0=false;
		}
		if(
			r.special && (t==1) && window.opera_keyboard_fix0 && (
				(ev.keyCode==36 && ev.which==36)||
				(ev.keyCode==35 && ev.which==35)||
				(ev.keyCode==45 && ev.which==45)||
				(ev.keyCode==46 && ev.which==46)
				))ret.special=true;
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


function editor_text_ean13_focus(obj,form,key)
{
	if(obj.editor_text_ean13)
	{
		clearTimeout(obj.editor_text_ean13);
	}
	if(typeof(obj.editor_text_ean13_hint)!='undefined')
		return;
	var d=document.createElement('div');
	obj.editor_text_ean13_hint=d;
	d.style.position='absolute';
	d.style.backgroundColor='white';
		obj.editor_text_ean13_hint.style.display='block';
	//checksum button
	var b=document.createElement('input');
	var clti="var obj=$i('"+obj.id+"');if(obj.editor_text_ean13){clearTimeout(obj.editor_text_ean13);return;};";

	b.setAttribute('type','button');
	b.setAttribute('value','csum');
	b.setAttribute('onfocus',clti);
	b.setAttribute('onblur',"var obj=$i('"+obj.id+"');if(obj.editor_text_ean13)clearTimeout(obj.editor_text_ean13);obj.editor_text_ean13=setTimeout('var obj=$i(\\'"+obj.id+"\\');obj.editor_text_ean13_hint.style.display=\\'none\\';obj.removeChild(obj.editor_text_ean13_hint);delete obj.editor_text_ean13_hint;',200);");
	b.setAttribute('onclick',
		"var form=$i('"+form.id+"');if(form.value.length<12)return;tv=form.value.substr(0,12);"+
		"tv+=barcode_gen_ean_sum(tv).toString();form.focus();form.value=tv;this.focus();");
	d.appendChild(b);
	//from id button
	b=document.createElement('input');
	b.setAttribute('type','button');
	b.setAttribute('value','gen');
	b.setAttribute('onfocus',clti);
	b.setAttribute('onblur',"var obj=$i('"+obj.id+"');if(obj.editor_text_ean13)clearTimeout(obj.editor_text_ean13);obj.editor_text_ean13=setTimeout('var obj=$i(\\'"+obj.id+"\\');obj.editor_text_ean13_hint.style.display=\\'none\\';obj.removeChild(obj.editor_text_ean13_hint);delete obj.editor_text_ean13_hint;',200);");
	b.setAttribute('onclick',"var te='"+key+"';while(te.length<11)te='0'+te;te='2'+te;te+=barcode_gen_ean_sum(te).toString();"+
		"var form=$i('"+form.id+"');form.focus();form.value=te;this.focus();");
	d.appendChild(b);
	obj.appendChild(d);
	
}

function editor_text_ean13_blur(obj,form,key)
{
	if(obj.editor_text_ean13)clearTimeout(obj.editor_text_ean13);
	obj.editor_text_ean13=setTimeout("var obj=$i('"+obj.id+"');obj.editor_text_ean13_hint.style.display='none';"+
		"obj.removeChild(obj.editor_text_ean13_hint);delete obj.editor_text_ean13_hint;",200);
}



