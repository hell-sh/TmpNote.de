/* Copyright (c) 2018, Hellsh Ltd. */

var editor;

document.addEventListener("DOMContentLoaded", function()
{
	$(window).on("resize",function()
	{
		$("#tmpnote-card").css("height",window.innerHeight-$("#tmpnote-header")[0].clientHeight-60);
		if(!$("#tmpnote-editor").hasClass("uk-hidden"))
		{
			$("#tmpnote-editor").css("height",$("#tmpnote-card")[0].clientHeight-$("#tmpnote-tools")[0].clientHeight);
		}
		if(editor!==undefined)
		{
			editor.resize();
		}
	}).resize();
	editor = ace.edit("tmpnote-editor");
	editor.setTheme("ace/theme/monokai");
	if(typeof onEditorReady=="function")
	{
		onEditorReady();
	}
});

function setData(key,val)
{
	localStorage.setItem(key,val);
}

function unsetData(key)
{
	localStorage.removeItem(key);
}

function getData(key)
{
	return localStorage.getItem(key);
}

function rand(min,max)
{
	let r=-1;
	do
	{
		if(typeof crypto!="undefined")
		{
			r=parseInt(crypto.getRandomValues(new Uint32Array(1))[0].toString().substr(1,(max+"").length));
		}
		else
		{
			r=Math.floor(Math.random()*max);
		}
		r+=min;
	}
	while(r<min||r>max);
	return r;
}
