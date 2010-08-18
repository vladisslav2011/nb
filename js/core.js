




//utility functions
/* Создание нового объекта XMLHttpRequest для общения с Web-сервером */
function new_xmlHTTP() 
{
    if (window.XMLHttpRequest) {
        return new window.XMLHttpRequest;
    }
    else {
        try {
            return new ActiveXObject("MSXML2.XMLHTTP.3.0");
        }
        catch(ex) {
            return null;
        }
    }
}

function new_xmlHTTP_off()
{
	var xmlHttp = false;
	/*@cc_on @*/
	/*@if (@_jscript_version >= 5)
	try {
		xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		try {
			xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (e2) {
			xmlHttp = false;
		}
	}
	@end @*/
	
	if (!xmlHttp && typeof XMLHttpRequest != 'undefined') {
		xmlHttp = new XMLHttpRequest();
	}
	return xmlHttp;
}

function htmlspecialchars(html) { 
      // Сначала необходимо заменить & 
      html = html.replace(/&/g, "&amp;"); 
      // А затем всё остальное в любой последовательности 
      html = html.replace(/</g, "&lt;"); 
      html = html.replace(/>/g, "&gt;"); 
      html = html.replace(/"/g, "&quot;"); 
      html = html.replace(/'/g, "&#039;"); 
      // Возвращаем полученное значение 
      return html; 
}

function isArray( obj )
{
	return typeof( obj ) == 'object' && obj.constructor == Array;
}

function $i(a){return document.getElementById(a);};

var text_content='textContent';
if(typeof(document.textContent)=='undefined')text_content='innerText';

function js2php(o)
{
	if(typeof(o)=='string')
	{
		return 's:'+o.length+':"'+o+'";';
	};
	if(typeof(o)=='number')
	{
		return 's:'+o.toString().length+':"'+o.toString()+'";';
	};
	var c=0,com='';
	if(isArray(o))
	{
		for(var k=0;k<o.length;k++)
		{
			com+=('i:'+k+';'+js2php(o[k]));
		};
		return 'a:'+o.length+':{'+com+'}';
	};
	for(var a in o)
	{
		c++;
		com+=(js2php(a)+js2php(o[a]));
	};
	return 'a:'+c+':{'+com+'}';
}

function object_serialize(o)
{
	var a;var r="{";
	for(a in o)
	{
		if(r!="{")r+=",";
		r+=(a+":'"+(o[a]+'').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0')+"'");
	}
	return r+"}";
}

function object_merge(a,b)
{
	var x;var r={};
	for(x in a)
		r[x]=a[x];
	for(x in b)
		r[x]=b[x];
	return r;
}


function has_css_class(item,className)
{
	if(item.className==className)return true;
	var m=new regexp("^(?:.* )*"+className+"(?:.* )*$","");
	return m.test(item.className);
}

function add_css_class(item,className)
{
	if(has_css_class(item,className))return;
	item.className+=(" "+className);
}

function remove_css_class(item,className)
{
	var repl=new RegExp("^((?:.* )*)"+className+"((?: .*)*)$","");
	var n=t1.replace(item.className,"$1$2").replace(/  /,' ');
	item.className=n;
}


function findPosY(obj)
{
    var cur = 0;
    var m=obj;
    while (m)
    {
        cur += m.offsetTop;
        m = m.offsetParent;
    }
    var m=obj;
    while (m!=document.body)
    {
    	if(typeof(m.scrollTop)!='undefined')
	        cur -= m.scrollTop;
        m = m.parentNode;
    }
    return cur;
}

function findPosX(obj)
{
    var cur = 0;
    var m=obj;
    while (m)
    {
        cur += m.offsetLeft;
        m = m.offsetParent;
    }
    var m=obj;
    while (m!=document.body)
    {
    	if(typeof(m.scrollLeft)!='undefined')
	        cur -= m.scrollLeft;
        m = m.parentNode;
    }
    return cur;
}

function findPosXY(obj)
{
    var r={x:0,y:0};
    var m=obj;
    while (m)
    {
        r.y += m.offsetTop;
        r.x += m.offsetLeft;
        m = m.offsetParent;
    }
    var m=obj;
    while (m!=document.body)
    {
    	if(typeof(m.scrollTop)!='undefined')
    	{
	        r.y -= m.scrollTop;
    	    r.x -= m.scrollLeft;
    	}
        m = m.parentNode;
    }
    return r;
}


function async_post(uri,data,callback)
{
  xmlHttp=new_xmlHTTP();
  xmlHttp.open("POST", uri, true);
  xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xmlHttp.onreadystatechange = callback;
  xmlHttp.send(data);
}

function async_get(uri,callback)
{
  xmlHttp=new_xmlHTTP();
  xmlHttp.open("GET", uri, true);
  xmlHttp.onreadystatechange = callback;
  xmlHttp.send(null);
  //alert(callback);
}

function async_put(uri,data,callback)
{
  xmlHttp=new_xmlHTTP();
  xmlHttp.open("PUT", uri, true);
  xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xmlHttp.onreadystatechange = callback;
  xmlHttp.send(data);
}

function async_delete(uri,callback)
{
  xmlHttp=new_xmlHTTP();
  xmlHttp.open("DELETE", uri, true);
  xmlHttp.onreadystatechange = callback;
  xmlHttp.send(null);
}


//bugfix some versions of opera

function force_update(node)
{
	var anode;
	if(typeof(node)=="String")anode=$i(node);
	else anode=node;
	var n = document.createTextNode('');
	anode.appendChild(n);
	setTimeout(function(){n.parentNode.removeChild(n)},0);
	
}

//parse event to keyboard flags and keycode/charcode
//diffeerent browsers supported (I think)
function event_to_mkc(ev)
{
	if(typeof(ev)=='undefined')ev=window.event;
	var m=0;
	if(ev.altKey)m|=1;
	if(ev.ctrlKey)m|=2;
	if(ev.shiftKey)m|=4;
	if(ev.metaKey)m|=8;
	var charcode=0;
	var keycode=0;
//	$i('debug').innerHTML='k'+ev.keyCode+'c'+ev.charCode+'w'+ev.which;
	if(typeof(ev.charCode)=='undefined')//opera
	{
		charcode=ev.which;
	}else{
		charcode=ev.charCode;
	};
	if(charcode<32)
	{
		keycode=ev.keyCode;
		charcode=0;
		//if(charcode!=0)keycode=0;
	};
	return {m:m,charcode:charcode,keycode:keycode,ALT:1,CTRL:2,SHIFT:4,META:8};
}

function stop_event(ev)
{
	try{
		ev.stopPropagation();
		ev.preventDefault();
	}catch(e){};
	return false;
}




//utility functions end










function resizer_object()
{

this.drag_context={
	active:false,
	hit:false,
	hit_x:0,
	hit_y:0,
	button:0,
	ghost:{},
	data:''
	};


this.create_ghost=function(ev,elem,data)
{
	this.drag_context.ghost=elem.cloneNode(true);
	this.drag_context.ghost.style.display='none';
	document.body.appendChild(this.drag_context.ghost);
	this.drag_context.ghost.style.position='absolute';
	//this.drag_context.ghost.style.oppacity='0.50';
	if(this.drag_context.ghost.style.backgroundColor=='')this.drag_context.ghost.style.backgroundColor='white';
	this.drag_context.hit=true;
	this.drag_context.hit_x=ev.pageX;
	this.drag_context.hit_y=ev.pageY;
	this.drag_context.data=data;
	if(typeof(this.drag_context.plus)=='undefined')
	{
		this.drag_context.plus=document.createElement('div');
		this.drag_context.plus.appendChild(document.createTextNode('+'));
		document.body.appendChild(this.drag_context.plus);
		this.drag_context.plus.style.display='none';
		this.drag_context.plus.style.position='absolute';
		this.drag_context.plus.style.backgroundColor='grey';
		this.drag_context.plus.style.border='1px solid white';
	}
}

this.update_ghost=function(x,y)
{
	try{
		this.drag_context.ghost.style.display='block';
		this.drag_context.ghost.style.position='absolute';
		this.drag_context.ghost.style.top=(y+3)+'px';
		this.drag_context.ghost.style.left=(x+3)+'px';
		this.drag_context.plus.style.top=(y+17)+'px';
		this.drag_context.plus.style.left=(x+3)+'px';
	}catch(e){};
}

this.destroy_ghost=function()
{
	try{
		this.drag_context.ghost.style.display='none';
		this.drag_context.ghost.parentNode.removeChild(this.drag_context.ghost);
		this.drag_context.plus.style.display='none';
	}catch(e){};
	this.drag_context.ghost={};
}


this.setwh=function(c,w,h)
{
	var style=null;
	for(var k=0;k<document.styleSheets.length;k++)
	{
		style=document.styleSheets[k];
		for(var t=0;t<style.cssRules.length;t++)
		{
			if((style.cssRules[t].selectorText=='.' + c)||(style.cssRules[t].selectorText=='.' + c + ':hover'))
			{
				if(this.rszx)style.cssRules[t].style.width= w+'px';
				if(this.rszy)style.cssRules[t].style.height= h+'px';
			}
		}
	}
}




this.drag_delta=2;
this.rszx=0;
this.rszy=0;
this.movx=0;
this.movy=0;
this.obj=null;
this.byclass=0;
this.X=0;
this.Y=0;
this.wX=0;
this.wY=0;
this.pX=0;
this.pY=0;
this.moving=0;


this.doc_mouse_move=function(ev)
{
	if(this.drag_context.active)
	{
		if(this.drag_context.button!=1)
		{
			this.drag_context.active=false;
			this.destroy_ghost();
			return;
		}
		this.update_ghost(ev.pageX,ev.pageY);
		
		ev.stopPropagation();
		ev.preventDefault();
		return false;
	}
	if(this.drag_context.hit)
	{
		if(this.drag_context.button!=1)
		{
			this.drag_context.hit=false;
			return;
		}
		var dx=Math.abs(ev.pageX-this.drag_context.hit_x);
		var dy=Math.abs(ev.pageY-this.drag_context.hit_y);
		if(dx>=this.drag_delta || dy>=this.drag_delta )
		{
			this.drag_context.active=true;
			this.drag_context.hit=false;
			this.update_ghost(ev.pageX,ev.pageY);
		}
		ev.stopPropagation();
		ev.preventDefault();
		return false;
	}
	if(! this.obj)return;
	if(this.moving != ev.ctrlKey)
	{
		this.wX=ev.clientX - this.obj.clientWidth;//x;
		this.wY=ev.clientY - this.obj.clientHeight;//y;
		this.pX=ev.clientX - this.obj.offsetLeft;//x;
		this.pY=ev.clientY - this.obj.offsetTop;//y;
		this.moving=ev.ctrlKey;
	}
	if (this.obj && (this.rszx || this.rszy) && (! this.moving))
	{
		var width = (ev.clientX - this.wX > 5)?ev.clientX - this.wX:5;
		var height = (ev.clientY - this.wY >5)?ev.clientY - this.wY:5;
		//var mon=$i('monitor');
		//if(mon)mon.innerHTML='wX=' + this.wX + ';wY=' + this.wY;
		if(this.byclass==0)
		{
		if(this.rszx)this.obj.style.width=width + 'px';
		if(this.rszy)this.obj.style.height=height + 'px';
		//if(mon)mon.innerHTML='wX=' + height + ';wY=' + width;
		}else{
		this.setwh(this.obj.className,width,height);
		//if(mon)mon.innerHTML='wX=' + this.wX + ';wY=' + this.wY;
		
		}
		ev.stopPropagation();
		ev.preventDefault();
		return false;
	}
	//ctrlKey
	if (this.obj && (this.movx || this.movy) && this.moving)
	{
		var left = ev.clientX - this.pX;
		var top = ev.clientY - this.pY;
		//var mon=$i('monitor');
		//if(mon)mon.innerHTML='wX=' + this.wX + ';wY=' + this.wY;
		if(this.byclass==0)
		{
		if(this.movx)this.obj.style.left=left + 'px';
		if(this.movy)this.obj.style.top=top + 'px';
		//if(mon)mon.innerHTML='wX=' + height + ';wY=' + width;
		}else{
		this.setlt(this.obj.className,left,top);
		//if(mon)mon.innerHTML='wX=' + this.wX + ';wY=' + this.wY;
		
		}
		ev.stopPropagation();
		ev.preventDefault();
		return false;
	}
	

}

this.doc_mouse_down=function (ev) 
{
  this.drag_context.button=1;
  try{
  	if(! ev.target || ! ev.target.id) return;
  	}catch(e){return;};
  this.obj = ev.target;
  if(! this.obj.id.match(/.*(resizeable|resize_style|movable|move_style).*/))
  {
  	this.obj=null;
  	return;
  }
  // $i('monitor').innerHTML=this.obj.id.match(/.*resize.*/);
  this.byclass=0;
  if( this.obj.id.match(/.*e_style.*/))this.byclass=1;
  //alert(ev.target.name);
  //alert(event);
  this.wX=ev.clientX - this.obj.clientWidth;//x;
  this.wY=ev.clientY - this.obj.clientHeight;//y;
  this.pX=ev.clientX - this.obj.offsetLeft;//x;
  this.pY=ev.clientY - this.obj.offsetTop;//y;
  //this.rsz=(ev.clientX+document.body.scrollLeft-this.obj.offsetLeft+4-this.obj.clientWidth>0)?1:0;
  this.rszx=1;
  this.rszy=1;
  this.movx=1;
  this.movy=1;
  if(this.obj.id.match(/.*(resizeablex|resize_stylex).*/))
	  this.rszy=0;
  if(this.obj.id.match(/.*(resizeabley|resize_styley).*/))
	  this.rszx=0;
  if(! this.obj.id.match(/.*(resizeable|resize_style).*/))
  {
	  this.rszy=0;
	  this.rszx=0;
  }
  if(this.obj.id.match(/.*(movablex|move_stylex).*/))
	  this.movy=0;
  if(this.obj.id.match(/.*(movabley|move_styley).*/))
	  this.movx=0;
  if(! this.obj.id.match(/.*(movable|move_style).*/))
  {
	  this.movy=0;
	  this.movx=0;
  }
  
  ev.stopPropagation();
  ev.preventDefault();
  return false;
}


this.doc_mouse_up=function(ev) 
{
	this.drag_context.button=0;
	if(this.drag_context.active)
	{
		this.drag_context.active=false;
		this.destroy_ghost();
	}
	if(this.drag_context.hit)
	{
		this.drag_context.hit=false;
	}
 if(this.obj)
 {
	//obj.style.position='';
	//obj.style.width='';
	if(ev.target != this.obj)if(this.obj.onmouseup)this.obj.onmouseup(ev);
	this.obj = null;
 }
 return true;
}



}

///////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////// onload //////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////



var resizer=new resizer_object();
var global_key_hooks=new Array();for(var k=0;k<16;k++)global_key_hooks[k]=new Array();
var global_char_hooks=new Array();for(var k=0;k<16;k++)global_char_hooks[k]=new Array();
function global_key_press(ev)
{
	var mkc=event_to_mkc(ev);
	if(typeof(ev) == 'undefined')ev=window.event;
	if(typeof(ev.target) == 'undefined')return true;
	if(
		ev.target==document.documentElement	||	//mozilla,opera 9.64
		ev.target==document					||	//konqueror
		ev.target==document.body				//opera 10.10
	)
	{
		var end=false;
		if(mkc.charcode !=0 && typeof(global_char_hooks[mkc.m]) !='undefined')
			if(typeof(global_char_hooks[mkc.m][mkc.charcode])=='function')
				end=global_char_hooks[mkc.m][mkc.charcode]();
		if(mkc.keycode !=0 && typeof(global_key_hooks[mkc.m]) !='undefined')
			if(typeof(global_key_hooks[mkc.m][mkc.keycode])=='function')
				end=global_key_hooks[mkc.m][mkc.keycode]();
		if(end)
		{
			ev.preventDefault();
			ev.stopPropagation();
			return false;
		};
	};
	return true;
}


if (document.addEventListener)
{
	document.addEventListener('mousemove', function(ev){resizer.doc_mouse_move(ev);}, false); 
	document.addEventListener('mousedown', function(ev){resizer.doc_mouse_down(ev);}, false); 
	document.addEventListener('mouseup', function(ev){resizer.doc_mouse_up(ev);}, false); 
	document.addEventListener('keypress', global_key_press, false); 
} else if (document.attachEvent)
{

	document.onmousemove=function(ev){resizer.doc_mouse_move(ev);};
	document.onmousedown=function(ev){resizer.doc_mouse_down(ev);};
	document.onmouseup=function(ev){resizer.doc_mouse_up(ev);};
	document.onkeypress=global_key_press;
	try{
		document.captureEvents(Event.MOUSEDOWN | Event.MOUSEMOVE | Event.MOUSEUP | Event.KEYPRESS);
	}catch(e){};
}

document.onload_functions=Array();
window.onload=function(ev)
{
	if(document.onload_functions)
		for(var k=0;k<document.onload_functions.length;k++)
			document.onload_functions[k]();
};
	



































//при изменении поля для которого установлена необходимость перезагрузки других полей нужно отправить измененное значение
//установить для изменяемых полей атрибут changing чтобы они правильно обрабатывали фокусировку
//при попытке сфокусировать изменяющееся поле, установить у него атрибут фокусировки и вырубить все поля(glabal disabled)
//после успешного сохранения перезагрузить поля с установленным changing
//если была попытка сфокусировать изменяющееся поле включить все поля(снять global disabled)и вернуть фокус на первый элемент
//формы поля с атрибутом фокусировки

//сохранение производится замещаемой операцией(цепная операция с заменяемым хвостом в 1 элементарное действие):
// при попытке начала операции проверить не начата ли уже другая такая операция
// если не начата выставить флаг начала
// если начата, записать в буфер новое значение
// после успешной отправки проверить буфер
// если в буфере есть значение - отправить его и удалить из буфера
// если нет значения снять флаг начала и выполнить завершающие действия

//глобальные кнопки
// сохранить и продолжить
// проверить корректность (ели есть поля с установленным invalid поругаться и выйти)
// послать запрос сохранения
// установить global save lock перекрывающий другие блокировки
// если идет какая-нибудь операция добавить действие в финишную цепочку
// иначе сразу выполнить действие
// при завершении действия снять global save lock

// сохранить и вернуться/закрыть
// сделать то же что и для 'сохранить и продолжить' но по завершении вместо снятия блокировки отправить перейти по ссылке
// path step up (перейти к точки, из которой было открыть данное окно или закрыть если такой точки нету)

// не сохранять и вернуться/закрыть
// удалить веременную копию из таблиц версий и path step up

// отменить все изменения
// удалить веременную копию из таблиц версий
// загрузить актуальную версию
// перегрузить страницу

/*

типы объектов
type=object
просто набор полей
branch_parent - содержит дополнительные поля в родительском объекте
!!!!!!!!!!!!!!!! может не надо это делать
main table - таблица с полями
use_visibility_list - будет ли использоваться список видимости реквизитов
(табличка типа (id_obj,condition_query,and_mask,or_mask)

type=array
набор полей, некоторые являются ключами и определяют значения остальных
содержит все те же поля, что и type=object
сценарий должен смотреть на свойство is_key полей

type=object_ref
ссылка на объект, значение - номер свойства объекта

type=array_ref
ссылка на эелемент массива объектов (несколько свойств отбора и одно редактируемое поле)

type=branch_ref
эксклюзивная ветвь свойств объекта (ссылка на объект, объединяемый с данным)
добавляет все поля указанного объекта, содержит все ключи данного объекта
!!!!!!!!!!!!!!!! может не надо это делать




type=string
текстовая строка
editor - нестандартный редактор
validator - нестандартный валидатор
len - длина строки(sql)

type=int
type=decimal
type=double
len - число знаков до запятой
prec - число знаков после запятой


что получилось
create table `!!def` (	`id` int unsigned not null auto_increment primary key,
			`parent` int unsigned,
			`type` varchar(10),
			`vtype` varchar(100),
			`use visibility list` int(1),
			`table` varchar(200),
			`name` varchar(200),
			`vname` varchar(200),
			`sql formula` varchar(655535),
			`no store` int(1),
			`only init` int(1),
			`sql type` varchar(200),
			`editor` varchar(200),
			
)
create table `!!key` (	`id` int unsigned,
			`fid` int unsigned,
			primary key (id,fid)
)



*/


// поля контекста элемента управления 'редактор' (нужны для отправки и обновления поля)
// объекты двух типов: object{prop,prop,prop,..} и array[key,key,..]{prop,prop,prop,..}
// тип объекта вида 'ref.meterials.width' или 'doc.income.multi1.amount'
// refs object{prod array[id,date]{<tabl>,name,height,width,tree_prop},mat,subj}
// refs.prod
// путь как-то так path=22[44:58,36:10223].

// массив с ключами, позволяющий однозначно идентифицировать объект и поле
// что-то типа obj_name->ref.materials, obj_date->'21.05.2008', obj_id->12345
// или    типа obj_name->doc.income.multi1, obj_date->'21.05.2008', obj_id->12345, multi_lineno->5
// номер в массиве объектов
// outer_id - id тега, в который заключен редактор
// у редактора может быть установлен атрибут container типа core object (==null - не является контейнером)
// у редактора может быть установливается атрибутparent типа core object
// на запрос обновления возврашается 2 поля : html, js
/*
var res=eval(result);
document.getElementById(outer_id).innerHTML=res.html;
this.objects[n]=eval(res.js);
this.objects[n].perent=this;
res=null;
*/


//попробуем дергать через httprequest куски js-кода и выполнять через eval
/*
<input id='i1' type=text onfocus='activatemon(this,'text');' onblur='deactivatemon(this);'>
genfetch(obj,objtype)
 возвращает function(obj), возращающую строку для добавления к postdata
genchecker(obj,objtype)
 возвращает function(obj), возращающую true, если объект изменился и валиден
*/


function change_sender()
{
this.monitored=new Array();
this.sending=false;
this.next_post='';
this.callback_uri='';
this.queue=new Array();
this.timeout=null;
this.request_counter=0;
this.debug=true;

this.activatemon = function(def)
{
	//check against double-activation
	def.obj.change_sender_monitored=true;
	var s=this.monitored.length;
	try{
	for(var k=0;k<s;k++)
	{
		if(this.monitored[k] && this.monitored[k].obj==def.obj)
		{
			if(this.timeout){clearTimeout(this.timeout);this.timeout=null}
			return;
		}
	}
	}catch(e){};
	//deactivate if any active
	try{
		while(this.monitored[0] && this.monitored[0].obj)this.deactivatemon(this.monitored[0].obj);
	}catch(e){};
	
	/*
	var mon=new Object();
	mon['obj']=obj;
	mon['checker']=genchecker(obj);
	mon['fetch']=genfetch(obj);
	monitored.push(mon);
	*/
	//generate checker and fetch
	if(def.obj)
	{
		if(! def.checker)def['checker']=this.genchecker(def);
		if(! def.fetch)def['fetch']=this.genfetch(def);
	}
	//store monitored object
	this.monitored.push(def);
}

this.ismonitored=function(obj)
{
	var s=this.monitored.length;
	try{
	for(var k=0;k<s;k++)
	{
		if(this.monitored[k] && this.monitored[k].obj==obj)
		{
			return true;
		}
	}
	}catch(e){return false;};
	return false;
}

this.latedeactivate = function(obj)
{
	var a=this;
	this.timeout=setTimeout(function(){a.deactivatemon(obj);},100);
}

this.deactivatemon = function(obj)
{
	if(this.timeout){clearTimeout(this.timeout);this.timeout=null}
	this.timeout=null;
	this.timerch();
	var s=this.monitored.length;
	var mnew=[];
	for(var k=0;k<s;k++)
	{
		if(this.monitored[k] && this.monitored[k].obj!=obj)mnew.push(this.monitored[k]);
	}
	this.monitored=mnew;
	obj.change_sender_monitored=false;

}



this.timerch = function(force)
{
	var s=this.monitored.length;
	var changed=false;
	var change_t=false;
	var tosend=new Object;
	for(var k=0;k<s;k++)
	{
		if(this.monitored[k] && this.monitored[k].obj && this.monitored[k].checker && ((this.monitored[k].obj.tagName=="INPUT" && this.monitored[k].obj.type=="text") || this.monitored[k].obj.tagName=="TEXTAREA" || force==true))
			if(change_t = this.monitored[k].checker())
			{
				changed |=change_t;
				tosend['static']=  this.monitored[k].static;
				tosend['uri']=this.monitored[k].uri;
				tosend['val']= this.monitored[k].fetch();
				tosend['c_id']= this.monitored[k].obj.id;
			}
	//сюда добавить presend?
	}
	if(changed)
		this.send_or_push(tosend);
}


this.send_or_push=function(tosend)//tosend={uri:uri,static:'var',val:'value'}
{
	this.request_counter++;
	if(this.sending)
	{
		if(this.queue.length>0 && this.queue[0].obj==tosend.obj && this.queue[0].c_id==tosend.c_id)
			this.queue[0]=tosend;
		else
			this.queue.unshift(tosend);
		this.show_sending(this.queue.length+1);
		return;
	}else
		this.send_async(tosend);
}

this.send_async = function(d)//d={uri:uri,static:'var',val:'value'}
{
//	alert(typeof(this));
	this.sending=true;
	this.show_sending(this.queue.length+1);
	var xmlHttp=new_xmlHTTP();
	
	var curi='';
	if(this.callback_uri)	curi=this.callback_uri
	if(d.uri)		curi=d.uri;
	
	xmlHttp.open("POST", curi, true);
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.chs=this;
	xmlHttp.onreadystatechange = function()
	{
		//alert(typeof(xmlHttp));
		try {if (xmlHttp.readyState == 4){if (xmlHttp.status == 200)
		{
			xmlHttp.chs.sending=false;
			xmlHttp.chs.show_sending(xmlHttp.chs.queue.length);
			if(xmlHttp.responseText.match(/^<.*/) && (!xmlHttp.chs.debug))
				window.location.reload(true);//got something like html. Maybe session ended or auth failed.
			else //response has to be js code
				eval(xmlHttp.responseText);
			if(xmlHttp.chs.queue.length>0)
			{
				xmlHttp.chs.send_async(xmlHttp.chs.queue.pop());
			}
		} else {xmlHttp.chs.safe_alert(1,'С запросом возникла проблема.');	}}}
		catch( e ) {xmlHttp.chs.safe_alert('Произошло исключение: ' + e.description);};
		
	}
	//debug
	/*	if($i('errordump'))
		{
			var tt=0;
			if($i('errordump').cntr)tt=$i('errordump').cntr;
			tt++;
	
			$i('errordump').innerHTML += '<br>'  + tt + ' : ';
			$i('errordump').innerHTML += htmlspecialchars(curi)+';';
			$i('errordump').innerHTML += htmlspecialchars(decodeURIComponent(d.static + '=' + d.val));
			$i('errordump').cntr=tt;
		}*/
	var send_buffer='';
	var key;
	if(typeof(d.dynamic)!='undefined')
	{
		if(typeof(d.dynamic)=='string')
			send_buffer+=d.dynamic;
		else{
			for(key in d.dynamic)
			{
				if(send_buffer !='')send_buffer+='&';
				send_buffer+=(encodeURIComponent(key)+'='+encodeURIComponent(d.dynamic[key]));
			}
		}
		
	}
	if(typeof(d.static)!='undefined')
	{
		if(typeof(d.static)=='string')
		{
			if(send_buffer !='')
				send_buffer=d.static+'&'+send_buffer;
			else
				send_buffer=d.static;
		}else{
			for(key in d.static)
			{
				if(send_buffer !='')send_buffer+='&';
				send_buffer+=(encodeURIComponent(key)+'='+encodeURIComponent(d.static[key]));
			}
		}
	}
	if(typeof(d.val=='string'))
	{
		send_buffer+=('&val=' + encodeURIComponent(d.val));
	}
	xmlHttp.send(send_buffer);
	//async_post(this.callback_uri,d,this.callback);
}





this.fetchfuncs=[];
this.checkerfuncs=[];


this.genfetch = function(o)
{
	var s=this.fetchfuncs.length;
	for(var k=0;k<s;k++)
	{
		var res=this.fetchfuncs[k](o);
		if(typeof(res)=="function")return res;
	}
	return null;
}

this.genchecker = function(o)
{
	var s=this.checkerfuncs.length;
	for(var k=0;k<s;k++)
	{
		var res=this.checkerfuncs[k](o);
		if(typeof(res)=="function")return res;
	}
	return (function(a){return false;});
}


this.bgifc=function(id,bg)//tosend={uri:uri,static:'var',val:'value'}
{
	if(this.queue.length>0  && this.queue[0].c_id==id)
		return null;
	var r=$i(id);
	r.style.backgroundColor=bg;
	return r;
}

this.show_sending=function(n)
{
	var m=$i('async_monitor');
	if(typeof(m)=='undefined')return;
	if(m==null)return;
	var c=$i('async_monitor_count');
	if(typeof(c)=='undefined')return;
	if(n===0)
	{
		m.style.visibility='hidden';
	}else{
		m.style.visibility='visible';
		c[text_content]=n;
	}
}

}

var chse=new change_sender();


